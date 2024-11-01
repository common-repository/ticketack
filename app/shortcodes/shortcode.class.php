<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\TKTApp;

/**
 * Base shortcode
 *
 * Usage:
 *
 * [tkt_program]
 */
abstract class TKTShortcode
{
    /**
     * @var TKTApp
     */
    protected $app;

    public function __construct(TKTApp $app)
    {
        $this->app = $app;

        add_shortcode(
            $this->get_tag(),
            array($this, 'run')
        );
    }

    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    abstract public function get_tag();

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    abstract public function run($atts, $content);
}

