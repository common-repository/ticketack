<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\TKTModel;
use Ticketack\Core\Base\TKTRequest;
use Ticketack\Core\Base\No2_HTTP;

/**
 * Ticketack Engine Cashregister.
 */

class Cashregister extends TKTModel implements \JsonSerializable
{
    /**
     * @override
     */
    public static $resource         = 'cashregisters';

    protected $_id                  = null;
    protected $name                 = null;
    protected $auto_closing         = false;
    protected $cash_balance         = [];
    protected $salepoint_id         = null;

    protected static $cashregisters = null; // cache

    /**
     * @return true if given id is a valid salepoint id, false otherwise.
     */
    public static function is_valid_id($id)
    {
        return tkt_is_uuidv4($id);
    }

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
        return is_null($lang) ? $this->name : $this->name[$lang];
    }

    public function auto_closing()
    {
        return $this->auto_closing;
    }

    public function cash_balance()
    {
        return $this->cash_balance;
    }

    public function salepoint_id()
    {
        return $this->salepoint_id;
    }

    public function salepoint()
    {
        return Salepoint::find($this->salepoint_id);
    }

    public function operations($request_query = [], $data = [])
    {
        $rsp = TKTRequest::request(
            TKTRequest::GET,
            "/cashregisters/".$this->_id."/operations",
            $request_query,
            $data,
            [
                'factory' => 'Cashregisteroperation',
                'return_as_collection' => true
            ]
        );
        return (No2_HTTP::is_success($rsp->status) ? $rsp->data : null);
    }

    public function operations_by_pool($pool_id, $data = [])
    {
        $rsp = TKTRequest::request(
            TKTRequest::GET,
            "/cashregisters/".$this->_id."/pool/".$pool_id."/operations",
            [],
            $data,
            [
                'factory' => 'Cashregisteroperation',
                'return_as_collection' => true
            ]
        );
        return (No2_HTTP::is_success($rsp->status) ? $rsp->data : null);
    }

    public function has_id()
    {
        return is_string($this->_id) && (strlen($this->_id) > 0);
    }

    public function jsonSerialize() : mixed
    {
        $ret = [
           'name'         => $this->name(),
           'auto_closing' => $this->auto_closing(),
           'cash_balance' => $this->cash_balance(),
           'salepoint_id' => $this->salepoint_id()
        ];

        if ($this->has_id()) {
            $ret['_id'] = $this->_id();
        }

        return $ret;
    }
}
