<?php
class s2class {
// variables and constructor are declared at the end
	/**
	Load translations
	*/
	function load_translations() {
		load_plugin_textdomain('subscribe2', false, S2DIR);
		load_plugin_textdomain('subscribe2', false, S2DIR . "languages/");
		$mofile = WP_LANG_DIR . '/subscribe2-' . apply_filters('plugin_locale', get_locale(), 'subscribe2') . '.mo';
		load_textdomain('subscribe2', $mofile);
	} // end load_translations()

	/**
	Load all our strings
	*/
	function load_strings() {
		// adjust the output of Subscribe2 here

		$this->please_log_in = "<p class=\"s2_message\">" . sprintf(__('To manage your subscription options please <a href="%1$s">login.</a>', 'subscribe2'), get_option('siteurl') . '/wp-login.php') . "</p>";

		$this->profile = "<p class=\"s2_message\">" . sprintf(__('You may manage your subscription options from your <a href="%1$s">profile</a>', 'subscribe2'), get_option('siteurl') . "/wp-admin/admin.php?page=s2") . "</p>";
		if ( $this->s2_mu === true ) {
			global $blog_id;
			$user_ID = get_current_user_id();
			if ( !is_user_member_of_blog($user_ID, $blog_id) ) {
				// if we are on multisite and the user is not a member of this blog change the link
				$this->profile = "<p class=\"s2_message\">" . sprintf(__('<a href="%1$s">Subscribe</a> to email notifications when this blog posts new content.', 'subscribe2'), get_option('siteurl') . "/wp-admin/?s2mu_subscribe=" . $blog_id) . "</p>";
			}
		}

		$this->confirmation_sent = "<p class=\"s2_message\">" . __('A confirmation message is on its way!', 'subscribe2') . "</p>";

		$this->already_subscribed = "<p class=\"s2_error\">" . __('That email address is already subscribed.', 'subscribe2') . "</p>";

		$this->not_subscribed = "<p class=\"s2_error\">" . __('That email address is not subscribed.', 'subscribe2') . "</p>";

		$this->not_an_email = "<p class=\"s2_error\">" . __('Sorry, but that does not look like an email address to me.', 'subscribe2') . "</p>";

		$this->barred_domain = "<p class=\"s2_error\">" . __('Sorry, email addresses at that domain are currently barred due to spam, please use an alternative email address.', 'subscribe2') . "</p>";

		$this->error = "<p class=\"s2_error\">" . __('Sorry, there seems to be an error on the server. Please try again later.', 'subscribe2') . "</p>";

		$this->no_page = __('You must to create a WordPress page for this plugin to work correctly.', 'subscribe2');

		$this->disallowed_keywords = __('Your chosen email type (per-post or digest) does not support the following keywords:', 'subscribe2');

		$this->mail_sent = "<p class=\"s2_message\">" . __('Message sent!', 'subscribe2') . "</p>";

		$this->mail_failed = "<p class=\"s2_error\">" . __('Message failed!', 'subscribe2') . "</p>";

		// confirmation messages
		$this->no_such_email = "<p class=\"s2_error\">" . __('No such email address is registered.', 'subscribe2') . "</p>";

		$this->added = "<p class=\"s2_message\">" . __('You have successfully subscribed!', 'subscribe2') . "</p>";

		$this->deleted = "<p class=\"s2_message\">" . __('You have successfully unsubscribed.', 'subscribe2') . "</p>";

		$this->subscribe = __('subscribe', 'subscribe2'); //ACTION replacement in subscribing confirmation email

		$this->unsubscribe = __('unsubscribe', 'subscribe2'); //ACTION replacement in unsubscribing in confirmation email

		// menu strings
		$this->options_saved = __('Options saved!', 'subscribe2');
		$this->options_reset = __('Options reset!', 'subscribe2');
	} // end load_strings()

/* ===== Install, upgrade, reset ===== */
	/**
	Install our table
	*/
	function install() {
		// load our translations and strings
		$this->load_translations();

		// include upgrade-functions for maybe_create_table;
		if ( !function_exists('maybe_create_table') ) {
			require_once(ABSPATH . 'wp-admin/install-helper.php');
		}
		$date = date('Y-m-d');
		$sql = "CREATE TABLE $this->public (
			id int(11) NOT NULL auto_increment,
			email varchar(64) NOT NULL default '',
			active tinyint(1) default 0,
			date DATE default '$date' NOT NULL,
			time TIME DEFAULT '00:00:00' NOT NULL,
			ip char(64) NOT NULL default 'admin',
			conf_date DATE,
			conf_time TIME,
			conf_ip char(64),
			PRIMARY KEY (id) )";

		// create the table, as needed
		maybe_create_table($this->public, $sql);

		// create table entries for registered users
		$users = $this->get_all_registered('ID');
		if ( !empty($users) ) {
			foreach ( $users as $user_ID ) {
				$check_format = get_user_meta($user_ID, $this->get_usermeta_keyname('s2_format'), true);
				if ( empty($check_format) ) {
					// no prior settings so create them
					$this->register($user_ID);
				}
			}
		}

		// safety check if options exist and if not create them
		if ( !is_array($this->subscribe2_options) ) {
			$this->reset();
		}
	} // end install()

	/**
	Upgrade function for the database and settings
	*/
	function upgrade() {
		// load our translations and strings
		$this->load_translations();

		require(S2PATH . "classes/class-s2-upgrade.php");
		global $s2_upgrade;
		$s2_upgrade = new s2class_upgrade;

		// ensure that the options are in the database
		require(S2PATH . "include/options.php");
		// catch older versions that didn't use serialised options
		if ( !isset($this->subscribe2_options['version']) ) {
			$this->subscribe2_options['version'] = '2.0';
		}

		// let's take the time to ensure that database entries exist for all registered users
		$s2_upgrade->upgrade_core();
		if ( version_compare($this->subscribe2_options['version'], '2.3', '<') ) {
			$s2_upgrade->upgrade23();
			$this->subscribe2_options['version'] = '2.3';
			update_option('subscribe2_options', $this->subscribe2_options);
		}
		if ( version_compare($this->subscribe2_options['version'], '5.1', '<') ) {
			$s2_upgrade->upgrade51();
			$this->subscribe2_options['version'] = '5.1';
			update_option('subscribe2_options', $this->subscribe2_options);
		}
		if ( version_compare($this->subscribe2_options['version'], '5.6', '<') ) {
			$s2_upgrade->upgrade56();
			$this->subscribe2_options['version'] = '5.6';
			update_option('subscribe2_options', $this->subscribe2_options);
		}
		if ( version_compare($this->subscribe2_options['version'], '5.9', '<') ) {
			$s2_upgrade->upgrade59();
			$this->subscribe2_options['version'] = '5.9';
			update_option('subscribe2_options', $this->subscribe2_options);
		}
		if ( version_compare($this->subscribe2_options['version'], '6.4', '<') ) {
			$s2_upgrade->upgrade64();
			$this->subscribe2_options['version'] = '6.4';
			update_option('subscribe2_options', $this->subscribe2_options);
		}
		if ( version_compare($this->subscribe2_options['version'], '7.0', '<') ) {
			$s2_upgrade->upgrade70();
			$this->subscribe2_options['version'] = '7.0';
			update_option('subscribe2_options', $this->subscribe2_options);
		}
		if ( version_compare($this->subscribe2_options['version'], '8.5', '<') ) {
			$s2_upgrade->upgrade85();
			$this->subscribe2_options['version'] = '8.5';
			update_option('subscribe2_options', $this->subscribe2_options);
		}
		if ( version_compare($this->subscribe2_options['version'], '8.6', '<') ) {
			$s2_upgrade->upgrade86();
			$this->subscribe2_options['version'] = '8.6';
			update_option('subscribe2_options', $this->subscribe2_options);
		}
		if ( version_compare($this->subscribe2_options['version'], '8.8', '<') ) {
			$s2_upgrade->upgrade88();
			$this->subscribe2_options['version'] = '8.8';
			update_option('subscribe2_options', $this->subscribe2_options);
		}

		$this->subscribe2_options['version'] = S2VERSION;
		update_option('subscribe2_options', $this->subscribe2_options);

		return;
	} // end upgrade()

	/**
	Reset our options
	*/
	function reset() {
		// load our translations and strings
		$this->load_translations();

		delete_option('subscribe2_options');
		wp_clear_scheduled_hook('s2_digest_cron');
		unset($this->subscribe2_options);
		require(S2PATH . "include/options.php");
		$this->subscribe2_options['version'] = S2VERSION;
		update_option('subscribe2_options', $this->subscribe2_options);
	} // end reset()

