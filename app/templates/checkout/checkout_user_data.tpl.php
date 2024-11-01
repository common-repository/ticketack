<?php

if (!defined('ABSPATH')) exit;

use Ticketack\WP\TKTApp;

/**
 * Checkout user data form
 * This template will be parsed by underscore.js
 *
 * Input: {
 *   "items": Array of CartItem instances,
 *   "requestedFields": Array of requested fields,
 *   "requiredFields": Array of required fields,
 * }
 */
?>
<%
function acceptsField(field) {
    return requestedFields.includes(field);
}

function needsField(field) {
    return requiredFields.includes(field);
}

%>
<% if (items && items.length  >0) { %>
<div class="tkt-wrapper">
    <div class="row">
        <div class="col">
            <h3 class="tkt-section-title mb-4">
                <?php echo esc_html(tkt_t('Titulaire')) ?>
            </h3>
            <p class="mb-4">
                <?php echo esc_html(tkt_t('Dans le cadre des conditions sanitaires actuelles, nous sommes tenus de demander à chaque détenteur de ticket les informations nécessaires pour le contacter si besoin.<br/>Nous vous remercions donc de bien vouloir remplir les informations ci-dessous.')) ?>
            </p>
        </div>
    </div>
    <form class="user-data-form">
        <% items.map(function (item, i) { %>
        <div class="user-data-wrapper">
            <div class="user-data-title">
                <?php echo esc_html(tkt_t('Ticket' )) ?> <%= i + 1 %> - <%= item.name %>
            </div>
            <div class="user-data-fields">
                <div class="row">
                    <% if (acceptsField('company'))  { %>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label <%= needsField('company') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Entreprise')); ?></label>
                                <input name="user_data[index-<%= item.index %>][company]" type="text" class="form-control" placeholder="<?php echo esc_html(tkt_t('Votre entreprise')); ?>" value="<%= item.getUserData('company') %>" <%= needsField('company') ? 'required' : '' %>>
                            </div>
                        </div>
                    <% } %>

                    <% if (acceptsField('firstname'))  { %>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label <%= needsField('firstname') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Prénom')); ?></label>
                                <input name="user_data[index-<%= item.index %>][firstname]" type="text" class="form-control" placeholder="<?php echo esc_html(tkt_t('Votre prénom')); ?>" value="<%= item.getUserData('firstname') %>" <%= needsField('firstname') ? 'required' : '' %>>
                            </div>
                        </div>
                    <% } %>

                    <% if (acceptsField('lastname'))  { %>
                        <div class="col-md-6 col-md-offset-1">
                            <div class="form-group">
                                <label <%= needsField('lastname') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Nom')); ?></label>
                                <input name="user_data[index-<%= item.index %>][lastname]" type="text" class="form-control" placeholder="<?php echo esc_html(tkt_t('Votre nom')); ?>" value="<%= item.getUserData('lastname') %>" <%= needsField('lastname') ? 'required' : '' %>>
                            </div>
                        </div>
                    <% } %>

                    <% if (acceptsField('email'))  { %>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label <%= needsField('email') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Adresse e-mail')); ?></label>
                                <input name="user_data[index-<%= item.index %>][email]" type="email" class="form-control" placeholder="<?php echo esc_html(tkt_t('Votre adresse e-mail')) ?>" value="<%= item.getUserData('email') %>" <%= needsField('email') ? 'required' : '' %>>
                            </div>
                    </div>
                    <% } %>

                    <% if (acceptsField('address'))  { %>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label <%= needsField('address') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Adresse')); ?></label>
                                <textarea name="user_data[index-<%= item.index %>][address]" class="form-control" placeholder="<?php echo esc_html(tkt_t('Votre adresse')); ?>" <%= needsField('address') ? 'required' : '' %>><%= item.getUserData('address') %></textarea>
                            </div>
                        </div>
                    <% } %>

                    <% if (acceptsField('zip'))  { %>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label <%= needsField('zip') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Code postal')) ?></label>
                                <input name="user_data[index-<%= item.index %>][zip]" type="text" class="form-control" placeholder="<?php echo esc_html(tkt_t('NPA')); ?>" value="<%= item.getUserData('zip') %>" <%= needsField('zip') ? 'required' : '' %>>
                            </div>
                        </div>
                    <% } %>

                    <% if (acceptsField('city'))  { %>
                        <div class="<%= acceptsField('zip') ? 'col-md-7 col-md-offset-1' : 'col-md-12' %>">
                            <div class="form-group">
                                <label <%= needsField('city') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Ville')); ?></label>
                                <input name="user_data[index-<%= item.index %>][city]" type="text" class="form-control" placeholder="<?php echo esc_html(tkt_t('Ville')); ?>" value="<%= item.getUserData('city') %>" <%= needsField('city') ? 'required' : '' %>>
                            </div>
                        </div>
                    <% } %>

                    <% if (acceptsField('country'))  { %>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label <%= needsField('country') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Pays')); ?></label>
                                <select name="user_data[index-<%= item.index %>][country]" class="form-control" <%= needsField('country') ? 'required' :
         '' %>>
                                    <option value=""></option>
                                    <?php foreach (tkt_get_countries() as $country) : ?>
                                    <option value="<?php echo esc_attr($country[TKT_LANG]) ?>"><?php echo esc_html($country[TKT_LANG]) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <% } %>

                    <% if (acceptsField('phone'))  { %>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label <%= needsField('phone') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Téléphone')) ?></label>
                                <input class="form-control" type="tel" pattern="^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$" placeholder="<?php echo esc_html(tkt_t("Votre numéro, sans espace")) ?>" value="<%= item.getUserData('phone') %>" name="user_data[index-<%= item.index %>][phone]" <%= needsField('phone') ? 'required' : '' %>>
                            </div>
                        </div>
                    <% } %>

                    <% if (acceptsField('cellphone'))  { %>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label <%= needsField('cellphone') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Téléphone portable')) ?></label>
                                <input class="form-control" type="tel" pattern="^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$" placeholder="<?php echo esc_html(tkt_t("Votre numéro, sans espace")) ?>" value="<%= item.getUserData('cellphone') %>" name="user_data[index-<%= item.index %>][cellphone]" <%= needsField('cellphone') ? 'required' : '' %>>
                            </div>
                        </div>
                    <% } %>

                    <% if (acceptsField('age'))  { %>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label <%= needsField('age') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Âge')) ?></label>
                                <select name="user_data[index-<%= item.index %>][age]" class="form-control" <%= needsField('age') ? 'required' : '' %>>
                                    <option value=""></option>
                                    <?php
                                    $ages = tkt_get_ages();
                                    foreach ($ages as $age) {
                                        echo '<option value="' . esc_attr($age) . '">' . esc_html($age) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    <% } %>

                    <% if (acceptsField('birthdate'))  { %>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label <%= needsField('birthdate') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Date de naissance')) ?></label>
                                <input class="form-control datepickerize" type="text" name="user_data[index-<%= item.index %>][birthdate]" data-date-format="DD.MM.YYYY" <%= needsField('birthdate') ? 'required' : '' %>>
                            </div>
                        </div>
                    <% } %>

                    <% if (acceptsField('sex'))  { %>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label <%= needsField('sex') ? 'class="required"' : '' %>><?php echo esc_html(tkt_t('Genre')) ?></label>
                                <select name="user_data[index-<%= item.index %>][sex]" class="form-control" <%= needsField('sex') ? 'required' : '' %>>
                                    <option value=""></option>
                                    <?php
                                    $sexes = tkt_get_sexes();
                                    foreach ($sexes as $sex_key => $sex_value) {
                                        echo '<option value="' . esc_attr($sex_key) . '">' . esc_html($sex_value) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    <% } %>

                    <div class="col-md-12">
                        <p class="small"><?php echo esc_html(tkt_t('* ces champs sont requis')) ?></p>
                    </div>

                </div>
            </div>
        </div>
    <% }) %>

        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn button btn-block checkout-user-data-button">
                    <?php echo esc_html(tkt_t('Enregistrer')) ?>
                </button>
            </div>
        </div>

    </form>

    <div class="row mt-2">
        <div class="col-12">
            <div style="display: none;" class="text-center alert alert-info info-msg"></div>
            <div style="display: none;" class="text-center alert alert-success success-msg"></div>
            <div style="display: none;" class="text-center alert alert-danger error-msg"></div>
        </div>
    </div>
</div>
<% } %>
