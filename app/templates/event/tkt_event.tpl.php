<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;
use Ticketack\Core\Models\Screening;

/**
 * TKTEvent template
 *
 * Input:
 * $data: {
 *   "tkt_event": { ... }
 * }
 */

$e = $data->tkt_event;

/* Check if we want to access the event (movie) or the screening.
 * If we access the screening:
 * - if it has only one movie, show the movie page (_single.tpl.php).
 * - if it has more than one movie, show the package page (_package.tpl.php).
 */
$is_film_package    = false;
$selected_screening = null;
$selected_s_id      = tkt_get_url_param('s_id', '');
if (!empty($selected_s_id)) {
    $screenings = array_map(function ($s) {
        return new Screening($s);
    }, json_decode(get_post_meta($e->ID, 'screenings')[0], /*assoc*/true));
    foreach ($screenings as $s) {
        if ($s->_id() == $selected_s_id) {
            $selected_screening = $s;
            $is_film_package    = count($s->movies()) > 1;
            break;
        }
    }
}

if ($is_film_package) {
    $data->screening = $selected_screening;
    TKTTemplate::output('event/_package', $data);
} else {
    TKTTemplate::output('event/_single', $data);
}
?>
