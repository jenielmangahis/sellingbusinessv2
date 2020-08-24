=== Import XML and RSS Feeds ===
Contributors: MooveAgency
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6L6H4CRP9228N
Stable tag: trunk
Tags: xml, rss, xml import, rss import, feed import, feed, import
Requires at least: 4.3
Tested up to: 5.0
Requires PHP: 5.6
License: GPLv2

This plugin adds the ability to import content from an external XML/RSS file, or from an uploaded XML/RSS.

== Description ==

This plugin adds the ability to import content from an external XML/RSS file, or from an uploaded XML/RSS and add the content to any post type in your WordPress install. It also supports importing taxonomies alongside posts.

### The process of import:

* Select the source ( URL or FILE UPLOAD )
* Select your repeated XML element you want to import - This should be the node in your XML file which will be considered a post upon import.
* Select the post type you want to import the content to.
* Match the fields from the XML node you've selected (step 2) to the corresponding fields you have available on the post type.

### XML files and URLs

The XML source file should be a valid XML file. The plugin does check if the URL source or the Uploaded file is valid for import and processing. If you use the URL source for importing, please make sure the URL you are using is not password protected with HTTP Auth or any other form of authentification (it needs to be public).

Accepted formats: XML 1.0, XML 2.0, Atom 1, RSS


### Features

* XML Preview - After successfully uploading an XML file or reading an external URL, the plugin will present you with an XML preview of the selected node, which can be used to check if you've selected the correct node or you have all the data read correctly by the plugin. This preview presents one item from the selected node and it is paginated so you can navigate back and forward between the elements.

* Linking Taxonomies to Posts - This plugin allows you to import categories/taxonomies from the XML file and link the imported posts to these taxonomies. First you need to have the taxonomies created in WordPress to allow the plugin to import into these taxonomies. By default WordPress has two taxonomies: categories and tags.

* Limit posts - In the "Import Settings" area you can limit the import. You can use multiple patterns to include posts in the import. Use semicolon to separate the values. Eg.: 1-8;10;14-

* Importing and linking multiple taxonomies to one post - To import and link one post to multiple taxonomies, you need to have an XML element in your selected node with a list of categories separated by commas. These elements will be recognized and imported separately as taxonomy terms.

* **[Premium]** Save & Load templates - After the fields are matched, you can save the matching as a template, and use it when it's needed.

* **[Premium]** Support for tag attributes 

* **[Premium]** Custom Fields & ACF support

> Note: some features are part of the Premium Add-on. You can [get Import XML Premium Add-on here](https://www.mooveagency.com/wordpress-plugins/)!

### Demo Video

You can view a demo of the plugin here: 

[vimeo https://vimeo.com/305452075]

[Import XML feed WordPress Plugin by Moove Agency](https://vimeo.com/305452075)

== Screenshots ==
1. Import XML feed - Select XML/RSS feed from URL.
2. Import XML feed - Select XML/RSS feed from File Upload.
3. Import XML feed - Select the repeat element from feed
4. Import XML feed - Matching elements ( * the screenshots contains Premium features as well )
5. Import XML feed - Import finished
6. Import XML feed - Templates [Premium]

== Installation ==
1. Upload the plugin files to the plugins directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the \'Plugins\' screen in WordPress
3. Use the Settings->Moove feed importer screen to configure the plugin

== Changelog ==

= 1.2.1 =
* Updated plugin premium box

= 1.2.0 =
* Updated plugin premium box

= 1.1.9 =
* Fixed translation slugs
* PHP 7 compatibility

= 1.1.8 =
* Adding Czech translation

= 1.1.7 =
* Adding donation box

= 1.1.6 =
* Fixed PHP warnings

= 1.1.5 =
* Fixed multiple taxonomy import, comma separated list allowed

= 1.1.4 =
* Fixed post_title field, HTML tags will be removed from it

= 1.1.3 =
* Fixed PHP Warning message

= 1.1.2 =
* Fixed Date format issue

= 1.1.1 =
* Fixed ACF functions

= 1.1.0 =
* Added post limitation

= 1.0.9 =
* Fixed "Wrong or unreadable XML file!" error on file upload.

= 1.0.8 =
* Fixed "Wrong or unreadable XML file!" error appeared for Internet Explorer users.

= 1.0.7 =
* Fixed featured image import

= 1.0.6. =
* Added ability to set post_date from xml/rss feed. (thanks to metadan)

= 1.0.5. =
* Fixed Options page controller issue

= 1.0.4. =
* Rss "Atom" namespase issue fixed

= 1.0.3. =
* Third party include fixed

= 1.0.2. =
* Validated, sanitized and escaped inputs

= 1.0.1. =
* Code modified to follow WP standards

= 1.0.0. =
* Initial release of the plugin.