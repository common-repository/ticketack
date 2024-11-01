<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * Ticket connection shortcode
 *
 * Usage:
 *
 * [tkt_ticket_connect]
 */
class TicketConnectShortcode extends TKTShortcode
{
    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_ticket_connect";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $code_message = isset($atts['show_id_code_message']) ? $atts['show_id_code_message'] : null;
        $deprecated   = isset($atts['deprecated']) ? $atts['deprecated'] : '';

        return TKTTemplate::render(
            'ticket/ticket_connect',
            (object)[
                "show_id_code_message" => $code_message,
                "deprecated"           => $deprecated
            ]
        );
    }
}
