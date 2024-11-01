<?php
namespace Ticketack\Core\Base\Currency;

/**
 * USD (US Dollar) Model.
 *
 * @see Money
 */

class USD extends CHF
{
    static protected $regexp = '/^\s*(?:USD\s+)?(\-?\d{1,3}(?:\'\d\d\d)*|\d+)(?:\.(\d\d*|-))?\s*$/';

    protected $currency = 'USD';
}
