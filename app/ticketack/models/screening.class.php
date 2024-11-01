<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\TKTModel;
use Ticketack\Core\Base\No2_HTTP;

/**
 * Ticketack Engine Screening.
 */

class Screening extends TKTModel implements \JsonSerializable
{
    // - some eventival URLs ends with '?'
    const POSTER_REGEXP = '/\.(jpe?g|png|gif|webp|avif)(\?)?$/i';

    // - Support Youtube and Vimeo videos
    //   see https://regexr.com/5sm04
    const TRAILER_REGEXP = '/(^(https:\/\/)((player\.|www\.)?vimeo\.com|youtu\.be|(www\.)?youtube\.com)\/([\w]+[\/]?)?([\?]?.*)?)|https:\/\/.+[.]mp4$/i';

    /**
     * @override
     */
    public static $resource = 'screenings';

    protected $_id         = null;
    protected $title       = null;
    protected $sections    = [];
    protected $description = null;
    protected $movies      = [];
    protected $note        = null;
    protected $start_at    = null;
    protected $stop_at     = null;
    protected $buckets     = [];
    protected $pricings    = [];
    protected $place       = null;
    protected $refs        = [];
    protected $opaque      = null;

    /**
     * @return true if given id is a valid screening id, false otherwise.
     */
    public static function is_valid_id($id)
    {
        return tkt_is_uuidv4($id);
    }

    /**
     * Get a screening id from one of his refs
     *
     * @param mixed $id_or_ref
     *
     * @return The screening id as uuidv4 if found, null otherwise
     */
    public static function id_from_ref($id_or_ref)
    {
        if (static::is_valid_id($id_or_ref)) {
            return $id_or_ref;
        }

        $screening = static::find($id_or_ref);

        return (null !== $screening) ? $screening->_id : null;
    }

    /**
     * cosmetic helper for scope_start_at_gt.
     */
    public static function scope_in_the_future($req)
    {
        return $req->start_at_gte(date_create("now"));
    }

    /**
     * scope filtering screenings on start_at values.
     */
    public static function scope_start_at_gte($req, $when)
    {
        return $req->query('start_at_gte', tkt_datetime_to_iso8601($when));
    }

    /**
     * scope filtering screenings on stop_at values.
     */
    public static function scope_stop_at_gte($req, $when)
    {
        return $req->query('stop_at_gte', tkt_datetime_to_iso8601($when));
    }

    /**
     * scope filtering screenings on stop_at values.
     */
    public static function scope_stop_at_lte($req, $when)
    {
        return $req->query('stop_at_lte', tkt_datetime_to_iso8601($when));
    }

    /**
     * scope filtering screenings movies sections
     */
    public static function scope_in_movie_sections($req, $sections)
    {
        if (!is_array($sections)) {
            $sections = [$sections];
        }

        return $req->query('films_sections_ids', implode(',', $sections));
    }

    /**
     * scope filtering screenings sections
     */
    public static function scope_in_screening_sections($req, $sections)
    {
        if (!is_array($sections)) {
            $sections = [$sections];
        }

        return $req->query('sections_ids', implode(',', $sections));
    }

    /**
     * scope filtering screenings on start_at values.
     */
    public static function scope_start_at_lte($req, $when)
    {
        return $req->query('start_at_lte', tkt_datetime_to_iso8601($when));
    }

    /**
     * scope filtering screenings on fims _ids
     */
    public static function scope_with_films($req, $films_ids)
    {
        return $req->query('films_ids', implode(',', $films_ids));
    }

    /**
     * scope sorting screenings by start_at values.
     */
    public static function scope_order_by_start_at($req)
    {
        return $req->add_post_process(function ($status, $screenings) {
            if (No2_HTTP::is_success($status)) {
                usort($screenings, function ($a, $b) {
                    return ($a->start_at->getTimestamp() - $b->start_at->getTimestamp());
                });
            }
            return $screenings;
        });
    }

    /**
     * scope filtering screenings that cannot be sold by a user having given
     * $roles.
     */
    public static function scope_for_sellers($req, $roles)
    {
        return $req->add_post_process(function ($status, $screenings) use ($roles) {
            if (No2_HTTP::is_success($status)) {
                $screenings = array_filter($screenings, function ($screening) use ($roles) {
                    return (count($screening->pricings_for_sellers($roles)) > 0);
                });
            }
            return $screenings;
        });
    }


