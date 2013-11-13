<?php
$attributemap = array(

	// Generated Facebook Attributes
	'facebook_user'			=>	'eduPersonPrincipalName', // username OR uid @ facebook.com
	'facebook_targetedID'		=>	'eduPersonTargetedID', // http://facebook.com!uid
	'facebook_cn'			=>	'cn', // duplicate of displayName

	// Attributes Returned by Facebook
	'facebook.first_name'		=>	'givenName',
	'facebook.last_name'		=>	'sn',
	'facebook.name'			=>	'displayName', // or 'cn'
	'facebook.email'		=>	'mail',
	//'facebook.pic'			=>	'jpegPhoto', // URL not image data
	//'facebook.pic_square'			=>	'jpegPhoto', // URL not image data
	'facebook.username'		=>	'uid', // facebook username (maybe blank)
	//'facebook.uid'		=>	'uid', // numeric facebook user id
	'facebook.profile_url'		=>	'labeledURI',
	'facebook.locale'		=>	'preferredLanguage',
	'facebook.about_me'		=>	'description',
);
