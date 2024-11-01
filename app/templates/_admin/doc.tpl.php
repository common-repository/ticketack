<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Module settings documentation tab
 */
?>
<h2>
    <?php echo wp_kses_post(tkt_t('La documentation est disponible <a target="_blank" href="https://ticketack.com/knowledge-base/integration-avec-le-module-wordpress/">à cette adresse</a> et le journal des modifications <a target="_blank" href="https://yoda.netoxygen.ch/public/projects/401b5210-986f-46b3-99cb-38546b73e16c/changelog">ici</a>')); ?>.
</h2>
<div id="ht-kb-fe-embed-container" class="Help ht-kb-fe-embed-container">
    <div id="ht-kb-fe-embed-contents" style="display: block;">
        <iframe id="ht-kb-fe-embed-iframe" src="https://ticketack.com/?kbembed=content" loading="lazy"></iframe>
    </div>
</div>
<style type="text/css">
    #ht-kb-fe-embed-iframe {
        width: calc(100% - 20px);
        height: 100%;
        position: absolute;
    }

    div#wpfooter {
        display: none;  
    }
</style>
