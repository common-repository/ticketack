<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * Booking form shortcode
 *
 * Usage:
 *
 * [tkt_booking_wizard ids="...,..." /]
 *
 */
class BookingWizardShortcode extends TKTShortcode
{
    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_booking_wizard";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $ids = isset($atts['ids']) ? explode(',', $atts['ids']) : null;
        if (is_null($ids)) {
            return null;
        }

        $layout = isset($atts['layout']) ? $atts['layout'] : 'wizard';
        $theme  = isset($atts['theme']) ? $atts['theme'] : 'light';

        return TKTTemplate::render(
            'booking_wizard/'.$layout,
            (object)[
                'ids'   => $ids,
                'theme' => $theme
            ]
        );
    }
}
