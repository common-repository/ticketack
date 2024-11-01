<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Program event template
 *
 * Input:
 * $data: {
 *   "days": [ Datetime, Datetime, ... ],
 *   "active": "2018-09-25" or null
 * }
 */

$days       = $data->days;
$active     = $data->active;
$query_mask = '?d=%s';
?>

<?php if (!empty($days)) : ?>
<div class="tkt-wrapper tkt-days-filters">
  <div class="container">
    <div class="row">
      <div class="col">
          <ul>
              <?php foreach ($days as $day) : ?>
              <li class="tkt-day-filter <?php echo $active == $day->format('Y-m-d') ? 'active' : '' ?>">
                  <a href="<?php echo esc_attr(sprintf($query_mask, $day->format('Y-m-d'))) ?>">
                      <span class="tkt-day-filter-day">
                          <?php echo esc_attr(strftime('%A', $day->getTimestamp())) ?>
                      </span>
                      <span class="tkt-day-filter-date"><?php echo esc_html($day->format('j')) ?></span>
                  </a>
              </li>
              <?php endforeach; ?>
          </ul>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
