=== Twitter posts to Blog ===
Contributors: badbreze,sforsberg
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QV5Y8ZNVWGEA8
Tags: twitter, autopost
Tested up to: 3.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin simply create posts in your blog from selected twitter searches.

Good work with this plugin.


== Description ==
= With this plugin you can stream tweets to your blog, it's simply to use =
* Go to the plugin settings menu "Twitter To WP" under "Dashboard"
* Configure the plugin options eg.:
* Capabilities: here you can select who can change settings of this plugin 
* Cron time: choose how much time must pass before load new items, use "never" to disable
* Publish status: Choose how the plugin create articles: published or draft
* Posts Tags: Type tags you want append to each tweet (dont use query strings here)
* Posts categories: Choose categories you want append to each tweet post
* Body images: Check if you want to insert images into body of the posts
* Body text: Check if you want to insert the tweet text into body of the posts
* Images size: deprecated, witing for feedback about the use of this image (the user avatar)
* Items at time: choose how much tweets want to import each time the cron run
* Max Title Length: because the title is the tweet text here you can choose the lenght of the title (truncate tweet text) (0 = no title)
* Post Modifications: here you can remove from tweets #hastags and/or @replies - Tanks to sforsberg
* Words blacklist: insert unwanted words to the blacklist (comma separated) tweets with choosed words will be ignored
* Your search queryes: here you can add or remove terms for tweets import, here the query samples

Built by <a href="//twitter.com/iwafer">@iWafer</a> / <a rel="author" href="https://plus.google.com/111606514487113936457">Damian Gomez</a>

For my projects see http://www.divenock.com/progetti/

= Limitations =
* Twitter api has some limitations, one of this is the last week limitation for Standard Search Api

