<?php
namespace Ticketack\Core\Models;

use Ticketack\WP\TKTApp;
use Ticketack\Core\Base\Currency\CHF;

/**
 * Ticketack Engine helper for pricings (used in Tickettypes, Tickets and Screenings).
 *
 * @notes
 *  Instances are *immutable*.
 */

class Pricing implements \JsonSerializable
{
    protected $key = null;
    protected $name = null;
    protected $description = null;
    protected $price = [];
    protected $value = [];
    protected $VAT = 0;
    protected $sellers = [];
    protected $category = null;
    protected $opaque = null;

    /**
     * @throw Exception
     */
    public function __construct(array &$properties = [])
    {
        $this->name = $properties['name'];
        if (array_key_exists('description', $properties)) {
            $this->description = $properties['description'];
        }

        $this->price = ['CHF' => CHF::parse($properties['price']['CHF'])];
        if (array_key_exists('value', $properties) && isset($properties['value']['CHF'])) {
            $this->value = ['CHF' => CHF::parse($properties['value']['CHF'])];
        } else {
            $this->value = $this->price;
        }

        $VAT = floatval($properties['VAT']);
        if ($VAT < 0 || $VAT > 100) {
            throw new \InvalidArgumentException("$VAT: invalid VAT value");
        }
        $this->VAT = $VAT;

        $this->sellers = $properties['sellers'];

        if (array_key_exists('category', $properties)) {
            $this->category = $properties['category'];
        }

        if (array_key_exists('opaque', $properties)) {
            $this->opaque = $properties['opaque'];
        }
    }

    /* setters */

    /**
     * Set this pricing key.
     *
     * @param string $key
     *
     * @return Pricing
     */
    public function set_key($key)
    {
        $this->key = $key;

        return $this;
    }

    /* getters */

    public function key()
    {
        return $this->key;
    }

    public function name($lang)
    {
        return isset($this->name[$lang]) ? $this->name[$lang] : null;
    }

    public function description($lang)
    {
        return isset($this->description[$lang]) ? $this->description[$lang] : null;
    }

    public function price($currency = null)
    {
        $currency = $currency ?? TKTApp::get_instance()->get_config('currency', 'CHF');
        return $this->price[$currency] ?? null;
    }

    public function value($currency = null)
    {
        $currency = $currency ?? TKTApp::get_instance()->get_config('currency', 'CHF');
        return $this->value[$currency] ?? null;
    }

    public function VAT()
    {
        return $this->VAT;
    }

    public function sellers()
    {
        return $this->sellers;
    }

    public function category()
    {
        return $this->category;
    }

    public function opaque()
    {
        return $this->opaque;
    }

    public function has_description()
    {
        return is_array($this->description);
    }

    public function has_category()
    {
        return is_string($this->category) && (strlen($this->category) > 0);
    }

    public function has_opaque()
    {
        return is_array($this->opaque);
    }

    public function can_be_sold_by($roles)
    {
        return (count(array_intersect($this->sellers, $roles)) > 0);
    }

    public function jsonSerialize() : mixed
    {
        $ret = [
            'name'    => $this->name,
            'price'   => $this->price,
            'value'   => $this->value,
            'VAT'     => $this->VAT(),
            'sellers' => $this->sellers(),
        ];

        if ($this->has_description()) {
            $ret['description'] = $this->description;
        }
        if ($this->has_category()) {
            $ret['category'] = $this->category();
        }
        if ($this->has_opaque()) {
            $ret['opaque'] = $this->opaque();
        }

        return $ret;
    }
}