/* ===== mail handling ===== */
	/**
	Performs string substitutions for subscribe2 mail tags
	*/
	function substitute($string = '') {
		if ( '' == $string ) {
			return;
		}
		$string = str_replace("{BLOGNAME}", html_entity_decode(get_option('blogname'), ENT_QUOTES), $string);
		$string = str_replace("{BLOGLINK}", get_option('home'), $string);
		$string = str_replace("{TITLE}", stripslashes($this->post_title), $string);
		$link = "<a href=\"" . $this->get_tracking_link($this->permalink) . "\">" . $this->get_tracking_link($this->permalink) . "</a>";
		$string = str_replace("{PERMALINK}", $link, $string);
		if ( strstr($string, "{TINYLINK}") ) {
			$tinylink = file_get_contents('http://tinyurl.com/api-create.php?url=' . urlencode($this->get_tracking_link($this->permalink)));
			if ( $tinylink !== 'Error' && $tinylink != false ) {
				$tlink = "<a href=\"" . $tinylink . "\">" . $tinylink . "</a>";
				$string = str_replace("{TINYLINK}", $tlink, $string);
			} else {
				$string = str_replace("{TINYLINK}", $link, $string);
			}
		}
		$string = str_replace("{DATE}", $this->post_date, $string);
		$string = str_replace("{TIME}", $this->post_time, $string);
		$string = str_replace("{MYNAME}", stripslashes($this->myname), $string);
		$string = str_replace("{EMAIL}", $this->myemail, $string);
		$string = str_replace("{AUTHORNAME}", stripslashes($this->authorname), $string);
		$string = str_replace("{CATS}", $this->post_cat_names, $string);
		$string = str_replace("{TAGS}", $this->post_tag_names, $string);
		$string = str_replace("{COUNT}", $this->post_count, $string);

		return $string;
	} // end substitute()

	/**
	Delivers email to recipients in HTML or plaintext
	*/
	function mail($recipients = array(), $subject = '', $message = '', $type = 'text', $attachments = array()) {
		if ( empty($recipients) || '' == $message ) { return; }

		// Replace any escaped html symbols in subject then apply filter
		$subject = strip_tags(html_entity_decode($subject, ENT_QUOTES));
		$subject = apply_filters('s2_email_subject', $subject);

		if ( 'html' == $type ) {
			$headers = $this->headers('html', $attachments);
			if ( 'yes' == $this->subscribe2_options['stylesheet'] ) {
				$mailtext = apply_filters('s2_html_email', "<html><head><title>" . $subject . "</title><link rel=\"stylesheet\" href=\"" . get_stylesheet_uri() . "\" type=\"text/css\" media=\"screen\" /></head><body>" . $message . "</body></html>", $subject, $message);
			} else {
				$mailtext = apply_filters('s2_html_email', "<html><head><title>" . $subject . "</title></head><body>" . $message . "</body></html>", $subject, $message);
			}
		} else {
			$headers = $this->headers('text', $attachments);
			$message = preg_replace('|&[^a][^m][^p].{0,3};|', '', $message);
			$message = preg_replace('|&amp;|', '&', $message);
			$message = wordwrap(strip_tags($message), $this->word_wrap, "\n");
			$mailtext = apply_filters('s2_plain_email', $message);
		}

		// Construct BCC headers for sending or send individual emails
		$bcc = '';
		natcasesort($recipients);
		if ( function_exists('wpmq_mail') || $this->subscribe2_options['bcclimit'] == 1 || count($recipients) == 1 ) {
			// BCCLimit is 1 so send individual emails or we only have 1 recipient
			foreach ( $recipients as $recipient ) {
				$recipient = trim($recipient);
				// sanity check -- make sure we have a valid email
				if ( !is_email($recipient) || empty($recipient) ) { continue; }
				// Use the mail queue provided we are not sending a preview
				if ( function_exists('wpmq_mail') && !$this->preview_email ) {
					@wp_mail($recipient, $subject, $mailtext, $headers, $attachments, 0);
				} else {
					@wp_mail($recipient, $subject, $mailtext, $headers, $attachments);
				}
			}
			return true;
		} elseif ( $this->subscribe2_options['bcclimit'] == 0 ) {
			// we're not using BCCLimit
			foreach ( $recipients as $recipient ) {
				$recipient = trim($recipient);
				// sanity check -- make sure we have a valid email
				if ( !is_email($recipient) ) { continue; }
				// and NOT the sender's email, since they'll get a copy anyway
				if ( !empty($recipient) && $this->myemail != $recipient ) {
					('' == $bcc) ? $bcc = "Bcc: $recipient" : $bcc .= ", $recipient";
					// Bcc Headers now constructed by phpmailer class
				}
			}
			$headers .= "$bcc\n";
		} else {
			// we're using BCCLimit
			$count = 1;
			$batch = array();
			foreach ( $recipients as $recipient ) {
				$recipient = trim($recipient);
				// sanity check -- make sure we have a valid email
				if ( !is_email($recipient) ) { continue; }
				// and NOT the sender's email, since they'll get a copy anyway
				if ( !empty($recipient) && $this->myemail != $recipient ) {
					('' == $bcc) ? $bcc = "Bcc: $recipient" : $bcc .= ", $recipient";
					// Bcc Headers now constructed by phpmailer class
				}
				if ( $this->subscribe2_options['bcclimit'] == $count ) {
					$count = 0;
					$batch[] = $bcc;
					$bcc = '';
				}
				$count++;
			}
			// add any partially completed batches to our batch array
			if ( '' != $bcc ) {
				$batch[] = $bcc;
			}
		}
		// rewind the array, just to be safe
		reset($recipients);

		// actually send mail
		if ( isset($batch) && !empty($batch) ) {
			foreach ( $batch as $bcc ) {
					$newheaders = $headers . "$bcc\n";
					$status = @wp_mail($this->myemail, $subject, $mailtext, $newheaders, $attachments);
			}
		} else {
			$status = @wp_mail($this->myemail, $subject, $mailtext, $headers, $attachments);
		}
		return $status;
	} // end mail()

	/**
	Construct standard set of email headers
	*/
	function headers($type = 'text', $attachments = array()) {
		if ( empty($this->myname) || empty($this->myemail) ) {
			if ( $this->subscribe2_options['sender'] == 'blogname' ) {
				$this->myname = html_entity_decode(get_option('blogname'), ENT_QUOTES);
				$this->myemail = get_option('admin_email');
			} else {
				$admin = $this->get_userdata($this->subscribe2_options['sender']);
				$this->myname = html_entity_decode($admin->display_name, ENT_QUOTES);
				$this->myemail = $admin->user_email;
				// fail safe to ensure sender details are not empty
				if ( empty($this->myname) ) {
					$this->myname = html_entity_decode(get_option('blogname'), ENT_QUOTES);
				}
				if ( empty($this->myemail) ) {
					// Get the site domain and get rid of www.
					$sitename = strtolower( $_SERVER['SERVER_NAME'] );
					if ( substr( $sitename, 0, 4 ) == 'www.' ) {
						$sitename = substr( $sitename, 4 );
					}
					$this->myemail = 'wordpress@' . $sitename;
				}
			}
		}

		if ( function_exists('mb_encode_mimeheader') ) {
			$header['From'] = mb_encode_mimeheader($this->myname, 'UTF-8', 'Q') . " <" . $this->myemail . ">";
			$header['Reply-To'] = mb_encode_mimeheader($this->myname, 'UTF-8', 'Q') . " <" . $this->myemail . ">";
		} else {
			$header['From'] = $this->myname. " <" . $this->myemail . ">";
			$header['Reply-To'] = $this->myname . " <" . $this->myemail . ">";
		}
		$header['Return-path'] = "<" . $this->myemail . ">";
		$header['Precedence'] = "list\nList-Id: " . html_entity_decode(get_option('blogname'), ENT_QUOTES) . "";
		if ( empty($attachments) && $type == 'html' ) {
			// To send HTML mail, the Content-Type header must be set
			$header['Content-Type'] = get_option('html_type') . "; charset=\"". get_option('blog_charset') . "\"";
		} elseif ( empty($attachments) && $type == 'text' ) {
			$header['Content-Type'] = "text/plain; charset=\"". get_option('blog_charset') . "\"";
		}

		// apply header filter to allow on-the-fly amendments
		$header = apply_filters('s2_email_headers', $header);
		// collapse the headers using $key as the header name
		foreach ( $header as $key => $value ) {
			$headers[$key] = $key . ": " . $value;
		}
		$headers = implode("\n", $headers);
		$headers .= "\n";

		return $headers;
	} // end headers()

	/**
	Function to add UTM tracking details to links
	*/
	function get_tracking_link($link) {
		if ( empty($link) ) { return; }
		if ( !empty($this->subscribe2_options['tracking']) ) {
			(strpos($link, '?') > 0) ? $delimiter .= '&' : $delimiter = '?';
			$tracking = $this->subscribe2_options['tracking'];
			if ( strpos($tracking, "{ID}") ) {
				$id = url_to_postid($link);
				$tracking = str_replace("{ID}", $id, $tracking);
			}
			if ( strpos($tracking, "{TITLE}") ) {
				$id = url_to_postid($link);
				$title = urlencode(htmlentities(get_the_title($id),1));
				$tracking = str_replace("{TITLE}", $title, $tracking);
			}
			return $link . $delimiter . $tracking;
		} else {
			return $link;
		}
	} // end get_tracking_link()

	/**
	Sends an email notification of a new post
	*/
	function publish($post, $preview = '') {
		if ( !$post ) { return $post; }

		if ( $this->s2_mu && !apply_filters('s2_allow_site_switching', $this->site_switching) ) {
			global $switched;
			if ( $switched ) { return; }
		}

		if ( $preview == '' ) {
			// we aren't sending a Preview to the current user so carry out checks
			$s2mail = get_post_meta($post->ID, '_s2mail', true);
			if ( (isset($_POST['s2_meta_field']) && $_POST['s2_meta_field'] == 'no') || strtolower(trim($s2mail)) == 'no' ) { return $post; }

			// are we doing daily digests? If so, don't send anything now
			if ( $this->subscribe2_options['email_freq'] != 'never' ) { return $post; }

			// is the current post of a type that should generate a notification email?
			// uses s2_post_types filter to allow for custom post types in WP 3.0
			if ( $this->subscribe2_options['pages'] == 'yes' ) {
				$s2_post_types = array('page', 'post');
			} else {
				$s2_post_types = array('post');
			}
			$s2_post_types = apply_filters('s2_post_types', $s2_post_types);
			if ( !in_array($post->post_type, $s2_post_types) ) {
				return $post;
			}

			// Are we sending notifications for password protected posts?
			if ( $this->subscribe2_options['password'] == "no" && $post->post_password != '' ) {
					return $post;
			}

			// Is the post assigned to a format for which we should not be sending posts
			$post_format = get_post_format($post->ID);
			$excluded_formats = explode(',', $this->subscribe2_options['exclude_formats']);
			if ( $post_format !== false && in_array($post_format, $excluded_formats) ) {
				return $post;
			}

			$s2_taxonomies = apply_filters('s2_taxonomies', array('category'));
			$post_cats = wp_get_object_terms($post->ID, $s2_taxonomies, array('fields' => 'ids'));
			$check = false;
			// is the current post assigned to any categories
			// which should not generate a notification email?
			foreach ( explode(',', $this->subscribe2_options['exclude']) as $cat ) {
				if ( in_array($cat, $post_cats) ) {
					$check = true;
				}
			}

			if ( $check ) {
				// hang on -- can registered users subscribe to
				// excluded categories?
				if ( '0' == $this->subscribe2_options['reg_override'] ) {
					// nope? okay, let's leave
					return $post;
				}
			}

			// Are we sending notifications for Private posts?
			// Action is added if we are, but double check option and post status
			if ( $this->subscribe2_options['private'] == "yes" && $post->post_status == 'private' ) {
				// don't send notification to public users
				$check = true;
			}

			// lets collect our subscribers
			$public = array();
			if ( !$check ) {
				// if this post is assigned to an excluded
				// category, or is a private post then
				// don't send public subscribers a notification
				$public = $this->get_public();
			}
			if ( $post->post_type == 'page' ) {
				$post_cats_string = implode(',', get_all_category_ids());
			} else {
				$post_cats_string = implode(',', $post_cats);
			}
			$registered = $this->get_registered("cats=$post_cats_string");

			// do we have subscribers?
			if ( empty($public) && empty($registered) ) {
				// if not, no sense doing anything else
				return $post;
			}
		} else {
			// make sure we prime the taxonomy variable for preview posts
			$s2_taxonomies = apply_filters('s2_taxonomies', array('category'));
		}

		// we set these class variables so that we can avoid
		// passing them in function calls a little later
		$this->post_title = "<a href=\"" . get_permalink($post->ID) . "\">" . html_entity_decode($post->post_title, ENT_QUOTES) . "</a>";
		$this->permalink = get_permalink($post->ID);
		$this->post_date = get_the_time(get_option('date_format'), $post);
		$this->post_time = get_the_time('', $post);

		$author = get_userdata($post->post_author);
		$this->authorname = html_entity_decode(apply_filters('the_author', $author->display_name), ENT_QUOTES);

		// do we send as admin, or post author?
		if ( 'author' == $this->subscribe2_options['sender'] ) {
			// get author details
			$user = &$author;
			$this->myemail = $user->user_email;
			$this->myname = html_entity_decode($user->display_name, ENT_QUOTES);
		} elseif ( 'blogname' == $this->subscribe2_options['sender'] ) {
			$this->myemail = get_option('admin_email');
			$this->myname = html_entity_decode(get_option('blogname'), ENT_QUOTES);
		} else {
			// get admin details
			$user = $this->get_userdata($this->subscribe2_options['sender']);
			$this->myemail = $user->user_email;
			$this->myname = html_entity_decode($user->display_name, ENT_QUOTES);
		}

		$this->post_cat_names = implode(', ', wp_get_object_terms($post->ID, $s2_taxonomies, array('fields' => 'names')));
		$this->post_tag_names = implode(', ', wp_get_post_tags($post->ID, array('fields' => 'names')));

		// Get email subject
		$subject = html_entity_decode(stripslashes(wp_kses($this->substitute($this->subscribe2_options['notification_subject']), '')));
		// Get the message template
		$mailtext = apply_filters('s2_email_template', $this->subscribe2_options['mailtext']);
		$mailtext = stripslashes($this->substitute($mailtext));

		$plaintext = $post->post_content;
		if ( function_exists('strip_shortcodes') ) {
			$plaintext = strip_shortcodes($plaintext);
		}
		$plaintext = preg_replace('|<s[^>]*>(.*)<\/s>|Ui','', $plaintext);
		$plaintext = preg_replace('|<strike[^>]*>(.*)<\/strike>|Ui','', $plaintext);
		$plaintext = preg_replace('|<del[^>]*>(.*)<\/del>|Ui','', $plaintext);
		$plaintext = trim(strip_tags($plaintext));

		$gallid = '[gallery id="' . $post->ID . '"';
		$content = str_replace('[gallery', $gallid, $post->post_content);

		// remove the autoembed filter to remove iframes from notification emails
		if ( get_option('embed_autourls') ) {
			global $wp_embed;
			$priority = has_filter('the_content', array(&$wp_embed, 'autoembed'));
			if ( $priority !== false ) {
				remove_filter('the_content', array(&$wp_embed, 'autoembed'), $priority);
			}
		}

		$content = apply_filters('the_content', $content);
		$content = str_replace("]]>", "]]&gt", $content);

		$excerpt = $post->post_excerpt;
		if ( '' == $excerpt ) {
			// no excerpt, is there a <!--more--> ?
			if ( false !== strpos($plaintext, '<!--more-->') ) {
				list($excerpt, $more) = explode('<!--more-->', $plaintext, 2);
				// strip leading and trailing whitespace
				$excerpt = strip_tags($excerpt);
				$excerpt = trim($excerpt);
			} else {
				// no <!--more-->, so grab the first 55 words
				$excerpt = strip_tags($plaintext);
				$words = explode(' ', $excerpt, $this->excerpt_length + 1);
				if (count($words) > $this->excerpt_length) {
					array_pop($words);
					array_push($words, '[...]');
					$excerpt = implode(' ', $words);
				}
			}
		}
		$html_excerpt = $post->post_excerpt;
		if ( '' == $html_excerpt ) {
			// no excerpt, is there a <!--more--> ?
			if ( false !== strpos($content, '<!--more-->') ) {
				list($html_excerpt, $more) = explode('<!--more-->', $content, 2);
				// balance HTML tags and then strip leading and trailing whitespace
				$html_excerpt = trim(balanceTags($html_excerpt, true));
			} else {
				// no <!--more-->, so grab the first 55 words
				$words = explode(' ', $content, $this->excerpt_length + 1);
				if (count($words) > $this->excerpt_length) {
					array_pop($words);
					array_push($words, '[...]');
					$html_excerpt = implode(' ', $words);
					// balance HTML tags and then strip leading and trailing whitespace
					$html_excerpt = trim(balanceTags($html_excerpt, true));
				} else {
					$html_excerpt = $content;
				}
			}
		}

		// remove excess white space from with $excerpt and $plaintext
		$excerpt = preg_replace('|[ ]+|', ' ', $excerpt);
		$plaintext = preg_replace('|[ ]+|', ' ', $plaintext);

		// prepare mail body texts
		$plain_excerpt_body = str_replace("{POST}", $excerpt, $mailtext);
		$plain_body = str_replace("{POST}", $plaintext, $mailtext);
		$html_body = str_replace("\r\n", "<br />\r\n", $mailtext);
		$html_body = str_replace("{POST}", $content, $html_body);
		$html_excerpt_body = str_replace("\r\n", "<br />\r\n", $mailtext);
		$html_excerpt_body = str_replace("{POST}", $html_excerpt, $html_excerpt_body);

		if ( $preview != '' ) {
			$this->myemail = $preview;
			$this->myname = __('Plain Text Excerpt Preview', 'subscribe2');
			$this->mail(array($preview), $subject, $plain_excerpt_body);
			$this->myname = __('Plain Text Full Preview', 'subscribe2');
			$this->mail(array($preview), $subject, $plain_body);
			$this->myname = __('HTML Excerpt Preview', 'subscribe2');
			$this->mail(array($preview), $subject, $html_excerpt_body, 'html');
			$this->myname = __('HTML Full Preview', 'subscribe2');
			$this->mail(array($preview), $subject, $html_body, 'html');
		} else {
			// Registered Subscribers first
			// first we send plaintext summary emails
			$recipients = $this->get_registered("cats=$post_cats_string&format=excerpt&author=$post->post_author");
			$recipients = apply_filters('s2_send_plain_excerpt_suscribers', $recipients, $post->ID);
			$this->mail($recipients, $subject, $plain_excerpt_body);

			// next we send plaintext full content emails
			$recipients = $this->get_registered("cats=$post_cats_string&format=post&author=$post->post_author");
			$recipients = apply_filters('s2_send_plain_fullcontent_suscribers', $recipients, $post->ID);
			$this->mail($recipients, $subject, $plain_body);

			// next we send html excerpt content emails
			$recipients = $this->get_registered("cats=$post_cats_string&format=html_excerpt&author=$post->post_author");
			$recipients = apply_filters('s2_send_html_excerpt_suscribers', $recipients, $post->ID);
			$this->mail($recipients, $subject, $html_excerpt_body, 'html');

			// next we send html full content emails
			$recipients = $this->get_registered("cats=$post_cats_string&format=html&author=$post->post_author");
			$recipients = apply_filters('s2_send_html_fullcontent_suscribers', $recipients, $post->ID);
			$this->mail($recipients, $subject, $html_body, 'html');

			// and finally we send to Public Subscribers
			$recipients = apply_filters('s2_send_public_suscribers', $public, $post->ID);
			$this->mail($recipients, $subject, $plain_excerpt_body, 'text');
		}
	} // end publish()

	/**
	Send confirmation email to a public subscriber
	*/
	function send_confirm($what = '', $is_remind = false) {
		if ( $this->filtered == 1 ) { return true; }
		if ( !$this->email || !$what ) { return false; }
		$id = $this->get_id($this->email);
		if ( !$id ) {
			return false;
		}

		// generate the URL "?s2=ACTION+HASH+ID"
		// ACTION = 1 to subscribe, 0 to unsubscribe
		// HASH = wp_hash of email address
		// ID = user's ID in the subscribe2 table
		// use home instead of siteurl incase index.php is not in core wordpress directory
		$link = get_option('home') . "/?s2=";

		if ( 'add' == $what ) {
			$link .= '1';
		} elseif ( 'del' == $what ) {
			$link .= '0';
		}
		$link .= wp_hash($this->email);
		$link .= $id;

		// sort the headers now so we have all substitute information
		$mailheaders = $this->headers();

		if ( $is_remind == true ) {
			$body = $this->substitute(stripslashes($this->subscribe2_options['remind_email']));
			$subject = $this->substitute(stripslashes($this->subscribe2_options['remind_subject']));
		} else {
			$body = $this->substitute(stripslashes($this->subscribe2_options['confirm_email']));
			if ( 'add' == $what ) {
				$body = str_replace("{ACTION}", $this->subscribe, $body);
				$subject = str_replace("{ACTION}", $this->subscribe, $this->subscribe2_options['confirm_subject']);
			} elseif ( 'del' == $what ) {
				$body = str_replace("{ACTION}", $this->unsubscribe, $body);
				$subject = str_replace("{ACTION}", $this->unsubscribe, $this->subscribe2_options['confirm_subject']);
			}
			$subject = html_entity_decode($this->substitute(stripslashes($subject)), ENT_QUOTES);
		}

		$body = str_replace("{LINK}", $link, $body);

		if ( $is_remind == true && function_exists('wpmq_mail') ) {
			// could be sending lots of reminders so queue them if wpmq is enabled
			@wp_mail($this->email, $subject, $body, $mailheaders, '', 0);
		} else {
			return @wp_mail($this->email, $subject, $body, $mailheaders);
		}
	} // end send_confirm()

