<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;
use Ticketack\WP\Templates\TKTTemplate;
use Ticketack\Core\Models\Screening;

/**
 * TKTEvent template
 *
 * Input:
 * $data: {
 *   "tkt_event": { ... }
 * }
 */

$e = $data->tkt_event;

$title   = json_decode(get_post_meta($e->ID, 'title')[0]);
$posters = array_map(function ($s) {
    return json_decode($s);
}, get_post_meta($e->ID, 'posters'))[0];
$trailers = array_map(function ($s) {
    return json_decode($s);
}, get_post_meta($e->ID, 'trailers'))[0];
$opaque     = json_decode(get_post_meta($e->ID, 'opaque')[0]);
$screenings = array_map(function ($s) {
    return new Screening($s);
}, json_decode(get_post_meta($e->ID, 'screenings')[0], /*assoc*/true));


$description = "";
if (!empty($opaque->description)) {
    $description = $opaque->description->{TKT_LANG};
    if (strip_tags($description) == "") {
        $description = "";
    }
}

$ids = array_map(function ($s) {
    return $s->_id();
}, $screenings);

$images_width  = TKTApp::get_instance()->get_config('images_dimensions.big_width');
$images_height = TKTApp::get_instance()->get_config('images_dimensions.big_height');

$nb_slides = count($trailers) + count($posters);

$original_languages = "";
if (!empty($opaque->languages->original)) {
    $original_languages = $opaque->languages->original;
    $original_languages = implode(', ', array_map(function ($l) {
        return (is_object($l) && isset($l->{TKT_LANG})) ? $l->{TKT_LANG} : (is_string($l) ? $l : '');
    }, $original_languages));
}

$audio = "";
if (!empty($opaque->languages->audio)) {
    $audio = $opaque->languages->audio;
    if (!is_array($audio)) {
        $audio = [$audio];
    }
    $audio = implode(', ', array_map(function ($a) {
        return (is_object($a) && isset($a->{TKT_LANG})) ? $a->{TKT_LANG} : (is_string($a) ? $a : '');
    }, $audio));
}

$subtitles = "";
if (is_array($opaque->languages->subtitles) && !empty($opaque->languages->subtitles)) {
    $subtitles = implode(', ', array_map(function ($s) {
        return (is_object($s) && isset($s->{TKT_LANG})) ? $s->{TKT_LANG} : (is_string($s) ? $s : '');
    }, $opaque->languages->subtitles));
}

$countries = "";
if (!empty($opaque->countries)) {
    $countries = implode(', ', array_map(function ($c) {
        return (is_object($c) && isset($c->{TKT_LANG})) ? $c->{TKT_LANG} : (is_string($c) ? $c : '');
    }, $opaque->countries));
}

