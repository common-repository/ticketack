<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\Core\Models\Article;

/**
 * Articles listtemplate
 *
 * Input:
 * $data: {
 *   "articles"             : [ ... ],
 *   "pagination"           : [
 *      "show_pagination"   : bool,
 *      "tkt_page"          : int,
 *      "total_page"        : int
 *   ]
 * }
 */

$current_page       = $data->pagination->tkt_page;
$total_page = $data->pagination->total_page;

$first_link = $current_page == 1 ? 'javascript:;' : '?tkt_page=1';
$prev_link  = $current_page == 1 ? 'javascript:;' : '?tkt_page='.($current_page - 1);
$next_link  = $current_page == $total_page ? 'javascript:;' : '?tkt_page='.($current_page + 1);
$last_link  = $current_page == $total_page ? 'javascript:;' : '?tkt_page='.$total_page;
?>

<div class="tkt-articles-pagination">
    <div class="btn-group" role="group">

            <button type="button" class="button btn btn-sm btn-secondary page-item <?php echo ($current_page == 1) ? "disabled" : "" ?>">
                <a href="<?php echo esc_attr($first_link) ?>" class="btn btn-sm btn-link">
                    <<
                </a>
            </button>
            <button type="button" class="button btn btn-sm btn-secondary page-item <?php echo ($current_page == 1) ? "disabled" : "" ?>">
                <a href="<?php echo esc_attr($prev_link) ?>" class="btn btn-sm btn-link">
                    <?php echo esc_html(tkt_t('Précédente')) ?>
                </a>
            </button>

            <?php if ($current_page >= 4) : ?>
                <button type="button" class="button btn btn-sm btn-secondary">...</button>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $total_page; $p++) : ?>
                <?php if ($p > $current_page -3 && $p < $current_page + 3) : ?>
                    <button type="button" class="button btn btn-sm page-item <?php echo ($p == $current_page) ? "btn-primary active" : "btn-secondary" ?>">
                        <a href="?tkt_page=<?php echo esc_attr($p) ?>" class="btn btn-sm btn-link <?php echo esc_attr($p == $current_page ? 'active' : '') ?>"><?php echo esc_html($p) ?></a>
                    </button>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($current_page <= $total_page - 3) : ?>
                <button type="button" class="button btn btn-sm btn-secondary">...</button>
            <?php endif; ?>

            <button type="button" class="button btn btn-sm btn-secondary page-item <?php echo ($current_page == $total_page) ? "disabled" : "" ?>">
                <a href="<?php echo esc_attr($next_link) ?>" class="btn btn-sm btn-link">
                    <?php echo esc_html(tkt_t('Suivante')) ?>
                </a>
            </button>
            <button type="button" class="button btn btn-sm btn-secondary page-item <?php echo ($current_page == $total_page) ? "disabled" : "" ?>">
                <a href="<?php echo esc_attr($last_link) ?>" class="btn btn-sm btn-link">
                    >>
                </a>
            </button>
    </div>
</div>

