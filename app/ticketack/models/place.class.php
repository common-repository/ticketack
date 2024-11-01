<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\TKTModel;

/**
 * Ticketack Engine Place as found under the 'cinema_hall' name in a Screening.
 */
class Place extends TKTModel implements \JsonSerializable
{
    public static $resource = 'places';

    protected $_id         = null;
    protected $created_at  = null;
    protected $updated_at  = null;
    protected $name        = null;
    protected $cinema      = null;
    protected $address     = null;
    protected $zip         = null;
    protected $city        = null;
    protected $state       = null;
    protected $country     = null;
    protected $coordinates = null;
    protected $map         = null;
    protected $opaque      = null;

    public function __construct(array &$properties = [])
    {
        if (array_key_exists('created_at', $properties)) {
            $this->created_at = tkt_iso8601_to_datetime($properties['created_at']);
            unset($properties['created_at']);
        }
        if (array_key_exists('updated_at', $properties)) {
            $this->updated_at = tkt_iso8601_to_datetime($properties['updated_at']);
            unset($properties['updated_at']);
        }
        parent::__construct($properties);
    }

    public function _id()
    {
        return $this->_id;
    }

    public function created_at()
    {
        return $this->created_at;
    }

    public function updated_at()
    {
        return $this->updated_at;
    }

    public function name()
    {
        return $this->name;
    }

    public function cinema()
    {
        return $this->cinema;
    }

    public function address()
    {
        return $this->address;
    }

    public function zip()
    {
        return $this->zip;
    }

    public function city()
    {
        return $this->city;
    }

    public function state()
    {
        return $this->state;
    }

    public function country()
    {
        return $this->country;
    }

    public function coordinates()
    {
        return $this->coordinates;
    }

    public function map()
    {
        return $this->map;
    }

    public function opaque()
    {
        return $this->opaque;
    }

    public function has_id()
    {
        return is_string($this->_id) && (strlen($this->_id) > 0);
    }

    public function has_created_at()
    {
        return $this->created_at() instanceof Datetime;
    }

    public function has_updated_at()
    {
        return $this->updated_at() instanceof Datetime;
    }

    public function has_address()
    {
        return is_string($this->address) && (strlen($this->address) > 0);
    }

    public function has_zip()
    {
        return is_string($this->zip) && (strlen($this->zip) > 0);
    }

    public function has_state()
    {
        return is_string($this->state) && (strlen($this->state) > 0);
    }

    public function has_country()
    {
        return is_string($this->country) && (strlen($this->country) > 0);
    }

    public function has_coordinates()
    {
        return is_array($this->coordinates) &&
            isset($this->coordinates['lat']) &&
            isset($this->coordinates['long']);
    }

    public function has_map()
    {
        return is_array($this->map);
    }

    public function has_opaque()
    {
        return is_array($this->opaque);
    }

    public function jsonSerialize() : mixed
    {
        $ret = [
           'name'     => $this->name(),
           'cinema'   => $this->cinema(),
           'city'     => $this->city(),
        ];

        if ($this->has_id()) {
            $ret['_id'] = $this->_id();
        }
        if ($this->has_created_at()) {
            $ret['created_at'] = tkt_datetime_to_iso8601($this->created_at());
        }
        if ($this->has_updated_at()) {
            $ret['updated_at'] = tkt_datetime_to_iso8601($this->updated_at());
        }
        if ($this->has_address()) {
            $ret['address'] = $this->address();
        }
        if ($this->has_zip()) {
            $ret['zip'] = $this->zip();
        }
        if ($this->has_state()) {
            $ret['state'] = $this->state();
        }
        if ($this->has_country()) {
            $ret['country'] = $this->country();
        }
        if ($this->has_coordinates()) {
            $ret['coordinates'] = $this->coordinates();
        }
        if ($this->has_map()) {
            $ret['map'] = $this->map();
        }
        if ($this->has_opaque()) {
            $ret['opaque'] = $this->opaque();
        }

        return $ret;
    }
}
