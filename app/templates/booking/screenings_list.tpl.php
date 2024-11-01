<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * Screenings and pricings list
 *
 * Input:
 * $data: {
 *   "ids": [ ... ],
 *   "theme": 'dark|light'
 * }
 */

?>
<div class="tkt-wrapper">
    <section class="tkt-section tkt-<?php echo esc_attr($data->theme) ?>-section book-section">
        <div class="row">
            <div class="col">
                <div
                    class="booking-form screenings-list"
                    data-component="Booking/ScreeningsList"
                    data-show-on-load="1"
                    data-ids="<?php echo esc_attr(implode(',', $data->ids)) ?>">
                </div>
            </div>
        </div>
    </section>

    <div>
        <!-- Underscore.js templates used by client side -->
        <script type="text/template" id="tkt-booking-screenings-list-dates-tpl">
            <?php TKTTemplate::output('booking/screenings_list_dates', (object)[]) ?>
        </script>
        <script type="text/template" id="tkt-booking-screenings-list-pricings-tpl">
            <?php TKTTemplate::output('booking/screenings_list_pricings', (object)[]) ?>
        </script>
        <script type="text/template" id="tkt-booking-screenings-list-success-tpl">
            <?php TKTTemplate::output('booking/screenings_list_success', (object)[]) ?>
        </script>
    </div>
</div>
