<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\TKTModel;

/**
 * Event (movie, artist, ...) representation
 */
class Event extends TKTModel implements \JsonSerializable
{
    public static $resource = 'movies';

    protected $_id        = null;
    protected $title      = null;
    protected $screenings = [];
    protected $sections   = [];
    protected $created_at = null;
    protected $updated_at = null;
    protected $opaque     = null;

    /**
     * Get an array of Event from an array of screenings
     *
     * @param Screening[] $screenings
     * @param mixed $event_id: Filter on a specific event (optional)
     *
     * @return Event[]: an array of Event
     */
    public static function from_screenings($screenings, $event_id = null)
    {
        $events = [];

        if (!is_array($screenings)) {
            return $events;
        }

        array_map(function ($s) use (&$events, $event_id) {

            array_map(function ($m) use (&$events, $s, $event_id) {

                if (!is_null($event_id) && $m->_id() != $event_id) {
                    return;
                }

                if (isset($events[$m->_id()])) {
                    $event = $events[$m->_id()];
                } else {
                    $event = static::from_movie($m);
                    $event->screenings = [];
                }

                array_push($event->screenings, $s);

                $events[$m->_id()] = $event;

            }, $s->movies());

        }, $screenings);

        return $events;
    }

    /**
     * Create an Event from a Movie
     *
     * @param Movie $movie
     *
     * @return Event
     */
    public static function from_movie(Movie $movie)
    {
        $properties = [
            "_id"        => $movie->_id(),
            "title"      => $movie->title(),
            "sections"   => $movie->sections(),
            "created_at" => $movie->created_at(),
            "updated_at" => $movie->updated_at(),
            "opaque"     => $movie->opaque(),
            "posters"    => $movie->posters(),
            "trailers"   => $movie->trailers()
        ];

        return new static($properties);
    }

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
                return is_array($obj) ? new Section($obj) : $obj;
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

    public function localized_description($lang)
    {
        $description = $this->opaque('description');
        if (empty($description) || !isset($description[$lang])) {
            return null;
        }

        return $description[$lang];
    }

    public function screenings()
    {
        return $this->screenings;
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
            $posters = array_values(array_filter($this->opaque['posters'], function ($poster) {
                if (is_array($poster) && array_key_exists('url', $poster)) {
                    return preg_match(Screening::POSTER_REGEXP, $poster['url']);
                }
                return false;
            }));
        }

        // Fallback on screenings posters
        if (empty($posters)) {
            foreach ($this->screenings as $s) {
                $posters = array_merge($posters, $s->posters());
            }
        }

        return array_map(function ($poster) {
            return (object)$poster;
        }, $posters);
    }

    /**
     * Helper to access the first poster in opaque.posters
     *
     * @return object: The first poster as an object with a "url" key,
     *         null if the opaque.posters is not set or empty
     */
    public function first_poster()
    {
        $posters = $this->posters();
        return !empty($posters) ? $posters[0] : null;
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

        // Fallback on screenings trailers
        if (empty($trailers)) {
            foreach ($this->screenings as $s) {
                $trailers = array_merge($trailers, $s->trailers());
            }
        }

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

    public function start_at()
    {
        $start_at = null;

        if (empty($this->screenings)) {
            return $start_at;
        }

        foreach ($this->screenings as $s) {
            if (is_null($start_at) || $s->start_at() < $start_at) {
                $start_at = $s->start_at();
            }
        }

        return $start_at;
    }

    public function stop_at()
    {
        $stop_at = null;

        if (empty($this->screenings)) {
            return $stop_at;
        }

        foreach ($this->screenings as $s) {
            if (is_null($stop_at) || $s->stop_at() > $stop_at) {
                $stop_at = $s->stop_at();
            }
        }

        return $stop_at;
    }

    public function jsonSerialize() : mixed
    {
        $ret = [
            '_id'        => $this->_id(),
            'title'      => $this->title,
            'screenings' => $this->screenings
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