/* ===== Public Subscriber functions ===== */
	/**
	Return an array of all the public subscribers
	*/
	function get_public($confirmed = 1) {
		global $wpdb;
		if ( 1 == $confirmed ) {
			if ( '' == $this->all_confirmed ) {
				$this->all_confirmed = $wpdb->get_col("SELECT email FROM $this->public WHERE active='1'");
			}
			return $this->all_confirmed;
		} else {
			if ( '' == $this->all_unconfirmed ) {
				$this->all_unconfirmed = $wpdb->get_col("SELECT email FROM $this->public WHERE active='0'");
			}
			return $this->all_unconfirmed;
		}
	} // end get_public()

	/**
	Given a public subscriber ID, returns the email address
	*/
	function get_email($id = 0) {
		global $wpdb;

		if ( !$id ) {
			return false;
		}
		return $wpdb->get_var($wpdb->prepare("SELECT email FROM $this->public WHERE id=%d", $id));
	} // end get_email()

	/**
	Given a public subscriber email, returns the subscriber ID
	*/
	function get_id($email = '') {
		global $wpdb;

		if ( !$email ) {
			return false;
		}
		return $wpdb->get_var($wpdb->prepare("SELECT id FROM $this->public WHERE email=%s", $email));
	} // end get_id()

	/**
	Add an public subscriber to the subscriber table
	If added by admin it is immediately confirmed, otherwise as unconfirmed
	*/
	function add($email = '', $confirm = false) {
		if ( $this->filtered == 1 ) { return; }
		global $wpdb;

		if ( !is_email($email) ) { return false; }

		if ( false !== $this->is_public($email) ) {
			// is this an email for a registered user
			$check = $wpdb->get_var($wpdb->prepare("SELECT user_email FROM $wpdb->users WHERE user_email=%s", $this->email));
			if ( $check ) { return; }
			if ( $confirm ) {
				$wpdb->query($wpdb->prepare("UPDATE $this->public SET active='1', ip=%s WHERE CAST(email as binary)=%s", $this->ip, $email));
			} else {
				$wpdb->query($wpdb->prepare("UPDATE $this->public SET date=CURDATE(), time=CURTIME() WHERE CAST(email as binary)=%s", $email));
			}
		} else {
			if ( $confirm ) {
				global $current_user;
				$wpdb->query($wpdb->prepare("INSERT INTO $this->public (email, active, date, time, ip) VALUES (%s, %d, CURDATE(), CURTIME(), %s)", $email, 1, $current_user->user_login));
			} else {
				$wpdb->query($wpdb->prepare("INSERT INTO $this->public (email, active, date, time, ip) VALUES (%s, %d, CURDATE(), CURTIME(), %s)", $email, 0, $this->ip));
			}
		}
	} // end add()

	/**
	Remove a public subscriber user from the subscription table
	*/
	function delete($email = '') {
		global $wpdb;

		if ( !is_email($email) ) { return false; }
		$wpdb->query($wpdb->prepare("DELETE FROM $this->public WHERE CAST(email as binary)=%s", $email));
	} // end delete()

	/**
	Toggle a public subscriber's status
	*/
	function toggle($email = '') {
		global $wpdb;

		if ( '' == $email || !is_email($email) ) { return false; }

		// let's see if this is a public user
		$status = $this->is_public($email);
		if ( false === $status ) { return false; }

		if ( '0' == $status ) {
			$wpdb->query($wpdb->prepare("UPDATE $this->public SET active='1', conf_date=CURDATE(), conf_time=CURTIME(), conf_ip=%s WHERE CAST(email as binary)=%s", $this->ip, $email));
		} else {
			$wpdb->query($wpdb->prepare("UPDATE $this->public SET active='0', conf_date=CURDATE(), conf_time=CURTIME(), conf_ip=%s WHERE CAST(email as binary)=%s", $this->ip, $email));
		}
	} // end toggle()

	/**
	Send reminder email to unconfirmed public subscribers
	*/
	function remind($emails = '') {
		if ( '' == $emails ) { return false; }

		$recipients = explode(",", $emails);
		if ( !is_array($recipients) ) { $recipients = (array)$recipients; }
		foreach ( $recipients as $recipient ) {
			$this->email = $recipient;
			$this->send_confirm('add', true);
		}
	} //end remind()

	/**
	Is the supplied email address a public subscriber?
	*/
	function is_public($email = '') {
		global $wpdb;

		if ( '' == $email ) { return false; }

		// run the query and force case sensitivity
		$check = $wpdb->get_var($wpdb->prepare("SELECT active FROM $this->public WHERE CAST(email as binary)=%s", $email));
		if ( '0' == $check || '1' == $check ) {
			return $check;
		} else {
			return false;
		}
	} // end is_public()

