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
 *   "item_width"           => 12,
 *   "items_per_row"        => 2,
 *   "filter"               => 'type',
 *   "filter_fields"        => [],
 *   "service_filters"      => [],
 *   "top_filter"           => 'screenings',
 *   "top_filter_values"    => [],
 *   "image_width_pct"      => 35,
 *   "image_ratio"          => 'auto',
 *   "description_max_line" => 4,
 *   "expanded"             => true
 * }
 */

// we do not show the calendar in expanded mode
if (!$data->expanded) {
    $dots  = [];
    $dates = [];
    foreach ($data->screenings as $screening) {
        $date = $screening->start_at()->format('Y-m-d');
        $dates[$date] = $date;
        if (!array_key_exists($date, $dots)) {
            $dots[$date] = [];
        }
        // add dots logic, depending on screening section ?
        $dots[$date] = array_unique(array_merge($dots[$date], array_map(function ($s) {
            return sanitize_title($s->name(TKT_LANG));
        }, $screening->sections())));
    }
    $dates = array_values($dates);
}
?>
<div id="tkt_program" class="tkt-wrapper tkt-agenda" data-component="Program/Agenda">
    <div class="container">
        <?php if (empty($data->screenings)) : ?>
            <h3 class="no-screening-title"><?php echo esc_html(tkt_t("Aucune séance à afficher")) ?></h3>
        <?php else: ?>
            <div class="tkt_agenda_days <?php echo !!$data->expanded ? 'expanded' : '' ?>">
                <?php if (!$data->expanded) : ?>
                    <input
                        type="hidden"
                        class="tkt-input agenda-date-input"
                        data-component="Form/Calendar"
                        data-theme="dark"
                        data-enable="<?php echo esc_attr(implode(',', $dates)) ?>"
                        data-dots='<?php echo esc_attr(wp_json_encode($dots)) ?>'
                        required
                        data-alt-format="<?php echo esc_attr(tkt_t('l j F')) ?>"
                    />
                <?php endif; ?>

                <?php
                    $days = [];
                    foreach($data->screenings as $screening) {
                        $key = $screening->start_at()->format('Y-m-d');
                        if (!array_key_exists($key, $days)) {
                            $days[$key] = [];
                        }
                        $days[$key][] = $screening;
                    }
                ?>

                <?php $index = 0; ?>
                <?php foreach($days as $date => $screenings) : ?>
                    <?php TKTTemplate::output('program/agenda/day',
                        (object)[
                            'date'         => new Datetime($date),
                            'screenings'   => $screenings,
                            'index'        => $index,
                            'can_go_left'  => $index > 0,
                            'can_go_right' => $index++ < count($days) - 1,
                            'expanded'     => !!$data->expanded
                         ]
                    ) ?>
                <?php endforeach; ?>
            </div>
      <?php endif; ?>
    </div>
</div>

<!-- Underscore.js templates used by client side -->
<script type="text/template" id="tkt-agenda-modal-tpl">
    <?php TKTTemplate::output('program/agenda/modal', (object)[]) ?>
</script>
<script type="text/template" id="tkt-booking-form-dates-tpl">
    <?php TKTTemplate::output('booking/form_dates', (object)[]) ?>
</script>
<script type="text/template" id="tkt-booking-form-pricings-tpl">
    <?php TKTTemplate::output('booking/form_pricings', (object)[]) ?>
</script>
<script type="text/template" id="tkt-booking-form-success-tpl">
    <?php TKTTemplate::output('booking/form_success', (object)[]) ?>
</script>
