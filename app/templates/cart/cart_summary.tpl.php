<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * Cart summary template
 *
* Input: {
 *   "theme"             : 'dark|light',
 *   "enable_promo_code" : bool
 * }
 */
?>
<div class="tkt-wrapper tkt-cart-summary" data-component="Cart/CartSummary">
    <div data-component="Media/Loading" data-size-sm data-align-center></div>
</div>

<!-- Underscore.js template used by client side -->
<script type="text/template" id="tkt-cart-summary-table-tpl">
    <?php TKTTemplate::output('cart/cart_summary_table', (object)[
        'enable_promo_code' => $data->enable_promo_code,
        'theme'             => $data->theme
        ])
    ?>
</script>
