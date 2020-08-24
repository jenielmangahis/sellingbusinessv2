=== Advanced Ads Pro ===
Contributors: webzunft
Tags: ads, ad, banner, adverts, advertisement, anchor, ad place, ad space
Requires at least: WP 4.4, Advanced Ads 1.10
Tested up to: 5.0
Stable tag: 2.2.2

Advanced Ads Pro is for those who want to perform magic on their ads.

== Copyright ==

Copyright 2014-2018, Thomas Maier, https://wpadvancedads.com

This plugin is only distributed by webgilde GmbH. Arrangements to use it in themes and plugins can be made individually.

== Description ==

Advanced Ads Pro extends the free version of Advanced Ads with additional features that help to increase revenue from ads.

Features:

* check delivered ads within the admin bar in the frontend
* cache-busting to lazy load ads on cached pages
* test placements against each other
* option to limit an ad to be displayed only once per page
* refresh ads without reloading the page
* flash ad type with fallback
* select ad-related user role for users
* inject ads into any content which uses a filter hook
* click fraud protection
* alternative ads for ad block users
* lazy loading
* place custom code after an ad

placements

* pick any position for the ad in your frontend
* inject ads between posts on posts lists, e.g. home, archive, category
* inject ads based on images, tables, containers, quotes and any headline level in the content
* ads on random positions in posts (fighting ad blindness)
* ads above the main post headline
* ads in the middle of a post
* background / skin ads
* set a minimum content length before content injections are happending
* dedicated placements for bbPress
* dedicated placements for BuddyPress
* show ads from another blog in a multisite
* repeat content placement injections
* allow Post List placement in any loop on static pages

display and visitor conditions:

* display ads based on where the user comes from (referrer)
* display ads based on the user agent (browser)
* display ads based on url parameters (request uri)
* display ads based on user capability
* display ads based on the browser language
* display ads based on number of previous page impressions
* display ads based on number of ad impressions per period
* display ads to new or recurring visitors only
* display ads based on a set cookie
* display ads based on page template
* display ads based on post meta data
* display ads based on post parent
* display ads based on the day of the week
* display ads based on language of the page set with WPML

== Installation ==

Advanced Ads Pro is based on the free Advanced Ads plugin, a simple and powerful ad management solution for WordPress.
You can use Advanced Ads along any other ad management plugin and don’t need to switch completely.

== Changelog ==

= 2.2.2 =

* allow content injection based on any visible text output
* enabled cache-busting for ads that have the "specific days" condition
* prevent Custom Position placement from being shown in the footer
* made ads of type 'group' work correctly with the Slider add-on
* show only one alternative ad when an ad blocker is found
* group refresh: prevent content jumping when ads have different height
* fixed Paid Memberships Pro visitor condition on some BuddyPress pages
* fixed conflict with cache-busting/lazy-load in Newspaper theme
* updated French, German, Spanish and Japanese translations

= 2.2.1 =

* use ajax fallback of passive cache-busting only if enabled
* implemented Visitor Condition for BuddyPress profile fields
* made ad filters on the ad list page work server side
* added Italian translation
* made Content Middle placement find the middle more correctly

= 2.2 =

* added support for user consent and Advanced Ads 1.9
* added option to grid group allowing to control the breakdown on smaller screens
* only inject Middle and Random Content placements to paragraphs in the main content, unless "Disable Level Limitation" is checked

= 2.1.4 =

* changed label of LazyLoad offset option
* fixed tracking of ads delivered with passive cache-busting

= 2.1.3 =

* prevent ad injection into image caption and gallery
* prevent injection of Content Middle placement into blockquotes
* added distance before which to start loading ads lazily
* import ad for adblockers during the import procedure
* fixed Analytics tracking on multisite if an ad from a sub-blog is used
* fixed errors caused by removed alternative ads for adblockers

= 2.1.2 =

* fix for Minimum Content Injection feature

= 2.1.1 =

* prevented infinity loop caused by groups with refresh interval
* fixed html markup on AdSense edit page

= 2.1 =

* added option to allow Post List placement in any loop on static pages
* added option to repeat Content placement after every X number of paragraphs or headings
* made possible to add custom code after ad content
* added 'inject_placement' event that triggers when cache-busting injects new placement
* optimized warning if basic Advanced Ads plugin is missing
* use more safe method to check ad markup for validity for cache-busting
* added filter to change durations of individual ads in refreshing groups

= 2.0.4 =

* allowed to rotate ordered ads with same weight
* fixed background ad link if Tracking is not enabled
* fixed background ad style

= 2.0.3 =

* allow up to 20 rows in grid settings
* made compatible WP Rocket’s script defer option without "Safe mode" enabled
* fixed issue with lazy load and "Content" placement
* fixed issue with group refresh and passive cache-busting when ads have visitor conditions

= 2.0.2 =

