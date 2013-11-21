<?php
class s2class_upgrade {
	function upgrade_core() {
		// let's take the time to double check data for registered users
		global $mysubscribe2;
		if ( version_compare($mysubscribe2->wp_release, '3.5', '<') ) {
			global $wpdb;
			$users = $wpdb->get_col($wpdb->prepare("SELECT ID from $wpdb->users WHERE ID NOT IN (SELECT user_id FROM $wpdb->usermeta WHERE meta_key=%s)", $mysubscribe2->get_usermeta_keyname('s2_format')));
			if ( !empty($users) ) {
				foreach ($users as $user_ID) {
					$mysubscribe2->register($user_ID);
				}
			}
		} else {
			$args = array(
				'meta_query' => array(
					array('key' => $mysubscribe2->get_usermeta_keyname('s2_format'), 'compare' => 'NOT EXISTS')
				)
			);

			$user_query = new WP_User_Query( $args );
			$users = $user_query->get_results();
			if ( !empty($users) ) {
				foreach ($users as $user) {
					$mysubscribe2->register($user->ID);
				}
			}
		}
	} // end upgrade_core()

	function upgrade23() {
		global $mysubscribe2, $wpdb;

		// include upgrade-functions for maybe_add_column;
		if ( !function_exists('maybe_add_column') ) {
			require_once(ABSPATH . 'wp-admin/install-helper.php');
		}
		$date = date('Y-m-d');
		maybe_add_column($mysubscribe2->public, 'date', "ALTER TABLE $mysubscribe2->public ADD date DATE DEFAULT '$date' NOT NULL AFTER active");

		// update the options table to serialized format
		$old_options = $wpdb->get_col("SELECT option_name from $wpdb->options where option_name LIKE 's2%' AND option_name != 's2_future_posts'");

		if ( !empty($old_options) ) {
			foreach ( $old_options as $option ) {
				$value = get_option($option);
				$option_array = substr($option, 3);
				$mysubscribe2->subscribe2_options[$option_array] = $value;
				delete_option($option);
			}
		}
	} // end upgrade23()

	function upgrade51() {
		global $mysubscribe2;

		// include upgrade-functions for maybe_add_column;
		if ( !function_exists('maybe_add_column') ) {
			require_once(ABSPATH . 'wp-admin/install-helper.php');
		}
		maybe_add_column($mysubscribe2->public, 'ip', "ALTER TABLE $mysubscribe2->public ADD ip char(64) DEFAULT 'admin' NOT NULL AFTER date");
	} // end upgrade51()

	function upgrade56(){
		global $mysubscribe2;
		// correct autoformat to upgrade from pre 5.6
		if ( $mysubscribe2->subscribe2_options['autoformat'] == 'text' ) {
			$mysubscribe2->subscribe2_options['autoformat'] = 'excerpt';
		}
		if ( $mysubscribe2->subscribe2_options['autoformat'] == 'full' ) {
			$mysubscribe2->subscribe2_options['autoformat'] = 'post';
		}
	} // end upgrade56()

	function upgrade59() {
		global $mysubscribe2, $wpdb;
		// ensure existing public subscriber emails are all sanitized
		$confirmed = $mysubscribe2->get_public();
		$unconfirmed = $mysubscribe2->get_public(0);
		$public_subscribers = array_merge((array)$confirmed, (array)$unconfirmed);

		foreach ( $public_subscribers as $email ) {
			$new_email = $mysubscribe2->sanitize_email($email);
			if ( $email !== $new_email ) {
				$wpdb->get_results($wpdb->prepare("UPDATE $mysubscribe2->public SET email=%s WHERE CAST(email as binary)=%s", $new_email, $email));
			}
		}
	} // end upgrade59()

