<?php
namespace Ticketack\WP\Actions;

/**
 * Admin Menu action
 */
class TranslationAction extends TKTAction
{
    /**
     * Get this action tag(s)
     *
     * @return mixed: A single tag (which will call the <code>run</code> method)
     *                or an associative array with the tag as key and the method to call as value.
     */
    public function get_tag()
    {
        return "init";
    }

    /**
     * Run this action
     */
    public function run()
    {
        load_plugin_textdomain(
            'wpticketack',
            FALSE,
            dirname(plugin_basename(__FILE__)).'/../locales/'
        );
    }
}
