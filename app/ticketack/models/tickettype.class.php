<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\TKTModel;
use Ticketack\Core\Base\No2_HTTP;
use Ticketack\WP\TKTApp;

/**
 * Ticketack Engine Tickettype.
 */

class Tickettype extends TKTModel implements \JsonSerializable
{
    /**
     * @override
     */
    public static $resource = 'tickettypes';

    const ONE_TIME_PASS_ID = 'one-time-pass';

    protected $_id         = null;
    protected $name        = null;
    protected $description = null;
    protected $windows     = [];
    protected $pricings    = null;
    protected $opaque      = null;

    /**
     * @override
     */
    public function __construct(array &$properties = [])
    {
        if (array_key_exists('windows', $properties)) {
            $this->windows = array_map(function ($obj) {
                return new Window($obj);
            }, $properties['windows']);
            unset($properties['windows']);
        }
        if (array_key_exists('pricings', $properties)) {
            $this->pricings = [];
            foreach ($properties['pricings'] as $key => $obj) {
                $pricing = tkt_id(new Pricing($obj))->set_key($key);
                $this->pricings[$key] = $pricing;
            }
            unset($properties['pricings']);
        }
        parent::__construct($properties);
    }

    // helper for scope_order_by_opaque_eshop_sort_weight()
    public static function opaque_eshop_sort_weight_cmp($a, $b)
    {
        $a_weight = ($a->has_opaque() && isset($a->opaque()['eshop_sort_weight']) ? $a->opaque()['eshop_sort_weight'] : null);
        $b_weight = ($b->has_opaque() && isset($b->opaque()['eshop_sort_weight']) ? $b->opaque()['eshop_sort_weight'] : null);
        if (is_null($a_weight) && is_null($b_weight)) {
            return  0;
        } elseif (is_null($a_weight)) {
            return -1;
        } elseif (is_null($b_weight)) {
            return  1;
        }

        $diff = doubleval($a_weight) - doubleval($b_weight);
        if ($diff < 0) {
            return -1;
        } if ($diff > 0) {
            return  1;
        } else {
            return  0;
        }
    }

    /**
     * scope filtering tickettypes that cannot be sold by a user having given
     * $roles.
     */
    public static function scope_for_sellers($req, $roles)
    {
        return $req->add_post_process(function ($status, $tickettypes) use ($roles) {
            if (No2_HTTP::is_success($status)) {
                $tickettypes = array_values(array_filter($tickettypes, function ($tickettype) use ($roles) {
                    return (count($tickettype->pricings_for_sellers($roles)) > 0);
                }));
            }
            return $tickettypes;
        });
    }

    /**
     * scope filtering pricings that cannot be sold by a user having given
     * $roles.
     */
    public static function scope_filter_pricings_for_sellers($req, $roles)
    {
        return $req->add_post_process(function ($status, $tickettypes) use ($roles) {
            if (No2_HTTP::is_success($status)) {
                $tickettypes = array_map(function ($tickettype) use ($roles) {
                    $tickettype->pricings = $tickettype->pricings_for_sellers($roles);
                    return $tickettype;
                }, $tickettypes);
            }
            return $tickettypes;
        });
    }

    /**
     * Scope filtering pricings that can be sold given an array of user roles.
     */
    public static function scope_sellable_by($req, $roles)
    {
        return $req->filter_pricings_for_sellers($roles)
                   ->for_sellers($roles);
    }

    /**
     * scope sorting tickettypes depending on the earliest window's start_at
     * (or window matching screening start_at)
     */
    public static function scope_order_by_earliest_window_start_at($req)
    {
        return $req->add_post_process(function ($status, $tickettypes) {
            if (No2_HTTP::is_success($status)) {
                usort($tickettypes, function ($a, $b) {
                    return Window::timestamp_cmp($a->earliest_window(), $b->earliest_window());
                });
            }
            return $tickettypes;
        });
    }

    /**
     * scope sorting tickettypes depending on opaque.eshop_sort_weight and
     * fallback on the earlist window's start_at.
     */
    public static function scope_order_by_opaque_eshop_sort_weight($req)
    {
        return $req->add_post_process(function ($status, $tickettypes) {
            if (No2_HTTP::is_success($status)) {
                usort($tickettypes, function ($a, $b) {
                    $weight_diff = Tickettype::opaque_eshop_sort_weight_cmp($a, $b);
                    return ($weight_diff ?: Window::timestamp_cmp($a->earliest_window(), $b->earliest_window()));
                });
            }
            return $tickettypes;
        });
    }

    public function pricings_for_sellers($roles)
    {
        return array_filter($this->pricings, function ($pricing) use ($roles) {
            return $pricing->can_be_sold_by($roles);
        });
    }

    public function earliest_window()
    {
        $windows = $this->windows;
        if (count($windows) === 0) {
            return null;
        }

        usort($windows, function ($a, $b) {
            return Window::timestamp_cmp($a, $b);
        });

        return $windows[0];
    }

    public function _id()
    {
        return $this->_id;
    }

    public function name($lang)
    {
        return isset($this->name[$lang]) ? $this->name[$lang] : null;
    }

    public function description($lang)
    {
        return isset($this->description[$lang]) ? $this->description[$lang] : null;
    }

    public function windows()
    {
        return $this->windows;
    }

    public function pricings()
    {
        return $this->pricings;
    }

    public function pricing($key)
    {
        return array_key_exists($key, $this->pricings()) ? $this->pricings[$key] : null;
    }

    public function opaque()
    {
        return $this->opaque;
    }

    public function has_opaque()
    {
        return is_array($this->opaque);
    }

    public function required_fields($salepoint_id)
    {
        if (empty($this->opaque["fields"])) {
            return [];
        }

        if (!array_key_exists($salepoint_id, $this->opaque["fields"])) {
            $salepoint_id = 'default';
        }
        return $this->opaque["fields"][$salepoint_id]["required"];
    }

    public function requested_fields($salepoint_id)
    {
        if (empty($this->opaque["fields"])) {
            return [];
        }

        if (!array_key_exists($salepoint_id, $this->opaque["fields"])) {
            $salepoint_id = 'default';
        }
        return $this->opaque["fields"][$salepoint_id]["requested"];
    }

    public function jsonSerialize() : mixed
    {
        $ret = [
            '_id'         => $this->_id(),
            'name'        => $this->name,
            'description' => $this->description,
            'windows'     => $this->windows(),
            'pricings'    => $this->pricings(),
        ];

        if ($this->has_opaque()) {
            $ret['opaque'] = $this->opaque();
        }

        return $ret;
    }
}
