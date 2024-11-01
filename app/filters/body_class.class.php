<?php
namespace Ticketack\WP\Filters;

/**
 * BodyClass filter
 */
class BodyClassFilter extends TKTFilter
{
    /**
     * Get this filter tag
     *
     * @return string: The tag to use
     */
    public function get_tag()
    {
        return "body_class";
    }

    /**
     * Run this filter
     */
    public function run($args = null)
    {
        $classes = $args;
        global $post;

        $shortcodes = [
            'tkt_program',
            'tkt_cart',
            'tkt_event',
            'tkt_screening',
            'tkt_buy_pass'
        ];

        if (isset($post->post_content)) {
            foreach ($shortcodes as $shortcode) {
                if(has_shortcode($post->post_content, $shortcode ) ) {
                    $classes[] = 'body_tkt';
                    $classes[] = 'body_'.$shortcode;
                }
            }
        }
        if (isset($post->post_type)) {
            if ($post->post_type == 'tkt-event') {
                $classes[] = 'body_tkt_event';
            } elseif ($post->post_type == 'tkt-article') {
                $classes[] = 'body_tkt_article';
            } elseif ($post->post_type == 'tkt-people') {
                $classes[] = 'body_tkt_people';
            }
        }

        return $classes;
    }
}
