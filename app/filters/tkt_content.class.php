<?php
namespace Ticketack\WP\Filters;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * TktContent filter
 */
class TktContentFilter extends TKTFilter
{
    /**
     * Get this filter tag
     *
     * @return string: The tag to use
     */
    public function get_tag()
    {
        return "the_content";
    }

    /**
     * Run this filter
     */
    public function run($args = null)
    {
        $post = get_post();
        // Check if we're inside the main loop in a single post page.
        if (is_single() && in_the_loop() && is_main_query()) {
            $content = "";
            if ($post->post_type == 'tkt-event') {
                $content = trim(preg_replace('#\R+#', '', TKTTemplate::render("event/tkt_event", (object)[
                    "tkt_event" => get_post()
                ])));
            } elseif ($post->post_type == 'tkt-article') {
                $content = trim(preg_replace('#\R+#', '', TKTTemplate::render("article/tkt_article", (object)[
                    "tkt_article" => get_post()
                ])));
            }

            /**
             * We cannot use wp_kses* here since it doesn't allow js templating.
             * For example:
             *
             * <?php
             *  $html = '<script type="text/javascript"><%/></script>';
             *  echo wp_kses($html, ['script' => ['type' => true], '%' => []]);
             * ?>
             *
             * Will give:
             * <script type="text/javascript"></script>
             *
             * See https://core.trac.wordpress.org/ticket/62025
             */
            echo $content;
        }

        return $args;
    }
}
