<?php
namespace Ticketack\Core\Base\Currency;

use Ticketack\WP\TKTApp;
use Ticketack\Core\Base\Currency\Money;

class Currency
{
    public static function __callStatic($name, $arguments)
    {
        $currency  = TKTApp::get_instance()->get_config('currency', 'CHF');
        $classname = 'Ticketack\Core\Base\Currency\\'.$currency;

        if (class_exists($classname) && is_subclass_of($classname, Money::class)) {
            return $classname::$name(...$arguments);
        }

        throw new \Exception("$classname class not found or is not implementing Money interface");
    }
}
