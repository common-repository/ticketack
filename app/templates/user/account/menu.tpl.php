<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * User account menu
 *
 * Input:
 * $data: {
 *   "tabs" : [profile|orders|tickets],
 *   "active_tab": "profile|orders|tickets|logout"
 * }
 */
?>

<div class="tkt-wrapper">
    <section class="tkt-section tkt-light-section tkt-user-account-menu-section">
        <div class="row">
        <?php foreach ($data->tabs as $tab) : ?>
            <div class="col-6 col-sm-2">
                <?php TKTTemplate::output('user/account/'.$tab.'/menu', (object)[ 'active' => $data->active_tab === $tab]) ?>
            </div>
        <?php endforeach; ?>
        </div>
    </section>
</div>
