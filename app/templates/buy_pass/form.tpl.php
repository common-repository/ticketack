<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Buy pass form template
 *
 * Input:
 * $data: {
 *   "tickettypes": [ ... ],
 *   "selected": "festival_pass",
 *   "theme"   : "dark|light",
 * }
 */
// keep in sync with Image::PASS_PHOTO_WIDTH from eshop
$width = 300;
?>
<div class="tkt-wrapper">
  <section class="tkt-section tkt-<?php echo esc_attr($data->theme) ?>-section tkt-pass-form-section">
    <h3 class="tkt-section-title mb-3">
      <?php echo esc_html(tkt_t("Titulaire")) ?>
    </h3>
    <div class="row">
      <div class="col">
        <form>
          <fieldset>
            <div class="row">
              <div id="field-wrapper-firstname" class="field-wrapper col-md-4" style="display: block;">
                <div class="form-group">
                   <label class="required" for="firstname"><?php echo esc_html(tkt_t("Prénom")); ?></label>
                   <input name="firstname" type="text" class="tkt-input form-control field" id="firstname" placeholder="<?php echo esc_html(tkt_t("Votre prénom")) ?>" required />
                </div>
              </div>

              <div id="field-wrapper-lastname" class="field-wrapper col-md-4" style="display: block;">
                <div class="form-group">
                  <label class="required" for="lastname"><?php echo esc_html(tkt_t("Nom")); ?></label>
                  <input name="lastname" type="text" class="tkt-input form-control field" id="lastname" placeholder="<?php echo esc_html(tkt_t("Votre nom de famille")) ?>" required />
                </div>
              </div>

              <div id="field-wrapper-email" class="field-wrapper col-md-4" style="display: block;">
                <div class="form-group">
                  <label class="required" for="email"><?php echo esc_html(tkt_t("E-mail")); ?></label>
                  <input name="email" type="email" class="tkt-input form-control field" id="email" placeholder="<?php echo esc_html(tkt_t("Votre adresse e-mail")) ?>" pattern="[^@]+@[^@]+\.[a-zA-Z]{2,}" required />
                </div>
              </div>
            </div>

            <div class="row">

              <div id="field-wrapper-address" class="field-wrapper form-group col-md-4">
                <label class="required" for="address"><?php echo esc_html(tkt_t("Adresse")) ?></label>
                <textarea name="address" class="tkt-input form-control field" id="address" placeholder="<?php echo esc_html(tkt_t("Votre adresse")) ?>" required></textarea>
              </div>

              <div id="field-wrapper-zip" class="field-wrapper col-md-4">
                <div class="form-group">
                  <label class="required" for="zip"><?php echo esc_html(tkt_t("Numéro postal")) ?></label>
                  <input name="zip" type="text" class="tkt-input form-control field" id="zip" placeholder="<?php echo esc_html(tkt_t("NPA")) ?>" required />
                </div>
              </div>

              <div id="field-wrapper-city" class="field-wrapper col-md-4">
                <div class="form-group">
                  <label class="required" for="city"><?php echo esc_html(tkt_t("Ville")) ?></label>
                  <input name="city" type="text" class="tkt-input form-control field" id="city" placeholder="<?php echo esc_html(tkt_t("Ville")) ?>" required />
                </div>
              </div>
            </div>

            <div class="row">
              <div id="field-wrapper-country" class="form-group field-wrapper col-md-12" style="display: none;">
                <label class="required" for="country"><?php echo esc_html(tkt_t("Pays")) ?></label>
                <select name="country" id="country" class="tkt-input form-control field" required>
                  <option value=""></option>
                  <?php $countries = tkt_get_countries();
                  foreach ($countries as $country) {
                    echo '<option value="'.esc_attr($country[tkt_current_lang()]).'">'.esc_html($country[tkt_current_lang()]).'</option>';
                  } ?>
                </select>
              </div>
            </div>

            <div class="row">
              <div id="field-wrapper-phone" class="field-wrapper col-md-6">
                <div class="form-group">
                  <label class="required" for="phone"><?php echo esc_html(tkt_t("Téléphone")) ?></label>
                  <input name="phone" type="text" class="tkt-input form-control field" id="phone" placeholder="<?php echo esc_html(tkt_t("Téléphone")) ?>" required />
                </div>
              </div>
              <div id="field-wrapper-cellphone" class="field-wrapper col-md-6">
                <div class="form-group">
                  <label class="required" for="cellphone"><?php echo esc_html(tkt_t("Téléphone portable")) ?></label>
                  <input name="cellphone" type="text" class="tkt-input form-control field" id="cellphone" placeholder="<?php echo esc_html(tkt_t("Téléphone portable")) ?>" required />
                </div>
              </div>
            </div>

            <div class="row">
              <div id="field-wrapper-age" class="col-md-12 field-wrapper form-group" style="display: none;">
                <label class="required" for="age"><?php echo esc_html(tkt_t('Âge')) ?></label>
                <select name="age" id="age" class="tkt-input form-control field" required>
                  <option value=""></option>
                  <?php
                    $ages     = tkt_get_ages();
                    foreach ($ages as $age) {
                        echo '<option value="'.esc_attr($age).'">'.esc_html($age).'</option>';
                    }
                  ?>
                </select>
              </div>
            </div>

            <div class="row">
              <div id="field-wrapper-birthdate" class="col-md-12 field-wrapper form-group" style="display: none;">
                <label class="required" for="birthdate"><?php echo esc_html(tkt_t('Date de naissance')) ?></label>
                <input name="birthdate" type="text" class="tkt-input form-control field" id="birthdate" data-component="Form/Calendar" placeholder="<?php echo esc_html(tkt_t("Date de naissance")) ?>" required data-date-format="d.m.Y" />
              </div>
            </div>

            <div class="row">
              <div id="field-wrapper-sex" class="col-md-12 field-wrapper form-group" style="display: none;">
                <label class="required" for="sex"><?php echo esc_html(tkt_t('Genre')) ?></label>
                <select name="sex" id="sex" class="tkt-input form-control field" required>
                  <option value=""></option>
                    <?php
                      $sexes = tkt_get_sexes();
                      foreach ($sexes as $sex_key => $sex_value) {
                          echo '<option value="'.esc_attr($sex_key).'">'.esc_html($sex_value).'</option>';
                      }
                    ?>
                </select>
              </div>
            </div>


            <div class="row">
              <div id="field-wrapper-photo" class="field-wrapper col-md-12" style="display: none;"> 
                <label class="photo"><?php echo esc_html(tkt_t('Photo')) ?></label>
                <div class="col-md-12 form-group tkt-wrapper-photo" data-component="Media/Webcam" data-width="<?php echo esc_attr($width) ?>">
                    <div class="row">
                      <div class="form-group col-md-6 mt-3 text-center">
                        <input name="photo" type="file" class="tkt-input form-control field" id="photo" accept="image/jpeg, image/png, image/gif, image/webp, image/avif" data-component="Form/ImageDataUrl" data-width="<?php echo esc_attr($width) ?>" hidden >
                        <button type="button" class="btn btn-default choose-btn">
                          <span><i class="tkt-icon-image"></i> <?php echo esc_html(tkt_t('Choisir une image')) ?></span>
                        </button>
                        <i class="tkt-icon-info" data-component="Ui/Tippy" data-tippy-content="<?php echo esc_html(tkt_t("Formats pris en charge : jpeg, png, webp, avif ou gif")) ?>"></i><br>
                        <span class="small">ou</span>
                        <div class="form-group col-md-12 p-0 mt-3">
                            <button type="button" class="btn btn-default">
                              <i class="tkt-icon-camera"></i>
                              <span class="stop_video"> <?php echo esc_html(tkt_t('Arrêter la vidéo')) ?></span>
                              <span class="start_video"> <?php echo esc_html(tkt_t('Prendre une photo depuis la caméra')) ?></span>
                            </button>
                            <button type="button" class="btn btn-success takePicture">
                                <?php echo esc_html(tkt_t('Prendre une photo')) ?>
                            </button>
                        </div>
                      </div>
                      <div class="form-group preview col-md-6">
                        <div class="contentarea">
                            <div class="camera form-group">
                                <video id="video" class="mt-3 mb-3" style="display: block">
                                    <?php echo esc_html(tkt_t('Vidéo non supportée')) ?>
                                </video>
                            </div>
                            <canvas id="canvas" style="display: none;"></canvas>
                        </div>
                        <img class="image-preview" width="<?php echo esc_attr($width) ?>" />
                      </div>
                      <div class="col-md-12">
                      <em class="small"><?php echo esc_html(tkt_t("Sans photo clairement reconnaissable, nous nous réservons le droit de vous refuser l'entrée. En cas d'abus, votre billet pourra être bloqué.")); ?></em>
                      </div>
                    </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div id="field-wrapper-comment" class="field-wrapper form-group col-md-4">
                <label class="required" for="comment"><?php echo esc_html(tkt_t("Commentaires")) ?></label>
                <textarea name="comment" class="tkt-input form-control field" id="comment" placeholder="<?php echo esc_html(tkt_t("Votre commentaire")) ?>" required></textarea>
              </div>
            </div>
            <div class="row">
              <div id="field-wrapper-gift_message" class="col-md-12 field-wrapper form-group" style="display: none;">
                <input id="gift_toggl" type="checkbox">
                <label for="gift_message"><?php echo esc_html(tkt_t("Ceci est un cadeau")); ?></label>
                <textarea id="user_gift_message" class="form-control" name="gift_message" data-default="Profitez de votre cadeau !" style="display: none;"></textarea>
              </div>
            </div>
          </fieldset>

          <fieldset>
            <div id="submit-section" class="row">
              <div class="col-md-12 text-right">
                <p id="notice-required" class="small"><?php echo esc_html(tkt_t('Ces champs sont requis')) ?></p>
                <button type="submit" class="button active btn-block">
                  <i class="glyphicon glyphicon-shopping-cart"></i><?php echo esc_html(tkt_t("Ajouter au panier")) ?>
                </button>
              </div>
            </div>
          </fieldset>
          <div class="row mt-2">
              <div class="col-md-12 alert alert-success"></div>
              <div class="col-md-12 alert alert-danger"></div>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
