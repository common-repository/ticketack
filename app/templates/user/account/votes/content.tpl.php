<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * User account votes content
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "user": { ... },
 *   "tickets": [ ... ],
 *   "other_tickets": [ ... ],
 *   "orders": [ ... ],
 *   "votesConfig": { ... },
 * }
 */
?>
<%
const errors = {
    'screening.start_at': <?php echo wp_kses_post(wp_json_encode(tkt_t('Les votes seront possibles dès le début de la séance'))) ?>,
    'screening.stop_at': <?php echo wp_kses_post(wp_json_encode(tkt_t('Les votes seront possibles dès la fin de la séance'))) ?>,
    'needs_scan': <?php echo wp_kses_post(wp_json_encode(tkt_t('Vous ne pouvez pas voter car votre billet n\'a pas été contrôlé'))) ?>,
    'not_before': <?php echo wp_kses_post(wp_json_encode(tkt_t('Les votes ne sont pas encore possibles pour cette séance'))) ?>,
    'not_after': <?php echo wp_kses_post(wp_json_encode(tkt_t('Les votes ne sont pas plus possibles pour cette séance'))) ?>,
};
const bookings = [];
tickets.concat(other_tickets).map(t => {
    t.bookings?.map(b => {
        if (!b.screening?.opaque?.disable_votes && b.isVotable())
            bookings.push(b);
    });
});
%>
<div id="tkt-account-content-profile" class="tkt-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <% if (!bookings.length) { %>
            <h3 class="text-info text-center mt-3">
                <?php echo esc_html(tkt_t('Aucun vote disponible pour le moment.')) ?>
            </h3>
            <% } else { %>
            <div id="tickets-accordion">
                <% bookings.map((b, i) => { %>
                <div class="card">
                    <div class="card-header" id="heading-<%= i %>">
                        <h5 class="mb-0">
                            <div class="row">
                                <div class="col col-12 col-md-6">
                                    <% if (b.screening.getFirstPosterUrl()) { %>
                                        <!-- TODO: Make this weserv stuff done by any helper... -->
                                        <img src="//wsrv.nl/?h=80&url=<%= encodeURIComponent(b.screening.getFirstPosterUrl()) %>" />
                                    <% } %>

                                    <button class="btn btn-link text-left" data-toggle="collapse" data-target="#collapse<%= i %>" >
                                        <i class="tkt-icon-tag"></i>
                                        <b><%= b.screening?.getTitle() %></b>
                                        <div>
                                            <small><%= b.screening?.getStartAt() %></small>
                                        </div>
                                    </button>
                                </div>
                                <div class="col col-12 col-md-6 flex-row items-center justify-end">
                                    <div class="mr-2 mb-2 flex-col items-end">
                                        <% const { votable, reason } = b.isVotable(); %>
                                        <div
                                            data-component="Ui/Rating"
                                            data-score="<%= b.vote?.score || 0 %>"
                                            data-ticket-id="<%= b.ticket_id %>"
                                            data-booking-id="<%= b._id %>"
                                            data-size="24"
                                            data-step="<%= votesConfig?.step %>"
                                            data-max="<%= votesConfig?.max_score %>"
                                            data-disabled-reason="<%= reason ? errors[reason] : '' %>"
                                        ></div>
                                        <% if (reason) { %>
                                            <small>
                                                <i class="tkt_icon_warning" />
                                                <%= errors[reason] %>
                                            </small>
                                        <% } %>
                                    </div>
                                </div>
                            </div>
                        </h5>
                    </div>
                </div>
                <% }) %>
            </div>
            <% } %>
        </div>
    </div>
</div>
