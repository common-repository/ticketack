<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;
use Ticketack\WP\TKTApp;

/**
 * Cart summary shortcode
 *
 * Usage:
 *
 * [tkt_cart_summary]
 */
class CartSummaryShortcode extends TKTShortcode
{
    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_cart_summary";
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
        $theme             = array_key_exists('theme', (array)$atts) ? $atts['theme'] : 'light';


        return TKTTemplate::render(
            'cart/cart_summary',
            (object)[
                'enable_promo_code' => $enable_promo_code,
                'theme'             => $theme
            ]
        );
    }
}
