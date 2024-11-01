<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Program event template
 *
 * Input:
 * $data: {
 *   "rows": [
 *      "date" => [
 *          "value" => "label",
 *      ],
 *      "section" => [
 *          "value" => "label",
 *          "value" => "label",
 *      ],
 *   ],
 *   "target": "..."
 * }
 */

$rows = $data->rows;
?>

<?php if (!empty($rows)) : ?>
<div
  class="tkt-wrapper tkt-filters"
  data-component="Program/FilterRows"
  data-criterium="<?php echo esc_attr(implode(',', array_keys($rows))) ?>"
  <?php echo (!empty($data->target) ? 'data-target="'.esc_attr($data->target).'"' : '') ?>
>
  <div class="row">
    <div class="col">
        <?php foreach ($rows as $type => $filters) : ?>
        <ul>
            <li class="tkt-filter active" data-criteria="<?php echo esc_attr($type) ?>" data-<?php echo esc_attr($type) ?>="">
                <?php echo esc_html(tkt_t('Tout')) ?>
            </li>
            <?php foreach ($filters as $filter) : ?>
            <li class="tkt-filter" data-criteria="<?php echo esc_attr($type) ?>" data-<?php echo esc_attr($type) ?>="<?php echo esc_attr($filter['value']) ?>">
                <?php echo esc_html($filter['label']) ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>
