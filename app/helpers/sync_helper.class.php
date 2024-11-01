<?php

namespace Ticketack\WP\Helpers;

use Ticketack\WP\TKTApp;
use Ticketack\Core\Models\Screening;
use Ticketack\Core\Models\Event;

/**
 * Helper class to handle Ticketack/Wordpress events/posts synchronization
 */
class SyncHelper
{
    const POST_TYPE    = 'tkt-event';

    const IMPORT_ONLY_NEW = false;

    public static function sync_events()
    {
        $default_lang     = TKTApp::get_instance()->get_config('i18n.default_lang', 'fr');
        $tags_filter      = TKTApp::get_instance()->get_config('import.tags_filter', '');
        $tags             = empty($tags_filter) ? [] : explode(',', $tags_filter);
        $places_filter    = TKTApp::get_instance()->get_config('import.places_filter', '');
        $places           = empty($places_filter) ? [] : explode(',', $places_filter);
        $save_attachments = (bool)TKTApp::get_instance()->get_config('import.save_attachments', false);

        ini_set('memory_limit', '800M');
        ini_set('max_execution_time', 0);

        $events = static::load_next_events($tags, $places);

        if (!empty($events)) {
            wp_defer_term_counting( true );
            wp_defer_comment_counting( true );
            define( 'WP_IMPORTING', true );

            $languages = TKT_WPML_INSTALLED ? icl_get_languages('skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str') : [];
            array_map(function ($e) use ($languages, $default_lang, $save_attachments) {
                $def_post_id = static::create_post($e, $default_lang, $save_attachments);

                if (is_null($def_post_id) || !TKT_WPML_INSTALLED) {
                    return;
                }

                foreach (array_keys($languages) as $lang) {
                    if ($lang == $default_lang) {
                        continue;
                    }
                    $tr_post_id = static::create_post($e, $lang, $save_attachments);
                    if (!is_null($tr_post_id)) {
                        static::link_translations($def_post_id, $tr_post_id, $lang);
                    }
                }
            }, $events);

            wp_defer_term_counting( false );
            wp_defer_comment_counting( false );

            $msg = count($events) > 1 ?
                tkt_t('%d événements ont été importés') :
                tkt_t('%d événement a été importé');
            tkt_flash_notice(sprintf($msg, count($events)), 'success');

            static::clear_comet_cache();
        } else {
            tkt_flash_notice(tkt_t("Aucun événement n'a été importé"));
        }
    }

    protected static function clear_comet_cache() {
        if (is_plugin_active('comet-cache-pro/comet-cache-pro.php') && method_exists('comet_cache', 'clear') ) {
            \comet_cache::clear();
            tkt_flash_notice(tkt_t('Le cache a été vidé.'), 'success');
        }
    }

    protected static function load_next_events($tags = [], $places = [])
    {
        $screenings = Screening::all()
            ->in_the_future()
            ->order_by_start_at()
            ->get('_id,title,start_at,stop_at,description,cinema_hall,films,sections,opaque,refs');

        if (!empty($places)) {
            $screenings = array_values(array_filter($screenings, function ($screening) use ($places) {
                if (in_array($screening->place()->_id(), $places)) {
                    return true;
                }
            }));
        }

        $events = Event::from_screenings($screenings);

        if (!empty($tags)) {
            $events = array_values(array_filter($events, function ($e) use ($tags) {
                $event_tags = $e->opaque('tags', []);
                // We merge the event tags with the screening ones iif it's a film package
                // because film packages can be tagged as confirmed while their events aren't.
                foreach ($e->screenings() as $s) {
                    if ($s->opaque('eventival', [])['type'] === 'film_package') {
                        $event_tags = array_merge($event_tags, $s->opaque('tags', []));
                    }
                }
                foreach ($event_tags as $t) {
                    if (in_array($t[TKT_LANG], $tags)) {
                        return true;
                    }
                }

                return false;
            }));
        }

        return $events;
    }

    protected static function create_post($event, $lang, $save_attachments)
    {
        // Let's try to find a title in the current language
        $title = $event->localized_title_or_default_or_original($lang);

        $slug  = tkt_get_event_slug($event, $lang);
        // WP automatically prepends 'http://' to the guid !
        $guid  = 'http://'.$slug;

        $post_content = trim(preg_replace('#\R+#', '', $event->opaque('description')[$lang]));

        $post = [
            "post_title"    => $title,
            "post_content"  => $post_content,
            "post_type"     => static::POST_TYPE,
            'post_name'     => $slug,
            "post_status"   => "publish",
            "guid"          => $guid
        ];

        // Check for any existing post
        $existing_post = get_post(static::get_id_from_guid($guid));
        if (!is_null($existing_post)) {
            if (static::IMPORT_ONLY_NEW) {
                return null;
            }

            $post['ID'] = $existing_post->ID;
        }

        // Save post image
        $post_id = wp_insert_post($post);

        if ($save_attachments && count($event->posters()) > 0) {
            static::save_event_image($event, $post_id, $lang);
        }

        static::save_post_metas($event, $post_id, $lang);

        return $post_id;
    }

