<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * Pantaflix player template
 *
 * Input:
 * $data: {
 *   "provider": string
 *   "screening": Screening
 *   "content_id": int
 *   "allowed_ticket_types": string
 * }
 */
?>
<div
    class="tkt-wrapper tkt-pantaflix-player"
    data-component="Pantaflix/Player"
    data-provider="<?php echo esc_attr($data->provider) ?>"
    data-screening-id="<?php echo esc_attr($data->screening->_id()) ?>"
    data-content-id="<?php echo esc_attr($data->content_id) ?>"
    data-allowed-ticket-types="<?php echo esc_attr($data->allowed_ticket_types) ?>"
>
    <button class="tkt-pantaflix-button">
        <svg viewBox="0 0 36 37" xmlns="http://www.w3.org/2000/svg">
            <path d="M30.728 5.669c7.03 7.029 7.03 18.427 0 25.456-7.029 7.03-18.427 7.03-25.456 0-7.03-7.029-7.03-18.427 0-25.456 7.029-7.03 18.427-7.03 25.456 0zm-4.617 12.465l-11.707-6.76c-.299-.172-.543-.03-.543.314v13.518c0 .345.244.486.543.313L26.11 18.76c.299-.172.299-.454 0-.626z" fill="#FFF" fill-rule="nonzero"></path>
        </svg>
        <span>
            <?php echo esc_html(tkt_t('Regarder')) ?>
        </span>
    </button>

    <!-- Underscore.js templates used by client side -->
    <script type="text/template" id="tkt-pantaflix-player-login-tpl">
        <?php TKTTemplate::output('pantaflix/login', (object)[]) ?>
    </script>
    <script type="text/template" id="tkt-pantaflix-player-iframe-tpl">
        <?php TKTTemplate::output('pantaflix/iframe', $data) ?>
    </script>
</div>
