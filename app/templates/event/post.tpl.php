<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

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
    $date_title = $e->opaque('free_text_3')['fr'];
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
    $description = $e->opaque('description')['fr'];
    if (strip_tags($description) == "") {
        $description = "";
    }
}

$ids = array_map(function ($s) {
    return $s->_id();
}, $e->screenings());

$images_width  = TKTApp::get_instance()->get_config('images_dimensions.big_width');
$images_height = TKTApp::get_instance()->get_config('images_dimensions.big_height');
?>
<div class="tkt-wrapper tkt_event">

  <div class="row">
    <div class="col">
      <div id="event-carousel" data-component="Media/Carousel">
        <div class="carousel-inner">
          <?php foreach ($e->trailers() as $i => $t) : ?>
          <div class="carousel-item <?php echo $i == 0 ? 'active' : '' ?>">
            <div class="tkt-event-carousel-trailer-wrapper d-block w-100">
              <div
              id="tkt-event-carousel-trailer-<?php echo esc_attr($i) ?>"
                class="tkt-event-carousel-trailer"
                data-component="Media/YoutubeVideo"
                data-video-id="<?php echo esc_attr(Pkt_yt_video_id($t->url)) ?>"
                data-video-image="<?php echo esc_attr(tkt_img_proxy_url($t->image, $images_width, $images_height)) ?>"
                data-bs4-carousel-id="event-carousel">
              </div>
            </div>
          </div>
          <?php endforeach; ?>
          <?php foreach ($e->posters() as $i => $p) : ?>
          <div class="carousel-item <?php echo count($e->trailers()) == 0 && $i == 0 ? 'active' : '' ?>">
            <img style="max-width: 924px" class="d-block w-100" src="<?php echo esc_attr(tkt_img_proxy_url($p->url, $images_width, $images_height)) ?>" alt="<?php echo esc_attr($e->localized_title_or_original('fr')) ?>">
          </div>
          <?php endforeach; ?>
        </div>
        <?php if (count($e->trailers()) + count($e->posters()) > 1) : ?>
        <a class="carousel-control-prev" href="#event-carousel" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Précédent</span>
        </a>
        <a class="carousel-control-next" href="#event-carousel" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Suivant</span>
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm left-col text-left align-self-start">

      <div class="row">
        <div class="col">
          <span class="screening-date"><?php echo wp_kses_post($date_title) ?></span>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <span class="title active">
            <?php echo esc_html($e->localized_title_or_original('fr')) ?>
          </span>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <span class="genre">
            <?php echo esc_html($e->opaque()['genre']) ?>
          </span>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <span class="free-text-one">
            <?php echo wp_kses_post($e->opaque('free_text_1')['fr']) ?>
          </span>
        </div>
      </div>
    </div>

    <div class="col-sm right-col text-right align-self-start">
      <div class="infos">
        <?php echo wp_kses_post($e->opaque('free_text_2')['fr']) ?>
      </div>
    </div>

  </div>

  <div class="row" data-component="Program/BookabilityState">
    <div class="col" data-bookability-ids="<?php echo esc_attr(implode(',', $ids)) ?>">
      <span class="show-booking-form">
        <div data-component="Media/Loading" data-size-sm class="show-while-loading"></div>
        <span class="show-if-bookable show-if-almost-not-bookable d-none">
          <a href=""><?php echo esc_html(tkt_t('Billets')) ?></a>
        </span>
        <span class="show-if-almost-not-bookable assertive d-none"><?php echo esc_html(tkt_t('Il ne reste que quelques places !')) ?></span>
        <span class="show-if-not-bookable assertive d-none"><?php echo esc_html(tkt_t('Complet')) ?></span>
      </span>
    </div>
  </div>

  <?php echo do_shortcode('[tkt_booking_form layout="form" theme="dark" ids="'.implode(',', $ids).'" /]') ?>

  <div class="row">
    <div class="col">
      <div class="synopsis">
        <span class="title assertive">
          <?php echo esc_html($e->localized_title_or_original('fr')) ?>
        </span>
        <span class="text">
            <?php echo wp_kses_post($description) ?>
        </span>
      </div>
    </div>
  </div>

</div>
