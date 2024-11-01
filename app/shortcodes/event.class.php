<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;
use Ticketack\Core\Models\Screening;
use Ticketack\Core\Models\Event;
use Ticketack\Core\Base\TKTApiException;

/**
 * Event shortcode
 *
 * Usage:
 *
 * [tkt_event]
 *
 * The event id is retrieved from the query var id
 */
class EventShortcode extends TKTShortcode
{
    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_event";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $event_id = tkt_get_url_param('id');
        if (empty($event_id)) {
            return null;
        }

        try {
            $screenings = Screening::all()
                ->with_films([$event_id])
                ->order_by_start_at()
                ->get('_id,title,start_at,stop_at,films,opaque');

            if (empty($screenings)) {
                return null;
            }

            $event = array_shift(Event::from_screenings($screenings, $event_id));

            return TKTTemplate::render(
                'event/event',
                (object)[ 'event' => $event ]
            );
        } catch (TKTApiException $e) {
            return sprintf(
                "Impossible de charger l'événement: %s",
                $e->getMessage()
            );
        }
    }
}