/* ===== Registered User and Subscriber functions ===== */
	/**
	Is the supplied email address a registered user of the blog?
	*/
	function is_registered($email = '') {
		global $wpdb;

		if ( '' == $email ) { return false; }

		$check = $wpdb->get_var($wpdb->prepare("SELECT user_email FROM $wpdb->users WHERE user_email=%s", $email));
		if ( $check ) {
			return true;
		} else {
			return false;
		}
	} // end is_registered()

	/**
	Return Registered User ID from email
	*/
	function get_user_id($email = '') {
		global $wpdb;

		if ( '' == $email ) { return false; }

		$id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $wpdb->users WHERE user_email=%s", $email));

		return $id;
	} // end get_user_id()

	/**
	Return an array of all subscribers emails or IDs
	*/
	function get_all_registered($return = 'email') {
		global $wpdb;

		if ( $this->s2_mu ) {
			if ( $return === 'ID' ) {
				if ( $this->all_registered_id === '' ) {
					$this->all_registered_id = $wpdb->get_col("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='" . $wpdb->prefix . "capabilities'");
				}
				return $this->all_registered_id;
			} else {
				if ( $this->all_registered_email === '' ) {
					$this->all_registered_email = $wpdb->get_col("SELECT a.user_email FROM $wpdb->users AS a INNER JOIN $wpdb->usermeta AS b ON a.ID = b.user_id WHERE b.meta_key='" . $wpdb->prefix . "capabilities'");
				}
				return $this->all_registered_email;
			}
		} else {
			if ( $return === 'ID' ) {
				if ( $this->all_registered_id === '' ) {
					$this->all_registered_id = $wpdb->get_col("SELECT ID FROM $wpdb->users");
				}
				return $this->all_registered_id;
			} else {
				if ( $this->all_registered_email === '' ) {
					$this->all_registered_email = $wpdb->get_col("SELECT user_email FROM $wpdb->users");
				}
				return $this->all_registered_email;
			}
		}
	} // end get_all_registered()

	/**
	Return an array of registered subscribers
	Collect all the registered users of the blog who are subscribed to the specified categories
	*/
	function get_registered($args = '') {
		global $wpdb;

		parse_str($args, $r);
		if ( !isset($r['format']) )
			$r['format'] = 'all';
		if ( !isset($r['cats']) )
			$r['cats'] = '';
		if ( !isset($r['author']) )
			$r['author'] = '';

		// collect all subscribers for compulsory categories
		$compulsory = explode(',', $this->subscribe2_options['compulsory']);
		foreach ( explode(',', $r['cats']) as $cat ) {
			if ( in_array($cat, $compulsory) ) {
				$r['cats'] = '';
			}
		}

		$JOIN = ''; $AND = '';
		// text or HTML subscribers
		if ( 'all' != $r['format'] ) {
			$JOIN .= "INNER JOIN $wpdb->usermeta AS b ON a.user_id = b.user_id ";
			$AND .= $wpdb->prepare(" AND b.meta_key=%s AND b.meta_value=", $this->get_usermeta_keyname('s2_format'));
			if ( 'html' == $r['format'] ) {
				$AND .= "'html'";
			} elseif ( 'html_excerpt' == $r['format'] ) {
				$AND .= "'html_excerpt'";
			} elseif ( 'post' == $r['format'] ) {
				$AND .= "'post'";
			} elseif ( 'excerpt' == $r['format'] ) {
				$AND .= "'excerpt'";
			}
		}

		// specific category subscribers
		if ( '' != $r['cats'] ) {
			$JOIN .= "INNER JOIN $wpdb->usermeta AS c ON a.user_id = c.user_id ";
			$and = '';
			foreach ( explode(',', $r['cats']) as $cat ) {
				('' == $and) ? $and = $wpdb->prepare("c.meta_key=%s", $this->get_usermeta_keyname('s2_cat') . $cat) : $and .= $wpdb->prepare(" OR c.meta_key=%s", $this->get_usermeta_keyname('s2_cat') . $cat);
			}
			$AND .= " AND ($and)";
		}

		// specific authors
		if ( '' != $r['author'] ) {
			$JOIN .= "INNER JOIN $wpdb->usermeta AS d ON a.user_id = d.user_id ";
			$AND .= $wpdb->prepare(" AND (d.meta_key=%s AND NOT FIND_IN_SET(%s, d.meta_value))", $this->get_usermeta_keyname('s2_authors'), $r['author']);
		}

		if ( $this->s2_mu ) {
			$sql = $wpdb->prepare("SELECT a.user_id FROM $wpdb->usermeta AS a INNER JOIN $wpdb->usermeta AS e ON a.user_id = e.user_id " . $JOIN . "WHERE a.meta_key='" . $wpdb->prefix . "capabilities' AND e.meta_key=%s AND e.meta_value <> ''" . $AND, $this->get_usermeta_keyname('s2_subscribed'));
		} else {
			$sql = $wpdb->prepare("SELECT a.user_id FROM $wpdb->usermeta AS a " . $JOIN . "WHERE a.meta_key=%s AND a.meta_value <> ''" . $AND, $this->get_usermeta_keyname('s2_subscribed'));
		}
		$result = $wpdb->get_col($sql);
		if ( $result ) {
			$ids = implode(',', array_map(array($this, 'prepare_in_data'), $result));
			$registered = $wpdb->get_col("SELECT user_email FROM $wpdb->users WHERE ID IN ($ids)");
		}

		if ( empty($registered) ) { return array(); }

		// apply filter to registered users to add or remove additional addresses, pass args too for additional control
		$registered = apply_filters('s2_registered_subscribers', $registered, $args);
		return $registered;
	} // end get_registered()

	/**
	Function to ensure email is compliant with internet messaging standards
	*/
	function sanitize_email($email) {
		$email = trim($email);
		if ( !is_email($email) ) { return; }

		// ensure that domain is in lowercase as per internet email standards http://www.ietf.org/rfc/rfc5321.txt
		list($name, $domain) = explode('@', $email, 2);
		return $name . "@" . strtolower($domain);
	} // end sanitize_email()

	/**
	Create the appropriate usermeta values when a user registers
	If the registering user had previously subscribed to notifications, this function will delete them from the public subscriber list first
	*/
	function register($user_ID = 0, $consent = false) {
		global $wpdb;

		if ( 0 == $user_ID ) { return $user_ID; }
		$user = get_userdata($user_ID);

		// Subscribe registered users to categories obeying excluded categories
		if ( 0 == $this->subscribe2_options['reg_override'] || 'no' == $this->subscribe2_options['newreg_override'] ) {
			$all_cats = $this->all_cats(true, 'ID');
		} else {
			$all_cats = $this->all_cats(false, 'ID');
		}

		$cats = '';
		foreach ( $all_cats as $cat ) {
			('' == $cats) ? $cats = "$cat->term_id" : $cats .= ",$cat->term_id";
		}

		if ( '' == $cats ) {
			// sanity check, might occur if all cats excluded and reg_override = 0
			return $user_ID;
		}

		// has this user previously signed up for email notification?
		if ( false !== $this->is_public($this->sanitize_email($user->user_email)) ) {
			// delete this user from the public table, and subscribe them to all the categories
			$this->delete($user->user_email);
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), $cats);
			foreach ( explode(',', $cats) as $cat ) {
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $cat, $cat);
			}
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_format'), 'excerpt');
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_autosub'), $this->subscribe2_options['autosub_def']);
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_authors'), '');
		} else {
			// create post format entries for all users
			if ( in_array($this->subscribe2_options['autoformat'], array('html', 'html_excerpt', 'post', 'excerpt')) ) {
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_format'), $this->subscribe2_options['autoformat']);
			} else {
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_format'), 'excerpt');
			}
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_autosub'), $this->subscribe2_options['autosub_def']);
			// if the are no existing subscriptions, create them if we have consent
			if (  true === $consent ) {
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), $cats);
				foreach ( explode(',', $cats) as $cat ) {
					update_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $cat, $cat);
				}
			}
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_authors'), '');
		}
		return $user_ID;
	} // end register()

	/**
	Get admin data from record 1 or first user with admin rights
	*/
	function get_userdata($admin_id) {
		global $wpdb, $userdata;

		if ( is_numeric($admin_id) ) {
			$admin = get_userdata($admin_id);
		} elseif ( $admin_id == 'admin' ) {
			//ensure compatibility with < 4.16
			$admin = get_userdata('1');
		} else {
			$admin = &$userdata;
		}

		if ( empty($admin) || $admin->ID == 0 ) {
			$role = array('role' => 'administrator');
			$wp_user_query = get_users( $role );
			$admin = $wp_user_query[0];
		}

		return $admin;
	} //end get_userdata()

	/**
	Subscribe/unsubscribe user from one-click submission
	*/
	function one_click_handler($user_ID, $action) {
		if ( !isset($user_ID) || !isset($action) ) { return; }

		$all_cats = $this->all_cats(true);

		if ( 'subscribe' == $action ) {
			// Subscribe
			$new_cats = array();
			foreach ( $all_cats as $cat ) {
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $cat->term_id, $cat->term_id);
				$new_cats[] = $cat->term_id;
			}

			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), implode(',', $new_cats));

			if ( 'yes' == $this->subscribe2_options['show_autosub'] && 'no' != get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true) ) {
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_autosub'), 'yes');
			}
		} elseif ( 'unsubscribe' == $action ) {
			// Unsubscribe
			foreach ( $all_cats as $cat ) {
				delete_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $cat->term_id);
			}

			delete_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'));
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_autosub'), 'no');
		}
	} //end one_click_handler()

