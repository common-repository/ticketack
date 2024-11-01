<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * Screenings program template
 *
 * Input:
 * $data: {
 *   "screenings": [
 *
 *   ],
 *   "filter_fields": ['date', 'cinema_hall', ...]
 *   "items_per_row": int 1 | 2 | 3 | 4 | 6,
 *   "image_width_pct": float,
 *   "image_ratio": float | "auto",
 *   "description_max_line": int
 * }
 */

$nb_per_row = 12 / $data->items_per_row;
?>
<div id="tkt_program" class="tkt-wrapper tkt-blocks" data-component="Program/BookabilityState">
    <div class="container">
        <?php if (empty($data->screenings)) : ?>
            <h3 class="no-screening-title"><?php echo esc_html(tkt_t("Aucune séance à afficher")) ?></h3>
        <?php else: ?>

        <div class="row no-gutters <?php echo $data->items_per_row === 1 ? 'p-3' : '' ?> ">
            <?php foreach($data->screenings as $screening) : ?>
            <div class="tkt_program_screening <?php echo esc_attr($data->items_per_row > 1 ? ' col-sm-6 p-4' : '') ?> col-12 col-md-<?php echo intval($nb_per_row) ?>" <?php echo esc_html(tkt_screening_data_attributes($screening, $data->filter_fields)) ?>>
                <?php TKTTemplate::output('program/blocks/screening',
                    (object)[
                        'screening'            => $screening,
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
