<?php
namespace Ticketack\Core\Base\Currency;

/**
 * ZAR (South African Rand) Model.
 *
 * @see Money
 */

class ZAR extends CHF
{
    static protected $regexp = '/^\s*(?:ZAR\s+)?(\-?\d{1,3}(?:\'\d\d\d)*|\d+)(?:\.(\d\d*|-))?\s*$/';

    protected $currency = 'ZAR';
}