	function upgrade64() {
		global $mysubscribe2;
		// change old CAPITALISED keywords to those in {PARENTHESES}; since version 6.4
		$keywords = array('BLOGNAME', 'BLOGLINK', 'TITLE', 'POST', 'POSTTIME', 'TABLE', 'TABLELINKS', 'PERMALINK', 'TINYLINK', 'DATE', 'TIME', 'MYNAME', 'EMAIL', 'AUTHORNAME', 'LINK', 'CATS', 'TAGS', 'COUNT', 'ACTION');
		$keyword = implode('|', $keywords);
		$regex = '/(?<!\{)\b('.$keyword.')\b(?!\{)/xm';
		$replace = '{\1}';
		$mysubscribe2->subscribe2_options['mailtext'] = preg_replace($regex, $replace, $mysubscribe2->subscribe2_options['mailtext']);
		$mysubscribe2->subscribe2_options['notification_subject'] = preg_replace($regex, $replace, $mysubscribe2->subscribe2_options['notification_subject']);
		$mysubscribe2->subscribe2_options['confirm_email'] = preg_replace($regex, $replace, $mysubscribe2->subscribe2_options['confirm_email']);
		$mysubscribe2->subscribe2_options['confirm_subject'] = preg_replace($regex, $replace, $mysubscribe2->subscribe2_options['confirm_subject']);
		$mysubscribe2->subscribe2_options['remind_email'] = preg_replace($regex, $replace, $mysubscribe2->subscribe2_options['remind_email']);
		$mysubscribe2->subscribe2_options['remind_subject'] = preg_replace($regex, $replace, $mysubscribe2->subscribe2_options['remind_subject']);

		if ( version_compare($mysubscribe2->wp_release, '3.5', '<') ) {
			$users = $mysubscribe2->get_all_registered('ID');
			foreach ( $users as $user_ID ) {
				$check_format = get_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_format'), true);
				// if user is already registered update format remove 's2_excerpt' field and update 's2_format'
				if ( 'html' == $check_format ) {
					delete_user_meta($user_ID, 's2_excerpt');
				} elseif ( 'text' == $check_format ) {
					update_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_format'), get_user_meta($user_ID, 's2_excerpt'));
					delete_user_meta($user_ID, 's2_excerpt');
				}
				$subscribed = get_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_subscribed'), true);
				if ( strstr($subscribed, '-1') ) {
					// make sure we remove '-1' from any settings
					$old_cats = explode(',', $subscribed);
					$pos = array_search('-1', $old_cats);
					unset($old_cats[$pos]);
					$cats = implode(',', $old_cats);
					update_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_subscribed'), $cats);
				}
			}
		} else {
			$args = array(
				'relation' => 'AND',
				'meta_query' => array(
					array('key' => $mysubscribe2->get_usermeta_keyname('s2_format'),
					'value' => 'html')
				),
				'meta_query' => array(
					array('key' => 's2_excerpt',
					'compare' => 'EXISTS')
				)
			);

			$user_query = new WP_User_Query( $args );
			$users = $user_query->get_results();
			if ( !empty($users) ) {
				foreach ($users as $user) {
					delete_user_meta($user->ID, 's2_excerpt');
				}
			}

			$args = array(
				'relation' => 'AND',
				'meta_query' => array(
					array('key' => $mysubscribe2->get_usermeta_keyname('s2_format'),
					'value' => 'text')
				),
				'meta_query' => array(
					array('key' => 's2_excerpt',
					'compare' => 'EXISTS')
				)
			);

			$user_query = new WP_User_Query( $args );
			$users = $user_query->get_results();
			if ( !empty($users) ) {
				foreach ($users as $user) {
					update_user_meta($user->ID, $mysubscribe2->get_usermeta_keyname('s2_format'), get_user_meta($user->ID, 's2_excerpt'));
					delete_user_meta($user->ID, 's2_excerpt');
				}
			}

			$args = array(
				'meta_query' => array(
					array('key' => $mysubscribe2->get_usermeta_keyname('s2_subscribed'),
					'value' => '-1',
					'compare' => 'LIKE')
				)
			);

			$user_query = new WP_User_Query( $args );
			$users = $user_query->get_results();
			if ( !empty($users) ) {
				foreach ($users as $user) {
					$subscribed = get_user_meta($user->ID, $mysubscribe2->get_usermeta_keyname('s2_subscribed'), true);
					$old_cats = explode(',', $subscribed);
					$pos = array_search('-1', $old_cats);
					unset($old_cats[$pos]);
					$cats = implode(',', $old_cats);
					update_user_meta($user->ID, $mysubscribe2->get_usermeta_keyname('s2_subscribed'), $cats);
				}
			}
		}

		// upgrade old wpmu user meta data to new
		if ( $mysubscribe2->s2_mu === true ) {
			global $s2class_multisite, $wpdb;
			$s2class_multisite->namechange_subscribe2_widget();
			// loop through all users
			foreach ( $users as $user_ID ) {
				// get categories which the user is subscribed to (old ones)
				$categories = get_user_meta($user_ID, 's2_subscribed', true);
				$categories = explode(',', $categories);
				$format = get_user_meta($user_ID, 's2_format', true);
				$autosub = get_user_meta($user_ID, 's2_autosub', true);

				// load blogs of user (only if we need them)
				$blogs = array();
				if ( count($categories) > 0 && !in_array('-1', $categories) ) {
					$blogs = get_blogs_of_user($user_ID, true);
				}

				foreach ( $blogs as $blog ) {
					switch_to_blog($blog->userblog_id);

					$blog_categories = (array)$wpdb->get_col("SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'category'");
					$subscribed_categories = array_intersect($categories, $blog_categories);
					if ( !empty($subscribed_categories) ) {
						foreach ( $subscribed_categories as $subscribed_category ) {
							update_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_cat') . $subscribed_category, $subscribed_category);
						}
						update_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_subscribed'), implode(',', $subscribed_categories));
					}
					if ( !empty($format) ) {
						update_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_format'), $format);
					}
					if ( !empty($autosub) ) {
						update_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_autosub'), $autosub);
					}
					restore_current_blog();
				}

				// delete old user meta keys
				delete_user_meta($user_ID, 's2_subscribed');
				delete_user_meta($user_ID, 's2_format');
				delete_user_meta($user_ID, 's2_autosub');
				foreach ( $categories as $cat ) {
					delete_user_meta($user_ID, 's2_cat' . $cat);
				}
			}
		}
	} // end upgrade64()

