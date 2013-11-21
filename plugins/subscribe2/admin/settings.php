<?php
if ( !function_exists('add_action') ) {
	exit();
}

global $s2nonce, $wpdb, $wp_version, $current_tab;

// was anything POSTed?
if ( isset( $_POST['s2_admin']) ) {
	check_admin_referer('subscribe2-options_subscribers' . $s2nonce);
	if ( isset($_POST['reset']) ) {
		$this->reset();
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>$this->options_reset</strong></p></div>";
	} elseif ( isset($_POST['preview']) ) {
		global $user_email, $post;
		$this->preview_email = true;
		if ( 'never' == $this->subscribe2_options['email_freq'] ) {
			$posts = get_posts('numberposts=1');
			$post = $posts[0];
			$this->publish($post, $user_email);
		} else {
			$this->subscribe2_cron($user_email);
		}
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Preview message(s) sent to logged in user', 'subscribe2') . "</strong></p></div>";
	} elseif ( isset($_POST['resend']) ) {
		$status = $this->subscribe2_cron('', 'resend');
		if ( $status === false ) {
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('The Digest Notification email contained no post information. No email was sent', 'subscribe2') . "</strong></p></div>";
		} else {
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Attempt made to resend the Digest Notification email', 'subscribe2') . "</strong></p></div>";
		}
	} elseif ( isset($_POST['submit']) ) {
		foreach ($_POST as $key => $value) {
			if ( in_array($key, array('bcclimit', 's2page', 'entries')) ) {
				// numerical inputs fixed for old option names
				if ( is_numeric($_POST[$key]) && $_POST[$key] >= 0 ) {
					$this->subscribe2_options[$key] = (int)$_POST[$key];
				}
			} elseif ( in_array($key, array('show_meta', 'show_button', 'ajax', 'widget', 'counterwidget', 's2meta_default', 'reg_override')) ) {
				// check box entries
				( isset($_POST[$key]) && $_POST[$key] == '1' ) ? $newvalue = '1' : $newvalue = '0';
				$this->subscribe2_options[$key] = $newvalue;
			} elseif ( $key === 'appearance_users_tab' ) {
				$options = array('show_meta', 'show_button', 'ajax', 'widget', 'counterwidget', 's2meta_default');
				foreach ( $options as $option ) {
					if ( !isset($_POST[$option]) ) {
						$this->subscribe2_options[$option] = '';
					}
				}
			} elseif ( in_array($key, array('notification_subject', 'mailtext', 'confirm_subject', 'confirm_email', 'remind_subject', 'remind_email')) && !empty($_POST[$key]) ) {
				// email subject and body templates
				$this->subscribe2_options[$key] = $_POST[$key];
			} elseif ( in_array($key, array('compulsory', 'exclude', 'format')) ) {
				sort($_POST[$key]);
				$newvalue = implode(',', $_POST[$key]);

				if ($key === 'format') {
					$this->subscribe2_options['exclude_formats'] = $newvalue;
				} else {
					$this->subscribe2_options[$key] = $newvalue;
				}
			} elseif ( $key === 'registered_users_tab' ) {
				$options = array('compulsory', 'exclude', 'format', 'reg_override');
				foreach ( $options as $option ) {
					if ( !isset($_POST[$option]) ) {
						if ($option === 'format') {
							$this->subscribe2_options['exclude_formats'] = '';
						} else {
							$this->subscribe2_options[$option] = '';
						}
					}
				}
			} elseif ( $key === 'email_freq' ) {
				// send per-post or digest emails
				$email_freq = $_POST['email_freq'];
				$scheduled_time = wp_next_scheduled('s2_digest_cron');
				$timestamp_offset = get_option('gmt_offset') * 60 * 60;
				$crondate = (isset($_POST['crondate'])) ? $_POST['crondate'] : 0;
				$crontime = (isset($_POST['crondate'])) ? $_POST['crontime'] : 0;
				if ( $email_freq != $this->subscribe2_options['email_freq'] || $crondate != date_i18n(get_option('date_format'), $scheduled_time + $timestamp_offset) || $crontime != date('G', $scheduled_time + $timestamp_offset) ) {
					$this->subscribe2_options['email_freq'] = $email_freq;
					wp_clear_scheduled_hook('s2_digest_cron');
					$scheds = (array)wp_get_schedules();
					$interval = ( isset($scheds[$email_freq]['interval']) ) ? (int)$scheds[$email_freq]['interval'] : 0;
					if ( $interval == 0 ) {
						// if we are on per-post emails remove last_cron entry
						unset($this->subscribe2_options['last_s2cron']);
						unset($this->subscribe2_options['previous_s2cron']);
					} else {
						// if we are using digest schedule the event and prime last_cron as now
						$time = time() + $interval;
						$srttimestamp = strtotime($crondate) + ($crontime * 60 * 60);
						if ( $srttimestamp === false || $srttimestamp === 0 ) {
							$srttimestamp == time();
						}
						$timestamp = $srttimestamp - $timestamp_offset;
						while ($timestamp < time()) {
							// if we are trying to set the time in the past increment it forward
							// by the interval period until it is in the future
							$timestamp += $interval;
						}
						wp_schedule_event($timestamp, $email_freq, 's2_digest_cron');
						if ( !isset($this->subscribe2_options['last_s2cron']) ) {
							$this->subscribe2_options['last_s2cron'] = current_time('mysql');
						}
					}
				}
			} else {
				if ( isset($this->subscribe2_options[$key]) ) {
					$this->subscribe2_options[$key] = $_POST[$key];
				}
			}
		}

		echo "<div id=\"message\" class=\"updated fade\"><p><strong>$this->options_saved</strong></p></div>";
		update_option('subscribe2_options', $this->subscribe2_options);
	}
}

