<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * Events/Screenings filter shortcode
 *
 * Usage:
 *
 * [tkt_filter filter-*="*"]
 */
class FilterShortcode extends TKTShortcode
{
    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_filter";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $filters = [];
        foreach ($atts as $att => $value) {
            if (strpos($att, 'filter-') === 0) {
                $key = substr($att, 7);
                $filters[$key] = $value;
            }
        }

        return TKTTemplate::render(
            'filters/filter',
            (object)[
                "filters" => $filters
            ]
        );
    }
}
