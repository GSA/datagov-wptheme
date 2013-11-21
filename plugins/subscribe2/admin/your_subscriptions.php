<?php
if ( !function_exists('add_action') ) {
	exit();
}

global $user_ID, $s2nonce;

if ( isset($_GET['email']) ) {
	global $wpdb;
	$user_ID = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_email = %s", urldecode($_GET['email'])));
} else {
	get_currentuserinfo();
}

// was anything POSTed?
if ( isset($_POST['s2_admin']) && 'user' == $_POST['s2_admin'] ) {
	check_admin_referer('subscribe2-user_subscribers' . $s2nonce);

	if ( isset($_POST['submit']) ) {
		if ( isset($_POST['s2_format']) ) {
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_format'), $_POST['s2_format']);
		} else {
			// value has not been set so use default
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_format'), 'excerpt');
		}
		if ( isset($_POST['new_category']) ) {
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_autosub'), $_POST['new_category']);
		} else {
			// value has not been passed so use Settings defaults
			if ( $this->subscribe2_options['show_autosub'] == 'yes' && $this->subscribe2_options['autosub_def'] == 'yes' ) {
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_autosub'), 'yes');
			} else {
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_autosub'), 'no');
			}
		}

		$cats = ( isset($_POST['category']) ) ? $_POST['category'] : '';

		if ( empty($cats) || $cats == '-1' ) {
			$oldcats = explode(',', get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true));
			if ( $oldcats ) {
				foreach ( $oldcats as $cat ) {
					delete_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $cat);
				}
			}
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), '');
		} elseif ( $cats == 'digest' ) {
			$all_cats = $this->all_cats(false, 'ID');
			foreach ( $all_cats as $cat ) {
				('' == $catids) ? $catids = "$cat->term_id" : $catids .= ",$cat->term_id";
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $cat->term_id, $cat->term_id);
			}
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), $catids);
		} else {
			if ( !is_array($cats) ) {
				$cats = (array)$_POST['category'];
			}
			sort($cats);
			$old_cats = explode(',', get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true));
			$remove = array_diff($old_cats, $cats);
			$new = array_diff($cats, $old_cats);
			if ( !empty($remove) ) {
				// remove subscription to these cat IDs
				foreach ( $remove as $id ) {
					delete_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $id);
				}
			}
			if ( !empty($new) ) {
				// add subscription to these cat IDs
				foreach ( $new as $id ) {
					update_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $id, $id);
				}
			}
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), implode(',', $cats));
		}

		$authors = ( isset($_POST['author']) ) ? $_POST['author'] : '';
		if ( is_array($authors) ) {
			$authors = implode(',', $authors);
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_authors'), $authors);
		} elseif ( empty($authors) ) {
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_authors'), '');
		}
	} elseif ( isset($_POST['subscribe']) ) {
		$this->one_click_handler($user_ID, 'subscribe');
	} elseif ( isset($_POST['unsubscribe']) ) {
		$this->one_click_handler($user_ID, 'unsubscribe');
	}

	echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Subscription preferences updated.', 'subscribe2') . "</strong></p></div>\n";
}

