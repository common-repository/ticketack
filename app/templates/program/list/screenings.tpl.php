<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
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
 * }
 */
?>
<div id="tkt_program" class="tkt-wrapper" data-component="Program/BookabilityState">
    <div class="container">
        <?php if (empty($data->screenings)) : ?>
        <h3 class="no-event-title"><?php echo esc_html(tkt_t('Aucuné séance programmée actuellement, revenez nous visiter prochainement.')) ?></h3>
        <?php else: ?>

        <?php foreach ($data->screenings as $screening) : ?>
        <div class="tkt_program_screening" <?php echo esc_attr(tkt_screening_data_attributes($screening, $data->filter_fields)) ?>>
            <?php TKTTemplate::output('program/list/screening', (object)[ 'screening' => $screening ]) ?>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
</div>
