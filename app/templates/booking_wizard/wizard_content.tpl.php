<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Booking form: dates selector partial
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
$currency = TKTApp::get_instance()->get_config('currency', 'CHF');
?>
<%
var notFinishedScreenings = _.filter(state.screenings, function (s) {
    return s.stop_at > new Date() && s.seats.available > 0;
});

var calendarEnabledDates = [];
_.forEach(notFinishedScreenings, function(s) {
    var day = s.start_at.format("YYYY-MM-DD");
    if (!calendarEnabledDates.includes(day))
        calendarEnabledDates.push(day);
});

var selectedDayScreenings = _.filter(notFinishedScreenings, function (s) {
    return s.start_at.isSame(state.day, 'day');
});

var timeChoices = [];
if (state.nbRuns === 1) {
    timeChoices = selectedDayScreenings.map(function (s) {
        return {
            _ids: [s._id],
            start_at: s.start_at
        };
    });
} else if (state.nbRuns == 2) {
    var sortedScreenings = selectedDayScreenings.sort(function (a, b) {
        return a.start_at == b.start_at ? 0 : (
            b.start_at > a.start_at ? -1 : 1
        );
    });
    for(var i = 0; i < sortedScreenings.length - 1; i++) {
        var first = sortedScreenings[i];
        var next  = sortedScreenings[i + 1];
        if (first.stop_at.isSame(next.start_at)) {
            timeChoices.push({
                _ids: [first._id, next._id],
                start_at: first.start_at
            });
        }
    }
}

function getNbAvailableSeatsBySize(s) {
    let availableSizes = { S: 0, M: 0, L: 0, XL: 0};
    s.cinema_hall.map.seats.filter(seat => seat.status === 'free').map(seat => {
        var size = seat.placing.row;
        if (!(size in availableSizes))
            availableSizes[size] = 0;
        availableSizes[size] += 1;
    });
    return availableSizes;
}

let availableSizes = { S: 0, M: 0, L: 0, XL: 0};
if (state.selectedScreenings.length == 1) {
    availableSizes = getNbAvailableSeatsBySize(state.selectedScreenings[0]);
} else if (state.selectedScreenings.length == 2) {
    var available1 = getNbAvailableSeatsBySize(state.selectedScreenings[0]);
    var available2 = getNbAvailableSeatsBySize(state.selectedScreenings[1]);
    for (var size in available1) {
        if (!(size in available2))
            continue;

        availableSizes[size] = Math.min(
            available1[size],
            available2[size]
        );
    }
}

function getAvailablePricings() {
    const pricings = [];
    Object.keys(state.selectedScreenings[0].pricings).map(key => {
        var keys = [];
        if (state.nbRuns === 1) {
            keys = [
                '1619601449435',
                '1623621075732',
                '1623621107179',
                '1623621272175',
                '1623621288472'
            ];
        } else if (state.nbRuns === 2) {
            keys = [
                '1620030024748',
                '1623621249674',
                '1623621260440',
                '1623621297610',
                '1623621308541'
            ];
        }

        if (keys.includes(key))
            pricings.push(state.selectedScreenings[0].pricings[key]);
    });

    return pricings;
}

function isSelectedPricing(size, index, key) {
    if ((state.selectedPricings || []).length ==0)
        return false;
    return !!(state.selectedPricings.find(p => (
        p.size === size &&
        p.index === index &&
        p.pricing.key === key
    )));
}

