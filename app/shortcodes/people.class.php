<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Helpers\SyncPeopleHelper;
use Ticketack\WP\Templates\TKTTemplate;
use Ticketack\Core\Models\Article;
use Ticketack\Core\Base\TKTApiException;

/**
 * Article shortcode
 *
 * Usage:
 *
 * [tkt_people]
 *
 */
class PeopleShortcode extends TKTShortcode
{
    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_people";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $filter_fields = isset($atts['filter_fields']) ? explode(',', $atts['filter_fields']) : [];

        $args = [
            'post_type' => SyncPeopleHelper::POST_TYPE,
            'nopaging'  => true,
            'orderby'   => 'title',
            'order'     => 'ASC',
            /*'meta_query' => [
                [
                    'key' => 'role',
                    'value' => 'Commissioning Editor & New Talent Consultant'
                ]
            ]*/
        ];

        if (TKT_WPML_INSTALLED) {
            $args['suppress_filters'] = false;
            $args['language'] = 'fr';
        }

        // Querying first to get meta off all people
        $all_people = new \WP_Query($args);

        $professions = [];
        $companies   = [];
        $countries   = [];

        foreach ($all_people->posts as $post) {
            $meta          = get_post_meta($post->ID);
            $professions[] = $meta['profession'][0];
            $companies[]   = $meta['company'][0];
            $countries[]   = $meta['country'][0];
        }

        $professions = array_filter(array_unique($professions));
        $companies   = array_filter(array_unique($companies));
        $countries   = array_filter(array_unique($countries));

        sort($professions);
        sort($companies);
        sort($countries);

        // Querying to get filtered people
        $people = new \WP_Query($args);

        try {
            return TKTTemplate::render(
                'people/people',
                (object)[
                    'people'        => $people,
                    'filter_fields' => $filter_fields,
                    'professions'   => $professions,
                    'companies'     => $companies,
                    'countries'     => $countries,
                ]
            );
        } catch (TKTApiException $e) {
            return sprintf(
                "Impossible de charger les people : %s",
                $e->getMessage()
            );
        }
    }
}
