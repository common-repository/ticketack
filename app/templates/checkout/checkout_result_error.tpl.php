<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Checkout error result template
 * This template will be parsed by underscore.js
 *
 * Input: {}
 */
?>
<div class="tkt-wrapper">
  <div class="tkt-checkout-result checkout-result-error text-center alert alert-danger">
    <?php echo esc_html(tkt_t("Une erreur est survenue lors de votre commande.")) ?><br/>
    <?php echo esc_html(tkt_t("Merci de bien vouloir réessayer.")) ?>
  </div>
</div>

