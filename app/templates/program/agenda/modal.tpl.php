<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Booking form: dates selector partial
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "screening": Screening instance
 * }
 */
?>
<% if (screening) { %>
    <div class="tkt-wrapper tkt-agenda-modal">
        <div class="tkt-agenda-modal-blur"></div>
        <div class="tkt-agenda-modal-content">
            <i class="close-modal tkt-icon-times"></i>
            <div class="tkt-agenda-modal-header">
                <div class="left">
                    <h3 class="title">
                        <div>
                            <%= screening.getTitle(<?php echo wp_json_encode(TKT_LANG) ?>) %>
                        </div>
                        <small class="infos">
                            <%= [screening.start_at.format('LLL'), screening.cinema_hall.name].join(', ') %>
                        </small>
                    </h3>
                </div>
                <div class="right">
                    <% if (screening.opaque.posters && screening.opaque.posters.length > 0) { %>
                        <img class="poster" src="<%= screening.opaque.posters[0].url %>" />
                    <% } %>
                </div>
            </div>
            <div class="tkt-agenda-modal-body">
                <div
                    class="booking-form v2"
                    data-component="Booking/Form"
                    data-ids="<%= [screening._id] %>"
                    data-show="pricings"
                >
                </div>
            </div>
            <div class="tkt-agenda-modal-footer">

            </div>
        </div>
    </div>
<% } %>
