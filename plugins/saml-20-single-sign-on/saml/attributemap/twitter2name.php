<?php
$attributemap = array(

	// Generated Twitter Attributes
	'twitter_screen_n_realm'	=>	'eduPersonPrincipalName', // screen_name@twitter.com
	//'twitter_at_screen_name'	=>	'eduPersonPrincipalName', // legacy @twitter format
	'twitter_targetedID'		=>	'eduPersonTargetedID', // http://twitter.com!id_str

	// Attributes Returned by Twitter
	'twitter.screen_name'		=>	'uid', // equivalent to twitter username without leading @
	//'twitter.id_str'		=>	'uid', // persistent numeric twitter user id
	'twitter.name'			=>	'displayName',
	'twitter.url'			=>	'labeledURI',
	'twitter.lang'			=>	'preferredLanguage',
	//'twitter.profile_image_url'	=>	'jpegPhoto',
	'twitter.description'		=>	'description',
);
