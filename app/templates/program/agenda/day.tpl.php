<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * Program screening template
 *
 * Input:
 * $data: {
 *   "date": Datetime,
 *   "screenings": [{ ... }]
 *   "can_go_left": boolean,
 *   "can_go_right": boolean,
 * }
 */

$screenings = $data->screenings;

$date    = $data->date;
$isToday = $date->format('Ymd') === (new Datetime())->format('Ymd');
$title   = $isToday ? tkt_t('Aujourd\'hui') : tkt_date_to_min_s($date);

if (!function_exists('tkt_audio')) {
    function tkt_audio($movie) {
        if (empty($movie->opaque('languages'))) {
            return null;
        }

        $audio = $movie->opaque('languages')['audio'];
        $audio = is_array($audio) ? strtoupper(implode(',', $audio)) : null;

        $subtitles = $movie->opaque('languages')['subtitles'];
        $subtitles = is_array($subtitles) ? implode(',', $subtitles) : null;

        return implode('/', array_filter([$audio, $subtitles]));
    }
}

if (!function_exists('tkt_ages')) {
    function tkt_ages($movie) {
        $ages = array_filter([
            $movie->opaque('l_min_age'),
            $movie->opaque('s_min_age'),
            $movie->opaque('a_min_age')
        ]);

        if (empty($ages)) {
            return null;
        }

        return sprintf(tkt_t('%d ans'), current($ages));
    }
}
?>

<div class="tkt-wrapper tkt_agenda_day hidden" data-index="<?php echo esc_attr($data->index) ?>" data-date="<?php echo esc_attr($date->format('Y-m-d')) ?>">
    <div class="day_title_wrapper">
        <div class="arrow arrow-left <?php echo !$data->can_go_left ? 'inactive' : 'active' ?>"></div>
        <h3 class="day_title">
            <?php echo esc_html($title) ?>
        </h3>
        <div class="arrow arrow-right <?php echo !$data->can_go_right ? 'inactive' : 'active' ?>"></div>
    </div>
    <div class="tkt_program_screenings">
        <?php foreach ($screenings as $s) : ?>
            <?php $m = $s->movies()[0]; ?>
            <div class="row">
                <div class="col">
                    <div class="tkt_program_screening">

                    <span class="tkt_screening_date show-booking-modal" data-screening-id="<?php echo esc_attr($s->_id()) ?>">
                            <a class="tkt_screening_link" href="<?php echo esc_attr(tkt_event_details_url($s)) ?>">
                                <span class="dot"></span>
                                <span class="date">
                                    <?php echo esc_html(tkt_date_and_time_to_time_s($s->start_at())) ?>
                                </span>
                            </a>
                        </span>

                        <?php if ($data->expanded) : ?>
                            <span class="tkt_screening_audio">
                                <?php echo esc_html(tkt_audio($m)) ?>
                            </span>
                            <span class="tkt_screening_ages">
                                <?php echo esc_html(tkt_ages($m)) ?>
                            </span>
                        <?php else : ?>
                            <h3 class="tkt_screening_title">
                                <span class="dot color"></span>
                                <a class="tkt_screening_link" href="<?php echo esc_attr(tkt_event_book_url($m, $s)) ?>">
                                    <?php echo esc_html($s->localized_title_or_original(TKT_LANG)) ?>
                                </a>
                            </h3>

                            <span class="tkt_screening_audio">
                                <?php echo esc_html(tkt_audio($m)) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
