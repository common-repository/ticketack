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

$e          = $data->event;
$screenings = $e->screenings();

$ids = implode(',', array_map(function ($s) {
    return $s->_id();
}, $e->screenings()));

$images_width  = TKTApp::get_instance()->get_config('images_dimensions.big_width');
$images_height = TKTApp::get_instance()->get_config('images_dimensions.big_height');
$image_url     = tkt_img_proxy_url($e->first_poster()->url, $images_width, $images_height);

?>
<div class="event-inner">

  <div class="row">

        <div class="col-12 col-md-6 text-center left-col">
            <div class="poster-wrapper">
                <img class="img-fluid poster poster-event-list m-0" src="<?php echo esc_attr($image_url) ?>" />
            </div>
        </div>

        <div class="col-12 col-md-6 right-col">

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
                    <p class="description text-justify mt-3">
                        <?php echo wp_kses_post($e->localized_description(TKT_LANG)) ?>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <span class="tickets" data-bookability-ids="<?php echo esc_attr($ids) ?>">
                        <a class="show-while-loading">...</a>
                        <span class="more-infos show-if-not-bookable d-none">
                            <a href="<?php echo esc_attr(tkt_event_details_url($e)) ?>">
                            <?php echo esc_html(tkt_t('Plus d\'informations')) ?>
                            </a>
                        </span>
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
