<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;
use Ticketack\Core\Models\Tickettype;

/**
 * Buy pass form
 *
 * Usage:
 *
 * [tkt_buy_pass]
 */
class BuyPassShortcode extends TKTShortcode
{
    const REDIRECT_NONE            = 'none';
    const REDIRECT_TO_CART         = 'cart';
    const REDIRECT_TO_TKT_CART     = 'tkt_cart';
    const REDIRECT_TO_TKT_CHECKOUT = 'tkt_checkout';

    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_buy_pass";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $selected = isset($atts['selected']) ? $atts['selected'] : (isset($_GET['selected']) ? sanitize_text_field($_GET['selected']) : null);
        $theme    = isset($atts['theme']) ? $atts['theme'] : 'light';

        if (!empty($_GET['types'])) {
            $atts['types'] = sanitize_text_field($_GET['types']); // override with URL
        }
        $types = isset($atts['types']) ? explode(',', $atts['types']) : [];

        $tickettypes = Tickettype::all()
            ->order_by_opaque_eshop_sort_weight()
            ->for_sellers(['eshop'])
            ->filter_pricings_for_sellers(['eshop'])
            ->get();

        if (!empty($types)) {
            $tickettypes = array_values(array_filter($tickettypes, function ($t) use ($types) {
                return in_array($t->_id(), $types);
            }));
        }

        return TKTTemplate::render(
            'buy_pass/buy',
            (object)[
                'theme'       => $theme,
                'tickettypes' => $tickettypes,
                'selected'    => $selected,
            ]
        );

        return null;
    }
}
