<?php

namespace Ticketack\WP\Helpers;

use Ticketack\WP\TKTApp;

/**
 * Helper class to handle Ticketack/Wordpress events/posts synchronization
 */
class SyncPeopleHelper
{
    const POST_TYPE = 'tkt-person';

    const IMPORT_ONLY_NEW = false;

    const EVENTIVAL_PEOPLE_URL = 'https://eventival.eu/giff/2019/en/ws/d2jbt2vKmNK59E4tNyefJ6JS2SHsLm/people.xml';

    const EVENTIVAL_PEOPLE_DETAILS_URL = 'https://eventival.eu/giff/2019/en/ws/d2jbt2vKmNK59E4tNyefJ6JS2SHsLm/people/__ID__.xml';

    const TAG_PROGUEST = '19_proguest';

    public static function fetch_people()
    {
        return simplexml_load_file(static::EVENTIVAL_PEOPLE_URL);
    }

    public static function fetch_person_details($person_id)
    {
        return simplexml_load_file(static::details_url($person_id));
    }

    public static function fetch_person_details_from_xml($xml)
    {
        return simplexml_load_string($xml);
    }

    public static function must_be_imported($details)
    {
        return static::has_tag(static::TAG_PROGUEST, $details);
    }

    public static function import($details, $default_lang)
    {
        $def_post_id = static::create_post($details, $default_lang);

        if (is_null($def_post_id) || !TKT_WPML_INSTALLED) {
            return false;
        }

        $success = true;
        $languages = icl_get_languages('skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str');
        foreach (array_keys($languages) as $lang) {
            if ($lang == $default_lang) {
                continue;
            }
            $tr_post_id = static::create_post($details, $lang);
            if (is_null($tr_post_id)) {
                return false;
            }

            static::link_translations($def_post_id, $tr_post_id, $lang);
        }

        return true;
    }

    protected static function get_tags($details)
    {
        return array_map(function ($node) { return (string)$node[0]; }, $details->xpath('eventival_categorization/tags/tag'));
    }

    protected static function has_tag($tag, $details)
    {
        return in_array($tag, static::get_tags($details));
    }

    protected static function get_photos($details)
    {
        $photos = [];
        foreach ($details->xpath('photos') as $node) {
            $photo = (string)$node->photo;
            if (!empty($photo)) {
                $photos[] = $photo;
            }
        }

        return !empty($photos) ? $photos: '';
    }

    public static function details_url($id)
    {
        return str_replace('__ID__', $id, static::EVENTIVAL_PEOPLE_DETAILS_URL);
    }

    protected static function get_fullname($details)
    {
        return "{$details->names->name_first} {$details->names->name_last}";
    }

    protected static function get_country($details)
    {
        $country = $details->xpath('business_cards/business_card/address/country/human_readable');

        if (!empty($country)) {
            return (string)$country[0];
        }

        return [];
    }

    protected static function get_profession($details)
    {
        $role = $details->xpath('business_cards/business_card/company/role');

        if (!empty($role)) {
            return (string)$role[0];
        }

        return '';
    }

    protected static function get_company($details)
    {
        $company = $details->xpath('business_cards/business_card/company/full_name');

        if (!empty($company)) {
            return (string)$company[0];
        }

        return '';
    }

    protected static function create_post($details, $lang)
    {
        $fullname = static::get_fullname($details);
        $title    = "$fullname {$details->ids->internal_id}";
        $slug     = static::get_slug($title, $lang);

        // WP automatically prepends 'http://' to the guid !
        $guid  = 'http://'.$slug;

        $post_content = $fullname;

        $post = [
            "post_title"    => $fullname,
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

        // Save post
        $post_id = wp_insert_post($post);

        static::save_post_metas($details, $post_id, $lang);

        return $post_id;
    }

    protected static function get_slug($title, $lang)
    {
        return sanitize_title($title).($lang === TKTApp::get_instance()->get_config('i18n.default_lang', 'fr') ? '' : '-'.$lang);
    }

    protected static function save_post_metas($details, $post_id, $lang)
    {
        $photos = static::get_photos($details);
        update_post_meta($post_id, 'internal_id', (string)$details->ids->internal_id);
        update_post_meta($post_id, 'tags', wp_slash(wp_json_encode(static::get_tags($details))));
        update_post_meta($post_id, 'country', static::get_country($details));
        update_post_meta($post_id, 'profession', static::get_profession($details));
        update_post_meta($post_id, 'company', static::get_company($details));
        update_post_meta($post_id, 'photos', $photos ? wp_slash(wp_json_encode($photos)) : null);
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

        do_action('wpml_set_element_language_details', $set_language_args);
    }

    protected static function get_id_from_guid($guid)
    {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid=%s", $guid));
    }
}
