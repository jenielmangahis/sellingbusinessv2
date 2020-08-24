=== WooCommerce eWAY Gateway ===
Contributors: automattic, woothemes, royho, akeda, mattyza, bor0, dwainm, laurendavissmith001, mikejolley, kloon, jeffstieler
Tags: credit card, eway, payment request, gateway, woocommerce, automattic
Requires at least: 4.4
Tested up to: 5.0
Stable tag: 3.1.19
Requires PHP: 5.6
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

This is the official WooCommerce extension to take credit card and subscription payments directly on your store with eWAY.

== Description ==

The eWAY extension for WooCommerce allows you to take credit card payments directly on your store without redirecting your customers to a third party site to make payment. Supports **WooCommerce Subscriptions, WooCommerce Refunds API**, as well as **token payments**, which allows customers to save credit cards for future purchases. Everything happens on your site without the customer ever leaving.

The eWAY payment gateway for WooCommerce makes use of eWAY’s brand new Rapid 3.1 API, it supports **3D Secure** and is **fully PCI compliant** as per eWAY’s specifications and adds support for processing **subscription payments** as well as **token payments** allowing customers to save credit cards for future purchases.

By using eWAY’s Rapid 3.1 API there is a single endpoint for processing payment, meaning you only need this one extension to take payment through any of eWAY’s processing countries, eWAY Australia, eWAY New Zealand, eWAY Singapore, eWAY Malaysia, and eWAY Hong Kong. eWAY uses complex DNS technology to ensure your payment is routed to the correct country.

= Key Features

* Ability to host promotional flash sales in real-time
* Generate discount coupons for your customers to help with special promotions
* Product reviews from your customers
* Automatic up-sells and cross-sells
* Intuitive order management suite

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Install and activate WooCommerce if you haven't already done so
1. For help setting up and configuring, please refer to our [user guide](https://docs.woocommerce.com/document/eway)

== Frequently Asked Questions ==

= Does this require an eWAY merchant account? =

Yes! An eWAY merchant account, customer API key and customer API password are required for this gateway to function.

= Does this require an SSL certificate? =

An SSL certificate is recommended for additional safety and security for your customers.

= Where do I find my eWAY API Key? =

eWay has updated the API setup instructions. Please go to this link for the latest information: https://go.eway.io/s/article/How-do-I-setup-my-Live-eWAY-API-Key-and-Password.

= eWAY Credit Card option not showing at checkout =

When in live mode, you need to have SSL enabled and your store must be using AUD, NZD, SGD, HKD or MYR as the store currency. You must also have valid API keys for the mode you are using (Sandbox credentials for Sandbox mode; and live credentials for live mode).

= Where can I find a list of error codes and their meanings? =

A list of error codes can be found inside the eWAY Rapid 3.1 Documentation. [Download the eWAY Rapid 3.1 Documentation](https://eway.io/api-v3/#response-amp-error-codes)

= I am getting a V6018 error code at checkout =

When using eWAY, the store currency must match the eWAY location you are using. For example, if you’re using eWAY Australia you need to have your store currency set to AUD.

= Is 3D Secure supported? =

Yes, it is, as of version 3.0 of the plugin.

= Failed to process your transaction, error code: SOAP-ERROR: Parsing WSDL =

If you get an error that says:

`Failed to process your transaction, error code: SOAP-ERROR: Parsing WSDL: Couldn't load from 'https://api.sandbox.ewaypayments.com/soap.asmx?WSDL'; : failed to load external entity "https://api.sandbox.ewaypayments.com/soap.asmx?WSDL"`

Check that you’re using the correct API key and that the correct password has been entered. If you’re using sandbox mode, be sure to use the API key and password from your eWay Partner Account sandbox account.

= Where can I find documentation? =

For help setting up and configuring, please refer to our [user guide](https://docs.woocommerce.com/document/eway)

= Where can I get support or talk to other users? =

If you get stuck, you can ask for help in the Plugin Forum.

== Changelog ==

= 2018-11-19 - version 3.1.19 =
* Update - WP 5.0 compatibility.

= 2018-10-17 - version 3.1.18 =
* Update - Add settings link
* Update - WC 3.5 compatibility.

= 2018-08-21 - version 3.1.17 =
* Fix    - Store Host IP is captured/Depicted as customer IP address on Eway site.

= 2018-05-22 - version 3.1.16 =
* Update - Privacy policy notification.
* Update - Export/erasure hooks added.
* Update - WC 3.4 compatibility.

= 2018-05-02 - version 3.1.15 =
* Update - WP tested up to version.
* Fix - coding standards.
* Fix - nonce usage, input sanitization, output escaping.