* add #debug=true to a URL in order to output cache-busting code in the JS console
* fixed ad shortcode issue
* fixed rare Click Fraud script issue
* updated Spanish translation

= 2.0.1 =

* fixed conflict between Ad block alternative and group refresh

= 2.0 =

* implemented lazy loading
* implemented click fraud protection
* implemented alternative ads when an ad block is enabled
* disabled passive cache-busting if an AA shortcode is placed inside the ad content
* fixed group refresh option with custom position placements
* prevent Post List ads in RSS Feeds
* fix issue when `advanced.js` uses `defer` attribute

= 1.16.1 =

* added `advanced-ads-pro-background-selector` filter to customize background selector
* fixed a bug in passive cache-busting and visitor conditions that was introduced in the previous version

= 1.16 =

* only enable group refresh feature when cache-busting is used
* implemented display condition for paginated posts
* disabled cache-busting on AMP pages
* hide WPML condition if WPML is not installed
* removed overview widget logic
* added Japanese translation
* fixed issue when combining multiple visitor conditions and using passive cache-busting

= 1.15.2 =

* fixed issue with wrong AMP pages check
* fixed content length calculation for non-latin texts
* moved placement tests code into new module

= 1.15.1 =

* handle 'URL parameter' display condition correctly when Advanced Visitor Conditions module is disabled
* add the 'advanced-ads-output-wrapper-after-content-group' filter
* updated translation files

= 1.15 =

* updated complete placement and group option handling and layout according to Advanced Ads 1.8
* changed all condition labels from show/hide to is/is not
* removed deprecated general minimum content length option from Pro settings – this is an option for placements only now
* updated all translation files
* fix reload on refresh feature (Responsive add-on)

= 1.14 =

* added ad notice to Group ad type using only AJAX or no cache-busting
* add group wrapper only if needed
* add "Advertisement" label inside wrapper of the first ad when using groups
* move empty cache-busting wrapper instead of ad/group wrapper
* prevent conflict when Autoptimize and NextGen Gallery are used at the same time
* clear group wrapper when 'Reload ads on resize' (Responsive add-on) feature is used
* fixed url parameter condition using query strings twice
* fixed placement tests not working when placement name consists only of numbers

= 1.13 =

* implemented ad reload logic to reload ad when screen resizes and Responsive add-on is activated
* fixed issue causing passive-cache busting and layer not to work together

= 1.12.1 =

* added check post template conditions to prevent crash on sites running WP version prior to 4.7

= 1.12 =

* added template display conditions for every post type (new in WP 4.7)
* added flexbox for input fields
* prevent critical JS errors when cache-busting script is missing
* optimized callback for use of advanced JS file
* fixed error message when all placements were removed

= 1.11 =

* auto refresh of groups considers weight when selecting ads
* added `advanced-ads-pro-inject-content-selector` filter to manipulate content selector
* added `blockquote` as condition for content injection placement
* Above Headline placement code no longer added to archive pages
* use `ADVANCED_ADS_PRO_CUSTOM_POSITION_MOVE_INTO_HIDDEN` constant to allow injecting ads into hidden elements
* use cache-busting fallback method for ad-group ad types, because passive cache-busting breaks conditions of sub-ads
* updated AMP page check to work with WP AMP plugin
* updated Spanish translation

= 1.10 =

* compatibility with Advanced Ads 1.7.15
* allow to override specific options in functions and shortcodes
* disabled Url Parameter in Visitor Conditions – use the same Display Condition instead
* fixed AMP ads not being injected correctly

= 1.9 =

* added post meta display condition
* added post parent display condition
* fixed ad grid used with cache-busting

= 1.8 =

* added support for WPML language switcher
* added background ads placement
* added list item to available post content elements
* added support for tracking impressions and clicks for cache-busted ads in Google Analytics
* updated translation files and German translation
* fixed conflict between Slider and passive cache-busting
* fixed option to prevent ad content injection for a specific post
* fixed some text domains

= 1.7 =

* added dedicated bbPress placements
* added dedicated BuddyPress placements
* added condition to show ads per day
* allow to use ads from another blog in a multisite
* make ads work with bbPress
* fixed problem with Tracking of grouped ads
* fixed user rights issue on multisites
* fixed PHP 7 conflict

= 1.6 =

* added Grid ad group type to allow to display multiple ads in multiple columns
* prevent random and middle content ad injection into empty paragraphs and tables
* allow to inject ads into any content with a filter hook using ADVANCED_ADS_PRO_CUSTOM_CONTENT_FILTER
* optimized JavaScript to fix more issues when scripts are moved to the footer – not all problems solved yet
* allow to use the Slider with the Custom Position placement with passive cache-busting
* save cookies with JavaScript only and not with PHP’s `setcookie()` to prevent issues with caches
* set duration of some cookies with constants: `ADVANCED_ADS_PRO_REFERRER_EXDAYS`, `ADVANCED_ADS_PRO_PAGE_IMPR-EXDAYS`
* fixed content injection option for posts overwriting other settings
* fixed small issue in can_display_by_display_limit function
* fixed issue with minified and footer scripts

