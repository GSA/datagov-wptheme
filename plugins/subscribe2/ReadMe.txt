=== Subscribe2 ===
Contributors: MattyRob, Skippy, RavanH, bhadaway
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=2387904
Tags: posts, subscription, email, subscribe, notify, notification
Requires at least: 3.3
Tested up to: 3.6.1
Stable tag: 9.0
License: GPL3

Sends a list of subscribers an email notification when new posts are published to your blog

== Description ==
Subscribe2 provides a comprehensive subscription management and email notification system for WordPress blogs that sends email notifications to a list of subscribers when you publish new content to your blog.

Email Notifications can be sent on a per-post basis or periodically in a Digest email. Additionally, certain categories can be excluded from inclusion in the notification and posts can be excluded on an individual basis by setting a custom field.

The plugin also handles subscription requests allowing users to publicly subscribe (**Public Subscribers**) by submitting their email address in an easy to use form or to register with your blog (**Registered Users**) which enables greater flexibility over the email content for per-post notifications for the subscriber. Admins are given control over the presentation of the email notifications, can bulk manage subscriptions for users and manually send email notices to subscribers.

The format of the email can also be customised for per-post notifications, Subscribe2 can generate emails for each of the following formats:

* plaintext excerpt
* plaintext full post (Registered Users only)
* HTML excerpt (Registered Users only)
* HTML full post (Registered Users only)

