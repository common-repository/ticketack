<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\Core\Models\Article;

/**
 * Articles listtemplate
 *
 * Input:
 * $data: {
 *   "articles": [ ... ],
 *   "sort": ""
 * }
 */
$sort_options = [
    (object)['value' => '', 'label' => '---'],
    (object)[
        'value' => Article::SORT_TYPE_ALPHA,
        'label' => 'A -> Z'
    ],
    (object)[
        'value' => Article::SORT_TYPE_REV_ALPHA,
        'label' => 'Z -> A'
    ],
    (object)[
        'value' => Article::SORT_TYPE_INCR_PRICE,
        'label' => 'Prix croissant'
    ],
    (object)[
        'value' => Article::SORT_TYPE_DECR_PRICE,
        'label' => 'Prix décroissant'
    ]
];
$selected = $data->sort;
?>
<div class="tkt-wrapper">
    <div class="tkt-articles-sort">
        <form method="GET">
            <div class="form-group row">
                <div class="col-12">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">
                        <?php echo esc_html(tkt_t('Trier par')) ?>
                            </span>
                        </div>
                        <select class="form-control" name="sort" onchange="this.form.submit()">
                            <?php foreach ($sort_options as $option) : ?>
                            <option
                                value="<?php echo esc_attr($option->value) ?>"
                                <?php echo $selected === $option->value ? 'selected' : '' ?>
                            >
                                <?php echo esc_html(tkt_t($option->label)) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
