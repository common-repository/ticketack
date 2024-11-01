<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * Pantaflix login template
 * This template will be parsed by underscore.js
 *
 * Input: {
 *  invalid_ticket: boolean
 * }
 */
?>
<div class="tkt-wrapper" data-component="Ticket/TicketConnect"></div>
<script type="text/template" id="tkt-ticket-connect-tpl">
<div class="tkt-wrapper">
    <div class="tkt-ticket-connect">
        <div class="connect-panel">
            <% if (invalid_ticket) { %>
                <div class="alert alert-danger">
                    <?php echo esc_html(tkt_t("Votre accréditation/pass ne vous permet pas de visionner ce contenu.")) ?>
                </div>
            <% } %>
            <div class="ticket_connect">
                <div>
                    <?php echo wp_kses_post(tkt_ticketidize(tkt_t("Vous avez un TicketID ?"))) ?>
                </div>
                <div class="col">
                    <div class="row mt-5 input-pass">
                        <input id="pass-id" type="number" class="tkt-input input-invert form-control text-center pass-number-input" placeholder="123456" maxlength="6"/>
                        <p class="minus">-</p>
                        <input id="pass-code" type="password" class="input input-invert text-center pass-key-input" placeholder="abcde" maxlength="6"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-center">
                        <div class="error pass-error d-none text-center text-danger"></div>
                        <button class="btn btn-primary button login-btn connect-btn mt-5 mb-3">
                          <i class="tkt-icon-sign-in-alt"></i> <?php echo esc_html(tkt_t('Connexion')) ?>
                        </button>
                    </div>
                </div>
                <hr />
                <div>
                    <?php echo wp_kses_post(tkt_ticketidize(tkt_t("Votre TicketID se trouve sur votre abonnement"))) ?>
                </div>
                <br />
            </div>
        </div>
    </div>

    <%
    jQuery(document).ready(function($) {
        $("#pass-id").keyup(function() {
            if (this.value.length == this.maxLength) {
                $('#pass-code').focus();
            }
        });
    });
    %>
</div>
</script>
