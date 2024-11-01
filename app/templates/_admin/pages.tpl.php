<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Module settings API tab
 */
$tab = 'pages';
?>
<style>
.tkt-ticketid_ticket {
    font-family: Roboto,"Segoe UI", "Helvetica Neue",Arial,"Noto Sans",sans-serif;
    color: #e34449;
}
.tkt-ticketid_id {
    font-family: Roboto,"Segoe UI", "Helvetica Neue",Arial,"Noto Sans",sans-serif;
    color: #000;
    font-style: italic;
    font-weight: bold;
}
</style>

<form method="post">
    <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('tkt_admin_options')) ?>" />
    <?php
        settings_fields('ticketack-'.$tab);
        do_settings_sections('ticketack-'.$tab);
        submit_button();
    ?>
</form>
