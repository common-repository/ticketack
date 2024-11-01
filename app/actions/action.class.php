<?php
namespace Ticketack\WP\Actions;

use Ticketack\WP\TKTApp;

/**
 * Base action
 */
abstract class TKTAction
{
    /**
     * @var TKTApp
     */
    protected $app;

    public function __construct(TKTApp $app)
    {
        $this->app = $app;

        $tags = $this->get_tag();
        if (!is_array($tags)) {
            $tags = [$tags => 'run'];
        }

        foreach ($tags as $tag => $method) {
            add_action(
                $tag,
                array($this, $method),
                $this->get_priority()
            );
        }
    }

    /**
     * Get this action tag(s)
     *
     * @return mixed: A single tag (which will call the <code>run</code> method)
     *                or an associative array with the tag as key and the method to call as value.
     */
    abstract public function get_tag();

    /**
     * Run this action
     */
    abstract public function run();

    /**
     * Get this action priority
     *
     * @return int: Action priority
     */
    public function get_priority()
    {
        return 10;
    }
}

