<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\Core\Models\Event;

/**
 * Program screening template
 *
 * Input:
 * $data: {
 *   "screening": { ... },
 *   "output": "title|date|time|datetime|venue|poster|url",
 *   "with_link": true|false
 * }
 */

$s      = $data->screening;
$output = $data->output;
$m = array_shift(Event::from_screenings([$s]));

$images_width  = TKTApp::get_instance()->get_config('images_dimensions.big_width');
$images_height = TKTApp::get_instance()->get_config('images_dimensions.big_height');
$image_url     = tkt_img_proxy_url($m->first_poster()->url, $images_width, $images_height);
?>

<?php if (empty($output)) : ?>
<div class="tkt-wrapper next-screening event-inner">
  <div class="row">
    <div class="col">
      <div class="poster-wrapper">
        <a href="<?php echo esc_attr(tkt_event_book_url($m, $s)) ?>">
          <img src="<?php echo esc_attr($image_url) ?>" />
        </a>
      </div>
      <div class="infos-wrapper">
        <h4 class="title text-center">
            <span>
              <?php echo esc_html($m->localized_title_or_original(TKT_LANG)) ?>
            </span>
        </h4>
        <div class="datetime text-center">
            <span>
              <?php echo esc_html(tkt_datetime_to_s($s->start_at())) ?>
            </span>
        </div>
      </div>
    </div>
  </div>
</div>
<?php else: ?>
    <?php
    $value = '';
    switch ($output) {
        case 'title':
            $value = $m->localized_title_or_original(TKT_LANG);
            break;
        case 'date':
            $value = tkt_date_to_min_s($s->start_at());
            break;
        case 'time':
            $value = tkt_date_and_time_to_time_s($s->start_at());
            break;
        case 'datetime':
            $value = tkt_date_and_time_to_min_s($s->start_at());
            break;
        case 'venue':
            $value = $s->place()->name();
            break;
        case 'poster':
            $value = '<img src="'.$image_url.'" />';
            break;
        case 'url':
            $value = tkt_event_book_url($m, $s);
            break;
    }
    if ($data->with_link) {
        $value = sprintf('<a href="%s">%s</a>', tkt_event_book_url($m, $s), $value);
    }
    echo wp_kses_post($value);
    ?>
<?php endif; ?>
