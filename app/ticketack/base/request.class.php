<?php
namespace Ticketack\Core\Base;

/**
 * This class goal is to make Ticketack Engine requests in PHP less painful by
 * providing a DSL with chaining methods of objects.
 *
 * It tries to mimic No2_SQLQuery's interface methods but using the Ticketack
 * Engine REST API instead of an SQL database.
 *
 * @note
 *   - Most method return a modified clone <code>$this</code>
 *   - the current implementation is a wrapper around TKTAPI and doesn't
 *     handle multiple profiles / engines for now.
 */

class TKTRequest
{
    /*
     * Theses constant are used as "hint" for a GET request. If
     * EXPECT_MANY is given the the returned value is always an array.
     * The values are choosen so bitwise operation are possible.
     */
    const SURPRISE    = -1; /**< don't make any expectations       */
    const EXPECT_ZERO =  1; /**< expect no result (strange choice) */
    const EXPECT_ONE  =  2; /**< expect exactly one result         */
    const EXPECT_MANY =  6; /**< expect many results, a collection (can be 0 or 1) */

    const GET   = 'GET';     /**< HTTP GET request */
    const PATCH = 'PATCH';   /**< HTTP PATCH request */
    const PUT   = 'PUT';     /**< HTTP PUT request */
    const POST  = 'POST';    /**< HTTP POST request */
    const DELETE = 'DELETE'; /**< HTTP DELETE request */

    const FIELDS_QUERY_PARAM = 'fields'; /**< query parameter */

    /**
     * Send a request.
     *
     * @param $method
     *   The HTTP method to use, one of GET, PATCH, PUT, POST or DELETE.
     *
     * @param $path
     *   The request path.
     *
     * @param $query
     *   The request query.
     *
     * @param $data (optional)
     *   The data to send, only for PATCH, PUT, and POST request methods.
     *
     * @param $options (optional)
     *   An array of options. The following keys are valid:
     *   - factory:
     *      a class used to create instances from requests with results, should
     *      have a load() static method.
     *   - return_as_collection:
     *      if true an array with the result will always be returned, even if
     *      there is no or one element(s). If not set or set to false the
     *      return values will be decided as follow:
     *      - when there is no result, null is returned.
     *      - when there is only one result, the object is returned.
     *      - when there is many results, an array of objects is returned.
     *
     * @return
     *   A stdClass instance with data (associative array(s) or instance(s) of
     *   the given factory class) and status (int) from the request.
     */
    public static function request($method, $path, $query = [], $data = [], $options = [])
    {
        // add a leading slash to $path if needed
        $path = '/api' . (preg_match('#^/#', $path) ? $path : '/' . $path);

       // nocache query parameter should always appear in first position, otherwise the nginx cache could compute one URL with trailing & and one without it
       $add_no_cache = array_key_exists('nocache', $options) && $options['nocache'] === 'true';

        // merge $path quand $query to build a request
        $path .= count($query) > 0 ?
            '?' . ($add_no_cache ? 'nocache=true&' : '') . http_build_query($query) :
            ($add_no_cache ? '?nocache=true' : '');

        // call the API
        $response = TKTApi::get_instance()->request($method, $path, $data);

        // some response won't have a body, like 404 Not Found.
        $body = $response->getBody();
        if (strlen($body) > 0) {
            $result = json_decode($body, /*assoc=*/true);

            // XXX: workaround the buggy php json_decode() which allow scalar
            // values to be parsed.
            if (!is_array($result)) {
                tkt_flash_notice(tkt_t("Impossible de communiquer avec la billetterie…"), 'error');
                return null;
            }
        }

        $status = $response->getStatusCode();

        // shortcut on error
        if (!No2_HTTP::is_success($status)) {
            //No2_Logger::info("request(method=$method, req=$req) unsuccessful, got status=$status");
            return (object)['status' => $status, 'data' => $result];
        }

        /*
         * try to guess if the JSON response was an object (as opposed to an
         * array). Since we asked json_decode() to give us arrays we can't
         * tell the difference from $result, so we fallback to regexp-check
         * $body.
         */
        if (is_array($result) && !preg_match('/\A\s*\[/m', $body)) {
            $result = [$result]; // "force" $result to be an array of arrays
        }

        $objects = &$result;
        if (array_key_exists('factory', $options) && class_exists($options['factory'])) {
            $klass = $options['factory'];
            $objects = array_map(function ($array) use ($klass) {
                return $klass::load($array);
            }, $result);
        }

        if (array_key_exists('return_as_collection', $options) &&
                $options['return_as_collection']) {
            $results = $objects;
        } else {
            switch (count($objects)) {
                case 0:
                    $results = null;
                    break;
                case 1:
                    $results = $objects[0];
                    break;
                default:
                    $results = $objects;
                    break;
            }
        }

        $rsp = (object)['status' => $status, 'data' => $results];
        return $rsp;
    }


    /**
     * a TKTRequest from which this was formed.
     */
    protected $parent = null;

    /**
     * The number of parents, used to generate uniq tags.
     */
    protected $height = 0;

    /**
     * called to create a clone of this and setting the parent and height
     * properties properly.
     *
     * @return
     *   A new clone of this object.
     */
    protected function specialize()
    {
        $klone = clone $this;
        $klone->parent = $this;
        $klone->height = $this->height + 1;

        return $klone;
    }

    /**
     * Class used to create new instances when the request return result(s).
     * It is also used to find the query path, so it should have public
     * static $path property. It should be a subclass of
     * TKTBaseModel.
     */
    public $klass;

