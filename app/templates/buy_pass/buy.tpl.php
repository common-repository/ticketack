<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * Buy pass template
 *
 * Input:
 * $data: {
 *   "tickettypes": [ ... ],
 *   "selected": "festival_pass",
 *   "theme"   : "dark|light",
 * }
 */
?>
<div
  class="tkt-wrapper"
  data-component="Pass/BuyForm"
  data-redirect="<?php echo esc_attr(TKTApp::get_instance()->get_config('cart.cart_redirect', 'none')) ?>"
  data-cart-url="<?php echo esc_attr(tkt_cart_url()) ?>"
  data-checkout-url="<?php echo esc_attr(tkt_checkout_url()) ?>">
  <div class="row">
    <div class="col">
      <?php TKTTemplate::output('buy_pass/pass_list', $data) ?>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <?php TKTTemplate::output('buy_pass/form', $data) ?>
    </div>
  </div>
</div>
