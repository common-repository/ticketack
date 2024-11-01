<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Helpers\SyncHelper;
use ScssPhp\ScssPhp\Compiler;

/**
 * Utils functions
 */

/**
 * Extract url param
 *•
 * @param string $key: The param name
 * @param mixed $default: The default value if the url param is not found
 *
 * @return mixed: The URL param if found, $default otherwise
 */
function tkt_get_url_param($key, $default = null)
{
    return array_key_exists($key, $_GET) ? sanitize_text_field($_GET[$key]) : $default;
}

/**
 * Formats a DateTime in a full date and time format
 *
 * @param $dt
 *   The DateTime object to format.
 *
 * @return
 *   A string.
 */
function tkt_datetime_to_s($dt)
{
    $fmt = "j F Y H:i";
    return wp_date($fmt, $dt->getTimestamp());
}

/**
 * Formats a DateTime in a full date format
 *
 * @param $dt
 *   The DateTime object to format.
 *
 * @return
 *   A string.
 */
function tkt_date_to_s($dt)
{
    $fmt = "j F Y";
    return wp_date($fmt, $dt->getTimestamp());
}

/**
 * Formats a DateTime in a minimal date format
 *
 * @param $dt
 *   The DateTime object to format.
 *
 * @return
 *   A string.
 */
function tkt_date_to_min_s($dt)
{
    $fmt = "j F";
    return wp_date($fmt, $dt->getTimestamp());
}

/**
 * Formats a DateTime in a minimal date and time format
 *
 * @param $dt
 *   The DateTime object to format.
 *
 * @return
 *   A string.
 */
function tkt_date_and_time_to_min_s($dt)
{
    $fmt = "j F H:i";
    return wp_date($fmt, $dt->getTimestamp());
}

/**
 * Formats a DateTime in a hour format
 *
 * @param $dt
 *   The DateTime object to format.
 *
 * @return
 *   A string.
 */
function tkt_date_and_time_to_time_s($dt)
{
    $fmt = "H:i";
    return wp_date($fmt, $dt->getTimestamp());
}

/**
 * convert an ISO-8601 formated string to a PHP DateTime object.
 *
 * see http://stackoverflow.com/questions/14849446/php-parse-date-in-iso-format
 *
 * @bugs
 *   Milliseconds will be lost.
 *
 * @return
 *   A DateTime object or false on error.
 */
function tkt_iso8601_to_datetime($str)
{
    $i = strtotime($str);
    $d = new \DateTime();
    return $d->setTimestamp($i);
}

/**
 * convert a PHP DateTime object to an ISO-8601 string
 *
 * @see
 *   http://php.net/manual/en/class.datetime.php#datetime.constants.iso8601
 *
 * @return
 *   A string.
 */
function tkt_datetime_to_iso8601($d)
{
    return $d->format(\DateTime::ATOM);
}

/**
 * Get a plugin asset url
 *
 * @param string $path: The file path, relative to plugin root dir
 *
 * @return string
 */
function tkt_assets_url($path)
{
    return plugin_dir_url( TKT_APP ) . 'front/' . $path . '?v=' . TKT_ASSETS_VERSION;

}
/**
 * Get a link to a page by its slug, in the current language
 *
 * @param string $slug in the default language
 * @param string $query
 *
 * @return string
 */
function tkt_page_url($slug, $query = "")
{
    $url = get_site_url(/*$blog_id*/null, $slug);

    if (TKT_WPML_INSTALLED) {
        // get the page in default language
        $page   = get_page_by_path($slug, OBJECT, 'page');
        $pageId = $page->ID;
        if (tkt_current_lang() != tkt_default_lang()) {
            // get the slug in current language
            $pageId = tkt_translated_id_by_id($page->ID, 'page', tkt_current_lang(), $slug);
        }

        $url = apply_filters('wpml_permalink', get_permalink($pageId));
    }

    return sprintf('%s%s', $url, (!empty($query) ? '?'.$query : ''));
}

/**
 * Get the Program page url
 *
 * @param string $query
 *
 * @return string
 */
function tkt_program_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.program');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Get the Ticket View page url
 *
 * @param string $query
 *
 * @return string
 */
function tkt_ticket_view_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.ticket_view');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Get the Shop page url
 *
 * @param string $query
 *
 * @return string
 */
function tkt_shop_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.shop');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Get the Cart page url
 *
 * @param string $query
 *
 * @return string
 */
function tkt_cart_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.cart');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Get the Checkout page url
 *
 * @param string $query
 *
 * @return string
 */
function tkt_checkout_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.checkout');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Get the Thank you page url
 *
 * @param string $query
 *
 * @return string
 */
function tkt_thank_you_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.thank_you');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Get the eshop buy pass page url
 *
 * @return string
 */
function tkt_buy_pass_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.pass');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Get the Cart page url
 *
 * @return string
 */
function tkt_cart_reset_url()
{
    return sprintf(
        "%s/cart/reset",
        TKTApp::get_instance()->get_config('ticketack.eshop_uri')
    );
}

/**
 * Get an event details url
 *
 * @param Event $event
 *
 * @return string
 */
function tkt_event_details_url($event)
{
    if (TKT_WPML_INSTALLED) {
        $slug = tkt_get_event_slug($event, TKT_LANG);
        $page = get_page_by_path($slug, OBJECT, 'tkt-event');
        return apply_filters('wpml_permalink', get_permalink($page->ID));
    }

    return get_site_url(
        /*$blog_id*/null,
        sprintf(
            '%s/%s',
            'events',
          tkt_get_event_slug($event, TKT_LANG)
        )
    );
}

/**
 * Get an event book url
 *
 * @param Event $event
 * @param Screening $screening: pre-selected screening
 *
 * @return string
 */
