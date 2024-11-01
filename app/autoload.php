<?php

if (!defined('ABSPATH')) exit;

require_once(TKT_APP.'/app.class.php');
require_once(TKT_ACTIONS.'/action.class.php');
require_once(TKT_FILTERS.'/filter.class.php');
require_once(TKT_SHORTCODES.'/shortcode.class.php');
require_once(TKT_TEMPLATES.'/template.class.php');
require_once(TKT_HELPERS.'/sync_helper.class.php');
require_once(TKT_HELPERS.'/sync_articles_helper.class.php');
require_once(TKT_HELPERS.'/sync_people_helper.class.php');
require_once(TKT_HELPERS.'/locales_helper.class.php');
require_once(TKT_HELPERS.'/utils.inc.php');
require_once(TKT_LIB.'/base/api.class.php');
require_once(TKT_LIB.'/base/currency/money.class.php');
require_once(TKT_LIB.'/base/currency/chf.class.php');
require_once(TKT_LIB.'/base/currency/ars.class.php');
require_once(TKT_LIB.'/base/currency/cfc.class.php');
require_once(TKT_LIB.'/base/currency/currency.class.php');
require_once(TKT_LIB.'/base/currency/egp.class.php');
require_once(TKT_LIB.'/base/currency/eur.class.php');
require_once(TKT_LIB.'/base/currency/hkd.class.php');
require_once(TKT_LIB.'/base/currency/money.class.php');
require_once(TKT_LIB.'/base/currency/rub.class.php');
require_once(TKT_LIB.'/base/currency/usd.class.php');
require_once(TKT_LIB.'/base/currency/zar.class.php');
require_once(TKT_LIB.'/base/http.class.php');
require_once(TKT_LIB.'/base/model.class.php');
require_once(TKT_LIB.'/base/request.class.php');
require_once(TKT_LIB.'/models/bucket.class.php');
require_once(TKT_LIB.'/models/event.class.php');
require_once(TKT_LIB.'/models/movie.class.php');
require_once(TKT_LIB.'/models/place.class.php');
require_once(TKT_LIB.'/models/section.class.php');
require_once(TKT_LIB.'/models/pricing.class.php');
require_once(TKT_LIB.'/models/screening.class.php');
require_once(TKT_LIB.'/models/tickettype.class.php');
require_once(TKT_LIB.'/models/user.class.php');
require_once(TKT_LIB.'/models/window.class.php');
require_once(TKT_LIB.'/models/article.class.php');
require_once(TKT_LIB.'/models/article_stock.class.php');
require_once(TKT_LIB.'/models/article_variant.class.php');
require_once(TKT_LIB.'/models/articlecategory.class.php');
require_once(TKT_LIB.'/models/salepoint.class.php');
require_once(TKT_LIB.'/models/cashregister.class.php');
require_once(TKT_LIB.'/models/settings.class.php');

if (defined('WP_CLI')) {
    require_once(TKT_CLI.'/tkt_people_commands.class.php');
}
