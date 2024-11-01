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

$screenings = $e->screenings();
$ids = implode(',', array_map(function ($s) {
    return $s->_id();
}, $screenings));

$images_width  = TKTApp::get_instance()->get_config('images_dimensions.big_width');
$images_height = TKTApp::get_instance()->get_config('images_dimensions.big_height');
$image_url     = tkt_img_proxy_url($e->first_poster()->url, $images_width, $images_height);
?>
<div class="tkt-wrapper event-inner h-100">

  <div class="row no-gutters h-100">
    <div class="col left-col">
      <div class="poster-wrapper">
        <img class="img-fluid poster" src="<?php echo esc_attr($image_url) ?>" />
      </div>

      <div class="infos-wrapper">
        <div class="row">
          <div class="col">
            <span class="title">
              <a href="<?php echo esc_html(tkt_event_details_url($e)) ?>">
                <?php echo esc_html($e->localized_title_or_original('fr')) ?>
              </a>
            </span>
          </div>
        </div>

        <?php if (!empty($e->opaque()['genre'])) : ?>
        <div class="row">
          <div class="col">
            <span class="genre">
              <?php echo esc_html($e->opaque()['genre']) ?>
            </span>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($screenings)) : ?>
        <div class="screenings-wrapper">
            <?php foreach ($screenings as $s) : ?>
            <button class="screening-btn">
                <a href="<?php echo esc_attr(tkt_event_book_url($e, $s)) ?>">
                    <span class="screening-btn-time">
                        <i class="tkt-icon-tag"></i>&nbsp;
                        <?php echo esc_html(tkt_date_and_time_to_time_s($s->start_at())) ?>
                    </span>
                    <span class="screening-btn-tags">
                        <?php if ($s->opaque()['_3d']) : ?>
                        <span class="tag 3d">3D</span>
                        <?php endif; ?>

                        <?php if (!empty($s->opaque()['version'])) : ?>
                        <span class="tag version"><?php echo esc_html($s->opaque()['version']) ?></span>
                        <?php endif; ?>
                    </span>
                </a>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="row">
          <div class="col">
            <span class="more-infos">
              <a href="<?php echo esc_attr(tkt_event_details_url($e)) ?>">
                <small><i class="tkt-icon-chevron-right"></i>&nbsp;</small>
                <?php echo esc_html(tkt_t('Plus d\'informations')) ?>
              </a>
            </span>
          </div>
        </div>

      </div>
    </div>
  </div>

</div>

