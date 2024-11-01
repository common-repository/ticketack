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

$ids = implode(',', array_map(function ($s) {
    return $s->_id();
}, $e->screenings()));

$images_width  = TKTApp::get_instance()->get_config('images_dimensions.big_width');
$images_height = TKTApp::get_instance()->get_config('images_dimensions.big_height');
$image_url     = tkt_img_proxy_url($e->first_poster()->url, $images_width, $images_height);
?>
<div class="tkt-wrapper event-inner h-100">

  <div class="row h-50 toto">
    <div class="col left-col h-100">
      <div class="poster-wrapper d-none d-md-block h-100">
        <img class="img-fluid poster h-100" src="<?php echo esc_html($image_url) ?>" />
      </div>
    </div>
  </div>

  <div class="row h-50 p-3">
    <div class="col">
      <div class="row infos-wrapper h-75">
        <div class="col">

          <div class="row">
            <div class="col">
              <span class="title">
              <a href="<?php echo esc_attr(tkt_event_details_url($e)) ?>">
                  <?php echo esc_html($e->localized_title_or_original(TKT_LANG)) ?>
                </a>
              </span>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <span class="description">
                  <?php echo wp_kses_post($e->localized_description(TKT_LANG)) ?>
              </span>
            </div>
          </div>

        </div>
      </div>

      <div class="row h-25">
        <div class="col">

          <div class="row">
            <div class="col">
              <span class="tickets" data-bookability-ids="<?php echo esc_attr($ids) ?>">
                <a class="show-while-loading">...</a>
                <a
                  class="show-if-bookable show-if-almost-not-bookable"
                  href="<?php echo esc_attr(tkt_event_details_url($e)) ?>">
                  <?php echo esc_html(tkt_t('Billets')) ?> <span class="event-complete"></span>
                </a>
                <div class="show-if-almost-not-bookable assertive d-none"><?php echo esc_html(tkt_t('Il ne reste que quelques places !')) ?></div>
                <div class="show-if-not-bookable assertive d-none"><?php echo esc_html(tkt_t('Complet')) ?></div>
              </span>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>

</div>
