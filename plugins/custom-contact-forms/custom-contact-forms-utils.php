<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/
if (!class_exists('ccf_utils')) {
	class ccf_utils {
		function ccf_utils() {
			$this->defineConstants();
		}
		
		function redirect($location) {
			if (!empty($location)) {
				wp_redirect($location);
				exit();
			}
		}
		
		function load_module($path, $required = true) {
			if (empty($path)) return false;
			if ($required) require_once('modules/' . $path);
			else include_once('modules/' . $path);
			return true;
		}
		
		function encodeOption($option) {
			return htmlspecialchars(stripslashes($option), ENT_QUOTES);
		}
		
		function startSession() {
			if (!@session_id()) @session_start();
		}
		
		function getWPTablePrefix() {
			global $wpdb;
			return $wpdb->prefix;
		}
		
		function encodeOptionArray($option_array) {
			foreach ($option_array as $option) {
				if (is_array($option))
					$option = ccf_utils::encodeOptionArray($option);
				else
					$option = ccf_utils::encodeOption($option);
			}
			return $option_array;
		}
		
		function decodeOption($option, $strip_slashes = 1, $decode_html_chars = 1) {
			if ($strip_slashes == 1) $option = stripslashes($option);
			if ($decode_html_chars == 1) $option = html_entity_decode($option);
			return $option;
		}
		
		function defineConstants() {
			$prefix = ccf_utils::getWPTablePrefix();
			define('CCF_AJAX_URL', admin_url('admin-ajax.php'));
			define('CCF_FORMS_TABLE', $prefix . 'customcontactforms_forms');
			define('CCF_FIELDS_TABLE', $prefix . 'customcontactforms_fields');
			define('CCF_STYLES_TABLE', $prefix . 'customcontactforms_styles');
			define('CCF_USER_DATA_TABLE', $prefix . 'customcontactforms_user_data');
			define('CCF_FIELD_OPTIONS_TABLE', $prefix . 'customcontactforms_field_options');
			define('CCF_BASE_PATH', ABSPATH . 'wp-content/plugins/custom-contact-forms/');
			define('CCF_DEAD_STATE_VALUE', 'ccf-dead-state');
			$GLOBALS['ccf_tables_array'] = array(CCF_FORMS_TABLE, CCF_FIELDS_TABLE, CCF_STYLES_TABLE, CCF_USER_DATA_TABLE, CCF_FIELD_OPTIONS_TABLE);
			$GLOBALS['ccf_fixed_fields'] = array('customcontactforms_submit' => '', 
							'fid' => '',
							'recaptcha_challenge_field' => '',
							'recaptcha_response_field' => '',
							'fixedEmail' => __("Use this field if you want the plugin to throw an error on fake emails.", 'custom-contact-forms'), 
							'fixedWebsite' => __("This field will throw an error on invalid website addresses.", 'custom-contact-forms'), 
							'emailSubject' => __("This field lets users specify the subject of the email sent to you on submission.", 'custom-contact-forms'), 
							'form_page' => '', 
							'captcha' => __("This field requires users to type numbers in an image preventing spam.", 'custom-contact-forms'), 
							'recaptcha' => __( 'This field requires users to enter text from an image using reCaptcha. reCaptcha is a free anti-bot service that helps digitize books. This will only work if you specify reCaptcha public and private keys in general settings.', 'custom-contact-forms' ), 
							'ishuman' => __("This field requires users to check a box to prove they aren't a spam bot.", 'custom-contact-forms'),
							'usaStates' => __("This is a dropdown field showing each state in the US. If you want a state initially selected, enter it in 'Initial Value.'", 'custom-contact-forms'),
							'datePicker' => __("This field displays a text box that when clicked pops up an interactive calender.'", 'custom-contact-forms'),
							'allCountries' => __("This is a dropdown field showing countries. If you want a country initially selected, enter it in 'Initial Value.'", 'custom-contact-forms'),
							'resetButton' => __("This field lets users reset all form fields to their initial values. This will be inserted next to the submit button.", 'custom-contact-forms'),
							'MAX_FILE_SIZE' => ''
							);
		}
	}
}
?>