= 1.5 =

* added option to place a given container for ads into the frontend using the Custom Position placement
* allow to enable passive cache-busting for ads which are not delivered through placements
* added warning to placements tests when AdSense limit is enabled
* updated text domain according to stricter WordPress standards
* updated German translation

= 1.4.1 =

* added page templates display condition
* added option to remove custom position placement variable manually
* added is/is_not operator for browser language condition
* added page option to hide content injections on specific pages
* don’t show custom position placement ads when the selector does not exist
* fixed script error for non-ads when ad blockers are enabled
* fixed ad group refresh to hide all ads until js is loaded
* fixed cache-busting error for external scripts

= 1.4 =

* refresh ads without reloading the page
* option to limit an ad to be displayed only once per page
* test placements
* combine multiple ad requests (ajax cache-busting) into one for better performance
* moved URL parameters Visitor Condition into Display Conditions
* prevent ad injection into image caption
* prevent ad injection for images within tables
* fixed combined visitor conditions in passive cache-busting
* fixed user capabilities if not set properly
* fixed missing array issue for cache-busting

= 1.3.2 =

* added support for passive cache-busting tablet check in Responsive add-on
* fixed for timeout layer and passive cache-busting
* fixed single AdSense ads running with always with passive cache-busting
* fixed tracking issue with passive cache-busting groups

= 1.3.1.2 =

* fixed limitation for ads injected into the content

= 1.3.1.1 =

* display link to activate Advanced Visitor Conditions if not enabled
* fixed user role assignment
* fixed missing index issue
* fixed check for minimum content length for Pro content injection placements
* adjustments to show Advertisements label

= 1.3.1 =

* disable cache-busting within feed
* allow to inject ads only in the main query
* fixed error message on activation
* fixed error when $wp_query is not set
* added French translation

= 1.3 =

* added passive cache-busting method
* removed code deprecated with Advanced Ads 1.7.1
* added Spanish translation
* fixed text domain loading
* fixed compatibility with multiple sliders

= 1.2.5.2 =

* fixed url parameters visitor condition for cache-busting
* fixed some visitor conditions returning true if compare value is missing

= 1.2.5.1 =

* fixed potentially missing index

= 1.2.5 =

* allow content injection based on any headline level (h2, h3, or h4)
* disabled cache-busting when set to "auto" and not needed
* select ad-related user role for users

= 1.2.4 =

* allow content injection based on images, tables and containers
* added visitor condition to limit ads by impressions per period
* extended cache-busting script to allow hooking into it
* updated user rights to see ad list in admin bar

= 1.2.3 =

* added support for Autoptimize
* added option to allow lower minimum length for individual content placements
* deprecated global content length setting
* fixed content middle placement not working with cache-busting
* fixed Media Library link not opening for flash file ad type

= 1.2.2 =

* prevented ad from being injected into another ad
* fixed list of active ads in the admin bar
* admin-bar: only display on content-pages
* fixed js conflict

= 1.2.1 =

* added placement to inject ads into post archive pages
* load advanced js file for whole plugin, not only if visitor conditions module is activated
* fixed setting multiple cookies for the referrer
* cache-busting:
  * support script includes outside heads: does not support ads between `wp_head()` and `</head>`
  * improve javascript execution
  * ads are listed in admin bar
  * now supports JS observers
  * pass ad arguments as JSON - fixes various issues

= 1.2 =

* added placement for custom positions, including frontend picker
* display current ads in admin bar only for admins
* fixed random ad position not working for cache-busted ads
* added query string to be considered by url parameters check
* translated into German

= 1.1.1 =

* added visitor condition to check for cookies
* added visitor condition to check previous page impressions
* added visitor condition to check for new visitors

= 1.1 =

* updated plugin link
* random post injection now working with javascript
* added placement for ad injection above the main headline
* added placement for ads in the middle of post content
* added option to check content length before injection
* renamed option key
* show warning if Advanced Ads is not installed

= 1.0.2 =

* allow to negate user-can visitor condition

= 1.0.1 =

* added licensing code
* added user agent string check to visitor conditions
* added user capability visitor condition
* added browser language check to visitor conditions
* added request uri parameter check to visitor conditions

= 1.0.0 =

* minor fixes and additions to readme and code
* fixed naming convention of base file
* added module configuration
* applied coding conventions
* added cache-busting module (disabled by default; placements only)
* override cache-busting settings per placement
* allow to fully disable display-by-referer module (now disabled by default)
* added url referer check to visitor display conditions
* fixed random ad logic not loading when there is no ad item attached to the placement
* fight ad blindness and inject ads on a random position within the post content
* control ads on frontend with a new menu icon showing the currently displayed ads, groups and placements
