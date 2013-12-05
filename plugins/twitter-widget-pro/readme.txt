=== Twitter Widget Pro ===
Contributors: aaroncampbell, PeteMall, range, saracannon
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9993090
Tags: twitter, widget, feed
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 2.6.0

A widget that properly handles twitter feeds, including parsing @username, #hashtags, and URLs into links.

== Description ==

A widget that properly handles twitter feeds, including parsing @username, #hashtag, and URLs into links.  It supports displaying profiles images, and even lets you control whether to display the time and date of a tweet or how log ago it happened (about 5 hours ago, etc).

<a href="http://ran.ge/wordpress-plugin/twitter-widget-pro/">Twitter Widget Pro</a>
Brought to you by <a href="http://ran.ge/" title="WordPress Development">Ran.ge</a>

== Installation ==

1. Use automatic installer to install and active the plugin.
1. You should see a notice appear in your admin that links you to the settings page.
1. Follow the instructions to setup your Twitter app and authenticate your account (an unfortunate step made necessary by Twitter's API changes).
1. In WordPress admin go to 'Appearance' -> 'Widgets' and add "Twitter Widget Pro" to one of your widget-ready areas of your site

== Frequently Asked Questions ==

= Can I follow more than one feed? =

Absolutely, each instance of the widget can have different settings and track different feeds.

= I get an error similar to "Parse error: syntax error, unexpected T_STRING, expecting T_OLD_FUNCTION or T_FUNCTION or T_VAR or '}' in /.../wp-twitter-widget.php on line ##" when I try to activate the plugin.  Is your plugin broke? =

No.  This error occurs because the plugin requires PHP 5 and you're running PHP 4. Most hosts offer PHP5 but sometimes you have to enable it in your control panel, through .htaccess, or by asking them.  There may be instructions for your specific host in the <a href="http://codex.wordpress.org/Switching_to_PHP5">Switching to PHP5</a> article in the codex.

= How can I add this widget to a post or page? =

You can now use the twitter-widget shortcode to embed this widget into a post or
page.  The simplest form of this would be `[twitter-widget username="yourTwitterUsername"]`

= How exactly do you use the twitter-widget shortcode? =
The simplest form of this would be `[twitter-widget username="yourTwitterUsername"]`
However, there are more things you can control.

* username - A Twitter username to pull a feed of Tweets from.  The user needs to be authenticated.
* list - A Twitter list id owned by one of the users you've authenticated.
* before_widget - This is inserted before the widget.
* after_widget - This is inserted after the widget, and is often used to close tags opened in before_widget
* before_title - This is inserted before the title and defults to <h2>
* after_title - This is inserted after the title and defults to </h2>
* errmsg - This is the error message that displays if there's a problem connecting to Twitter
* hiderss - set to true to hide the RSS icon (defaults to false)
* hidereplies - set to true to hide @replies that are sent from the account (defaults to false)
* hidefrom - set to true to hide the "from ____" link that shows the application the tweet was sent from (defaults to false)
* avatar - set to one of the available sizes (mini, normal, bigger, or original) (defaults to none)
* targetBlank - set to true to have all links open in a new window (defaults to false)
* showXavisysLink - set to true to display a link to the Twitter Widget Pro page.  We greatly appreciate your support in linking to this page so others can find this useful plugin too!  (defaults to false)
* items - The number of items to display (defaults to 10)
* fetchTimeOut - The number of seconds to wait for a response from Twitter (defaults to 2)
* showts - Number of seconds old a tweet has to be to show ___ ago rather than a date/time (defaults to 86400 seconds which is 24 hours)
* dateFormat - The format for dates (defaults to'h:i:s A F d, Y' or it's localization)
* title - The title of the widget (defaults to 'Twitter: Username')
* showretweets - set to true to show retweets, false to hide them (defaults to true)
* showintents - set to true to show the reply, retweet, and favorite links for each tweet, false to hide them (defaults to true)
* showfollow - set to true to show the follow button after tweets, false to hide it (defaults to true)

You can see these put into action by trying something like:

* `[twitter-widget username="wpinformer" before_widget="<div class='half-box'>" after_widget="</div>" before_title="<h1>" after_title="</h1>" errmsg="Uh oh!" hiderss="true" hidereplies="true" targetBlank="true" avatar="1" showXavisysLink="1" items="3" showts="60"]Your Title[/twitter-widget]`
* `[twitter-widget username="wpinformer" before_widget="<div class='half-box'>" after_widget="</div>" before_title="<h1>" after_title="</h1>" errmsg="Uh oh!" hiderss="true" hidereplies="true" targetBlank="true" avatar="1" showXavisysLink="1" items="3" showts="60" title="Your Title"]`
* `[twitter-widget username="wpinformer"]`

= How can I style it to look nicer? =

There are plenty of CSS classes throughout the HTML that is generated, and you can use those to style things.  Here is some sample CSS that I use with the <a href="http://essencetheme.com" title="Essence Theme for WordPress">Essence Theme</a>.  You'll need to get the "Everything" sprite from <a href="https://dev.twitter.com/docs/image-resources">Twitter's Image Resources</a>.
`
.widget_twitter div {
	padding:0;
}

.widget_twitter ul li {
	margin-bottom:5px;
}

.widget_twitter .follow-button,
.widget_twitter .xavisys-link {
	margin:0 10px 10px 25px;
}

.widget_twitter .entry-meta {
	display:block;
	font-size:80%;
}

.widget_twitter .intent-meta a {
	background: url(images/everything-spritev2.png); /** from Twitter ressources */
	display: inline-block;
	height: 16px;
	text-indent: -9999px;
	width: 16px;
}
.widget_twitter .intent-meta a.in-reply-to {
	background-position: 0 center;
}
.widget_twitter .intent-meta a:hover.in-reply-to {
	background-position: -16px center;
}
.widget_twitter .intent-meta a.favorite {
	background-position: -32px center;
}
.widget_twitter .intent-meta a:hover.favorite {
	background-position: -48px center;
}
.widget_twitter .intent-meta a.retweet {
	background-position: -80px center;
}
.widget_twitter .intent-meta a:hover.retweet {
	background-position: -96px center;
}
`

= Why can't I display a friends feed anymore? =

Aparently the database queries required to display the friends feed was causing twitter to crash, so they removed it.  Unfortunately, this is outside my control.

== Screenshots ==

1. To use the widget, go to Appearance -> Widgets and Add "Twitter Widget Pro" widget.
2. Each widget has settings that need to be set, so the next step is to click the down arrow on the right of the newly added widget and adjust all the settings.  When you're done click "Save"
3. This is what the widget looks like in the default theme with no added styles.
4. By using some (X)HTML in the title element and adding a few styles and a background image, you could make it look like this.

== Upgrade Notice ==

= 2.6.0 =
Fix lists and add ability to remove accounts, as well as plenty of minor bug fixes.

= 2.5.4 =
Minor fix for a strict PHP notice

= 2.5.3 =
Another SSL verification fix and add Twitter List support to shortcodes

= 2.5.2 =
Fixes hashtag links, ignoring of retweets, and SSL verification

= 2.5.1 =
Fix potential conflict with other oAuth plugins

= 2.5.0 =
Upgrade to the new Twitter API and add support for lists

== Changelog ==

= 2.6.0 =
* Add ability to remove accounts
* Fix bug that made lists not work
* Work around PHP bug in json_decode() - https://bugs.php.net/bug.php?id=45989
* Fix some strict standards warnings

= 2.5.4 =
* Minor fix for a strict PHP notice

= 2.5.3 =
* Don't force verification of Twitter's SSL certificate
* Add support for Twitter Lists in shortcodes

= 2.5.2 =
* Don't force verification of Twitter's SSL certificate
* Fix empty admin notice
* Fix bug with retweets not being ignored
* Fix hashtag links

= 2.5.1 =
* Rename OAuthUtil class to twpOAuthUtil
* Don't use 'account/verify_credentials' as it has a very low limit

= 2.5.0 =
* Use the new Twitter API with oAuth
* Adds support for lists
* No longer supports non-https Twitter requests (Twitter API requirement)

= 2.4.1 =
* Make sure hashtags are parsed UTF-8
* Add German translation

= 2.4.0 =
* Better linking of URLs, especially handling of punctuation at end of URLs
* Support for extended characters in hashtags
* Allow externally linked images to serve via ssl
* More efficient regex for link parsing
* Link FTP links
* Link E-Mail addresses
* Complete conversion to Range plugin framework from Xavisys framework
* Added place to check API usage

= 2.3.11 =
* Fix issue with "Include Retweets" not unchecking

= 2.3.10 =
* Update feeds and links to Ran.ge
* Move assets to assets dir
* Pull in some Range branding to replace Xavisys branding

= 2.3.9 =
* Display dates based on your WordPress timezone setting

= 2.3.8 =
* Pass $tweet to widget_twitter_content filter
* Fix issue with mixed content warning on HTTPS pages

= 2.3.7 =
* Add option to clear TLC update locks

= 2.3.6 =
* Make sure we didn't get a string from json_decode()

= 2.3.5 =
* Added a setting to allow using the Twtter API via HTTP or HTTPS
* Fixed support forum links
* Small alignment adjustment on settings page

= 2.3.4 =
* Added an Arabic translation - Thanks Yaser Maadan - http://www.englize.com
* Fix conflict with Jetpack by disabling their Twitter widget

= 2.3.3 =
* Updated TLC to fix issue where updates could get stuck "locked"
* Add data-lang support for Twitter intents
* Updated Italian translation - Thanks Gianni Diurno - http://gidibao.net

= 2.3.2 =
* Fix some strings that were not translatable
* New Italian translation Thanks to Gianni Diurno - http://gidibao.net

= 2.3.1 =
* Fixes issue with saving a couple of the settings (tweet intents and follow button)

= 2.3.0 =
* Integrate Mark Jaquith's TLC Transients https://gist.github.com/1149945 which lets us only get rid of old tweets once we have successfully received new ones.
* Add Twitter Web Intents (reply, retweet, favorite)
* Add option for Twitter Follow button
* Fix issues with saving avatar sizes
* Remove "Hide RSS Icon and Link" option - Decisions not options.
* Removed link from title.  Use the follow button option or if you have unfiltered_html privileges you can enter the HTML link as the title.

= 2.2.4 =
* Use the new API URLs
* Use the new "hide replies" option in the Twitter API
* Add support to show retweets
* Add support for multiple profile image sizes

= 2.2.3 =
* The Twitter-pocolypse hit a while back but since so many servers are 64 bit it wasn't an issue.  This fixes links for users on 32 bit servers.
* Fix some notices thrown by translated strings

= 2.2.2 =
* Fix issue introduced in 2.2.1 with an incorrect function name '_hashtagLink' (sorry about that)

= 2.2.1 =
* Add missing space between "from" and "in reply to"
* Add the ability to remove the "from" links from displaying - Thanks to <a href="http://yoast.com/">Joost de Valk</a> for the request and the patch!

= 2.2.0 =
* Now uses the Xavisys WordPress Plugin Framework - http://bluedogwebservices.com/xavisys-wordpress-plugin-framework/
* Added an options page where you can set defaults that apply to widgets, shortcodes, and php calls (everything can be overridden)
* Added the Xavisys feed to the dashboard (which can be hidden using the screen options)
* Fixed problem for people with certain WP configs and PHP 5.0-5.1.x that caused the Services_JSON class to get included twice.
* Fixed issue with urls in the form www.site.com

= 2.1.4 =
* Mixed-case attributes now work properly in the shortcode
* Fixed issue with matching only @ as a username

= 2.1.3 =
* Fixed extraneous closing tag that caused invalid HTML
* You can now specify your own date format as a per-widget option

= 2.1.2 =
* Fixed spacing issue that was introduced in 2.1.1
* Added links to the Support Forum

= 2.1.1 =
* Added an option to open links in new windows

= 2.1.0 =
* Added a shortcode to allow you to embed a widget in any post or page

= 2.0.5 =
* Remove the settings link from the plugin line on the plugins page
* Add a link to manage widgets to the plugin line on the plugins page
* Make date string translatable

= 2.0.4 =
* Added twitterwidget-title and twitterwidget-rss classes to the title and rss links for separate styling
* Removed the optional anonymous statistics collection.  Nothing is ever collected anymore.

= 2.0.3 =
* Removed some whitespace that was messing up styling for some people

= 2.0.2 =
* Fixed Profile image error
* Added another FAQ (about PHP 5)

= 2.0.1 =
* Fixed problem with invalid actions introduced into 2.0.0

= 2.0.0 =
* Completely rewitten to use the new widget class introduced in WordPress 2.8
* Now uses the json class that will be included in WordPress 2.9 for anyone on PHP < 5.2
* The "Show Link to Twitter Widget Pro" is now off by default to comply with the latest decisions regarding the plugin repository

= 1.5.1 =
* Re-enables the caching that got inadvertantly disabled in 1.5.0.

= 1.5.0 =
* This is an attempt at catching an elusive error.  If you're getting an error referencing line 332, please try this new version.

= 1.4.9 =
* Fixed an uncaught exception that could occur starting in 1.4.8

= 1.4.8 =
* The HTML has been changed for displaying profile images.  If you show profile images, you may need to update your CSS accordingly
* Changed name of widget from "Twitter Feed" to "Twitter Widget Pro"
* Fixed issue with calculation of "time since" for tweets that were months old

= 1.4.7 =
* Properly registering settings, should fix problem with WPMU

= 1.4.6 =
* Added ability to hide @replies from your widget

= 1.4.5 =
* Switched to using date_i18n so dates are localized

= 1.4.4 =
* Added Danish translation - Props <a href="http://wordpress.blogos.dk/">Georg</a>
* Add ability to give more information on WHY you should upgrade on the plugins page
* Fixed PHP notice when you add a new copy of widget to your sidebar

= 1.4.3 =
* Added the text domain to some translatable strings that were missing it
* Added the Spanish translation thanks to Rafael Poveda <RaveN>!! (Really....thanks for being the first translator for this)

= 1.4.2 =
* Thanks to RaveN and Dries Arnold for pointing out that the "about # ____ ago" phrases weren't translatable

= 1.4.1 =
* Fixed some translatable strings
* Fixed readme text

= 1.4.0 =
* Make translatable
* Include POT file
* Remove JS submitted for for stats and use HTTP class instead

= 1.3.7 =
* Added some spans with classes to make styling to meta data easier

= 1.3.6 =
* Fixes issue with linking URLs containing a ~
* Removed some debugging stuff

= 1.3.5 =
* #Hashtags are now linked to twitter search

= 1.3.4 =
* Added convert_chars filter to the tweet text to properly handle special characters
* Fixed "in reply to" text which stopped working when Twitter changed their API

= 1.3.3 =
* Some configs still couldn't turn off the link to Twitter Widget Pro page

= 1.3.2 =
* Fixed problem with link to Twitter Widget Pro page not turning off

= 1.3.1 =
* Added error handling after wp_remote_request call
* Added link to Twitter Widget Pro page and option to turn it off per widget

= 1.3.0 =
* Updated to use HTTP class and phased out Snoopy
* No longer relies on user having a caching solution in place.  Caches for 5 minutes using blog options
* Allow HTML in title and error message if user can

= 1.2.2 =
* Fixed minor issue with Zend JSON Decoder
* Added an option for Twitter timeout.  2 seconds wasn't enough for some people

= 1.2.1 =
* Fixed some minor errors in the collection code
* Added the admin options page (how did that get missed?!?)

= 1.2.0 =
* Removed friends feed option, twitter removed this functionality
* Added an option to set your own message to display when twitter is down
* Added optional anonymous statistics collection

= 1.1.4 =
* Added an error if there was a problem connecting to Twitter.
* Added some text if there are no tweets.

= 1.1.3 =
* Fixed validation problems if source is a link containg an &

= 1.1.2 =
* Title link always links to correct username, rather than the last person to tweet on that feed
* Added option to hide RSS icon/link

= 1.1.1 =
* Fixed issue with @username parsing of two names with one space between them (@test @ing)
* Fixed readme typo

= 1.1.0 =
* Most major fix is the inclusion of json_decode.php for users that don't have json_decode() which was added in PHP 5.2.0
* Fixed problem with displaying a useless li when profile images aren't displayed on a single user widget
* Default title is now set to "Twitter: UserName"

= 1.0.0 =
* Released to wordpress.org repository

= 0.0.3 =
* Fixed some of the settings used with Snoopy
* Set a read timeout for fetching the files

= 0.0.2 =
* Changed some function names
* Moved form display to a separate function (_showForm)
* Now uses wp_parse_args to handle defaults
* Added comments
* Added seconds to the _timeSince function so you can have something like "about 25 seconds ago"

= 0.0.1 =
* Original Version
