<?php
if ( !function_exists('add_action') ) {
	exit();
}

global $wpdb, $subscribers, $what, $current_tab;

// detect or define which tab we are in
$current_tab = isset( $_GET['tab'] ) ? esc_attr($_GET['tab']) : 'public';

// was anything POSTed ?
if ( isset($_POST['s2_admin']) ) {
	check_admin_referer('bulk-subscribers');
	if ( !empty($_POST['addresses']) ) {
		$sub_error = '';
		$unsub_error = '';
		foreach ( preg_split("|[\s,]+|", $_POST['addresses']) as $email ) {
			$email = $this->sanitize_email($email);
			if ( is_email($email) && $_POST['subscribe'] ) {
				if ( $this->is_public($email) !== false ) {
					('' == $sub_error) ? $sub_error = "$email" : $sub_error .= ", $email";
					continue;
				}
				$this->add($email, true);
				$message = "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Address(es) subscribed!', 'subscribe2') . "</strong></p></div>";
			} elseif ( is_email($email) && $_POST['unsubscribe'] ) {
				if ( $this->is_public($email) === false ) {
					('' == $unsub_error) ? $unsub_error = "$email" : $unsub_error .= ", $email";
					continue;
				}
				$this->delete($email);
				$message = "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Address(es) unsubscribed!', 'subscribe2') . "</strong></p></div>";
			}
		}
		if ( $sub_error != '' ) {
			echo "<div id=\"message\" class=\"error\"><p><strong>" . __('Some emails were not processed, the following were already subscribed' , 'subscribe2') . ":<br />$sub_error</strong></p></div>";
		}
		if ( $unsub_error != '' ) {
			echo "<div id=\"message\" class=\"error\"><p><strong>" . __('Some emails were not processed, the following were not in the database' , 'subscribe2') . ":<br />$unsub_error</strong></p></div>";
		}
		echo $message;
		$_POST['what'] = 'confirmed';
	} elseif ( (isset($_POST['action']) && $_POST['action'] === 'delete') || (isset($_POST['action2']) && $_POST['action2'] === 'delete') ) {
		if ( $current_tab === 'public' ) {
			foreach ( $_POST['subscriber'] as $address ) {
				$this->delete($address);
			}
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Address(es) deleted!', 'subscribe2') . "</strong></p></div>";
		} elseif ( $current_tab === 'registered' ) {
			global $current_user;
			$users_deleted_error = '';
			$users_deleted = '';
			foreach ( $_POST['subscriber'] as $address ) {
				$user = get_user_by('email', $address);
				if ( !current_user_can('delete_user', $user->ID) || $user->ID == $current_user->ID ) {
					$users_deleted_error = __('Delete failed! You cannot delete some or all of these users', 'subscribe2') . "<br />";
					continue;
				} else {
					$users_deleted = __('User(s) deleted! Any posts made by these users were assigned to you', 'subscribe2');
					//wp_delete_user($user->$id, $current_user->ID);
				}
			}
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . $users_deleted_error . $users_deleted . "</strong></p></div>";
		}
	} elseif ( (isset($_POST['action']) && $_POST['action'] === 'toggle') || (isset($_POST['action2']) && $_POST['action2'] === 'toggle') ) {
		global $current_user;
		$this->ip = $current_user->user_login;
		foreach ( $_POST['subscriber'] as $address ) {
			$this->toggle($address);
		}
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Status changed!', 'subscribe2') . "</strong></p></div>";
	} elseif ( isset($_POST['remind']) ) {
		$this->remind($_POST['reminderemails']);
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Reminder Email(s) Sent!', 'subscribe2') . "</strong></p></div>";
	} elseif ( isset($_POST['sub_categories']) && 'subscribe' == $_POST['manage'] ) {
		$this->subscribe_registered_users($_POST['exportcsv'], $_POST['category']);
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Registered Users Subscribed!', 'subscribe2') . "</strong></p></div>";
	} elseif ( isset($_POST['sub_categories']) && 'unsubscribe' == $_POST['manage'] ) {
		$this->unsubscribe_registered_users($_POST['exportcsv'], $_POST['category']);
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Registered Users Unsubscribed!', 'subscribe2') . "</strong></p></div>";
	} elseif ( isset($_POST['sub_format']) ) {
		$this->format_change( $_POST['exportcsv'], $_POST['format'] );
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Format updated for Selected Registered Users!', 'subscribe2') . "</strong></p></div>";
	} elseif ( isset($_POST['sub_digest']) ) {
		$this->digest_change( $_POST['exportcsv'], $_POST['sub_category'] );
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Digest Subscription updated for Selected Registered Users!', 'subscribe2') . "</strong></p></div>";
	}
}

