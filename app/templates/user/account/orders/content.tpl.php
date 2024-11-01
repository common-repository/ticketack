<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * User account orders content
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "user": { ... },
 *   "tickets": [ ... ],
 *   "other_tickets": [ ... ],
 *   "orders": [ ... ],
 * }
 */
?>
<%
const isVisibleOrder = order => {
  let yesterday = new Date();
  yesterday.setDate(yesterday.getDate() - 1);

  let orderDate = new Date(order.created_at);
  return (orderDate.getTime() > yesterday.getTime()) || (order.status === "COMPLETED");
};
%>

<div id="tkt-account-content-profile" class="tkt-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <% if (!orders || orders.length == 0) { %>
            <h3 class="text-info text-center mt-3">
                <?php echo esc_html(tkt_t('Vous n\'avez pas encore de commandes.')) ?>
            </h3>
            <% } else { %>
            <table class="table table-bordered tale-condensed table-striped table-hover">
                <tr>
                    <th><?php echo esc_html(tkt_t('#')) ?></th>
                    <th><?php echo esc_html(tkt_t('Date')) ?></th>
                    <th><?php echo esc_html(tkt_t('Prix total')) ?></th>
                    <th><?php echo esc_html(tkt_t('Paiement')) ?></th>
                    <th><?php echo esc_html(tkt_t('Statut')) ?></th>
                </tr>
                <% orders.reverse().filter(isVisibleOrder).map(function (order) { %>
                <tr>
                    <td>
                        <b><%= order.id %></b>
                    </td>
                    <td>
                        <%= order.getFormattedDate() %>
                    </td>
                    <td>
                        <%= order.getFormattedTotal() %>
                    </td>
                    <td>
                        <%= order.getFormattedPaymentMethod() %>
                    </td>
                    <td>
                        <span class="badge badge-<%= order.getStatusColorClassname() %>">
                            <%= order.getFormattedStatus() %>
                        <span>
                    </td>
                </tr>
                <% }); %>
            </table>
            <% } %>
        </div>
    </div>
</div>
