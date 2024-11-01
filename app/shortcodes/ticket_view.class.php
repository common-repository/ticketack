<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * Ticket view shortcode
 *
 * Usage:
 *
 * [tkt_ticket_view [_id="12345678-1234-1234-4321-123456789012"]]
 */
class TicketViewShortcode extends TKTShortcode
{
    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_ticket_view";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $_id      = tkt_get_url_param('uuid', (isset($atts['_id']) ? $atts['_id'] : null));
        $theme    = isset($atts['theme']) ? $atts['theme'] : 'light';

        return TKTTemplate::render(
            'ticket/ticket_view',
            (object)[
                'theme'       => $theme,
                'ticket_id'   => $_id,
            ]
        );
    }
}
