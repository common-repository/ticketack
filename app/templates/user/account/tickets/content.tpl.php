<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * User account pass content
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "user": { ... },
 *   "tickets": [ ... ],
 *   "other_tickets": [ ... ],
 *   "orders": [ ... ],
 * }
 */
?>
<%
const ticketsGroups = [
    {
        // passes linked to the connected user account
        title: "<?php echo esc_html(tkt_t('Mes abonnements')) ?>",
        tickets: tickets.filter(ticket => !ticket.isOneTimePass() && ticket.isActivated())
    },
    {
        // other passes, added with their TicketID
        title: <?php echo wp_kses_post(wp_json_encode(tkt_ticketidize(tkt_t('Autres abonnements (ajoutés avec leur TicketID)')))) ?>,
        tickets: other_tickets.filter(ticket => !ticket.isOneTimePass() && ticket.isActivated()).map(ticket => {
            // those tickets can be forgotten because they were added with their ticketID
            ticket.isForgettable = true;
            return ticket;
        })
    },
    {
        // future one-time-passes
        title: "<?php echo esc_html(tkt_t('Mes tickets pour une séance unique')) ?>",
        tickets: tickets.filter(ticket => ticket.isOneTimePass() && !ticket.getScreening()?.isFinished() && ticket.isActivated())
    },
    {
        // past one-time-passes
        title: "",
        tickets: tickets.filter(ticket => ticket.isOneTimePass() && ticket.getScreening()?.isFinished() && ticket.isActivated())
    }
];
%>
<div id="tkt-account-content-profile" class="tkt-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <% if (!tickets || tickets.length == 0) { %>
            <h3 class="text-info text-center mt-3">
                <?php echo esc_html(tkt_t('Vous n\'avez pas encore de billets.')) ?>
            </h3>
            <% } else { %>
            <div id="tickets-accordion">
                <% ticketsGroups.filter(group => group.tickets.length > 0).map(group => { %>
                <h4 class="tickets-group-title"><%= group.title %></h4>
                <% group.tickets.sort(function (a, b) { return a.activated_at > b.activated_at ? -1 : 1}).map((ticket, i) => { %>
                <div class="card">
                    <div class="card-header <%= ticket.isOneTimePass() && ticket.getScreening()?.isFinished() ? 'past' : ''%>" id="heading-<%= i %>">
                        <h5 class="mb-0">
                            <div class="row">
                                <div class="col col-auto">
                                    <% if (ticket.getScreening() && ticket.getScreening().getFirstPosterUrl()) { %>
                                        <img src="<%= ticket.bookings[0].screening.getFirstPosterUrl() %>" />
                                    <% } %>

                                    <button class="btn btn-link text-left" data-toggle="collapse" data-target="#collapse<%= i %>" >
                                        <i class="tkt-icon-tag"></i>
                                        <b><%=
                                            ticket.isOneTimePass() ?
                                                ticket.getScreeningName() :
                                                (ticket.getTypeName() + ' - ' + ticket.getDisplayName())
                                        %></b>
                                        <div>
                                            <small><%=
                                                ticket.isOneTimePass() ?
                                                    (ticket.getScreeningStartAt() + ' - ' + ticket.getScreeningPlace()) : ''
                                            %></small>
                                        </div>
                                    </button>
                                </div>
                                <div class="col text-right">
                                    <span class="badge badge-dark">
                                        <% if (!ticket.isOneTimePass()) { %>
                                            <i class="tkt-icon-tags"></i>
                                            <%= ticket.bookings?.length || 0 %>
                                        <% } else { %>
                                            <i class="tkt-icon-tag"></i>
                                        <% } %>
                                    </span>
                                    <% if (ticket.getPdfUrl()) { %>
                                    <div class="ticket-number mt-2">
                                        <% if (ticket.isForgettable) { %>
                                        <button class="btn btn-link btn-sm ticket-forget-link" data-ticket-id="<%= ticket._id %>">
                                            <i class="tkt-icon-trash"></i>
                                            <?php echo esc_html(tkt_t('Oublier ce billet')) ?>
                                        </button>
                                        <% } %>
                                        <% if (ticket.isOneTimePass()) { %>
                                        <a class="btn btn-link btn-sm ticket-view-link" href="<%= ticket.getTicketViewUrl() %>">
                                            <i class="tkt-icon-eye"></i>
                                            <?php echo esc_html(tkt_t('Voir ce ticket')) ?>
                                        </a>
                                        <% } %>
                                        <% if (!ticket.isOneTimePass()) { %>
                                        <a class="btn btn-link btn-sm ticket-view-link" href="<%= ticket.getTicketViewUrl() %>">
                                            <i class="tkt-icon-eye"></i>
                                            <?php echo esc_html(tkt_t('Voir mes réservations')) ?>
                                        </a>
                                        <% } %>
                                        <% if (!ticket.isOneTimePass() || !ticket.getScreening()?.isFinished()) { %>
                                        <a class="btn btn-link btn-sm ticket-download-link" target="_blank" href="<%= ticket.getPdfUrl() %>">
                                            <i class="tkt-icon-download"></i>
                                            <?php echo esc_html(tkt_t('Télécharger')) ?>
                                        </a>
                                        <% } %>
                                    </div>
                                    <% } %>
                                </div>
                            </div>
                        </h5>
                    </div>
                </div>
                <% }) %>
                <% }) %>
            </div>
            <% } %>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h4 class="tickets-group-title mt-4">
                <b><?php echo esc_html(tkt_t('Ajouter un billet acheté en billetterie physique')) ?></b>
            </h4>
            <div class="card p-2">
                <div class="tkt-wrapper" data-component="Ticket/TicketConnect"></div>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Ticket connection widget content
 * This template will be parsed by underscore.js
 *
 * JS Input: {
 *   "ticket": Ticket instance, if the ticket is connected,
 *   "program_url": String
 * }
 */
?>
<script type="text/template" id="tkt-ticket-connect-tpl">
    <div class="tkt-ticket-connect">
        <div class="connect-panel">
            <div class="ticket_connect">
                <div class="mt-2">
                    <?php echo wp_kses_post(tkt_ticketidize(tkt_t("Saisissez votre TicketID"))) ?>
                </div>
                <div class="col">
                    <div class="row input-pass">
                        <input id="pass-id" type="number"
                            class="tkt-input input-invert form-control text-center pass-number-input"
                            placeholder="123456" maxlength="6" />
                        <p class="minus">-</p>
                        <input id="pass-code" type="password" class="input input-invert text-center pass-key-input"
                            placeholder="abcdef" maxlength="6" />
                    </div>
                </div>
                <div class="row">
                    <div class="col text-center">
                        <div class="error pass-error d-none text-center text-danger"></div>
                        <button class="btn btn-primary button login-btn connect-btn mt-2 mb-3">
                            <i class="tkt-icon-log-out"></i> <?php echo esc_html(tkt_t('Valider')) ?>
                        </button>
                    </div>
                </div>
                <hr />
                <div>
                    <?php echo wp_kses_post(tkt_ticketidize(tkt_t("Votre TicketID se trouve sur votre abonnement"))) ?>
                </div>
            </div>
        </div>
    </div>

    <%
    jQuery(document).ready(function($) {
        $("#pass-id").keyup(function() {
            if (this.value.length == this.maxLength) {
                $('#pass-code').focus();
            }
        });
    });
    %>
</script>
