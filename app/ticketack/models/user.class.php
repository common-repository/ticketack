<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\No2_HTTP;
use Ticketack\Core\Base\TKTModel;
use Ticketack\Core\Base\TKTRequest;
use Ticketack\Core\Base\TKTApiException;
use Ticketack\WP\TKTApp;

/**
 * Ticketack Engine User.
 */

class User extends TKTModel implements \JsonSerializable
{
    /**
     * @override
     */
    public static $resource = 'users';

    protected $_id        = null;
    protected $name       = null;
    protected $roles      = null;
    protected $salepoints = [];
    protected $opaque     = null;

    /**
     * @var User
     */
    protected static $current = null;

    public static function get_current()
    {
        if (is_null(static::$current)) {
            $api_key = TKTApp::get_instance()->get_config('ticketack.api_key');

            if (empty($api_key)) {
                return null;
            }

            $rsp = TKTRequest::request(TKTRequest::GET, sprintf('/authentication/%s', $api_key));

            if ($rsp->status !== No2_HTTP::OK) {
                tkt_flash_notice(sprintf("%d: Authentification failed, impossible to load current user", $rsp->status), 'error');
                return null;
            }

            static::$current = new static($rsp->data);
        }

        return static::$current;
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

    public function name()
    {
        return $this->name;
    }

    public function roles()
    {
        return $this->roles;
    }

    public function salepoints()
    {
        return $this->salepoints;
    }

    /**
     * Access opaque fields
     *
     * @param string $field: Field name, optional
     * @param mixed $default: Default value if $field is
     *                        provided but not found
     *
     * @return mixed: All the opaque fields if $field is not provided,
     *                opaque $field value otherwise.
     */
    public function opaque($field = null, $default = null)
    {
        if (!is_null($field)) {
            return isset($this->opaque[$field]) ? $this->opaque[$field] : $default;
        }

        return $this->opaque;
    }

    public function has_opaque()
    {
        return is_array($this->opaque);
    }

    public function has_opaque_key($key)
    {
        return $this->has_opaque() && array_key_exists($key, $this->opaque);
    }

    /**
     * Handle properties JSONification, like DateTime to ISO8601.
     */
    public function jsonSerialize() : mixed
    {
        $ret = [
            '_id'        => $this->_id(),
            'name'       => $this->name(),
            'roles'      => $this->roles(),
            'salepoints' => $this->salepoints(),
        ];

        if ($this->has_opaque()) {
            $ret['opaque'] = $this->opaque();
        }

        return $ret;
    }
}

