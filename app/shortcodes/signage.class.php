<?php
namespace Ticketack\WP\Shortcodes;
use Ticketack\WP\Templates\TKTTemplate;
use Ticketack\Core\Models\Screening;
use Ticketack\Core\Base\TKTApiException;
use \Datetime;
use \DateInterval;


/**
 * Signage shortcode
 *
 * Usage:
 *
 * [tkt_signage hall="hall_name" timeout="time" language="fr"]
 *
 */
class SignageShortcode extends TKTShortcode
{
    const CLASSIC_LAYOUT          = 'classic';
    const STRETCH_LTR_LAYOUT      = 'stretch-ltr';
    const STRETCH_RTL_LAYOUT      = 'stretch-rtl';
    const RECEPTION_LAYOUT        = 'reception';
    const NOT_AFTER_BEGINNING     = 0;
    const TILL_THE_END            = -1;
    const DEFAULT_SCREEN_ID       = 0;

    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_signage";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $layout                     = isset($atts['layout']) ? $atts['layout'] : static::CLASSIC_LAYOUT;
        $screen_id                  = isset($atts['screen_id']) ? $atts['screen_id'] : static::DEFAULT_SCREEN_ID;
        $time_after_beginning       = isset($atts['timeout']) ? $atts['timeout'] : static::NOT_AFTER_BEGINNING;
        $reverse_time               = isset($atts['reverse']) ? $atts['reverse'] : false;
        $hall_name                  = isset($atts['hall']) ? $atts['hall'] : null;
        $main_movie_index           = 0;
        $language                   = isset($atts['language']) ? $atts['language'] : TKT_LANG;

        if (empty($hall_name)) {
            return null;
        }

        $screening_id = $this->get_next_screening($hall_name,$time_after_beginning,$reverse_time);

        try {
            $screening = Screening::find($screening_id);

            if (empty($screening)) {
                $timeout = new Datetime();
                $timeout = $timeout->add(new DateInterval("PT1M"));
            } else {
                $timeout = $screening->stop_at();
                if ($time_after_beginning != static::TILL_THE_END) {
                    if ($reverse_time) {
                        $timeout = clone($screening->stop_at());
                        $timeout = $timeout->sub(new DateInterval("PT".$time_after_beginning."M"));
                    } else {
                        $timeout = clone($screening->start_at());
                        $timeout = $timeout->add(new DateInterval("PT".$time_after_beginning."M"));
                    }
                }
            }

            $options = (object)[
                'screening'        => $screening,
                'screen_id'        => $screen_id,
                'time_to_reload'   => $timeout,
                'main_movie_index' => $main_movie_index,
                'language'         => $language
            ];

            switch ($layout) {
                case static::STRETCH_LTR_LAYOUT:
                    return TKTTemplate::render('signage/stretch-ltr', $options);
                case static::STRETCH_RTL_LAYOUT:
                    return TKTTemplate::render('signage/stretch-rtl', $options);
                case static::RECEPTION_LAYOUT:
                    return TKTTemplate::render('signage/reception', $options);
                case static::CLASSIC_LAYOUT:
                default:
                    return TKTTemplate::render('signage/door', $options);
            }
        } catch (TKTApiException $e) {
            return sprintf(
                "Impossible de charger l'événement: %s",
                $e->getMessage()
            );
        }
    }

    /**
     * Get screening id of the next screening
     *
     * @param string $hall_name: Cinema hall name to look for
     * @param int $timeout : Time (in minutes) we still get the film after its beginning
     *
     * @return string: the screening ID of the current screening or NULL
     */
    public function get_next_screening($hall_name,$timeout,$reverse_time)
    {
        $now          = new DateTime();
        $stop_at_lte  = clone($now);
        $stop_at_gte  = clone($now);
        $start_at_gte = clone($now);

        $query = Screening::all()->order_by_start_at()
            ->stop_at_gte($now)
            ->stop_at_lte($stop_at_lte->add(new DateInterval('P1D')));

        if ($timeout != static::TILL_THE_END) {
            if ($reverse_time) {
                $query = $query->stop_at_gte($stop_at_gte->add(new DateInterval("PT".$timeout."M")));
            } else {
                $query = $query->start_at_gte($start_at_gte->sub(new DateInterval("PT".$timeout."M")));
            }
        }

        $screenings = $query->get('_id,cinema_hall._id,start_at');
        foreach ($screenings as $screening) {
            if($screening->Place()->_id() == $hall_name) {
                return $screening->_id();
            }
        }

        return null;
    }

}
