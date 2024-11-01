<?php
namespace Ticketack\WP\Actions;

use Ticketack\WP\TKTApp;
use Ticketack\WP\helpers\SyncPeopleHelper;

/**
 * Sync people action
 */
class SyncPeopleAction extends TKTAction
{
    /**
     * Get this action tag(s)
     *
     * @return mixed: A single tag (which will call the <code>run</code> method)
     *                or an associative array with the tag as key and the method to call as value.
     */
    public function get_tag()
    {
        return [
            "admin_head-edit.php"    => "add_link",
            "admin_post_sync_people" => "run"
        ];
    }

    /**
     * Add the link in Posts admin listing page
     */
    public function add_link()
    {
        // FIXME we should replace this link by something like "Plan an import" button which
        // will add a flag in wp_options triggered by a cron to launch a new import
        return false;
    }

    /**
     * Run the synchronization
     */
    public function run()
    {
        SyncPeopleHelper::sync();

        wp_redirect(admin_url("edit.php?post_type=tkt-people"));
        exit;
    }
}