/* ===== helper functions: forms and stuff ===== */
	/**
	Get an object of all categories, include default and custom type
	*/
	function all_cats($exclude = false, $orderby = 'slug') {
		$all_cats = array();
		$s2_taxonomies = apply_filters('s2_taxonomies', array('category'));

		foreach( $s2_taxonomies as $taxonomy ) {
			if ( taxonomy_exists($taxonomy) ) {
				$all_cats = array_merge($all_cats, get_categories(array('hide_empty' => false, 'orderby' => $orderby, 'taxonomy' => $taxonomy)));
			}
		}

		if ( $exclude === true ) {
			// remove excluded categories from the returned object
			$excluded = explode(',', $this->subscribe2_options['exclude']);

			// need to use $id like this as this is a mixed array / object
			$id = 0;
			foreach ( $all_cats as $cat) {
				if ( in_array($cat->term_id, $excluded) ) {
					unset($all_cats[$id]);
				}
				$id++;
			}
		}

		return $all_cats;
	} // end all_cats()

	/**
	Function to sanitise array of data for SQL
	*/
	function prepare_in_data($data) {
		global $wpdb;
		return $wpdb->prepare('%s', $data);
	} // end prepare_in_data()

	/**
	Export subscriber emails and other details to CSV
	*/
	function prepare_export( $subscribers ) {
		$subscribers = explode(",\r\n", $subscribers);
		natcasesort($subscribers);

		$exportcsv = "User Email,User Type,User Name";
		$all_cats = $this->all_cats(false, 'ID');

		foreach ($all_cats as $cat) {
			$exportcsv .= "," . $cat->cat_name;
			$cat_ids[] = $cat->term_id;
		}
		$exportcsv .= "\r\n";

		if ( !function_exists('get_userdata') ) {
			require_once(ABSPATH . WPINC . '/pluggable.php');
		}

		foreach ( $subscribers as $subscriber ) {
			if ( $this->is_registered($subscriber) ) {
				$user_ID = $this->get_user_id( $subscriber );
				$user_info = get_userdata( $user_ID );

				$cats = explode(',', get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true));
				$subscribed_cats = '';
				foreach ( $cat_ids as $cat ) {
					(in_array($cat, $cats)) ? $subscribed_cats .= ",Yes" : $subscribed_cats .= ",No";
				}

				$exportcsv .= $subscriber . ',';
				$exportcsv .= __('Registered User', 'subscribe2');
				$exportcsv .= ',' . $user_info->display_name;
				$exportcsv .= $subscribed_cats . "\r\n";
			} else {
				if ( $this->is_public($subscriber) === '1' ) {
					$exportcsv .= $subscriber . ',' . __('Confirmed Public Subscriber', 'subscribe2') . "\r\n";
				} elseif ( $this->is_public($subscriber) === '0' ) {
					$exportcsv .= $subscriber . ',' . __('Unconfirmed Public Subscriber', 'subscribe2') . "\r\n";
				}
			}
		}

		return $exportcsv;
	} // end prepare_export()

	/**
	Filter for usermeta table key names to adjust them if needed for WPMU blogs
	*/
	function get_usermeta_keyname($metaname) {
		global $wpdb;

		// Is this WordPressMU or not?
		if ( $this->s2_mu === true ) {
			switch( $metaname ) {
				case 's2_subscribed':
				case 's2_cat':
				case 's2_format':
				case 's2_autosub':
				case 's2_authors':
					return $wpdb->prefix . $metaname;
					break;
			}
		}
		// Not MU or not a prefixed option name
		return $metaname;
	} // end get_usermeta_keyname()

	/**
	Adds information to the WordPress registration screen for new users
	*/
	function register_form() {
		if ( 'no' == $this->subscribe2_options['autosub'] ) { return; }
		if ( 'wpreg' == $this->subscribe2_options['autosub'] ) {
			echo "<p>\r\n<label>";
			echo __('Check here to Subscribe to email notifications for new posts', 'subscribe2') . ":<br />\r\n";
			echo "<input type=\"checkbox\" name=\"reg_subscribe\"" . checked($this->subscribe2_options['wpregdef'], 'yes', false) . " />";
			echo "</label>\r\n";
			echo "</p>\r\n";
		} elseif ( 'yes' == $this->subscribe2_options['autosub'] ) {
			echo "<p>\r\n<center>\r\n";
			echo __('By registering with this blog you are also agreeing to receive email notifications for new posts but you can unsubscribe at anytime', 'subscribe2') . ".<br />\r\n";
			echo "</center></p>\r\n";
		}
	} // end register_form()

	/**
	Process function to add action if user selects to subscribe to posts during registration
	*/
	function register_post($user_ID = 0) {
		global $_POST;
		if ( 0 == $user_ID ) { return; }
		if ( 'yes' == $this->subscribe2_options['autosub'] || ( 'on' == $_POST['reg_subscribe'] && 'wpreg' == $this->subscribe2_options['autosub'] ) ) {
			$this->register($user_ID, true);
		} else {
			$this->register($user_ID, false);
		}
	} // end register_post()

