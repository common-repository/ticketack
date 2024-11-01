<?php
namespace Ticketack\WP;

use Ticketack\Core\Base\TKTApi;
use Ticketack\Core\Models\Settings;

/**
 * Ticketack App
 */

class TKTApp
{
    /**
     * @var array
     *
     * Ticketack actions
     */
    protected $actions;

    /**
     * @var array
     *
     * Ticketack filters
     */
    protected $filters;

    /**
     * @var array
     *
     * Ticketack shortcodes
     */
    protected $shortcodes;

    /**
     * @var array
     *
     * Ticketack configuration
     */
    protected $config;

    /**
     * @var TKTApp
     *
     * Singleton instance
     */
    protected static $instance = null;

    /**
     * Create a singleton TKTApp instance
     *
     * @return TKTApp
     */
    public static function get_instance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected function __construct()
    {
        $this->actions    = [];
        $this->filters    = [];
        $this->shortcodes = [];

        $this->load_config();
    }

    /**
     * Load the wp options and the ticketack settings
     *
     * @param bool $force_refresh: True to force refresh the cached
     *                             settings even if the file exists
     */
    public function load_config($force_refresh = false)
    {
        $this->config = [
            'pages'             => (array)get_option('tkt_pages'),
            'cart'              => (array)get_option('tkt_cart'),
            'checkout'          => (array)get_option('tkt_checkout'),
            'registration'      => (array)get_option('tkt_registration'),
            'ticketack'         => (array)get_option('tkt_api'),
            'images_dimensions' => (array)get_option('tkt_images_dimensions'),
            'images_proxy'      => (array)get_option('tkt_images_proxy'),
            'pass'              => (array)get_option('tkt_pass'),
            'i18n'              => (array)get_option('tkt_i18n'),
            'import'            => (array)get_option('tkt_import'),
            'advanced'          => (array)get_option('tkt_advanced')
        ];

        // Check if Ticketack API is configured
        $api_config = $this->config['ticketack'];
        foreach (['engine_uri', 'api_key'] as $property) {
            if (!array_key_exists($property, $api_config) || empty($api_config[$property])) {
                return false;
            }
        }

        // Setup Ticketack API
        TKTApi::setup(
            $this->get_config('ticketack.engine_uri'),
            $this->get_config('ticketack.api_key')
        );

        $generated_config_path = TKT_APP.'/config.inc.php';
        if (!file_exists($generated_config_path) || $force_refresh) {
            // Refresh cached settings
            if (!Settings::refresh($generated_config_path, $this->config)) {
                return false;
            }
        }

        if (file_exists($generated_config_path)) {
            @include($generated_config_path);

            if (!is_array($ticketack_config)) {
                return false;
            }
            $this->config = $ticketack_config;
        }

        return true;
    }

    /**
     * Register an action
     *
     * @param string $classname: The action class name
     * @param string $filename: The action filename
     */
    public function register_action($classname, $filename)
    {
        $this->actions[$classname] = TKT_ACTIONS.'/'.$filename;
    }

    /**
     * Register a filter
     *
     * @param string $classname: The filter class name
     * @param string $filename: The filter filename
     */
    public function register_filter($classname, $filename)
    {
        $this->filters[$classname] = TKT_FILTERS.'/'.$filename;
    }

    /**
     * Register a shortcode
     *
     * @param string $classname: The shortcode class name
     * @param string $filename: The shortcode filename
     */
    public function register_shortcode($classname, $filename)
    {
        $this->shortcodes[$classname] = TKT_SHORTCODES.'/'.$filename;
    }

    /**
     * Register the theme provided shortcodes
     *
     * Theme shortcodes should be declared in THEME_ROOT/ticketack/shortcodes as
     * child classes of TKTShortcode in the Ticketack\WP\Shortcodes namespace.
     *
     * If the class name is composed of several words, the filename should use _
     * as separator (eg. HpFiltersShortcode => hp_filters.class.php).
     *
     * @return boolean
     */
    public function register_theme_shortcodes()
    {
        $path = TKT_OVERRIDE_DIR.'/ticketack/shortcodes/';
        if (!file_exists($path)) {
            return;
        }

        $files = glob($path . '*.class.php');
        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            $name            = str_replace('.class.php', '', basename($file));
            $camel_case_name = implode('', array_map(function ($n) {
                return ucfirst(strtolower($n));
            }, explode('_', $name)));
            $classname = 'Ticketack\\WP\\Shortcodes\\'.$camel_case_name.'Shortcode';

            $this->shortcodes[$classname] = $file;
        }
    }

    /**
     * Start the application
     */
    public function start()
    {
        // Instantiate actions
        foreach ($this->actions as $classname => $filename) {
            require_once($filename);
            $action = new $classname($this);
        }

        // Instantiate filters
        foreach ($this->filters as $classname => $filename) {
            require_once($filename);
            $filter = new $classname($this);
        }

        // Instantiate shortcodes
        foreach ($this->shortcodes as $classname => $filename) {
            require_once($filename);
            $shortcode = new $classname($this);
        }
    }

    public function get_config($path = null, $default = null)
    {
        if (is_null($path)) {
            return $this->config;
        }

        if (!is_array($path)) {
            $path = explode('.', $path);
        }

        $value = $this->config;

        foreach ($path as $p) {
            if (!isset($value[$p])) {
                return $default;
            }

            $value = $value[$p];
        }

        return $value;
    }

    public function is_configured()
    {
        return
            !empty($this->get_config('ticketack.engine_uri')) &&
            !empty($this->get_config('ticketack.eshop_uri')) &&
            !empty($this->get_config('ticketack.api_key'));
    }
}