if ( $current_tab == 'registered' ) {
	// Get Registered Subscribers
	$registered = $this->get_registered();
	$all_users = $this->get_all_registered();
	// safety check for our arrays
	if ( '' == $registered ) { $registered = array(); }
	if ( '' == $all_users ) { $all_users = array(); }
} else {
	//Get Public Subscribers
	$confirmed = $this->get_public();
	$unconfirmed = $this->get_public(0);
	// safety check for our arrays
	if ( '' == $confirmed ) { $confirmed = array(); }
	if ( '' == $unconfirmed ) { $unconfirmed = array(); }
}

$reminderform = false;
if ( isset($_REQUEST['what']) ) {
	if ( 'public' == $_REQUEST['what'] ) {
		$what = 'public';
		$subscribers = array_merge((array)$confirmed, (array)$unconfirmed);
	} elseif ( 'confirmed' == $_REQUEST['what'] ) {
		$what = 'confirmed';
		$subscribers = $confirmed;
	} elseif ( 'unconfirmed' == $_REQUEST['what'] ) {
		$what = 'unconfirmed';
		$subscribers = $unconfirmed;
		if ( !empty($subscribers) ) {
			$reminderemails = implode(",", $subscribers);
			$reminderform = true;
		}
	} elseif ( is_numeric($_REQUEST['what']) ) {
		$what = intval($_REQUEST['what']);
		$subscribers = $this->get_registered("cats=$what");
	} elseif ( 'registered' == $_REQUEST['what'] ) {
		$what = 'registered';
		$subscribers = $registered;
	} elseif ( 'all_users' == $_REQUEST['what'] ) {
		$what = 'all_users';
		$subscribers = $all_users;
	}
} else {
	if ( $current_tab === 'public' ) {
		$what = 'public';
		$subscribers = array_merge((array)$confirmed, (array)$unconfirmed);
	} else {
		$what = 'all_users';
		$subscribers = $all_users;
	}
}

if ( !empty($_REQUEST['s']) ) {
	if ( !empty($_POST['s']) ) {
		foreach ( $subscribers as $subscriber ) {
			if ( is_numeric(stripos($subscriber, $_POST['s'])) ) {
				$result[] = $subscriber;
			}
		}
		$subscribers = $result;
	} else {
		foreach ( $subscribers as $subscriber ) {
			if ( is_numeric(stripos($subscriber, $_REQUEST['s'])) ) {
				$result[] = $subscriber;
			}
		}
		$subscribers = $result;
	}
}

