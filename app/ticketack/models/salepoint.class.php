<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\TKTModel;
use Ticketack\Core\Base\No2_HTTP;

/**
 * Ticketack Engine Salepoint.
 */

class Salepoint extends TKTModel implements \JsonSerializable
{
    const POS_LAYOUT_TICKETS  = 'tickets';
    const POS_LAYOUT_ARTICLES = 'articles';

    /**
     * @override
     */
    public static $resource = 'salepoints';

    protected $_id      = null;
    protected $name     = null;
    protected $delivery = null;
    protected $pos_layout = null;
    protected $settings = [];

    protected static $salepoints = null; // cache
    protected static $cashregisters = null; // cache

    /**
     * @return true if given id is a valid salepoint id, false otherwise.
     */
    public static function is_valid_id($id)
    {
        return tkt_is_uuidv4($id);
    }

    /**
     * @override
     * XXX: if you change something here, double check jsonSerialize() and
     * update the unit test
     */
    public function __construct(array &$properties = [])
    {
        parent::__construct($properties);
    }

    public function _id()
    {
        return $this->_id;
    }

    public function name($lang = null)
    {
        return is_null($lang) ? $this->name : (isset($this->name[$lang]) ? $this->name[$lang] : tkt_t("non défini"));
    }

    public function delivery()
    {
        return $this->delivery;
    }

    public function pos_layout()
    {
        return $this->pos_layout;
    }

    public function has_pos_layout()
    {
        return is_string($this->pos_layout) && (strlen($this->pos_layout) > 0);
    }

    public function settings()
    {
        return $this->settings;
    }

    /**
     * getter for a setting value.
     *
     * @param $key
     *   The setting option needed.
     *
     * @param $default (null)
     *   The value to return if the setting option is not set, default to null.
     *
     * @return
     *   The setting value if it exists, null otherwise.
     *
     * <b>Example</b>
     * @code
     *   $salepoint->setting('customer.name') # same as $salepoint->setting(['customer', 'name'])
     * @endcode
     */
    public function setting($key, $default = null)
    {
        $desc     = (is_array($key) ? $key : explode('.', strval($key)));
        $setting = (array)$this->settings;

        foreach ($desc as $atom) {
            if (is_array($setting) && array_key_exists($atom, $setting)) {
                $setting = $setting[$atom];
            } else {
                return $default;
            }
        }

        return $setting;
    }

    public function has_id()
    {
        return is_string($this->_id) && (strlen($this->_id) > 0);
    }

    public function cashregisters()
    {
        if (is_null(static::$cashregisters)) {
            static::$cashregisters = Cashregister::all()->get(null, /* allow cached results */ true);
            usort(static::$cashregisters, function($a, $b) {return strcmp($a->name(TKT_LANG), $b->name(TKT_LANG));});
        }

        return array_values(array_filter(
            static::$cashregisters,
            function ($c) {
                return $c->salepoint_id() === $this->_id;
            }
        ));
    }

    /**
     * Scope to get salepoints from their ids
     */
    public static function scope_ids($req, $ids)
    {
        return $req->query('_ids', implode(',', $ids));
    }

    public function jsonSerialize() : mixed
    {
        $ret = [
           'name'     => $this->name(),
           'delivery' => $this->delivery(),
           'settings'   => $this->settings()
        ];

        if ($this->has_id()) {
            $ret['_id'] = $this->_id();
        }

        if ($this->has_pos_layout()) {
            $ret['pos_layout'] = $this->pos_layout();
        }

        return $ret;
    }
}
