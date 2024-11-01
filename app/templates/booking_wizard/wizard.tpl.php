<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * Booking wizard template
 *
 * Input:
 * $data: {
 *   "ids": [ ... ],
 *   "theme": 'dark|light'
 * }
 */
$theme = isset($data->theme) ? $data->theme : 'dark';
?>
<div class="tkt-wrapper">
    <section class="tkt-section tkt-<?php echo esc_attr($theme) ?>-section book-section">
        <div class="row">
            <div class="col">
                <div
                    class="booking-wizard"
                    data-component="BookingWizard/Wizard"
                    data-redirect="<?php echo esc_attr(TKTApp::get_instance()->get_config('cart.cart_redirect', 'none')) ?>"
                    data-cart-url="<?php echo esc_attr(tkt_cart_url()) ?>"
                    data-checkout-url="<?php echo esc_attr(tkt_checkout_url()) ?>"
                    data-show-on-load="<?php echo esc_attr(tkt_get_url_param( 'book', -1 ) == 1 ? 'true' : 'false') ?>"
                    data-ids="<?php echo esc_attr(implode(',', $data->ids)) ?>"
                >
                    <div class="booking-wizard-wrapper">
                        <div class="booking-wizard-left-wrapper">
                            <div class="booking-wizard-menu-wrapper"></div>
                        </div>
                        <div class="booking-wizard-right-wrapper">
                            <div class="booking-wizard-top-navigation-wrapper"></div>
                            <div class="booking-wizard-content-wrapper"></div>
                            <div class="booking-wizard-bottom-navigation-wrapper"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Underscore.js templates used by client side -->
    <script type="text/template" id="tkt-booking-wizard-navigation-tpl">
        <?php TKTTemplate::output('booking_wizard/wizard_navigation', (object)[]) ?>
    </script>
    <script type="text/template" id="tkt-booking-wizard-menu-tpl">
        <?php TKTTemplate::output('booking_wizard/wizard_menu', (object)[]) ?>
    </script>
    <script type="text/template" id="tkt-booking-wizard-content-tpl">
        <?php TKTTemplate::output('booking_wizard/wizard_content', (object)[]) ?>
    </script>
</div>
