<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * BuyArticle form: pricings partial
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "article": { ... },
 *   "salepoint_id": "12345678-1234-1234-1234-123456789012"
 * }
 */
$currency = TKTApp::get_instance()->get_config('currency', 'CHF');
?>
<div class="tkt-wrapper">
    <% if (article.variants.length) { %>
    <div class="pricings-form">
        <div class="row">
            <div class="col">
                <span class="assertive">
                    <?php echo esc_html(tkt_t('Saisissez le nombre d\'éléments que vous souhaitez ajouter à votre panier :')) ?>
                </span>
            </div>
        </div>
        <% _.mapKeys(article.variants, function(variant, key) { %>
            <div class="row pricing-row <%= variant._id %> ">
                <div class="col">
                    <% if (variant.hasStockForSalepoint(salepoint_id)) { %>
                        <% if (variant.variable_price) { %>
                            <span class="tkt-badge tkt-badge-plus-minus tkt-badge-split flex-rev-on-mobile">
                                <span class="tkt-badge-part tkt-grey-badge tkt-minus-btn text-center">-</span>
                                <span class="tkt-badge-part tkt-light-badge d-flex justify-content-center text-center">
                                    <span class="variant-qty mr-1">
                                        <%= article.variants.length == 1 ? 1 : 0 %>
                                    </span>
                                    x
                                    <span class="pricing-name ml-1 mr-1">
                                        <%= variant.name.<?php echo esc_html(TKT_LANG) ?> %> :
                                    </span>
                                    <input type="number" min="0" step="1.0" autocomplete="off" style="width:100px" class="form-control pricing-price" name="variants[<%= variant._id %>][price]" data-variant-id="<%= variant._id %>" value="" />
                                </span>
                                <span class="tkt-badge-part tkt-dark-badge tkt-plus-btn text-center">+</span>
                            </span>
                            <input type="hidden" data-variant="<%= key %>" class="tkt-input variant-input" value="<%= article.variants.length == 1 ? 1 : 0 %>"/>
                        <% } else { %>
                            <span class="tkt-badge tkt-badge-plus-minus tkt-badge-split flex-rev-on-mobile">
                            <span class="tkt-badge-part tkt-grey-badge tkt-minus-btn text-center">-</span>
                            <span class="tkt-badge-part tkt-light-badge text-center">
                                <span class="variant-qty">
                                    <%= article.variants.length == 1 ? 1 : 0 %>
                                    </span>
                                    x
                                    <span class="pricing-name">
                                        <%= variant.name.<?php echo esc_html(TKT_LANG) ?> %> :
                                    </span>
                                    <span class="pricing-price">
                                        <%= variant.price.<?php echo esc_html($currency) ?> %> <?php echo esc_html($currency) ?>
                                    </span>
                                </span>
                                <span class="tkt-badge-part tkt-dark-badge tkt-plus-btn text-center">+</span>
                            </span>
                            <input type="hidden" data-variant="<%= key %>" class="tkt-input variant-input" value="<%= article.variants.length == 1 ? 1 : 0 %>"/>
                         <% } %>

                    <% } else { %>
                        <span class="tkt-badge tkt-badge-split flex-rev-on-mobile">
                            <span class="tkt-badge-part tkt-light-badge text-center out-of-stock">
                            <%= variant.name.<?php echo esc_html(TKT_LANG) ?> %> : <?php echo esc_html(tkt_t("Épuisé")) ?>
                            </span>
                        </span>
                        <div class="tkt-variant-error-msg d-none" data-variant-id="<%= variant._id %>"></div>
                    <% } %>
                </div>
            </div>
        <% }) %>
        <div class="row">
            <div class="col">
                <div class="error-panel d-none"></div>
                <button class="button add-to-cart-btn active" >
                    <?php echo esc_html(tkt_t('Ajouter à mon panier')) ?>
                </button>
            </div>
        </div>
    </div>
    <% } %>
</div>
