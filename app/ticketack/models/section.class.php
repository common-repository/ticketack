<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\TKTModel;
use Ticketack\WP\TKTApp;

/**
 * Ticketack Engine Section as found under the 'sections' name in a Screening.
 */
class Section implements \JsonSerializable
{
    protected $_id  = null;
    protected $name = null;

    public function __construct(array $properties = [])
    {
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }

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

    public function jsonSerialize() : mixed
    {
        return [
            '_id'  => $this->_id(),
            'name' => $this->name()
        ];
    }
}