$dates = [];
foreach ($screenings as $s) {
    $dates[] = tkt_date_and_time_to_min_s($s->start_at()).' | '.$s->place()->name();
}
?>
<div class="tkt-wrapper tkt_event">
  <div id="tkt-event">

    <section class="tkt-section title-section">
      <div class="row">
        <div class="col">
          <h3 class="tkt-section-title">
            <?php echo esc_html(tkt_localized_or_default_or_original($title, TKT_LANG)) ?>
          </h3>
          <small class="single-date">
            <?php echo esc_html(implode(', ', $dates)) ?>
          </small>
        </div>
      </div>
    </section>
    <?php if ($nb_slides > 0) : ?>
    <section class="tkt-full-section carousel-section">
      <div class="row">
        <div class="col">
          <div id="event-carousel" data-component="Media/Carousel" class="glide">
            <div class="glide__track" data-glide-el="track">
              <ul class="glide__slides">
              <?php foreach ($trailers as $i => $t) : ?>
                <?php if (preg_match("/youtu[.]?be/", $t->url) === 1): ?>
                  <li class="glide__slide <?php echo $i == 0 ? 'active' : '' ?>">
                    <div class="tkt-event-carousel-trailer-wrapper d-block w-100">
                      <div
                        id="tkt-event-carousel-trailer-<?php echo esc_attr($i) ?>"
                        class="tkt-event-carousel-trailer"
                        data-component="Media/YoutubeVideo"
                        data-video-id="<?php echo esc_attr(tkt_yt_video_id($t->url)) ?>"
                        data-video-image="<?php echo esc_attr(tkt_img_proxy_url($t->image, $images_width, $images_height)) ?>"
                        data-controls="1"
                        data-bs4-carousel-id="event-carousel">
                      </div>
                    </div>
                  </li>
                <?php else: ?>
                  <li class="glide__slide <?php echo $i == 0 ? 'active' : '' ?>">
                    <div class="tkt-event-carousel-trailer-wrapper d-block w-100">
                      <div
                        id="tkt-event-carousel-trailer-<?php echo esc_attr($i) ?>"
                        class="tkt-event-carousel-trailer"
                        data-video-image="<?php echo esc_attr(tkt_img_proxy_url($t->image, $images_width, $images_height)) ?>"
                        data-bs4-carousel-id="event-carousel"
                      />
                        <iframe width="100%" class="h-100" src=<?php echo esc_attr($t->url) ?> frameBorder="0" allow="encrypted-media" allowFullScreen></iframe>
                      </div>
                    </div>
                  </li>
                <?php endif; ?>
              <?php endforeach; ?>
              <?php foreach ($posters as $i => $p) : ?>
              <li class="glide__slide <?php echo count($trailers) == 0 && $i == 0 ? 'active' : '' ?>">
                <img class="d-block w-100" src="<?php echo esc_attr(tkt_img_proxy_url($p->url, $images_width, $images_height)) ?>" alt="<?php echo esc_attr($title->{TKT_LANG}) ?>">
              </li>
              <?php endforeach; ?>
            </div>
            <?php if ($nb_slides > 1) : ?>
            <div class="glide__arrows" data-glide-el="controls">
              <button class="glide__arrow glide__arrow--left" data-glide-dir="&lt;"><</button>
              <button class="glide__arrow glide__arrow--right" data-glide-dir="&gt;">></button>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <section class="tkt-section tkt-light-section infos-section">
      <?php if (!empty($description)) : ?>
      <div class="row">
        <div class="col">
          <h3 class="tkt-section-title"><?php echo esc_html(tkt_t('Synopsis')) ?></h3>
          <div class="synopsis">
            <span class="text">
                <?php echo wp_kses_post($description) ?>
            </span>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <div class="row">

        <?php if (!empty($opaque->genre) || !empty($opaque->duration)) : ?>
        <div class="col-md left-col text-left">
          <h3 class="tkt-section-title"><?php echo esc_html(tkt_t('Details')) ?></h3>

          <?php if (!empty($opaque->genre)) : ?>
          <div class="row">
            <div class="col">
              <span class="tkt-badge tkt-badge-split">
                <span class="tkt-badge-part tkt-dark-badge"><?php echo esc_html(tkt_t('Genre')) ?></span>
                <span class="tkt-badge-part tkt-grey-badge"><?php echo esc_html(ucfirst($opaque->genre)) ?></span>
              </span>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($opaque->duration)) : ?>
          <div class="row">
            <div class="col">
              <div class="duration">
                <span class="tkt-badge tkt-badge-split">
                  <span class="tkt-badge-part tkt-dark-badge"><?php echo esc_html(tkt_t('Durée')) ?></span>
                  <span class="tkt-badge-part tkt-grey-badge"><?php echo esc_html($opaque->duration) ?> <?php echo esc_html(tkt_t('min')) ?></span>
                </span>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($countries)) : ?>
          <div class="row">
            <div class="col">
              <div class="countries">
                <span class="tkt-badge tkt-badge-split">
                  <?php // FIXME: plural ?>
                  <span class="tkt-badge-part tkt-dark-badge"><?php echo esc_html((substr_count($countries, ',') > 0) ? tkt_t('Payss') : tkt_t('Pays')) ?></span>
                  <span class="tkt-badge-part tkt-grey-badge"><?php echo esc_html($countries) ?></span>
                </span>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($original_languages)) : ?>
          <div class="row">
            <div class="col">
              <div class="original_languages">
                <span class="tkt-badge tkt-badge-split">
                  <span class="tkt-badge-part tkt-dark-badge"><?php echo esc_html((substr_count($original_languages, ',') > 0) ? tkt_t('Langues originales') : tkt_t('Langue originale')) ?></span>
                  <span class="tkt-badge-part tkt-grey-badge"><?php echo esc_html($original_languages) ?></span>
                </span>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($audio)) : ?>
          <div class="row">
            <div class="col">
              <div class="audio">
                <span class="tkt-badge tkt-badge-split">
                  <?php // FIXME: plural ?>
                  <span class="tkt-badge-part tkt-dark-badge"><?php echo esc_html((substr_count($audio, ',') > 0) ? tkt_t('Audios') : tkt_t('Audio')) ?></span>
                  <span class="tkt-badge-part tkt-grey-badge"><?php echo esc_html($audio) ?></span>
                </span>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($subtitles)) : ?>
          <div class="row">
            <div class="col">
              <div class="subtitles">
                <span class="tkt-badge tkt-badge-split">
                  <span class="tkt-badge-part tkt-dark-badge"><?php echo esc_html(tkt_t('Sous-titres')) ?></span>
                  <span class="tkt-badge-part tkt-grey-badge"><?php echo esc_html($subtitles) ?></span>
                </span>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($opaque->year)) : ?>
          <div class="row">
            <div class="col">
              <div class="subtitles">
                <span class="tkt-badge tkt-badge-split">
                  <span class="tkt-badge-part tkt-dark-badge"><?php echo esc_html(tkt_t('Année')) ?></span>
                  <span class="tkt-badge-part tkt-grey-badge"><?php echo esc_html($opaque->year) ?></span>
                </span>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($opaque->l_min_age)) : ?>
          <div class="row">
            <div class="col">
              <div class="l_min_age">
                <span class="tkt-badge tkt-badge-split">
                  <span class="tkt-badge-part tkt-dark-badge"><?php echo esc_html(tkt_t('Âge min. lég.')) ?></span>
                  <span class="tkt-badge-part tkt-grey-badge"><?php echo esc_html($opaque->l_min_age) ?></span>
                </span>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($opaque->s_min_age)) : ?>
          <div class="row">
            <div class="col">
              <div class="s_min_age">
                <span class="tkt-badge tkt-badge-split">
                  <span class="tkt-badge-part tkt-dark-badge"><?php echo esc_html(tkt_t('Âge min. sug.')) ?></span>
                  <span class="tkt-badge-part tkt-grey-badge"><?php echo esc_html($opaque->s_min_age) ?></span>
                </span>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($opaque->a_min_age)) : ?>
          <div class="row">
            <div class="col">
              <div class="a_min_age">
                <span class="tkt-badge tkt-badge-split">
                  <span class="tkt-badge-part tkt-dark-badge"><?php echo esc_html(tkt_t('Âge min. acc.')) ?></span>
                  <span class="tkt-badge-part tkt-grey-badge"><?php echo esc_html($opaque->a_min_age) ?></span>
                </span>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($opaque->free_text_1->{TKT_LANG})) : ?>
          <div class="row">
            <div class="col">
              <span class="free-text-one">
                <?php echo wp_kses_post($opaque->free_text_1->{TKT_LANG}) ?>
              </span>
            </div>
          </div>
          <?php endif; ?>

        </div>
        <?php endif; ?>

        <div class="col-md right-col">
          <?php if (!empty($opaque->people)) : ?>
          <h3 class="tkt-section-title"><?php echo esc_html(tkt_t('Distribution')) ?></h3>
          <?php foreach ($opaque->people as $p) : ?>
          <div class="row">
            <div class="col">
              <span class="tkt-badge tkt-badge-split">
                <span class="tkt-badge-part tkt-dark-badge"><?php echo !empty($p->activity) ? esc_html(ucfirst(strtolower(tkt_t($p->activity)))) : '' ?></span>
                <span class="tkt-badge-part tkt-grey-badge"><?php echo esc_html(implode(' ', array_filter([$p->fullname ?? null, $p->firstname ?? null, $p->lastname ?? null]))) ?></span>
              </span>
            </div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>

          <?php if (!empty($opaque->free_text_2->{TKT_LANG})) : ?>
          <div class="row">
            <div class="col">
              <div class="free-text-2">
                <span><?php echo wp_kses_post($opaque->free_text_2->{TKT_LANG}) ?></span>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>

      </div>
    </section>

    <section class="tkt-section tkt-dark-section book-section">
      <h3 class="tkt-section-title">
        <?php echo esc_html(tkt_t('Achetez vos billets')) ?>
      </h3>

      <?php echo do_shortcode('[tkt_booking_form ids="'.implode(',', $ids).'" /]') ?>

    </section>
  </div>
</div>
