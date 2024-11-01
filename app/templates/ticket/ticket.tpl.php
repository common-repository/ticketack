<?php

if (!defined('ABSPATH')) exit;

/**
 * Ticket view
 * This template will be parsed by underscore.js
 *
 * JS Input: {
 *   "ticket": Ticket instance, if the ticket is connected,
 *   "tickets": Array of tickets instances
 *   "program_url": String,
 *   "votesConfig": { ... },
 * }
 */
?>
<script type="text/template" id="tkt-ticket-tpl">
    <%
    const errors = {
        'screening.start_at': <?php echo wp_kses_post(wp_json_encode(tkt_t('Les votes seront possibles dès le début de la séance'))) ?>,
        'screening.stop_at': <?php echo wp_kses_post(wp_json_encode(tkt_t('Les votes seront possibles dès la fin de la séance'))) ?>,
        'needs_scan': <?php echo wp_kses_post(wp_json_encode(tkt_t('Vous ne pouvez pas voter car votre billet n\'a pas été contrôlé'))) ?>,
        'not_before': <?php echo wp_kses_post(wp_json_encode(tkt_t('Les votes ne sont pas encore possibles pour cette séance'))) ?>,
        'not_after': <?php echo wp_kses_post(wp_json_encode(tkt_t('Les votes ne sont pas plus possibles pour cette séance'))) ?>,
    };
    %>
    <% if (tickets?.length > 1) { %>
        <div class="mb-3">
            <ul class="nav nav-tabs nav-fill">
                <% _.sortBy(tickets, t => t.getDisplayName()).map(function (t) {  %>
                    <li class="nav-item">
                        <a data-ticket-id="<%= t._id %>"class="nav-link ticket-link <%= t._id === ticket?._id ? 'active' : '' %>">
                            <%= t.getTypeName() %> -
                            <%= t.getDisplayName() %>
                        </a>
                    </li>
                <% }) %>
            </ul>
        </div>
    <% } %>

    <% if (ticket) { %>

    <%
        const pastBookings = ticket.bookings?.filter(b => b.screening?.isFinished());
        const futureBookings = ticket.bookings?.filter(b => !b.screening?.isFinished());
    %>
    <div class="tkt-ticket-view">
        <p class="alert alert-info small"><span class="glyphicon glyphicon-info-sign"></span><?php echo esc_html(tkt_t("Nous n'émettons pas de billet individuel pour les réservations, votre ticket actuel vous sert de titre d'entrée aux séances réservées.")) ?></span></p>

        <!-- Title -->
        <div class="mb-3">
            <h3>
                <strong>
                    <%= ticket.getTypeName() %> -
                    <%= ticket.getDisplayName() %>
                </strong>
            </h3>
        </div>

        <!-- Réservations -->
        <section class="tkt-section tkt-light-section">
            <% if (!pastBookings.length && !futureBookings.length) { %>
                <h3><?php echo esc_html(tkt_t('Réservations')) ?></h3>
                <div class="mb-2">
                    <?php echo esc_html(tkt_t("Il n'y a actuellement aucune réservation sur ce billet.")) ?>
                </div>
            <% } else { %>
                <% if (futureBookings) { %>
                    <h3><?php echo esc_html(tkt_t('Réservations')) ?></h3>
                    <table class="table table-striped table-hover no-more-tables">
                        <thead>
                            <tr>
                                <th><?php echo esc_html(tkt_t('Date')) ?></th>
                                <th><?php echo esc_html(tkt_t('Réservation')) ?></th>
                                <th><?php echo esc_html(tkt_t('Lieu')) ?></th>
                                <th><i class="tkt-icon-smartphone"></i></th>
                                <th class="text-right"><?php echo esc_html(tkt_t('Action')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <% futureBookings.map(function(b) { %>
                            <tr>
                                <td><%= b.screening_start_at.format("DD.MM.YYYY HH[h]mm") %> -
                                    <%= b.screening_stop_at.format("HH[h]mm") %></td>
                                <td><%= b.screening.getTitle() %></td>
                                <td><%= b.screening.cinema_hall.name %></td>
                                <td class="text-center">
                                    <% if (b.scanned_at.length) { %>
                                        <i class="tkt-icon-checkmark"></i>
                                    <% } %>
                                </td>
                                <td>
                                    <div class="flex-col items-end">
                                        <% if (b.isCancelable) { %>
                                            <a href="#" class="btn btn-danger btn-bloc cancel-booking-btn" data-booking-id="<%= b._id %>">
                                                <?php echo esc_html(tkt_t("Annuler")) ?>
                                            </a>
                                        <% } %>
                                        <% if (b.vote || !b.screening.opaque?.disable_votes) { %>
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
                                        <% } %>
                                    </div>
                                </td>
                            </tr>
                            <% }) %>
                        <tbody>
                    </table>
                    <div class="cancelable_booking_err text-danger text-center"></div>
                <% } %>

                <% if (pastBookings.length) { %>
                    <h3><?php echo esc_html(tkt_t('Réservations passées')) ?></h3>
                    <table class="table table-striped table-hover no-more-tables">
                        <thead>
                            <tr>
                                <th><?php echo esc_html(tkt_t('Date')) ?></th>
                                <th><?php echo esc_html(tkt_t('Réservation')) ?></th>
                                <th><?php echo esc_html(tkt_t('Lieu')) ?></th>
                                <th><i class="tkt-icon-smartphone"></i></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <% pastBookings.map(function(b) { %>
                            <tr>
                                <td><%= b.screening_start_at.format("DD.MM.YYYY HH[h]mm") %> -
                                    <%= b.screening_stop_at.format("HH[h]mm") %></td>
                                <td><%= b.screening.getTitle() %></td>
                                <td><%= b.screening.cinema_hall.name %></td>
                                <td class="text-center">
                                    <% if (b.scanned_at.length) { %>
                                        <i class="tkt-icon-checkmark"></i>
                                    <% } %>
                                </td>
                                <td>
                                    <% if (b.vote || !b.screening.opaque?.disable_votes) { %>
                                        <% const { votable, reason } = b.isVotable(); %>
                                        <div
                                            data-component="Ui/Rating"
                                            data-score="<%= b.vote?.score || 0 %>"
                                            data-ticket-id="<%= b.ticket_id %>"
                                            data-booking-id="<%= b._id %>"
                                            data-size="24"
                                            data-step="<%= votesConfig?.step %>"
                                            data-max="<%= votesConfig.max_score %>"
                                            data-disabled-reason="<%= reason ? errors[reason] : '' %>"
                                        ></div>
                                        <% if (reason) { %>
                                            <small>
                                                <i class="tkt_icon_warning" />
                                                <%= errors[reason] %>
                                            </small>
                                        <% } %>
                                    <% } %>
                                </td>
                            </tr>
                            <% }) %>
                        <tbody>
                    </table>
                    <div class="cancelable_booking_err text-danger text-center"></div>
                <% } %>
            <% } %>
            <a class="btn button w-100" href="<%= program_url %>">
                <?php echo esc_html(tkt_t("Réserver des séances")) ?>
            </a>
        </section>

        <!-- Owner -->
        <% if (ticket?.hasContactInfo()) { %>
        <section class="tkt-section tkt-<?php echo esc_attr($theme) ?>-section mt-3">
            <h3 class="panel-title"><?php echo esc_html(tkt_t('Titulaire')) ?></h3>
            <div class="text-center">
                <% if (ticket.contact?.rfc2397_portrait?.length) { %>
                    <img class="img-responsive img-thumbnail" src="<%= ticket.contact?.rfc2397_portrait %>" />
                    <br />
                <% } %>

                <% if (ticket.contact?.firstname || ticket.contact?.lastname) { %>
                <h5>
                    <%= [ticket.contact.firstname, ticket.contact.lastname].filter(v => !!v).join(' ') %>
                </h5>
                <% } %>

                <div>
                    <% if (ticket.contact?.birthdate) { %>
                        <%= new Date(ticket.contact.birthdate).toLocaleDateString() %>
                        <br />
                    <% } %>

                    <% if (ticket.contact?.address?.street) { %>
                        <%= ticket.contact.address.street %>
                        <br />
                    <% } %>

                    <% if (ticket.contact?.address?.zip || ticket.contact?.address?.city) { %>
                        <%= [ticket.contact.address.zip, ticket.contact.address.city].filter(v => !!v).join(' ') %>
                        <br />
                    <% } %>

                    <% if (ticket.contact?.address?.country) { %>
                        <%= ticket.contact.address.country %>
                        <br />
                    <% } %>

                    <% if (ticket.contact?.email) { %>
                        <%= ticket.contact.email %>
                    <% } %>
                </div>
            </div>
        </section>
        <% } %>

        <!-- Guests -->
        <% if (ticket?.hasGuestsInfo()) { %>
        <section class="tkt-section tkt-<?php echo esc_attr($theme) ?>-section mt-3">
            <h3 class="panel-title"><?php echo esc_html(tkt_t('Invités')) ?></h3>
            <div class="text-center">
                <% ticket.getGuestsInfo().forEach(guest => { %>
                    <h5>
                        <%= [guest.firstname, guest.lastname].filter(v => !!v).join(' ') %><br />
                        <small><%= guest.email %></small>
                    </h5>
                <% }); %>
            </div>
        </section>
        <% } %>

        <% if (ticket.getWalletBalance() > 0) { %>
        <div class="row">
            <div class="col">
                <section class="tkt-section tkt-light-section mt-3">
                    <div class="panel-heading wallet_info">
                        <h3 class="panel-title">Portefeuille électronique</h3>
                    </div>
                    <div class="panel-body wallet_info text-center">
                        <div class="text-center">
                            <h4><%= ticket.getFormattedWalletBalance() %></h4>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <% } %>

        <div class="row">
            <div class="col">
                <section class="tkt-section tkt-light-section mt-3">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo esc_html(tkt_t('Validité')) ?></h3>
                        <div class="panel-body">
                            <div class="well text-center">
                                <h5>
                                <?php echo esc_html(sprintf(tkt_t('Activé le %s'), '<%= ticket.activated_at.format("LL") %>')) ?>
                                </h5>
                                <p><?php echo esc_html(tkt_t('Tarif :')) ?> <%= ticket.activated_pricing.name.<?php echo esc_html(TKT_LANG) ?> %>
                                    (<%= ticket.getFormattedPriceAndCurrency() %>)
                                </p>
                            </div>

                            <% if (ticket.hasExpired()) { %>
                                <div class="text-center alert alert-danger">
                                    <b>A expiré le <%= ticket.getExpirationDate().format('LL') %> </b>
                                </div>
                            <% } else {%>
                                <div class="text-center alert alert-success">
                                    <b><?php echo esc_html(sprintf(tkt_t("Valable jusqu'au %s"), "<%= ticket.getExpirationDate().format('LL') %>")) ?></b>
                                    <p>
                                        <%= ticket.placesAvailable() %>
                                    </p>
                                </div>
                            <% } %>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <% if (ticket.isForgettable) { %>
        <div class="row mt-3">
            <div class="col">
                <button class="btn button forget-ticket-btn w-100">
                    <i class="tkt-icon-log-out"></i> <?php echo esc_html(tkt_t('Oublier ce ticket')) ?>
                </button>
            </div>
        </div>
        <% } %>
    </div>
    <% } %>
</script>
