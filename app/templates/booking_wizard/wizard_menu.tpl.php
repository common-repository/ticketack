<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Booking wizard: menu template
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
<%
function itemClass(step) {
    const classes = ['booking-wizard-menu-item'];
    if (state.step === step)
        classes.push('active');
    else if (state.maxStep < step)
        classes.push('forbidden');
    else
        classes.push('accessible');

    return classes.join(' ');
}
%>
<div class="tkt-wrapper booking-wizard-menu-items">
    <div class="<%= itemClass(1) %>" data-target="1">
        <div class="booking-wizard-menu-icon"><i class="tkt-icon-level-down-alt"></i></div>
        <div class="booking-wizard-menu-content">
            <div class="booking-wizard-menu-title"><?php echo esc_html(tkt_t('Nombre de descentes')) ?></div>
            <div class="booking-wizard-menu-infos">
                <% if(state.nbRuns == 1) { %>
                <span><?php echo esc_html(tkt_t('1 descente')) ?></span>
                <% } else if(state.nbRuns == 2) { %>
                <span><?php echo esc_html(tkt_t('2 descentes')) ?></span>
                <% } %>
            </div>
        </div>
    </div>
    <div class="<%= itemClass(2) %>" data-target="2">
        <div class="booking-wizard-menu-icon"><i class="tkt-icon-calendar"></i></div>
        <div class="booking-wizard-menu-content">
            <div class="booking-wizard-menu-title"><?php echo esc_html(tkt_t('Date')) ?></div>
            <div class="booking-wizard-menu-infos">
                <% if (state.day) { %>
                <span>
                    <%= state.day.format("dddd D MMMM") %>
                </span>
                <% } %>
            </div>
        </div>
    </div>
    <div class="<%= itemClass(3) %>" data-target="3">
        <div class="booking-wizard-menu-icon"><i class="tkt-icon-clock"></i></div>
        <div class="booking-wizard-menu-content">
            <div class="booking-wizard-menu-title"><?php echo esc_html(tkt_t('Départ')) ?></div>
            <div class="booking-wizard-menu-infos">
                <% if (state.selectedTimes) { %>
                <span>
                    <%= state.selectedTimes %>
                </span>
                <% } %>
            </div>
        </div>
    </div>
    <div class="<%= itemClass(4) %>" data-target="4">
        <div class="booking-wizard-menu-icon"><i class="tkt-icon-th-list"></i></div>
        <div class="booking-wizard-menu-content">
            <div class="booking-wizard-menu-title"><?php echo esc_html(tkt_t('Choix des MountainCarts')) ?></div>
            <div class="booking-wizard-menu-infos">
                <% if (state.selectedSizes) { %>
                    <%= Object.keys(state.selectedSizes).map(size => state.selectedSizes[size] + ' x ' + size).join(' - ') %>
                <% } %>
            </div>
        </div>
    </div>
    <div class="<%= itemClass(5) %>" data-target="5">
        <div class="booking-wizard-menu-icon"><i class="tkt-icon-user"></i></div>
        <div class="booking-wizard-menu-content">
            <div class="booking-wizard-menu-title"><?php echo esc_html(tkt_t('Vos informations')) ?></div>
            <div class="booking-wizard-menu-infos">
            </div>
        </div>
    </div>
    <div class="<%= itemClass(6) %>" data-target="6">
        <div class="booking-wizard-menu-icon"><i class="tkt-icon-list"></i></div>
        <div class="booking-wizard-menu-content">
            <div class="booking-wizard-menu-title"><?php echo esc_html(tkt_t('Tarifs')) ?></div>
            <div class="booking-wizard-menu-infos">
            </div>
        </div>
    </div>
</div>