function tkt_event_book_url($event, $screening = null)
{
    if (TKT_WPML_INSTALLED) {
        $slug = tkt_get_event_slug($event, TKT_LANG);
        $page = get_page_by_path($slug, OBJECT, 'tkt-event');

        return sprintf(
            "%s?book=1%s",
            apply_filters('wpml_permalink', get_permalink($page->ID)),
            (!is_null($screening) ? '&s_id='.$screening->_id() : '')
        );
    }

    return get_site_url(
        /*$blog_id*/null,
        sprintf(
            "%s/%s/?book=1%s",
            'events',
        tkt_get_event_slug($event, TKT_LANG),
            (!is_null($screening) ? '&s_id='.$screening->_id() : '')
        )
    );
}

/**
 * Get an article details url
 *
 * @param Article $article
 *
 * @return string
 */
function tkt_article_details_url($article)
{
    if (TKT_WPML_INSTALLED) {
        $slug = tkt_get_article_slug($article, TKT_LANG);
        $page = get_page_by_path($slug, OBJECT, 'tkt-article');
        if ($page) {
            return apply_filters('wpml_permalink', get_permalink($page->ID));
        }
    }

    return get_site_url(
        /*$blog_id*/null,
        sprintf(
            '%s/%s',
            'articles',
          tkt_get_article_slug($article, TKT_LANG)
        )
    );
}

/**
 * Get the login page url
 *
 * @param string $query
 *
 * @return string
 */
function tkt_login_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.login');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Get the registration page url
 *
 * @param string $query
 *
 * @return string
 */
function tkt_registration_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.registration');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Get the lost password page url
 *
 * @param string $query
 *
 * @return string
 */
function tkt_lostpassword_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.lostpassword');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Get the change password page url
 *
 * @param string $query
 *
 * @return string
 */
function tkt_changepassword_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.changepassword');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Get the user account page url
 *
 * @param string $query
 *
 * @return string
 */
function tkt_user_account_url($query = "")
{
    $slug = TKTApp::get_instance()->get_config('pages.account');
    return $slug ? tkt_page_url($slug, $query) : null;
}

/**
 * Extract a Youtube video ID from an URL
 * See https://gist.github.com/ghalusa/6c7f3a00fd2383e5ef33
 *
 * @param string: Youtube video url
 *
 * @return string: Video ID
 */
function tkt_yt_video_id($yt_url)
{
    // Here is a sample of the URLs this regex matches: (there can be more content after the given URL that will be ignored)
    // http://youtu.be/dQw4w9WgXcQ
    // http://www.youtube.com/embed/dQw4w9WgXcQ
    // http://www.youtube.com/watch?v=dQw4w9WgXcQ
    // http://www.youtube.com/?v=dQw4w9WgXcQ
    // http://www.youtube.com/v/dQw4w9WgXcQ
    // http://www.youtube.com/e/dQw4w9WgXcQ
    // http://www.youtube.com/user/username#p/u/11/dQw4w9WgXcQ
    // http://www.youtube.com/sandalsResorts#p/c/54B8C800269D7C1B/0/dQw4w9WgXcQ
    // http://www.youtube.com/watch?feature=player_embedded&v=dQw4w9WgXcQ
    // http://www.youtube.com/?feature=player_embedded&v=dQw4w9WgXcQ
    // It also works on the youtube-nocookie.com URL with the same above options.
    // It will also pull the ID from the URL in an embed code (both iframe and object tags)
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $yt_url, $match);

    return $match[1];
}

/**
 * Helper function to allow to make calls
 * on an object using the __construct() result like
 * $my_obj = tkt_id(new MyObj())->chainable_method();
 */
function tkt_id($obj)
{
    return $obj;
}