// show our form
echo "<div class=\"wrap\">";
echo "<div id=\"icon-users\" class=\"icon32\"></div>";
echo "<h2>" . __('Notification Settings', 'subscribe2') . "</h2>\r\n";
if ( isset($_GET['email']) ) {
	$user = get_userdata($user_ID);
	echo "<span style=\"color: red;line-height: 300%;\">" . __('Editing Subscribe2 preferences for user', 'subscribe2') . ": " . $user->display_name . "</span>";
}
echo "<form method=\"post\">";
echo "<p>";
if ( function_exists('wp_nonce_field') ) {
	wp_nonce_field('subscribe2-user_subscribers' . $s2nonce);
}
echo "<input type=\"hidden\" name=\"s2_admin\" value=\"user\" />";
if ( $this->subscribe2_options['email_freq'] == 'never' ) {
	echo __('Receive email as', 'subscribe2') . ": &nbsp;&nbsp;";
	echo "<label><input type=\"radio\" name=\"s2_format\" value=\"html\"" . checked(get_user_meta($user_ID, $this->get_usermeta_keyname('s2_format'), true), 'html', false) . " />";
	echo " " . __('HTML - Full', 'subscribe2') ."</label>&nbsp;&nbsp;";
	echo "<label><input type=\"radio\" name=\"s2_format\" value=\"html_excerpt\"" . checked(get_user_meta($user_ID, $this->get_usermeta_keyname('s2_format'), true), 'html_excerpt', false) . " />";
	echo " " .  __('HTML - Excerpt', 'subscribe2') . "</label>&nbsp;&nbsp;";
	echo "<label><input type=\"radio\" name=\"s2_format\" value=\"post\"" . checked(get_user_meta($user_ID, $this->get_usermeta_keyname('s2_format'), true), 'post', false) . " />";
	echo " " . __('Plain Text - Full', 'subscribe2') . "</label>&nbsp;&nbsp;";
	echo "<label><input type=\"radio\" name=\"s2_format\" value=\"excerpt\"" . checked(get_user_meta($user_ID, $this->get_usermeta_keyname('s2_format'), true), 'excerpt', false) . " />";
	echo " " . __('Plain Text - Excerpt', 'subscribe2') . "</label><br /><br />\r\n";

	if ( $this->subscribe2_options['show_autosub'] == 'yes' ) {
		echo __('Automatically subscribe me to newly created categories', 'subscribe2') . ': &nbsp;&nbsp;';
		echo "<label><input type=\"radio\" name=\"new_category\" value=\"yes\"" . checked(get_user_meta($user_ID, $this->get_usermeta_keyname('s2_autosub'), true), 'yes', false) . " />";
		echo " " . __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
		echo "<label><input type=\"radio\" name=\"new_category\" value=\"no\"" . checked(get_user_meta($user_ID, $this->get_usermeta_keyname('s2_autosub'), true), 'no', false) . " />";
		echo " " . __('No', 'subscribe2') . "</label>";
		echo "</p>";
	}

	if ( $this->subscribe2_options['one_click_profile'] == 'yes' ) {
		// One-click subscribe and unsubscribe buttons
		echo "<h2>" . __('One Click Subscription / Unsubscription', 'subscribe2') . "</h2>\r\n";
		echo "<p class=\"submit\"><input type=\"submit\" class=\"button-primary\" name=\"subscribe\" value=\"" . __("Subscribe to All", 'subscribe2') . "\" />&nbsp;&nbsp;";
		echo "<input type=\"submit\" class=\"button-primary\" name=\"unsubscribe\" value=\"" . __("Unsubscribe from All", 'subscribe2') . "\" /></p>";
	}

	// subscribed categories
	if ( $this->s2_mu ) {
		global $blog_id;
		$subscribed = get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true);
		// if we are subscribed to the current blog display an "unsubscribe" link
		if ( !empty($subscribed) ) {
			$unsubscribe_link = esc_url( add_query_arg('s2mu_unsubscribe', $blog_id) );
			echo "<p><a href=\"". $unsubscribe_link ."\" class=\"button\">" . __('Unsubscribe me from this blog', 'subscribe2') . "</a></p>";
		} else {
			// else we show a "subscribe" link
			$subscribe_link = esc_url( add_query_arg('s2mu_subscribe', $blog_id) );
			echo "<p><a href=\"". $subscribe_link ."\" class=\"button\">" . __('Subscribe to all categories', 'subscribe2') . "</a></p>";
		}
		echo "<h2>" . __('Subscribed Categories on', 'subscribe2') . " " . get_option('blogname') . " </h2>\r\n";
	} else {
		echo "<h2>" . __('Subscribed Categories', 'subscribe2') . "</h2>\r\n";
	}
	('' == $this->subscribe2_options['compulsory']) ? $compulsory = array() : $compulsory = explode(',', $this->subscribe2_options['compulsory']);
	$this->display_category_form(explode(',', get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true)), $this->subscribe2_options['reg_override'], $compulsory);
} else {
	// we're doing daily digests, so just show
	// subscribe / unnsubscribe
	echo __('Receive periodic summaries of new posts?', 'subscribe2') . ': &nbsp;&nbsp;';
	echo "<label>";
	echo "<input type=\"radio\" name=\"category\" value=\"digest\"";
	if ( get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true) ) {
		echo " checked=\"checked\"";
	}
	echo " /> " . __('Yes', 'subscribe2') . "</label> <label><input type=\"radio\" name=\"category\" value=\"-1\" ";
	if ( !get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true) ) {
		echo " checked=\"checked\"";
	}
	echo " /> " . __('No', 'subscribe2');
	echo "</label></p>";
}

if ( count($this->get_authors()) > 1 && $this->subscribe2_options['email_freq'] == 'never' ) {
	echo "<div class=\"s2_admin\" id=\"s2_authors\">\r\n";
	echo "<h2>" . __('Do not send notifications for post made by these authors', 'subscribe2') . "</h2>\r\n";
	$this->display_author_form(explode(',', get_user_meta($user_ID, $this->get_usermeta_keyname('s2_authors'), true)));
	echo "</div>\r\n";
}

