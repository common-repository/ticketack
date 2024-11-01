<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Booking form: pricings partial
 * This template will be parsed by underscore.js
 *
 * JS Input: {
 *   "bookability": An object with the following keys :
 *       - availabilities: object
 *       - cannot_book_explanation: string
 *       - eligible_types: array
 *       - screening_already_booked: boolean
 *       - screening_exists: boolean
 *       - screening_seats: boolean
 *       - ticket_can_book_screening: boolean
 *       - ticket_cannot_book_explanation: string
 *       - ticket_logged_in: boolean
 *   "screening": Screening instance,
 *   "show_pricings": A boolean indicating if we must show the pricings form
 *   "show_ticket_id": A boolean indicating if we must show the ticket connection form
 *   "account_tickets": An array of tickets coming from the connected user account, if any
 *   "connected_tickets": An array of tickets added with their ticketID
 * }
 */

$currency = TKTApp::get_instance()->get_config('currency', 'CHF');
?>

<% const availabilities = bookability?.availabilities; %>
<% const eligible_types = (bookability?.eligible_types || screening?.eligible_types || []).filter(t => t._id != 'one-time-pass'); %>
<% const sellable_types = eligible_types.filter(t => !!t.sellable); %>

<div class="tkt-wrapper">
    <% if (_.keys(screening.pricings).length) { %>
        <% if ('map_only_bookings' in screening.opaque && screening.opaque.map_only_bookings) { %>
            <div class="row">
                <div class="col">
                    <iframe width="100%" style="min-height: 500px" frameborder="0" src="<%= TKTApi.getScreeningMapUrl(screening._id) %>"></iframe>
                </div>
            </div>
        <% } else if (show_pricings) { %>
            <% if (!availabilities || availabilities.me['right-now']['one-time-pass'] > 0) { %>
                <h4><?php echo esc_html(tkt_t("Acheter des places")) ?></h3>
                <div class="pricings-form">
                    <div class="row">
                        <div class="col">
                            <span>
                                <?php echo esc_html(tkt_t('Saisissez le nombre de place(s) que vous souhaitez ajouter à votre panier :')) ?>
                            </span>
                        </div>
                    </div>
                    <% _.mapKeys(screening.pricings, function(p, key) { %>
                    <div class="row pricing-row" data-pricing-wrapper="<%= key %>">
                        <div class="col">
                            <span class="tkt-badge tkt-badge-split flex-rev-on-mobile tkt-badge-plus-minus">
                                <span class="tkt-badge-part tkt-grey-badge tkt-minus-btn text-center">-</span>
                                <span class="tkt-badge-part tkt-light-badge text-center">
                                    <span class="pricing-qty">
                                        0
                                    </span>
                                    x
                                    <span class="pricing-name">
                                        <%= p.name.<?php echo esc_html(TKT_LANG) ?> %> :
                                        <% if (p.description.<?php echo esc_html(TKT_LANG) ?>) { %>
                                            <i class="tkt-icon-info" data-component="Ui/Tippy" data-tippy-content="<%= p.description.<?php echo esc_html(TKT_LANG) ?> %>"></i>
                                        <% } %>
                                    </span>
                                    <span class="pricing-price">
                                        <%= p.price.<?php echo esc_html($currency) ?>.toFixed(2) %> <?php echo esc_html($currency) ?>
                                    </span>
                                </span>
                                <span class="tkt-badge-part tkt-dark-badge tkt-plus-btn text-center">+</span>
                            </span>
                            <input type="hidden" data-pricing="<%= key %>" class="input pricing-input" value="0"/>
                        </div>
                    </div>
                    <% }) %>
                    <div class="row">
                        <div class="col">
                            <div class="error pricings-error d-none"></div>
                            <button class="button add-to-cart-btn active" >
                                <?php echo esc_html(tkt_t('Ajouter à mon panier')) ?>
                            </button>
                        </div>
                    </div>
                </div>
            <% } else { %>
                <h5 class="no-one-time-pass-message">
                    <% if (availabilities && availabilities['me']['from-now']['one-time-pass'] > 0) { %>
                        <?php echo esc_html(tkt_t("Il n'est pas encore possible d'acheter des places pour cette séance.")) ?>
                    <% } else if (availabilities && availabilities['me']['absolute']['one-time-pass'] > 0) { %>
                        <?php echo esc_html(tkt_t("Il n'est plus possible d'acheter des places pour cette séance.")) ?>
                    <% } else { %>
                        <?php echo esc_html(tkt_t("Il n'est pas possible d'acheter des places pour cette séance.")) ?>
                    <% } %>
                </h5>
                <% if (availabilities && availabilities['on-site']['right-now']['one-time-pass'] > 0) { %>
                    <h5 class="no-one-time-pass-message">
                        <?php echo esc_html(tkt_t("Des places sont néanmoins disponibles aux caisses.")) ?>
                    </h5>
                <% } else if (availabilities && availabilities['on-site']['from-now']['one-time-pass'] > 0) { %>
                    <h5 class="no-one-time-pass-message">
                        <?php echo esc_html(tkt_t("Des places seront néanmoins disponibles aux caisses.")) ?>
                    </h5>
                <% } %>
            <% } %>
        <% } %>
        <br/>
    <% } %>

    <% if (show_ticket_id && screening.opaque && (!('map_only_bookings' in screening.opaque && screening.opaque.map_only_bookings)) && eligible_types.length) { %>
    <h4><?php echo esc_html(tkt_t("Réserver des places sur vos billets")) ?></h3>

    <div class="book-panel">
        <%
            account_tickets.filter(ticket => !ticket.isOneTimePass() && ticket.canBook(screening)).concat(
                connected_tickets.filter(ticket => !ticket.isOneTimePass()),
                account_tickets.filter(ticket => !ticket.isOneTimePass() && !ticket.canBook(screening)),
            ).map(ticket => {
        %>
        <div class="ticket-wrapper" data-ticket-id="<%= ticket._id %>">
            <div class="row">
                <div class="col col-auto ticket-infos">
                    <a class="btn btn-outline btn-sm text-left" target="_blank" href="<%= ticket.getTicketViewUrl() %>">
                        <i class="tkt-icon-tag"></i>
                        <b><%= ticket.getTypeName() %></b>
                        - <%= ticket.getDisplayName() %>
                    </a>
                    <% if (ticket.hasBooked(screening)) { %>
                    <div class="already-booked">
                        <small data-ticket-id="<%= ticket._id %>">
                            <b><?php echo esc_html(tkt_t("Réservation pour cette séance avec ce billet :")) ?> <%= ticket.getBookingsFor(screening)?.length || 0 %></b>
                        </small>
                    </div>
                    <% } %>
                </div>
                <div class="col text-right book-btns">

                    <% if (ticket.canBook(screening)) { %>
                    <button data-ticket-id="<%= ticket._id %>" class="button book-btn active <%= ticket.canBook(screening) && !ticket.hasBooked(screening) ? '' : 'd-none' %>" >
                        <i class="tkt-icon-plus"></i>
                        <?php echo esc_html(tkt_t('Réserver une place sur ce billet')) ?>
                    </button>
                    <button data-ticket-id="<%= ticket._id %>" class="button book-btn-more active <%= ticket.canBook(screening) && ticket.hasBooked(screening) ? '' : 'd-none' %>" >
                        <i class="tkt-icon-plus"></i>
                        <?php echo esc_html(tkt_t('Réserver une place de plus sur ce billet')) ?>
                    </button>
                    <% } else { %>
                    <small data-ticket-id="<%= ticket._id %>" class="cannot-book-explanation">
                        <% if (ticket.hasBooked(screening)) { %>
                            <i><?php echo esc_html(tkt_t("Vous ne pouvez pas réserver plus de place pour cette séance avec ce billet.")) ?></i>
                        <% } else { %>
                            <i><?php echo esc_html(tkt_t("Vous ne pouvez pas réserver de place pour cette séance avec ce billet.")) ?></i>
                        <% } %>
                    </small>
                    <% } %>
                </div>
            </div>
            <div class="row">
                <div class="col text-right">
                    <small class="success book-form-success d-none text-success pl-2">
                        <?php echo esc_html(tkt_t('Votre place a bien été réservée.')) ?>
                    </small>
                </div>
            </div>
            <div class="row">
                <div class="col text-right">
                    <small class="book-form-error d-none text-danger pl-2">
                    </small>
                </div>
            </div>
        </div>
        <% }) %>
    </div>

    <div class="pass-form mt-3">
        <div class="connect-panel">
            <div class="ticket_connect">
                <div class="row">
                    <div class="col">
                        <span class="pass-title">
                            <?php echo wp_kses_post(tkt_ticketidize(tkt_t("Vous avez un TicketID ?"))) ?>

                            <a href="" class="show-connect-panel-form"><?php echo esc_html(tkt_t('Ajoutez-le')) ?></a> <?php echo esc_html(tkt_t('pour réserver.')) ?><br/>
                    </span>
                    </div>
                </div>
                <div class="connect-panel-form d-none">
                    <div class="col">
                        <div class="row mt-5 input-pass">
                            <input id="pass-id" type="number" class="tkt-input input-invert form-control text-center pass-number-input" placeholder="123456" maxlength="6"/>
                            <p class="minus">-</p>
                            <input id="pass-code" type="password" class="input input-invert text-center pass-key-input" placeholder="abcde" maxlength="6"/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col text-center">
                            <div class="error pass-error d-none text-center text-danger"></div>
                            <button class="btn btn-primary button login-btn connect-btn mt-5 mb-3">
                                <i class="tkt-icon-sign-in-alt"></i> <?php echo esc_html(tkt_t('Connexion')) ?>
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <?php echo wp_kses_post(tkt_ticketidize(tkt_t("Votre TicketID se trouve sur votre abonnement"))) ?>
                    </div>
                    <br>
                </div>
            </div>
            <?php if (TKTApp::get_instance()->get_config('pages.pass')) : ?>
            <% if (sellable_types.length > 0) { %>
            <div class="row">
                <div class="col mt-3">
                    <div class="buy-pass-link">
                        <span><?php echo esc_html(tkt_t("Acheter un abonnement")) ?></span>
                        <ul class="eligible-types-list">
                        <% sellable_types.map(function (t) { %>
                        <li class="eligible-type">
                            <span class="type-name">
                                <i class="tkt-icon-ticket"></i>
                                <a href="<?php echo esc_attr(tkt_buy_pass_url()) ?>?selected=<%= t._id %>" class="type-link">
                                    <%= t.name.<?php echo esc_html(TKT_LANG) ?> %>
                                </a>
                            </span>
                        </li>
                        <% }); %>
                        </ul>
                    </div>
                </div>
            </div>
            <% } %>
            <?php endif; ?>
        </div>
    </div>
    <% } %>

</div>
