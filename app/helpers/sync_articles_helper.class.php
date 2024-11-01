<?php

namespace Ticketack\WP\Helpers;

use Ticketack\Core\Base\No2_HTTP;
use Ticketack\Core\Base\TKTRequest;
use Ticketack\Core\Base\TKTApiException;
use Ticketack\Core\Models\Article;
use Ticketack\Core\Models\User;
use Ticketack\WP\TKTApp;

/**
 * Helper class to handle Ticketack/Wordpress articles/posts synchronization
 */
class SyncArticlesHelper extends SyncHelper
{
    const POST_TYPE = 'tkt-article';

    public static function sync_articles()
    {
        $app          = TKTApp::get_instance();
        $default_lang = $app->get_config('i18n.default_lang', 'fr');
        $salepoint_id = $app->get_config('ticketack.salepoint_id');
        if (!$salepoint_id) {
            tkt_flash_notice(tkt_t("Veuillez choisir un point de vente dans les réglages du module Ticketack"), 'error');
            return false;
        }

        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 0);

        $articles = static::load_articles($salepoint_id);

        if (!empty($articles)) {
            array_map(function ($article) use ($default_lang) {
                $def_post_id = static::create_post($article, $default_lang, /* save_attachments */false);

                if (is_null($def_post_id) || !TKT_WPML_INSTALLED) {
                    return;
                }

                $languages = icl_get_languages('skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str');
                foreach (array_keys($languages) as $lang) {
                    if ($lang == $default_lang) {
                        continue;
                    }
                    $tr_post_id = static::create_post($article, $lang, /* save_attachments */false);
                    if (!is_null($tr_post_id)) {
                        static::link_translations($def_post_id, $tr_post_id, $lang);
                    }
                }
            }, $articles);

            $msg = count($articles) > 1 ?
                tkt_t('%d articles ont été importés') :
                tkt_t('%d article a été importé');
            tkt_flash_notice(sprintf($msg, count($articles)), 'success');

            static::clear_comet_cache();
        } else {
            tkt_flash_notice(tkt_t("Aucun article n'a été importé"));
        }
    }

    protected static function load_articles($salepoint_id)
    {
        $articles = Article::all()
            ->in_pos($salepoint_id)
            ->get('_id,name,short_description,description,category,stock_type,variants,posters');

        return $articles;
    }

    protected static function create_post($article, $lang, $save_attachments)
    {
        $title = $article->name($lang);
        $slug  = tkt_get_article_slug($article, $lang);
        // WP automatically prepends 'http://' to the guid !
        $guid  = 'http://'.$slug;

        $post_content = trim(preg_replace('#\R+#', '', $article->description($lang)));

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
            $post['ID'] = $existing_post->ID;
        }

        // Save post
        $post_id = wp_insert_post($post);

        static::save_post_metas($article, $post_id, $lang);

        return $post_id;
    }

    protected static function save_post_metas($article, $post_id, $lang)
    {
        update_post_meta($post_id, 'id', wp_slash(wp_json_encode($article->_id())));
        update_post_meta($post_id, 'short_description', wp_slash(wp_json_encode($article->short_description($lang))));
        update_post_meta($post_id, 'category', wp_slash(wp_json_encode($article->category())));
        update_post_meta($post_id, 'variants', wp_slash(wp_json_encode($article->variants())));
        update_post_meta($post_id, 'posters', wp_slash(wp_json_encode($article->posters())));
        update_post_meta($post_id, 'stock_type', wp_slash(wp_json_encode($article->stock_type())));
    }
}
