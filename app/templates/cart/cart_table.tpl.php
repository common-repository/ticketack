<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Cart table template
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "cart"              : Cart instance,
 *   "ticket"            : Ticket instance, if the user is connected,
 *   "program_url"       : String,
 *   "cart_reset_url"    : String,
 *   "checkout_url"      : String,
 *   "enable_promo_code" : bool
 *   "hide_links"        : ['finalize', 'cancel', 'continue']
 *   FIXME: wallet missing here?
 * }
 */
$theme = $data->theme;
?>
<%
const pass     = cart.getPass();
const tickets  = cart.getTickets();
const articles = cart.getArticles();

const discounts  = cart.getDiscounts();
const fees       = cart.getFees();
const nbArticles = pass.length + tickets.length + articles.length;
%>

<div class="tkt-wrapper">
        <% if (cart.items.length == 0) { %>
        <section class="tkt-section tkt-<?php echo esc_attr($theme) ?>-section tkt-cart-section">
            <div class="row">
                <div class="col">
                    <h3 class="empty-cart-title">
                        <?php echo esc_html(tkt_t('Votre panier est vide')) ?>
                    </h3>
                </div>
            </div>
        </section>
        <% } else { %>
        <div class="row">
            <div class="col-12 col-lg-8">
                <section class="tkt-section tkt-<?php echo esc_attr($theme) ?>-section tkt-cart-section h-100">
                    <div class="row">
                        <div class="col">
                            <h3 class="tkt-section-title mb-3"><?php echo esc_html(tkt_t("Votre commande")) ?></h3>
                            <!-- Table screening -->
                            <% if (tickets.length) { %>
                            <table class="tkt-cart-table">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo esc_html(tkt_t('Tickets')) ?></th>
                                        <th scope="col" width="100px"><?php echo esc_html(tkt_t('Prix')) ?></th>
                                        <th scope="col" width="20px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <% _.each(tickets, function(item) { %>
                                    <tr>
                                        <td class="title-cell"><%= item.getFormattedTitle() %></td>
                                        <td class="price-cell text-right"><%= item.getFormattedPrice() %></td>
                                        <td class="action-cell text-right">
                                            <span class="tkt-remove-cart-item" data-item="<%= item.id %>">
                                                <i class="tkt-icon-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                    <% }); %>
                                </tbody>
                            </table>
                            <% }; %>
                            <!-- Table articles -->
                            <% if (articles.length) { %>
                            <table class="tkt-cart-table">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo esc_html(tkt_t('Articles')) ?></th>
                                        <th scope="col" width="100px"></th>
                                        <th scope="col" width="20px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <% _.each(articles, function(item) { %>
                                    <tr>
                                        <td class="title-cell"><%= item.getFormattedTitle() %></td>
                                        <td class="price-cell text-right"><%= item.getFormattedPrice() %></td>
                                        <td class="action-cell text-right">
                                            <span class="tkt-remove-cart-item" data-item="<%= item.id %>">
                                                <i class="tkt-icon-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                    <% }); %>
                                </tbody>
                            </table>
                            <% }; %>
                            <!-- Table pass -->
                            <% if (pass.length) { %>
                            <table class="tkt-cart-table">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo esc_html(tkt_t('Abonnements')) ?></th>
                                        <th scope="col" width="100px"></th>
                                        <th scope="col" width="20px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <% _.each(pass, function(item) { %>
                                    <tr>
                                        <td class="title-cell"><%= item.getFormattedTitle() %></td>
                                        <td class="price-cell text-right"><%= item.getFormattedPrice() %></td>
                                        <td class="action-cell text-right">
                                            <span class="tkt-remove-cart-item" data-item="<%= item.id %>">
                                                <i class="tkt-icon-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                    <% }); %>
                                </tbody>
                            </table>
                            <% }; %>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-12 col-lg-4">
                <section class="tkt-section tkt-<?php echo esc_attr($theme) ?>-section tkt-cart-section  h-100">
                    <div class="row">
                        <div class="col">
                            <h3 class="tkt-section-title mb-3"><?php echo esc_html(tkt_t("Récapitulatif")) ?></h3>
                            <!-- Table article -->
                            <table class="tkt-cart-table">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo esc_html(tkt_t("Commande")) ?></th>
                                        <th scope="col" width="100px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="title-cell">
                                            <%= nbArticles %> <%= nbArticles === 1 ? "<?php echo esc_html(tkt_t("produit")) ?>" : "<?php echo esc_html(tkt_t("produits")) ?>" %>
                                        </td>
                                        <td class="price-cell text-right"><%= cart.getOrderTotal() %></td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Table promo & discount code -->
                            <% if (discounts.length) { %>
                            <table class="tkt-cart-table">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo esc_html(tkt_t('Rabais')) ?></th>
                                        <th scope="col" width="100px"></th>
                                        <th scope="col" width="20px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <% _.each(discounts, function(item) { %>
                                    <tr>
                                        <% if(item.type != "shipping" && (item.article?.type == "code_discount" || item.article?.type == "manual_discount") ) { %>
                                        <td class="title-cell"><%= item.getFormattedTitle() %></td>
                                        <td class="price-cell text-right"><%= item.getFormattedPrice() %></td>
                                        <td class="action-cell text-right">
                                            <span class="tkt-remove-cart-item" data-item="<%= item.id %>">
                                                <i class="tkt-icon-trash"></i>
                                            </span>
                                        </td>
                                        <% } %>
                                    </tr>
                                    <% }); %>
                                </tbody>
                            </table>
                            <% }; %>
                            <!-- Table fees -->
                            <table class="tkt-cart-table">
                                <% if (fees.length) { %>
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo esc_html(tkt_t('Frais')) ?></th>
                                        <th scope="col" width="100px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <% _.each(fees, function(item) { %>
                                    <tr>
                                        <% if(item.type == "shipping" || item.type == "paymentfee") { %>
                                        <td class="title-cell"><%= item.getFormattedTitle() %></td>
                                        <td class="price-cell text-right"><%= item.getFormattedPrice() %></td>
                                        <% } %>
                                    </tr>
                                    <% }); %>
                                </tbody>
                                <% }; %>
                                <tfoot>
                                    <tr>
                                        <td colspan="2">
                                            <span class="total-title-cell">
                                                <?php echo esc_html(tkt_t('Total :')) ?>
                                            </span>
                                            <span class="total-price-cell">
                                                <%= cart.getFormattedTotal() %>
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <% if (cart.getTotal() > 0 && ticket && ticket.getWalletBalance() > 0 && !hide_links.includes('wallet')) { %>
                    <hr/>
                    <div class="row justify-content-md-end">
                        <div class="col col-12 use-wallet-wrapper">
                            <h4>
                                <?php echo esc_html(tkt_t('Vous disposez de')) ?>
                                <%= ticket.getFormattedWalletBalance() %>
                                <?php echo esc_html(tkt_t('sur votre portefeuille électronique')) ?>
                            </h4>
                            <span><?php echo esc_html(tkt_t('Saisissez ci-dessous le montant que vous souhaitez utiliser')) ?></span>
                            <div class="input-group mb-2">
                                <input type="number" min="0" max="<%= Math.min(ticket.getWalletBalance(), cart.getTotal()) %>" class="wallet-input form-control" placeholder="15.50" value="<%= Math.min(ticket.getWalletBalance(), cart.getTotal()) %>"/>
                                <div class="input-group-append">
                                    <a href="javascript:;" class="wallet-button button active">
                                        <?php echo esc_html(tkt_t('Utiliser mon portefeuille')) ?>
                                    </a>
                                </div>
                            </div>
                            <div class="alert alert-danger wallet-error d-none"></div>
                            <div class="alert alert-success wallet-success d-none"></div>
                        </div>
                    </div>
                    <% } %>

                    <?php if ($data->enable_promo_code) : ?>
                        <hr/>
                        <div class="row justify-content-md-end">
                            <div class="col col-12 use-promo-code-wrapper">
                                <div class="input-group">
                                    <input type="text" class="promo-code-input form-control" placeholder="<?php echo esc_html(tkt_t('Code promo')) ?>" />
                                    <div class="input-group-append">
                                        <a href="javascript:;" class="promo-code-button button active">
                                            <?php echo esc_html(tkt_t('Utiliser')) ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="alert alert-danger promo-code-error d-none"></div>
                                <div class="alert alert-success promo-code-success d-none"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <hr/>
                    <div class="row justify-content-md-end">
                        <% if (!hide_links.includes('finalize')) { %>
                        <div class="col col-12 finish-cart-wrapper">
                            <a href="<%= checkout_url %>" class="button active">
                                <?php echo esc_html(tkt_t('Finaliser ma commande')) ?>
                            </a>
                        </div>
                        <% } %>
                    </div>

                    <% if (!hide_links.includes('cancel')) { %>
                    <div class="row">
                        <div class="col cancel-order-wrapper">
                            <div class="cancel-order" >
                                <a href="" class="tkt-reset-cart-btn"><?php echo esc_html(tkt_t('Annuler ma commande')) ?></a>
                            </div>
                        </div>
                    </div>
                    <% } %>

                    <% if (!hide_links.includes('continue')) { %>
                    <div class="row">
                        <div class="col continue-shopping-wrapper">
                            <div class="continue-shopping" >
                                <a href="<%= program_url %>"><?php echo esc_html(tkt_t('Continuer mes réservations')) ?></a>
                            </div>
                        </div>
                    </div>
                    <% } %>
                </section>
            </div>
        </div><!-- end row -->
    <% } %>
</div>
