<?php
namespace Ticketack\Core\Models;

use Ticketack\Core\Base\TKTModel;
use Ticketack\WP\TKTApp;

/**
 * Ticketack Engine Article.
 */
class Article extends TKTModel implements \JsonSerializable
{
    // - some eventival URLs ends with '?'
    const POSTER_REGEXP = '/\.(jpe?g|png|gif|webp|avif)(\?)?$/i';

    const STOCK_TYPE_ARTICLE = 'article';
    const STOCK_TYPE_VARIANT = 'variant';
    const STOCK_TYPE_NONE    = 'none';

    const SORT_TYPE_SORT_WEIGHT = 'sort_weight';
    const SORT_TYPE_ALPHA       = 'alpha';
    const SORT_TYPE_REV_ALPHA   = 'rev-alpha';
    const SORT_TYPE_INCR_PRICE  = 'incr-price';
    const SORT_TYPE_DECR_PRICE  = 'decr-price';
    const SORT_TYPE_RANDOM      = 'random';

    /**
     * @override
     */
    public static $resource = 'articles';

    protected $_id               = null;
    protected $name              = null;
    protected $short_description = null;
    protected $category          = null;
    protected $tags              = [];
    protected $sort_weight       = 0;
    protected $description       = null;
    protected $pos               = null;
    protected $stocks            = [];
    protected $stock_type        = null;
    protected $supplier          = null;
    protected $posters           = [];
    protected $variants          = [];

    /**
     * @return true if given id is a valid article id, false otherwise.
     */
    public static function is_valid_id($id)
    {
        return tkt_is_uuidv4($id);
    }

    /**
     * Filter articles by salepoint
     */
    public static function scope_in_pos($req, $pos_id)
    {
        return $req->query('pos_ids', $pos_id);
    }

    /**
     * Filter articles by ids
     */
    public static function scope_in_ids($req, $ids)
    {
        return $req->query('article_ids', $ids);
    }

    /**
     * Filter articles by variants
     */
    public static function scope_by_variant($req, $variant_ids)
    {
        if (!is_array($variant_ids)) {
            $variant_ids = [$variant_ids];
        }

        return $req->query('variant_ids', implode(',', $variant_ids));
    }

    /**
     * scope filtering articles by categories
     */
    public static function scope_in_category($req, $category_ids)
    {
        if (!is_array($category_ids)) {
            $category_ids = [$category_ids];
        }

        return $req->query('category_ids', implode(',', $category_ids));
    }

    /**
     * scope to filter by tags
     */
    public static function scope_with_tags($req, $tags)
    {
        if (!is_array($tags)) {
            $tags = [$tags];
        }

        return $req->query('tags', implode(',', $tags));
    }

    /**
     * Filter articles by gtin
     */
    public static function scope_by_gtin($req, $gtin)
    {
        return $req->query('gtin', $gtin);
    }

    /**
     * scope adding stocks for every salepoints in variants.
     */
    public static function scope_with_stocks_by_salepoint($req)
    {
        return $req->add_post_process(function ($status, $articles) {
            if ($status === 200) {
                $articles = array_map(function ($article) {
                    foreach ($article->variants() as $variant) {
                        if ($article->stock_type === static::STOCK_TYPE_ARTICLE) {
                            foreach ($article->stocks() as $stock) {
                                foreach ($stock->salepoint_ids() as $salepoint) {
                                    $variant->set_stocks_by_salepoint($salepoint, $stock->availability());
                                }
                            }
                        } elseif ($article->stock_type === static::STOCK_TYPE_VARIANT) {
                            foreach ($variant->stocks() as $stock) {
                                foreach ($stock->salepoint_ids() as $salepoint) {
                                    $variant->set_stocks_by_salepoint($salepoint, $stock->availability());
                                }
                            }
                        } else {
                            //
                        }
                    }
                    return $article;
                }, $articles);
            }
            return $articles;
        });
    }

    /**
     * @override
     * XXX: if you change something here, double check jsonSerialize() and
     * update the unit test
     */
    public function __construct(array &$properties = [])
    {
        if (array_key_exists('variants', $properties)) {
            $this->variants = [];
            foreach ($properties['variants'] as $obj) {
                array_push($this->variants, new ArticleVariant($this, $obj));
            }
            unset($properties['variants']);
        }
        if (array_key_exists('stocks', $properties)) {
            $this->stocks = [];
            foreach ($properties['stocks'] as $obj) {
                array_push($this->stocks, new ArticleStock($obj));
            }
            unset($properties['stocks']);
        }
        parent::__construct($properties);
    }

    public function _id()
    {
        return $this->_id;
    }

    public function name($lang = null)
    {
        if (is_null($lang)) {
            return $this->name;
        }
        if (isset($this->name[$lang])) {
            return $this->name[$lang];
        }

        $default_lang = TKTApp::get_instance()->get_config('i18n.default_lang', 'fr');
        if (isset($this->name[$default_lang])) {
            return $this->name[$default_lang];
        }

        return null;
    }

    public function category()
    {
        return $this->category;
    }

    public function short_description($lang = null)
    {
        if (is_null($lang)) {
            return $this->short_description;
        }

        if (isset($this->short_description[$lang])) {
            return $this->short_description[$lang];
        }

        $default_lang = TKTApp::get_instance()->get_config('i18n.default_lang', 'fr');
        if (isset($this->short_description[$default_lang])) {
            return $this->short_description[$default_lang];
        }

        return null;
    }

    public function tags()
    {
        return $this->tags;
    }

