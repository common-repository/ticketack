<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\TKTModel;

/**
 * Ticketack Engine Movie, found as a 'film' in a Screening.
 */
class Movie extends TKTModel implements \JsonSerializable
{
    public static $resource = 'movies';

    protected $_id        = null;
    protected $title      = null;
    protected $sections   = [];
    protected $created_at = null;
    protected $updated_at = null;
    protected $opaque     = null;

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
        if (array_key_exists('sections', $properties)) {
            $this->sections = array_map(function ($obj) {
                return new Section($obj);
            }, $properties['sections']);
            unset($properties['sections']);
        }
        parent::__construct($properties);
    }

    public function _id()
    {
        return $this->_id;
    }

    public function title($lang = null)
    {
        if (is_null($lang)) {
            return $this->title;
        }

        return isset($this->title[$lang]) ? $this->title[$lang] : null;
    }

    public function original_title()
    {
        return tkt_original($this->title);
    }

    public function localized_title_or_original($lang)
    {
        return tkt_localized_or_original($this->title, $lang);
    }

    public function localized_title_or_default_or_original($lang)
    {
        return tkt_localized_or_default_or_original($this->title, $lang);
    }

    public function original_title_if_different_from_localized($lang)
    {
        return tkt_original_if_different_from_localized($this->title, $lang);
    }

    public function sections()
    {
        return $this->sections;
    }

    public function created_at()
    {
        return $this->created_at;
    }

    public function updated_at()
    {
        return $this->updated_at;
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

    public function posters()
    {
        $posters = [];
        if (is_array($this->opaque) && isset($this->opaque['posters'])) {
            $posters = array_filter($this->opaque['posters'], function ($poster) {
                if (is_array($poster) && array_key_exists('url', $poster)) {
                    return preg_match(Screening::POSTER_REGEXP, $poster['url']);
                }
                return false;
            });
        }
        return $posters;
    }

    public function trailers()
    {
        $trailers = [];
        if (is_array($this->opaque) && isset($this->opaque['trailers'])) {
            $trailers = array_filter($this->opaque['trailers'], function ($trailer) {
                if (is_array($trailer) && array_key_exists('url', $trailer)) {
                    return preg_match(Screening::TRAILER_REGEXP, $trailer['url']);
                }
                return false;
            });
        }
        return $trailers;
    }

    public function has_sections()
    {
        return is_array($this->sections);
    }

    public function has_created_at()
    {
        return $this->created_at() instanceof Datetime;
    }

    public function has_updated_at()
    {
        return $this->updated_at() instanceof Datetime;
    }

    public function has_opaque()
    {
        return is_array($this->opaque);
    }

    public function jsonSerialize() : mixed
    {
        $ret = [
            '_id'     => $this->_id(),
            'title'   => $this->title,
        ];

        if ($this->has_sections()) {
            $ret['sections'] = $this->sections();
        }
        if ($this->has_created_at()) {
            $ret['created_at'] = tkt_datetime_to_iso8601($this->created_at());
        }
        if ($this->has_updated_at()) {
            $ret['updated_at'] = tkt_datetime_to_iso8601($this->updated_at());
        }
        if ($this->has_opaque()) {
            $ret['opaque'] = $this->opaque();
        }

        return $ret;
    }
}
