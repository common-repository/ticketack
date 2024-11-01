<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;

/**
 * Articles listtemplate
 *
 * Input:
 * $data: {
 *   "theme": 'dark|light',
 *   "articles"             : [ ... ],
 *   "hide_sorters"         : true|false,
 *   "add_to_cart_mode"     : popup|direct,
 *   "sort"                 : "",
 *   "pagination"           : [
 *      "show_pagination"   : bool,
 *      "tkt_page"          : int,
 *      "total_page"        : int
 *   ]
 * }
 */

?>

<div id="tkt-shop" class="tkt-wrapper">
    <?php if (empty($data->articles)) : ?>
        <h3 class="no-event-title"><?php echo esc_html(tkt_t('Aucun article dans cette catégorie, revenez nous visiter prochainement.')) ?></h3>
    <?php else: ?>
        <?php if (!$data->hide_sorters) : ?>
        <div class="tkt-articles-toolbar">
            <div class="tkt-articles-sort-wrapper">
                <?php TKTTemplate::output('shop/sort/sort', $data) ?>
            </div>
        </div>
        <?php endif; ?>
        <?php foreach ($data->articles as $chunk) : ?>
            <div class="row mb-4">
            <?php foreach ($chunk as $article) : ?>
                <div class="col tkt-article">
                    <?php TKTTemplate::output('shop/list/article', (object)[ 'article' => $article, 'add_to_cart_mode' => $data->add_to_cart_mode ]) ?>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <?php if ($data->pagination->show) : ?>
            <div class="tkt-articles-pagination-wrapper">
                <?php TKTTemplate::output('shop/pagination/pagination', $data) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Underscore.js templates used by client side -->
<script type="text/template" id="tkt-buy-article-form-pricings-tpl">
    <?php TKTTemplate::output('buy_article/form_pricings', $data) ?>
</script>
<script type="text/template" id="tkt-buy-article-form-success-tpl">
    <?php TKTTemplate::output('buy_article/form_success', $data) ?>
</script>