// submit
echo "<p class=\"submit\"><input type=\"submit\" class=\"button-primary\" name=\"submit\" value=\"" . __("Update Preferences", 'subscribe2') . " &raquo;\" /></p>";
echo "</form>\r\n";

// list of subscribed blogs on wordpress mu
if ( $this->s2_mu && !isset($_GET['email']) ) {
	global $blog_id, $current_user, $s2class_multisite;
	$s2blog_id = $blog_id;
	get_currentuserinfo();
	$blogs = $s2class_multisite->get_mu_blog_list();

	$blogs_subscribed = array();
	$blogs_notsubscribed = array();

	foreach ( $blogs as $blog ) {
		// switch to blog
		switch_to_blog($blog['blog_id']);

		// check that the Subscribe2 plugin is active on the current blog
		$current_plugins = get_option('active_plugins');
		if ( !is_array($current_plugins) ) {
			$current_plugins = (array)$current_plugins;
		}
		if ( !in_array(S2DIR . 'subscribe2.php', $current_plugins) ) {
			continue;
		}

		// check if we're subscribed to the blog
		$subscribed = get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true);

		$blogname = get_option('blogname');
		if ( strlen($blogname) > 30 ) {
			$blog['blogname'] = wp_html_excerpt($blogname, 30) . "..";
		} else {
			$blog['blogname'] = $blogname;
		}
		$blog['description'] = get_option('blogdescription');
		$blog['blogurl'] = get_option('home');
		$blog['subscribe_page'] = get_option('home') . "/wp-admin/admin.php?page=s2";

		$key = strtolower($blog['blogname'] . "-" . $blog['blog_id']);
		if ( !empty($subscribed) ) {
			$blogs_subscribed[$key] = $blog;
		} else {
			$blogs_notsubscribed[$key] = $blog;
		}
		restore_current_blog();
	}

	if ( !empty($blogs_subscribed) ) {
		ksort($blogs_subscribed);
		echo "<h2>" . __('Subscribed Blogs', 'subscribe2') . "</h2>\r\n";
		echo "<ul class=\"s2_blogs\">\r\n";
		foreach ( $blogs_subscribed as $blog ) {
			echo "<li><span class=\"name\"><a href=\"" . $blog['blogurl'] . "\" title=\"" . $blog['description'] . "\">" . $blog['blogname'] . "</a></span>\r\n";
			if ( $s2blog_id == $blog['blog_id'] ) {
				echo "<span class=\"buttons\">" . __('Viewing Settings Now', 'subscribe2') . "</span>\r\n";
			} else {
				echo "<span class=\"buttons\">";
				if ( is_user_member_of_blog($current_user->id, $blog['blog_id']) ) {
					echo "<a href=\"". $blog['subscribe_page'] . "\">" . __('View Settings', 'subscribe2') . "</a>\r\n";
				}
				echo "<a href=\"" . esc_url( add_query_arg('s2mu_unsubscribe', $blog['blog_id']) ) . "\">" . __('Unsubscribe', 'subscribe2') . "</a></span>\r\n";
			}
			echo "<div class=\"additional_info\">" . $blog['description'] . "</div>\r\n";
			echo "</li>";
		}
		echo "</ul>\r\n";
	}

	if ( !empty($blogs_notsubscribed) ) {
		ksort($blogs_notsubscribed);
		echo "<h2>" . __('Subscribe to new blogs', 'subscribe2') . "</h2>\r\n";
		echo "<ul class=\"s2_blogs\">";
		foreach ( $blogs_notsubscribed as $blog ) {
			echo "<li><span class=\"name\"><a href=\"" . $blog['blogurl'] . "\" title=\"" . $blog['description'] . "\">" . $blog['blogname'] . "</a></span>\r\n";
			if ( $s2blog_id == $blog['blog_id'] ) {
				echo "<span class=\"buttons\">" . __('Viewing Settings Now', 'subscribe2') . "</span>\r\n";
			} else {
				echo "<span class=\"buttons\">";
				if ( is_user_member_of_blog($current_user->id, $blog['blog_id']) ) {
					echo "<a href=\"". $blog['subscribe_page'] . "\">" . __('View Settings', 'subscribe2') . "</a>\r\n";
				}
				echo "<a href=\"" . esc_url( add_query_arg('s2mu_subscribe', $blog['blog_id']) ) . "\">" . __('Subscribe', 'subscribe2') . "</a></span>\r\n";
			}
			echo "<div class=\"additional_info\">" . $blog['description'] . "</div>\r\n";
			echo "</li>";
		}
		echo "</ul>\r\n";
	}
}

echo "</div>\r\n";

include(ABSPATH . 'wp-admin/admin-footer.php');
// just to be sure
die;
?>