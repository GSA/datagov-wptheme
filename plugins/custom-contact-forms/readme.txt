=== Custom Contact Forms ===
Contributors: Taylor Lovett
Donate link: http://www.taylorlovett.com
Tags: contact form, web form, custom contact form, custom forms, captcha form, contact fields, form mailers
Requires at least: 2.8.1
Tested up to: 3.4.1
Stable tag: 5.1.0.3

A customizable and intuitive contact form plugin for Wordpress.

== Description ==

__Attention:__ Custom Contact Forms really needs support from developers. We need code contribution to make this plugin better. Please fork the plugin on [Github](https://github.com/tlovett1/custom-contact-forms)!

Customize every aspect of your forms without any knowledge of CSS: borders, padding, sizes, colors. Ton's of great features. Required fields, captchas, tooltip popovers, unlimited fields/forms/form styles, use a custom thank you page or built-in popover with a custom success message set for each form.

Special Features:
------------------

*	__NEW__ Rearrange fields with a drag-and-drop interface
*	__NEW__ Export form submissions to .CSV
*	__NEW__ File Upload Fields
*	__NEW__ Redesigned admin panel
*	__NEW__ - Option to only include JQuery and CSS and pages that actually use your forms
*	__NEW__ - Date field that when click displays a stylish calender popover
*	Saved Form Submission dashboard widget!
*	Instantly attach a dropdown with all the countries or all the US States - new fixed fields
*	Import and export forms/fields/styles/etc. with ease!
*	All form submissions saved and displayed in admin panel as well as emailed to you
*	This plugin can now be translated in to different languages.
*	Error messages can be customized for each field
*	Choose between XHTML or HTML. All code is clean and valid!
*	Create __unlimited__ forms
*	Create __unlimited__ fields
*	Required Fields
*	__NEW__ - a dashboard widget that displays the latest form submissions
*	Custom Contact Forms now uses PHPMailer and thus supports STMP and SSL
*	Have your contact forms send mail to multiple email addresses
*	Create text fields, textareas, checkboxs, and dropdown fields!
*	Custom HTML Forms Feature - if you are a web developer you can write your own form html and __use this plugin simply to process your form requests__. Comes with a few useful features.
*	__Displays forms in theme files__ as well as pages and posts.
*	Set a different destination email address for each form
*	Customize every aspect of fields and forms: titles, labels, maxlength, initial value, form action, form method, form style, and much more
*	Create checkboxes, textareas, text fields, etc.
*	__Captcha__ and __"Are You Human?"__ spam blockers included and easily attached to any form
*	Create __custom styles in the style manager__ to change the appearance of your forms: borders, font sizes, colors, padding, margins, background, and more
*	You can create unlimited styles to use on as many forms as you want without any knowledge of css or html.
*	Show a stylish JQuery form thank you message or use a custom thank you page.
*	Custom error pages for when forms are filled out incorrectly
*	Option to have forms remember field values for when users hit the back button after an error
*	Easily report bugs and suggest new features
*	Script in constant development - new version released every week
*	Easily process your forms with 3rd party sites like Infusionsoft or Aweber
*	Set a __custom thank you page__ for each form or use the built in thank you page popover with a custom thank you message
*	No javascript required
*	Detailed guide for using the plugin as well as default content to help you understand how to use Custom COntact Forms
*	Stylish field tooltips powered by jquery
*	Manage options for your dropdowns and radio fields in an easy to use manager
*	Popover forms with Jquery (Coming soon!)
*	Free unlimited support
*	AJAX enabled admin panel
*	Assign different CSS classes to each field.
*	Ability to disable JQuery if it is conflicting with other plugins.
*	Uses UTF8 character set so non-english characters are easily used!

Restrictions/Requirements:
-------------------------
*	Works with Wordpress 3.0+
*	PHP register_globals and safe_mode should be set to "Off" (this is done in your php.ini file)
*	Your theme must call wp_head() and wp_footer()

== Installation ==
1. Upload to /wp-content/plugins
2. Activate the plugin from your Wordpress Admin Panel
3. Configure the plugin, create fields, and create forms in the Settings page called Custom Contact Forms
4. Display those forms in posts and pages by inserting the code: __[customcontact form=FORMID]__
5. In the instruction section of the plugin. Press the button to insert the default content. The default content contains a very generic form that will help you understand the many ways you can use Custom Contact Forms.

== Configuring and Using the Plugin ==
1. Create as many forms as you want.
2. Create fields and attach those fields to the forms of your choice. Attach the fields in the order that you want them to show up in the form. If you mess up you can detach and reattach them.
3. Display those forms in posts and pages by inserting the code: __[customcontact form=FORMID]__. Replace __FORMID__ with the id listed to the left of the form slug next to the form of your choice above. You can also __display forms in theme files__; the code for this is provided within each forms admin section.
4. Prevent spam by attaching the fixed field, captcha or ishuman. Captcha requires users to type in a number shown on an image. Ishuman requires users to check a box to prove they aren't a spam bot.
5. Add a form to your sidebar, by dragging the Custom Contact Form reusable widget in to your sidebar.
6. Configure the General Settings appropriately; this is important if you want to receive your web form messages!
7. Create form styles to change your forms appearances. The image below explains how each style field can change the look of your forms.
8. (advanced) If you are confident in your HTML and CSS skills, you can use the Custom HTML Forms feature as a framework and write your forms from scratch. This allows you to use this plugin simply to process your form requests. The Custom HTML Forms feature will process and email any form variables sent to it regardless of whether they are created in the fields manager.

Custom Contact Forms is an extremely intuitive plugin allowing you to create any type of contact form you can image. CCF is very user friendly but with possibilities comes complexity. __It is recommend that you click the button in the instructions section of the plugin to add default fields, field options, and forms.__ The default content will help you get a feel for the amazing things you can accomplish with this plugin. __It is also recommended you click the "Show Plugin Usage Popover"__ in the instruction area of the admin page to read in detail about all parts of the plugin.

== Support ==
For questions, feature requests, and support concerning the Custom Contact Forms plugin, please visit:
http://www.taylorlovett.com/wordpress-plugins

== Frequently Asked Questions ==

= Something isn't working. Help! =
*	First try deactivating and reactivating the plugin
* 	If that doesn't fix the problem, try deleting and reinstalling the plugin
*	If that doesn't work, you should file a bug report.

= When I try to do something in the admin panel, all I get is a new page with a -1. =
*	This is a bug we are currently trying to fix that usually happens in Internet Explorer 8. If you are having this problem, please try using Firefox.

= All my fields and field options got detached. What do I do? Will this happen again? =
*	Custom Contact Forms changed the way fields and field options are attached in version 4.5. It won't happen again. Just reattach everything and continue using the plugin.

= I don't know where to start. This is really confusing. =
*	Read the Plugin Usage Popover; it explains how to use everything in great detail.
*	If you don't want to read or learn anything, simply press the "Insert Default Content" button (in the Plugin Usage Popover). This creates a few basic fields and a form. Then just insert the form in a page, post, or theme file.

= I can't figure out how to insert a form into a page or post. Help! =
*	Find the form in the Form Manager, a snippet of code will be displaed that looks like [customcontact form=1]. Replace 1 with the ID for the specific form you want to use and insert the snippet into a page or post. You're done!

= How can I include jQuery and CSS files only on pages that display a form? =
*	First go to general settings, set "Restrict Frontend JS and CSS to Form Pages Only" to "Yes".
*	Now go to the Form Manager, within each of your forms there is a field called "Form Pages". Add the post or page id's where you plan to use that form to the "Form Pages" field.

= I'm not receiving any emails =
*	Check that the "Email Form Submissions" option is set to yes in General Settings.
*	Try filling out a form with the "Use Wordpress Mail Function" option set to "No".
*	Make sure the "Default From" email you are using within General Settings actually exists on your server.
*	Try deactivating other plugins to make sure there are no conflicts
*	If there is still a problem, contact your host. This plugin utilizes existing mail functionality on your server, it doesn't create any new functions. If there is a problem, then it is with Wordpress or your host.

= When I activate Custom Contact Forms, the Javascript for another plugin or my theme does not work. =
*	Disable the "Frontend jQuery" option in General Settings. Custom Contact Forms will still work without JQuery but won't be as pretty.

= I need even more customization in my forms. What can I do? =
*	Use the Custom HTML Forms Feature (see admin panel) which allows you to write the HTML/CSS for each of your forms.

= The form success popover is not showing up. =
*	The form success popover is included in wp_footer. If your theme does not call wp_footer(), it will not work.

= Certain characters aren't showing up correctly in my emails. =
*	First, make sure you are upgraded to the latest version which uses UTF-8
*	If that doesn't fix the problem, try using a different mail client. Sometimes mail clients display certain languages poorly.

== Upgrade Notice ==
We are planning to add popover forms and file attachments soon.

== Screenshots ==
Visit http://www.taylorlovett.com/wordpress-plugins for screenshots. Right now all the screenshots are from Version 1, thus are quite out-dated. Install the plugin to see what it looks like. You won't regret it. I promise!

== Changelog ==

= 5.1.0.3 =
*   custom-contact-forms-front.php - $field_value properly escaped

= 5.1.0.1 =
*   custom-contact-forms-admin.php - Small UI updates
*   css/custom-contact-forms-admin.css - New admin styles

= 5.0.0.1 =
*	ishuman fixed field bug fixed
*	attach field bug fixed

= 5.0.0.0 =
*	Admin user interface improved 1000% with drag-and-drop fields as well as save/delete buttons.
*	Import bug fixed

= 4.8.0.0 =
*	js/jquery.tools.min.js - Updated to fix firefox tooltip bug

= 4.7.0.5 =
*	custom-contact-forms-front.php - Notice bugs fixed
*	custom-contact-forms.php - Notice bugs fixed
*	modules/db/custom-contact-forms-activate-db.php - Notice bugs fixed
*	modules/db/custom-contact-forms-db.php - Notice bugs fixed
*	modules/extra_fields/countries_field.php - Notice bugs fixed
*	modules/extra_fields/states_field.php - Notice bugs fixed
*	custom-contact-forms-admin.php - Notice bugs fixed, new language phrases added

= 4.7.0.4 =
*	custom-contact-forms-front.php - Language stuff changed

= 4.7.0.3 =
*	js/jquery.tools.js - Updated to not include jQuery
*	custom-contact-forms-front.php - jQuery bug fixed


= 4.7.0.1 =
*	custom-contact-forms-front.php - Look and feel changed
*	css/custom-contact-forms.css - Look and feel changed
*	js/custom-contact-forms-admin-ajax.js - IE detach field/field option bug fixed


= 4.7.0.0 =
*	All files have been changed!

= 4.6.0.1 =
*	custom-contact-forms-admin.php - -1 bug fixed in IE
*	js/jquery.form.js - Updated jquery forms plugin fixes huge IE bug

= 4.6.0.0 =
*	custom-contact-forms.php - Dependencies included differently, new general setting options
*	custom-contact-forms-admin.php - New field type (Date), guidelines inserted in to all pages, new general settings
*	modules/usage_popover/custom-contact-forms-usage-popover.php - New field type added
*	custom-contact-forms.php - Dependencies included differently, new field type added, JQuery files included differently
*	js/custom-contact-forms-datepicker.js - New file
*	js/jquery.ui.datepicker.js - New file



= 4.5.3.2 =
*	modules/widgets/custom-contact-forms-dashboard.php - Bugs fixed
*	custom-contact-forms-admin.php - Quick start guide added to general settings and form submissions.
*	custom-contact-forms.php - Dashboard widget security bug fixed.
*	modules/usage_popover/custom-contact-forms-quick-start-popover.php - Language changes made
*	modules/db/custom-contact-forms-db.php - Roles bug fixed

= 4.5.3.1 =
*	modules/widgets/custom-contact-forms-dashboard.php - Array shift bug fix

= 4.5.3.0 =
*	custom-contact-forms-admin.php - Dashboard widget security bug fixed. Now you can limit which users can see the dashboard widget. Also a quick start guide has been added.
*	custom-contact-forms.php - Dashboard widget security bug fixed.
*	modules/widgets/custom-contact-forms-dashboard.php - Dashboard widget security bug fixed. Now you can limit which users can see the dashboard widget.
*	modules/usage_popover/custom-contact-forms-usage-popover.php - Minor display changes made
*	modules/usage_popover/custom-contact-forms-quick-start-popover.php - Minor display changes made
*	js/custom-contact-forms-admin.js - Quick start guide added
*	css/custom-contact-forms-admin.css - Quick start guide added


= 4.5.2.2 =
*	custom-contact-forms.php - JQuery plugin conflict fixed

= 4.5.2.1 =
*	js/custom-contact-forms-admin-ajax.js - Save image bug fixed
*	custom-contact-forms-admin.php - Minor display change

= 4.5.2 =
*	custom-contact-forms.php - Template form display function fixed
*	custom-contact-forms-admin.php - jQuery dialog used for plugin usage popover
*	modules/db/custom-contact-forms-activate.php - Field options column changed to text
*	modules/widgets/custom-contact-forms-dashboard.php - jQuery dialog used for popovers
*	modules/widgets/custom-contact-forms-dashboard.css - jQuery dialog used for popovers

= 4.5.1.2 =
*	modules/widgets/custom-contact-forms-widget.php - Widget form display bug fixed

= 4.5.1.1 =
*	custom-contact-forms-admin.php - Display changes, form submissions non-ajax delete fixed


= 4.5.1 =
*	custom-contact-forms.php - enable_form_access_manager option added and defaulted to disabled
*	custom-contact-forms-admin.php - enable_form_access_manager option added and defaulted to disabled
*	custom-contact-forms-front.php - enable_form_access_manager option added and defaulted to disabled

= 4.5.0 =
*	custom-contact-forms.php - Saved form submissions manager, form background color added to style manager, import/export feature
*	custom-contact-forms-utils.php - Methods added/removed for efficiency
*	custom-contact-forms-admin.php - Admin code seperated in to a different file
*	custom-contact-forms-front.php - Admin code seperated in to a different file
*	modules/db/custom-contact-forms-db.php - DB methods reorganized for efficiency
*	modules/db/custom-contact-forms-activate-db.php - DB methods reorganized for efficiency
*	modules/db/custom-contact-forms-default-db.php - DB methods reorganized for efficiency
*	modules/usage-popover/custom-contact-forms-popover.php - Popover code seperated in to a different file
*	modules/export/custom-contact-forms-export.php - Functions for importing and exporting
*	modules/extra_fields/countries_field.php
*	modules/extra_fields/date_field.php
*	modules/extra_fields/states_field.php
*	modules/widget/custom-contact-forms-dashboard.php
*	css/custom-contact-forms-admin.css - AJAX abilities added
*	css/custom-contact-forms-standard.css - Classes renamed
*	css/custom-contact-forms.css - Classes renamed
*	css/custom-contact-forms-dashboard.css - Classes renamed
*	js/custom-contact-forms-dashboard.js - AJAX abilities added to admin panel
*	lang/custom-contact-forms.po - Allows for translation to different languages
*	lang/custom-contact-forms.mo - Allows for translation to different languages

= 4.0.9.2 =
*	css/custom-contact-forms-admin.css - Minor display changes
*	js/custom-contact-forms.js - JQuery conflict issue fixed

= 4.0.9.1 =
*	custom-contact-forms-admin.php - Minor display changes
*	css/custom-contact-forms-admin.css - Minor display changes to field options

= 4.0.9 =
*	js/custom-contact-forms.js - JQuery conflict issue fixed
*	js/custom-contact-forms-admin.js - JQuery conflict issue fixed
*	js/custom-contact-forms-admin-inc.js - JQuery conflict issue fixed
*	js/custom-contact-forms-admin-ajax.js - JQuery conflict issue fixed
*	custom-contact-forms-admin.php - JQuery conflict issue fixed
*	custom-contact-forms-front.php - Unnecessary JQuery dependencies removed

= 4.0.8.1 =
*	custom-contact-forms-admin.php - Email charset set to UTF-8
*	css/custom-contact-forms-admin.css - Usage Popover z-index set to 10000 and Usage button styled.
*	custom-contact-forms-front.php - Email charset set to UTF-8

= 4.0.8 =
*	custom-contact-forms-admin.php - Admin panel updated, WP_PLUGIN_URL to plugins_url()
*	custom-contact-forms-front.php - WP_PLUGIN_URL to plugins_url()

= 4.0.7 =
*	custom-contact-forms-admin.php - Admin panel updated

= 4.0.6 =
*	modules/widgets/custom-contact-forms-widget.php - Form title added via widget

= 4.0.5 =
*	modules/db/custom-contact-forms-db.php - Form email cutoff bug fixed

= 4.0.4 =
*	custom-contact-forms-admin.php - Bug reporting mail error fixed

= 4.0.3 =
*	custom-contact-forms-front.php - PHPMailer bug fixed, form redirect fixed
*	custom-contact-forms-static.php - Form redirect function added
*	custom-contact-forms-admin.php - redirects fixed, phpmailer bug fixed
*	widget/phpmailer - deleted
*	widget/db/custom-contact-forms-db.php - table charsets changed to UTF8

= 4.0.2 =
*	custom-contact-forms-front.php - Field instructions bug fixed
*	custom-contact-forms-admin.php - Display change

= 4.0.1 =
*	custom-contact-forms.php
*	custom-contact-forms-admin.php - support for multiple form destination emails added
*	custom-contact-forms-front.php - Mail bug fixed, email validation bug fixed
*	lang/custom-contact-forms.php - Phrases deleted/added


= 4.0.0 =
*	custom-contact-forms.php - Saved form submissions manager, form background color added to style manager, import/export feature
*	custom-contact-forms-user-data.php - Saved form submission
*	custom-contact-forms-db.php - DB methods reorganized for efficiency
*	custom-contact-forms-static.php - Methods added/removed for efficiency
*	custom-contact-forms-admin.php - Admin code seperated in to a different file
*	custom-contact-forms-popover.php - Popover code seperated in to a different file
*	custom-contact-forms-export.php - Functions for importing and exporting
*	css/custom-contact-forms-admin.css - AJAX abilities added
*	css/custom-contact-forms-standard.css - Classes renamed
*	js/custom-contact-forms-admin.js - AJAX abilities added to admin panel
*	download.php - Allows export file to be downloaded
*	lang/custom-contact-forms.po - Allows for translation to different languages
*	lang/custom-contact-forms.mo - Allows for translation to different languages

= 3.5.5 =
*	custom-contact-forms.php - Plugin usage popover reworded
*	css/custom-contact-forms-admin.css - Admin panel display problem fixed

= 3.5.4 =
*	custom-contact-forms.php - custom thank you redirect fix
*	custom-contact-forms-db.php - Style insert bug fixed, Unexpected header output bug fixed

= 3.5.3 =
*	custom-contact-forms.php - Style popover height option added to style manager. Form title heading not shown if left blank.
*	custom-contact-forms-db.php - New success popover height column added to styles table

= 3.5.2 =
*	custom-contact-forms.php - Plugin Usage popover added, insert default content button
*	custom-contact-forms-db.php - Insert default content function

= 3.5.1 =
*	custom-contact-forms.php - Style options added, color picker added, success popover styling bugs fixed
*	custom-contact-forms-db.php - Style format changed, new style fields added to tables
*	Lots of javascript files
*	Lots of images for the colorpicker

= 3.5.0 =
*	custom-contact-forms.php - Radio and dropdowns added via the field option manager
*	custom-contact-forms-mailer.php - Email body changed
*	custom-contact-forms-db.php - Field option methods added
*	custom-contact-forms.css - Form styles reorganized, file removed
*	css/custom-contact-forms.css - Form styles reorganized
*	css/custom-contact-forms-standards.css - Form styles reorganized
*	css/custom-contact-forms-admin.css - Form styles reorganized

= 3.1.0 =
*	custom-contact-forms.php - Success message title, disable jquery, choose between xhmtl and html, and more
*	custom-contact-forms-db.php - Success message title added
*	custom-contact-forms.css - Form styles rewritten

= 3.0.2 =
*	custom-contact-forms.php - Bugs fixed

= 3.0.1 =
*	custom-contact-forms.php - Php tags added to theme form display code

= 3.0.0 =
*	custom-contact-forms.php - Required fields, admin panel changed, style manager bugs fixed, custom html feature added, much more
*	custom-contact-forms-db.php - New functions added and old ones fixed
*	custom-contact-forms.css - New styles added and old ones modified

= 2.2.5 =
*	custom-contact-forms.php - Fixed field insert bug fixed

= 2.2.4 =
*	custom-contact-forms.php - Textarea field instruction bug fixed

= 2.2.3 =
*	custom-contact-forms.php - Remember fields bug fixed, init rearranged, field instructions
*	custom-contact-forms.css
*	custom-contact-forms-db.php

= 2.2.0 =
*	custom-contact-forms.php - Plugin nav, hide plugin author link, bug reporting, suggest a feature
*	custom-contact-forms.css - New styles added and style bugs fixed

= 2.1.0 =
*	custom-contact-forms.php - New fixed field added, plugin news, bug fixes
*	custom-contact-forms.css - New styles added and style bugs fixed
*	custom-contact-forms-db.php - New fixed field added

= 2.0.3 =
*	custom-contact-forms.php - custom style checkbox display:block error fixed
*	custom-contact-forms.css - li's converted to p's

= 2.0.2 =
*	custom-contact-forms.php - Form li's changed to p's
*	images/ - folder readded to correct captcha error

= 2.0.1 =
*	custom-contact-forms.php - Duplicate form slug bug fixed, default style values added, stripslahses on form messages
*	custom-contact-forms-db.php - default style values added

= 2.0.0 =
*	custom-contact-forms.php - Style manager added
*	custom-contact-forms.css - style manager styles added
*	custom-contact-forms-db.php - Style manager db functions added

= 1.2.1 =
*	custom-contact-forms.php - Upgrade options changed
*	custom-contact-forms-css.php - CSS bug corrected

= 1.2.0 =
*	custom-contact-forms.php - Option to update to Custom Contact Forms Pro

= 1.1.3 =
*	custom-contact-forms.php - Captcha label bug fixed
*	custom-contact-forms-db.php - Default captcha label changed

= 1.1.2 =
*	custom-contact-forms-db.php - create_tables function edited to work for Wordpress MU due to error in wp-admin/includes/upgrade.php

= 1.1.1 =
*	custom-contact-forms.css - Label styles changed
*	custom-contact-forms.php - Admin option added to remember field values

= 1.1.0 =
*	custom-contact-forms-db.php - Table upgrade functions added
*	custom-contact-forms.php - New functions for error handling and captcha
*	custom-contact-forms.css - Forms restyled
*	custom-contact-forms-images.php - Image handling class added
*	image.php, images/ - Image for captcha displaying

= 1.0.1 =
*	custom-contact-forms.css - Form style changes

= 1.0.0 =
*	Plugin Release
