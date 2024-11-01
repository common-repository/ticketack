<?php
namespace Ticketack\WP\Filters;

/**
 * Thumbnail filter
 */
class ThumbnailFilter extends TKTFilter
{
    /**
     * Get this filter tag
     *
     * @return string: The tag to use
     */
    public function get_tag()
    {
        return "post_thumbnail_html";
    }

    /**
     * Run this filter
     */
    public function run($args = null)
    {
        $post = get_post();
        if ( in_array($post->post_type, ['tkt-event', 'tkt-article']) && is_single() ) {
            return null;
        }
        $raw_posters = get_post_meta($post->ID, 'posters');

        if (!empty($raw_posters)) {
            $posters = array_map(function ($s) {
                return json_decode($s);
            }, $raw_posters);

            if (!empty($posters)) {
                $posters = $posters[0];
            }

            if (!empty($posters)) {
                return '<img src="'.$posters[0]->url.'" />';
            }
        }

        return $args;
    }
}
