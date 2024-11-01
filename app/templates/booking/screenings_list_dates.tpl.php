<?php

if (!defined('ABSPATH')) exit;

/**
 * Booking form: dates selector partial
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "screenings": Array of Screening instances
 * }
 */
?>
<% if (screenings.length > 0) { %>
<div class="row">
    <div class="col">
        <div class="dates-wrapper">
        <% _.forEach(screenings, function(s) { %>
            <span data-screening_id="<%= s._id %>" class="tkt-badge tkt-light-badge date">
                <%= s.start_at.format("dddd Do") + ' / ' + s.start_at.format("HH[h]") + (s.start_at.minutes() > 0 ? s.start_at.format("mm") : "") %> <span>/</span>
                <%= s.cinema_hall.name %>
                <% if(s.opaque) { %>
                    <% if (s.opaque.additional_info) { %>
                        <% if (s.opaque.additional_info.<?php echo esc_html(TKT_LANG) ?> && s.opaque.additional_info.<?php echo esc_html(TKT_LANG) ?> != 'COMPLET') { %>
                                <%= ' <span>/</span> ' + s.opaque.additional_info.<?php echo esc_html(TKT_LANG) ?> %>
                        <% } %>
                    <% } %>
                <% } %>
                <% if (s.seats) { %>
                    <% if (s.seats.available == 0 && s.buckets.length > 0) { %>
                        <span>/</span> <span class="full-label">COMPLET</span>
                    <% } %>
                <% } %>
            </span>
        <% }) %>
        </div>
    </div>
</div>
<% } %>
