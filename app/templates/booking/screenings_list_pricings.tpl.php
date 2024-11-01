<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Booking form: pricings partial
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "screening": Screening instance
 * }
 */
$currency = TKTApp::get_instance()->get_config('currency', 'CHF');
?>
<% if (screening && _.keys(screening.pricings).length) { %>
<div class="pricings-form">
    <div class="error pricings-error d-none"></div>
    <button class="button book-btn active d-none my-3">
        <?php echo esc_html(tkt_t("Réserver une place sur mon abonnement")) ?>
    </button>
    <table width="100%">
    <% _.mapKeys(screening.pricings, function(p, key) { %>
    <tr class="pricing-row">
        <td>
            <span class="pricing-name"><%= p.name.<?php echo esc_html(TKT_LANG) ?> %></span>
        </td>
        <td width="60px">
            <span class="pricing-price">
                <%= p.price.<?php echo esc_html($currency) ?>.toFixed(0) %> <?php echo esc_html($currency) ?>
            </span>
        </td>
        <td width="60px">
            <span class="tkt-badge tkt-badge-split flex-rev-on-mobile tkt-badge-plus-minus">
                <span class="tkt-badge-part tkt-grey-badge tkt-minus-btn text-center">-</span>
                <span class="tkt-badge-part tkt-light-badge text-center">
                    <span class="pricing-qty">
                        0
                    </span>
                </span>
                <span class="tkt-badge-part tkt-dark-badge tkt-plus-btn text-center">+</span>
            </span>
            <input type="hidden" data-pricing="<%= key %>" class="input pricing-input" value="0"/>
        </td>
    </tr>
    <% }) %>
    </table>
    <button class="button add-to-cart-btn active my-3" >
        <?php echo esc_html(tkt_t('Ajouter au panier')) ?>
    </button>
</div>
<% } else if (screening && screening.opaque.booking_mode === 'free') { %>
<div class="pricings-form">
    <?php echo esc_html(tkt_t("Entrée libre")) ?>
</div>
<% } %>
