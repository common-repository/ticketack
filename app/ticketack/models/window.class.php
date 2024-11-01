<?php
namespace Ticketack\Core\Models;

/**
 * Ticketack Engine helper for windows (used in Tickettypes and Tickets).
 *
 * @notes
 *  Instances are *immutable*.
 */
class Window implements \JsonSerializable
{
    const STATIC_TIME_FRAME_WINDOW  = 'STATIC_TIME_FRAME_WINDOW';
    const DYNAMIC_TIME_FRAME_WINDOW = 'DYNAMIC_TIME_FRAME_WINDOW';
    const SCREENING_WINDOW          = 'SCREENING_WINDOW';

    /**
     * compare two Window object by their start_at / stop_at timestamps.
     */
    public static function timestamp_cmp($a, $b)
    {
        if (is_null($a)) {
            return -1;
        } elseif (is_null($b)) {
            return 1;
        }
        $start_at_diff = ($a->start_at()->getTimestamp() - $b->start_at()->getTimestamp());
        $stop_at_diff  = ($a->stop_at()->getTimestamp()  - $b->stop_at()->getTimestamp());
        return ($start_at_diff ? $start_at_diff : $stop_at_diff);
    }

    /* one of the constant type */
    protected $type = null;

    /* for static time frame window, DateTime */
    protected $start_at = null;
    protected $stop_at  = null;

    /*
     * for dynamic time frame window, ISO8601 duration as a string
     *
     * NOTE: we don't use a DateInterval here because DateInterval cloning is
     * buggy since 2k9, see https://bugs.php.net/bug.php?id=50559
     */
    protected $iso8601_duration = null;

    /* screening tied window */
    protected $screening_id = null;

    /* in every window type */
    protected $nbookings = 0;

    /**
     * @throw Exception
     */
    public function __construct(array &$properties = [])
    {
        if (array_key_exists('screening_id', $properties) &&
                Screening::is_valid_id($properties['screening_id'])) {
            $this->type         = Window::SCREENING_WINDOW;
            $this->screening_id = $properties['screening_id'];
        } elseif (array_key_exists('start_at', $properties) &&
                array_key_exists('stop_at', $properties)) {
            $this->type     = Window::STATIC_TIME_FRAME_WINDOW;
            $this->start_at = tkt_iso8601_to_datetime($properties['start_at']);
            $this->stop_at  = tkt_iso8601_to_datetime($properties['stop_at']);
        } elseif (array_key_exists('duration', $properties)) {
            $this->type             = Window::DYNAMIC_TIME_FRAME_WINDOW;
            $this->iso8601_duration = $properties['duration'];
        } else {
            throw new \InvalidArgumentException('invalid window properties');
        }
        $this->nbookings = intval($properties['nbookings']);
    }

    /* getters */

    public function type()
    {
        return $this->type;
    }

    public function start_at()
    {
        switch ($this->type()) {
            case Window::STATIC_TIME_FRAME_WINDOW:
                $start_at = clone $this->start_at;
                break;
            case Window::DYNAMIC_TIME_FRAME_WINDOW:
                $start_at = new \DateTime();
                break;
            case Window::SCREENING_WINDOW:
                $start_at = $this->screening()->start_at();
                break;
        }

        return $start_at;
    }

    public function stop_at()
    {
        switch ($this->type()) {
            case Window::STATIC_TIME_FRAME_WINDOW:
                $stop_at = $this->stop_at;
                break;
            case Window::DYNAMIC_TIME_FRAME_WINDOW:
                $stop_at = $this->start_at()->add($this->duration());
                break;
            case Window::SCREENING_WINDOW:
                $stop_at = $this->screening()->stop_at();
                break;
        }

        return $stop_at;
    }

    public function duration()
    {
        switch ($this->type()) {
            case Window::DYNAMIC_TIME_FRAME_WINDOW:
                $duration = new \DateInterval($this->iso8601_duration);
                break;
            case Window::SCREENING_WINDOW: /* FALLTHROUGH */
            case Window::STATIC_TIME_FRAME_WINDOW:
                $duration = $this->start_at()->diff($this->stop_at());
                break;
        }

        return $duration;
    }

    /**
     * Return the screening id iff the window type is Window::SCREENING_WINDOW.
     * Otherwise, null is returned.
     */
    public function screening_id()
    {
        return $this->screening_id;
    }

    public function nbookings()
    {
        return $this->nbookings;
    }

    /**
     * Return the window screening iff the window type is Window::SCREENING_WINDOW.
     * Otherwise, null is returned.
     */
    public function screening()
    {
        if (!isset($this->_screening)) {
            $this->_screening = Screening::find($this->screening_id);
        }
        return $this->_screening;
    }

    public function jsonSerialize() : mixed
    {
        $ret = [
            'nbookings' => $this->nbookings(),
        ];

        switch ($this->type()) {
            case Window::STATIC_TIME_FRAME_WINDOW:
                $ret['start_at'] = tkt_datetime_to_iso8601($this->start_at);
                $ret['stop_at']  = tkt_datetime_to_iso8601($this->stop_at);
                break;
            case Window::DYNAMIC_TIME_FRAME_WINDOW:
                $ret['duration'] = $this->iso8601_duration;
                break;
            case Window::SCREENING_WINDOW:
                $ret['screening_id'] = $this->screening_id();
                break;
        }

        return $ret;
    }
}
