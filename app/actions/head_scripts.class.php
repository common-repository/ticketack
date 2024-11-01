<?php
namespace Ticketack\WP\Actions;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Helpers\LocalesHelper;
use Ticketack\Core\Models\Tickettype;
use Ticketack\Core\Models\Settings;

/**
 * Head Scripts action
 */
class HeadScriptsAction extends TKTAction
{
    /**
     * Get this action tag(s)
     *
     * @return mixed: A single tag (which will call the <code>run</code> method)
     *                or an associative array with the tag as key and the method to call as value.
     */
    public function get_tag()
    {
        return "wp_footer";
    }

    /**
     * Get this action priority
     *
     * @return int: Action priority
     */
    public function get_priority()
    {
        $load_underscore_at_the_end = (bool)TKTApp::get_instance()->get_config('advanced.load_underscore_at_the_end', false);
        return $load_underscore_at_the_end ? -100000 : 100000;
    }

    /**
     * Run this action
     */
    public function run()
    {
        $app = TKTApp::get_instance();
        $otp = Tickettype::find(Tickettype::ONE_TIME_PASS_ID);
        $salepoint_id = $app->get_config('ticketack.salepoint_id');

        echo '
        <script>
            if (typeof jQuery === "function") {
                /* Wordpress version of jQuery doesn\'t expose the $ global object */
                window.$ = jQuery;
            }
            // moment locale must be injected globally because it\'s needed
            // before the config initialization
            window.moment_locale = "'.esc_html(TKT_LANG).'";
            window.moment_timezone = "'.esc_html(get_option('timezone_string')).'";
            window.tkt_config = {
                "version": "1",
                "base_url": "'.esc_html(get_site_url()).'/",
                "engine_uri": "'.esc_html($app->get_config('ticketack.engine_uri')).'/",
                "eshop_uri": "'.esc_html($app->get_config('ticketack.eshop_uri')).'/",
                "api_key": "'.esc_html($app->get_config('ticketack.api_key')).'",
                "salepoint_id": "'.esc_html($app->get_config('ticketack.salepoint_id')).'",
                "cashregister_id": "'.esc_html($app->get_config('ticketack.cashregister_id')).'",
                "edition": "'.esc_html($app->get_config('edition')).'",
                "program_url": "'.esc_html(tkt_program_url()).'",
                "ticket_view_url": "'.esc_html(tkt_ticket_view_url()).'",
                "shop_url": "'.esc_html(tkt_shop_url()).'",
                "cart_url": "'.esc_html(tkt_cart_url()).'",
                "cart_reset_url": "'.esc_html(tkt_cart_reset_url()).'",
                "checkout_url": "'.esc_html(tkt_checkout_url()).'",
                "login_url": "'.esc_html(tkt_login_url()).'",
                "buy_pass_url": "'.esc_html(tkt_buy_pass_url()).'",
                "registration_url": "'.esc_html(tkt_registration_url()).'",
                "user_account_url": "'.esc_html(tkt_user_account_url()).'",
                "buyer_requested_fields": '.wp_json_encode($app->get_config('eshop.requested_buyer_data')).',
                "buyer_required_fields": '.wp_json_encode($app->get_config('eshop.required_buyer_data')).',
                "otp_requested_fields": '.wp_json_encode($otp ? $otp->requested_fields($salepoint_id) : []).',
                "otp_required_fields": '.wp_json_encode($otp ? $otp->required_fields($salepoint_id) : []).',
                "lang": "'.esc_html(TKT_LANG).'",
                "i18n": '.wp_json_encode(LocalesHelper::dump_js_locales(), JSON_PRETTY_PRINT).'
            };
        </script>
        <script src="'.esc_attr(tkt_assets_url('build/app.js')).'"></script>
';
    }
}
