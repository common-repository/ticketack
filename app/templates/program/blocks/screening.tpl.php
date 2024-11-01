<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Program screening template
 *
 * Input:
 * $data: {
 *   "screening": { ... },
 *   "image_width_pct": float,
 *   "image_ratio": float | "auto",
 *   "description_max_line": int
 * }
 */

$s = $data->screening;
$m = $s->movies()[0];
$description = $m->opaque("description")[TKT_LANG];

$images_width  = TKTApp::get_instance()->get_config('images_dimensions.big_width');
$images_height = TKTApp::get_instance()->get_config('images_dimensions.big_height');
$image_url     = tkt_img_proxy_url($s->first_poster()->url, $images_width, $images_height);
?>
<div class="tkt-wrapper screening-inner">

  <div class="row">

    <div class="image-wrapper" style="width: <?php echo esc_attr($data->image_width_pct) ?>%; aspect-ratio: <?php echo esc_attr($data->image_ratio) ?>;">
      <div class="poster-wrapper w-100 h-100">
        <a href="<?php echo esc_attr(tkt_event_book_url($m, $s)) ?>">
          <img class="img-fluid poster w-100 h-100" src="<?php echo esc_attr($image_url) ?>" />
        </a>
      </div>
    </div>

    <div class="details right-col text-right align-self-end pl-2" style="width: <?php echo esc_attr($data->image_width_pct != 100 ? (100 - $data->image_width_pct) : 100 )?>%;">

      <div class="row">
        <div class="col">
          <span class="description" style="-webkit-line-clamp: <?php echo esc_attr($data->description_max_line) ?>;"><?php echo esc_html(strip_tags($description)) ?></span>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <span class="date"><?php echo esc_html(tkt_date_to_min_s($s->start_at())) ?></span>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <span class="title">
          <a href="<?php echo esc_attr(tkt_event_book_url($m, $s)) ?>">
              <?php echo esc_html($s->localized_title_or_original(TKT_LANG)) ?>
            </a>
          </span>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <span class="genre">
            <?php echo esc_html($m->opaque('genre')) ?>
          </span>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <span class="tickets" data-bookability-ids="<?php echo esc_attr($s->_id()) ?>">
            <div class="show-while-loading" data-component="Media/Loading" data-size-sm data-align-center></div>
            <a
              class="show-if-bookable show-if-almost-not-bookable"
              href="<?php echo esc_attr(tkt_event_details_url($s)) ?>">
              <span class="screening-complete"></span>
            </a>
            <span class="show-if-almost-not-bookable screening-complete"><?php echo esc_html(tkt_t('Il ne reste que quelques places')) ?></span>
            <span class="show-if-not-bookable screening-complete"><?php echo esc_html(tkt_t('Complet')) ?></span>
          </span>
        </div>
      </div>

    </div>

  </div>
</div>
