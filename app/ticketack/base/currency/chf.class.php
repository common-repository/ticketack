<?php
namespace Ticketack\Core\Base\Currency;

/**
 * CHF (Swiss Franc) Model.
 *
 * @see Money
 */

class CHF implements Money, \JsonSerializable
{
    // computed by max_units_value(), don't access directly.
    static private $max_units_value = null;

    static protected $regexp = '/^\s*(?:CHF\s+|Fr\.\s+)?(\-?\d{1,3}(?:\'\d\d\d)*|\d+)(?:\.(\d\d*|-))?\s*$/';

    public static function max_units_value()
    {
        if (is_null(static::$max_units_value)) {
            static::$max_units_value = preg_replace('/\d\d$/', '', strval(PHP_INT_MAX));
        }
        return static::$max_units_value;
    }

    /**
     * Parse a textual representation of swiss franc and convert it into a new
     * CHF object.
     *
     * @param $s
     *   A CHF instance or a money textual representation.
     *   Example:
     *     "9001"
     *     "CHF 42'145.15"
     *     "CHF 2.10"
     *     "CHF 42.-"
     *     "Fr. 42.-"
     *     "0.65"
     *     "84.-"
     *
     * @return
     *   A CHF instance on success, null otherwise.
     */
    public static function parse($money)
    {
        if ($money instanceof static) {
            return $money;
        }

        $result  = null;
        $matches = [];
        if (preg_match(static::$regexp, strval($money), $matches)) {
            $units  = str_replace("'", "", $matches[1]);
            $cents  = (count($matches) < 3 || $matches[2] === '-' ? 0 : $matches[2]);
            $result = new static($units, $cents);
        }
        return $result;
    }

    protected $units;
    protected $cents;
    protected $currency = 'CHF';

    /**
     * CHF constructor
     *
     * This constructor is protected, use CHF::parse() instead.
     *
     * @param $units (int)
     *   The amount of units
     *
     * @param $cents (int)
     *   The amount of cents
     */
    protected function __construct($units, $cents)
    {
        $this->units   = intval($units);
        $this->cents    = intval($cents);

        if ($this->cents() < 0) {
            throw new InvalidArgumentException('cents cannot be lesser than zero');
        } elseif ($this->cents() >= 100) {
            throw new InvalidArgumentException('cents cannot be greater than 99');
        }
    }

    /**
     * public getter for units
     *
     * @return an int.
     */
    public function units()
    {
        return $this->units;
    }

    /**
     * public getter for cents
     *
     * @return an int.
     */
    public function cents()
    {
        return $this->cents;
    }

    /**
     * public getter for currency
     *
     * @return a string.
     */
    public function currency()
    {
        return $this->currency;
    }

    /**
     * compare a given object to this.
     *
     * @return
     *   true if the given argument is equals to this, false otherwise.
     */
    public function equals($opaque)
    {
        $other = static::parse($opaque);
        return ($other instanceof static &&
            $this->units() === $other->units() &&
            $this->cents()  === $other->cents()
        );
    }

    /**
     * Add another CHF to this.
     *
     * @param $other
     *   The value to add to this. see static::parse() for acceptable values.
     *
     * @throw
     *   InvalidArgumentException when $other could not be parsed.
     *
     * @return
     *   a new CHF instance that is the sum of $this and $other.
     */
    public function add($other)
    {
        $to_add = static::parse($other);
        if (is_null($to_add)) {
            throw new InvalidArgumentException($other);
        }

        $units  = $this->units() + $to_add->units();
        $cents  = $this->cents()  + $to_add->cents();
        if ($cents >= 100) { // at most 99 + 99 = 198
            $units += 1;
            $cents -= 100;
        }
        return new static($units, $cents);
    }

    /**
     * Return a float value of this money
     *
     * @return float
     */
    public function value()
    {
        return floatval(
            $this->units() +
            $this->cents() / 100
        );
    }

    /**
     * Convert a CHF instance into a string.
     *
     * @return
     *   A string that can be parsed by static::parse.
     */
    public function __toString()
    {
        return sprintf("%s %d.%02d", $this->currency(), $this->units(), $this->cents());
    }

    public function jsonSerialize() : mixed
    {
        return (float)sprintf("%d.%02d", $this->units(), $this->cents());
    }

    /**
     * Prepare $float for CHF::parse
     *
     * @return string
     */
    public static function prepare($float)
    {
        $float = (float)str_replace(',', '.', $float);
        return number_format((float)$float, 2, '.', '');
    }
}
