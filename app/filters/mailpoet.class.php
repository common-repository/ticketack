<?php
namespace Ticketack\WP\Filters;

/**
 * Mailpoet filter
 */
class MailpoetFilter extends TKTFilter
{
    /**
     * Get this filter tag
     *
     * @return string: The tag to use
     */
    public function get_tag()
    {
        return "mailpoet_newsletter_shortcode";
    }

    /**
     * Run this filter
     */
    public function run($args = null)
    {
        var_dump($args);
        exit();
        // always return the shortcode if it doesn't match your own!
        if ($shortcode !== '[custom:tkt-event]') {
            return $shortcode;
        }

        return "IT WORKS";
    }
}
