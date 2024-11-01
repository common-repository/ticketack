<?php
namespace Ticketack\Core\Base\Currency;

/**
 * Money interface.
 *
 * Implement a correct logic for money computation operations.
 *
 * @notes Instances are *immutable*.
 *
 * Money, it's a crime
 * Share it fairly but don't take a slice of my pie
 * Money, so they say
 * Is the root of all evil today
 * But if you ask for a rise it's no surprise that they're giving none away
 */

interface Money
{
    /**
     * Parse a textual representation of money and convert it into a new
     * instance.
     *
     * @param $s
     *   A instance of this class or a money textual representation.
     *
     * @return
     *   an instance of this class on success, null otherwise.
     */
    public static function parse($money);

    /**
     * public getter for units
     *
     * @return an int.
     */
    public function units();

    /**
     * public getter for cents
     *
     * @return an int.
     */
    public function cents();

    /**
     * public getter for this money's currency.
     *
     * @return a string.
     */
    public function currency();

    /**
     * compare a given object to this.
     *
     * @return
     *   true if the given argument is equals to this, false otherwise.
     */
    public function equals($opaque);

    /**
     * Add another money to this.
     *
     * @param $other
     *   The value to add to this. see static::parse() for acceptable values.
     *
     * @throw
     *   InvalidArgumentException when $other could not be parsed.
     *
     * @return
     *   an instance of this class that is the sum of $this and $other.
     */
    public function add($other);

    /**
     * Convert an instance of this class into a string.
     *
     * @return
     *   A string that can be parsed by static::parse.
     */
    public function __toString();
}
