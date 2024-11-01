<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Booking wizard: navigation template
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "state": {
 *       "step": int,
 *       "maxStep": int,
 *       "nbRuns": int,
 *       "day": Moment object,
 *       "time": string,
 *       "screenings": Array of Screening instances,
 *       "selectedScreenings": Array of 1 or 2 selected screenings,
 *       "selectedSizes" Object with the number of seats by size,
 *       "selectedPricings": Array of the selected pricings,
 *       "userInfos": Array of the tickets user infos
 *   }
 * }
 */
?>
<div class="tkt-wrapper">
    <div class="row">
        <div class="col">
            Navigation
        </div>
    </div>
</div>
