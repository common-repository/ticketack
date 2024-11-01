<?php
namespace Ticketack\WP\Actions;

use Ticketack\WP\TKTApp;

/**
 * Admin Notices action
 */
class AdminNoticesAction extends TKTAction
{
    /**
     * Get this action tag(s)
     *
     * @return mixed: A single tag (which will call the <code>run</code> method)
     *                or an associative array with the tag as key and the method to call as value.
     */
    public function get_tag()
    {
        return "admin_notices";
    }

    /**
     * Run this action
     */
    public function run()
    {
        global $pagenow;

        if ($pagenow != 'options-general.php' && !TKTApp::get_instance()->is_configured()) {
            tkt_flash_notice(
                sprintf(
                    '%s <a href="%s">%s</a>',
                    esc_html(tkt_t('Ticketack n\'est pas configuré.')),
                    esc_attr(admin_url('options-general.php?page=ticketack-admin')),
                    esc_html(tkt_t('Accéder aux réglages'))
                )
            );
        }

        // Iterates trough notices and display them
        $notices = get_option('tkt_flash_notices', []);

        foreach ($notices as $notice) {
            printf('<div class="notice notice-%s %s"><p>%s</p></div>',
                esc_attr($notice['type']),
                esc_attr($notice['dismissible']),
                esc_html($notice['notice'])
            );
        }

        // Reset our options to prevent notices being displayed forever
        if(!empty($notices)) {
            delete_option('tkt_flash_notices', []);
        }
    }
}
