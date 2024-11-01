<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;
?>

<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Lato:300,400,700,900');

    .wp-site-blocks, .has-global-padding {
        padding:0;
    }
</style>

<?php
/**
 * Program events template
 *
 * Input:
 * $data: {
 *   "events": [
 *
 *   ],
 *   "filter_fields": ['date', 'cinema_hall', ...],
 *   "nb_per_row": 3
 * }
 */
$timeout = $data->slider_timeout;
?>
<div id="tkt_program" class="tkt-wrapper alignfull" style="height: 1920px">
    <section class="tkt-section tkt-dark-section" style="height:5%" ><div class="header_soon text-center"><h1>Prochainement</h1></div></section>
    <section class="tkt-section tkt-light-section" style="height:95%" > <div class="container-fluid slider_soon_portrait" style="height:100%">
        <?php if (empty($data->events)) : ?>
        <h3 class="no-event-title"><?php echo esc_html(tkt_t('Aucun événement programmé actuellement, revenez nous visiter prochainement.')) ?></h3>
        <?php else: ?>

            <?php foreach($data->events as $event) : ?>
            <div class="tkt_slider_event h-100 col-12" data-type="<?php echo esc_attr($event->opaque('type')) ?>" <?php echo esc_html(tkt_event_data_attributes($event, $data->filter_fields)) ?>>
                <?php TKTTemplate::output('program/slider_portrait/event', (object)[ 'event' => $event ]) ?>
            </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
    </section>
</div>

<script>
let slideIndex = 0;
showSlides();

function showSlides() {
    let i;
    let slides = document.getElementsByClassName("tkt_slider_event");
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }

    slideIndex++;

    if (slideIndex > slides.length)
        slideIndex = 1;

    slides[slideIndex-1].style.display = "block";
    setTimeout(showSlides, <?php echo esc_html($timeout); ?>); // Change image every 3 seconds
}
</script>

