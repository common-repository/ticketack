<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\Core\Models\User;
use Ticketack\Core\Models\Article;
use Ticketack\Core\Base\TKTApiException;
use Ticketack\WP\Templates\TKTTemplate;
use Ticketack\WP\Shortcodes\ShopShortcode;

/**
 * Article template
 *
 * Input:
 * $data: {
 *   "theme": 'dark|light',
 *   "article": { ... }
 *   "add_to_cart_mode" : popup|direct,
 * }
 */
$article = $data->article;

$images_width  = TKTApp::get_instance()->get_config('images_dimensions.big_width');
$images_height = TKTApp::get_instance()->get_config('images_dimensions.big_height');
$image_url     = tkt_img_proxy_url($article->first_poster()->url, $images_width, $images_height);

$salepoint_id = TKTApp::get_instance()->get_config('ticketack.salepoint_id');
?>
<div class="tkt-wrapper article-inner">
    <div class="row">

        <div class="col-sm-12">
            <div class="poster-wrapper">
                <a href="<?php echo esc_attr(tkt_article_details_url($article)) ?>">
                    <img class="img-fluid poster" src="<?php echo esc_attr($image_url) ?>" />
                </a>
            </div>
        </div>

        <div class="col-sm-12">
            <h3 class="name">
                <a href="<?php echo esc_attr(tkt_article_details_url($article)) ?>">
                    <?php echo esc_html($article->name(TKT_LANG)) ?>
                </a>
            </h3>
        </div>

        <div class="col-sm-12 mt-4">
            <span class="short_description"><?php echo esc_html($article->short_description(TKT_LANG)) ?></span>
        </div>

        <div class="col-sm-12 text-center mt-3 mb-3">
            <a class="button" href="<?php echo esc_attr(tkt_article_details_url($article)) ?>">
                <?php echo esc_html(tkt_t('Plus d\'informations')); ?>
            </a>
        </div>
        <div class="col-sm-12 text-center mt-3 mb-3">
        <?php if (!$article->has_stock_for_salepoint($salepoint_id)) : ?>
            <span class="article-out-of-stock"><?php echo esc_html(tkt_t("Épuisé")) ?></span>
            </br>
        <?php else : ?>
            </br>
            <?php if ($data->add_to_cart_mode === ShopShortcode::ADD_TO_CART_MODE_POPUP || count($article->variants()) > 1) : ?>
            <div class="add-to-cart" data-component="Shop/Shop">
                <button class="button add-to-cart-from-shop">
                    <?php echo esc_html(tkt_t("Acheter")) ?>
                </button>
                </br>
                <div
                    class="buy-article-form"
                    style="display: none;"
                    data-component="BuyArticle/Form"
                    data-redirect="<?php echo esc_attr(TKTApp::get_instance()->get_config('cart.cart_redirect', 'none')) ?>"
                    data-cart-url="<?php echo esc_attr(tkt_cart_url()) ?>"
                    data-checkout-url="<?php echo esc_attr(tkt_checkout_url()) ?>"
                    data-article-id="<?php echo esc_attr($article->_id()) ?>"
                    data-salepoint-id="<?php echo esc_attr($salepoint_id) ?>"
                >
                    <section class="tkt-section tkt-<?php echo esc_attr($data->theme) ?>-section buy-section">
                    </section>
                </div>
            </div>
            <?php else : ?>
                <button
                    class="button direct-add-to-cart-from-shop"
                    data-component="BuyArticle/AddToCartButton"
                    data-redirect="<?php echo esc_attr(TKTApp::get_instance()->get_config('cart.cart_redirect', 'none')) ?>"
                    data-cart-url="<?php echo esc_attr(tkt_cart_url()) ?>"
                    data-checkout-url="<?php echo esc_attr(tkt_checkout_url()) ?>"
                    data-article-id="<?php echo esc_attr($article->_id()) ?>"
                    data-salepoint-id="<?php echo esc_attr($salepoint_id) ?>"
                >
                    <i class="glyphicon glyphicon-plus add-to-cart-indicator"></i>&nbsp;
                    <?php echo esc_html(tkt_t("Ajouter au panier")) ?>
                </button>
            <?php endif; ?>
        <?php endif; ?>
        </div>
    </div>
</div>
