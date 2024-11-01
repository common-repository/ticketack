<?php
namespace Ticketack\WP\Actions;

use Ticketack\WP\TKTApp;

/**
 * Assets action
 */
class AssetsAction extends TKTAction
{
    /**
     * Get this action tag(s)
     *
     * @return mixed: A single tag (which will call the <code>run</code> method)
     *                or an associative array with the tag as key and the method to call as value.
     */
    public function get_tag()
    {
        return "wp_enqueue_scripts";
    }

    /**
     * Run this action
     */
    public function run()
    {
        wp_enqueue_style('tkt-main-css', tkt_assets_url('build/main.css'));
        if (file_exists(TKT_OVERRIDE_DIR.'/tkt_override.css')) {
            wp_enqueue_style('tkt-override-css', get_stylesheet_directory_uri().'/tkt_override.css?t='.time());
        }
        wp_enqueue_script('jquery');
        $load_underscore_at_the_end = (bool)TKTApp::get_instance()->get_config('advanced.load_underscore_at_the_end', false);
        if ($load_underscore_at_the_end) {
            wp_enqueue_script(
                'underscore',
                /*src*/'',
                /*deps*/[],
                /*ver*/false,
                /*in_footer*/true
            );
        }
    }
}