    /**
     * scope filtering pricings that cannot be sold by a user having given
     * $roles.
     */
    public static function scope_filter_pricings_for_sellers($req, $roles)
    {
        return $req->add_post_process(function ($status, $screenings) use ($roles) {
            if (No2_HTTP::is_success($status)) {
                $screenings = array_map(function ($screening) use ($roles) {
                    $screening->pricings = $screening->pricings_for_sellers($roles);
                    return $screening;
                }, $screenings);
            }
            return $screenings;
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

    public function pricings_for_sellers($roles)
    {
        $filtered =  array_filter($this->pricings, function ($pricing) use ($roles) {
            return $pricing->can_be_sold_by($roles);
        });

        usort($filtered, function ($a, $b) {
            return Tickettype::opaque_eshop_sort_weight_cmp($a, $b);
        });

        return array_values($filtered);
    }

    /**
     * @override
     * XXX: if you change something here, double check jsonSerialize() and
     * update the unit test
     */
    public function __construct(array &$properties = [])
    {
        if (array_key_exists('start_at', $properties)) {
            $this->start_at = tkt_iso8601_to_datetime($properties['start_at']);
            unset($properties['start_at']);
        }
        if (array_key_exists('stop_at', $properties)) {
            $this->stop_at = tkt_iso8601_to_datetime($properties['stop_at']);
            unset($properties['stop_at']);
        }
        if (array_key_exists('films', $properties)) {
            $this->movies = array_map(function ($obj) {
                return new Movie($obj);
            }, $properties['films']);
            unset($properties['films']);
        }
        if (array_key_exists('cinema_hall', $properties)) {
            $this->place = new Place($properties['cinema_hall']);
            unset($properties['cinema_hall']);
        }
        if (array_key_exists('sections', $properties)) {
            $this->sections = array_map(function ($obj) {
                return new Section($obj);
            }, $properties['sections']);
            unset($properties['sections']);
        }
        if (array_key_exists('buckets', $properties)) {
            $this->buckets = array_map(function ($obj) {
                return new Bucket($this, $obj);
            }, $properties['buckets']);
            unset($properties['buckets']);
        }
        if (array_key_exists('pricings', $properties)) {
            $this->pricings = [];
            foreach ($properties['pricings'] as $key => $obj) {
                $pricing = (new Pricing($obj))->set_key($key);
                $this->pricings[$key] = $pricing;
            }
            unset($properties['pricings']);
        }
        if (array_key_exists('description', $properties)) {
            $this->description = (array)$properties['description'];
            unset($properties['description']);
        }
        if (array_key_exists('opaque', $properties)) {
            $this->opaque = (array)$properties['opaque'];
            unset($properties['opaque']);
        }
        parent::__construct($properties);
    }

    /**
     * Helper to access the opaque.posters attribute
     *
     * @return array: An array of objects with a "url" key.
     */
    public function posters()
    {
        if (!$this->has_opaque_key('posters')) {
            return [];
        }

        $posters = array_values(array_filter(
            $this->opaque['posters'],
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

    /**
     * Helper to access the first poster in opaque.posters
     *
     * @return object: The first poster as an object with a "url" key,
     *         null if the opaque .posters is not set or empty
     */
    public function first_poster()
    {
        $posters = $this->posters();
        return !empty($posters) ? $posters[0] : null;
    }

    /**
     * Helper to access the opaque.trailers attribute
     *
     * @return array: An array of objects with a "url" key.
     */
    public function trailers()
    {
        if (!$this->has_opaque_key('trailers')) {
            return [];
        }

        $trailers = array_values(array_filter(
            $this->opaque['trailers'],
            function ($trailer) {
                if (is_array($trailer) && array_key_exists('url', $trailer)) {
                    return preg_match(static::TRAILER_REGEXP, $trailer['url']);
                }
                return false;
            }
        ));

        return array_map(function ($trailer) {
            return (object)$trailer;
        }, $trailers);
    }

    /**
     * Helper to access the first trailer in opaque.trailers
     *
     * @return object: The first trailer as an object with a "url" key,
     *         null if the opaque.trailers is not set or empty
     */
    public function first_trailer()
    {
        $trailers = $this->trailers();
        return !empty($trailers) ? $trailers[0] : null;
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

    public function description($lang = null)
    {
        if (is_null($lang)) {
            return $this->description;
        }

        return isset($this->description[$lang]) ? $this->description[$lang] : null;
    }

    public function movies()
    {
        return $this->movies;
    }

    public function note()
    {
        return $this->note;
    }

    public function start_at()
    {
        return $this->start_at;
    }

    public function stop_at()
    {
        return $this->stop_at;
    }

    public function buckets()
    {
        return $this->buckets;
    }

    public function bucket($bucket_id)
    {
        foreach ($this->buckets() as $bucket) {
            if ($bucket->_id() === $bucket_id)
                return $bucket;
        }
        return null;
    }

    /*
     * @return an array of bucket matching the given context.
     *
     * @param $ignore
     *   a bitmask of ignored match failure, eg. (MATCH_UNAUTHORIZED | MATCH_TOO_EARLY)
     */
    protected function matching_buckets($now, $tickettype, $user, $ignore)
    {
        $ignore_mask = ~$ignore;
        $matching = [];

        foreach ($this->buckets() as $bucket) {
            $match = $bucket->match($now, $tickettype, $user);
            if (($match & $ignore_mask) === Bucket::MATCH_SUCCESS) {
                $matching[] = $bucket;
            }
        }

        return $matching;
    }

    /*
     * @return an array of bookable (i.e. matching or stealable) bucket in the
     * given context
     *
     * @see Bucket::matching_buckets()
     */
    public function bookable_buckets($now, $tickettype, $user, $ignore)
    {
        // associative array of bucket_id => Bucket, so we don't have duplicates.
        $bookable = [];

        foreach ($this->matching_buckets($now, $tickettype, $user, $ignore) as $bucket) {
            $bookable[$bucket->_id()] = $bucket;
            foreach ($bucket->may_steal_from() as $victim) {
                $bookable[$victim->_id()] = $victim;
            }
        }

        return array_values($bookable);
    }

    public function pricings()
    {
        return $this->pricings;
    }

    public function place()
    {
        return $this->place;
    }

    public function refs()
    {
        return $this->refs;
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

    public function has_sections()
    {
        return is_array($this->sections);
    }

    public function has_description()
    {
        return is_array($this->description);
    }

    public function has_note()
    {
        return is_string($this->note) && (strlen($this->note) > 0);
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
            '_id'         => $this->_id(),
            'title'       => $this->title,
            'films'       => array_map(function ($movie) {
                                 return $movie->jsonSerialize();
                             }, $this->movies()),
            'start_at'    => tkt_datetime_to_iso8601($this->start_at),
            'stop_at'     => tkt_datetime_to_iso8601($this->stop_at),
            'buckets'     => array_map(function ($bucket) {
                                 return $bucket->jsonSerialize();
                             }, $this->buckets()),
            'pricings'    => array_map(function ($pricing) {
                                 return $pricing->jsonSerialize();
                             }, $this->pricings()),
            'cinema_hall' => !empty($this->place()) ? $this->place()->jsonSerialize() : null,
            'refs'        => $this->refs(),
        ];

        if ($this->has_sections()) {
            $ret['sections'] = $this->sections();
        }
        if ($this->has_description()) {
            $ret['description'] = $this->description;
        }
        if ($this->has_note()) {
            $ret['note'] = $this->note();
        }
        if ($this->has_opaque()) {
            $ret['opaque'] = $this->opaque();
        }

        return $ret;
    }

    /*
     * TODO: this method is left for retro-compatibility: bookings statistic
     * are aggregated from all the buckets. Callers of this method should
     * either displayor use every bucket individually or filter the relevant
     * one(s), but using all of them (like this method does) is probably the
     * wrong way most of the time.
     */
    public static function __seats_retro_compat($screening_obj)
    {
        return (object)[
            'total' => array_reduce($screening_obj->buckets, function ($memo, $bucket) {
                return $memo + $bucket->total_capacity;
            }, 0),
            'available' => array_reduce($screening_obj->buckets, function ($memo, $bucket) {
                return $memo + $bucket->available;
            }, 0),
            'unconfirmed' => array_reduce($screening_obj->buckets, function ($memo, $bucket) {
                return $memo + $bucket->unconfirmed;
            }, 0),
            'confirmed' => array_reduce($screening_obj->buckets, function ($memo, $bucket) {
                return $memo + $bucket->confirmed;
            }, 0),
            'used' => array_reduce($screening_obj->buckets, function ($memo, $bucket) {
                return $memo + $bucket->scanned;
            }, 0),
        ];
    }
}

