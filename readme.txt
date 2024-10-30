=== Israel Post for WooCommerce ===
Contributors: ilpost,zorem
Tags: Israel post, Israel Post Shipping, דואר ישראל
Requires at least: 5.0
Tested up to: 6.1.1
Requires PHP: 7.0
Stable tag: 1.6.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The official Israel post plugin allows you to create & print Israel Post international shipping labels (only) directly through your store's orders admin. The plugin integrates the Israel post API into WooCommerce to create and print shipping labels, fulfill orders and track shipments
Compatible with WC version up to 7.1.0

This plugin can be used only by merchants who have an account with the Israel Post export services.

= Generate Israel Post Shipping Labels =

Generate Israel Post international shipping labels directly from your WooCommerce admin. Setup sender address and defaults and generate Israel Post shipping labels directly from your WooCommerce admin, choose your print format, shipping options, update weight and totals and reprint labels if needed.

= Tracking Page =

Boost sales with a branded tracking page to show the latest order status and marketing banner to impress and upsell customers.

== Features ==

* Fast and easy setup, option to set default from address and  
* Create international shipping labels from WooCommerce orders admin and single order admin
* Option to split shipments into a few packages (tracking numbers)
* Dynamic shipping services - the shipping services will display dynamically by the recipient destination country.
* Automatically update the tracking number and label in WooCommerce after the label creation.
* Set a Tracking Page on your store with tracking status and tracking events 
* Experience premium support, timely compatibility updates and bug fixes.

https://www.youtube.com/watch?v=1NcwCbjocIs

== Translation ==

EN/HE

== Compatibility ==

* Compatible with Advanced Shipment Tracking plugin and Tracking Per Item Add-on

== how to get the Israel Post API Keys ==

To obtain the Israel Post API keys, you need to send an email to the Israel Post support PostilAPISupport@malam.com and provide your Israel Post account name and server IP address to whitelist it.

== Installation ==
1. Upload the folder `woo-israel-post` to the `/wp-content/plugins/` folder
2. Activate the plugin through the \'Plugins\' menu in WordPress

== Changelog ==
= 1.6.1 =
* Dev - Added Shaar Olami adaptions

= 1.6.0 =
* Dev - Added compatibility with WordPress 6.1.1 and WooCommerce 7.1.0

= 1.5.9 =
* Dev - Added Invoice & Attachments upload

= 1.5.8 =
* Dev - Added IOSS field

= 1.5.7 =
* Dev - Get HS Code from attribute wordpress

= 1.5.6 =
* Dev - Tested with WordPress 5.7

= 1.5.5 =
* Enhancement - Update tracking URL to the new Israel Post domain.

= 1.5.4 =
* Dev - Added regular Air Mail (UA) support 
* Dev - Tested with  WooCommerce 5.0.0

= 1.5.3 =
* Dev - Tested with WordPress 5.6 and WooCommerce 4.8

= 1.5.2 =
* Dev - Added product value validation in generate label form
* Dev - Added compatibility with WooCommerce 4.6

= 1.5.1 =
* Dev - Added compatibility with WordPress 5.5.1 and WooCommerce 4.5

= 1.5.0 =
* Dev - Change get currency in generate label form from WooCommerce settings to order
* Enhancement - Add currency symbol before item price and total price in generate label form

= 1.4.9 =
* Enhancement - Change error message on generate label form for Israel Post API call
* Enhancement - Added address validation message on header in generate label form

= 1.4.8 =
* Fix - Fixed loading issue on single order page
* Fix - Set input box of Length, width and height so user can use with decimal

= 1.4.7 =
* Enhancement - Added a option in settings and generate label form add Package dimesions
* Enhancement - Added a settings page link on plugins page
* Fix - Fixed settings not saved issue
* Fix - Fixed warnings - A non well formed numeric value encountered

= 1.4.6 =
* Added compatibility with WooCommerce 4.3.0

= 1.4.5 =
* Dev - For all Europe countries remove Fiscal option for Customs Procedure Type

= 1.4.4 =
* Dev - Add post data of label in error log

= 1.4.3 =
* Enhancement - Added a option in settings to Use sender phone as customer phone in case customer phone is empty
* Fix - Fixed warnings Undefined index:  in israel-post-for-woocommerce/includes/views/generate-label-popup.php on line 106  
* Fix - Fixed warnings - PHP Notice:  Trying to get property of non-object in israel-post-for-woocommerce/includes/class-wc-il-post-admin.php on line 260

= 1.4.2 =
* Enhancement - Added new Label Format in settings and generate shipping label form

= 1.4.1 =
* Added message on country services api call if status code is 204 than show a message in generate label popup - No available shipping options for the destination country.
* Added HS Code field in Product details table in generate label form
* Fixed responsive issue in generate label form

= 1.4 =
* Make sender address field required in settings page

= 1.3.9 =
* Fixed error in generate label api call - Bad Request: The posted object struct is incorrect

= 1.3.8 =
* Added functionality - Address line 2 concatenate with address line 1 in generate label post data
* Set Debug log active as default
* Set weight round in generate label form
* Updated settings page tab
* Set Sender address default from WooCommerce settings
* Remove special character from Product title from generate label form

= 1.3.7 =
* Added error log on country service api call
* Added functionality for make the row disabled if product is already shipped and added a label - shipped

= 1.3.6 =
* Fixed settings save issue

= 1.3.5 =
* Updated settings page allignment from center to right
* Updated documents link
* Updated test connection message

= 1.3.4 =
* Changed error message in test connection functionality

= 1.3.3 =
* Add error handling in settings and label generate form
* Fixed error in test connection functionality

= 1.3.2 =
* Updated Hebrew language translation files

= 1.3.1 =
* Added compatibility with WooCommerce 3.9.1 and WordPress 5.3.2

= 1.3 =
* Updated settings page design
* Updated Documentation page

= 1.2 =
* Added documents page
* Added logs link in settings page

= 1.1 =
* Added option for set shipping provider USPS if destination country is USA
* Updated text domain of plugin

= 1.0 =
* Initial version