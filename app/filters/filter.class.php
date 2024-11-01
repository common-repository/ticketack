<?php
namespace Ticketack\WP\Filters;

use Ticketack\WP\TKTApp;

/**
 * Base filter
 */

abstract class TKTFilter
{
    /**
     * @var TKTApp
     */
    protected $app;

    public function __construct(TKTApp $app)
    {
        $this->app = $app;
        add_filter(
            $this->get_tag(),
            array($this, 'run')
        );
    }

    /**
     * Get this Filter tag
     *
     * @return string: The tag to use to run this filter
     */
    abstract public function get_tag();

    /**
     *Run this Filter
     */
    abstract public function run($args = null);
}


