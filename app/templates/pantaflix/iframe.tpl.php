<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Pantaflix player iframe template
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "provider": string
 *   "ticket": Ticket
 *   "contentId": int
 * }
 *

 */
?>
<div style="overflow: hidden; padding-top: 56.25%; height: 0; position: relative; width: 100%;">
    <iframe
        style="border: 0; height: 100%; left: 0; position: absolute; top: 0; width: 100%;"
        src="https://embed.cdn.pantaflix.com/latest/?code=<%= ticket._id %>&provider=ticketack.<%= provider %>#/<%= provider %>/playlist/<%= contentId %>"
        allow="autoplay; encrypted-media; picture-in-picture"
        frameborder="0"
        allowfullscreen="true"
    ></iframe>
</div>
