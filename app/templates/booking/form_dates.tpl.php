<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Booking form: dates selector partial
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "screenings": Array of Screening instances
 * }
 */
?>
<% screenings = _.filter(screenings, function (s) { return s.stop_at > new Date(); }); %>
<% if (screenings.length > 1) { %>
<%
    var m = {};
    _.forEach(screenings, function(s) {
        var day = s.start_at.format("dddd D MMMM");
        if (!m[day])
            m[day] = [];
        m[day].push(s);
    });


    var dates = [];
    _.forEach(screenings, function(s) {
        var day = s.start_at.format("YYYY-MM-DD");
        if (!dates.includes(day))
            dates.push(day);
    });
%>

<div class="tkt-wrapper">
    <div class="row">
        <div class="col">
            <span>
                <?php echo esc_html(tkt_t('Veuillez choisir la date désirée :')) ?>
            </span>
            <div class="days-wrapper">
                <% if (Object.keys(m).length > 6) { %>
                    <input
                        id="calendar"
                        type="text"
                        class="tkt-input form-control data-field"
                        data-component="Form/Calendar"
                        required
                        data-alt-format="<?php echo esc_html(tkt_t('l j F')) ?>" 
                        data-enable="<%= dates.join(',') %>"
                        data-inline="true"
                    />

                <% } else { %>
                    <% _.forEach(Object.keys(m), function(day) { %>
                    <span
                        class="tkt-badge tkt-light-badge day"
                        data-day="<%= day %>"
                        data-screening_id="<%= _.map(m[day], function (s) { return s._id; }).join(',') %>">
                        <%= day %>
                    </span>
                    <% }) %>
                <% } %>
            </div>

            <br/>

            <span>
                <?php echo esc_html(tkt_t('Veuillez choisir l\'heure désirée :')) ?>
            </span>
            <div class="dates-wrapper">
                <% _.forEach(m, function(screenings, day) { %>
                    <% _.forEach(screenings, function(s) { %>
                    <span
                        class="tkt-badge tkt-light-badge date"
                        data-day="<%= day %>"
                        data-screening_id="<%= s._id  %>"
                    >
                        <%= s.start_at.format("LT") %>

                        <% if (s.opaque && s.opaque.version) { %>
                        <%= (' - ' + s.opaque.version) %>
                        <% } %>

                        <% if (s.opaque && s.opaque._3d) { %>
                        <%= s.opaque && s.opaque._3d && (' - 3D') %>
                        <% } %>
                        <%= ' - ' + s.cinema_hall.name %>
                    </span>
                    <% }) %>
                <% }) %>
            </div>

            <br/>

        </div>
    </div>
</div>
<% } else if (screenings.length == 1) { %>
<span data-screening_id="<%= screenings[0]._id %>" class="date single-date">
    <%= screenings[0].start_at.format("dddd D MMMM") %> <?php echo esc_html(tkt_t('à')) ?> <%= screenings[0].start_at.format("LT") + ' - ' + screenings[0].cinema_hall.name %>

</span>
<% } else { %>
<span><?php echo esc_html(tkt_t("Il n'y a pas de billetterie disponible pour cet événement.")) ?></span>
<% } %>
