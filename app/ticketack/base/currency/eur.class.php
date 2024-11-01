<?php
namespace Ticketack\Core\Base\Currency;

/**
 * EUR (Euro) Model.
 *
 * @see Money
 */

class EUR extends CHF
{
    static protected $regexp = '/^\s*(?:EUR\s+)?(\-?\d{1,3}(?:\'\d\d\d)*|\d+)(?:\.(\d\d*|-))?\s*$/';

    protected $currency = 'EUR';
}