    public function description($lang = null)
    {
        if (is_null($lang)) {
            return $this->description;
        }

        if (isset($this->description[$lang])) {
            return $this->description[$lang];
        }

        $default_lang = TKTApp::get_instance()->get_config('i18n.default_lang', 'fr');
        if (isset($this->description[$default_lang])) {
            return $this->description[$default_lang];
        }

        return null;
    }

    public function sort_weight()
    {
        return intval($this->sort_weight);
    }

    public function pos()
    {
        return $this->pos;
    }

    public function stocks()
    {
        return $this->stocks;
    }

    public function supplier()
    {
        return $this->supplier;
    }

    public function variants()
    {
        return $this->variants;
    }

    public function has_stock_for_salepoint($salepoint_id)
    {
        switch ($this->stock_type) {
            case static::STOCK_TYPE_NONE:
                return true;
            case static::STOCK_TYPE_ARTICLE:
                foreach ($this->stocks as $s) {
                    if ($s->check_stock($salepoint_id)) {
                        return true;
                    }
                }

                return false;
            case static::STOCK_TYPE_VARIANT:
                foreach ($this->variants as $v) {
                    if ($v->has_stock_for_salepoint($salepoint_id)) {
                        return true;
                    }
                }

                return false;
            default:
                return false;
        }
    }
    /**
     * Get a variant by its _id
     *
     * @return ArticleVariant if found, null otherwise
     */
    public function variant($variant_id)
    {
        foreach ($this->variants as $v) {
            if ($v->_id() == $variant_id) {
                return $v;
            }
        };

        return null;
    }

    public function price($currency = null)
    {
        return $this->variants()[0]->price($currency);
    }

    public function value($currency = null)
    {
        return $this->variants()[0]->value($currency);
    }

    /**
     * Helper to access the opaque.posters attribute
     *
     * @return array: An array of objects with a "url" key.
     */
    public function posters()
    {
        if (!$this->posters) {
            return [];
        }

        $posters = array_values(array_filter(
            $this->posters,
            function ($poster) {
                if (is_array($poster) && array_key_exists('url', $poster)) {
                    return preg_match(static::POSTER_REGEXP, $poster['url']);
                }
                return false;
            }
        ));

        return array_map(function ($poster) {
            return (object)$poster;
        }, $posters);
    }

    public function stock_type()
    {
        return $this->stock_type;
    }

    /**
     * Helper to access the first poster in opaque.posters
     *
     * @return object: The first poster as an object with a "url" key,
     *         null if the opaque .posters is not set or empty
     */
    public function first_poster()
    {
        $posters = $this->posters();
        return !empty($posters) ? $posters[0] : null;
    }

    public function has_id()
    {
        return is_string($this->_id) && (strlen($this->_id) > 0);
    }

    public function has_description()
    {
        return is_array($this->description);
    }

    public function has_tags()
    {
        return is_array($this->tags) && !empty($this->tags);
    }

    public static function sort($articles, $sort_type)
    {
        if ($sort_type === static::SORT_TYPE_RANDOM) {
            shuffle($articles);
            return $articles;
        }

        $cmp = function ($a, $b) { return 0; };

        switch ($sort_type) {
            case static::SORT_TYPE_SORT_WEIGHT;
                $cmp = function ($a, $b) {
                    $weight_a = $a->sort_weight();
                    $weight_b = $b->sort_weight();
                    return $weight_a > $weight_b ? 1 : (
                        $weight_a < $weight_b ? -1 : 0
                    );
                };
                break;
            case static::SORT_TYPE_ALPHA;
                $cmp = function ($a, $b) {
                    $name_a = strtolower($a->name(TKT_LANG));
                    $name_b = strtolower($b->name(TKT_LANG));
                    return strcmp($name_a, $name_b);
                };
                break;
            case static::SORT_TYPE_REV_ALPHA;
                $cmp = function ($a, $b) {
                    $name_a = strtolower($a->name(TKT_LANG));
                    $name_b = strtolower($b->name(TKT_LANG));
                    return strcmp($name_b, $name_a);
                };
                break;
            case static::SORT_TYPE_INCR_PRICE;
                $cmp = function ($a, $b) {
                    $price_a = $a->price()->value();
                    $price_b = $b->price()->value();
                    return $price_a > $price_b ? 1 : (
                        $price_a < $price_b ? -1 : 0
                    );
                };
                break;
            case static::SORT_TYPE_DECR_PRICE;
                $cmp = function ($a, $b) {
                    $price_a = $a->price()->value();
                    $price_b = $b->price()->value();
                    return $price_a < $price_b ? 1 : (
                        $price_a > $price_b ? -1 : 0
                    );
                };
                break;
        }

        usort($articles, $cmp);

        return $articles;
    }

    /**
     * Handle properties JSONification, like DateTime to ISO8601.
     */
    public function jsonSerialize() : mixed
    {
        $ret = [
            'name'              => $this->name,
            'short_description' => !empty($this->short_description) ? $this->short_description : [],
            'category'          => $this->category(),
            'sort_weight'       => $this->sort_weight(),
            'pos'               => $this->pos(),
            'stocks'            => $this->stocks(),
            'stock_type'        => $this->stock_type(),
            'supplier'          => $this->supplier(),
            'posters'           => $this->posters(),
            'variants'          => !empty($this->variants) ? $this->variants : []
        ];

        if ($this->has_id()) {
            $ret['_id'] = $this->_id();
        }

        if ($this->has_tags()) {
            $ret['tags'] = $this->tags();
        }

        if ($this->has_description()) {
            $ret['description'] = $this->description;
        }

        return $ret;
    }
}
