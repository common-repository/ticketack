<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Program event template
 *
 * Input:
 * $data: {
 *   "event": { ... }
 * }
 */

$e = $data->event;

$date_title = "";
if (!empty($e->opaque('free_text_3'))) {
    $date_title = strip_tags($e->opaque('free_text_3')['fr'], '<br/>');
    if (strip_tags($date_title) == "") {
        $date_title = "";
    }
}

if (empty($date_title)) {
    $date_title = implode('<br/>', array_unique(array_map(function ($s) {
        return str_replace(':', 'H', tkt_date_and_time_to_min_s($s->start_at()));
    }, $e->screenings())));
}

$description = "";
if (!empty($e->opaque('description'))) {
    $description = strip_tags($e->opaque('description')['fr'], '<br/>');
    if (strip_tags($description) == "") {
        $description = "";
    }
}

$ids = array_map(function ($s) {
    return $s->_id();
}, $e->screenings());

$first_poster = $e->first_poster();
$upload_dir   = wp_upload_dir();
$poster_url   = $upload_dir['url'].'/'.basename($first_poster->url);
?>

<h3>
  <strong><?php echo esc_html(strtoupper($date_title)) ?></strong><p>&nbsp;</p>
</h3>
<h2>
  <?php echo esc_html(strtoupper($e->opaque()['genre'])) ?>
</h2>
<?php if (!empty($e->opaque('free_text_1'))) : ?>
<div>
  <?php echo wp_kses_post($e->opaque('free_text_1')['fr']) ?><p>&nbsp;</p>
</div>
<?php endif; ?>
<div>
  <?php echo wp_kses_post($description) ?><p>&nbsp;</p>
</div>
<?php if ($e->opaque('type') == 'music_group') : ?>
<div>
<a href="<?php echo esc_attr(tkt_event_book_url($e)) ?>" alt="<?php echo esc_attr($e->localized_title_or_original('fr')) ?>">BILLETS</a>
</div>
<?php endif; ?>
<div>
    <a href="<?php echo esc_attr(tkt_event_details_url($e)) ?>" alt="<?php echo esc_attr($e->localized_title_or_original('fr')) ?>">PLUS D'INFORMATIONS</a>
</div>
<br/>
<div>
  <?php echo wp_kses_post($e->opaque('free_text_2')['fr']) ?><p>&nbsp;</p>
</div>
<!--more-->
