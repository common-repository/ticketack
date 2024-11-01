<?php

if (!defined('ABSPATH')) exit;
/**
 * Person template
 *
 * Input:
 * $data: { ... }
 */

$photo = null;

if ($data['photos'][0]) {
    $photo = json_decode($data['photos'][0])[0];
}
?>
<div class="row tkt-person-about">
    <div class="col-sm-4 tkt-person-photo">
    <?php if ($photo) : ?>
        <img src="<?php echo esc_attr(tkt_img_proxy_url($photo)) ?>" alt="<?php echo esc_attr(the_title()); ?>">
    <?php endif;?>
    </div>
    <div class="col-sm-8 tkt-person-info">
        <h3><?php echo esc_html(the_title()); ?></h3>
        <div class="row">
            <div class="col-12 tkt-person-profession"><?php echo esc_html($data['profession'][0]) ?></div>
            <div class="col-12 tkt-person-company"><?php echo esc_html($data['company'][0]) ?></div>
            <div class="col-12 tkt-person-country"><?php echo esc_html($data['country'][0]) ?></div>
        </div>
    </div>
</div>
