<?php
namespace Ticketack\Core\Base\Currency;

/**
 * CFC (Costa Rican Colon) Model.
 *
 * @see Money
 */

class CFC extends CHF
{
    static protected $regexp = '/^\s*(?:CFC\s+)?(\-?\d{1,3}(?:\'\d\d\d)*|\d+)(?:\.(\d\d*|-))?\s*$/';

    protected $currency = 'CFC';
}
