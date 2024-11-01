<?php

namespace Ticketack\WP\Helpers;

/**
 * Some i18n helper functions
 */
class LocalesHelper
{
    public static function dump_js_locales()
    {
        return [
            // Note: we need to write it this way so as xgettext can find our translations... ;(
            'Veuillez choisir au moins un billet'                                          => tkt_t('Veuillez choisir au moins un billet'),
            'Veuillez remplir les deux champs'                                             => tkt_t('Veuillez remplir les deux champs'),
            'Les informations que vous avez saisies sont invalides'                        => tkt_t('Les informations que vous avez saisies sont invalides'),
            'Une erreur est survenue. Veuillez ré-essayer ultérieurement.'                 => tkt_t('Une erreur est survenue. Veuillez ré-essayer ultérieurement.'),
            'Vous ne pouvez pas réserver de place pour cette séance avec votre abonnement.'      => tkt_t('Vous ne pouvez pas réserver de place pour cette séance avec votre abonnement.'),
            'Vous ne pouvez pas réserver une place de plus pour cette séance avec votre abonnement.' => tkt_t('Vous ne pouvez pas réserver une place de plus pour cette séance avec votre abonnement.'),
            'Veuillez choisir un tarif'                                                    => tkt_t('Veuillez choisir un tarif'),
            'Votre panier a été mis à jour'                                                => tkt_t('Votre panier a été mis à jour'),
            'Code promo invalide'                                                          => tkt_t('Code promo invalide'),
            'Impossible d\'utiliser ce code promo'                                         => tkt_t('Impossible d\'utiliser ce code promo'),
            'Le code promo a bien été pris en compte'                                      => tkt_t('Le code promo a bien été pris en compte'),
            'Vous disposez de'                                                             => tkt_t('Vous disposez de'),
            'sur votre porte monnaie électronique'                                         => tkt_t('sur votre porte monnaie électronique'),
            'Montant trop élevé'                                                           => tkt_t('Montant trop élevé'),
            'title'                                                                        => tkt_t('Nom'),
            'name'                                                                         => tkt_t('Nom'),
            'firstname'                                                                    => tkt_t('Prénom'),
            'lastname'                                                                     => tkt_t('Nom'),
            'email'                                                                        => tkt_t('E-mail'),
            'password'                                                                     => tkt_t('Mot de passe'),
            'address'                                                                      => tkt_t('Adresse'),
            'street'                                                                       => tkt_t('Rue'),
            'zip'                                                                          => tkt_t('Code postal'),
            'city'                                                                         => tkt_t('Ville'),
            'country'                                                                      => tkt_t('Pays'),
            'phone'                                                                        => tkt_t('Téléphone'),
            'cellphone'                                                                    => tkt_t('Télépĥone portable'),
            'birthdate'                                                                    => tkt_t('Date de naissance'),
            'rfc2397_portrait'                                                             => tkt_t('Photo'),
            'age'                                                                          => tkt_t('Âge'),
            'sex'                                                                          => tkt_t('Sexe'),
            'language'                                                                     => tkt_t('Langue'),
            'ENOTSUP'                                                                      => tkt_t('n\'est pas supporté'),
            'ENOPROP'                                                                      => tkt_t('est manquant'),
            'EDUPLICATE'                                                                   => tkt_t('existe déjà'),
            'EISNOTSTRING'                                                                 => tkt_t('n\'est pas une chaine de caractère'),
            'EEMPTY'                                                                       => tkt_t('est vide'),
            'ETOOSHORT'                                                                    => tkt_t('est trop court'),
            'ETOOBIG'                                                                      => tkt_t('est trop grand'),
            'EINVAL'                                                                       => tkt_t('est invalide'),
            'EISNOTOBJ'                                                                    => tkt_t('n\'est pas un objet'),
            'EISNOTARRAY'                                                                  => tkt_t('n\'est pas un tableau'),
            'EISNOTBOOL'                                                                   => tkt_t('n\'est pas un booléen'),
            'PENDING_STATUS'                                                               => tkt_t('En attente'),
            'OPEN'                                                                         => tkt_t('En cours'),
            'PAYING'                                                                       => tkt_t('Paiement en cours'),
            'PAID'                                                                         => tkt_t('Payée'),
            'COMPLETED'                                                                    => tkt_t('Terminée'),
            'ACTIVATION_ERROR'                                                             => tkt_t('Erreur'),
            'CANCELED'                                                                     => tkt_t('Annulée'),
            'POSTFINANCE'                                                                  => tkt_t('Postfinance'),
            'PROXYPAY'                                                                     => tkt_t('Proxypay'),
            'ESHOP_TRANSFER'                                                               => tkt_t('Virement'),
            'NULL_PAYMENT'                                                                 => tkt_t('Gratuit'),
            'LATER_PAYMENT'                                                                => tkt_t('Réservation'),
            'POS_CASH'                                                                     => tkt_t('Espèces'),
            'POS_CASH_COLLECTOR'                                                           => tkt_t('Monnayeur'),
            'POS_SUMUP'                                                                    => tkt_t('Carte bancaire'),
            'POS_OTHER_EFT'                                                                => tkt_t('Carte bancaire'),
            'POS_ZVT_EFT'                                                                  => tkt_t('Carte bancaire'),
            'POS_WALLET'                                                                   => tkt_t('Porte-monnaie'),
            'POS_TRANSFER'                                                                 => tkt_t('Virement'),
            'ACTIVATED'                                                                    => tkt_t('Activé'),
            'NEW'                                                                          => tkt_t('Inactif'),
            'PENDING'                                                                      => tkt_t('En attente de paiement'),
            'BLOCKED'                                                                      => tkt_t('Bloqué'),
            'Director'                                                                     => tkt_t('directeur'),
            'Directors'                                                                    => tkt_t('directeurs'),
            'réservation'                                                                  => tkt_t('réservation'),
            'réservations'                                                                 => tkt_t('réservations'),
            'sur'                                                                          => tkt_t('sur'),
            'disponible'                                                                   => tkt_t('disponible'),
            'disponibles'                                                                  => tkt_t('disponibles'),
        ];
    }

    /**
     * This function is used to let gettext know about some dynamic strings
     * that should be translated (Kronos data, ...)
     */
    private static function dummy_function_for_dynamic_contents()
    {
        $dummy = [
            tkt_t('actor'),
            tkt_t('actors'),
            tkt_t('cast'),
            tkt_t('camera'),
            tkt_t('cinematographer'),
            tkt_t('co-producer'),
            tkt_t('co-producers'),
            tkt_t('creator'),
            tkt_t('creators'),
            tkt_t('director'),
            tkt_t('directors'),
            tkt_t('editor'),
            tkt_t('editing'),
            tkt_t('music'),
            tkt_t('printcontact'),
            tkt_t('producer'),
            tkt_t('producers'),
            tkt_t('photography'),
            tkt_t('screenplay'),
            tkt_t('screenwriter'),
            tkt_t('writer'),
            tkt_t('writers'),
            tkt_t('sound'),
            tkt_t('production design'),
            // for some reason those two are not caught by translate.sh (used in app/templates/cart/cart_table.tpl.php)
            tkt_t("produit"),
            tkt_t("produits"),
        ];
    }
}
