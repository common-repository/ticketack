<?php
namespace Ticketack\WP\Actions;

use Ticketack\WP\helpers\SyncHelper;

/**
 * Sync posts action
 */
class SyncEventsAction extends TKTAction
{
    /**
     * Get this action tag(s)
     *
     * @return mixed: A single tag (which will call the <code>run</code> method)
     *                or an associative array with the tag as key and the method to call as value.
     */
    public function get_tag()
    {
        return [
            "admin_head-edit.php"    => "add_link",
            "admin_post_sync_events" => "run"
        ];
    }

    /**
     * Add the link in Posts admin listing page
     */
    public function add_link()
    {
        $action_link = admin_url("admin-post.php?action=sync_events");
?>
    <script type="text/javascript">
    jQuery(document).ready(function ($) {

        var pageTitle = $("body.post-type-tkt-event #wpbody-content > .wrap > h1.wp-heading-inline");
        var sync_link = $('<a href="<?php echo esc_attr($action_link) ?>" class="page-title-action"><?php echo esc_html(tkt_t("Importer depuis Ticketack")) ?></a>');

        pageTitle.after(sync_link);
    });
    </script>
<?php
    }

    /**
     * Run the synchronization
     */
    public function run()
    {
        SyncHelper::sync_events();

        wp_redirect(admin_url("edit.php?post_type=tkt-event"));
        exit;
    }
}
