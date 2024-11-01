<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\No2_HTTP;
use Ticketack\Core\Base\TKTRequest;
use Ticketack\Core\Base\TKTException;

/**
 * Ticketack Engine helper for Screenings buckets.
 *
 * @notes
 *  Instances are *immutable*.
 */
class Bucket implements \JsonSerializable
{
    /* constants for match() return values */
    const MATCH_SUCCESS          = 0x00;
    const MATCH_BUCKET_IS_STASH  = 0x01;
    const MATCH_TOO_LATE         = 0x02;
    const MATCH_TOO_EARLY        = 0x04;
    const MATCH_UNAUTHORIZED     = 0x08;
    const MATCH_WRONG_TICKETTYPE = 0x10;

    // a weird place to define this constant but meh
    const EPOCH_TIMESTAMP = 0;

    protected $_id            = null;
    protected $total_capacity = 0;
    protected $available      = 0;
    protected $unconfirmed    = 0;
    protected $confirmed      = 0;
    protected $scanned        = 0;
    protected $may_steal_from = [];
    protected $rules          = [];

    protected $screening = null;

    /*
     * @return the sum of available, unconfirmed, confirmed and scanned
     * properties from the given buckets.
     */
    public static function sum($buckets)
    {
        $sum = [
            'available'      => 0,
            'unconfirmed'    => 0,
            'confirmed'      => 0,
            'scanned'        => 0,
            'total_capacity' => 0,
        ];
        foreach ($buckets as $bucket) {
            $sum['available']      += $bucket->available;
            $sum['unconfirmed']    += $bucket->unconfirmed;
            $sum['confirmed']      += $bucket->confirmed;
            $sum['scanned']        += $bucket->scanned;
            $sum['total_capacity'] += $bucket->total_capacity;
        }

        return $sum;
    }

    /**
     * @throw Exception
     */
    public function __construct($screening, array &$properties = [])
    {
        $this->screening = $screening;
        $this->_id       = $properties['_id'];

        foreach (['total_capacity', 'available', 'unconfirmed', 'confirmed', 'scanned'] as $prop) {
            $val = $properties[$prop];
            if ($val < 0) {
                throw new \InvalidArgumentException("$val: invalid $prop value");
            }
            $this->$prop = $val;
        }

        if (array_key_exists('may_steal_from', $properties)) {
            $this->may_steal_from = $properties['may_steal_from'];
        }

        if (array_key_exists('rules', $properties)) {
            $this->rules = $properties['rules'];
            if (array_key_exists('not_before', $this->rules)) {
                $this->rules['not_before'] = tkt_iso8601_to_datetime($this->rules['not_before']);
            }
            if (array_key_exists('not_after', $this->rules)) {
                $this->rules['not_after'] = tkt_iso8601_to_datetime($this->rules['not_after']);
            }
        }
    }

    public function _id()
    {
        return $this->_id;
    }

    /*
     * @return an array of the stealable bucket(s)
     */
    public function may_steal_from()
    {
        $screening = $this->screening;
        $victims = array_map(function ($bucket_id) use ($screening) {
            return $screening->bucket($bucket_id);
        }, $this->may_steal_from);

        return $victims;
    }

    /*
     * @return a bitmask of matching errors.
     *
     * NOTE: the returned value *must* be compared using the MATCH constants
     * because MATCH_SUCCESS is zero:
     *   if ($bucket->match(...)) {
     *     // this codepath is executed when $bucket has *not* matched
     *   }
     */
    public function match($now = null, $tickettype = null, $user = null)
    {
        $result = static::MATCH_SUCCESS;
        $now    = ($now  ?: new \DateTime());
        $user   = ($user ?: current_user());

        if (array_key_exists('not_after', $this->rules)) {
            if ($this->rules['not_after']->getTimestamp() === static::EPOCH_TIMESTAMP) {
                $result |= static::MATCH_BUCKET_IS_STASH;
            }
            if ($now > $this->rules['not_after']) {
                $result |= static::MATCH_TOO_LATE;
            }
        }
        if (array_key_exists('not_before', $this->rules) &&
                $now < $this->rules['not_before']) {
            $result |= static::MATCH_TOO_EARLY;
        }
        if (array_key_exists('only_for_tickettypes', $this->rules)) {
            $only = $this->rules['only_for_tickettypes'];
            if (!in_array($tickettype, $only)) {
                $result |= static::MATCH_WRONG_TICKETTYPE;
            }
        }
        if (array_key_exists('only_for_roles', $this->rules)) {
            $only = $this->rules['only_for_roles'];
            if (!$user->has_role_in_array($only)) {
                $result |= static::MATCH_UNAUTHORIZED;
            }
        }

        return $result;
    }

    public function inc_total_capacity_by($n)
    {
        if ($n < 0)
            throw new \InvalidArgumentException("$n: can not be negative");
        $total_capacity = $this->total_capacity;
        $new_capacity   = intval($total_capacity + $n);
        $rsp = TKTRequest::request(
            /* method */TKTRequest::PATCH,
            /* path */sprintf('/screenings/%s/buckets/%s', $this->screening->_id(), $this->_id()),
            /* query */[],
            /* data */sane_json_encode(['total_capacity' => $new_capacity])
        );
        switch ($rsp->status) {
            case No2_HTTP::OK:
                $this->total_capacity = $new_capacity;
                break;
            case No2_HTTP::NOT_FOUND:
                throw new BucketUpdateException("Screening(_id=%s) or Bucket(_id=%s) not found", $this->screening->_id(), $this->_id());
            case No2_HTTP::BAD_REQUEST: // FALLTHROUGH
            case No2_HTTP::CONFLICT: // FALLTHROUGH
                throw new BucketUpdateException("Failed to set Bucket(_id=%s) total_capacity to %d", $this->_id(), $new_capacity);
            default: // unknow status
                throw new TKTException(sprintf("%d: Unknown status for booking request", $rsp->status));
        }
    }

    public function jsonSerialize() : mixed
    {
        $ret = [
            '_id'            => $this->_id,
            'total_capacity' => $this->total_capacity,
            'available'      => $this->available,
            'unconfirmed'    => $this->unconfirmed,
            'confirmed'      => $this->confirmed,
            'scanned'        => $this->scanned,
            'may_steal_from' => $this->may_steal_from,
            'rules'          => $this->rules,
        ];

        if (array_key_exists('not_before', $ret['rules'])) {
            $ret['rules']['not_before'] = tkt_datetime_to_iso8601($ret['rules']['not_before']);
        }
        if (array_key_exists('not_after', $ret['rules'])) {
            $ret['rules']['not_after'] = tkt_datetime_to_iso8601($ret['rules']['not_after']);
        }

        return $ret;
    }
}


class BucketException extends \Exception { }
class BucketNotFoundException extends \Exception { }
class BucketMatchException extends \Exception {
    // see https://secure.php.net/manual/en/language.exceptions.extending.php
    public function __construct($match_result = null, $code = 0, \Exception $previous = null)
    {
        $message = null;
        if ($match_result) {
            $errors = [];
            if ($match_result & Bucket::MATCH_TOO_EARLY)
                $errors[] = "TOO_EARLY";
            if ($match_result & MATCH_TOO_LATE)
                $errors[] = "TOO_LATE";
            if ($match_result & MATCH_UNAUTHORIZED)
                $errors[] = "UNAUTHORIZED";
            if ($match_result & MATCH_WRONG_TICKETTYPE)
                $errors[] = "WRONG_TICKETTYPE";
            $message = join(", ", $errors);
        }
        parent::__construct($message, $code, $previous);
    }
}
class BucketUpdateException extends \Exception { }

