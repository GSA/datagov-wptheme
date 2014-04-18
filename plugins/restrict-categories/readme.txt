=== Restrict Categories ===
Contributors: mmuro
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=G87A9UN9CLPH4&lc=US&item_name=Restrict%20Categories&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: restrict, admin, administration, cms, categories, category
Requires at least: 3.1
Tested up to: 3.8.1
Stable tag: 2.6.3

Restrict the categories that users can view, add, and edit in the admin panel.

== Description ==

*Restrict Categories* is a plugin that allows you to select which categories users can view, add, and edit in the Posts edit screen.

This plugin allows you to restrict access based on the user role AND username.

== Installation ==

1. Upload `restrict-categories` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to <em>Settings > Restrict Categories</em> to configure which categories will be restricted for each user and/or role.

== Frequently Asked Questions ==

= Does this work with custom roles I have created? =

Yes!  Roles created through plugins like Members will be listed on <em>Settings > Restrict Categories</em>

= Will this prevent my regular visitors from seeing posts? =

No.  This plugin only affects logged in users in the admin panel.

= I messed up and somehow prevented the Administrator account from seeing certain categories! =

Restrict Categories is an opt-in plugin.  By default, every role has access to every category, depending on the capabilities.
If you check a category box in a certain role, such as Administrator, you will <em>restrict</em> that role to viewing only those categories.

To fix this, go to <em>Settings > Restrict Categories</em>, uncheck <em>all</em> boxes under the Administrator account and save your changes.  You can also click the Reset button to reset all changes to the default configuration.

= How does it work when I've selected categories for a role AND a user? =

Selecting categories for a user will <em>override</em> the categories you've selected for that user's role.

In other words, Restrict Categories allows you complete control over groups of users while also allowing you to selectively change a setting for a single user.

== Screenshots ==

1. Roles and Users with selected categories to restrict
2. The Posts edit screen with restricted categories
3. The Categories selection on the Add New Post screen with restricted categories

== Changelog ==

**Version 2.6.3 - Jan 28, 2013**

* Add "Search Users" feature to Users tab
* Remove check for get_users_of_blog, which was deprecated in WordPress 3.1.

**Version 2.6.2 - Aug 14, 2013**

* Fix notices in PHP 5.4

**Version 2.6.1 - Mar 12, 2013**

* Check if array key exists before stripping placeholder to resolve warnings for some servers

**Version 2.6 - Mar 11, 2013**

* Add "Select All" feature
* Update minimum capability to manage_categories
* Fix bug where users may see all categories in certain cases
* Fix PHP notices

**Version 2.5 - Sep 13, 2012**

* Fix bug for saving with pagination

**Version 2.4 - Jan 17, 2012**

* Add pagination controls for Roles and Users. Customize number displayed using the Screen Options tab

**Version 2.3 - Aug 17, 2011**

* Fix bug where custom taxonomies were being hidden on the Add Post screen

**Version 2.2.2 - Jul 13, 2011**

* Fix bug where XML-RPC support was broken

**Version 2.2.1 - May 19, 2011**

* Fix bug where 'View All' and 'Most Popular' tabs were not set correctly for the Roles screen

**Version 2.2 - May 16, 2011**

* Fix bug where Pages type was not being displayed in Internal Linking WordPress feature
* Added 'View All' and 'Most Popular' tabs to each role/user to make it easier to find relevant categories

**Version 2.1 - May 10, 2011**

* Correct problem that prevented tabbed interface from being uploaded

**Version 2.0 - Apr 18, 2011**

* Improve the user interface by separating the Roles and Users via tabs

**Version 1.9 - Mar 30, 2011**

* Added XML-RPC support so categories are restricted using mobile devices and remote applications

**Version 1.8 - Mar 12, 2011**

* Fix bug for WordPress 3.1 users that broke user restriction.
* Switch code to PHP classes to prevent conflicts with other plugins.
* Now using register_setting to save options instead of custom method.
* Uninstalling/Deleting plugin now removes Restrict Categories database options.

**Version 1.7 - Feb 18, 2011**

* Fix bug that hid list of images under Gallery tab on Media Uploader.

**Version 1.6.1 - Jan 24, 2011**

* Fix bug where user restriction was not being applied.

**Version 1.6 - Jan 20, 2011**

* Added restriction based on username
* Show number of selected categories for each role and username
* Improve reliability of Posts Edit screen query

**Version 1.5 - Jan 6, 2011**

* Updated user interface
* Fix bug to allow sub-categories
* Fix bug to allow duplicate category names
* Fix bug for categories with single quotes, ampersands, and other encoded characters

**Version 1.4 - Nov 29, 2010**

* Fix for bug assuming database table prefix
* Improve compatibility with PHP 5.2 and empty array checking
* Added string localization

**Version 1.3 - Nov 23, 2010**

* Update that removes restricted categories from all terms lists (Category management page, Posts dropdown filter, and New/Edit post category list)
* Fix for "Wrong datatype" bug on checkboxes

**Version 1.2 - Nov 8, 2010**

* Fix for a bug that would allow restricted users to use the category dropdown filter to gain access to categories

**Version 1.1 - Nov 8, 2010**

* Updated list of categories to include those that are unassigned
* Fixed a small HTML bug
* Now storing options as an array instead of converting to a string

**Version 1.0 - Nov 8, 2010**

* Plugin launch!

== Upgrade Notice ==

= 2.6.3
Add "Search Users" feature to Users tab

= 2.5 =
Bug fix for saving with pagination

= 2.4 =
Added pagination controls for Roles and Users. Customize number displayed using the Screen Options tab.

= 2.3 =
Bug fix for hidden custom taxonomies on the Add Post screen.

= 2.2.2 =
Recommended upgrade for XML-RPC users! Fixes bug that accidentally broke restriction for XML-RPC.

= 2.2.1 =
Recommended upgrade that fixes bug with 'View All' and 'Most Popular' tabs not being set correctly.

= 2.2 =
Recommended upgrade that fixes bug with Internal Linking feature. More UI improvements.

= 2.1 =
Recommended upgrade! Corrects error that prevented the tabbed interface from being uploaded in the last version.

= 2.0 =
Improved the UI by separating the Roles and Users into their own tabs.

= 1.9 =
Added support for XML-RPC so categories are now restricted for mobile devices (i.e. WordPress iPhone app).

= 1.8 =
Bug fix for WordPress 3.1 users that broke user restriction.

= 1.7 =
Bug fix for restricted users where uploaded images listed in the Gallery tab on Media Uploader were hidden.

= 1.6.1 =
For WordPress 3.0 - 3.0.4 users: highly recommended update that fixes a bug where user restriction was not being applied.

= 1.6 =
Added ability to restrict categories based on username.

= 1.5 =
Recommended upgrade that improves user interface and fixes reported bugs.

= 1.4 =
This version fixes problems with error messages.

= 1.3 =
Upgrade for compatibility with WordPress 3.1.

= 1.2 =
Recommended upgrade to correct bug which would allow restricted users to bypass category restriction.

= 1.1 =
This version adds the ability to select unassigned categories.