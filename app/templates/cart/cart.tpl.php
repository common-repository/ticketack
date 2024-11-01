<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * Cart template
 *
 * Input: {
 *   "theme"             : 'dark|light',
 *   "hidden_links"      : 'finalize,cancel,continue',
 *   "enable_promo_code" : bool
 * }
 */
?>
<div class="tkt-wrapper tkt-cart" data-component="Cart/Cart" data-hide-links="<?php echo esc_attr($data->hidden_links) ?>">
    <div data-component="Media/Loading" data-size-sm data-align-center></div>
</div>

<!-- Underscore.js template used by client side -->
<script type="text/template" id="tkt-cart-table-tpl">
    <?php TKTTemplate::output('cart/cart_table', (object)[
        'enable_promo_code' => $data->enable_promo_code,
        'theme'             => $data->theme
        ])
    ?>
</script>
