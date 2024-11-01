<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * User registration shortcode
 *
 * Usage:
 *
 * [tkt_user_account tabs="profile,orders,bookings,pass"]
 */
class UserAccountShortcode extends TKTShortcode
{
    const MENU_ENTRY_PROFILE  = 'profile';
    const MENU_ENTRY_ORDERS   = 'orders';
    const MENU_ENTRY_TICKETS  = 'tickets';
    const MENU_ENTRY_VOTES    = 'votes';
    const MENU_ENTRY_LOGOUT   = 'logout';

    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_user_account";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $default_tabs = [
            static::MENU_ENTRY_ORDERS,
            static::MENU_ENTRY_TICKETS,
            static::MENU_ENTRY_VOTES,
            static::MENU_ENTRY_PROFILE,
            static::MENU_ENTRY_LOGOUT,
        ];
        $tabs = isset($atts['tabs']) ? explode(',', $atts['tabs']) : $default_tabs;
        $active_tab = tkt_get_url_param('tab', current($tabs));

        return TKTTemplate::render(
            'user/user_account',
            (object)[
                'tabs'       => $tabs,
                'active_tab' => $active_tab
            ]
        );
    }
}
