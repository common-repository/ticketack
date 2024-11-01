<?php
namespace Ticketack\Core\Base\Currency;

/**
 * RUB (Russian Rouble) Model.
 *
 * @see Money
 */

class RUB extends CHF
{
    static protected $regexp = '/^\s*(?:RUB\s+)?(\-?\d{1,3}(?:\'\d\d\d)*|\d+)(?:\.(\d\d*|-))?\s*$/';

    protected $currency = 'RUB';
}
