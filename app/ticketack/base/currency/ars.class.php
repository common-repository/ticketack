<?php
namespace Ticketack\Core\Base\Currency;

/**
 * ARS (Argentina Peso) Model.
 *
 * @see Money
 */

class ARS extends CHF
{
    static protected $regexp = '/^\s*(?:ARS\s+)?(\-?\d{1,3}(?:\'\d\d\d)*|\d+)(?:\.(\d\d*|-))?\s*$/';

    protected $currency = 'ARS';
}
