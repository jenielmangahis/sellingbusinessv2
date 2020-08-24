=== Ultimate WP REST API ===
Contributors: EGANY, phamwon	
Donate link: https://reactaz.com
Tags: wordpress, rest api, menus api, mobile app, jwt
Requires at least: 4.7
Tested up to: 4.9.8
Requires PHP: 7.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Enhanced WordPress RESTful API, extend the WordPress default APIs without installing multiple plugins for additional APIs
 
== Description ==

An awesome WordPress plugin to extend the WordPress APIs like Menu, Featured/Thumb Images, JWT Authentication & caching...

All the API is tested and used in the apps of EGANY, you can take a look at 
* [EGANY apps](https://codecanyon.net/user/egany_com/portfolio "EGANY.COM")
* [gikApp - React Native Mobile App For Wordpress](https://codecanyon.net/item/gikapp-react-native-full-application/19465924 "gikApp - React Native Mobile App For Wordpress")
* [Qribto - The React Native App For Crypto Currency News Site](https://codecanyon.net/item/qribto-the-react-native-app-for-crypto-currency-news-site/22462364 "Qribto - The React Native App For Crypto Currency News Site")

FEATURES:

* Menus WP API: Adding menus endpoints on WP REST API / Extends WordPress WP REST API with new routes pointing to WordPress menus.
* Better REST API Featured Images: Adds a top-level field with featured image data including available sizes and URLs to the post object returned by the REST API.
* User WP API: Adding user endpoints on WP REST API. If you wish to Register User or Retrieve Password using REST API, without exposing Administrator credentials to the Front End application, you are at the right place. Since WordPress 4.7, REST API was natively included in WordPress.
* Caching WP API: FileStorage, MemoryStorage
* Authentication: auth by account and by JWT
 
== Installation ==
 
This section describes how to install the plugin and get it working.
 
 
1. Upload `Ultimate_WP_REST_API` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add this block code to your .htaccess file at Wordpress site root folder
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]

== Frequently Asked Questions ==
 
= Is it free? =
 It's absolutely free, no cost and fees
 
== Screenshots ==
 
1. UWR-General-setting-1.png
2. UWR-Caching-1.png
3. UWR-API-Testing-1.png
4. UWR-API-Testing-2.png

 
== Changelog ==
= 1.2.8 =
* Fix minor bug
* Update installation guideline 
= 1.2.7 =
* Add API settings/option (GET)
* /settings/option?name=[option_name]
= 1.2.6 =
* Disable Auth by User Account 
= 1.2.5 =
* Fixed bug UserInfo - User email existing 
* Fixed "There is no posting of comments" error
* After logging in to the application using the existing login/password, the first name and last name fields in the user profile are not displayed.
= 1.2.4 =
* Fixed bug login by nonce, update code 
= 1.2.3 =
* Fixed wp_create_nonce don't exist
= 1.2.2 =
* Fixed encode URL bugs
= 1.2.1 =
* Update JWT bugs
= 1.2.0 =
* Update API settings
= 1.1.0 =
* Delete lib never used 
= 1.0.0 =
* Initial release