<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Program event template
 *
 * Input:
 * $data: {
 *   "event": { ... },
 *   "image_width_pct": float ,
 *   "image_ratio": float | "auto",
 *   "description_max_line": int
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
<div class="event-inner">
    <div class="row">
        <div class="image-wrapper" style="width: <?php echo esc_attr($data->image_width_pct) ?>%; aspect-ratio: <?php echo esc_attr($data->image_ratio) ?>;">
            <div class="poster-wrapper w-100 h-100">
                <img class="img-fluid poster poster-event-list m-0 w-100 h-100"
                    src="<?php echo esc_attr($image_url) ?>" />
            </div>
        </div>
        <div class="details pr-3 pl-3"
            style="width: <?php echo esc_attr($data->image_width_pct != 100 ? (100 - $data->image_width_pct) : 100) ?>%;">
            <div class="row">
                <div class="col">
                    <a href="<?php echo esc_attr(tkt_event_details_url($e)) ?>">
                        <h2 class="title">
                            <?php echo esc_html($e->localized_title_or_original(TKT_LANG)) ?>
                        </h2>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <p class="description text-justify mt-3"
                        style="-webkit-line-clamp: <?php echo esc_attr($data->description_max_line) ?>;">
                        <?php echo wp_kses_post($e->localized_description(TKT_LANG)) ?>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="tickets" data-bookability-ids="<?php echo esc_attr($ids) ?>">
                        <a class="show-while-loading">...</a>
                        <a class="show-if-bookable show-if-almost-not-bookable" href="<?php echo esc_attr(tkt_event_details_url($e)) ?>">
                            <?php echo esc_html(tkt_t('Billets')) ?> <span class="event-complete"></span>
                        </a>
                        <div class="show-if-almost-not-bookable assertive d-none">
                            <?php echo esc_html(tkt_t('Il ne reste que quelques places !')) ?></div>
                        <div class="show-if-not-bookable assertive d-none"><?php echo esc_html(tkt_t('Complet')) ?></div>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