If you want to send full content HTML emails to Public Subscribers too then upgrade to [Subscribe2 HTML](http://semperplugins.com/plugins/subscribe2-html/).

== Installation ==
AUTOMATIC INSTALLATION

1. Log in to your WordPress blog and visit Plugins->Add New.
2. Search for Subscribe2, click "Install Now" and then Activate the Plugin
3. Visit the "Subscribe2 -> Settings" menu.
4. Configure the options to taste, including the email template and any categories which should be excluded from notification
5. Visit the "Subscribe2 -> Subscribers" menu.
6. Manually subscribe people as you see fit.
7. Create a [WordPress Page](http://codex.wordpress.org/Pages) to display the subscription form.  When creating the page, you may click the "S2" button on the QuickBar to automatically insert the Subscribe2 token.  Or, if you prefer, you may manually insert the Subscribe2 shortcode or token: [subscribe2] or the HTML invisible `<!--subscribe2-->` ***Ensure the token is on a line by itself and that it has a blank line above and below.***
This token will automatically be replaced by dynamic subscription information and will display all forms and messages as necessary.
8. In the WordPress "Settings" area for Subscribe2 select the page name in the "Appearance" section that of the WordPress page created in step 7.

MANUAL INSTALLATION

1. Copy the entire /subscribe2/ directory into your /wp-content/plugins/ directory.
2. Activate the plugin.
3. Visit the "Subscribe2 -> Settings" menu.
4. Configure the options to taste, including the email template and any categories which should be excluded from notification
5. Visit the "Subscribe2 -> Subscribers" menu.
6. Manually subscribe people as you see fit.
7. Create a [WordPress Page](http://codex.wordpress.org/Pages) to display the subscription form.  When creating the page, you may click the "S2" button on the QuickBar to automatically insert the Subscribe2 token.  Or, if you prefer, you may manually insert the Subscribe2 shortcode or token: [subscribe2] or the HTML invisible `<!--subscribe2-->` ***Ensure the token is on a line by itself and that it has a blank line above and below.***
This token will automatically be replaced by dynamic subscription information and will display all forms and messages as necessary.
8. In the WordPress "Settings" area for Subscribe2 select the page name in the "Appearance" section that of the WordPress page created in step 7.

== Frequently Asked Questions ==
= I want HTML email to be the default email type =
You need to pay for the [Subscribe2 HTML version](http://semperplugins.com/plugins/subscribe2-html/).

= Where can I get help? =
So, you've downloaded the plugin an it isn't doing what you expect. First you should read the included documentation. There is a [ReadMe.txt](http://plugins.svn.wordpress.org/subscribe2/trunk/ReadMe.txt) file and a [legacy PDF startup guide](http://plugins.svn.wordpress.org/subscribe2/tags/6.0/The%20WordPress%20Subscriber%20User%20Guide.pdf) installed with the plugin.

Next you could search in the [WordPress forums](http://wordpress.org/support/), the old [Subscribe2 Forum](http://getsatisfaction.com/subscribe2/), or the [Subscribe2 blog FAQs](http://subscribe2.wordpress.com/category/faq/).

If you can't find an answer then post a new topic at the [WordPress forums](http://wordpress.org/support/) or make a [donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2387904) to get my attention!

= Where can I get translation files for Subscribe2, and what do I do with them? =
All of the translation files I have been given are available at [http://plugins.trac.wordpress.org/browser/subscribe2/i18n/](http://plugins.trac.wordpress.org/browser/subscribe2/i18n/).

You need to download the *.mo file and place it on your server either in a folder called languages/ in the wp-content/plugins/subscribe2/ folder or you can place it in the subscribe2/ folder.

= What if there isn't a translation file for my language? =
If your language isn't list then you will need to make your own translation by use the *.pot file the is distributed with every release of the code. [This](http://codex.wordpress.org/Translating_WordPress) WordPress Codex page gives more background on how you make translation files.

= Sending post notifications or email with Subscribe2 =
Subscribe2 sends an email at the very moment the post is published. Since Subscribe2 sends live mail with no un-do, it's important to use the Preview function in WordPress to make sure the post has been edited to perfection *before* moving it from Draft to Published mode.

Mail is sent when a post is published - it will not be re-sent if you Update the post later. If you need to send a mailing a second time (e.g. during testing), switch the post to Draft mode, then re-publish it.

You can also manually send emails to groups of your subscribers using the Send Email page that the plugin creates in the WordPress administration area.

= Where can I find the HTML and CSS templates? =
While the template field in Settings | Subscribe2 does not display HTML by default, feel free to add HTML to it as needed. You can insert references to static images for use as banners, wrap sections of the template in divs or other elements, or do whatever you like.

There is no need to include HTML header data or body tags - just focus on the HTML content, in conjunction with the template tags documented on the settings page.

Subscribe2 does not maintain a separate stylesheet for the emails it generates. Instead, it uses the CSS of your currently active WordPress theme. If you need new/custom styles specific to your newsletter that aren't included in your theme stylesheet, try adding elements such as div id="newsletter_sidebar" to your HTML, with corresponding #newsletter_sidebar rules in your stylesheet.

Note that if you ever change your site theme, you'll need to copy these additions over to the new theme's stylesheet. To avoid this problem, consider placing a custom CSS file on your server outside of your theme directory, and link to it from the template, thus overriding the active theme styles permanently.

= Some or all email notifications fail to send, why?  =
In the first instance **check this with your hosting provider**, they have access to your server logs and will be able to tell you where and why emails are being blocked.

This is by far the most common question I am asked and the most frequent issue that arises. Without fail it is always down to a server side limitation or restriction.

These restrictions broadly fall into one of three areas. These are the sender details, the header details and restrictions on the number of messages sent.

**Sender Details**. You may need to ensure that the email notification is being sent from an email address on the same domain as your blog. So, if your blog is http://www.example.com the email should be something like admin@example.com. To do this go to Subscribe2->Settings and carefully select from the dropdown list where is says "Send Email From". Here you will see "Post Author", then the name of your blog and then the names of your administrator level users. It may be wise to set up a dummy user account specifically to send the emails from and make sure you give that account an on domain email address.

**Header Details**. Some hosting providers place a restriction on the maximum number of recipients in any one email message.  Some hosts simply block all emails on certain low-cost hosting plans.

Subscribe2 provides a facility to work around a restriction of the maximum number of recipients per email by sending batches of emails.  To enable this feature, go to Subscribe2->Settings and locate the setting to restrict the number of recipients per email. If this is set to 30 then each outgoing email notification will only contain addresses for 30 recipients.

Reminder: Because Subscribe2 places all recipients in BCC fields, and places the blog admin in the TO field, the blog admin will receive one email per batched delivery. So if you have 90 subscribers, the blog admin should receive three post notification emails, one for each set of 30 BCC recipients.

Batches will occur for each group of message as described above.  A site like this with many public and registered subscribers could conceivably generate a lot of email for your own inbox.

**Restrictions on the number of messages sent**. In order to combat spam many hosts are now implementing time based limitations. This means you are only allowed to send a certain number of messages per unit time, 500 per hour for example. Subscribe2 does not have a work around for this inbuilt but see the next question.

= My host has a limit of X emails per hour / day, can I limit the way Subscribe2 sends emails? =
This is the second most common question I get asked (the first being about emails not being sent which quote often ends up here anyway!). This is more commonly called 'throttling' or 'choking'. PHP is a scripting language and while it is technically possible to throttle emails using script it is not very efficient. It is much better in terms of speed and server overhead (CPU cycles and RAM) to throttle using a server side application.

In the first instance you should try to solve the problem by speaking to your hosting provider about changing the restrictions, move to a less restricting hosting package or change hosting providers.

If the above has not put you off then I spent some time writing a Mail Queue script for Subscribe2 that adds the mails to a database table and sends then in periodic batches. It is available, at a price, [here](http://semperplugins.com/plugins/wordpress-mail-queue-wpmq/).

= My Digest emails fail to send, why? =
If you have already worked through all of the above email trouble shooting tips, and you are still not seeing your periodic digest emails send there may be an issue with the WordPress pseudo-cron functions on your server.

The pseudo-cron is WordPress is named after the cron jobs on servers. These are tasks that are run periodically to automate certain functions. In WordPress these tasks include checking for core and plugin updates, publishing scheduled posts and in the case of Subscribe2 sending the digest email. so, if the psuedo-cron is not working the email won't send.

some reasons why your pseudo-cron may not be working are explained [here](http://wordpress.org/support/topic/296236#post-1175405). You can also try overcoming these by calling the wp-cron.php file directly and there are even [instructions](http://www.satollo.net/how-to-make-the-wordpress-cron-work) about how to set up a server cron job to do this periodically to restore WordPress pseudo-cron to a working state.

= I'd like to send the Digest email but on a different interval to the ones listed. Is this possible? =
Yes, this is possible, it just requires a little bit of code. Subscribe2 uses the intervals that are currently defined in WordPress (and by any plugins that create additional intervals), so all you need to do is add to the available intervals. Use code like this and simply change the interval time (in seconds) and description.

`function add_my_new_sched($sched) {
	$sched['my_new'] = array('interval' => 2419200, 'display' => 'Four Weekly');
	return $sched;
}
add_filter('cron_schedules', 'add_my_new_sched');`

= I'd like to change the size of the image inserted by the {IMAGE} keyword in the paid version of the code. Is this possible? =
Yes, this is possible, it just requires a little bit of code. Subscribe2 introduced a filter in version 8.6 that allows on-the-fly customisation of the image size. Use the code below in a plugin of your own.

`function my_s2_image_size() {
	// return a pre-defined size like 'thumbnail' or 'full'
	// or return a physical size as an array like array(300, 300) or array(150, 150)

	// examples:
	return 'thumbnail';
	return 'full'
	return array(300,300);
}

add_filter('s2_image_size', 'my_s2_image_size');`

= When I click on Send Preview in Susbcribe2->Settings I get 4 emails, why =
Subscribe2 supports 4 potential email formats for Susbcribers so you will get a preview for each of the different possibilities.

= Why do I need to create a WordPress Page =
Subscribe2 uses a filter system to display dynamic output to your readers. The token may result in the display of the subscription form, a subscription message, confirmation that an email has been sent, a prompt to log in. This information needs a static location for the output of the filter and a WordPress page is the ideal place for this to happen.

If you decide to use Subscribe2 only using the widget you must still have at least one WordPress page on your site for Subscribe2 to work correctly.

= Why is my admin address getting emails from Subscribe2? =
This plugin sends emails to your subscribers using the BCC (Blind Carbon Copy) header in email messages. Each email is sent TO: the admin address. There may be emails for a plain text excerpt notification, plain text full text and HTML format emails and additionally if the number of recipients per email has been set due to hosting restrictions duplicate copies of these emails will be sent to the admin address.

= I can't find my subscribers / the options / something else =
Subscribe2 creates four (4) new admin menus in the back end of WordPress. These are all under the top level menu header **Subscribe2**.

* Your Subscriptions : Allows the currently logged in user to manage their own subscriptions
* Subscribers : Allows you to manually (un)subscribe users by email address, displays lists of currently subscribed users and allows you to bulk subscribe Registered Users
* Settings : Allows administrator level users to control many aspects of the plugins operation. It should be pretty self explanatory from the notes on the screen
* Send Mail : Allows users with Publish capabilities to send emails to your current subscribers

**Note:** In versions of the plugin prior to version 7.0 the menus are under the WordPress system at Posts -> Mail Subscribers, Tools -> Subscribers, Users -> Subscriptions and Settings -> Subscribe2.

= I'm confused, what are all the different types of subscriber? =
There are basically only 2 types of subscriber. Public Subscribers and Registered Subscribers.

Public Subscribers have provided their email address for email notification of your new posts. When they enter there address on your site they are sent an email asking them to confirm their request and added to a list of Unconfirmed Subscribers. Once they complete their request by clicking on the link in their email they will become Confirmed Subscribers. They will receive a limited email notification when new post is made or periodically (unless that post is assigned to one of the excluded categories you defined).  These Public Subscribers will receive a plaintext email with an excerpt of the post: either the excerpt you created when making the post, the portion of text before a <!--more--> tag (if present), or the first 50 words or so of the post.

Registered Users have registered with your WordPress blog (provided you have enabled this in the core WordPress settings). Registered users of the blog can elect to receive email notifications for specific categories (unless Digest email are select, then it is an opt in or out decision).  The Subscribe2->Your Subscriptions menu item will also allow them greater control to select the delivery format (plaintext or HTML), amount of message (excerpt or full post), and the categories to which they want to subscribe.  You, the blog owner, have the option (Subscribe2->Settings) to allow registered users to subscribe to your excluded categories or not.

**Note** You can send HTML emails to Public Subscribers with the paid [Subscribe2 HTML version](http://semperplugins.com/plugins/subscribe2-html/) of the plugin.

= Can I put the form elsewhere? (header, footer, sidebar without the widget) =
The simple answer is yes you can but this is not supported so you need to figure out any problems that are caused by doing this on your own. Read <a href="http://subscribe2.wordpress.com/2006/09/19/sidebar-without-a-widget/">here</a> for the basic approach.

= I'd like to be able to collect more information from users when they subscribe, can I? =
Get them to register with your blog rather than using the Subscribe2 form. Additional fields would require much more intensive form processing, checking and entry into the database and since you won't then be able to easily use this information to personalise emails there really isn't any point in collecting this data.

= How do I use the Subscribe2 shortcode? =
In version 6.1 of Subscribe2 the new standard WordPress shortcode [subscribe2] was introduced. By default, it behaves same as old Subscribe2 token, `<--subscribe2-->`, which means that it will show the same Subscribe2 output in your chosen page in WordPress or in the Widget.

But it also has advanced options, which are related to form. The default form contains two buttons for subscribing and unsubscribing. You may, for example, only want form that handles unsubscribing, so the shortcode accepts a **hide** parameter to hide one of the buttons.

If you use the shortcode [subscribe2 hide="subscribe"] then the button for subscribing will be hidden and similarly if you use [subscribe2 hide="unsubscribe"], only button for subscribing will be shown.

The new shortcode also accepts two further attributes, these are **id** and **url**. To understand these parameters you need to understand that Subscribe2 returns a user to the default WordPress Page on your site where you use the shortcode or token however in some circumstances you may want to override this behaviour. If you specify a WordPress page id using the id parameter or a full URL using the url parameter then the user would be returned to the alternative page.

There are many scenarios in which to use new options, but here is an example:

* Two separate WordPress pages, "Subscribe" that shows only Subscribe button, and "Unsubscribe", that shows only Unsubscribe button. Both pages also have text that should help users in use of form.
* In the widget, show only Subscribe button and post form content to page "Subscribe"
* In the Subscribe2 email template for new post, add text "You can unsubscribe on a following page:" which is followed with link to "Unsubscribe" page

= I can't find or insert the Subscribe2 token or shortcode, help! =
If, for some reason the Subscribe2 button does not appear in your browser window try refreshing your browser and cache (Shift and Reload in Firefox). If this still fails then insert the token manually. In the Rich Text Editor (TinyMCE) make sure you switch to the "code" view and type in [subscribe2] or <!--subscribe2-->.

= My digest email didn't send, how can I resend it? =
If for some reason you wish to resend the last digest email you should find a Resend Digest button on the Subscribe2->Settings page under the Email Settings tab. If the button is not there then you are either in per-post mode or there isn't a previous digest to re-send.

In per post mode, to resend edit the post you want to re-send and change the status to draft and save, then publish the post again.

= I would really like Registered users to have the Subscription page themed like my site, is this possible? =
Yes, it is. There is a small extension to Subscribe2 that delivers exactly this functionality. It is available from [Theme Tailors](http://stiofan.themetailors.com/store/products/tt-subscribe2-front-end-plugin/) for just $5.

= I'd like to change the length of the excerpt included in the email notification. Can I do that? =
Yes, you can. There is a filter in Subscribe2 that allow you to change from the default of approximately 55 words. An example of the filter code you need would look like this:

`function my_excerpt() {
	// return whatever number of words you want the excerpt length to be
	return 30;
}
add_filter('s2_excerpt_length', 'my_excerpt');`

= How do I make use of the support for Custom Post Types =
In a plugin file for your site or perhaps functions.php in your theme add the following code where 'my_post_type' is change to the name of your custom post type.

`function my_post_types($types) {
	$types[] = 'my_post_type';
	return $types;
}
add_filter('s2_post_types', 'my_post_types');`

= How can I make use of the support for Custom Taxonomies =
In a plugin file for your site or perhaps functions.php in your theme add the following code where 'my_
taxonomy_type' is change to the name of your custom taxonomy type.

`function my_taxonomy_types($taxonomies) {
	$taxonomies[] = 'my_taxonomy_type';
	return $taxonomies;
}
add_filter('s2_taxonomies', 'my_taxonomy_types');`

= I want to personalise the message displayed when someone subscribes or unsubscribes, how do I do that? =
There is a filter for both of these in Subscribe2 from version 9.0 and upwards. To use it you need to create a little filter code plugin, an example is below:

`function subscribe_change($message) {
	$message .= "<p>A warm welcome to our blog. We hope you enjoy our emails.</p>";
	return $message;
}
add_filter('s2_subscribe_confirmed', 'subscribe_change');

function unsubscribe_change($message) {
	$message .= "<p>We're sorry to see you leave, come back anytime.</p>";
	return $message;
}
add_filter('s2_unsubscribe_confirmed', 'unsubscribe_change');`

= How do I make use of the new option to AJAXify the form? =
The first thing you will need to do is visit the options page and enable the AJAX setting where it says "Enable AJAX style subscription form?", this will load the necessary javascript onto your WordPress site.

Next you need to decide if you want the link to be on a WordPress page or in your Sidebar with the Widget.

For a WordPress page you use the normal Subscribe2 token but add a 'link' parameter with the text you'd like your users to click, so something like:

`[subscribe2 link="Click Here to Subscribe"]`

For Sidebar users, visit the Widgets page and look in the Subscribe2 Widget, there is a new option at the bottom called "Show as link". If you choose this a link will be placed in your sidebar that displays the form when clicked.

In either case, if your end users have javascript disabled in their browser the link will sinply take them through to the subscription page you are recommended to create at step 7 of the install instructions.

The final thing to mention is the styling of the form. The CSS taken from the jQuery-UI libraries and there are several to choose from. I quite link darkness-ui and that is the styling used by default. But what if you want to change this?

Well, you need to write a little code and provide a link to the Google API or Microsoft CDN hosted CSS theme you prefer. The example below changes the theme from ui-darkness to ui-lightness. More choice are detailed on the [jQuery release blog](http://blog.jqueryui.com/2011/08/jquery-ui-1-8-16/) where the them names are listed and linked to the address you'll need.

`function custom_ajax_css() {
	return "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/ui-lightness/jquery-ui.css";
}
add_filter('s2_jqueryui_css', 'custom_ajax_css');`

= I want to change the kinds of users who can access the Subscribe2 menus. Is that possible? =
Yes, it is possible with a little bit for code either in a custom plugin or your functions.php file in your theme. You use the add_filter() command that is part of WordPress to change the [capability](http://codex.wordpress.org/Roles_and_Capabilities#Capabilities) that allows access to each of the Subscribe2 menus.

`function s2_admin_changes( $capability, $menu ) {
	// $capability is the core WordPress capability to allow admin page access
	// $menu is the title of the page:
	//	'user' for access to personal subscription settings
	//	'manage' to allow access to the user management screen
	//	'settings' to allow access to the plugin settings
	//	'send' for access to the Send Email page

	// identify the menu you are changing capability for first
	// then return a new capability
	if ( $menu == 'send' ) {
		return 'read';
	}

	return $capability;
}

add_filter('s2_capability', 's2_admin_changes', 10, 2);`

= I want to change the Administrator users that get notifications of new subscriptions and unsubscriptions, how do I do that? =
In Subscribe2->Settings you can turn off email notifications to Administrator level users when a Public Subscriber joins or leaves but what if you still want an email but to different people? Subscribe2 has a filter that allows you to add and remove users immediately before sending like this:

`function my_admin_filter($recipients = array(), $email) {
	// $recipients is an array of admin email addresses
	// $email will be 'subscribe' or 'unsubscribe'
	if ($email == 'subscribe') {
		foreach ($recipients as $key => $email) {
			if ( $email == 'admin@mysite.com') {
				unset($recipients[$key]);
			}
		}
		$recipients[] = 'different.user@mysite.com';
	}
	return $recipients;
}
add_filter('s2_admin_email', 'my_admin_filter', 10, 2);`

= I want to change the email subject, how do I do that? =
You can change the email subject with the 's2_email_subject' filter. Something like this:

`function s2_subject_changes($subject) {
	return "This is my preferred email subject";
}

add_filter('s2_email_subject', 's2_subject_changes');`

= Can I suggest you add X as a feature =
I'm open to suggestions but since the software is written by me for use on my site and then shared for free because others may find it useful as it comes don't expect your suggestion to be implemented unless I'll find it useful.

= I'd like to be able to send my subscribers notifications in HTML =
By default Public Subscribers get plain text emails and only Registered Subscribers can opt to receive email in HTML format. If you really want HTML for all you need to pay for the [Subscribe2 HTML version](http://semperplugins.com/plugins/subscribe2-html/).

= Which version should I be using, I'm on WordPress x.x.x? =
WordPress 3.1 and up requires Subscribe2 from the 7.x or 8.x stable branch. The most recent version is hosted via [Wordpress.org](http://wordpress.org/extend/plugins/subscribe2/).

WordPress 2.8 and up requires Subscribe2 from the 6.x stable branch. The most recent version is [6.5](http://downloads.wordpress.org/plugin/subscribe2.6.5.zip).

WordPress 2.3.x through to 2.7.x require Subscribe2 from the 4.x or 5.x stable branch. The most recent version is [5.9](http://downloads.wordpress.org/plugin/subscribe2.5.9.zip).

WordPress 2.1.x and 2.2.x require Subscribe2 from the 3.x stable branch. The most recent version is [3.8](http://downloads.wordpress.org/plugin/subscribe2.3.8.zip).

WordPress 2.0.x requires Subscribe2 from the 2.x stable branch. The most recent version is [2.22](http://downloads.wordpress.org/plugin/subscribe2.2.22.zip).

= Why doesn't the form appear in my WordPress page? =
This is usually caused by one of two things. Firstly, it is possible that the form is there but because you haven't logged out of WordPress yourself you are seeing a message about managing your profile instead. Log out of WordPress and it will appear as the subscription form you are probably expecting.

Secondly, make sure that the token ([subscribe2] or <!--subscribe2-->) is correctly entered in your page with a blank line above and below. The easiest way to do this is to deactivate the plugin, visit your WordPress page and view the source. The token should be contained in the source code of the page. If it is not there you either have not correctly entered the token or you have another plugin that is stripping the token from the page code.

== Screenshots ==
1. The Subscribe2->Mail Subscribers admin page generated by the plugin.
2. The Subscribe2->Subscribers admin page generated by the plugin.
3. The Subscribe2->Subscriptions admin page generated by the plugin.
4. The Subscribe2->Subscribe2 admin page generated by the plugin.

== Changelog ==
See [ChangeLog.txt](http://plugins.svn.wordpress.org/subscribe2/trunk/ChangeLog.txt)

== Upgrade Notice ==
See Version History