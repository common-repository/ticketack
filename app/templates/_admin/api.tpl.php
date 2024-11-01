<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Module settings API tab
 */
$tab = 'api';
?>
<form method="post">
    <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('tkt_admin_options')) ?>" />
    <?php
        settings_fields('ticketack-'.$tab);
        do_settings_sections('ticketack-'.$tab);
        submit_button();
    ?>
</form>
