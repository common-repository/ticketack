<?php
namespace Ticketack\Core\Base\Currency;

/**
 * EGP (Egypt Pound) Model.
 *
 * @see Money
 */

class EGP extends CHF
{
    static protected $regexp = '/^\s*(?:EGP\s+)?(\-?\d{1,3}(?:\'\d\d\d)*|\d+)(?:\.(\d\d*|-))?\s*$/';

    protected $currency = 'EGP';
}