/* ===== comment subscriber functions ===== */
	/**
	Display check box on comment page
	*/
	function s2_comment_meta_form() {
		if ( is_user_logged_in() ) {
			echo $this->profile;
		} else {
			echo "<p style=\"width: auto;\"><label><input type=\"checkbox\" name=\"s2_comment_request\" value=\"1\" " . checked($this->subscribe2_options['comment_def'], 'yes', false) . "/>" . __('Check here to Subscribe to notifications for new posts', 'subscribe2') . "</label></p>";
		}
	} // end s2_comment_meta_form()

	/**
	Process comment meta data
	*/
	function s2_comment_meta($comment_ID, $approved = 0) {
		if ( $_POST['s2_comment_request'] == '1' ) {
			switch ($approved) {
				case '0':
					// Unapproved so hold in meta data pending moderation
					add_comment_meta($comment_ID, 's2_comment_request', $_POST['s2_comment_request']);
					break;
				case '1':
					// Approved so add
					$is_public = $this->is_public($comment->comment_author_email);
					if ( $is_public == 0 ) {
						$this->toggle($comment->comment_author_email);
					}
					$is_registered = $this->is_registered($comment->comment_author_email);
					if ( !$is_public && !$is_registered ) {
						$this->add($comment->comment_author_email, true);
					}
					break;
				default :
					break;
			}
		}
	} // end s2_comment_meta()

	/**
	Action subscribe requests made on comment forms when comments are approved
	*/
	function comment_status($comment_ID = 0) {
		global $wpdb;

		// get meta data
		$subscribe = get_comment_meta($comment_ID, 's2_comment_request', true);
		if ( $subscribe != '1' ) { return $comment_ID; }

		// Retrieve the information about the comment
		$sql = $wpdb->prepare("SELECT comment_author_email, comment_approved FROM $wpdb->comments WHERE comment_ID=%s LIMIT 1", $comment_ID);
		$comment = $wpdb->get_row($sql, OBJECT);
		if ( empty($comment) ) { return $comment_ID; }

		switch ($comment->comment_approved) {
			case '0': // Unapproved
				break;
			case '1': // Approved
				$is_public = $this->is_public($comment->comment_author_email);
				if ( $is_public == 0 ) {
					$this->toggle($comment->comment_author_email);
				}
				$is_registered = $this->is_registered($comment->comment_author_email);
				if ( !$is_public && !$is_registered ) {
					$this->add($comment->comment_author_email, true);
				}
				delete_comment_meta($comment_ID, 's2_comment_request');
				break;
			default: // post is trash, spam or deleted
				delete_comment_meta($comment_ID, 's2_comment_request');
				break;
		}

		return $comment_ID;
	} // end comment_status()

/* ===== widget functions ===== */
	/**
	Register the form widget
	*/
	function subscribe2_widget() {
		require_once( S2PATH . 'include/widget.php');
		register_widget('S2_Form_widget');
	} // end subscribe2_widget()

	/**
	Register the counter widget
	*/
	function counter_widget() {
		require_once( S2PATH . 'include/counterwidget.php');
		register_widget('S2_Counter_widget');
	} // end counter_widget()

