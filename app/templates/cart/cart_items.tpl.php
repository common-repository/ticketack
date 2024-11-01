<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * Cart items template
 *
 * Input: {
 * }
 */
?>
<div class="tkt-wrapper tkt-cart-items" data-component="Cart/CartItems">
    <div data-component="Media/Loading" data-size-sm data-align-center></div>
</div>

<!-- Underscore.js template used by client side -->
<script type="text/template" id="tkt-cart-items-table-tpl">
    <?php TKTTemplate::output('cart/cart_items_table', (object)[]) ?>
</script>