if ( !class_exists('WP_List_Table') ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if ( !class_exists('Subscribe2_List_Table') ) {
	require_once( S2PATH . 'classes/class-s2-list-table.php' );
}

// Instantiate and prepare our table data - this also runs the bulk actions
$S2ListTable = new Subscribe2_List_Table();
$S2ListTable->prepare_items();

// show our form
echo "<div class=\"wrap\">";
echo "<div id=\"icon-tools\" class=\"icon32\"></div>";
$tabs = array('public' => __('Public Subscribers', 'subscribe2'), 'registered' => __('Registered Subscribers', 'subscribe2'));
echo "<h2 class=\"nav-tab-wrapper\">";
foreach ( $tabs as $tab_key => $tab_caption ) {
	$active = ($current_tab == $tab_key) ? "nav-tab-active" : "";
	echo "<a class=\"nav-tab " . $active . "\" href=\"?page=s2_tools&amp;tab=" . $tab_key . "\">" . $tab_caption . "</a>";
}
echo "</h2>";
echo "<form method=\"post\">\r\n";
echo "<input type=\"hidden\" name=\"s2_admin\" />\r\n";
switch ($current_tab) {
	case 'public':
		echo "<div class=\"s2_admin\" id=\"s2_add_subscribers\">\r\n";
		echo "<h2>" . __('Add/Remove Subscribers', 'subscribe2') . "</h2>\r\n";
		echo "<p>" . __('Enter addresses, one per line or comma-separated', 'subscribe2') . "<br />\r\n";
		echo "<textarea rows=\"2\" cols=\"80\" name=\"addresses\"></textarea></p>\r\n";
		echo "<input type=\"hidden\" name=\"s2_admin\" />\r\n";
		echo "<p class=\"submit\" style=\"border-top: none;\"><input type=\"submit\" class=\"button-primary\" name=\"subscribe\" value=\"" . __('Subscribe', 'subscribe2') . "\" />";
		echo "&nbsp;<input type=\"submit\" class=\"button-primary\" name=\"unsubscribe\" value=\"" . __('Unsubscribe', 'subscribe2') . "\" /></p>\r\n";
		echo "</div>\r\n";

		// subscriber lists
		echo "<div class=\"s2_admin\" id=\"s2_current_subscribers\">\r\n";
		echo "<h2>" . __('Current Subscribers', 'subscribe2') . "</h2>\r\n";
		echo "<br />";
		$cats = $this->all_cats();
		$cat_ids = array();
		foreach ( $cats as $cat) {
			$cat_ids[] = $cat->term_id;
		}
		$exclude = array_merge(array('all', 'all_users', 'registered'), $cat_ids);
		break;

	case 'registered':
		echo "<div class=\"s2_admin\" id=\"s2_add_subscribers\">\r\n";
		echo "<h2>" . __('Add/Remove Subscribers', 'subscribe2') . "</h2>\r\n";
		echo "<p class=\"submit\" style=\"border-top: none;\"><a class=\"button-primary\" href=\"" . admin_url() . "user-new.php\">" . __('Add Registered User', 'subscribe2') . "</a></p>\r\n";

		echo "</div>\r\n";

		// subscriber lists
		echo "<div class=\"s2_admin\" id=\"s2_current_subscribers\">\r\n";
		echo "<h2>" . __('Current Subscribers', 'subscribe2') . "</h2>\r\n";
		echo "<br />";
		$exclude = array('all', 'public', 'confirmed', 'unconfirmed');
		break;
}

// show the selected subscribers
echo "<table style=\"width: 100%; border-collapse: separate; border-spacing: 0px; *border-collapse: expression('separate', cellSpacing = '0px');\"><tr>";
echo "<td style=\"width: 50%; text-align: left;\">";
$this->display_subscriber_dropdown($what, __('Filter', 'subscribe2'), $exclude);
echo "</td>\r\n";
if ( $reminderform ) {
	echo "<td style=\"width: 25%; text-align: right;\"><input type=\"hidden\" name=\"reminderemails\" value=\"" . $reminderemails . "\" />\r\n";
	echo "<input type=\"submit\" class=\"button-secondary\" name=\"remind\" value=\"" . __('Send Reminder Email', 'subscribe2') . "\" /></td>\r\n";
} else {
	echo "<td style=\"width: 25%;\"></td>";
}
if ( !empty($subscribers) ) {
	$exportcsv = implode(",\r\n", $subscribers);
	echo "<td style=\"width: 25%; text-align: right;\"><input type=\"hidden\" name=\"exportcsv\" value=\"" . $exportcsv . "\" />\r\n";
	echo "<input type=\"submit\" class=\"button-secondary\" name=\"csv\" value=\"" . __('Save Emails to CSV File', 'subscribe2') . "\" /></td>\r\n";
} else {
	echo "<td style=\"width: 25%;\"></td>";
}
echo "</tr></table>";

// output our subscriber table
$S2ListTable->search_box(__('Search', 'subscribe2'), 'search_id');
$S2ListTable->display();
echo "</div>\r\n";

// show bulk managment form if filtered in some Registered Users
if ( $current_tab === 'registered' ) {
	echo "<div class=\"s2_admin\" id=\"s2_bulk_manage\">\r\n";
	echo "<h2>" . __('Bulk Management', 'subscribe2') . "</h2>\r\n";
	if ( $this->subscribe2_options['email_freq'] == 'never' ) {
		echo __('Preferences for Registered Users selected in the filter above can be changed using this section.', 'subscribe2') . "<br />\r\n";
		echo "<strong><em style=\"color: red\">" . __('Consider User Privacy as changes cannot be undone', 'subscribe2') . "</em></strong><br />\r\n";
		echo "<br />" . __('Action to perform', 'subscribe2') . ":\r\n";
		echo "<label><input type=\"radio\" name=\"manage\" value=\"subscribe\" checked=\"checked\" /> " . __('Subscribe', 'subscribe2') . "</label>&nbsp;&nbsp;\r\n";
		echo "<label><input type=\"radio\" name=\"manage\" value=\"unsubscribe\" /> " . __('Unsubscribe', 'subscribe2') . "</label><br /><br />\r\n";
		$this->display_category_form();
		echo "<p class=\"submit\"><input type=\"submit\" class=\"button-primary\" name=\"sub_categories\" value=\"" . __('Bulk Update Categories', 'subscribe2') . "\" /></p>";
		echo "<br />" . __('Send email as', 'subscribe2') . ":\r\n";
		echo "<label><input type=\"radio\" name=\"format\" value=\"html\" /> " . __('HTML - Full', 'subscribe2') . "</label>&nbsp;&nbsp;\r\n";
		echo "<label><input type=\"radio\" name=\"format\" value=\"html_excerpt\" /> " . __('HTML - Excerpt', 'subscribe2') . "</label>&nbsp;&nbsp;\r\n";
		echo "<label><input type=\"radio\" name=\"format\" value=\"post\" /> " . __('Plain Text - Full', 'subscribe2') . "</label>&nbsp;&nbsp;\r\n";
		echo "<label><input type=\"radio\" name=\"format\" value=\"excerpt\" checked=\"checked\" /> " . __('Plain Text - Excerpt', 'subscribe2') . "</label>\r\n";
		echo "<p class=\"submit\"><input type=\"submit\" class=\"button-primary\" name=\"sub_format\" value=\"" . __('Bulk Update Format', 'subscribe2') . "\" /></p>";
	} else {
		echo __('Preferences for Registered Users selected in the filter above can be changed using this section.', 'subscribe2') . "<br />\r\n";
		echo "<strong><em style=\"color: red\">" . __('Consider User Privacy as changes cannot be undone', 'subscribe2') . "</em></strong><br />\r\n";
		echo "<br />" . __('Subscribe Selected Users to recieve a periodic digest notification', 'subscribe2') . ":\r\n";
		echo "<label><input type=\"radio\" name=\"sub_category\" value=\"digest\" checked=\"checked\" /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;\r\n";
		echo "<label><input type=\"radio\" name=\"sub_category\" value=\"-1\" /> ";
		echo __('No', 'subscribe2') . "</label>";
		echo "<p class=\"submit\"><input type=\"submit\" class=\"button-primary\" name=\"sub_digest\" value=\"" . __('Bulk Update Digest Subscription', 'subscribe2') . "\" /></p>";
	}
	echo "</div>\r\n";
}
echo "</form></div>\r\n";

include(ABSPATH . 'wp-admin/admin-footer.php');
// just to be sure
die;
?>