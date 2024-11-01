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

$images_width  = TKTApp::get_instance()->get_config('images_dimensions.big_width');
$images_height = TKTApp::get_instance()->get_config('images_dimensions.big_height');
$image_url     = tkt_img_proxy_url($e->first_poster()->url, $images_width, $images_height);
?>

<div class="row h-100">
    <div class="col col-6 left-col text-center">
        <span class="tkt-section-title fs-1"> <?php echo esc_html($e->localized_title_or_original(TKT_LANG)) ?> </span>
        <?php if (!empty($screenings)) : ?>
        <div class="screenings-wrapper mt-2">
            <?php
                $i = 0;
                foreach ($screenings as $s) {
                    if (++$i > 30) {
                        break;
                    }
            ?>
                    <span>
                        <?php echo esc_html(tkt_date_and_time_to_min_s($s->start_at())) ?>
                    </span>
                    <span class="screening-btn-tags">
                        <?php if ($s->opaque()['_3d']) : ?>
                            <span class="tag 3d">3D</span>
                        <?php endif; ?>

                        <?php if (!empty($s->opaque()['version'])) : ?>
                            <span class="tag version"><?php echo esc_html($s->opaque()['version']) ?></span>
                        <?php endif; ?>
                    </span>
                    <br />
            <?php
                }
            ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="col col-6 right-col h-100" >
        <div class="poster-wrapper h-100 mx-auto text-center">
            <img class="img-fluid poster h-100" style="max-height: 940px" src="<?php echo esc_attr($image_url) ?>" />
        </div>
    </div>
</div>
