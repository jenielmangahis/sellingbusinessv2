=== Plugin Name ===
Contributors: krozero
Tags: widget area, custom widget area, widget, simple widget area, custom sidebar, dynamic sidebar, menu, menus, custom menu, custom menu locations, menu location, menu area
Requires at least: 3.0.1
Tested up to: 4.7
Stable tag: 1.2.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


A very simple way to create a custom widget area, sidebars and menu locations for your wordpress site.


== Description ==

A Wordpress Plugin that makes it very simple and easy to create a custom widget areas, sidebars and Menu locations. With the help of this plugin you can create multiple custom widget areas, menu locations and use it wherever you want to show in your site.

It allows you to show custom widget areas and menu locations created with this plugin in any part of your site (i.e, as sidebars, bottom widget areas , in header and plus with this plugin now you can also show it in your pages and posts contents.) There's two way of using this plugins. To show it in posts or pages content use shortcode link "Get shortcode" and for other like to show as sidebars etc. use code link "Get code".


== Installation ==

1. Upload `wp-custom-widget-area` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. click on CWA settings menu at left menu bar

== Frequently Asked Questions ==

= How to use custom widget area? =

	1. Create a new Widget area.
	2. Click on the "get code" link.
	3. Copy the code and Paste it in a wordpress theme where you want to display it.

= How to Use it in page or post content? =
	1. Click on the "get shortcode" link form widget area table below.
	2. Copy the shortcode and Paste it in a post or page editor in which you want it to display it.

= How to customize widget style? =
	1. Click on the advance link while creating new widget area and add widget class.
	2. Add custom css targeting your widget area class. i.e. 
	.mynewwidgetareaclass a{ color: red; } 
	at the bottom of your style.css where ".mynewwidgetareaclass" is your widget area class.

= How to edit widget area? =
	1. scroll down to the widget area table and click on the edit link.
	2. edit the widget area data.
	3. click on the update button to save changes.


= How to use menu locations? = 
	1. Create a new Menu Location.
	2. Click on the "get code" link from table below.
	3. Copy the code and Paste it in a wordpress theme where you want to display it.

= How to Use it in page or post content? =
	1. Click on the "get shortcode" link form table below.
	2. Copy the shortcode and Paste it in a post or page editor where you want to display it.

= How to customize menu style? =
	1. Pass the extra arguments while calling function
		i.e.
		wp_nav_menu( array( 'theme_location'	=> 'footer-location', 'menu_class' => 'Cwa-menu', [arguments] => ['values']...	) ); 
		Cick here to know more about available Parameters. 
		[Note: for shortcode pass arguments like [menu theme_location='footer-location' 'menu_class'='Cwa-menu' [arguments]=[values]...]
	2. Make sure you have passed custom menu class options i.e. 'menu_class' like in above code.
	3. Add custom css targeting your menu_class or container_class etc. i.e. 
		.Cwa-menu a{ color: red; } 
		at the bottom of your style.css.


== Screenshots ==

1. Custom widget area basic view.
2. Custom widget area advance view.
3. ** Menu locations ** create and use menus anywhere.

== Changelog ==
= 1.2.5 =
* fixed script and styles equeue 
* Added new feature for custom wrapper elements. 

= 1.2.2 =
* Widget area bug fix for tag less code display on frontend. support ticket : widget-title-showing-in-code-form

= 1.2.1 =
* Widget area edit form bug fix

= 1.2.0 =
* Added widget area edit option

= 1.1.5 =
* delete script bug fix

= 1.1.4 =
* database table upgrade bug fix
* form and js bug fix

= 1.1.3 =
* widget area bug fix
* improved user interface
* easy and simple how to use help guide

= 1.1.2 =
* database bug fix for v 1.1.0 and 1.1.1

= 1.1.1 =
* database update fix for v 1.1.0

= 1.1.0 =
* added new menu location feature
* design update

= 1.0.4 =
* 4.2 compatible

= 1.0.3 =
* fix for "code conflict with CoSchedule plugin"

= 1.0.2 =
* shortcode bug fix
* removed unused assets

= 1.0.1 =
* added widget area shortcode
* how to use update

= 1.0.0 =
* Launch version.



== Upgrade Notice ==
= 1.2.5 =
* fixed script and styles equeue 
* Added new feature for custom wrapper elements. 

= 1.2.2 =
* Widget area bug fix for tag less code display on frontend. support ticket : widget-title-showing-in-code-form

= 1.2.1 =
* Widget area edit form bug fix

= 1.2.0 =
* Added widget area edit option

= 1.1.5 =
* delete script bug fix

= 1.1.4 =
* database table upgrade bug fix
* form and js bug fix

= 1.1.3 =
* widget area bug fix
* improved user interface
* easy and simple how to use help guide

= 1.1.2 =
* database bug fix for v 1.1.0 and 1.1.1

= 1.1.1 =
* database update fix for v 1.1.0

= 1.1.0 =
* added new menu location feature
* design update

= 1.0.4 =
* 4.2 compatible

= 1.0.3 =
* code conflict fix

= 1.0.2 =
* shortcode bug fix for page and posts. 

= 1.0.1 =
* added widget area shortcode
* how to use update
