<?php
namespace Ticketack\WP\Actions;

use Ticketack\WP\TKTApp;
use Ticketack\WP\helpers\SyncHelper;

/**
 * Api action
 */
class ApiAction extends TKTAction
{
    /**
     * Get this action tag(s)
     *
     * @return mixed: A single tag (which will call the <code>run</code> method)
     *                or an associative array with the tag as key and the method to call as value.
     */
    public function get_tag()
    {
        return "rest_api_init";
    }

    /**
     * Run this action
     */
    public function run()
    {
        register_rest_route('ticketack/v1', '/settings/refresh', [
            'methods' => 'POST',
            'callback' => function () {
                $app = TKTApp::get_instance();
                if (!$app->load_config(/*$force_refresh*/true)) {
                    return new WP_Error('error', 'Could not refresh settings', ['status' => 500]);
                }

                return ['status' => 'success'];
            }
        ]);

        register_rest_route('ticketack/v1', '/events/import', [
            'methods' => 'POST',
            'callback' => function () {
                SyncHelper::sync_events();
                return ['status' => 'success'];
            }
        ]);
    }
}
