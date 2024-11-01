<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * Ticket connection template
 */
?>
<div class="tkt-wrapper" data-component="Ticket/TicketConnect"></div>

<?php TKTTemplate::output('ticket/ticket', (object)[]) ?>

<?php
/**
 * Ticket connection widget content
 * This template will be parsed by underscore.js
 *
 * JS Input: {
 *   "ticket": Ticket instance, if the ticket is connected,
 *   "program_url": String
 * }
 */
?>
<script type="text/template" id="tkt-ticket-connect-tpl">
    <% if (!ticket)  { %>
    <div class="tkt-ticket-connect">
        <div class="connect-panel">
            <div class="ticket_connect">
                <div>
                    <?php echo wp_kses_post(tkt_ticketidize(tkt_t("Vous avez un TicketID ?"))) ?>
                </div>
                <div class="col">
                    <div class="row mt-5 input-pass">
                        <input id="pass-id" type="number"
                            class="tkt-input input-invert form-control text-center pass-number-input"
                            placeholder="123456" maxlength="6" />
                        <p class="minus">-</p>
                        <input id="pass-code" type="password" class="input input-invert text-center pass-key-input"
                            placeholder="abcdef" maxlength="6" />
                    </div>
                </div>
                <div class="row">
                    <div class="col text-center">
                        <div class="error pass-error d-none text-center text-danger"></div>
                        <button class="btn btn-primary button login-btn connect-btn mt-5 mb-3">
                            <i class="tkt-icon-log-out"></i> <?php echo esc_html(tkt_t('Connexion')) ?>
                        </button>
                    </div>
                </div>
                <hr />
                <div>
                    <?php echo wp_kses_post(tkt_ticketidize(tkt_t("Votre TicketID se trouve sur votre abonnement"))) ?>
                </div>
            </div>
        </div>
    </div>
    <% } %>

    <%
    jQuery(document).ready(function($) {
        $("#pass-id").keyup(function() {
            if (this.value.length == this.maxLength) {
                $('#pass-code').focus();
            }
        });
    });
    %>
</script>