	function upgrade70() {
		global $mysubscribe2;
		if ( version_compare($mysubscribe2->wp_release, '3.5', '<') ) {
			$users = $mysubscribe2->get_all_registered('ID');
			foreach ( $users as $user_ID ) {
				$check_authors = get_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_authors'), true);
				if ( empty($check_authors) ) {
					update_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_authors'), '');
				}
			}
		} else {
			$args = array(
				'meta_query' => array(
					array('key' => $mysubscribe2->get_usermeta_keyname('s2_authors'), 'compare' => 'NOT EXISTS')
				)
			);

			$user_query = new WP_User_Query( $args );
			$users = $user_query->get_results();
			if ( !empty($users) ) {
				foreach ($users as $user) {
					update_user_meta($user->ID, $mysubscribe2->get_usermeta_keyname('s2_authors'), '');
				}
			}
		}
	} // end upgrade70()

	function upgrade85() {
		global $mysubscribe2, $wpdb;

		// include upgrade-functions for maybe_add_column;
		if ( !function_exists('maybe_add_column') ) {
			require_once(ABSPATH . 'wp-admin/install-helper.php');
		}
		maybe_add_column($mysubscribe2->public, 'time', "ALTER TABLE $mysubscribe2->public ADD time TIME DEFAULT '00:00:00' NOT NULL AFTER date");

		// update postmeta field to a protected name, from version 8.5
		$wpdb->query( "UPDATE $wpdb->postmeta SET meta_key = '_s2mail' WHERE meta_key = 's2mail'" );
	} // end upgrade85()

	function upgrade86() {
		global $mysubscribe2, $wpdb;

		// include upgrade-functions for maybe_add_column;
		if ( !function_exists('maybe_add_column') ) {
			require_once(ABSPATH . 'wp-admin/install-helper.php');
		}
		maybe_add_column($mysubscribe2->public, 'conf_date', "ALTER TABLE $mysubscribe2->public ADD conf_date DATE AFTER ip");
		maybe_add_column($mysubscribe2->public, 'conf_time', "ALTER TABLE $mysubscribe2->public ADD conf_time TIME AFTER conf_date");
		maybe_add_column($mysubscribe2->public, 'conf_ip', "ALTER TABLE $mysubscribe2->public ADD conf_ip char(64) AFTER conf_time");

		// remove unnecessary table data
		$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = 's2_cat'" );

		$sql = "SELECT ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ( $wpdb->users.ID = $wpdb->usermeta.user_id) WHERE ( $wpdb->usermeta.meta_key = '" . $mysubscribe2->get_usermeta_keyname('s2_subscribed') . "' AND $wpdb->usermeta.meta_value LIKE ',%' )";
		$users = $wpdb->get_results($sql);
		foreach ( $users as $user ) {
			// make sure we remove leading ',' from this setting
			$subscribed = get_user_meta($user->ID, $mysubscribe2->get_usermeta_keyname('s2_subscribed'), true);
			$old_cats = explode(',', $subscribed);
			unset($old_cats[0]);
			$cats = implode(',', $old_cats);
			update_user_meta($user->ID, $mysubscribe2->get_usermeta_keyname('s2_subscribed'), $cats);
		}
	} // end upgrade86()

	function upgrade88() {
		// to ensure compulsory category collects all users we need there to be s2_subscribed meta-keys for all users
		global $mysubscribe2;

		if ( version_compare($mysubscribe2->wp_release, '3.5', '<') ) {
			$all_registered = $mysubscribe2->get_all_registered('ID');
			if ( !empty($all_registered) ) {
				foreach ( $all_registered as $user_ID ) {
					$check_subscribed = get_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_subscribed'), true);
					if ( empty($check_subscribed) ) {
						update_user_meta($user_ID, $mysubscribe2->get_usermeta_keyname('s2_subscribed'), '');
					}
				}
			}
		} else {
			$args = array(
				'meta_query' => array(
					array('key' => $mysubscribe2->get_usermeta_keyname('s2_subscribed'), 'compare' => 'NOT EXISTS')
				)
			);

			$user_query = new WP_User_Query( $args );
			$users = $user_query->get_results();
			if ( !empty($users) ) {
				foreach ($users as $user) {
					update_user_meta($user->ID, $mysubscribe2->get_usermeta_keyname('s2_subscribed'), '');
				}
			}
		}

		// check the time column again as the upgrade86() function contained a bug
		// include upgrade-functions for maybe_add_column;
		if ( !function_exists('maybe_add_column') ) {
			require_once(ABSPATH . 'wp-admin/install-helper.php');
		}
		maybe_add_column($mysubscribe2->public, 'time', "ALTER TABLE $mysubscribe2->public ADD time TIME DEFAULT '00:00:00' NOT NULL AFTER date");
	} // end upgrade88()
}
?>