    /**
     * what to expect as result from a request, see EXPECT_* and SURPRISE
     * constants.
     */
    protected $hint;

    /**
     * request path.
     */
    protected $request_path = null;

    /**
     * request query
     */
    protected $request_query = [];

    /**
     * request data
     */
    protected $request_data = null;

    /**
     * array of function called after the execution of the request
     */
    protected $post_process = [];

    /**
     * @param $klass
     *   The model class that will be used to get the query path and, if
     *   the request yield a result, used to create instances.
     *
     * @param $hint
     *   a hint of how many results to expect. If EXPECT_MANY is given, the
     *   result is returned as an array regardless of the count.
     */
    public function __construct($klass, $hint = self::SURPRISE)
    {
        $this->klass        = $klass;
        $this->request_path = $klass::$resource;
        $this->hint         = intval($hint);
    }

    /**
     * this method allow model classes to define scope method, prefixed by
     * `scope_'.
     *
     * The model class scope_* method should take one argument that is "this",
     * a TKTRequest instance. It will modify it and should return
     * the result.
     */
    public function __call($name, $arguments)
    {
        $method_name = 'scope_' . $name;
        try {
            $reflect = new \ReflectionMethod($this->klass, $method_name);
            if ($reflect->isPublic() && $reflect->isStatic()) {
                array_unshift($arguments, null /* static method */, $this);
                return call_user_func_array([$reflect, 'invoke'], $arguments);
            }
        } catch (\Exception $e) {
            // ignore because the codepath after the try block trigger_error anyway
        }
        trigger_error('Call to undefined method ' .
            esc_html(get_class($this)) . '::' . esc_html($name) . '()', E_USER_ERROR);
    }

    /**
     * set the request's path.
     *
     * @param $path
     *   The new path to use.
     */
    public function path($path)
    {
        $neo = $this->specialize();
        $neo->request_path = $path;
        return $neo;
    }

    /**
     * set a query value.
     *
     * @param $key
     *   The query key.
     *
     * @param $value
     *   The query value.
     */
    public function query($key, $value)
    {
        $neo = $this->specialize();
        $neo->request_query[$key] = $value;
        return $neo;
    }

    /**
     * Inherited from the LIMIT SQL clause, set the query's limit and offset
     * params, unused at the moment.
     *
     * @param $off
     *   The offset
     *
     * @param $count
     *   The number of results to return
     *
     * @return
     *   a modified clone of <code>$this</code>, allowing to to chain
     *   methods.
     */
    public function limit($off, $count)
    {
        $limited = $this->specialize();

        $ioff   = intval($off);
        $icount = intval($count);

        $limited->request_query['limit']  = $icount;
        if ($ioff > 0) {
            $limited->request_query['offset'] = $ioff;
        }

        return $limited;
    }

    /**
     * sexy helper for the limit() method.
     *
     * @note
     *   This method is mostly designed to restrict the result to only one.
     *   When $n is 1, it will modify the hint accordingly (using EXPECT_ONE).
     *   There are cases when you'll set $n to 1 but still want an array as
     *   result (for exemple when $n is user provided). If so, use limit(0, $n)
     *   instead because limit() will not change the hint.
     *
     * @param $n
     *   The number of objects wanted.
     *
     * @return
     *   a modified clone of <code>$this</code>, allowing to to chain
     *   methods.
     */
    public function first($n = 1)
    {
        $limited = $this->limit(0, $n);
        if ($n == 1) {
            $limited->hint = self::EXPECT_ONE;
        }
        return $limited;
    }

    /**
     * execute this request as GET.
     *
     * @param mixed
     *   The list of fields to get, as an array or a comma
     *   separated list.
     * @param boolean
     *   True to allow cached results, false otherwise
     */
    public function get($fields = null, $allow_cached_results = false)
    {
        if (!is_null($fields)) {
            $fields = is_array($fields) ? implode(',', $fields) : $fields;
            $this->request_query[static::FIELDS_QUERY_PARAM] = $fields;
        }

        $klass = $this->klass;
        $options = [
            'factory' => $this->klass,
            'return_as_collection' => ($this->hint == self::EXPECT_MANY),
        ];

        if (!$allow_cached_results) {
            $options['nocache'] = 'true'; // FIXME: be more fine grained here to use cache
        }

        $rsp = static::request(
            static::GET,
            $this->request_path,
            $this->request_query,
            /* data */[],
            $options
        );
        $rsp = $this->do_post_process($rsp);
        return (No2_HTTP::is_success($rsp->status) ? $rsp->data : null);
    }

    /**
     * Add a post processing function to this request.
     *
     * @param
     *   a function taking exactly two arguments (the http status code and the
     *   an array of objects as constructed by request()) and returning the
     *   resulting array of objects.
     */
    public function add_post_process($func)
    {
        $neo = $this->specialize();
        $neo->post_process[] = $func;
        return $neo;
    }

    /**
     * handle the post processing logic.
     *
     * @param
     *   a response object as returned by request()
     */
    protected function do_post_process($rsp)
    {
        $single = !is_array($rsp->data);
        $data   = ($single ? [$rsp->data] : $rsp->data);
        $status = $rsp->status;

        foreach ($this->post_process as $func) {
            $data = $func($status, $data);
        }

        if ($single) {
            // NOTE: assume that count($data) is 0 or 1, the post process
            // function should not "split" a single object into many.
            $data = (count($data) === 1 ? $data[0] : null);
        }
        $neorsp = (object)['status' => $status, 'data' => $data];

        return $neorsp;
    }
}

class TKTException extends \Exception {}