= Examples To Finds tweets... =
* twitter search - containing both "twitter" and "search". This is the default operator
* "happy hour" - containing the exact phrase "happy hour"
* love OR hate - containing either "love" or "hate" (or both)
* beer -root - containing "beer" but not "root"
* #haiku - containing the hashtag "haiku"
* from:twitterapi - sent from the user @twitterapi
* to:twitterapi - sent to the user @twitterapi
* place:opentable:2 - about the place with OpenTable ID 2
* place:247f43d441defc03 - about the place with Twitter ID 247f43d441defc03
* @twitterapi - mentioning @twitterapi
* superhero since:2011-05-09 - containing "superhero" and sent since date "2011-05-09" (year-month-day).
* twitterapi until:2011-05-09 - containing "twitterapi" and sent before the date "2011-05-09".
* movie -scary :) - containing "movie", but not "scary", and with a positive attitude.
* flight :( - containing "flight" and with a negative attitude.
* traffic ? - containing "traffic" and asking a question.
* hilarious filter:links - containing "hilarious" and with a URL.
* news source:tweet_button - containing "news" and entered via the Tweet Button

= More? =
Want more functionality or some modifications? Ok tell me wath you want and i try to add or modify the plugin functions


== Installation ==
Copy the plugin into the WordPress directory ( wp-content/plugins/ )
Activate plugin from admin control panel

Create your twitter application here:
https://dev.twitter.com/apps/new

= Once you have created you need to pick tookens from the app panel and configure this plugin with these informations: =
* Consumer key
* Consumer secret

= And these (generated clicking (one time) on the button "Create my access token" on the application admin panel): =
* Access token
* Access token secret

All these data can be found on the application admin panel under the tab "OAuth tool".
If not configured correctly this plugin DOES NOT work.

This plugin create new menu under Settings ( Settings -> Twitter To WP )
Follow the description in order to configure the plugin


== Frequently Asked Questions ==

= I don't understand the utility of this plugin. Can you explain me what's mean? =

This plugin periodicaly pick tweets from tag,username or some text like this: 
https://twitter.com/search?q=%40iWafer&src=typd and for each tweet create one post in your blog.

= Where do I select the category,tags,content it will post to? =

In plugin settings page there is a tab called "Post customization" where you can choose those 
and more options for each tweet post this plugin publish

= Can i exclude tweets with unwanted words/authors? =

Yes you can exclude authors or words from the main configuration tab of the plugin

= How work the title/body formatting =

Formatting is easy to use, you can choose what you want to see in every post you publish, for egsample, you can set as title something 
like this:

`Tweet from %author%`

and the body content like this:

`<img src="%avatar_url%" alt="%author%"/> %author%: %tweet%. <a href="/">Back to Home</a>`

And your tweet result is something like:

`Tweet from iWafer
[IMAGE] iWafer: @divenock hello friend im the #best. Back to Home`

This is the list of codes you can use in the formatting fields

`%tweet% The text of the tweet
%author% The author name of this tweet
%avatar_url% An url to the author avatar for this tweet
%tweet_url% Url of this tweet in twitter.com
%tweet_images% An html block of all images in the tweet`

= How can i filter for tweets with images =

You can search tweets with images using this filter in your query string

`filter:images`

Or you can exclude images with the same filter but with a - in front of it
`-filter:images`

= How can i filter for tweets with Mentions or Replies =

You can search replies or mentions using these filters in your query string

`filter:mentions
filter:replies`

Or you can exclude replies with the same filters but with a - in front of it
`-filter:mentions
-filter:replies`

= Why my site doesn't grab images from twitter? = 
In some cases hosters have security setting in their configuration or firewalls or some other think like these, 
if you have problems with images in tweets the main problem maybe is the "allow_url_fopen" PHP setting, 
normaly you can request the activation of this function to enable the grabbing of images

= Can i intercept some actions on the plugin runtime? =
Here the list of actions with a small description

`dg_tw_before_images_placed`
This action runs before the insertion of the image (from twitter) in the post

`dg_tw_images_placed`
This action runs after the insertion of the image (from twitter) in the post (runs only if you choosed to insert the 
image as preview or as content

`dg_tw_after_post_published`
This action runs after the insertion of the post, the only parameter gived is the id of the post

= Can i customize posts without edit the plugin? =
Yes you can, usin filter you can edit some parts of the plugin engine without edit it, keeping the ability to 
update the plugin without lost any costomization made by you. Filters are listed here.

`dg_tw_before_post_tweet`
Allow to edit the post data before it be published, is and array with and array containing infomations for "wp_insert_post"

`dg_tw_the_author`
Filter the author name

`dg_tw_the_author_link`
Filter the html link to the author page on twitter

= Can i the id/author/avatar or query string of certain post in my template? =
All posts made by this plugin are created with some metas for those who want cusutomize their theme for tweets, 
here the collected post metas:

`dg_tw_query`
The query string used to pick up this post

`dg_tw_id`
The id of the tweet in this post

`dg_tw_author`
The author name of the tweet in this post

`dg_tw_author_avatar`
The avatar url of the author


== Screenshots ==

1. Configuration panel.
2. List items matching configuration and alow to publish manually.
3. Twitter app configuration panel


== Changelog ==

= What Next =
* Dont know, waiting featire request

= 1.11.20 =
* NEW: shortcode for date
* NEW: Customize date format

= 1.11.14 =
* FIX: Pick images from retweets (RonnyDee)
* NEW: Added actions to intercept runtime points
* NEW: Added some filters in the runtime

= 1.7.* =
* NEW: Image preview in retreive page
* NEW: Get images via curl (berendvaneerde)
* IMP: Layout of retreive page
* IMP: Layout for settings page
* FIX: Loop problem when update query strings
* FIX: Item limits now work correctly (bhaskarping)

= 1.6.* =
* FIX: Removed uneeded options, you can use filters in query string
* NEW: Added %tweet_images% shortcode for post content
* FIX: Revert times and some fix
* NEW: Tweet time in retreive page (danswhc)
* FIX: Plugin menu order set to AUTO
* NEW: Option to enable/disable featured image (jaja935)
* FIX: Css isues
* FIX: Regext fixed for some chars (ozdalgic)

= 1.5.* =
* New Backend design
* Users blacklist (acostanza)
* Manage twitter errors in manual posting page
* Fix title 0 length (Kiezkicker)
* Fix body and title format (Kiezkicker)
* Fix item limitation on import
* Add tweet url to formats (Kiezkicker)
* Fix some designs
* Fix target blank (thanks to Kiezkicker)

= 1.4.* =
* Post format support (BLUHIG)
* Manual import tweets (Ipstenu)
* Use P instead of SPAN to avoid some isues (Cibulka)
* Can remove url from the post title (yfbchelp)
* Custom post content formatting (Sweet)
* Use textarea instead of textbox for title and content formatting strings

= 1.4 =
* Tweet author as tag
* Timestamp in side of tweets (single post multiple tweets)
* Tweet link in body (thatothergirl)

= 1.3.* =
* Fix duplicates when you post multiple tweets in single post
* Cleared up various warning messages when indexes were missing 
* Fix the main loop take only the first query string
* Fix post meta with query string
* Check all fields in admin page
* Fix hashtags link (polle)
* Added taget blank (polle)
* Other fixes

= 1.3 =
* Single post with multiple tweets (pbassham)

= 1.2 =
* Custom title formatting (Sweet)
* Custom author
* Hashtags and Mentions clickable (thanks to Mike)

= 1.1.* =
* Bug fixes
* Special thanks to polle

= 1.1 =
* Tweet exclusion by retweet
* Bug fixes

= 1.0.* =
* Fix syntax error
* Bug fixes

= 1.0 =
* Twitter api 1.1 (fiuuuf this hard work make me satisfied)

= 0.6.4.* =
* fatal error fixed
* Updated changelog and added screenshots

= 0.6.4 =
* sforsberg: Thanks for "Post Modifications" filters

= 0.6.3.* =
* User from twitter fix
* Fix username and query in manual publishing
* Some fixes
* Twitter auhor links in the loop

= 0.6.3 =
* List next tweets
* Manual publishing of tweets from the list

= 0.6.2.* =
* Feedback request
* Readme updated
* Fix feedback request every time you save settings

= 0.6.2 =
* Some fix
* New menu position with icon
* Update capabilities because levels are deprecated
* Visual fixes

= 0.6.1 =
* Users Feedback:
* sllim99: Posts thumbnails if the tweet has images
* Umpqua: Posts urls as hyperlinks

= 0.6 =
* NO CHANGES, ONLY VERSION UPDATE

= 0.5.2 =
* Fix blacklist not filtering

= 0.5.1 =
* Small fix for query tags, thanks Rob Yardman

= 0.5 =
* Users Feedback:
* Rob Yardman: Tags for each query string
* Rob Yardman: Title length
* hazem: Words blacklist

= 0.4 =
* Post categories

= 0.3 =
* Formatting setting added.
* New backend interface
* More readable sorce code

= 0.2 =
* Removed debug code.

= 0.1 =
* Initial relase.

SELECT DISTINCT ID, post_title FROM wp_posts as p
INNER JOIN wp_term_relationships AS tr ON
(p.ID = tr.object_id AND
tr.term_taxonomy_id IN (3,5,6,7) )
INNER JOIN wp_term_taxonomy AS tt ON
(tr.term_taxonomy_id = tt.term_taxonomy_id AND
taxonomy = 'category');