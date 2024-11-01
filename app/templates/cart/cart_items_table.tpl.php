<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Cart table template
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "cart": Cart instance,
 * }
 */
?>
<div class="tkt-wrapper">
    <section class="tkt-section tkt-light-section tkt-cart-section">
        <% if (cart.items.length == 0) { %>
        <div class="row">
            <div class="col">
                <h3 class="empty-cart-title">
                    <?php echo esc_html(tkt_t('Votre panier est vide')) ?>
                </h3>
            </div>
        </div>
        <% } else { %>
            <% _.each(cart.mergedItems, function(item) { %>
            <div class="cart-item-row">
                <div class="row">
                    <div class="col-12 col-sm-4 col-md-3 cart-poster">
                        <img class="img-fluid poster" src="<%= item.getFormattedPoster() %>">
                    </div>
                    <div class="col-12 col-sm-8 col-md-9 cart-infos">
                        <span class="tkt-remove-cart-item" data-item="<%= item.id %>">
                            X
                        </span>
                        <div class="cart-title">
                            <%= item.getFormattedTitle() %>
                        </div>
                        <div class="cart-price">
                            <div>
                                <p>Quantité</p>
                                <input class="cart-number" type="" name="quantity" value="<%= item.quantity %>" disabled>
                            </div>
                            <p class="price-cell"><%= item.getFormattedPrice() %></p>
                        </div>
                    </div>
                </div>
            </div>
            <% }); %>
        <% } %>
    </section>
</div>
