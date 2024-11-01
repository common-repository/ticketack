<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\Templates\TKTTemplate;

/**
 * People template
 *
 * Input:
 * $data: {
 *   "people": {  },
 *   "filter_fields": ['name', 'company', ...],
 *   "countries": [],
 *   "companies": [],
 *   "professions": [],
 * }
 */
?>
<div id="tkt-people-wrapper" class="tkt-wrapper">
    <div class="tkt-people" data-component="People/Filter">
        <div class="row">
            <div class="col-md-9 tkt-people-list">
                <div class="row">
                    <?php while ( $data->people->have_posts() ) : ?>
                        <?php $data->people->the_post(); ?>
                        <?php $meta = get_post_meta(get_the_ID()); ?>

                        <div class="col-md-6 tkt-person" style="display: none;" <?php echo esc_attr(tkt_person_data_attributes(get_post(), $data->filter_fields)) ?>>
                            <?php TKTTemplate::output('people/person', $meta); ?>
                        </div>

                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </div>
            <div class="col-md-3 tkt-people-filters">
                <div class="tkt-people-filter-search">
                    <h4><?php echo esc_html(tkt_t('Recherche')) ?></h4>
                    <input type="text" class="tkt-filter-tags form-control" placeholder="<?php echo esc_html(tkt_t('Rechercher')) ?>">
                </div>
                <div class="tkt-people-filter-country">
                    <h4><?php echo esc_html(tkt_t('Pays')) ?></h4>
                    <ul>
                        <li class="reset-filter"><a class="tkt-filter-country" href="#"><?php echo esc_html(tkt_t('Tous')) ?></a></li>
                        <?php foreach ($data->countries as $country) : ?>
                            <li style="display: none;">
                                <a class="tkt-filter-country" href="#<?php echo esc_attr($country) ?>"><?php echo esc_html($country) ?></a> (<span class="tkt-filter-country-total" data-country="<?php echo esc_attr($country) ?>">0</span>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="tkt-people-filter-company">
                    <h4><?php echo esc_html(tkt_t('Société')) ?></h4>
                    <ul>
                        <li class="reset-filter"><a class="tkt-filter-company" href="#"><?php echo esc_html(tkt_t('Tous')) ?></a></li>
                        <?php foreach ($data->companies as $company) : ?>
                            <li style="display: none;">
                                <a class="tkt-filter-company" href="#<?php echo esc_attr($company) ?>"><?php echo esc_html($company) ?></a> (<span class="tkt-filter-company-total" data-company="<?php echo esc_attr($company) ?>">0</span>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="tkt-people-filter-profession">
                    <h4><?php echo esc_html(tkt_t('Profession')) ?></h4>
                    <ul>
                        <li class="reset-filter"><a class="tkt-filter-profession" href="#"><?php echo esc_html(tkt_t('Tous')) ?></a></li>
                        <?php foreach ($data->professions as $profession) : ?>
                            <li style="display: none;">
                                <a class="tkt-filter-profession" href="#<?php echo esc_attr($profession) ?>"><?php echo esc_html($profession) ?></a>  (<span class="tkt-filter-profession-total" data-profession="<?php echo esc_attr($profession) ?>">0</span>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
