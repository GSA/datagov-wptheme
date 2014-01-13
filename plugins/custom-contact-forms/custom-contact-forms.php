<?php
/*
	Plugin Name: Custom Contact Forms
	Plugin URI: http://taylorlovett.com/wordpress-plugins
	Description: Guaranteed to be 1000X more customizable and intuitive than Fast Secure Contact Forms or Contact Form 7. Customize every aspect of your forms without any knowledge of CSS: borders, padding, sizes, colors. Ton's of great features. Required fields, form submissions saved to database, captchas, tooltip popovers, unlimited fields/forms/form styles, import/export, use a custom thank you page or built-in popover with a custom success message set for each form.
	Version: 5.1.0.3
	Author: Taylor Lovett
	Author URI: http://www.taylorlovett.com
*/

/*
	If you have time to translate this plugin in to your native language, please contact me at 
	admin@taylorlovett.com and I will add you as a contributer with your name and website to the
	Wordpress plugin page.
	
	Languages: English

	Copyright (C) 2010-2011 Taylor Lovett, taylorlovett.com (admin@taylorlovett.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

load_plugin_textdomain( 'custom-contact-forms', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

require_once('custom-contact-forms-utils.php');
new ccf_utils();
ccf_utils::load_module('db/custom-contact-forms-db.php');
if (!class_exists('CustomContactForms')) {
	class CustomContactForms extends CustomContactFormsDB {
		var $adminOptionsName = 'customContactFormsAdminOptions';
		
		function activatePlugin() {
			$admin_options = $this->getAdminOptions();
			$admin_options['show_install_popover'] = 1;
			update_option($this->getAdminOptionsName(), $admin_options);
			ccf_utils::load_module('db/custom-contact-forms-activate-db.php');
			new CustomContactFormsActivateDB();
		}
		
		function getAdminOptionsName() {
			return $this->adminOptionsName;
		}
		
		function getAdminOptions() {
			$admin_email = get_option('admin_email');
			$customcontactAdminOptions = array('show_widget_home' => 1, 'show_widget_pages' => 1, 'show_widget_singles' => 1, 'show_widget_categories' => 1, 'show_widget_archives' => 1, 'default_to_email' => $admin_email, 'default_from_email' => $admin_email, 'default_from_name' => 'Custom Contact Forms', 'default_form_subject' => __('Someone Filled Out Your Contact Form!', 'custom-contact-forms'), 
			'remember_field_values' => 0, 'enable_widget_tooltips' => 1, 'mail_function' => 'default', 'form_success_message_title' => __('Successful Form Submission', 'custom-contact-forms'), 'form_success_message' => __('Thank you for filling out our web form. We will get back to you ASAP.', 'custom-contact-forms'), 'enable_jquery' => 1, 'code_type' => 'XHTML',
			'show_install_popover' => 0, 'email_form_submissions' => 1, 'enable_dashboard_widget' => 1, 'admin_ajax' => 1, 'smtp_host' => '', 'smtp_encryption' => 'none', 'smtp_authentication' => 0, 'smtp_username' => '', 'smtp_password' => '', 'smtp_port' => '', 'default_form_error_header' => __('You filled out the form incorrectly.', 'custom-contact-forms'), 
			'default_form_bad_permissions' => __("You don't have the proper permissions to view this form.", 'custom-contact-forms'), 'enable_form_access_manager' => 0, 'dashboard_access' => 2, 'form_page_inclusion_only' => 0, 'max_file_upload_size' => 10, 'recaptcha_public_key' => '', 'recaptcha_private_key' => '' ); // default general settings
			$customcontactOptions = get_option($this->getAdminOptionsName());
			if (!empty($customcontactOptions)) {
				foreach ($customcontactOptions as $key => $option)
					$customcontactAdminOptions[$key] = $option;
			}
			update_option($this->getAdminOptionsName(), $customcontactAdminOptions);
			return $customcontactAdminOptions;
		}
		
		function langHandle() {
			if (function_exists('load_plugin_textdomain')) {
				load_plugin_textdomain('custom-contact-forms', false, dirname(plugin_basename(__FILE__)) . '/lang');
			}
		}
	}
}
$custom_contact_forms = new CustomContactForms();

/* general plugin stuff */
if (isset($custom_contact_forms)) {
	register_activation_hook(__FILE__, array(&$custom_contact_forms, 'activatePlugin'));
}