// send error message if no WordPress page exists
$sql = "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status='publish' LIMIT 1";
$id = $wpdb->get_var($sql);
if ( empty($id) ) {
	echo "<div id=\"page_message\" class=\"error\"><p class=\"s2_error\"><strong>$this->no_page</strong></p></div>";
}

if ( $this->subscribe2_options['email_freq'] != 'never' ) {
	$disallowed_keywords = array('{TITLE}', '{PERMALINK}', '{DATE}', '{TIME}', '{LINK}', '{ACTION}');
} else {
	$disallowed_keywords = array('{POSTTIME}', '{TABLE}', '{TABLELINKS}', '{COUNT}', '{LINK}', '{ACTION}');
}
$disallowed = false;
foreach ( $disallowed_keywords as $disallowed_keyword ) {
	if ( strstr($this->subscribe2_options['mailtext'], $disallowed_keyword) !== false ) {
		$disallowed[] = $disallowed_keyword;
	}
}
if ( $disallowed !== false ) {
	echo "<div id=\"keyword_message\" class=\"error\"><p class=\"s2_error\"><strong>$this->disallowed_keywords</strong><br>" . implode($disallowed, ', ') . "</p></div>";
}

// send error message if sender email address is off-domain
if ( $this->subscribe2_options['sender'] == 'blogname' ) {
	$sender = get_bloginfo('admin_email');
} else {
	$userdata = $this->get_userdata($this->subscribe2_options['sender']);
	$sender = $userdata->user_email;
}
list($user, $domain) = explode('@', $sender, 2);
if ( !strstr($_SERVER['SERVER_NAME'], $domain) && $this->subscribe2_options['sender'] != 'author' ) {
	echo "<div id=\"sender_message\" class=\"error\"><p class=\"s2_error\"><strong>" . __('You appear to be sending notifications from an email address from a different domain name to your blog, this may result in failed emails', 'subscribe2') . "</strong></p></div>";
}

// detect or define which tab we are in
$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'email';

// show our form
echo "<div class=\"wrap\">";
echo "<div id=\"icon-options-general\" class=\"icon32\"></div>";
$tabs = array('email' => __('Email Settings', 'subscribe2'),
	'templates' => __('Templates', 'subscribe2'),
	'registered' => __('Registered Users', 'subscribe2'),
	'appearance' => __('Appearance', 'subscribe2'),
	'misc' => __('Miscellaneous', 'subscribe2'));
