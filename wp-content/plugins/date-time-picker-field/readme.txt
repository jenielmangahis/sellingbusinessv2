=== Date Time Picker Field ===
Contributors: carlosmoreirapt
Donate link: https://cmoreira.net/date-and-time-picker-for-wordpress/
Tags: datetimepicker, datetime, date picker, jquery
Requires at least: 4.5
Tested up to: 5.1
Requires PHP: 5.3
Stable tag: 1.7.4.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin will allow you to create date and time picker fields using CSS selectors.

== Description ==
Convert any input field on your website into a date time picker field using CSS selectors. You can display a calendar with just a date picker funcionality or enable a time picker also.

* Created by [Carlos Moreira](https://cmoreira.net) using a jQuery plugin by [xdsoft.net](https://xdsoft.net/jqplugins/datetimepicker/)
* More Information and Tutorials at <https://cmoreira.net/date-and-time-picker-for-wordpress/>

Compatible with [Contact Form 7](https://cmoreira.net/blog/date-and-time-picker-field-on-contact-form-7/), [Divi Forms](https://cmoreira.net/blog/date-picker-in-divi-contact-form/) and others.

== Frequently Asked Questions ==

= Why was this plugin developed? =

This plugin was developed because of the need of having a date and time picker field on a contact form that did not include this type of field.
Using this plugin you can convert any text input field into a data time picker field. Including on contact forms.

= How do I prevent the datetimepicker scripts and styles from loading across all my website? =

In the settings page for the plugin you can find the option to load the necessary files only when the shortcode [datetimepicker] exists on the page.
If you have this option selected, the plugin will only look for fields to convert when this shortcode exists on that page.


= How do I add specific times for different days? For example, for weekends? =

In the settings page for the plugin you can find a 'Advanced Settings' tab were you'll find some options to set available times for each day. You'll need to individually set each time available as default, for example '09:00,09:30,09:50,10:50,11:30' and then override this default values for each day you need. It will not work well if you don't setup a default list of allowed times. The list of times still needs to be inside the minimum and maximum times set in the 'Basic Settings' tab.

== Screenshots ==

1. Date and Timer picker.
2. Basic Settings.
3. Advanced Settings.

== Changelog ==
= v.1.7.4.1 =
 * fix with get_plugin_data() function

= v.1.7.4 =
 * language files
 * add version to loaded scripts and styles
 * remove unused files
 * AM/PM hour format bug fix

= v.1.7.3 =
 * fixed data format issue in some languages
 * Removed moment library in favour of custom formatter

= v.1.7.2 =
 * Fixed IE11 issue

= v.1.7.1 =
 * Added advanced options to better control time options for individual days

= v.1.6 =
 * Start of the week now follows general settings option
 * Added new Day.Month.Year format

 = v.1.5 =
 * Option to add minimum and maximum time entries
 * Option to disable past dates

= v.1.4 =
 * Option to add datetime field also in admin

= v.1.3 =
 * Solved PHP missing file

= v.1.2.2 =
 * Included option to prevent keyboard edit

= 1.2.1 =
* Added option to keep original placeholder

= 1.2 =
* Solved bug with date and hour format

= 1.1 =
* Added direct link to settings in plugins page
* Improved options handling

= 1.0 =
* Initial Release

== Upgrade Notice ==
= v.1.3 =
 * Solved PHP missing file

= v.1.2.2 =
 * Included option to prevent keyboard edit

= 1.2.1 =
* Added option to keep original placeholder

= 1.2 =
* Solved bug with date and hour format

= 1.1 =
* Added direct link to settings in plugins page
* Improved options handling

= 1.0 =
* Initial Release

== Screenshots ==

== Credits ==
* [xdsoft.net datetimepicker jQuery plugin](https://xdsoft.net/jqplugins/datetimepicker/)
* [Moment JavaScript date library](https://momentjs.com/)
* [Icon by Paomedia](https://github.com/paomedia/small-n-flat)


