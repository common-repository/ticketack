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
class FilterRowsShortcode extends TKTShortcode
{
    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_filter_rows";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $rows = [];
        foreach ($atts as $att => $value) {
            if (strpos($att, 'row-') === 0) {
                $key        = substr($att, 4);
                $values     = explode('|', $value);

                if (empty($values)) {
                    continue;
                }

                if ($key === 'next-days') {
                    $key     = 'date';
                    $nb_days = (int)current($values);
                    $values  = [];

                    $now = new \Datetime();
                    for ($i = 0; $i < $nb_days; $i++) {
                        $values[] = sprintf('%s:%s', $now->format('Y-m-d'), tkt_date_to_min_s($now));
                        $now->modify('+1 day');
                    }
                }

                $rows[$key] = [];
                foreach ($values as $v) {
                    $parts        = explode(':', $v);
                    $rows[$key][] = [
                        "value" => trim(reset($parts)),
                        "label" => trim(end($parts))
                    ];
                }
            }
        }

        $target = isset($atts['target']) ? $atts['target'] : null;

        return TKTTemplate::render(
            'filters/filter_rows',
            (object)[
                "rows"   => $rows,
                "target" => $target
            ]
        );
    }
}