function areUserInfosFilled() {
    return state.userInfos.length > 0 && !(_.some(state.userInfos, function(infos) {
        return infos.firstname == "" || infos.lastname == "";
    }));
}
%>
<div class="booking-wizard-content">

    <% if (state.step == 1) { %>
    <div class="booking-wizard-content-step" data-step="1">
        <h5 class="booking-wizard-content-step-title"><?php echo esc_html(tkt_t('Veuillez choisir le nombre de descentes')) ?></h5>
        <div class="booking-wizard-content-step-content">
            <div class="nb-runs-choices">
                <span class="tkt-badge tkt-light-badge nb-runs-choice" data-nb-runs="1">
                    1 <?php echo esc_html(tkt_t('descente')) ?>
                </span>
                <span class="tkt-badge tkt-light-badge nb-runs-choice" data-nb-runs="2">
                    2 <?php echo esc_html(tkt_t('descentes consécutives')) ?>
                </span>
            </div>
        </div>
    </div>
    <% } %>

    <% if (state.step == 2) { %>
    <div class="booking-wizard-content-step" data-step="2">
        <h5 class="booking-wizard-content-step-title"><?php echo esc_html(tkt_t('Veuillez choisir une date')) ?> :</h5>
        <div class="booking-wizard-content-step-content">
            <div class="booking-wizard-day-choice">
                <input
                    class="booking-wizard-calendar"
                    type="text"
                    class="tkt-input form-control data-field"
                    data-component="Form/Calendar"
                    required
                    data-alt-format="<?php echo esc_html(tkt_t('l j F')) ?>" 
                    data-enable="<%= calendarEnabledDates.join(',') %>"
                    data-inline="true"
                />
            </div>
        </div>
    </div>
    <% } %>

    <% if (state.step == 3) { %>
    <div class="booking-wizard-content-step" data-step="3">
        <h5 class="booking-wizard-content-step-title"><?php echo esc_html(tkt_t('Veuillez choisir une heure')) ?> :</h5>
        <div class="booking-wizard-content-step-content">
            <div class="booking-wizard-time-choices">
                <% _.forEach(timeChoices, function(c) { %>
                    <span
                        class="tkt-badge tkt-light-badge booking-wizard-time-choice"
                        data-time="<%= c.start_at.format("H[h]") + (c.start_at.minutes() > 0 ? c.start_at.format("mm") : "") %>"
                        data-screening-ids="<%= c._ids.join(',')  %>"
                    >
                        <%= c.start_at.format("H[h]") + (c.start_at.minutes() > 0 ? c.start_at.format("mm") : "") %>
                    </span>
                <% }) %>
            </div>
        </div>
    </div>
    <% } %>

    <% if (state.step == 4) { %>
    <div class="booking-wizard-content-step" data-step="4">
        <h5 class="booking-wizard-content-step-title"><?php echo esc_html(tkt_t('Veuillez choisir vos MountainCarts')) ?> :</h5>
        <div class="booking-wizard-content-step-content">
            <div class="booking-wizard-sizes-choices">
                <% if (Object.keys(availableSizes).length > 0) { %>
                    <% Object.keys(availableSizes).map(size => { %>
                    <div class="booking-wizard-sizes-choice-wrapper">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <?php echo esc_html(tkt_t('Taille')) ?> <%= size %>
                                </span>
                            </div>
                            <select class="booking-wizard-sizes-choice form-control" data-size="<%= size %>">
                                <option value="0">0</option>
                                <% for (var i = 1; i <= availableSizes[size]; i++) { %>
                                <option value="<%= i %>" <%= state.selectedSizes && state.selectedSizes[size] == i ? 'selected' : '' %>><%= i %></option>
                                <% } %>
                            </select>
                        </div>
                    </div>
                    <% }) %>
                    <div class="booking-wizard-sizes-next-button-wrapper">
                        <button class="button btn btn-block booking-wizard-sizes-next-button" <%= Object.keys(state.selectedSizes).length == 0 ? 'disabled' : '' %>>
                            <?php echo esc_html(tkt_t("Étape suivante")) ?>
                        </button>
                    </div>
                <% } else { %>
                    <div class="booking-wizard-warning-message">
                        <?php echo esc_html(tkt_t("Il n'y a plus de places pour cette date.")) ?>
                    </div>
                <% } %>
            </div>
        </div>
    </div>
    <% } %>

    <% if (state.step == 5) { %>
    <div class="booking-wizard-content-step" data-step="5">
        <h5 class="booking-wizard-content-step-title"><?php echo esc_html(tkt_t('Veuillez saisir vos informations')) ?> :</h5>
        <div class="booking-wizard-content-step-content">
            <div class="booking-wizard-user-infos">
                <% var ticketIndex = 0; %>
                <% if (Object.keys(state.selectedSizes).length > 0) { %>
                    <% Object.keys(state.selectedSizes).map((size, sizeIndex) => { %>
                        <% for (var i =1; i <= state.selectedSizes[size]; i++) { %>
                        <div class="booking-wizard-user-info-wrapper">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <?php echo esc_html(tkt_t('Taille')) ?> <%= size %>
                                    </span>
                                </div>
                                <input type="text" class="booking-wizard-user-info form-control" data-size="<%= size %>" data-field="firstname" data-ticket-index="<%= ticketIndex %>" data-index="<%= i %>" placeholder="<?php echo esc_html(tkt_t('Prénom')) ?>" value="<%= ticketIndex in state.userInfos ? state.userInfos[ticketIndex].firstname : '' %>" />
                            </div>
                        </div>
                        <% ticketIndex += 1; %>
                        <% } %>
                    <% }) %>
                    <div class="booking-wizard-user-info-next-button-wrapper">
                        <button class="button btn btn-block booking-wizard-next-button" <%= !areUserInfosFilled() ? 'disabled' : '' %>>
                            <?php echo esc_html(tkt_t("Étape suivante")) ?>
                        </button>
                    </div>
                <% } else { %>
                    <div class="booking-wizard-warning-message">
                        <?php echo esc_html(tkt_t("Il n'y a plus de places pour cette date.")) ?>
                    </div>
                <% } %>
            </div>
        </div>
    </div>
    <% } %>

    <% if (state.step == 6) { %>
    <div class="booking-wizard-content-step" data-step="5">
        <h5 class="booking-wizard-content-step-title"><?php echo esc_html(tkt_t('Veuillez choisir votre tarif')) ?> :</h5>
        <div class="booking-wizard-content-step-content">
            <div class="booking-wizard-pricings-choices">
                <% var ticketIndex = 0; %>
                <% if (Object.keys(state.selectedSizes).length > 0) { %>
                    <% Object.keys(state.selectedSizes).map(size => { %>
                        <% for (var i =1; i <= state.selectedSizes[size]; i++) { %>
                        <div class="booking-wizard-pricings-choice-wrapper">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <?php echo esc_html(tkt_t('Taille')) ?> <%= size %> - <%= state.userInfos[ticketIndex].firstname %>
                                    </span>
                                </div>
                                <select class="booking-wizard-pricings-choice form-control" data-size="<%= size %>" data-index="<%= i %>">
                                    <option value=""><?php echo esc_html(tkt_t('Choisissez un tarif')) ?></option>
                                    <% getAvailablePricings().map(pricing => { %>
                                        <% var key = pricing.key; %>
                                    <option value="<%= key %>" <%= isSelectedPricing(size, i, key) ? 'selected' : '' %>>
                                        <?php echo esc_html($currency) ?> <%= state.selectedScreenings[0].pricings[key].price.<?php echo esc_html($currency) ?> * state.nbRuns %> -
                                        <%= state.selectedScreenings[0].pricings[key].name['fr'] %>
                                    </option>
                                    <% }) %>
                                </select>
                            </div>
                        </div>
                        <% ticketIndex += 1; %>
                        <% } %>
                    <% }) %>
                    <div class="booking-wizard-pricings-book-button-wrapper">
                        <button class="button btn btn-block booking-wizard-book-button" <%= (state.selectedPricings || []).length < state.nbTickets ? 'disabled' : '' %>>
                            <?php echo esc_html(tkt_t("Réserver")) ?>
                        </button>
                    </div>
                    <div class="booking-wizard-error-wrapper">
                        <div class="booking-wizard-error alert alert-danger">
                        </div>
                    </div>
                <% } else { %>
                    <div class="booking-wizard-warning-message">
                        <?php echo esc_html(tkt_t("Il n'y a plus de places pour cette date.")) ?>
                    </div>
                <% } %>
            </div>
        </div>
    </div>

    <% } %>
</div>