/* ===== wp-cron functions ===== */
	/**
	Add a weekly event to cron
	*/
	function add_weekly_sched($sched) {
		$sched['weekly'] = array('interval' => 604800, 'display' => __('Weekly', 'subscribe2'));
		return $sched;
	} // end add_weekly_sched()

	/**
	Send a digest of recent new posts
	*/
	function subscribe2_cron($preview = '', $resend = '') {
		if ( defined('DOING_S2_CRON') && DOING_S2_CRON ) { return; }
		define( 'DOING_S2_CRON', true );
		global $wpdb, $post;

		if ( '' == $preview ) {
			// update last_s2cron execution time before completing or bailing
			$now = current_time('mysql');
			$prev = $this->subscribe2_options['last_s2cron'];
			$last = $this->subscribe2_options['previous_s2cron'];
			$this->subscribe2_options['last_s2cron'] = $now;
			$this->subscribe2_options['previous_s2cron'] = $prev;
			if ( '' == $resend ) {
				// update sending times provided this is not a resend
				update_option('subscribe2_options', $this->subscribe2_options);
			}

			// set up SQL query based on options
			if ( $this->subscribe2_options['private'] == 'yes' ) {
				$status	= "'publish', 'private'";
			} else {
				$status = "'publish'";
			}

			// send notifications for allowed post type (defaults for posts and pages)
			// uses s2_post_types filter to allow for custom post types in WP 3.0
			if ( $this->subscribe2_options['pages'] == 'yes' ) {
				$s2_post_types = array('page', 'post');
			} else {
				$s2_post_types = array('post');
			}
			$s2_post_types = apply_filters('s2_post_types', $s2_post_types);
			foreach( $s2_post_types as $post_type ) {
				('' == $type) ? $type = $wpdb->prepare("%s", $post_type) : $type .= $wpdb->prepare(", %s", $post_type);
			}

			// collect posts
			if ( $resend == 'resend' ) {
				if ( $this->subscribe2_options['cron_order'] == 'desc' ) {
					$posts = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_excerpt, post_content, post_type, post_password, post_date, post_author FROM $wpdb->posts WHERE post_date >= %s AND post_date < %s AND post_status IN ($status) AND post_type IN ($type) ORDER BY post_date DESC", $last, $prev));
				} else {
					$posts = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_excerpt, post_content, post_type, post_password, post_date, post_author FROM $wpdb->posts WHERE post_date >= %s AND post_date < %s AND post_status IN ($status) AND post_type IN ($type) ORDER BY post_date ASC", $last, $prev));
				}
			} else {
				if ( $this->subscribe2_options['cron_order'] == 'desc' ) {
					$posts = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_excerpt, post_content, post_type, post_password, post_date, post_author FROM $wpdb->posts WHERE post_date >= %s AND post_date < %s AND post_status IN ($status) AND post_type IN ($type) ORDER BY post_date DESC", $prev, $now));
				} else {
					$posts = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_excerpt, post_content, post_type, post_password, post_date, post_author FROM $wpdb->posts WHERE post_date >= %s AND post_date < %s AND post_status IN ($status) AND post_type IN ($type) ORDER BY post_date ASC", $prev, $now));
				}
			}
		} else {
			// we are sending a preview
			$posts = get_posts('numberposts=1');
		}

		// Collect sticky posts if desired
		if ( $this->subscribe2_options['stickies'] == 'yes' ) {
			$stickies = get_posts(array('post__in' => get_option('sticky_posts')));
			if ( !empty($stickies) ) {
				$posts = array_merge((array)$stickies, (array)$posts);
			}
		}

		// do we have any posts?
		if ( empty($posts) && !has_filter('s2_digest_email') ) { return false; }
		$this->post_count = count($posts);

		// if we have posts, let's prepare the digest
		$datetime = get_option('date_format') . ' @ ' . get_option('time_format');
		$all_post_cats = array();
		$ids = array();
		$mailtext = apply_filters('s2_email_template', $this->subscribe2_options['mailtext']);
		$table = '';
		$tablelinks = '';
		$message_post= '';
		$message_posttime = '';
		foreach ( $posts as $post ) {
			// keep an array of post ids and skip if we've already done it once
			if ( in_array($post->ID, $ids) ) { continue; }
			$ids[] = $post->ID;
			$s2_taxonomies = apply_filters('s2_taxonomies', array('category'));
			$post_cats = wp_get_object_terms($post->ID, $s2_taxonomies, array('fields' => 'ids'));
			$post_cats_string = implode(',', $post_cats);
			$all_post_cats = array_unique(array_merge($all_post_cats, $post_cats));
			$check = false;
			// Pages are put into category 1 so make sure we don't exclude
			// pages if category 1 is excluded
			if ( $post->post_type != 'page' ) {
				// is the current post assigned to any categories
				// which should not generate a notification email?
				foreach ( explode(',', $this->subscribe2_options['exclude']) as $cat ) {
					if ( in_array($cat, $post_cats) ) {
						$check = true;
					}
				}
			}
			// is the current post set by the user to
			// not generate a notification email?
			$s2mail = get_post_meta($post->ID, '_s2mail', true);
			if ( strtolower(trim($s2mail)) == 'no' ) {
				$check = true;
			}
			// is the current post private
			// and should this not generate a notification email?
			if ( $this->subscribe2_options['password'] == 'no' && $post->post_password != '' ) {
				$check = true;
			}
			// is the post assigned a format that should
			// not be included in the notification email?
			$post_format = get_post_format($post->ID);
			$excluded_formats = explode(',', $this->subscribe2_options['exclude_formats']);
			if ( $post_format !== false && in_array($post_format, $excluded_formats) ) {
				$check = true;
			}
			// if this post is excluded
			// don't include it in the digest
			if ( $check ) {
				continue;
			}
			$post_title = html_entity_decode($post->post_title, ENT_QUOTES);
			('' == $table) ? $table .= "* " . $post_title : $table .= "\r\n* " . $post_title;
			('' == $tablelinks) ? $tablelinks .= "* " . $post_title : $tablelinks .= "\r\n* " . $post_title;
			$message_post .= $post_title;
			$message_posttime .= $post_title;
			if ( strstr($mailtext, "{AUTHORNAME}") ) {
				$author = get_userdata($post->post_author);
				if ( $author->display_name != '' ) {
					$message_post .= " (" . __('Author', 'subscribe2') . ": " . html_entity_decode(apply_filters('the_author', $author->display_name), ENT_QUOTES) . ")";
					$message_posttime .= " (" . __('Author', 'subscribe2') . ": " . html_entity_decode(apply_filters('the_author', $author->display_name), ENT_QUOTES) . ")";
				}
			}
			$message_post .= "\r\n";
			$message_posttime .= "\r\n";

			$message_posttime .= __('Posted on', 'subscribe2') . ": " . mysql2date($datetime, $post->post_date) . "\r\n";
			if ( strstr($mailtext, "{TINYLINK}") ) {
				$tinylink = file_get_contents('http://tinyurl.com/api-create.php?url=' . urlencode($this->get_tracking_link(get_permalink($post->ID))));
			} else {
				$tinylink = false;
			}
			if ( strstr($mailtext, "{TINYLINK}") && $tinylink !== 'Error' && $tinylink !== false ) {
				$tablelinks .= "\r\n" . $tinylink . "\r\n";
				$message_post .= $tinylink . "\r\n";
				$message_posttime .= $tinylink . "\r\n";
			} else {
				$tablelinks .= "\r\n" . $this->get_tracking_link(get_permalink($post->ID)) . "\r\n";
				$message_post .= $this->get_tracking_link(get_permalink($post->ID)) . "\r\n";
				$message_posttime .= $this->get_tracking_link(get_permalink($post->ID)) . "\r\n";
			}

			if ( strstr($mailtext, "{CATS}") ) {
				$post_cat_names = implode(', ', wp_get_object_terms($post->ID, $s2_taxonomies, array('fields' => 'names')));
				$message_post .= __('Posted in', 'subscribe2') . ": " . $post_cat_names . "\r\n";
				$message_posttime .= __('Posted in', 'subscribe2') . ": " . $post_cat_names . "\r\n";
			}
			if ( strstr($mailtext, "{TAGS}") ) {
				$post_tag_names = implode(', ', wp_get_post_tags($post->ID, array('fields' => 'names')));
				if ( $post_tag_names != '' ) {
					$message_post .= __('Tagged as', 'subscribe2') . ": " . $post_tag_names . "\r\n";
					$message_posttime .= __('Tagged as', 'subscribe2') . ": " . $post_tag_names . "\r\n";
				}
			}
			$message_post .= "\r\n";
			$message_posttime .= "\r\n";

			( !empty($post->post_excerpt) ) ? $excerpt = $post->post_excerpt : $excerpt = '';
			if ( '' == $excerpt ) {
				// no excerpt, is there a <!--more--> ?
				if ( false !== strpos($post->post_content, '<!--more-->') ) {
					list($excerpt, $more) = explode('<!--more-->', $post->post_content, 2);
					$excerpt = strip_tags($excerpt);
					if ( function_exists('strip_shortcodes') ) {
						$excerpt = strip_shortcodes($excerpt);
					}
				} else {
					$excerpt = strip_tags($post->post_content);
					if ( function_exists('strip_shortcodes') ) {
						$excerpt = strip_shortcodes($excerpt);
					}
					$words = explode(' ', $excerpt, $this->excerpt_length + 1);
					if ( count($words) > $this->excerpt_length ) {
						array_pop($words);
						array_push($words, '[...]');
						$excerpt = implode(' ', $words);
					}
				}
				// strip leading and trailing whitespace
				$excerpt = trim($excerpt);
			}
			$message_post .= $excerpt . "\r\n\r\n";
			$message_posttime .= $excerpt . "\r\n\r\n";
		}

		// we add a blank line after each post excerpt now trim white space that occurs for the last post
		$message_post = trim($message_post);
		$message_posttime = trim($message_posttime);
		// remove excess white space from within $message_post and $message_posttime
		$message_post = preg_replace('|[ ]+|', ' ', $message_post);
		$message_posttime = preg_replace('|[ ]+|', ' ', $message_posttime);
		$message_post = preg_replace("|[\r\n]{3,}|", "\r\n\r\n", $message_post);
		$message_posttime = preg_replace("|[\r\n]{3,}|", "\r\n\r\n", $message_posttime);

		// apply filter to allow external content to be inserted or content manipulated
		$message_post = apply_filters('s2_digest_email', $message_post, $now, $prev, $last, $this->subscribe2_options['cron_order']);
		$message_posttime = apply_filters('s2_digest_email', $message_posttime, $now, $prev, $last, $this->subscribe2_options['cron_order']);

		//sanity check - don't send a mail if the content is empty
		if ( !$message_post && !$message_posttime && !$table && !$tablelinks ) {
			return;
		}

		// get sender details
		if ( $this->subscribe2_options['sender'] == 'blogname' ) {
			$this->myname = html_entity_decode(get_option('blogname'), ENT_QUOTES);
			$this->myemail = get_bloginfo('admin_email');
		} else {
			$user = $this->get_userdata($this->subscribe2_options['sender']);
			$this->myemail = $user->user_email;
			$this->myname = html_entity_decode($user->display_name, ENT_QUOTES);
		}

		$scheds = (array)wp_get_schedules();
		$email_freq = $this->subscribe2_options['email_freq'];
		$display = $scheds[$email_freq]['display'];
		( '' == get_option('blogname') ) ? $subject = "" : $subject = "[" . stripslashes(html_entity_decode(get_option('blogname'), ENT_QUOTES)) . "] ";
		$subject .= $display . " " . __('Digest Email', 'subscribe2');
		$mailtext = str_replace("{TABLELINKS}", $tablelinks, $mailtext);
		$mailtext = str_replace("{TABLE}", $table, $mailtext);
		$mailtext = str_replace("{POSTTIME}", $message_posttime, $mailtext);
		$mailtext = str_replace("{POST}", $message_post, $mailtext);
		$mailtext = stripslashes($this->substitute($mailtext));

		// prepare recipients
		if ( $preview != '' ) {
			$this->myemail = $preview;
			$this->myname = __('Digest Preview', 'subscribe2');
			$this->mail(array($preview), $subject, $mailtext);
		} else {
			$public = $this->get_public();
			$all_post_cats_string = implode(',', $all_post_cats);
			$registered = $this->get_registered("cats=$all_post_cats_string");
			$recipients = array_merge((array)$public, (array)$registered);
			$this->mail($recipients, $subject, $mailtext);
		}
	} // end subscribe2_cron()

	function s2cleaner_task() {
		$unconfirmed = $this->get_public('0');
		if ( empty($unconfirmed) ) { return; }
		global $wpdb;
		$sql = "SELECT email FROM $this->public WHERE active='0' AND date < DATE_SUB(CURDATE(), INTERVAL " . $this->clean_interval . " DAY)";
		$old_unconfirmed = $wpdb->get_col( $sql );
		if ( empty($old_unconfirmed) ) {
			return;
		} else {
			foreach ($old_unconfirmed as $email) {
				$this->delete($email);
			}
		}
		return;
	} // end s2cleaner_task()

