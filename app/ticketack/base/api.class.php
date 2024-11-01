<?php
namespace Ticketack\Core\Base;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Client;

/**
 * Helper methods to query the ticketack API.
 */
class TKTApi extends Client
{
    const HTTP_STATUS_OK = 200;

    /**
     * @var string
     *
     * Ticketack engine URI
     */
    protected $uri;

    /**
     * @var string
     *
     * Ticketack user API key to use
     */
    protected $api_key;

    /**
     * @var TKTApi
     *
     * Singleton instance
     */
    protected static $instance;

    /**
     * Create a singleton TKTApi instance
     *
     * @param string $uri: The Ticketack engine uri
     * @param string $api_key: The Ticketack user API key to use
     */
    public static function setup($uri, $api_key)
    {
        static::$instance = new static($uri, $api_key);
    }

    /**
     * Create a singleton TKTApi instance
     *
     * @param string $uri: The Ticketack engine uri
     * @param string $api_key: The Ticketack user API key to use
     *
     * @return TKTApi
     */
    public static function get_instance()
    {
        if (is_null(static::$instance)) {
            throw new TKTApiException("TKTApi not configured. Please call TKTApi::setup() before.");
        }

        return static::$instance;
    }

    /**
     * Constructor
     *
     * @param string $uri: The Ticketack engine uri
     * @param string $api_key: The Ticketack user API key to use
     */
    public function __construct($uri, $api_key)
    {
        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());
        $stack->push(static::api_key_middleware($api_key));

        parent::__construct([
            'base_uri' => $uri,
            'handler'  => $stack,
            'headers'  => [
                'Accept' => 'application/json',
                'Content-type' => 'application/json'
            ]
        ]);
    }

    public static function api_key_middleware($api_key)
    {
        return function (callable $handler) use ($api_key) {
            return function (RequestInterface $request, array $options) use ($handler, $api_key) {
                $request = $request->withHeader("X-API-Key", $api_key);
                return $handler($request, $options);
            };
        };
    }
}

class TKTApiException extends \Exception {}
