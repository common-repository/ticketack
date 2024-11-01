<?php
namespace Ticketack\Core\Models;

use Ticketack\WP\TKTApp;
use Ticketack\Core\Base\Currency\Currency;

/**
 * Ticketack Engine helper for article variant (used in Articles).
 *
 * @notes
 *  Instances are *immutable*.
 */

class ArticleVariant implements \JsonSerializable
{
    protected $_id                 = null;
    protected $name                = null;
    protected $stocks              = [];
    protected $stock_factor        = null;
    protected $gtin                = null;
    protected $sku                 = null;
    protected $price               = [];
    protected $purchasing_price    = [];
    protected $variable_price      = false;
    protected $value               = [];
    protected $vat                 = 0;
    protected $stocks_by_salepoint = [];

    protected $article = null;

    /**
     * @throw Exception
     */
    public function __construct(Article $article, array &$properties = [])
    {
        $currency           = TKTApp::get_instance()->get_config('currency', 'CHF');
        $this->article      = $article;
        $this->name         = isset($properties['name']) ? $properties['name'] : new stdClass();
        $this->stock_factor = isset($properties['stock_factor']) ? $properties['stock_factor'] : -1;
        $this->gtin         = isset($properties['gtin']) ? (string)$properties['gtin'] : "";
        $this->sku          = isset($properties['sku']) ? (string)$properties['sku'] : "";
        if (isset($properties['price'])) {
            $this->price = (object)[$currency => Currency::parse(Currency::prepare($properties['price'][$currency]))];
        } else {
            $this->price = new \stdClass();
        }

        if (isset($properties['_id'])) {
            $this->_id = $properties['_id'];
        }

        if (isset($properties['value']) && isset($properties['value'][$currency])) {
            $this->value = (object)[$currency => Currency::parse(Currency::prepare($properties['value'][$currency]))];
        } else {
            $this->value = $this->price;
        }

        if (isset($properties['purchasing_price']) && isset($properties['purchasing_price'][$currency])) {
            $this->purchasing_price = (object)[$currency => Currency::parse(Currency::prepare($properties['purchasing_price'][$currency]))];
        } else {
            $this->purchasing_price = (object)[$currency => Currency::parse('0.00')];
        }

        if (isset($properties['variable_price'])) {
            $this->variable_price = (boolean)$properties['variable_price'];
        }

        $vat = isset($properties['vat']) ? floatval($properties['vat']) : 0.00;
        if ($vat < 0 || $vat > 100) {
            throw new \InvalidArgumentException("$vat: invalid vat value");
        }
        $this->vat = $vat;

        if (array_key_exists('stocks', $properties)) {
            $this->stocks = [];
            foreach ($properties['stocks'] as $obj) {
                array_push($this->stocks, new ArticleStock($obj));
            }
            unset($properties['stocks']);
        }
    }

    /* setters */

    /**
     * Set this pricing _id.
     *
     * @param string $_id
     *
     * @return ArticleVariant
     */
    public function set_id($_id)
    {
        $this->_id = $_id;

        return $this;
    }

    public function set_stocks_by_salepoint($salepoint, $quantity)
    {
        if (!array_key_exists($salepoint, $this->stocks_by_salepoint)) {
            $this->stocks_by_salepoint[$salepoint] = $quantity;
        } elseif ($quantity !== null) {
            $this->stocks_by_salepoint[$salepoint] += $quantity;
        }

        return $this;
    }

    /* getters */

    public function _id()
    {
        return $this->_id;
    }

    public function name($lang = null)
    {
        if (is_null($lang)) {
            return $this->name;
        }

        return isset($this->name[$lang]) ? $this->name[$lang] : null;
    }

    public function stocks()
    {
        return $this->stocks;
    }

    public function stock_factor()
    {
        return is_float($this->stock_factor) ? (float)$this->stock_factor : (int)$this->stock_factor;
    }

    public function gtin()
    {
        return !empty($this->gtin) ? (string)$this->gtin : "";
    }

    public function sku()
    {
        return !empty($this->sku) ? (string)$this->sku : "";
    }

    public function price($currency = null)
    {
        $currency = $currency ?? TKTApp::get_instance()->get_config('currency', 'CHF');
        return $this->price->$currency ?? null;
    }

    public function value($currency = null)
    {
        $currency = $currency ?? TKTApp::get_instance()->get_config('currency', 'CHF');
        return $this->value->$currency ?? null;
    }

    public function purchasing_price($currency = null)
    {
        $currency = $currency ?? TKTApp::get_instance()->get_config('currency', 'CHF');
        return $this->purchasing_price->$currency ?? null;
    }

    public function is_variable_price()
    {
        return $this->variable_price;
    }

    public function vat()
    {
        return $this->vat;
    }

    public function has_id()
    {
        return is_string($this->_id) && (strlen($this->_id) > 0);
    }

    public function jsonSerialize() : mixed
    {
        $ret = [
            'name'                => $this->name(),
            'stocks'              => $this->stocks(),
            'stock_factor'        => $this->stock_factor(),
            'stocks_by_salepoint' => $this->stocks_by_salepoint,
            'stock_type'          => $this->article->stock_type(),
            'gtin'                => $this->gtin(),
            'sku'                 => $this->sku(),
            'price'               => $this->price,
            'value'               => $this->value,
            'purchasing_price'    => $this->purchasing_price,
            'variable_price'      => $this->variable_price,
            'vat'                 => $this->vat()
        ];

        if ($this->has_id()) {
            $ret['_id'] = $this->_id();
        }

        return $ret;
    }

    public function has_stock_for_salepoint($salepoint_id)
    {
        foreach ($this->stocks as $s) {
            if ($s->check_stock($salepoint_id)) {
                return true;
            }
        }

        return false;
    }
}