if (!is_admin()) { /* is front */
	require_once('custom-contact-forms-front.php');
	$custom_contact_front = new CustomContactFormsFront();
	if (!function_exists('serveCustomContactForm')) {
		function serveCustomContactForm($fid) {
			global $custom_contact_front;
			echo $custom_contact_front->getFormCode($custom_contact_front->selectForm($fid));
		}
	}
	add_action('init', array(&$custom_contact_front, 'frontInit'), 1);
	add_action('template_redirect', array(&$custom_contact_front, 'includeDependencies'), 1);
	//add_action('wp_enqueue_scripts', array(&$custom_contact_front, 'insertFrontEndScripts'), 1);
	//add_action('wp_print_styles', array(&$custom_contact_front, 'insertFrontEndStyles'), 1);
	add_shortcode('customcontact', array(&$custom_contact_front, 'shortCodeToForm'));
	
	add_filter('the_content', array(&$custom_contact_front, 'contentFilter'));
} else { /* is admin */
	$GLOBALS['ccf_current_page'] = (isset($_GET['page'])) ? $_GET['page'] : '';
	require_once('custom-contact-forms-admin.php');
	$custom_contact_admin = new CustomContactFormsAdmin();
	if (!function_exists('CustomContactForms_ap')) {
		function CustomContactForms_ap() {
			global $custom_contact_admin;
			if (!isset($custom_contact_admin)) return;
			if (function_exists('add_menu_page')) {
				add_menu_page(__('Custom Contact Forms', 'custom-contact-forms'), __('Custom Contact Forms', 'custom-contact-forms'), 'manage_options', 'custom-contact-forms', array(&$custom_contact_admin, 'printAdminPage'));
				add_submenu_page('custom-contact-forms', __('Custom Contact Forms', 'custom-contact-forms'), __('Custom Contact Forms', 'custom-contact-forms'), 'manage_options', 'custom-contact-forms', array(&$custom_contact_admin, 'printAdminPage'));
				add_submenu_page('custom-contact-forms', __('Saved Form Submissions', 'custom-contact-forms'), __('Saved Form Submissions', 'custom-contact-forms'), 'manage_options', 'ccf-saved-form-submissions', array(&$custom_contact_admin, 'printFormSubmissionsPage'));
				add_submenu_page('custom-contact-forms', __('General Settings', 'custom-contact-forms'), __('General Settings', 'custom-contact-forms'), 'manage_options', 'ccf-settings', array(&$custom_contact_admin, 'printSettingsPage'));
			}
		}
	}
	$admin_options = $custom_contact_admin->getAdminOptions();
	if (isset($admin_options['enable_dashboard_widget']) && $admin_options['enable_dashboard_widget'] == 1) {
		ccf_utils::load_module('widget/custom-contact-forms-dashboard.php');
		$ccf_dashboard = new CustomContactFormsDashboard();
		if ($ccf_dashboard->isDashboardPage()) {
			add_action('admin_print_styles', array(&$ccf_dashboard, 'insertDashboardStyles'), 1);
			add_action('admin_enqueue_scripts', array(&$ccf_dashboard, 'insertDashboardScripts'), 1);
		}
		add_action('wp_dashboard_setup', array(&$ccf_dashboard, 'install'));
	}
	add_action('init', array(&$custom_contact_admin, 'adminInit'), 1);
	if ($custom_contact_admin->isPluginAdminPage()) {
		add_action('admin_print_styles', array(&$custom_contact_admin, 'insertBackEndStyles'), 1);
		add_action('admin_enqueue_scripts', array(&$custom_contact_admin, 'insertAdminScripts'), 1);
	}
	add_action('wp_ajax_ccf-ajax', array(&$custom_contact_admin, 'handleAJAX'));
	add_action('wp_ajax_nopriv_ccf-ajax', array(&$custom_contact_admin, 'handleAJAX'));
	add_filter('plugin_action_links', array(&$custom_contact_admin,'appendToActionLinks'), 10, 2);
	add_action('admin_menu', 'CustomContactForms_ap');
}

/* widget stuff */
ccf_utils::load_module('widget/custom-contact-forms-widget.php');
if (!function_exists('CCFWidgetInit')) {
	function CCFWidgetInit() {
		register_widget('CustomContactFormsWidget');
	}
}
add_action('widgets_init', 'CCFWidgetInit');
