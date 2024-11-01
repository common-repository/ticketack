<?php
namespace Ticketack\WP\Shortcodes;

use Ticketack\WP\Templates\TKTTemplate;
use Ticketack\Core\Models\Screening;
use Ticketack\Core\Models\Event;
use Ticketack\Core\Base\TKTApiException;

/**
 * Pantaflix player shortcode
 *
 * Usage:
 *
 * [tkt_pantaflix_player
 *      provider="provider"
 *      id="screening_id"
 *      pantaflix_id_prefix="pantaflix_id_int|pantaflix_id_dev|pantaflix_id_prod"
 *      allowed_ticket_types="comma separated list of allowed ticket types" (optional)
 * ]]
 *
 */
class PantaflixPlayerShortcode extends TKTShortcode
{
    const PANTAFLIX_ID_PREFIX = 'pantaflix_id_int';

    /**
     * Get this Shortcode tag
     *
     * @return string: The tag to use to run this shortcode
     */
    public function get_tag()
    {
        return "tkt_pantaflix_player";
    }

    /**
     * Get this Shortcode tag
     *
     * @param array $atts: Shortcode attributes
     * @param string $content: Shortcode content
     */
    public function run($atts, $content)
    {
        $id                   = isset($atts['id']) ? $atts['id'] : null;
        $provider             = isset($atts['provider']) ? $atts['provider'] : null;
        $prefix               = isset($atts['pantaflix_id_prefix']) ? $atts['pantaflix_id_prefix'] : static::PANTAFLIX_ID_PREFIX;
        $allowed_ticket_types = isset($atts['allowed_ticket_types']) ? $atts['allowed_ticket_types'] : [];

        if (empty($id) || empty($provider)) {
            return null;
        }

        try {
            $screening = Screening::find($id);
            if (empty($screening)) {
                return null;
            }

            $pantaflix_content_id = null;
            $refs = array_filter($screening->refs(), function ($ref) use ($prefix) {
                return strpos($ref['id'], $prefix) === 0;
            });
            if (count($refs) == 0) {
                return null;
            }

            $ref = (int)end(explode('/', end($refs)['id']));
            if ($ref == 0) {
                return null;
            }

            return TKTTemplate::render(
                'pantaflix/player',
                (object)[
                    'provider'             => $provider,
                    'screening'            => $screening,
                    'allowed_ticket_types' => $allowed_ticket_types,
                    'content_id'           => $ref
                ]
            );
        } catch (TKTApiException $e) {
            return sprintf(
                "Impossible de charger le lecteur: %s",
                $e->getMessage()
            );
        }
    }
}