echo "<h2 class=\"nav-tab-wrapper\">";
foreach ( $tabs as $tab_key => $tab_caption ) {
	$active = ($current_tab == $tab_key) ? "nav-tab-active" : "";
	echo "<a class=\"nav-tab " . $active . "\" href=\"?page=s2_settings&amp;tab=" . $tab_key . "\">" . $tab_caption . "</a>";
}
echo "</h2>";

echo "<form method=\"post\">\r\n";
if ( function_exists('wp_nonce_field') ) {
	wp_nonce_field('subscribe2-options_subscribers' . $s2nonce);
}
echo "<input type=\"hidden\" name=\"s2_admin\" value=\"options\" />\r\n";
echo "<input type=\"hidden\" id=\"jsbcclimit\" value=\"" . $this->subscribe2_options['bcclimit'] . "\" />";
echo "<input type=\"hidden\" id=\"jsentries\" value=\"" . $this->subscribe2_options['entries'] . "\" />";

switch ($current_tab) {
	case 'email':
		// settings for outgoing emails
		echo "<div class=\"s2_admin\" id=\"s2_notification_settings\">\r\n";
		echo "<p>\r\n";
		echo __('Restrict the number of <strong>recipients per email</strong> to (0 for unlimited)', 'subscribe2') . ': ';
		echo "<span id=\"s2bcclimit_1\"><span id=\"s2bcclimit\" style=\"background-color: #FFFBCC\">" . $this->subscribe2_options['bcclimit'] . "</span> ";
		echo "<a href=\"#\" onclick=\"s2_show('bcclimit'); return false;\">" . __('Edit', 'subscribe2') . "</a></span>\n";
		echo "<span id=\"s2bcclimit_2\">\r\n";
		echo "<input type=\"text\" name=\"bcclimit\" value=\"" . $this->subscribe2_options['bcclimit'] . "\" size=\"3\" />\r\n";
		echo "<a href=\"#\" onclick=\"s2_update('bcclimit'); return false;\">". __('Update', 'subscribe2') . "</a>\n";
		echo "<a href=\"#\" onclick=\"s2_revert('bcclimit'); return false;\">". __('Revert', 'subscribe2') . "</a></span>\n";

		echo "<br /><br />" . __('Send Admins notifications for new', 'subscribe2') . ': ';
		echo "<label><input type=\"radio\" name=\"admin_email\" value=\"subs\"" . checked($this->subscribe2_options['admin_email'], 'subs', false) . " />\r\n";
		echo __('Subscriptions', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"admin_email\" value=\"unsubs\"" . checked($this->subscribe2_options['admin_email'], 'unsubs', false) . " />\r\n";
		echo __('Unsubscriptions', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"admin_email\" value=\"both\"" . checked($this->subscribe2_options['admin_email'], 'both', false) . " />\r\n";
		echo __('Both', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"admin_email\" value=\"none\"" . checked($this->subscribe2_options['admin_email'], 'none', false) . " />\r\n";
		echo __('Neither', 'subscribe2') . "</label><br /><br />\r\n";

		echo __('Include theme CSS stylesheet in HTML notifications', 'subscribe2') . ': ';
		echo "<label><input type=\"radio\" name=\"stylesheet\" value=\"yes\"" . checked($this->subscribe2_options['stylesheet'], 'yes', false) . " /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"stylesheet\" value=\"no\"" . checked($this->subscribe2_options['stylesheet'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label><br /><br />\r\n";

		echo __('Send Emails for Pages', 'subscribe2') . ': ';
		echo "<label><input type=\"radio\" name=\"pages\" value=\"yes\"" . checked($this->subscribe2_options['pages'], 'yes', false) . " /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"pages\" value=\"no\"" . checked($this->subscribe2_options['pages'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
		$s2_post_types = apply_filters('s2_post_types', NULL);
		if ( !empty($s2_post_types) ) {
			$types = '';
			echo __('Subscribe2 will send email notifications for the following custom post types', 'subscribe2') . ': <strong>';
			foreach ($s2_post_types as $type) {
				('' == $types) ? $types = ucwords($type) : $types .= ", " . ucwords($type);
			}
			echo $types . "</strong><br /><br />\r\n";
		}
		echo __('Send Emails for Password Protected Posts', 'subscribe2') . ': ';
		echo "<label><input type=\"radio\" name=\"password\" value=\"yes\"" . checked($this->subscribe2_options['password'], 'yes', false) . " /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"password\" value=\"no\"" . checked($this->subscribe2_options['password'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
		echo __('Send Emails for Private Posts', 'subscribe2') . ': ';
		echo "<label><input type=\"radio\" name=\"private\" value=\"yes\"" . checked($this->subscribe2_options['private'], 'yes', false) . " /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"private\" value=\"no\"" . checked($this->subscribe2_options['private'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
		echo __('Include Sticky Posts at the top of all Digest Notifications', 'subscribe2') . ': ';
		echo "<label><input type=\"radio\" name=\"stickies\" value=\"yes\"" . checked($this->subscribe2_options['stickies'], 'yes', false) . " /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"stickies\" value=\"no\"" . checked($this->subscribe2_options['stickies'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
		echo __('Send Email From', 'subscribe2') . ': ';
		echo "<label>\r\n";
		$this->admin_dropdown(true);
		echo "</label><br /><br />\r\n";
		if ( function_exists('wp_schedule_event') ) {
			echo __('Send Emails', 'subscribe2') . ": <br /><br />\r\n";
			$this->display_digest_choices();
			echo "<p>" . __('For digest notifications, date order for posts is', 'subscribe2') . ": \r\n";
			echo "<label><input type=\"radio\" name=\"cron_order\" value=\"desc\"" . checked($this->subscribe2_options['cron_order'], 'desc', false) . " /> ";
			echo __('Descending', 'subscribe2') . "</label>&nbsp;&nbsp;";
			echo "<label><input type=\"radio\" name=\"cron_order\" value=\"asc\"" . checked($this->subscribe2_options['cron_order'], 'asc', false) . " /> ";
			echo __('Ascending', 'subscribe2') . "</label></p>\r\n";
		}
		echo __('Add Tracking Parameters to the Permalink', 'subscribe2') . ": ";
		echo "<input type=\"text\" name=\"tracking\" value=\"" . stripslashes($this->subscribe2_options['tracking']) . "\" size=\"50\" /> ";
		echo "<br />" . __('eg. utm_source=subscribe2&amp;utm_medium=email&amp;utm_campaign=postnotify&amp;utm_id={ID}&amp;utm_title={TITLE}', 'subscribe2') . "\r\n";
		echo "</p>\r\n";
		echo "</div>\r\n";
	break;

	case 'templates':
		// email templates
		echo "<div class=\"s2_admin\" id=\"s2_templates\">\r\n";
		echo "<p>\r\n";
		echo "<table style=\"width: 100%; border-collapse: separate; border-spacing: 5px; *border-collapse: expression('separate', cellSpacing = '5px');\" class=\"editform\">\r\n";
		echo "<tr><td style=\"vertical-align: top; height: 350px; min-height: 350px;\">";
		echo __('New Post email (must not be empty)', 'subscribe2') . ":<br />\r\n";
		echo __('Subject Line', 'subscribe2') . ": ";
		echo "<input type=\"text\" name=\"notification_subject\" value=\"" . stripslashes($this->subscribe2_options['notification_subject']) . "\" size=\"45\" />";
		echo "<br />\r\n";
		echo "<textarea rows=\"9\" cols=\"60\" name=\"mailtext\">" . stripslashes($this->subscribe2_options['mailtext']) . "</textarea>\r\n";
		echo "</td><td style=\"vertical-align: top;\" rowspan=\"3\">";
		echo "<p class=\"submit\"><input type=\"submit\" class=\"button-secondary\" name=\"preview\" value=\"" . __('Send Email Preview', 'subscribe2') . "\" /></p>\r\n";
		echo "<h3>" . __('Message substitutions', 'subscribe2') . "</h3>\r\n";
		echo "<dl>";
		echo "<dt><b><em style=\"color: red\">" . __('IF THE FOLLOWING KEYWORDS ARE ALSO IN YOUR POST THEY WILL BE SUBSTITUTED' ,'subscribe2') . "</em></b></dt><dd></dd>\r\n";
		echo "<dt><b>{BLOGNAME}</b></dt><dd>" . get_option('blogname') . "</dd>\r\n";
		echo "<dt><b>{BLOGLINK}</b></dt><dd>" . get_option('home') . "</dd>\r\n";
		echo "<dt><b>{TITLE}</b></dt><dd>" . __("the post's title<br />(<i>for per-post emails only</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{POST}</b></dt><dd>" . __("the excerpt or the entire post<br />(<i>based on the subscriber's preferences</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{POSTTIME}</b></dt><dd>" . __("the excerpt of the post and the time it was posted<br />(<i>for digest emails only</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{TABLE}</b></dt><dd>" . __("a list of post titles<br />(<i>for digest emails only</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{TABLELINKS}</b></dt><dd>" . __("a list of post titles followed by links to the articles<br />(<i>for digest emails only</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{PERMALINK}</b></dt><dd>" . __("the post's permalink<br />(<i>for per-post emails only</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{TINYLINK}</b></dt><dd>" . __("the post's permalink after conversion by TinyURL", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{DATE}</b></dt><dd>" . __("the date the post was made<br />(<i>for per-post emails only</i>)", "subscribe2") . "</dd>\r\n";
		echo "<dt><b>{TIME}</b></dt><dd>" . __("the time the post was made<br />(<i>for per-post emails only</i>)", "subscribe2") . "</dd>\r\n";
		echo "<dt><b>{MYNAME}</b></dt><dd>" . __("the admin or post author's name", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{EMAIL}</b></dt><dd>" . __("the admin or post author's email", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{AUTHORNAME}</b></dt><dd>" . __("the post author's name", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{LINK}</b></dt><dd>" . __("the generated link to confirm a request<br />(<i>only used in the confirmation email template</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{ACTION}</b></dt><dd>" . __("Action performed by LINK in confirmation email<br />(<i>only used in the confirmation email template</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{CATS}</b></dt><dd>" . __("the post's assigned categories", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{TAGS}</b></dt><dd>" . __("the post's assigned Tags", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>{COUNT}</b></dt><dd>" . __("the number of posts included in the digest email<br />(<i>for digest emails only</i>)", 'subscribe2') . "</dd>\r\n";
		echo "</dl></td></tr><tr><td  style=\"vertical-align: top; height: 350px; min-height: 350px;\">";
		echo __('Subscribe / Unsubscribe confirmation email', 'subscribe2') . ":<br />\r\n";
		echo __('Subject Line', 'subscribe2') . ": ";
		echo "<input type=\"text\" name=\"confirm_subject\" value=\"" . stripslashes($this->subscribe2_options['confirm_subject']) . "\" size=\"45\" /><br />\r\n";
		echo "<textarea rows=\"9\" cols=\"60\" name=\"confirm_email\">" . stripslashes($this->subscribe2_options['confirm_email']) . "</textarea>\r\n";
		echo "</td></tr><tr><td style=\"vertical-align: top; height: 350px; min-height: 350px;\">";
		echo __('Reminder email to Unconfirmed Subscribers', 'subscribe2') . ":<br />\r\n";
		echo __('Subject Line', 'subscribe2') . ": ";
		echo "<input type=\"text\" name=\"remind_subject\" value=\"" . stripslashes($this->subscribe2_options['remind_subject']) . "\" size=\"45\" /><br />\r\n";
		echo "<textarea rows=\"9\" cols=\"60\" name=\"remind_email\">" . stripslashes($this->subscribe2_options['remind_email']) . "</textarea><br /><br />\r\n";
		echo "</td></tr></table>\r\n";
		echo "</div>\r\n";
	break;

	case 'registered':
		// compulsory categories
		echo "<div class=\"s2_admin\" id=\"s2_compulsory_categories\">\r\n";
		echo "<input type=\"hidden\" name=\"registered_users_tab\" value=\"options\" />\r\n";
		echo "<h3>" . __('Compulsory Categories', 'subscribe2') . "</h3>\r\n";
		echo "<p>\r\n";
		echo "<strong><em style=\"color: red\">" . __('Compulsory categories will be checked by default for Registered Subscribers', 'subscribe2') . "</em></strong><br />\r\n";
		echo "</p>";
		$this->display_category_form(explode(',', $this->subscribe2_options['compulsory']), 1, array(), 'compulsory');
		echo "</div>\r\n";

		// excluded categories
		echo "<div class=\"s2_admin\" id=\"s2_excluded_categories\">\r\n";
		echo "<h3>" . __('Excluded Categories', 'subscribe2') . "</h3>\r\n";
		echo "<p>";
		echo "<strong><em style=\"color: red\">" . __('Posts assigned to any Excluded Category do not generate notifications and are not included in digest notifications', 'subscribe2') . "</em></strong><br />\r\n";
		echo "</p>";
		$this->display_category_form(explode(',', $this->subscribe2_options['exclude']), 1, array(), 'exclude');
		echo "<p style=\"text-align: center;\"><label><input type=\"checkbox\" name=\"reg_override\" value=\"1\"" . checked($this->subscribe2_options['reg_override'], '1', false) . " /> ";
		echo __('Allow registered users to subscribe to excluded categories?', 'subscribe2') . "</label></p>\r\n";
		echo "</div>\r\n";

		// excluded post formats
		$formats = get_theme_support('post-formats');
		if ( $formats !== false ) {
			// excluded formats
			echo "<div class=\"s2_admin\" id=\"s2_excluded_formats\">\r\n";
			echo "<h3>" . __('Excluded Formats', 'subscribe2') . "</h3>\r\n";
			echo "<p>";
			echo "<strong><em style=\"color: red\">" . __('Posts assigned to any Excluded Format do not generate notifications and are not included in digest notifications', 'subscribe2') . "</em></strong><br />\r\n";
			echo "</p>";
			$this->display_format_form($formats, explode(',', $this->subscribe2_options['exclude_formats']));
			echo "</div>\r\n";
		}

		//Auto Subscription for new registrations
		echo "<div class=\"s2_admin\" id=\"s2_autosubscribe_settings\">\r\n";
		echo "<h3>" . __('Auto-Subscribe', 'subscribe2') . "</h3>\r\n";
		echo "<p>\r\n";
		echo __('Subscribe new users registering with your blog', 'subscribe2') . ":<br />\r\n";
		echo "<label><input type=\"radio\" name=\"autosub\" value=\"yes\"" . checked($this->subscribe2_options['autosub'], 'yes', false) . " /> ";
		echo __('Automatically', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"autosub\" value=\"wpreg\"" . checked($this->subscribe2_options['autosub'], 'wpreg', false) . " /> ";
		echo __('Display option on Registration Form', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"autosub\" value=\"no\"" . checked($this->subscribe2_options['autosub'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
		echo __('Auto-subscribe includes any excluded categories', 'subscribe2') . ":<br />\r\n";
		echo "<label><input type=\"radio\" name=\"newreg_override\" value=\"yes\"" . checked($this->subscribe2_options['newreg_override'], 'yes', false) . " /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"newreg_override\" value=\"no\"" . checked($this->subscribe2_options['newreg_override'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
		echo __('Registration Form option is checked by default', 'subscribe2') . ":<br />\r\n";
		echo "<label><input type=\"radio\" name=\"wpregdef\" value=\"yes\"" . checked($this->subscribe2_options['wpregdef'], 'yes', false) . " /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"wpregdef\" value=\"no\"" . checked($this->subscribe2_options['wpregdef'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
		echo __('Auto-subscribe users to receive email as', 'subscribe2') . ": <br />\r\n";
		echo "<label><input type=\"radio\" name=\"autoformat\" value=\"html\"" . checked($this->subscribe2_options['autoformat'], 'html', false) . " /> ";
		echo __('HTML - Full', 'subscribe2') ."</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"autoformat\" value=\"html_excerpt\"" . checked($this->subscribe2_options['autoformat'], 'html_excerpt', false) . " /> ";
		echo __('HTML - Excerpt', 'subscribe2') ."</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"autoformat\" value=\"post\"" . checked($this->subscribe2_options['autoformat'], 'post', false) . " /> ";
		echo __('Plain Text - Full', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"autoformat\" value=\"excerpt\"" . checked($this->subscribe2_options['autoformat'], 'excerpt', false) . " /> ";
		echo __('Plain Text - Excerpt', 'subscribe2') . "</label><br /><br />";
		echo __('Registered Users have the option to auto-subscribe to new categories', 'subscribe2') . ": <br />\r\n";
		echo "<label><input type=\"radio\" name=\"show_autosub\" value=\"yes\"" . checked($this->subscribe2_options['show_autosub'], 'yes', false) . " /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"show_autosub\" value=\"no\"" . checked($this->subscribe2_options['show_autosub'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"show_autosub\" value=\"exclude\"" . checked($this->subscribe2_options['show_autosub'], 'exclude', false) . " /> ";
		echo __('New categories are immediately excluded', 'subscribe2') . "</label><br /><br />";
		echo __('Option for Registered Users to auto-subscribe to new categories is checked by default', 'subscribe2') . ": <br />\r\n";
		echo "<label><input type=\"radio\" name=\"autosub_def\" value=\"yes\"" . checked($this->subscribe2_options['autosub_def'], 'yes', false) . " /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"autosub_def\" value=\"no\"" . checked($this->subscribe2_options['autosub_def'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label><br /><br />";
		echo __('Display checkbox to allow subscriptions from the comment form', 'subscribe2') . ": <br />\r\n";
		echo "<label><input type=\"radio\" name=\"comment_subs\" value=\"before\"" . checked($this->subscribe2_options['comment_subs'], 'before', false) . " /> ";
		echo __('Before the Comment Submit button', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"comment_subs\" value=\"after\"" . checked($this->subscribe2_options['comment_subs'], 'after', false) . " /> ";
		echo __('After the Comment Submit button', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"comment_subs\" value=\"no\"" . checked($this->subscribe2_options['comment_subs'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label><br /><br />";
		echo __('Comment form checkbox is checked by default', 'subscribe2') . ": <br />\r\n";
		echo "<label><input type=\"radio\" name=\"comment_def\" value=\"yes\"" . checked($this->subscribe2_options['comment_def'], 'yes', false) . " /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"comment_def\" value=\"no\"" . checked($this->subscribe2_options['comment_def'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
		echo __('Show one-click subscription on profile page', 'subscribe2') . ":<br />\r\n";
		echo "<label><input type=\"radio\" name=\"one_click_profile\" value=\"yes\"" . checked($this->subscribe2_options['one_click_profile'], 'yes', false) . " /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"one_click_profile\" value=\"no\"" . checked($this->subscribe2_options['one_click_profile'], 'no', false) . " /> ";
		echo __('No', 'subscribe2') . "</label>\r\n";
		echo "</p></div>\r\n";
	break;

	case 'appearance':
		// Appearance options
		echo "<div class=\"s2_admin\" id=\"s2_appearance_settings\">\r\n";
		echo "<input type=\"hidden\" name=\"appearance_users_tab\" value=\"options\" />\r\n";
		echo "<p>\r\n";

		// WordPress page ID where subscribe2 token is used
		echo __('Set default Subscribe2 page as', 'subscribe2') . ': ';
		echo "<select name=\"s2page\">\r\n";
		$this->pages_dropdown($this->subscribe2_options['s2page']);
		echo "</select>\r\n";

		// Number of subscribers per page
		echo "<br /><br />" . __('Set the number of Subscribers displayed per page', 'subscribe2') . ': ';
		echo "<span id=\"s2entries_1\"><span id=\"s2entries\" style=\"background-color: #FFFBCC\">" . $this->subscribe2_options['entries'] . "</span> ";
		echo "<a href=\"#\" onclick=\"s2_show('entries'); return false;\">" . __('Edit', 'subscribe2') . "</a></span>\n";
		echo "<span id=\"s2entries_2\">\r\n";
		echo "<input type=\"text\" name=\"entries\" value=\"" . $this->subscribe2_options['entries'] . "\" size=\"3\" />\r\n";
		echo "<a href=\"#\" onclick=\"s2_update('entries'); return false;\">". __('Update', 'subscribe2') . "</a>\n";
		echo "<a href=\"#\" onclick=\"s2_revert('entries'); return false;\">". __('Revert', 'subscribe2') . "</a></span>\n";

		// show link to WordPress page in meta
		echo "<br /><br /><label><input type=\"checkbox\" name=\"show_meta\" value=\"1\"" . checked($this->subscribe2_options['show_meta'], '1', false) . " /> ";
		echo __('Show a link to your subscription page in "meta"?', 'subscribe2') . "</label><br /><br />\r\n";

		// show QuickTag button
		echo "<label><input type=\"checkbox\" name=\"show_button\" value=\"1\"" . checked($this->subscribe2_options['show_button'], '1', false) . " /> ";
		echo __('Show the Subscribe2 button on the Write toolbar?', 'subscribe2') . "</label><br /><br />\r\n";

		// enable AJAX style form
		echo "<label><input type=\"checkbox\" name=\"ajax\" value=\"1\"" . checked($this->subscribe2_options['ajax'], '1', false) . " /> ";
		echo __('Enable AJAX style subscription form?', 'subscribe2') . "</label><br /><br />\r\n";

		// show Widget
		echo "<label><input type=\"checkbox\" name=\"widget\" value=\"1\"" . checked($this->subscribe2_options['widget'], '1', false) . " /> ";
		echo __('Enable Subscribe2 Widget?', 'subscribe2') . "</label><br /><br />\r\n";

		// show Counter Widget
		echo "<label><input type=\"checkbox\" name=\"counterwidget\" value=\"1\"" . checked($this->subscribe2_options['counterwidget'], '1', false) . " /> ";
		echo __('Enable Subscribe2 Counter Widget?', 'subscribe2') . "</label><br /><br />\r\n";

		// s2_meta checked by default
		echo "<label><input type =\"checkbox\" name=\"s2meta_default\" value=\"1\"" . checked($this->subscribe2_options['s2meta_default'], '1', false) . " /> ";
		echo __('Disable email notifications is checked by default on authoring pages?', 'subscribe2') . "</label>\r\n";
		echo "</p>";
		echo "</div>\r\n";
	break;

	case 'misc':
		//barred domains
		echo "<div class=\"s2_admin\" id=\"s2_barred_domains\">\r\n";
		echo "<h3>" . __('Barred Domains', 'subscribe2') . "</h3>\r\n";
		echo "<p>\r\n";
		echo __('Enter domains to bar from public subscriptions: <br /> (Use a new line for each entry and omit the "@" symbol, for example email.com)', 'subscribe2');
		echo "<br />\r\n<textarea style=\"width: 98%;\" rows=\"4\" cols=\"60\" name=\"barred\">" . esc_textarea($this->subscribe2_options['barred']) . "</textarea>";
		echo "</p>";
		echo "<h3>" . __('Links', 'subscribe2') . "</h3>\r\n";
		echo "<a href=\"http://wordpress.org/extend/plugins/subscribe2/\">" . __('Plugin Site', 'subscribe2') . "</a><br />";
		echo "<a href='http://plugins.trac.wordpress.org/browser/subscribe2/i18n/'>" . __('Translation Files', 'subscribe2') . "</a><br />";
		echo "<a href=\"http://wordpress.org/support/plugin/subscribe2\">" . __('Plugin Forum', 'subscribe2') . "</a><br />";
		echo "<a href=\"http://subscribe2.wordpress.com/\">" . __('Plugin Blog', 'subscribe2') . "</a><br />";
		echo "<a href=\"https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=2387904\">" . __('Make a donation via PayPal', 'subscribe2') . "</a>";
		echo "</div>\r\n";
	break;

}
// submit
echo "<p class=\"submit\" style=\"text-align: center\"><input type=\"submit\" class=\"button-primary\" name=\"submit\" value=\"" . __('Submit', 'subscribe2') . "\" /></p>";

if ($current_tab === 'misc') {
	// reset
	echo "<h3>" . __('Reset to Default Settings', 'subscribe2') . "</h3>\r\n";
	echo "<p>" . __('Use this to reset all options to their defaults. This <strong><em>will not</em></strong> modify your list of subscribers.', 'subscribe2') . "</p>\r\n";
	echo "<p class=\"submit\" style=\"text-align: center\">";
	echo "<input type=\"submit\" id=\"deletepost\" name=\"reset\" value=\"" . __('RESET', 'subscribe2') .
	"\" /></p>";
}
echo "</form></div>\r\n";

include(ABSPATH . 'wp-admin/admin-footer.php');
// just to be sure
die;
?>