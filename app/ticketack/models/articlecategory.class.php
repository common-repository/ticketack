<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\TKTModel;

/**
 * Ticketack Engine Article category
 */
class Articlecategory extends TKTModel implements \JsonSerializable
{
    // - some eventival URLs ends with '?'
    const POSTER_REGEXP = '/\.(jpe?g|png|gif|webp|avif)(\?)?$/i';

    public static $resource = 'articlecategories';

    protected $_id         = null;
    protected $name        = null;
    protected $description = null;
    protected $posters     = null;
    protected $parent      = null;

    public static function scope_root($req)
    {
        return $req->query('root', true);
    }

    public static function scope_in_pos($req, $pos_id)
    {
        return $req->query('pos_ids', $pos_id);
    }

    public static function scope_child_of($req, $parent)
    {
        return $req->query('parent', $parent);
    }

    public static function scope_ids($req, $articlecategories_ids)
    {
        return $req->query(
            'articlecategories_ids',
            implode(',', $articlecategories_ids)
        );
    }

    public function __construct(array &$properties = [])
    {
        parent::__construct($properties);
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

    /**
     * Helper to access the opaque.posters attribute
     *
     * @return array: An array of objects with a "url" key.
     */
    public function posters()
    {
        if (empty($this->posters)) {
            return [];
        }

        $posters = array_values(array_filter(
            $this->posters,
            function ($poster) {
                if (is_array($poster) && array_key_exists('url', $poster)) {
                    return preg_match(static::POSTER_REGEXP, $poster['url']);
                }
                return false;
            }
        ));

        return array_map(function ($poster) {
            return (object)$poster;
        }, $posters);
    }

    public function parent()
    {
        return $this->parent;
    }

    public function has_id()
    {
        return is_string($this->_id) && (strlen($this->_id) > 0);
    }

    public function jsonSerialize() : mixed
    {
        $ret = [
            'name'        => $this->name,
            'description' => $this->description,
            'posters'     => $this->posters(),
            'parent'      => $this->parent
        ];

        if ($this->has_id()) {
            $ret['_id'] = $this->_id();
        }

        return $ret;
    }
}