if (!function_exists('tkt_h')) {
    /**
     * Sanitize a string for display.
     *
     * Escape HTML tags for a safer display. This function assume UTF-8
     * encoding.
     *
     * @param $string
     *   The unsafe string.
     *
     * @return
     *   a string that can be safely presented to echo or print for output.
     */
    function tkt_h($string)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

if (!function_exists('tkt_html')) {
    /**
     * Sanitize a string for display but allows some html tags.
     *
     * Less safe than tkt_h() which strips everything.
     *
     * Note: This function does not modify any attributes on the tags that are
     * allowed, including the style and onmouseover attributes that a mischievous
     * user may abuse when posting text that will be shown to other users.
     *
     * @param $string
     *   The unsafe string.
     *
     * @param $tags_to_keep
     *   Tags to keep in strip tags
     *
     * @return
     *   a string that can be mostly safely presented to echo or print for output.
     */
    function tkt_html($string, $tags_to_keep = '<p><br><b><strong><i><em><a>')
    {
        return strip_tags($string, $tags_to_keep);
    }
}

if (!function_exists('tkt_allowed_tags')) {
    /**
     * Return a list of allowed html tags
     *
     * @return an array
     */
    function tkt_allowed_tags()
    {
        global $allowedposttags;
        $tags = $allowedposttags;

        $tags['form'] = array(
            'action'         => true,
            'accept'         => true,
            'accept-charset' => true,
            'enctype'        => true,
            'method'         => true,
            'name'           => true,
            'target'         => true,
        );
        $tags['script'] = ['type' => true];

        return $tags;
    }
}

function tkt_get_ages()
{
    $array = [tkt_t("Pas de réponse"), "< 5"];
    for ($i=5 ; $i<=80 ; $i++) {
           $array[] = $i;
    }
    $array[] = "> 80";
    return $array;
}

function tkt_get_sexes()
{
    return ['n/c' => tkt_t("Pas de réponse"), 'f' => tkt_t("Féminin"), 'm' => tkt_t("Masculin"), 'o' => tkt_t("Autre")];
}

function tkt_get_countries()
{
    return [
        ['en' => 'Unknown','fr' => 'Inconnu','de' => 'Unknown'],['en' => 'Afghanistan','fr' => 'Afghanistan','de' => 'Afghanistan'],['en' => 'Albania','fr' => 'Albanie','de' => 'Albanien'],['en' => 'Algeria','fr' => 'Algérie','de' => 'Algerien'],['en' => 'American Samoa','fr' => 'Samoa Américaines','de' => 'Amerikanisch-Samoa'],['en' => 'Andorra','fr' => 'Andorre','de' => 'Andorra'],['en' => 'Angola','fr' => 'Angola','de' => 'Angola'],['en' => 'Anguilla','fr' => 'Anguilla','de' => 'Anguilla'],['en' => 'Antarctica','fr' => 'Antarctique','de' => 'Antarktis'],['en' => 'Antigua and Barbuda','fr' => 'Antigua-et-barbuda','de' => 'Antigua and Barbuda'],['en' => 'Argentina','fr' => 'Argentine','de' => 'Argentinien'],['en' => 'Armenia','fr' => 'Arménie','de' => 'Armenien'],['en' => 'Aruba','fr' => 'Aruba','de' => 'Aruba'],['en' => 'Australia','fr' => 'Australie','de' => 'Australien'],['en' => 'Austria','fr' => 'Autriche','de' => 'Österreich'],['en' => 'Azerbaijan','fr' => 'AzerbaÏdjan','de' => 'Azerbaijan'],['en' => 'Bahamas','fr' => 'Bahamas','de' => 'Bahamas'],['en' => 'Bahrain','fr' => 'BahreÏn','de' => 'Bahrain'],['en' => 'Bangladesh','fr' => 'Bangladesh','de' => 'Bangladesch'],['en' => 'Barbados','fr' => 'Barbade','de' => 'Barbados'],['en' => 'Belarus','fr' => 'Bélarus','de' => 'Belarus'],['en' => 'Belgium','fr' => 'Belgique','de' => 'Belgien'],['en' => 'Belize','fr' => 'Belize','de' => 'Belize'],['en' => 'Benin','fr' => 'Bénin','de' => 'Benin'],['en' => 'Bermuda','fr' => 'Bermudes','de' => 'Bermuda'],['en' => 'Bhutan','fr' => 'Bhoutan','de' => 'Bhutan'],['en' => 'Bolivia','fr' => 'Bolivie, L\'État Plurinational De','de' => 'Bolivien'],['en' => 'Bosnia and Herzegovina','fr' => 'Bosnie-herzégovine','de' => 'Bosnien/Herzegowina'],['en' => 'Botswana','fr' => 'Botswana','de' => 'Botswana'],['en' => 'Bouvet Island','fr' => 'Bouvet, Île','de' => 'Bouvetinsel'],['en' => 'Brazil','fr' => 'Brésil','de' => 'Brasilien'],['en' => 'British Indian Ocean Territory','fr' => 'Océan Indien, Territoire Britannique De L\'','de' => 'British Indian Ocean Territor'],['en' => 'Brunei Darussalam','fr' => 'Brunéi Darussalam','de' => 'Brunei Darussalam'],['en' => 'Bulgaria','fr' => 'Bulgarie','de' => 'Bulgarien'],['en' => 'Burkina Faso','fr' => 'Burkina Faso','de' => 'Burkina Faso'],['en' => 'Burundi','fr' => 'Burundi','de' => 'Burundi'],['en' => 'Cambodia','fr' => 'Cambodge','de' => 'Cambodia'],['en' => 'Cameroon','fr' => 'Cameroun','de' => 'Cameroon, United Republic of'],['en' => 'Canada','fr' => 'Canada','de' => 'Kanada'],['en' => 'Cape Verde','fr' => 'Cap-vert','de' => 'Cape Verde'],['en' => 'Cayman Islands','fr' => 'CaÏmanes, Îles','de' => 'Cayman Islands'],['en' => 'Central African Republic','fr' => 'Centrafricaine, République','de' => 'Central African Republic'],['en' => 'Chad','fr' => 'Tchad','de' => 'Chad'],['en' => 'Chile','fr' => 'Chili','de' => 'Chile'],['en' => 'China','fr' => 'Chine','de' => 'China'],['en' => 'Christmas Island','fr' => 'Christmas, Île','de' => 'Christmas Island'],['en' => 'Cocos (Keeling) Islands','fr' => 'Cocos (keeling), Îles','de' => 'Cocos (Keeling) Islands'],['en' => 'Colombia','fr' => 'Colombie','de' => 'Kolumbien'],['en' => 'Comoros','fr' => 'Comores','de' => 'Comoros'],['en' => 'Congo','fr' => 'Congo','de' => 'Kongo'],['en' => 'Congo, the Democratic Republic of the','fr' => 'Congo, La République Démocratique Du','de' => 'Congo, the Democratic Republic of the'],['en' => 'Cook Islands','fr' => 'Cook, Îles','de' => 'Cook Islands'],['en' => 'Costa Rica','fr' => 'Costa Rica','de' => 'Costa Rica'],['en' => 'Cote D\'Ivoire','fr' => 'Côte D\'ivoire','de' => 'Elfenbeinküste'],['en' => 'Croatia','fr' => 'Croatie','de' => 'Kroatien'],['en' => 'Cuba','fr' => 'Cuba','de' => 'Kuba'],['en' => 'Cyprus','fr' => 'Chypre','de' => 'Zypern'],['en' => 'Czech Republic','fr' => 'Tchèque, République','de' => 'Tschechische Republik'],['en' => 'Denmark','fr' => 'Danemark','de' => 'Dänemark'],['en' => 'Djibouti','fr' => 'Djibouti','de' => 'Dschibuti'],['en' => 'Dominica','fr' => 'Dominique','de' => 'Dominica'],['en' => 'Dominican Republic','fr' => 'Dominicaine, République','de' => 'Dominikanische Republik'],['en' => 'Ecuador','fr' => 'équateur','de' => 'Ecuador'],['en' => 'Egypt','fr' => 'Égypte','de' => 'Ägypten'],['en' => 'El Salvador','fr' => 'El Salvador','de' => 'El Salvador'],['en' => 'Equatorial Guinea','fr' => 'Guinée équatoriale','de' => 'Equatorial Guinea'],['en' => 'Eritrea','fr' => 'Érythrée','de' => 'Eritrea'],['en' => 'Estonia','fr' => 'Estonie','de' => 'Estland'],['en' => 'Ethiopia','fr' => 'Éthiopie','de' => 'Äthiopien'],['en' => 'Falkland Islands (Malvinas)','fr' => 'Falkland, Îles (malvinas)','de' => 'Falkland Islands (Malvinas)'],['en' => 'Faroe Islands','fr' => 'Féroé, Îles','de' => 'Faroe Islands'],['en' => 'Fiji','fr' => 'Fidji','de' => 'Fiji'],['en' => 'Finland','fr' => 'Finlande','de' => 'Finnland'],['en' => 'France','fr' => 'France','de' => 'Frankreich'],['en' => 'French Guiana','fr' => 'Guyane FranÇaise','de' => 'Französisch-Guyana'],['en' => 'French Polynesia','fr' => 'Polynésie FranÇaise','de' => 'Französisch-Polynesien'],['en' => 'French Southern Territories','fr' => 'Terres Australes FranÇaises','de' => 'French Southern Territories'],['en' => 'Gabon','fr' => 'Gabon','de' => 'Gabun'],['en' => 'Gambia','fr' => 'Gambie','de' => 'Gambia'],['en' => 'Georgia','fr' => 'Géorgie','de' => 'Georgien'],['en' => 'Germany','fr' => 'Allemagne','de' => 'Deutschland'],['en' => 'Ghana','fr' => 'Ghana','de' => 'Ghana'],['en' => 'Gibraltar','fr' => 'Gibraltar','de' => 'Gibraltar'],['en' => 'Greece','fr' => 'Grèce','de' => 'Griechenland'],['en' => 'Greenland','fr' => 'Groenland','de' => 'Grönland'],['en' => 'Grenada','fr' => 'Grenade','de' => 'Grenada'],['en' => 'Guadeloupe','fr' => 'Guadeloupe','de' => 'Guadeloupe'],['en' => 'Guam','fr' => 'Guam','de' => 'Guam'],['en' => 'Guatemala','fr' => 'Guatemala','de' => 'Guatemala'],['en' => 'Guinea','fr' => 'Guinée','de' => 'Guinea'],['en' => 'Guinea-Bissau','fr' => 'Guinée-bissau','de' => 'Guinea-Bissau'],['en' => 'Guyana','fr' => 'Guyana','de' => 'Guyana'],['en' => 'Haiti','fr' => 'HaÏti','de' => 'Haiti'],['en' => 'Heard Island and Mcdonald Islands','fr' => 'Heard, Île Et Mcdonald, Îles','de' => 'Heard and MacDonald Islands'],['en' => 'Holy See (Vatican City State)','fr' => 'Saint-siège (État De La Cité Du Vatican)','de' => 'Vatikanstadt'],['en' => 'Honduras','fr' => 'Honduras','de' => 'Honduras'],['en' => 'Hong Kong','fr' => 'Hong-kong','de' => 'Hong Kong'],['en' => 'Hungary','fr' => 'Hongrie','de' => 'Ungarn'],['en' => 'Iceland','fr' => 'Islande','de' => 'Island'],['en' => 'India','fr' => 'Inde','de' => 'Indien'],['en' => 'Indonesia','fr' => 'Indonésie','de' => 'Indonesien'],['en' => 'Iran, Islamic Republic of','fr' => 'Iran, République Islamique D\'','de' => 'Iran (Islamic Republic of)'],['en' => 'Iraq','fr' => 'Irak','de' => 'Irak'],['en' => 'Ireland','fr' => 'Irlande','de' => 'Irland'],['en' => 'Israel','fr' => 'Israël','de' => 'Israel'],['en' => 'Italy','fr' => 'Italie','de' => 'Italien'],['en' => 'Jamaica','fr' => 'JamaÏque','de' => 'Jamaica'],['en' => 'Japan','fr' => 'Japon','de' => 'Japan'],['en' => 'Jordan','fr' => 'Jordanie','de' => 'Jordanien'],['en' => 'Kazakhstan','fr' => 'Kazakhstan','de' => 'Kazakhstan'],['en' => 'Kenya','fr' => 'Kenya','de' => 'Kenia'],['en' => 'Kiribati','fr' => 'Kiribati','de' => 'Kiribati'],['en' => 'Korea, Democratic People\'s Republic of','fr' => 'Corée, République Populaire Démocratique De','de' => 'Korea, Democratic People s Re'],['en' => 'Korea, Republic of','fr' => 'Corée, République De','de' => 'Korea, Republic of'],['en' => 'Kuwait','fr' => 'KoweÏt','de' => 'Kuwait'],['en' => 'Kyrgyzstan','fr' => 'Kirghizistan','de' => 'Kyrgystan'],['en' => 'Lao People\'s Democratic Republic','fr' => 'Lao, République Démocratique Populaire','de' => 'Lao People s Democratic Repub'],['en' => 'Latvia','fr' => 'Lettonie','de' => 'Lettland'],['en' => 'Lebanon','fr' => 'Liban','de' => 'Lebanon'],['en' => 'Lesotho','fr' => 'Lesotho','de' => 'Lesotho'],['en' => 'Liberia','fr' => 'Libéria','de' => 'Liberia'],['en' => 'Libyan Arab Jamahiriya','fr' => 'Libyenne, Jamahiriya Arabe','de' => 'Libyan Arab Jamahiriya'],['en' => 'Liechtenstein','fr' => 'Liechtenstein','de' => 'Liechtenstein'],['en' => 'Lithuania','fr' => 'Lituanie','de' => 'Litauen'],['en' => 'Luxembourg','fr' => 'Luxembourg','de' => 'Luxemburg'],['en' => 'Macao','fr' => 'Macao','de' => 'Macau'],['en' => 'Macedonia, the Former Yugoslav Republic of','fr' => 'Macédoine, L\'ex-république Yougoslave De','de' => 'Makedonien'],['en' => 'Madagascar','fr' => 'Madagascar','de' => 'Madagascar'],['en' => 'Malawi','fr' => 'Malawi','de' => 'Malawi'],['en' => 'Malaysia','fr' => 'Malaisie','de' => 'Malaysia'],['en' => 'Maldives','fr' => 'Maldives','de' => 'Maldives'],['en' => 'Mali','fr' => 'Mali','de' => 'Mali'],['en' => 'Malta','fr' => 'Malte','de' => 'Malta'],['en' => 'Marshall Islands','fr' => 'Marshall, Îles','de' => 'Marshall Islands'],['en' => 'Martinique','fr' => 'Martinique','de' => 'Martinique'],['en' => 'Mauritania','fr' => 'Mauritanie','de' => 'Mauritania'],['en' => 'Mauritius','fr' => 'Maurice','de' => 'Mauritius'],['en' => 'Mayotte','fr' => 'Mayotte','de' => 'Mayotte'],['en' => 'Mexico','fr' => 'Mexique','de' => 'Mexico'],['en' => 'Micronesia, Federated States of','fr' => 'Micronésie, États Fédérés De','de' => 'Micronesia'],['en' => 'Moldova, Republic of','fr' => 'Moldavie, République De','de' => 'Moldawien, Republik'],['en' => 'Monaco','fr' => 'Monaco','de' => 'Monaco'],['en' => 'Mongolia','fr' => 'Mongolie','de' => 'Mongolei'],['en' => 'Montserrat','fr' => 'Montserrat','de' => 'Montserrat'],['en' => 'Morocco','fr' => 'Maroc','de' => 'Marokko'],['en' => 'Mozambique','fr' => 'Mozambique','de' => 'Mozambique'],['en' => 'Myanmar','fr' => 'Myanmar','de' => 'Myanmar'],['en' => 'Namibia','fr' => 'Namibie','de' => 'Namibia'],['en' => 'Nauru','fr' => 'Nauru','de' => 'Nauru'],['en' => 'Nepal','fr' => 'Népal','de' => 'Nepal'],['en' => 'Netherlands','fr' => 'Pays-bas','de' => 'Niederlande'],['en' => 'Netherlands Antilles','fr' => 'Antilles Néerlandaises','de' => 'Netherlands Antilles'],['en' => 'New Caledonia','fr' => 'Nouvelle-calédonie','de' => 'New Caledonia'],['en' => 'New Zealand','fr' => 'Nouvelle-zélande','de' => 'Neuseeland'],['en' => 'Nicaragua','fr' => 'Nicaragua','de' => 'Nicaragua'],['en' => 'Niger','fr' => 'Niger','de' => 'Niger'],['en' => 'Nigeria','fr' => 'Nigéria','de' => 'Nigeria'],['en' => 'Niue','fr' => 'Niué','de' => 'Niue'],['en' => 'Norfolk Island','fr' => 'Norfolk, Île','de' => 'Norfolk Island'],['en' => 'Northern Mariana Islands','fr' => 'Mariannes Du Nord, Îles','de' => 'Northern Mariana Islands'],['en' => 'Norway','fr' => 'Norvège','de' => 'Norwegen'],['en' => 'Oman','fr' => 'Oman','de' => 'Oman'],['en' => 'Pakistan','fr' => 'Pakistan','de' => 'Pakistan'],['en' => 'Palau','fr' => 'Palaos','de' => 'Palau'],['en' => 'Palestinian Territory, Occupied','fr' => 'Palestinien Occupé, Territoire','de' => 'Palestinian Territory, Occupied'],['en' => 'Panama','fr' => 'Panama','de' => 'Panama'],['en' => 'Papua New Guinea','fr' => 'Papouasie-nouvelle-guinée','de' => 'Papua New Guinea'],['en' => 'Paraguay','fr' => 'Paraguay','de' => 'Paraguay'],['en' => 'Peru','fr' => 'Pérou','de' => 'Peru'],['en' => 'Philippines','fr' => 'Philippines','de' => 'Philippines'],['en' => 'Pitcairn','fr' => 'Pitcairn','de' => 'Pitcairn'],['en' => 'Poland','fr' => 'Pologne','de' => 'Polen'],['en' => 'Portugal','fr' => 'Portugal','de' => 'Portugal'],['en' => 'Puerto Rico','fr' => 'Porto Rico','de' => 'Puerto Rico'],['en' => 'Qatar','fr' => 'Qatar','de' => 'Qatar'],['en' => 'Reunion','fr' => 'Réunion','de' => 'Reunion'],['en' => 'Romania','fr' => 'Roumanie','de' => 'Rumänien'],['en' => 'Russian Federation','fr' => 'Russie, Fédération De','de' => 'Rußland'],['en' => 'Rwanda','fr' => 'Rwanda','de' => 'Rwanda'],['en' => 'Saint Helena','fr' => 'Sainte-hélène, Ascension Et Tristan Da Cunha','de' => 'Saint Helena'],['en' => 'Saint Kitts and Nevis','fr' => 'Saint-kitts-et-nevis','de' => 'Saint Kitts and Nevis'],['en' => 'Saint Lucia','fr' => 'Sainte-lucie','de' => 'Saint Lucia'],['en' => 'Saint Pierre and Miquelon','fr' => 'Saint-pierre-et-miquelon','de' => 'Saint Pierre and Miquelon'],['en' => 'Saint Vincent and the Grenadines','fr' => 'Saint-vincent-et-les Grenadines','de' => 'Saint Vincent and the Grenadi'],['en' => 'Samoa','fr' => 'Samoa','de' => 'Western Samoa'],['en' => 'San Marino','fr' => 'Saint-marin','de' => 'San Marino'],['en' => 'Sao Tome and Principe','fr' => 'Sao Tomé-et-principe','de' => 'Sao Tome and Principe'],['en' => 'Saudi Arabia','fr' => 'Arabie Saoudite','de' => 'Saudi Arabia'],['en' => 'Senegal','fr' => 'Sénégal','de' => 'Senegal'],['en' => 'Serbia and Montenegro','fr' => 'Serbie et Monténégro','de' => 'Serbia and Montenegro'],['en' => 'Seychelles','fr' => 'Seychelles','de' => 'Seychelles'],['en' => 'Sierra Leone','fr' => 'Sierra Leone','de' => 'Sierra Leone'],['en' => 'Singapore','fr' => 'Singapour','de' => 'Singapore'],['en' => 'Slovakia','fr' => 'Slovaquie','de' => 'Slovakei'],['en' => 'Slovenia','fr' => 'Slovénie','de' => 'Slowenien'],['en' => 'Solomon Islands','fr' => 'Salomon, Îles','de' => 'Solomon Islands'],['en' => 'Somalia','fr' => 'Somalie','de' => 'Somalia'],['en' => 'South Africa','fr' => 'Afrique Du Sud','de' => 'Südafrika'],['en' => 'South Georgia and the South Sandwich Islands','fr' => 'Géorgie Du Sud Et Les Îles Sandwich Du Sud','de' => 'South Georgia and the South Sandwich Islands'],['en' => 'Spain','fr' => 'Espagne','de' => 'Spanien'],['en' => 'Sri Lanka','fr' => 'Sri Lanka','de' => 'Sri Lanka'],['en' => 'Sudan','fr' => 'Soudan','de' => 'Sudan'],['en' => 'Suriname','fr' => 'Suriname','de' => 'Surinam'],['en' => 'Svalbard and Jan Mayen','fr' => 'Svalbard Et Île Jan Mayen','de' => 'Svalbard and Jan Mayen Island'],['en' => 'Swaziland','fr' => 'Swaziland','de' => 'Swaziland'],['en' => 'Sweden','fr' => 'Suède','de' => 'Schweden'],['en' => 'Switzerland','fr' => 'Suisse','de' => 'Schweiz'],['en' => 'Syrian Arab Republic','fr' => 'Syrienne, République Arabe','de' => 'Syrian Arab Republic'],['en' => 'Taiwan, Province of China','fr' => 'TaÏwan, Province De Chine','de' => 'Taiwan, Province of China'],['en' => 'Tajikistan','fr' => 'Tadjikistan','de' => 'Tajikistan'],['en' => 'Tanzania, United Republic of','fr' => 'Tanzanie, République-unie De','de' => 'Tanzania, United Republic of'],['en' => 'Thailand','fr' => 'ThaÏlande','de' => 'Thailand'],['en' => 'Timor-Leste','fr' => 'Timor-leste','de' => 'Timor-Leste'],['en' => 'Togo','fr' => 'Togo','de' => 'Togo'],['en' => 'Tokelau','fr' => 'Tokelau','de' => 'Tokelau'],['en' => 'Tonga','fr' => 'Tonga','de' => 'Tonga'],['en' => 'Trinidad and Tobago','fr' => 'Trinité-et-tobago','de' => 'Trinidad and Tobago'],['en' => 'Tunisia','fr' => 'Tunisie','de' => 'Tunesien'],['en' => 'Turkey','fr' => 'Turquie','de' => 'Türkei'],['en' => 'Turkmenistan','fr' => 'Turkménistan','de' => 'Turkemistan'],['en' => 'Turks and Caicos Islands','fr' => 'Turks Et CaÏques, Îles','de' => 'Turks and Caicos Islands'],['en' => 'Tuvalu','fr' => 'Tuvalu','de' => 'Tuvalu'],['en' => 'Uganda','fr' => 'Ouganda','de' => 'Uganda'],['en' => 'Ukraine','fr' => 'Ukraine','de' => 'Ukraine'],['en' => 'United Arab Emirates','fr' => 'émirats Arabes Unis','de' => 'United Arab Emirates'],['en' => 'United Kingdom','fr' => 'Royaume-uni','de' => 'Vereinigtes Königreich'],['en' => 'United States','fr' => 'États-unis','de' => 'Vereinigte Staaten von Amerik'],['en' => 'United States Minor Outlying Islands','fr' => 'Îles Mineures éloignées Des États-unis','de' => 'United States Minor Outlying'],['en' => 'Uruguay','fr' => 'Uruguay','de' => 'Uruguay'],['en' => 'Uzbekistan','fr' => 'Ouzbékistan','de' => 'Uzbekistan'],['en' => 'Vanuatu','fr' => 'Vanuatu','de' => 'Vanuatu'],['en' => 'Venezuela','fr' => 'Venezuela, République Bolivarienne Du','de' => 'Venezuela'],['en' => 'Viet Nam','fr' => 'Viêt-Nam','de' => 'Vietnam'],['en' => 'Virgin Islands, British','fr' => 'Îles Vierges Britanniques','de' => 'Virgin Islands (British)'],['en' => 'Virgin Islands, U.s.','fr' => 'Îles Vierges Des États-unis','de' => 'Virgin Islands (U.S.)'],['en' => 'Wallis and Futuna','fr' => 'Wallis Et Futuna','de' => 'Wallis and Futuna Islands'],['en' => 'Western Sahara','fr' => 'Sahara Occidental','de' => 'Western Sahara'],['en' => 'Yemen','fr' => 'Yémen','de' => 'Yemen, Republic of'],['en' => 'Zambia','fr' => 'Zambie','de' => 'Zambia'],['en' => 'Zimbabwe','fr' => 'Zimbabwe','de' => 'Zimbabwe'],['en' => 'Kosovo','fr' => 'Kosovo','de' => 'Kosovo'],['en' => 'Serbia','fr' => 'Serbie','de' => 'Serbia'],['en' => 'Zaire','fr' => 'Zaïre','de' => 'Zaire'],['en' => 'Yugoslavia','fr' => 'Yougoslavie','de' => 'Jugoslawien']
    ];
}

/**
 * @param $str
 *   the string to match.
 *
 * @return
 *   true if the given string is a UUIDv4, false otherwise.
 */
function tkt_is_uuidv4($str)
{
    $regexp = '/^[A-Fa-f0-9]{8}-[A-Fa-f0-9]{4}-4[A-Fa-f0-9]{3}-[ABab89][A-Fa-f0-9]{3}-[A-Fa-f0-9]{12}$/';
    return (preg_match($regexp, $str) ? true : false);
}

if (!function_exists('tkt_invalid_url_path_encode_url')) {
    // https://stackoverflow.com/questions/9831077/how-to-url-encode-only-non-ascii-symbols-of-url-in-php-but-leave-reserved-symbo
    function tkt_invalid_url_path_encode_url($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        if ($path !== false && strpos($path, '%') !== false) return $url; // avoid double encoding
        else {
            $encoded_path = array_map('rawurlencode', explode('/', $path));
            return str_replace($path, implode('/', $encoded_path), $url);
        }
    }
}

if (!function_exists('tkt_img_proxy_url')) {
    function tkt_img_proxy_url($remote_url, $max_width = null, $max_height = null, $animation = false)
    {
        $proxy_img_host = TKTApp::get_instance()->get_config("integrations.weserv.proxy_img_host");
        if (empty($proxy_img_host)) {
            return $remote_url;
        }

        if (!filter_var($remote_url, FILTER_VALIDATE_URL)) {
            // sometimes we get non RFC 1738 urls, let's be nice and try to fix it
            $remote_url =tkt_invalid_url_path_encode_url($remote_url);
            if (!filter_var($remote_url, FILTER_VALIDATE_URL)) {
                return false;
            }
        }

        // Default output, webp is supported by 98% of users as of 2024
        $output = 'webp';

        $n = $animation ? '-1' : null;

        return sprintf(
            "https://%s/?%s",
            $proxy_img_host,
            http_build_query(['url' => $remote_url, 'w' => $max_width, 'h' => $max_height, 'output' => $output, 'q' => 70, 'fit' => 'outside', 'n' => $n])
        );
    }
}

if (!function_exists('tkt_base64url_encode')) {
    /**
     * base64url variants,
     * stolen from http://us3.php.net/manual/en/function.base64-encode.php#103849
     */
    function tkt_base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

if (!function_exists('tkt_base64url_decode')) {
    function tkt_base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}

/**
 * Wrap Wordpress __ function
 */
function tkt_t($str) {
    return __($str, 'wpticketack');
}


function tkt_get_event_slug($event, $lang)
{
    $title = $event->localized_title_or_default_or_original($lang);
    // sanitize_title, uses remove_accents that removes accents differently depending on locale
    // e.g. ö is replaced by o usually but by oe if locale is German
    // This makes the slug computed different depending on the wordpress admin locale than the current user's locale
    // Let's fix that by forcing accent removal in a defined way.
    // https://developer.wordpress.org/reference/functions/remove_accents/
    $title = remove_accents($title, /* locale */ 'en_US');
    $slug  = sanitize_title($title).($lang === tkt_default_lang() ? '' : '-'.$lang);

    return $slug;
}

function tkt_get_article_slug($article, $lang)
{
    $title = $article->name($lang);
    $slug  = sanitize_title($title).($lang === tkt_default_lang() ? '' : '-'.$lang);

    return $slug;
}

/**
 * Generate html data attributes based on defined attributes
 *
 * @param TKTEvent $event
 * @param array $attributes
 */
function tkt_event_data_attributes($event, $attributes)
{
    $values = [];
    if (in_array('type', $attributes)) {
        $values[] = 'data-type="'.$event->opaque('type').'"';
    }

    if (in_array('date', $attributes)) {
        $dates = [];
        foreach ($event->screenings() as $s) {
            $dates[] = $s->start_at()->format('Y-m-d');
        }
            $values[] = 'data-date="'.implode(',', $dates).'"';
    }

    if (in_array('tags', $attributes)) {
        $tags = [];
        foreach ($event->opaque('tags', []) as $tag) {
            $tags[] = $tag[TKT_LANG];
        }
        $values[] = 'data-tags="'.implode(',', $tags).'"';
    }

    if (in_array('section', $attributes)) {
        $sections = [];
        foreach ($event->screenings() as $s) {
            foreach ($s->sections() as $section) {
                $sections[] = $section->name(TKT_LANG);
            }
        }
        $values[] = 'data-section="'.implode(',', $sections).'"';
    }

    return implode(' ', $values);
}


/**
 * Generate html data attributes based on defined attributes
 *
 * @param TKTEvent $screening
 * @param array $attributes
 */
function tkt_screening_data_attributes($screening, $attributes)
{
    $values = [];
    if (in_array('type', $attributes)) {
        $values[] = 'data-type="'.$screening->opaque('type').'"';
    }

    if (in_array('date', $attributes)) {
        $values[] = 'data-date="'.$screening->start_at()->format('Y-m-d').'"';
    }

    if (in_array('hall', $attributes)) {
        $values[] = 'data-hall="'.$screening->place()->name().'"';
    }

    if (in_array('sections', $attributes)) {
        $sections = [];
        foreach ($event->screenings() as $s) {
            foreach ($s->sections() as $section) {
                $sections[] = $section->name[tkt_default_lang()];
            }
        }
        $values[] = 'data-section="'.implode(',', $sections).'"';
    }

    if (in_array('tags', $attributes)) {
        $tags = [];
        foreach ($screening->opaque('tags', []) as $tag) {
            $tags[] = $tag[TKT_LANG];
        }
        $values[] = 'data-tags="'.implode(',', $tags).'"';
    }

    return implode(' ', $values);
}

/**
 * Generate html data attributes based on defined attributes
 *
 * @param TKTPerson $person
 * @param array $attributes
 */
function tkt_person_data_attributes($person, $attributes)
{
    $meta   = get_post_meta($person->ID);
    $values = [];
    $tags   = [];

    if (in_array('name', $attributes)) {
        $values[] = 'data-name="'.$person->post_content.'"';
        $tags[]   = $person->post_content;
    }

    if (in_array('country', $attributes)) {
        $values[] = 'data-country="'.$meta['country'][0].'"';
        $tags[]   = $meta['country'][0];
    }

    if (in_array('company', $attributes)) {
        $values[] = 'data-company="'.$meta['company'][0].'"';
        $tags[]   = $meta['company'][0];
    }

    if (in_array('profession', $attributes)) {
        $values[] = 'data-profession="'.$meta['profession'][0].'"';
        $tags[]   = $meta['profession'][0];
    }

    if (in_array('tags', $attributes)) {
        $values[] = 'data-tags="'.implode(' ', array_unique($tags)).'"';
    }

    return implode(' ', $values);
}

/**
 * Get the configured default lang
 *
 * @return string
 */
function tkt_default_lang()
{
    return TKTApp::get_instance()->get_config('i18n.default_lang', 'fr');
}

/**
 * Get the configured default lang
 *
 * @return string
 */
function tkt_current_lang()
{
    if (!TKT_WPML_INSTALLED) {
        return tkt_default_lang();
    }

    return ICL_LANGUAGE_CODE;
}

/**
 * Get the translated slug of a post
 *
 * @param int $id: The post id
 * @param string $type: The post type (post, page, tkt-event, ...)
 * @param string $lang: The desired language
 * @param string $default: default value
 *
 * @return string: The slug in the desired language
 */
function tkt_translated_slug_by_id($id, $type, $lang, $default)
{
    if (!TKT_WPML_INSTALLED) {
        return $default;
    }

    // get the post ID in $lang
    $post_id = icl_object_id($id, $type, FALSE, $lang);
    // get the post object
    $post_obj = get_post($post_id);

    return $post_obj->post_name;
}

/**
 * Get the id of a translated post
 *
 * @param int $id: The post id
 * @param string $type: The post type (post, page, tkt-event, ...)
 * @param string $lang: The desired language
 * @param string $default: default value
 *
 * @return int: The id of the post in the desired language
 */
function tkt_translated_id_by_id($id, $type, $lang, $default)
{
    if (!TKT_WPML_INSTALLED) {
        return $default;
    }

    // get the post ID in $lang
    $post_id = icl_object_id($id, $type, FALSE, $lang);

    // get the post object
    $post_obj = get_post($post_id);

    return $post_obj->ID;
}

/**
 * Return a list of overridable scss variables.
 */
function tkt_get_overridable_scss_variables()
{
    return [
        'text_color' => '#000',
        'error_color' => '#ce6060',
        'link_color' => '#007BFF',
        'active_color' => '#1C99E2',
        'btn_bg_color' => '#121212',
        'btn_text_color' => '#FFFFFF',
        'input_bg_color' => '#FFFFFF',
        'input_text_color' => '#000000',
        'section_padding' => '20px',
        'light_section_bg_color' => '#F0F0F0',
        'light_section_text_color' => '#000',
        'dark_section_bg_color' => '#212121',
        'dark_section_text_color' => '#FFF',
        'badge_bg_color' => '#FFF',
        'badge_text_color' => '#000',
        'badge_active_bg_color' => '#1C99E2',
        'badge_active_text_color' => '#FFF',
        'badge_title_bg_color' => '#000',
        'badge_title_text_color' => '#FFF',
        'badge_value_bg_color' => '#333',
        'badge_value_text_color' => '#FFF',
        'border_radius' => '4px',
    ];
}

/**
 * Compile scss override file with the variables
 * provided by `tkt_get_overridable_scss_variables()`.
 */
function tkt_compile_scss_override()
{
    $scss = new Compiler();
    $scss->setImportPaths(plugin_dir_path(TKT_APP).'front/build/');
    $scss->setFormatter('ScssPhp\ScssPhp\Formatter\Crunched');

    $variables = tkt_get_overridable_scss_variables();
    foreach ($variables as $name => $value) {
        $variables[$name] = TKTApp::get_instance()->get_config('advanced.'.$name, $value);
    }
    $scss->setVariables($variables);

    $output_path = TKT_OVERRIDE_DIR.'/tkt_override.css';
    file_put_contents($output_path, $scss->compile('@import "override.scss";'));
}

/**
 * Add a flash notice to {prefix}options table until a full page refresh is done
 *
 * @param string $notice our notice message
 * @param string $type This can be "info", "warning", "error" or "success", "warning" as default
 * @param boolean $dismissible set this to true to add is-dismissible functionality to your notice
 * @return void
 */
function tkt_flash_notice($notice = '', $type = 'warning', $dismissible = true)
{
    $notices = get_option('tkt_flash_notices', []);

    array_push($notices, [
        'notice'      => $notice,
        'type'        => $type,
        'dismissible' => $dismissible ? 'is-dismissible' : ''
    ]);

    // Then we update the option with our notices array
    update_option('tkt_flash_notices', $notices);
}

function tkt_ticketidize($str)
{
    return str_replace('TicketID', '<span class="tkt-ticketid_ticket">Ticket</span><span class="tkt-ticketid_id">ID</span>', $str);
}

function tkt_original($title)
{
    // original is mandatory, no need to ?? null here
    return ((object) $title)->original;
}

function tkt_localized_or_original($title, $lang)
{
    $localized = ((object) $title)->{$lang} ?? null;
    $original  = tkt_original($title);
    return ($localized ?? $original);
}

function tkt_localized_or_default_or_original($title, $lang)
{
    $localized = ((object) $title)->{$lang} ?? null;
    $default   = ((object) $title)->{tkt_default_lang()} ?? null;

    $original  = tkt_original($title);
    return ($localized ?? $default ?? $original);
}


function tkt_original_if_different_from_localized($title, $lang)
{
    $localized = ((object) $title)->{$lang} ?? null;
    $original  = tkt_original($title);
    return ($original != $localized) ? $original : null;
}

