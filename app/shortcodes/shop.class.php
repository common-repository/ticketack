<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;
use Ticketack\WP\TKTApp;
use Ticketack\Core\Models\Article;
use Ticketack\Core\Models\User;
use Ticketack\Core\Base\TKTApiException;
use Ticketack\Core\Base\No2_HTTP;
use Ticketack\Core\Base\TKTRequest;

/**
 * Shop shortcode
 *
 * Usage:
 *
 * [tkt_shop [category_ids="1,2,3"]]
 *
 */
class ShopShortcode extends TKTShortcode
{
    const LIST_TEMPLATE                 = 'list';
    const GRID_TEMPLATE                 = 'grid';
    const GALLERY_TEMPLATE              = 'gallery';
    const SLIDER_TEMPLATE               = 'slider';
    const DEFAULT_ITEM_WIDTH            = 12;
    const DEFAULT_NB_ARTICLES_BY_PAGE   = 9;
    const ADD_TO_CART_MODE_POPUP        = 'popup';
    const ADD_TO_CART_MODE_DIRECT       = 'direct';

    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_shop";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $theme            = isset($atts['theme']) ? $atts['theme'] : 'light';
        $template         = isset($atts['template']) ? $atts['template'] : static::LIST_TEMPLATE;
        $category_ids     = isset($atts['category_ids']) ? explode(',', $atts['category_ids']) : null;
        $tags             = isset($atts['tags']) ? explode(',', $atts['tags']) : null;
        $item_width       = isset($atts['item_width']) ? $atts['item_width'] : static::DEFAULT_ITEM_WIDTH;
        $hide_sorters     = isset($atts['hide_sorters']) ? !!$atts['hide_sorters'] : false;
        $only_in_stock    = isset($atts['only_in_stock']) ? $atts['only_in_stock'] : false;
        $nb               = isset($atts['nb']) ? $atts['nb'] : -1;
        $exclude          = isset($atts['exclude']) ? $atts['exclude'] : null;
        $sort             = isset($atts['sort']) ? $atts['sort'] : tkt_get_url_param('sort', Article::SORT_TYPE_SORT_WEIGHT);
        $show             = isset($atts['show_pagination']) ? $atts['show_pagination'] : true;
        $tkt_page         = isset($atts['tkt_page']) ? $atts['tkt_page'] : tkt_get_url_param('tkt_page', 1);
        $nb_by_page       = isset($atts['nb_by_page']) ? intval($atts['nb_by_page']) : static::DEFAULT_NB_ARTICLES_BY_PAGE;
        $add_to_cart_mode = isset($atts['add_to_cart_mode']) ? $atts['add_to_cart_mode'] : static::ADD_TO_CART_MODE_POPUP;

        // if sort is defined in the shortcode,
        // never show the sorters
        if (isset($atts['sort'])) {
            $hide_sorters = true;
        }

        $salepoint_id = TKTApp::get_instance()->get_config('ticketack.salepoint_id');

        try {
            $query = Article::all()->in_pos($salepoint_id);

            if (!empty($category_ids)) {
                $query = $query->in_category($category_ids);
            }

            if (!empty($tags)) {
                $query = $query->with_tags($tags);
            }

            $articles = $query->get('_id,name,short_description,description,category,stock_type,stocks,variants,posters,sort_weight');

            if ($only_in_stock) {
                $articles  = array_values(array_filter($articles, function ($article) use ($salepoint_id) {
                    return $article->has_stock_for_salepoint($salepoint_id);
                }));
            }

            if (!is_null($exclude)) {
                $articles = array_values(array_filter($articles, function ($article) use ($exclude) {
                    return $article->_id() != $exclude;
                }));
            }

            if (!is_null($sort)) {
                $articles = Article::sort($articles, $sort);
            }

            $nb = min($nb, count($articles));
            if ($nb > 0) {
                $articles = array_slice($articles, 0, $nb);
            }

            if ($nb_by_page > 0 && ($nb_by_page < $nb || $nb < 0)) {
                $total_page = ceil(count($articles) / $nb_by_page);
                $articles   = array_slice($articles, ($nb_by_page * ($tkt_page - 1)), $nb_by_page);
            } else {
                $show = false;
            }

            return TKTTemplate::render(
                'shop/'.$template.'/articles',
                (object)[
                    'theme'        => $theme,
                    'articles'     => array_chunk(
                        $articles,
                        (int)(12 / $item_width)
                    ),
                    'sort'              => $sort,
                    'hide_sorters'      => $hide_sorters,
                    'add_to_cart_mode'  => $add_to_cart_mode,
                    'pagination'        => (object)[
                        'show'          => $show,
                        'tkt_page'      => $tkt_page,
                        'total_page'    => $total_page,
                    ]
                ]
            );
        } catch (TKTApiException $e) {
            return sprintf(
                "Impossible de charger les articles : %s",
                $e->getMessage()
            );
        }
    }
}
