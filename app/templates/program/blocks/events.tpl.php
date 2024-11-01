<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * Program events template
 *
 * Input:
 * $data: {
 *   "events": [
 *
 *   ],
 *   "filter_fields": ['date', 'cinema_hall', ...],
 *   "items_per_row": 1 | 2 | 3 | 4 | 6,
 *   "image_width_pct": float,
 *   "image_ratio": float | "auto",
 *   "description_max_line": int
 */
$nb_per_row = 12 / $data->items_per_row;
?>
<div id="tkt_program" class="tkt-wrapper tkt-blocks" data-component="Program/BookabilityState">
    <div class="container">
        <?php if (empty($data->events)) : ?>
        <h3 class="no-event-title"><?php echo esc_html(tkt_t('Aucun événement programmé actuellement, revenez nous visiter prochainement.')) ?></h3>
        <?php else: ?>

        <div class="row no-gutters">
            <?php foreach($data->events as $event) : ?>
            <div class="tkt_program_event<?php echo $data->items_per_row > 1 ? ' col-sm-6 p-4' : '' ?> col-12 col-md-<?php echo intval($nb_per_row) ?>" data-type="<?php echo esc_attr($event->opaque('type')) ?>" <?php echo esc_html(tkt_event_data_attributes($event, $data->filter_fields)) ?>>
                <?php TKTTemplate::output('program/blocks/event',
                    (object) [
                        "event"                => $event,
                        "image_width_pct"      => $data->image_width_pct,
                        "image_ratio"          => $data->image_ratio,
                        "description_max_line" => $data->description_max_line
                    ]
                ) ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>
    </div>
</div>
