<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * Events/Screenings filter shortcode
 *
 * Usage:
 *
 * [tkt_days_filter filter-*="*"]
 */
class DaysFilterShortcode extends TKTShortcode
{
    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_days_filter";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $min = isset($atts['min_start_at']) ?
            tkt_iso8601_to_datetime($atts['min_start_at']) :
             new \Datetime();

        $max = new \Datetime();
        $max->setTime(23, 59, 59);
        if (isset($atts['max_start_at'])) {
            $max = tkt_iso8601_to_datetime($atts['max_start_at']);
        } else if (isset($atts['nb_days'])) {
            $nb_days = (int)$atts['nb_days'];
            $max->add(new \DateInterval('P'.($nb_days - 1).'D'));
        }

        $active = tkt_get_url_param('d', date('Y-m-d'));

        $days    = [];
        $current = clone($min);
        while ($current < $max) {
            array_push($days, clone($current));
            $current->modify('+1 day');
        }

        return TKTTemplate::render(
            'filters/days_filter',
            (object)[
                "days"   => $days,
                "active" => $active
            ]
        );
    }
}
