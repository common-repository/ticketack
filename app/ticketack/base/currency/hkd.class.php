<?php
namespace Ticketack\Core\Base\Currency;

/**
 * HKD (Hong Kong Dollar) Model.
 *
 * @see Money
 */

class HKD extends CHF
{
    static protected $regexp = '/^\s*(?:HKD\s+)?(\-?\d{1,3}(?:\'\d\d\d)*|\d+)(?:\.(\d\d*|-))?\s*$/';

    protected $currency = 'HKD';
}