    protected static function save_event_image($event, $post_id, $lang = 'fr')
    {
        if (count($event->posters()) == 0) {
            return false;
        }

        $poster     = $event->posters()[0];
        $url        = $poster->url;
        $basename   = basename($url);
        $upload_dir = wp_upload_dir();
        $dest_path  = $upload_dir['path'].'/'.$basename;
        $dest_url   = $upload_dir['url'].'/'.$basename;
        $filetype   = wp_check_filetype($basename, null);

        $existing_attachment_id = get_post_meta($post_id, '_thumbnail_id', /*$single*/true);
        if (!empty($existing_attachment_id) && intval($existing_attachment_id) > 0) {
            // Post already has an attachment
            $existing_attachment = get_post($existing_attachment_id);

            if ($existing_attachment && $existing_attachment->guid == $dest_url) {
                // The attachment has the same name: not changed ???
                static::link_attachment_to_post($post_id, $existing_attachment_id->ID);
                return false;
            }

            // We could delete the existing attachment here if wanted
        }

        if (!static::download_attachment($url, $dest_path)) {
            return false;
        }

        $image_id = static::create_attachment(
            $existing_attachment_id,
            $dest_url,
            $filetype['type'],
            $event->localized_title_or_original($lang),
            $dest_path,
            $post_id
        );

        static::link_attachment_to_post($post_id, $image_id);
    }

    protected static function save_post_metas($event, $post_id, $lang)
    {
        update_post_meta($post_id, 'screenings', wp_slash(wp_json_encode($event->screenings())));
        update_post_meta($post_id, 'opaque', wp_slash(wp_json_encode($event->opaque())));
        update_post_meta($post_id, 'trailers', wp_slash(wp_json_encode($event->trailers())));
        update_post_meta($post_id, 'posters', wp_slash(wp_json_encode($event->posters())));
        update_post_meta($post_id, 'title',  wp_slash(wp_json_encode($event->title())));
    }

    // See https://wpml.org/wpml-hook/wpml_set_element_language_details/
    protected static function link_translations($original_post_id, $translated_post_id, $lang)
    {
        // https://wpml.org/wpml-hook/wpml_element_type/
        $wpml_element_type = apply_filters('wpml_element_type', static::POST_TYPE);

        // get the language info of the original post
        // https://wpml.org/wpml-hook/wpml_element_language_details/
        $get_language_args = [
            'element_id'   => $original_post_id,
            'element_type' => static::POST_TYPE
        ];
        $original_post_language_info = apply_filters(
            'wpml_element_language_details',
            null,
            $get_language_args
        );

        $set_language_args = [
            'element_id'           => $translated_post_id,
            'element_type'         => $wpml_element_type,
            'trid'                 => $original_post_language_info->trid,
            'language_code'        => $lang,
            'source_language_code' => $original_post_language_info->language_code
        ];

        do_action('wpml_set_element_language_details', $set_language_args );
    }

    protected static function get_id_from_guid($guid)
    {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s", $guid));
    }

    protected static function download_attachment($url, $dest_path)
    {
        if (($content = file_get_contents($url)) === false) {
            return false;
        }

        if (file_put_contents($dest_path, $content) === false) {
            return false;
        }

        return true;
    }

    protected static function create_attachment($id, $guid, $type, $title, $dest_path, $post_id)
    {
        $attachment = [
            'guid'           => $guid,
            'post_mime_type' => $type,
            'post_title'     => $title,
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];
        if (!empty($id) && intval($id) > 0) {
            $attachment['ID'] = intval($id);
        }

        $image_id = wp_insert_attachment($attachment, $dest_path, $post_id);

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        // Generate the metadata for the attachment, and update the database record.
        $attach_data = wp_generate_attachment_metadata($image_id, $dest_path);

        wp_update_attachment_metadata($image_id, $attach_data);

        return $image_id;
    }

    protected static function link_attachment_to_post($post_id, $image_id)
    {
        // We delete first to ensure only one occurence of same meta
        // This should be replaced by only one call to update_post_meta()
        delete_post_meta($post_id, '_thumbnail_id');
        add_post_meta($post_id, '_thumbnail_id', $image_id);
    }
}
