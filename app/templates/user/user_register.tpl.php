<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * User registration template
 *
 * Input:
 * $data: {}
 */
$app = TKTApp::get_instance();
$requested_fields  = explode(',', $app->get_config('registration.requested_fields'));
$required_fields   = explode(',', $app->get_config('registration.required_fields'));

$recaptcha_public_key = TKTApp::get_instance()->get_config("integrations.recaptcha.public_key");
if (!function_exists('tkt_is_required')) {
    function tkt_is_required($required_fields, $field) {
        return in_array($field, $required_fields) ? 'required' : '';
    }
}
?>
<div class="tkt-wrapper" data-component="User/UserRegister">
  <section class="tkt-section tkt-light-section tkt-register-section">
    <div class="row">
      <div class="col">
        <form class="register-form">
        <?php if (!empty($recaptcha_public_key)) : ?>
          <!-- Google reCAPTCHA -->
          <input type="hidden" id="recaptchaResponse" name="recaptcha_client">
        <?php endif; ?>
          <?php if (!empty($requested_fields) || !empty($required_fields)) : ?>
          <fieldset id="registration-fields">
            <div class="row">
              <?php if (in_array('firstname', $requested_fields)) : ?>
              <div id="field-wrapper-firstname" class="field-wrapper col-md-6" style="display: block;">
                <div class="form-group">
                   <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'firstname')) ?>" for="firstname"><?php echo esc_html(tkt_t("Prénom")); ?></label>
                   <input name="user[contact][firstname]" type="text" class="tkt-input form-control data-field" id="firstname" placeholder="<?php echo esc_html(tkt_t("Votre prénom")) ?>" <?php echo esc_html(tkt_is_required($required_fields, 'firstname')) ?> />
                </div>
              </div>
              <?php endif; ?>

              <?php if (in_array('lastname', $requested_fields)) : ?>
              <div id="field-wrapper-lastname" class="field-wrapper col-md-6" style="display: block;">
                <div class="form-group">
                  <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'lastname')) ?>" for="lastname"><?php echo esc_html(tkt_t("Nom")); ?></label>
                  <input name="user[contact][lastname]" type="text" class="tkt-input form-control data-field" id="lastname" placeholder="<?php echo esc_html(tkt_t("Votre nom")) ?>" <?php echo esc_html(tkt_is_required($required_fields, 'lastname')) ?> />
                </div>
              </div>
              <?php endif; ?>

              <?php if (in_array('company', $requested_fields)) : ?>
              <div id="field-wrapper-company" class="field-wrapper col-md-12" style="display: block;">
                <div class="form-group">
                  <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'company')) ?>" for="company"><?php echo esc_html(tkt_t("Société")); ?></label>
                  <input name="user[contact][company]" type="text" class="tkt-input form-control data-field" id="company" placeholder="<?php echo esc_html(tkt_t("Votre société")) ?>" <?php echo esc_html(tkt_is_required($required_fields, 'company')) ?> />
                </div>
              </div>
              <?php endif; ?>

              <input name="user[name]" type="hidden" class="data-field">
              <div id="field-wrapper-email" class="field-wrapper col-6" style="display: block;">
                <div class="form-group">
                  <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'email')) ?>" for="email"><?php echo esc_html(tkt_t("Adresse e-mail")); ?></label>
                  <input name="user[contact][email]" type="email" class="tkt-input form-control data-field" id="email" placeholder="<?php echo esc_html(tkt_t("Votre adresse e-mail")) ?>" <?php echo esc_html(tkt_is_required($required_fields, 'email')) ?> pattern="[^@]+@[^@]+\.[a-zA-Z]{2,}" />
                </div>
              </div>
              <div id="field-wrapper-email" class="field-wrapper col-6" style="display: block;">
                <div class="form-group">
                  <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'email')) ?>" for="email"><?php echo esc_html(tkt_t("Confirmation de votre adresse e-mail")); ?></label>
                  <input name="user[contact][email2]" type="email" class="tkt-input form-control data-field" id="email" placeholder="<?php echo esc_html(tkt_t("Saisissez une deuxième fois votre adresse")) ?>" <?php echo esc_html(tkt_is_required($required_fields, 'email')) ?> />
                </div>
              </div>

              <?php if (in_array('address', $requested_fields)) : ?>
              <div id="field-wrapper-address" class="field-wrapper form-group col-12">
                <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'address')) ?>" for="address"><?php echo esc_html(tkt_t("Adresse")); ?></label>
                <textarea name="user[contact][address][street]" class="tkt-input form-control data-field" id="street" placeholder="<?php echo esc_html(tkt_t("Votre adresse")) ?>" <?php echo esc_html(tkt_is_required($required_fields, 'address')) ?>></textarea>
              </div>
              <?php endif; ?>

              <?php if (in_array('zip', $requested_fields)) : ?> <div id="field-wrapper-zip" class="field-wrapper col-md-6">
                <div class="form-group">
                  <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'zip')) ?>" for="zip"><?php echo esc_html(tkt_t("Numéro postal")); ?></label>
                  <input name="user[contact][address][zip]" type="text" class="tkt-input form-control data-field" id="zip" placeholder="<?php echo esc_html(tkt_t("NPA")) ?>" <?php echo esc_html(tkt_is_required($required_fields, 'zip')) ?> />
                </div>
              </div>
              <?php endif; ?>

              <?php if (in_array('city', $requested_fields)) : ?>
              <div id="field-wrapper-city" class="field-wrapper col-md-6">
                <div class="form-group">
                  <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'city')) ?>" for="city"><?php echo esc_html(tkt_t("Ville")); ?></label>
                  <input name="user[contact][address][city]" type="text" class="tkt-input form-control data-field" id="city" placeholder="<?php echo esc_html(tkt_t("Ville")) ?>" <?php echo esc_html(tkt_is_required($required_fields, 'city')) ?> />
                </div>
              </div>
              <?php endif; ?>

              <?php if (in_array('phone', $requested_fields)) : ?>
              <div id="field-wrapper-phone" class="field-wrapper form-group col-6">
                <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'phone')) ?>" for="phone"><?php echo esc_html(tkt_t("Téléphone")); ?></label>
                <input name="user[contact][phone]" type="tel" class="tkt-input form-control data-field" id="phone" placeholder="<?php echo esc_html(tkt_t("Votre numéro de téléphone")) ?>" <?php echo esc_html(tkt_is_required($required_fields, 'phone')) ?> />
              </div>
              <?php endif; ?>

              <?php if (in_array('cellphone', $requested_fields)) : ?>
              <div id="field-wrapper-cellphone" class="field-wrapper form-group col-6">
                <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'cellphone')) ?>" for="cellphone"><?php echo esc_html(tkt_t("Téléphone portable")); ?></label>
                <input name="user[contact][cellphone]" type="tel" class="tkt-input form-control data-field" id="cellphone" placeholder="<?php echo esc_html(tkt_t("Votre numéro de téléphone")) ?>" <?php echo esc_html(tkt_is_required($required_fields, 'cellphone')) ?> />
              </div>
              <?php endif; ?>

              <?php if (in_array('birthdate', $requested_fields)) : ?>
              <div id="field-wrapper-birthdate" class="col-md-12 field-wrapper form-group">
                <label class="required" for="birthdate"><?php echo esc_html(tkt_t('Date de naissance')) ?></label>
                <input name="user[contact][birthdate]" type="text" class="tkt-input form-control data-field" id="birthdate" data-component="Form/Calendar" placeholder="<?php echo esc_html(tkt_t("Date de naissance")) ?>" required data-alt-format="<?php echo esc_html(tkt_t('j F Y')) ?>" />
              </div>
              <?php endif; ?>


              <?php if (empty($cgv_url)) : ?>
              <input name="user[opaque][conditions]" value="checked" type="hidden" class="data-field">
              <?php else : ?>
              <div class="col-md-12 form-group">
                <div class="checkbox">
                  <label class="required">
                    <input name="user[opaque][conditions]" value="checked" type="checkbox" required class="data-field">
                    <?php echo wp_kses_post($terms_link) ?>
                  </label>
                </div>
              </div>
              <?php endif; ?>

              <div id="field-wrapper-password" class="field-wrapper col-6" style="display: block;">
                <div class="form-group">
                  <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'password')) ?>" for="password"><?php echo esc_html(tkt_t("Mot de passe")); ?></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="tkt-icon-lock"></i>
                      </span>
                    </div>
                    <input name="user[password]" type="password" class="tkt-input form-control data-field" id="password" placeholder="<?php echo esc_html(tkt_t("Choisissez un mot de passe")) ?>"  autocomplete="new-password" />
                  </div>
                </div>
              </div>
              <div id="field-wrapper-password" class="field-wrapper col-6" style="display: block;">
                <div class="form-group">
                  <label class="<?php echo esc_attr(tkt_is_required($required_fields, 'password')) ?>" for="password"><?php echo esc_html(tkt_t("Confirmation de votre mot de passe")); ?></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="tkt-icon-lock"></i>
                      </span>
                    </div>
                    <input name="user[password2]" type="password" class="tkt-input form-control data-field" id="password" placeholder="<?php echo esc_html(tkt_t("Saisissez une deuxième fois votre mot de passe")) ?>" required autocomplete="new-password" />
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                  <p id="notice-required" class="small"><?php echo esc_html(tkt_t('Tous ces champs sont requis')) ?></p>
              </div>

            </div>
          </fieldset>
          <?php endif; ?>

          <div id="registration-messages" class="row mt-2">
            <div class="col-12">
              <div style="display: none;" class="text-center alert alert-info info-msg"></div>
              <div style="display: none;" class="text-center alert alert-danger error-msg"></div>
              <div style="display: none;" class="text-center alert alert-success success-msg">
                <?php echo esc_html(tkt_t('Votre compte a bien été créé')) ?><br/>
                <b><?php echo esc_html(tkt_t('Veuillez l\'activer en cliquant sur le lien que vous avez reçu par e-mail.')) ?></b><br /><br />
                <?php if (tkt_login_url()) : ?>
                <a class="btn button" href="<?php echo esc_attr(tkt_login_url()) ?>">
                  <i class="tkt-icon-sign-in-alt"></i>
                  <?php echo esc_html(tkt_t('Connexion')) ?>
                </a>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <fieldset id="registration-submit" >
            <div id="submit-section" class="row">
              <div class="col-md-12 text-center">
                <button type="submit" class="submit-button button">
                  <span class="glyphicon glyphicon-ok"></span> <?php echo esc_html(tkt_t('Créer mon compte')) ?>
                </button>
              </div>
            </div>

            <?php if (tkt_login_url()) : ?>
            <hr />
            <div class="row">
                <div class="col text-center">
                    <a href="<?php echo esc_attr(tkt_login_url()) ?>">
                        <?php echo esc_html(tkt_t('Déjà un compte ? Connectez-vous !')) ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>

          </fieldset>

        </form>
      </div>
    </div>
  </section>
</div>
<?php if (!empty($recaptcha_public_key)) : ?>
  <!-- Google reCAPTCHA -->
  <script src="https://www.google.com/recaptcha/api.js?render=<?php echo esc_html($recaptcha_public_key) ?>"></script>
  <script>
    grecaptcha.ready(function() {
        grecaptcha.execute('<?php echo esc_html($recaptcha_public_key) ?>', {action: 'register'}).then(function(token) {
            document.getElementById('recaptchaResponse').value = token
        });
    });
  </script>
<?php endif; ?>
