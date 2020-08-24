=== SendPulse Email Marketing Newsletter ===
Contributors: SendPulse
Tags: newsletter, newsletters, mailchimp, mail chimp, sendgrid, autoresponder, email newsletter, email newsletters, email subscription, email signup, mandrill, subscription, subscription form, subscribe widget, newsletter widget, mailpoet, sumome, email, icontact, constant contact, aweber, convertkit, hubspot, mailjet, getresponse, mailing list, email marketing, bulk email
Requires at least: 4.4
Tested up to: 4.8.2
Stable tag: 2.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add e-mail subscription form, send marketing newsletters and create autoresponders.

== Description ==

SendPulse plug-in for WordPress
Add a newsletter subscription form to your site. Each new subscriber will be automatically added to your mailing list. Create and send newsletters through SendPulse, an e-mail marketing and transactional SMTP service.

= Features =
* 1-click installation and easy configuration
* Add multiple newsletter subscription form
* For webpush notifications, please install [SendPulse Free WebPush plugin](https://wordpress.org/plugins/sendpulse-web-push/)

= SendPulse Most Important Features =
* 50% Better open rate because of AI subsystem that predicts the best time and channel for e-mail delivery
* Email Marketing Automation Builder
* Drag and drop HTML email editor
* Unlimited Autoresponders and Mailing Lists
* Email personalization and list segmentation
* Rich analytics and reporting

= What is SendPulse? =
SendPulse is an e-mail marketing service that maximizes the newsletter open rate automatically. Our case studies show that you get 50% more open rates when use our Artificial Intelligence technology to predict the best time and channel to reach your customers.

[Get your free account here](https://sendpulse.com/register). You may send up to 15,000 emails every month.

= Contacts =
* Customer support – [https://sendpulse.com/support](https://sendpulse.com/support)
* Twitter – [https://twitter.com/SendPulseCom](https://twitter.com/SendPulseCom)
* Facebook – [https://facebook.com/sendpulse](https://facebook.com/sendpulse)

= Usage =

1. Create form by [SendPulse Form Constructor](https://login.sendpulse.com/emailservice/forms/constructor/).
2. Create new SendPulse Form from main menu Wordpress dashboard.
3. Paste Constructor Form code in form editor.
4. For display subscription form use shortcode (like `[sendpulse-form id="..."]` where "..." is form id) in editor or place `<?php echo do_shortcode('[sendpulse-form id="..."]')?>` in your theme's file.

= Requirement =
* PHP version >= 5.2.4+ ([Recommended](https://wordpress.org/about/requirements/) >= 5.6)


== Installation ==

1. Upload 'sendpulse-email-marketing-newsletter' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= How place shortcode in themes file? =
Shortcode can be used anywhere in the theme templates via do_shortcode function. For example, `<?php echo do_shortcode('[sendpulse-form id="..."]')?>`.


== Screenshots ==

1. SendPulse Forms table view.
2. Form editor.
3. API setting.
4. Import Wordpress user.

== Changelog ==

= 1.5.0 - 2017-08-22 =
* Changed: Ability to use the constructor code from SendPulse dashboard.
* Fixed: Support several forms on the page.

= 2.0.0 - 2017-09-19 =
* Added: Ability create multiple form with constructor code from SendPulse dashboard.
* Removed: Plugin generated subscribe form in favor constructor code from SendPulse dashboard.

= 2.0.1 - 2017-09-25 =
* Changed: Documentation and help link.

= 2.1.0 - 2017-10-18 =
* Changed: Down minimal PHP version requirement.

== Upgrade Notice ==
In version 2.0.0 of SendPulse Email Marketing Newsletter removed plugin generated subscribe form in favor constructor code from SendPulse dashboard. Its breaking change! Please use link https://login.sendpulse.com/emailservice/forms/constructor/ to generate new forms.