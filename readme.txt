=== Ticketack ===
Tags: ticketack
Requires at least: 5.9
Tested up to: 6.5.5
Stable tag: 2.80.2
Requires PHP: 8.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Ticketack integration plugin.

== Description ==

Official Ticketack integration plugin.

More infos about [Ticketack](https://ticketack.com).

Main features:

*   Display upcoming movies and screenings
*   Many display layouts (list, grid, gallery...)
*   Full add to cart and checkout process

Integrations:

*   Get access to [Kronos](https://kronos.ticketack.com) directly from
    the admin menu
*   Synchronize your [Eventival](https://eventival.com) guests.
*   Embed your [Pantaflix](https://www.pantaflixgroup.com/) VOD players.
*   All your images are cached and resized using [Weserv](https://wsrv.nl/)
    service.

Please note that you must have been provided some configuration informations
from the Ticketack team to be able to use this plugin.

Privacy policies:

*   [Ticketack](https://netoxygen.ch/conditions-generales/protection-des-donnees/)
*   [Kronos](https://netoxygen.ch/conditions-generales/protection-des-donnees/)
*   [Eventival](https://www.eventival.com/en/privacy-policy)
*   [Pantaflix](https://www.pantaflixgroup.com/site/assets/files/2435/180620_datenschutz_hauptversammlung_en.pdf)
*   [Weserv](https://github.com/weserv/images/blob/5.x/Privacy-Policy.md)

== Installation ==

This section describes how to install the plugin and get it working.

1. Activate the plugin through the 'Plugins' screen in WordPress
2. Use the Settings->Ticketack screen to configure the plugin

== Frequently Asked Questions ==

== Screenshots ==

== Development ==

Interested in development? [Browse the
code](https://github.com/NetOxygen/wpticketack) and submit a Pull Request.

== Changelog ==

= Changelog =
* [Changelog link](https://yoda.netoxygen.ch/public/projects/401b5210-986f-46b3-99cb-38546b73e16c/changelog)

= 2.80.2 =

* fix: Fix some escaping errors
* modified templates:
  - app/templates/ticket/ticket.tpl.php
  - app/templates/user/account/votes/content.tpl.php

= 2.80.1 =

* fix: Fix some warnings
* fix: Fix some escaping errors
* fix: Better handle booking modes and notes
* fix: Fix the compatibility with SiteOrigin page builder
* modified templates:
  - app/templates/booking/form_pricings.tpl.php
  - app/templates/pantaflix/login.tpl.php
  - app/templates/ticket/ticket_connect.tpl.php
  - app/templates/user/account/tickets/content.tpl.php
  - app/templates/event/_single.tpl.php

= 2.80.0 =

* feat: Expose the JS components loader
* fix: Fix some escaping errors

= 2.79.3 =

* Finish Wordpress review

= 2.79.2 =

* Fix deployment script

= 2.79.1 =

* Consider Wordpress review

= 2.78.4 =

* fix: Fix wordpress compliance
* fix: Fix sync helper compliance with PHP 8

= 2.78.3 =

* fix: Fix votes default value

= 2.78.1 =

* fix: Fix the TicketConnect module

= 2.78.0 =

* feat: Consider votes global settings
* fix: Force refresh profile when needed
* modified templates:
  - app/templates/ticket/ticket.tpl.php
  - app/templates/user/account/logout/menu.tpl.php
  - app/templates/user/account/votes/content.tpl.php
  - app/templates/user/user_login.tpl.php

= 2.77.1 =

* fix: Fix a bug related to unactivated tickets
* fix: Fix a bug related to email inputs

= 2.77.0 =

* feat: Filter ticket on current edition, if any
* fix: Fix a bug related to deleted tickets

= 2.76.0 =

* feat: Introduce new agenda layout for program shortcode

= 2.75.1 =

* fix: Fix a bug related to the salepoints settings override

= 2.75.0 =

* feat: Implement comment field in holder fields
* feat: Add more baseline supported image formats
* feat: User proxy image configuration from ticketack settings
* fix: Don't consider one-time-passes as eligible type for the purpose of showing book with a pass Implements B2325
* modified templates:
  - app/templates/buy_pass/form.tpl.php
  - app/templates/booking/form_pricings.tpl.php

= 2.74.1 =

* fix: Correction of information messages in booking form
* fix: Fix the double backslash in icomoon (You need to save Advanced plugin
  settings)

= 2.74.0 =

* fix: Better handle communication errors with Ticketack
* feat: Switch company field from textarea to input
* modified templates:
  - app/templates/checkout/checkout_form.tpl.php
  - app/templates/checkout/checkout_user_data.tpl.php

= 2.73.0 =

* feat: Introduce comments and company user fields
* modified templates:
  - app/templates/checkout/checkout_form.tpl.php
  - app/templates/checkout/checkout_user_data.tpl.php

= 2.72.1 =

* feat: Tickets guests are now visible
* modified templates:
  - app/templates/ticket/ticket.tpl.php

= 2.71.0 =

* feat: Payment method names come from the Kronos configuration
* modified templates:
  - app/templates/checkout/checkout_form.tpl.php

= 2.70.2 =

* fix: Fix a bug in the booking form shortcode
* modified templates:
  - app/templates/booking/form_pricings.tpl.php

= 2.70.1 =

* fix: Conflict between the shortcodes buy_pass and ticket_connect

= 2.70.0 =

* feat: Adding the template blocks for the shortcode 'tkt_program'

= 2.69.0 =

* i18n: Improve translations

* feat: Hide pricings if no seats are available
* modified templates:
  - app/templates/booking/form_pricings.tpl.php

* feat: tkt icons pack
* modified templates:
  - app/templates/user/user_register.tpl.php
  - app/templates/user/user_login.tpl.php
  - app/templates/user/user_account.tpl.php
  - app/templates/user/account/votes/menu.tpl.php
  - app/templates/user/account/votes/content.tpl.php
  - app/templates/user/account/tickets/menu.tpl.php
  - app/templates/user/account/tickets/content.tpl.php
  - app/templates/user/account/profile/menu.tpl.php
  - app/templates/user/account/profile/content.tpl.php
  - app/templates/user/account/orders/menu.tpl.php
  - app/templates/program/gallery/event.tpl.php
  - app/templates/user/account/logout/menu.tpl.php
  - app/templates/pantaflix/login.tpl.php
  - app/templates/cart/cart_table.tpl.php
  - app/templates/buy_pass/form.tpl.php
  - app/templates/booking_wizard/wizard_menu.tpl.php
  - app/templates/booking/form_pricings.tpl.php

* fix: Fix movies description in grid and list templates
* modified templates:
  - app/templates/program/grid/event.tpl.php
  - app/templates/program/list/event.tpl.php

= 2.68.2 =

* fix: Remove PHP 7.4 compatibility
* modified templates:
  - app/templates/booking/screenings_list_pricings.tpl.php

= 2.68.1 =

* fix: Fix PHP compatibility

= 2.68.0 =

* feat: Template improvements from JFT
* modified templates:
  - app/templates/event/_package.tpl.php
  - app/templates/event/_single.tpl.php

* feat: Remove unused redirect parameter
* modified templates:
  - app/templates/buy_pass/buy.tpl.php
  - app/templates/buy_pass/form.tpl.php
  - app/templates/buy_pass/pass_list.tpl.php
  - app/templates/user/user_register.tpl.php

* fix: Only variables sould be passed by reference
* modified templates:
  - app/templates/program/gallery/screening.tpl.php
  - app/templates/program/grid/screening.tpl.php
  - app/templates/program/list/screening.tpl.php

* feat: Update template tkt_programm
* modified templates:
  - app/templates/program/gallery/event.tpl.php
  - app/templates/program/gallery/screenings.tpl.php
  - app/templates/program/grid/event.tpl.php
  - app/templates/program/grid/screening.tpl.php
  - app/templates/program/grid/screenings.tpl.php
  - app/templates/program/list/event.tpl.php
  - app/templates/program/list/event_legacy.tpl.php
  - app/templates/program/list/screening.tpl.php

* feat: Update template tkt_cart_summary
* modified templates:
  - app/templates/cart/cart_summary.tpl.php
  - app/templates/cart/cart_summary_table.tpl.php

* fix: Fix section bug in program shortcode
* build: Remove PHP 7.4 support and remove notice on PHP 8.1

= 2.67.1 =

* fix: force locale when removing accents for slugs so that slugs don't depend on the locale

= 2.67.0 =

* feat: Better handle title fallbacks
* feat: Better handle title fallbacks
* compute title using helper
* fix: syntax
* fix: fix tkt_localized_or_default_or_original function
* fix film package template
* i18n: Add missing translations

= 2.66.3 =

* fix: Fix a bug about cart items

= 2.66.2 =

* fix: Add the sections parameter to the load_next_events function

= 2.66.1 =

* refactor: Improve the presentation of the "Photo" field when purchasing tickets
* modified templates:
  - app/templates/buy_pass/form.tpl.php

= 2.66.0 =

* feat: Show all the user tickets on the ticket view/connect page
* feat: Enhance communication tools with integrators
* modified templates:
  - app/templates/ticket/ticket.tpl.php

= 2.65.0 =

* feat: Add portrait template in the program shortcode
* feat: Manage vimeo videos and .mp4 links

= 2.64.3 =

* fix: Improve the changelog
* fix: Add the release link to the changelog and documentation, update translations

= 2.64.2 =

* fix: On a movie, the slider pauses when the video is launched

= 2.64.1 =

* doc: Add comment in release

= 2.64.0 =

* feat: Add theme attribute to more shortcodes
* fix: Image download opens twice in buy_pass form
* fix: Escape "<" in age field
* modified templates:
   - app/templates/buy_article/form.tpl.php
   - app/templates/buy_pass/form.tpl.php
   - app/templates/buy_pass/pass_list.tpl.php
   - app/templates/cart/cart.tpl.php
   - app/templates/cart/cart_table.tpl.php
   - app/templates/checkout/checkout.tpl.php
   - app/templates/checkout/checkout_form.tpl.php
   - app/templates/shop/list/article.tpl.php
   - app/templates/shop/list/articles.tpl.php



= 2.63.1 =

* fix: Syntax

= 2.63.0 =

* feat: Add signage templates and shortcodes
* fix: By-pass cache when loading the settings

= 2.62.0 =

* feat: Disable votes if booking hasn't been scanned
* fix: Screenings that are not votable were votable in the Votes tab
* fix: Don't show activation date of ticket in ticket list
* modified templates:
   - app/templates/ticket/ticket.tpl.php
   - app/templates/user/account/votes/content.tpl.php
   - app/templates/user/account/tickets/content.tpl.php

= 2.61.2 =

* feat: Add TicketID form on user account tickets tab
* feat: Show ticket wallet balance
* feat: Add ticket link on one-time-pass
* modified templates:
   - app/templates/user/account/tickets/content.tpl.php
   - app/templates/ticket/ticket.tpl.php

= 2.60.1 =

* feat: Introduce votes tab in user account page
* feat: Use some Ticketack global settings
* fix: Better handle ratings
* modified templates:
   - app/templates/cart/cart.tpl.php
   - app/templates/cart/cart_summary.tpl.php
   - app/templates/cart/cart_summary_table.tpl.php
   - app/templates/cart/cart_table.tpl.php
   - app/templates/checkout/checkout.tpl.php
   - app/templates/checkout/checkout_form.tpl.php
   - app/templates/ticket/ticket.tpl.php
   - app/shortcodes/user_account.class.php
   - app/templates/user/account/menu.tpl.php

= 2.59.0 =

* feat: Add newsletter subscription checkbox on checkout form
* feat: Show (and disable) draft pages in the pages settings

= 2.58.0 =

* feat: Introduce the votes
* fix: Open the tickets view in the same tab (user account)
* fix: Fix the language on the ticket view

= 2.57.5 =

* fix: Fix bookings pledge mechanism

= 2.57.4 =

* fix: Add the missing button to forget a ticket

= 2.57.3 =

* fix: Fix booking deletion on ticket connect view

= 2.57.2 =

* fix: Restore ticket view templates comtaibility

= 2.57.0 =

* i18n: English translation update
* feat: Viewed tickets are now saved like if they were added with a TicketID
* fix: The expiration date on a ticket's view is now correct
* modified templates:
   - app/templates/ticket/ticket.tpl.php
   - app/templates/event/event.tpl.php
   - app/templates/event/post.tpl.php
   - app/templates/program/grid/event.tpl.php
   - app/templates/program/list/event.tpl.php

= 2.56.7 =

* i18n: German translation update
* i18n: English translation update

= 2.56.6 =

* fix: Remove an unused message in the booking form

= 2.56.5 =

* fix: Enhance cache mechanism

= 2.56.4 =

* fix: Fix a bug in the user account tickets tab

= 2.56.3 =

 * fix: Fix PHP 8.1 compatibility

= 2.56.2 =

* feat: Rework booking form and user account
* feat: Rework ticket view
* feat: Introduce the new Ticketack library
* feat: Improve settings
* fix: Fix admin urls
* modified templates:
   - app/templates/booking/form_pricings.tpl.php
   - app/templates/checkout/checkout_form.tpl.php
   - app/templates/user/account/profile/content.tpl.php
   - app/templates/user/account/tickets/content.tpl.php
   - app/templates/user/user_register.tpl.php
   - app/templates/ticket/ticket.tpl.php
   - app/templates/ticket/ticket_connect.tpl.php
   - app/templates/ticket/ticket_view.tpl.php

= 2.54.1 =

* fix: Update of section filters in the programme shortcode

= 2.54.0 =

* feat: Link to configured ticket page from the booking form

= 2.53.0 =

* feat: Introduce show attribute on the tkt_booking_form shortcode
* fix: Add some error checks
* modified templates:
   - app/templates/booking/form.tpl.php
   - app/templates/booking/form_pricings.tpl.php


= 2.52.0 =

* feat: Enhance ticket's number of bookings representation
* fix: Fix the ticket view template

= 2.51.0 =

* feat: Ticket view implementation
* feat: Update icon pack tkt
* feat: add properties for program shortcode
* fix: getValidityWindows return moment object
* fix: Fix a typo in the tkt_user_account shortcode

= 2.50.1 =

* fix: Fix bookability messages

* modified templates:
   - app/templates/booking/form.tpl.php

= 2.50.0 =

* feat: Enhance bookability messages
* feat: Allow  <strong> tags in descriptions
* feat: Allow to select one pass in the buy_pass shortcode.
* i18n: Improve wording
* fix: Restore the pass pages in the settings
* fix: Fix a bug regarding the passes photo field

* modified templates:
   - app/templates/booking/form_pricings.tpl.php
   - app/templates/buy_pass/buy.tpl.php
   - app/templates/buy_pass/pass_list.tpl.php

= 2.49.3 =

* fix: Translate

= 2.49.2 =

* fix: Translate

= 2.49.1 =

* fix: Translate

= 2.49.0 =

* feat: Use cannot_book_explanation in booking form
* fix: Translate
* fix: Displays the time dynamically according to the language

= 2.48.0 =

* feat: Autoplay event posters slider
* fix: Translate
* feat: Remove duplicated availability message
   - app/templates/event/_package.tpl.php

= 2.47.0 =

* feat: Consider only current screening on film package page
* fix: Setting the language parameter to localize the title
* fix: Translate

= 2.46.8 =

* fix: Fix some details on the event page
* modified templates:
   - app/templates/event/_single.tpl.php

= 2.46.7 =

* fix: Fix a bug in the bookings form

= 2.46.6 =

* fix: account for new sections structure

= 2.46.5 =

* fix: Fix fileinput_to_dataurl component

= 2.46.4 =

* fix: Check that opcache_invalidate exists

= 2.46.3 =

* fix: Fix a bug in the pass form

= 2.46.2 =

* fix: Display a pass twice and add to cart

= 2.46.1 =

* fix: Fix a bug when taking a photo from the webcam

= 2.46.0 =

* feat: The tickettypes fields are now retrieved from Ticketack settings
  (Kronos)
* feat: For better performance, Ticketack settings are now in cache
* feat: Expose some API calls to refresh settings and import events
* fix: Set the default sort on articles list to the one from Kronos
* fix: Use the configured salepoint instead of the user ones
* modified templates:
   - app/templates/article/tkt_article.tpl.php
   - app/templates/shop/list/article.tpl.php
   - app/templates/buy_pass/form.tpl.php
   - app/templates/buy_pass/pass_list.tpl.php

= 2.45.0 =

* feat: Get the checkout fields in Kronos and more in the plugin configuration
* modified templates:
* - app/templates/checkout/checkout.tpl.php

= 2.44.0 =

* feat: Displays the message to book differently depending on if you have a reservation or not
* modified templates:
   -app/templates/booking/form_pricings.tpl.php

= 2.43.11 =

* fix: Fix en error message

= 2.43.10 =

* fix: Display the message: required field for the photo
* modified templates:
   -app/templates/buy_pass/form.tpl.php

= 2.43.9 =

* feat: Show the screneings booking modes if any

= 2.43.8 =

* fix: Fix a bug in the integration client

= 2.43.7 =

* fix: Fix a bug in the checkout form

= 2.43.6 =

* fix: Fix a bug in the cart icon

= 2.43.5 =

* fix: Add a default parameter to the tkt_buy_pass_url function

= 2.43.4 =

* Technical release

= 2.43.3 =

* fix: Check if the api key is empty

= 2.43.2 =

* fix: Hide the title and notice in the form (if there are no fields)
* feat: Display an error message in form_pass

= 2.43.1 =

* fix: Image upload, supported formats png, jpeg & gif
* modified templates:
  -app/templates/buy_pass/form.tpl.php

* feat: Update of uriJs dependency

= 2.43.0 =

* feat: Removes the alert message after 3 seconds
* fix: Changes the number of the cart, when adding a pass to the cart

= 2.42.1 =

* fix: Fix the booking form

= 2.42.0 =

* feat: Add link to recover password on login form

= 2.41.1 =

* fix: Change the DOM structure in booking_form shortcode

= 2.41.0 =

* feat: Change the order of the list
* modified templates:
   -app/templates/user/account/orders/content.tpl.php
   -app/templates/user/account/tickets/content.tpl.php

* feat: Pre-fill the buyer's data with the account data
* modified templates:
   -app/templates/cart/cart_table.tpl.php
   -app/templates/checkout/checkout_form.tpl.php

* fix: Better handle audio, subtitles and countries on event template
* feat: Handle bookability state in booking_form shortcode
* modified templates:
   -event/_single.tpl.php
   -booking/form.tpl.php

= 2.40.0 =

* feat: Adds webcam photo taking and preview
* modified templates:
   -app/templates/buy_pass/form.tpl.php

* fix: Orders history update
* modified templates:
   -app/templates/user/account/orders/content.tpl.php


= 2.39.0 =

* feat: Display the loaded picture
* feat: Do not reset photo field on buy pass form

= 2.38.0 =

* feat: Redirect to ticket view page depending on the configuration
* feat: Enhance auto-complete on some fields
* fix: Fix a bug on the account tickets page

= 2.37.2 =

* fix: Make Tippy allow html content

= 2.37.1 =

* fix: At the creation of the url of an article, verification that page exists

= 2.37.0 =

* feat: Addition of online payment fees in cart
* fix : Update style in cart
* fix : Translation fixes and update

* modified templates:
  -app/templates/cart/cart_table.tpl.php
  -app/templates/cart/cart_items_table.tpl.php

* modified templates translation:
  -app/templates/cart/cart_table.tpl.php
  -app/templates/booking/form_pricings.tpl.php
  -app/templates/pantaflix/login.tpl.php
  -app/templates/ticket/ticket_connect.tpl.php
  -app/templates/user/user_register.tpl.php
  -app/templates/user/user_login.tpl.php

= 2.36.0 =

* feat: Clear comet cache if installed and activated at post import
* feat: Update data program
* fix : Send cr_id instead of cashregister_id param
* fix : Fix cashregister id setting
* Translation update
* Revert "fix: Prevent cache when loading cart"

= 2.35.0 =

* feat: Introduce the knowledge base
* fix: Prevent cache when loading cart
* modified templates: none

= 2.34.1 =

* fix: Update of the jquery trash selector
* modified templates:
   - app/templates/cart/cart_table.tpl.php

= 2.34 =

* feat: New cart design
* modified templates:
   - app/templates/cart/cart_table.tpl.php

= 2.33.1 =

* Waiting time to load underscore.js
* Update of the cart_icon without counting the fees

= 2.33.0 =

* feat: Introduce allowed_ticket_types attribute on Pantaflix shortcode
* fix: Fix a bug on the movie page when no future screening is found
* modified templates:
   - app/templates/booking/form_pricings.tpl.php
   - app/templates/pantaflix/login.tpl.php
   - app/templates/pantaflix/player.tpl.php

= 2.32.0 =

* feat: Creation of variable prices
* feat: Add data-pricing-wrapper in pricing list
* feat: Handle Pantaflix provider argument
* fix: Fix CartIcon error in some cases
* modified templates :
   - buy_article/form_pricings.tpl.php
   - booking/form_pricings.tpl.php
   - pantaflix/player.tpl.php
   - pantaflix/iframe.tpl.php

= 2.31.0 =

* feat: Add gift_message field in form buy_pas
* feat: Update the documentation link
* modified templates
   - buy_pass/form.tpl.php

= 2.30.5 =

* fix: Order_id for payment

= 2.30.4 =

* fix: a bug on the pass form

= 2.30.3 =

* fix: some bugs on the checkout forms
* fix: some bugs on the articles popup

= 2.30.2 =

* fix: Fix the pass form display

= 2.30.1 =

* fix: Change display template of tkt_article

= 2.30.0 =

* feat: Imports screening by places

= 2.29.0 =

* feat: more fine grained ages
* fix: Updates Tippy when the day or time is changed
* fix: Remove the function date_default_timezone_set
* fix: update sccss import
* fix: Updating Moment with Timezone
* fix: time management by site origin

= 2.28.0 =

* feat: Icon and Tippy implementation tpl
* feat: Setting up the icons
* feat: Implementation of Tippy
* fix: notice if default fiels is empty
   - modified templates : booking/form_pricings.tpl.php buy_pass/pass_list.tpl.php

= 2.27.0 =

* feat: Adds the description of the films
* feat: Displays the new detail list in event/_single
* fix: Disable popover function
* fix: update css
* fix: Displays subscriptions associated with the film
* fix: Deletes the single date film package
* fix: Displays the single date in the booking form
* fix: Fix Youtube conflict on windows messages

= 2.26.9 =

* fix: Fix css override loading in child theme

= 2.26.8 =

* fix: Fix a bug in the buy_pass shortcode

= 2.26.7 =

* fix: Set the url of addScreeningToCart
* fix: Change focus on the name step in booking wizard

= 2.26.6 =

* fix: Fix user connect

= 2.26.5 =

* fix: Fix ticket connect

= 2.26.4 =

* fix: Fix booking form

= 2.26.3 =

* fix: better messages
* fix: parametrize urls

= 2.26.2 =

* fix: Fix Pantaflix URL

= 2.26.1 =

* fix: Fix booking form

= 2.26.0 =

* feat: Introduce Pantaflix player

= 2.25.4 =

* fix: Force the refresh for get screenins on booking wizard (experimental)
* Fix: TabIndex in booking_wizard on booking wizard (experimental)
* Fix: Displays the carts for size on booking wizard (experimental)
* fix: Typo
* feat: Add sanitary measures

= 2.25.3 =

* fix: Change width of the select time on booking wizard (experimental)

= 2.25.2 =

* fix: Fix some bugs on booking wizard, again (experimental)

= 2.25.1 =

* fix: Fix some bugs on booking wizard (experimental)

= 2.25.0 =

* fix: Do not allow ticket connection (outside the map) when showing the map
* fix: Better handle pass panels in buy_pass shortcode

= 2.24.0 =

* feat: Handle one-time-pass user data on checkout
* feat: Use the new TicketID connection on events

= 2.23.0 =

* feat: Introduce booking wizard (experimental)

= 2.22.0 =

* feat: Introduce tkt_ticket_connect (Ticket connection using a TicketID) shortcode and deprecate tkt_user_connect
* feat: Make it possible to choose an integrated buy pass page
* fix: Fix booking_form shortcode ids attribute

= 2.21.0 =

* feat: Add a calendar to select the screening to book

= 2.20.1 =

* fix: Fix Safari bug on map

= 2.20.0 =

* feat: Introduce user account
* feat: Add tkt_user_login shortcode
* feat: Handle age, sex and country checkout fields
* fix: Regex for Youtube videos

= 2.19.0 =

* feat: Use Datatrans instead of Postfinance for online payments

= 2.18.1 =

* fix: Import screenings refs
* fix: Fix a bug when we have a pass in the cart

= 2.17.0 =

* feat: Add tkt_booking_form shortcode
* refactor: Use tkt_booking_form shortcode in events pages

= 2.17.0 =

* feat: Add add_to_cart_mode attribute on shop shortcode
* feat: Add tkt_cart_items and tkt_cart_summary shortcodes
* feat: Add tkt_user_register shortcode
* i18n: Add translations

= 2.16.0 =

* feat: Add "tags" attribute ion the shop shortcode

= 2.15.4 =

* i18n: Add some missing translations

= 2.15.3 =

* fix: Add option to configure underscore.js to prevent some js conflicts

= 2.15.2 =

* fix: Add more people activities translations

= 2.15.1 =

* fix: Fix people activities translations
* fix: Fix filter_rows shortcode

= 2.15.0 =

* feat: Handle wallets
* feat: Add articles pagination

= 2.14.0 =

* feat: Handle promo codes

= 2.13.0 =

* feat: Add sort choice on articles listing
* feat: Add hide_sorters attribute on shop shortcode
* feat: Add sort attribute on shop shortcode
* feat: Add nb attribute on shop shortcode
* feat: Add exclude attribute on shop shortcode
* feat: Add only_in_stock attribute on shop shortcode

= 2.12.1 =

* fix: Enhance articles listing

= 2.12.0 =

* feat: Show out of stock articles in listings
* feat: Add articles to cart from listings

= 2.11.2 =

* fix: Enhance session management

= 2.11.1 =

* fix: Prevent js error by adding a missing default value

= 2.11.0 =

* feat: Add ability to display map on booking form
* fix: PHP syntax error

= 2.10.1 =

* fix: Fix some js bugs on screenings form

= 2.10.0 =

* feat: Filter screenings pricings

= 2.9.1 =

* fix: Prevent js error in buy pass shortcode

= 2.9.0 =

* feat: Filter pass pricings

= 2.8.1 =

* fix: Article slug when no WPML installed
* fix: Add country list
* feat: Add ability to send admin notices
* fix: Don't throw exception when on backoffice

= 2.8.0 =
* feat: Add birthdate form field
* feat: Handle child themes for overrides
* feat: Add configuration to download attachments on import
* feat: Add screening_section_ids attribute on program shortcode
* fix: Fix config call in ScreeningsList component
* fix: Fix lang configuration

= 2.7.3 =
* fix: Fix photo field in pass form

= 2.7.2 =
* fix: Fix pass config

= 2.7.1 =
* feat: Enhance link from cart to checkout page

= 2.7.0 =
* feat: Activate video controls on event template
* feat: Add places filter on next_screening shortcode

= 2.6.11 =
* fix: Fix confirm bug

= 2.6.10 =
* fix: Fix pass bug

= 2.6.9 =
* feat: Better handle articles stocks
* fix: Fix remove item from cart

= 2.6.8 =
* fix: Fix bugs on Safari

= 2.6.7 =
* fix: Fix cart bug
* fix: Fix border radius option

= 2.6.6 =
* ui: Rework templates (please check your template overrides)
* ui: Clean styles

= 2.6.5 =
* fix: Fix translation bug

= 2.6.4 =
* fix: Fix pages options bug
* fix: Fix scss compilation process

= 2.6.3 =
* feat: Add color picker for color config
* feat: Enhance translation
* fix: Fix scss compilation process

= 2.6.2 =
* fix: Fix deploy

= 2.6.1 =
* fix: Fix accordion
* fix: Fix admin bug in pages option

= 2.6.0 =
* feat: Get rid of RequireJS and Bootstrap (js) to maximize compatibility
* fix: Enhance scss compilation process
* fix: FIx slug translation bug

= 2.5.0 =
* feat: Add the Checkout process shortcode
* feat: Add the shop
* feat: Add overridables UI options
* feat: Add url output and with_link attribute to tkt_next_screening shortcode
* feat: Add cart and checkout options
* feat: Add hide_links attribute on cart shortcode
* fix: Redirections to cart or program when on a translated page
* fix: Movies title which appeared as objects

= 2.4.5 =
* feat: add output option to tkt_next_screening shortcode
* fix: add to cxart on Safari

= 2.4.4 =
* fix: small bug

= 2.4.3 =
* feat: new tkt_next_screening shortcode
* fix: accordion bug in buy pass page
* fix: Edge (un)compatibility

= 2.4.2 =
* Better handle getting screening infos

= 2.4.1 =
* Get the infos for more than 100 screenings
* Fix program shortcode day filter
* Fix post images bug

= 2.4.0 =
* Handle theme provided shortcodes and JS components
* Add ScreeningsList template for booking form
* Handle Vimeo trailers
* Add photo support on buy pass shortcode
* Add required and requested fields support on buy pass shortcode
* Enhance translations
* Add data-target support on filter_rows shortcode
* Fix lang on redirection to ticketack cart
* Fix carousel format
* Fix pricings popovers

= 2.3.1 =
* Small fix

= 2.3.0 =
* Add new filter_rows shortcode
* Enhance translation
* Small fixes

= 2.2.0 =
* Introduce film packages template
* Add filter_fields attribute on program shortcode to prepare filters work
* Small fixes

= 2.1.4 =
* Restore posters on events sliders

= 2.1.2 =
* Fix import from Ticketack

= 2.1.1 =
* Fix some language problems on import from Ticketack

= 2.1.0 =
* Add the "places" filter on program shortcode
* Minor bug fixes

= 2.0 =
* Be careful, there are breaking changes in this version, specially in the
  templates. You should backup before updating and call us if you have any doubt.

= 1.0 =
* First release
