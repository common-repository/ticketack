<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * Checkout template
 *
 * Input:
 * $data: {
 *   "theme"                 : 'dark|light',
 *   "cgv_url"               : "https://...",
 *   "privacy_url"           : "https://...",
 *   "sanitary_measures_url" : "https://...",
 *   "requested_fields"      : ["firstname", "lastname", ... ],
 *   "required_fields"       : ["firstname", "lastname", ... ],
 *   "allow_later"           : bool,
 *   "allow_proxypay"        : bool,
 *   "allow_null_payment"    : bool,
 *   "proxypay_config_error" : "str"
 * }
 */
?>

<div
    class="tkt-wrapper tkt-checkout"
    data-component="Checkout/Checkout"
    data-redirect="<?php echo esc_attr(tkt_thank_you_url()) ?>"
>
    <div class="tkt-checkout-form">
        <?php TKTTemplate::output('checkout/checkout_form', (object)[
            'theme'                 => $data->theme,
            'cgv_url'               => $data->cgv_url,
            'privacy_url'           => $data->privacy_policy_url,
            'sanitary_measures_url' => $data->sanitary_measures_url,
            'requested_fields'      => $data->requested_fields,
            'required_fields'       => $data->required_fields,
            'allow_later'           => $data->allow_later,
            'allow_proxypay'        => $data->allow_proxypay,
            'allow_null_payment'    => $data->allow_null_payment
        ]) ?>
    </div>
</div>


<!-- Underscore.js templates used by client side -->
<script type="text/template" id="tkt-checkout-result-ok-tpl">
    <?php TKTTemplate::output('checkout/checkout_result_ok', (object)[]) ?>
</script>
<script type="text/template" id="tkt-checkout-result-error-tpl">
    <?php TKTTemplate::output('checkout/checkout_result_error', (object)[]) ?>
</script>
