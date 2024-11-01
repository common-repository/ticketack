<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Booking form: success message partial
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "cart_url": String,
 *   "program_url": String
 * }
 */
?>
<div class="tkt-wrapper">
    <div class="row">
        <div class="col">
            <h3 class="success-title">
                <?php echo esc_html(tkt_t('Merci, votre commande est réservée dans votre panier pour 30 minutes.')) ?>
            </h3>
            <div class="float-right text-right">
                <a href="<%= cart_url %>" class="button show-cart-btn active" >
                    <?php echo esc_html(tkt_t('Accéder au panier')) ?>
                </a>
                <div class="continue-shopping" >
                    <a href="<%= program_url %>"><?php echo esc_html(tkt_t('Continuer')) ?></a> <?php echo esc_html(tkt_t('ma visite')) ?>
                </div>
            </div>
        </div>
    </div>
</div>
