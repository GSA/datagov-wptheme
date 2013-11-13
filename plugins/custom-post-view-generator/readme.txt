=== Custom Post View Generator ===

Contributors: Marco Constâncio
Tags: custom field, custom post, view, list,
Requires at least: 3.1
Tested up to: 3.2.1
Stable Tag: trunk

Creates views allowing the user to display of data of custom post type fields and other wordpress fields without the any theme modification.

== Description ==

Creates views allowing the user to display of data of custom post type fields and other wordpress fields without the any theme modification.

The plugin allows administrator the ability to create views by simply selecting a post type, the fields and their respective types on the administration page and each time someone views a post from that type, the data from the custom fields will be displayed on the frontend.

This plugin will use custom fields that are created in the wordpress page/post admin page and with fields created with the following plugins:

* **Reed Write** By Brian S. Reed (http://www.scottreeddesign.com/)
* **Custom Content Type Manager** By Everett Griffiths (http://code.google.com/p/wordpress-custom-content-type-manager/)
* **Ultimate Post Type Manager** by XYDAC (http://posttypemanager.wordpress.com/)
* **Advanced Custom Fields** by Elliot Condon (http://http://plugins.elliotcondon.com/advanced-custom-fields/)

PLEASE READ THE Frequently Asked Questions AND Fields Info FOR MORE DETAILED INFORMATION.

== Installation ==

1. Install the plugin either by uploading the contents of
custom-post-view-generator.zip to the '/wp-content/plugins/' directory
or by using the 'Add New' in 'Plugins' menu in WordPress
1. Activate the plugin through the 'Plugins' menu in WordPress
1. It is RECOMENDED that you delete you previous views and remake them to avoid compatibility issues.

= Instructions =

POST VIEWS:

1. Create the desired custom fields in the wordpress page/post admin page or with the plugins mentioned in the description.
1. Go to 'Page Views' on the 'CTP View Generator'
1. Select a post yype in the top drop-down box **Post type** and drag the a desired field on **Available fields** area to the left side.
1. After dragging the field, a grey box will be displayed allowing to setup the display properties of that field. Write the desired label for that custom field, select the appropriate type (you can either test each one, or check the *Fields Info* section in **Other Notes** page), select available output options and press **Save Layout**.
1. OPTIONAL: You can also change the field order by dragging the grey box(es) up and down and even select a diferent view template on the **Template** drop-down box and press **Save Layout** to save the changes

LIST VIEWS:

1. Go to 'List Views' on the 'CTP View Generator'
1. Input the desired list name and select a template on the 'List Views' meta box.
1. In the 'Fields' meta box, follow the same instructions that are indicated for the creation of 'Post Views'.
1. In the 'Finish' meta box press **Save Layout** and use the shortcode suggested in the 'List Views' meta box in a post or page to display the list view.
1. OPTIONAL 1: You can also add filters in the 'Paremeters' meta box, just make sure you read all intructions that are presented on the right side each time you change the paremeter section.
1. OPTIONAL 2: You can use the extra buttons in the 'Finish' meta box to instantly create a post or page to to display the list view.

== Screenshots ==

1. Administration Post View Page.
2. Post View.
3. Administration List View Page.
4. List View.

== Frequently Asked Questions ==

= How does this plugin works ? =

For the post views, the plugin works by replacing the post content with the data of the selected fields in the administration panel and it does this by using a wordpress filter function (**add_filter**) making the changes on-the-fly without any file modification. As for the list views, the plugins simply uses wordpress shortcodes to display the views.

= So this will work with any custom post type/fields ? =

No. Most custom post type plugins store the necessary information in diferent tables and in diferent ways so it is impossible to make this plugin to work will all custom post type/field. Currently this plugins only works with custom post types created by plugins specified in the description/installation pages.

= Are you going to add suport for more custom post type plugins ? =

Depends. The custom post type plugins that this plugin supports are very complete, and my opinion, the best plugins for creating custom post types, so it might not be worth it to add more, especially since some of them might take a deal to implement them. If someone sends me code for 'plugincode' or 'fieldtypes' folders or if the the plugin in question is widely used, I might add it.

= Is it possible add/remove/modify templates ? =

Yes. All the code relative to templates in this plugin are located in the 'templates/post' and 'templates/list' folder. In each folder there is a file called 'template_manual' that contains the necessary information to create/modify a tempate.

= Is it possible add/remove/modify types options in the 'Post Views' page and fields meta box in the 'List View' page ? =

Yes. All the code relative to field types in this plugin are located in the 'fieldtypes' folder and can be easily changed. In that folder there is a file called 'cvpg_fieldtype_manual' that contains the necessary information to create/modify a field type. The most simple filetype that can be used as an example is the 'date' file type.

= How can add support for other custom post type/fields plugin ? =

If you are familiar the custom post type/fields plugin that you are using or you created the custom post type youself and know how extract the its information, you can extend this plugin support by creating the appropiate file in the 'pluginscode' folder. In that folder there is a file called 'cpvg_plugincode_manual' that contains the necessary information to add support for a custom post type plugin.

== Fields Info ==

When selecting wich custom fields are going to be displayed, you need to select the appropriate type. Here is a list of the custom fields created by wordpress and the recomended plugins and their appropriate types in this plugin. NOTE1: plugins that add custom fields to the wordpress posts/pages usually store the data in the POSTMETA table, so check that section. NOTE2: Some plugins store data from custom fields differently depending on options selected by the admin in the plugins option page, so it might be necessary to try other types on this plugin.

= Wordpress Fields =

POST, PAGE:

* **ID, Parent ID** - Wordpress Post/Page ID, text
* **Author Id** - Wordpress User ID, text
* **Creation Date, Last modified** - Date, text
* **Post Url** - Hyperlink, text
* **Thumbnail ID** - Image (Wordpress Attachment)

USER:

* **ID**- Wordpress User ID, text
* **User status**- Boolean, text
* **User url** - Hyperlink, text
* **Remaing fields**: text

CATEGORY:

* **Id** - Wordpress Category ID(s), Muliple values (Serialized)
* **Remaining Fields** - Muliple values (Serialized)

TAG:

* **Id** - Wordpress Tag ID(s), Muliple values (Serialized)
* **Remaining Fields** - Muliple values (Serialized)

POSTMETA, TAXONOMY:

* **All Fields** - Depends how the values where stored.

= Reed Write By Brian S. Reed =

* **Checkboxes** - Mutiple Values (Serialized)
* **Single Line of Text** - Text
* **Mutiple Lines of Text** - Text
* **Date** - Date
* **Select** - Text
* **Select Multiple** - Mutiple Values (Vertical Bar)
* **Select an Image** - Image(s) (Wordpress Attachment)
* **Select a Color** - Color (Web)
* **Select a Pdf** - Wordpress Attachment ID(s)

= Custom Content Type Manager By Everett Griffiths =

* **Checkbox** - Boolean
* **Color Selector** - Color/Text (Depends on what the user Writes)
* **Date** - Text, Date
* **Dropdown** - Text
* **Image** - Image(s) (Wordpress Attachment)
* **Media Field** - Wordpress Attachment ID(s), Image (Wordpress Attachment), Audio, Video
* **Multiselect** - Multiple Values (JSON)
* **Relation** - Wordpress Attachment ID(s),
* **User** - Worpress User ID
* **Text** - Text
* **TextArea** - Text
* **Wysiwyg** - Text

= Ultimate Post Type Manager by XYDAC =

* **Image** - Single Image Url
* **Link** - Hiperlink (URL)
* **Text Area** - Text
* **Rich Text Area** - Text
* **Check Box** - Mutiple Values (Vertical Bar)
* **Gallery** - Multiple Image Urls (Comma)
* **Text** - Text
* **Radio Button** - Mutiple Values (Vertical Bar)
* **Combo Box** - Text

= Advanced Custom Fields by Elliot Condon =

* **Checkbox** - Multiple Values (Serialized)
* **Date** - Datw
* **File** - Wordpress Attachment ID(s), Image(s) (Wordpress Attachment)
* **Image** - Image(s) (Wordpress Attachment)
* **Number** - Text 
* **Page Link** - Wordpress Post/Page ID
* **Post Object** - Wordpress Post/Page ID
* **Radio Button** - Text
* **Relationship** - Wordpress Post/Page ID(s), Wordpress Post/Page ID(s) (Serialized) 
* **Select** - Text
* **Select (Multiple Values)** - Multiple Values (Array)
* **Text** - Text
* **Text Area** - Text
* **True / False** - Boolean
* **Wysiwyg Editor** - Text

== Changelog ==

= 0.1 =
First version of the plugin.

= 0.1.1 =
Fixed issue wich resulted warnings in php versions before 5.3.

= 0.1.2 =
Fixed bug that result incompatibility with other plugins that are using the add_filter function.
Fixed issue wich resulted warnings with content types plugins.

= 0.1.3 =
Fixed bug that resulted in shortcodes not being processed.
Fixed error in template.
Added new template.

= 0.2.0 =
Complete rewrite of code for better perfomance.
Added option to create views for post and pages.
Added option to select fields from other sections (categories, tags, etc).
Added option to create list views.

= 0.2.1 =
Fixed bug that caused and error on wp 3.3b2.

= 0.2.2 =
Fixed bug that caused post excerpt not showing.
Added two new fieldtypes.
Added support for the Advanced Custom Fields plugin.

= 0.3.0 =
Fixed RTL align bug and minor fixes.
Added support for touchscreens.
Added sorting and paging options for list views.
Added Misc field section with fields that allow the insertion of html code and other elements.

= 0.3.1 =
Modification that allows single quotes in html fields on post views.

= 0.3.2 =
Fix for wordpress 3.4.

= 0.4.0 =
Replaced Content Types code with code to support Reed Write plugin.
Updated support for the newer versions of Advanced Custom Fields.
Added several templates.
Added pagination support for list views (depends of the template).
Added user sorting support for list views (depends of the template).
Added suport to use the post featured image.
Several code fixes.

= 0.4.1 =
Small fix.

= 0.4.2 =
Small fixes to remove notices.
Disable function that modified the excerpt.
Added serialized wordpress attachment fieldtype.

= 0.4.3 =
Fix to the code that removed the read more link.

= 0.4.4 =
Fix for list views in wordpress 3.5.

= 0.4.5 =
Fix for load_scripts fatal error.
