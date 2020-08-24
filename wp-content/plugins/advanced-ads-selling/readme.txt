=== Advanced Ads – Selling Ads ===
Contributors: webzunft
Tags: ads, ad, banner, adverts, advertisement, browser, sell ads, selling ads, purchase ads, shop
Requires at least: 3.5, Advanced Ads 1.8.18
Tested up to: 4.9
Stable tag: 1.2.4

Let users purchase ads directly on the frontend of your site.

** Features **

* define “ad products” advertisers can choose from
* sell ads per day, impressions, clicks, or custom conditions
* set the placement the ad product will be visible on
* allow advertisers to upload their data after the purchase, to improve conversion
* informs publisher when all ad details are uploaded and the ad can be reviewed
* advertisers have their own account and see their purchases
* built on WooCommerce and therefore extendable with most of their add-ons
* pay with PayPal, Stripe, invoice, or any other payment method available with WooCommerce

** Dependencies **

* Tracking add-on to sell per impressions or clicks
* WooCommerce as the underlying e-commerce solution

== Copyright ==

Copyright 2014-2018, Thomas Maier, webgilde.com

This plugin is not to be distributed after purchase. Arrangements to use it in themes and plugins can be made individually.
The plugin is distributed in the hope that it will be useful,
but without any warrenty, even the implied warranty of
merchantability or fitness for a specific purpose.

== Description ==

With the Selling Ads add-on you can allow visitors to purchase ad space on your site directly.

== Installation ==

Selling Ads is based on the free Advanced Ads plugin, a simple and powerful ad management solution for WordPress. Before using this plugin download, install and activate Advanced Ads for free from http://wordpress.org/plugins/advanced-ads/.
You can use Advanced Ads along any other ad management plugin and don’t need to switch completely.

== Changelog ==

= 1.2.4 =

* fixed last change to make ad products virtual to not affect physical products

= 1.2.3 =

* make ad products virtual by default – needs to re-save already existing ads
* fixed translation typos

= 1.2.2 =

* fixed coding issue that causes an error message when an ad is published
* added option to hide ad setup from clients
* updated German translation

= 1.2.1 =

* swapped getimagesize() function for a more reliable solution
* added hooks to allow to purchase custom ad types
* removed overview widget logic

= 1.2 =

* made the add-on compatible with WooCommerce 3.0

= 1.1.4 =

* fixed default setup page not showing up when home_url() and site_url() are different

= 1.1.3 =

* fixed page content being empty when WooCommerce is not enabled

= 1.1.2 =

* updated German translation
* fixed issue with publish ad setup page and content retrieved in wp_head

= 1.1.1 =

* only show ad setup for ad product type
* hide all ad setup page related information when order does not contain an ad product
* fixed error on ad setup page

= 1.1 =

* added option to use an existing page for the ad setup process
* added `advanced-ads-selling-email-options` to allow to customize emails
* made compatible with Advanced Ads 1.7.16

= 1.0.2 =

* allow to upload gif files
* fixed notifications not containing the ad edit link
* fixed missing array error

= 1.0.1 =

* fixed prices not updating correctly
* fixes for new installations and ones without WooCommerce activated

= 1.0.0 =

* first plugin version