/* ===== Our constructor ===== */
	/**
	Subscribe2 constructor
	*/
	function s2init() {
		global $wpdb, $wp_version, $wpmu_version;
		// load the options
		$this->subscribe2_options = get_option('subscribe2_options');
		// if SCRIPT_DEBUG is true, use dev scripts
		$this->script_debug = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';

		// get the WordPress release number for in code version comparisons
		$tmp = explode('-', $wp_version, 2);
		$this->wp_release = $tmp[0];

		// Is this WordPressMU or not?
		if ( isset($wpmu_version) || strpos($wp_version, 'wordpress-mu') ) {
			$this->s2_mu = true;
		}
		if ( function_exists('is_multisite') && is_multisite() ) {
			$this->s2_mu = true;
		}

		// add action to handle WPMU subscriptions and unsubscriptions
		if ( $this->s2_mu === true ) {
			require_once(S2PATH . "classes/class-s2-multisite.php");
			global $s2class_multisite;
			$s2class_multisite = new s2_multisite;
			if ( isset($_GET['s2mu_subscribe']) || isset($_GET['s2mu_unsubscribe']) ) {
				add_action('init', array(&$s2class_multisite, 'wpmu_subscribe'));
			}
		}

		// load our translations
		add_action('plugins_loaded', array(&$this, 'load_translations'));

		// do we need to install anything?
		$this->public = $wpdb->prefix . "subscribe2";
		if ( $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $this->public)) != $this->public ) { $this->install(); }
		//do we need to upgrade anything?
		if ( $this->subscribe2_options === false || is_array($this->subscribe2_options) && $this->subscribe2_options['version'] !== S2VERSION ) {
			add_action('shutdown', array(&$this, 'upgrade'));
		}

		// add core actions
		add_filter('cron_schedules', array(&$this, 'add_weekly_sched'));
		// add actions for automatic subscription based on option settings
		add_action('register_form', array(&$this, 'register_form'));
		add_action('user_register', array(&$this, 'register_post'));
		if ( $this->s2_mu ) {
			add_action('add_user_to_blog', array(&$s2class_multisite, 'wpmu_add_user'), 10);
			add_action('remove_user_from_blog', array(&$s2class_multisite, 'wpmu_remove_user'), 10);
		}
		// add actions for processing posts based on per-post or cron email settings
		if ( $this->subscribe2_options['email_freq'] != 'never' ) {
			add_action('s2_digest_cron', array(&$this, 'subscribe2_cron'));
		} else {
			add_action('new_to_publish', array(&$this, 'publish'));
			add_action('draft_to_publish', array(&$this, 'publish'));
			add_action('auto-draft_to_publish', array(&$this, 'publish'));
			add_action('pending_to_publish', array(&$this, 'publish'));
			add_action('private_to_publish', array(&$this, 'publish'));
			add_action('future_to_publish', array(&$this, 'publish'));
			if ( $this->subscribe2_options['private'] == 'yes' ) {
				add_action('new_to_private', array(&$this, 'publish'));
				add_action('draft_to_private', array(&$this, 'publish'));
				add_action('auto-draft_to_private', array(&$this, 'publish'));
				add_action('pending_to_private', array(&$this, 'publish'));
			}
		}
		// add actions for comment subscribers
		if ( 'no' != $this->subscribe2_options['comment_subs'] ) {
			if ( 'before' == $this->subscribe2_options['comment_subs'] ) {
				add_action('comment_form_after_fields', array(&$this, 's2_comment_meta_form'));
			} else {
				add_action('comment_form', array(&$this, 's2_comment_meta_form'));
			}
			add_action('comment_post', array(&$this, 's2_comment_meta'), 1, 2);
			add_action('wp_set_comment_status', array(&$this, 'comment_status'));
		}
		// add action to display widget if option is enabled
		if ( '1' == $this->subscribe2_options['widget'] ) {
			add_action('widgets_init', array(&$this, 'subscribe2_widget'));
		}
		// add action to display counter widget if option is enabled
		if ( '1' == $this->subscribe2_options['counterwidget'] ) {
			add_action('widgets_init', array(&$this, 'counter_widget'));
		}

		// add action to 'clean' unconfirmed Public Subscribers
		if ( $this->clean_interval > 0 ) {
			add_action('wp_scheduled_delete', array(&$this, 's2cleaner_task'));
		}

		// Add actions specific to admin or frontend
		if ( is_admin() ) {
			// load strings
			add_action('init', array(&$this, 'load_strings'));

			//add menu, authoring and category admin actions
			add_action('admin_menu', array(&$this, 'admin_menu'));
			add_action('admin_menu', array(&$this, 's2_meta_init'));
			add_action('save_post', array(&$this, 's2_meta_handler'));
			add_action('create_category', array(&$this, 'new_category'));
			add_action('delete_category', array(&$this, 'delete_category'));

			// Add filters for Ozh Admin Menu
			if ( function_exists('wp_ozh_adminmenu') ) {
				add_filter('ozh_adminmenu_icon_s2_posts', array(&$this, 'ozh_s2_icon'));
				add_filter('ozh_adminmenu_icon_s2_users', array(&$this, 'ozh_s2_icon'));
				add_filter('ozh_adminmenu_icon_s2_tools', array(&$this, 'ozh_s2_icon'));
				add_filter('ozh_adminmenu_icon_s2_settings', array(&$this, 'ozh_s2_icon'));
			}

			// add write button
			if ( '1' == $this->subscribe2_options['show_button'] ) {
				add_action('admin_init', array(&$this, 'button_init'));
			}

			// add counterwidget css and js
			if ( '1' == $this->subscribe2_options['counterwidget'] ) {
				add_action('admin_init', array(&$this, 'widget_s2counter_css_and_js'));
			}

			// add one-click handlers
			if ( 'yes' == $this->subscribe2_options['one_click_profile'] ) {
				add_action( 'show_user_profile', array(&$this, 'one_click_profile_form') );
				add_action( 'edit_user_profile', array(&$this, 'one_click_profile_form') );
				add_action( 'personal_options_update', array(&$this, 'one_click_profile_form_save') );
				add_action( 'edit_user_profile_update', array(&$this, 'one_click_profile_form_save') );
			}

			// capture CSV export
			if ( isset($_POST['s2_admin']) && isset($_POST['csv']) ) {
				$date = date('Y-m-d');
				header("Content-Description: File Transfer");
				header("Content-type: application/octet-stream");
				header("Content-Disposition: attachment; filename=subscribe2_users_$date.csv");
				header("Pragma: no-cache");
				header("Expires: 0");
				echo $this->prepare_export($_POST['exportcsv']);
				exit(0);
			}
		} else {
			// load strings later on frontend for polylang plugin compatibility
			add_action('wp', array(&$this, 'load_strings'));

			if ( isset($_GET['s2']) ) {
				// someone is confirming a request
				if ( defined('DOING_S2_CONFIRM') && DOING_S2_CONFIRM ) { return; }
				define( 'DOING_S2_CONFIRM', true );
				add_filter('request', array(&$this, 'query_filter'));
				add_filter('the_title', array(&$this, 'title_filter'));
				add_filter('the_content', array(&$this, 'confirm'));
			}

			// add the frontend filters
			add_shortcode('subscribe2', array(&$this, 'shortcode'));
			add_filter('the_content', array(&$this, 'filter'), 10);

			// add actions for other plugins
			if ( '1' == $this->subscribe2_options['show_meta'] ) {
				add_action('wp_meta', array(&$this, 'add_minimeta'), 0);
			}

			// add actions for ajax form if enabled
			if ( '1' == $this->subscribe2_options['ajax'] ) {
				add_action('wp_enqueue_scripts', array(&$this, 'add_ajax'));
				add_action('wp_footer', array(&$this, 'add_s2_ajax'));
			}
		}
	} // end s2init()

	/**
	PHP5 Constructor
	Allows dynamic variable setting
	*/
	function __construct() {
		$this->word_wrap = apply_filters('s2_word_wrap', 80);
		$this->excerpt_length = apply_filters('s2_excerpt_length', 55);
		$this->site_switching = apply_filters('s2_allow_site_switching', false);
		$this->clean_interval = apply_filters('s2_clean_interval', 28);
	} // end __construct()

/* ===== our variables ===== */
	// cache variables
	var $subscribe2_options = array();
	var $all_confirmed = '';
	var $all_unconfirmed = '';
	var $all_registered_id = '';
	var $all_registered_email = '';
	var $all_authors = '';
	var $excluded_cats = '';
	var $post_title = '';
	var $permalink = '';
	var $post_date = '';
	var $post_time = '';
	var $myname = '';
	var $myemail = '';
	var $authorname = '';
	var $post_cat_names = '';
	var $post_tag_names = '';
	var $post_count = '';
	var $signup_dates = array();
	var $filtered = 0;
	var $preview_email = false;

	// state variables used to affect processing
	var $s2_mu = false;
	var $action = '';
	var $email = '';
	var $message = '';
	var $word_wrap;
	var $excerpt_length;
	var $site_switching;
	var $clean_interval;

	// some messages
	var $please_log_in = '';
	var $profile = '';
	var $confirmation_sent = '';
	var $already_subscribed = '';
	var $not_subscribed ='';
	var $not_an_email = '';
	var $barred_domain = '';
	var $error = '';
	var $mail_sent = '';
	var $mail_failed = '';
	var $form = '';
	var $no_such_email = '';
	var $added = '';
	var $deleted = '';
	var $subscribe = '';
	var $unsubscribe = '';
	var $confirm_subject = '';
	var $options_saved = '';
	var $options_reset = '';
} // end class subscribe2
?>