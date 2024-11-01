<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;
use Ticketack\WP\TKTApp;

/**
 * Cart shortcode
 *
 * Usage:
 *
 * [tkt_cart hidden_links="finalize,cancel,continue"]
 *
 * Default layout is "screenings"
 */
class CartShortcode extends TKTShortcode
{
    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_cart";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $enable_promo_code = TKTApp::get_instance()->get_config('eshop.enable_promo_code');
        $hidden_links      = array_key_exists('hide_links', (array)$atts) ? $atts['hide_links'] : '';
        $theme             = array_key_exists('theme', (array)$atts) ? $atts['theme'] : 'light';

        return TKTTemplate::render(
            'cart/cart',
            (object)[
                'hidden_links'      => $hidden_links,
                'enable_promo_code' => $enable_promo_code,
                'theme'             => $theme
            ]
        );
    }
}
