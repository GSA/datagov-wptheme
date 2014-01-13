<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/
if (!class_exists('CustomContactFormsActivateDB')) {
	class CustomContactFormsActivateDB extends CustomContactFormsDB {
		var $cache = array();	

		function CustomContactFormsActivateDB($run_activate = true) {
			if ($run_activate)
				$this->activateDB();
		}
		
		function activateDB() {
			$this->createTables();
			$this->updateTables();
			parent::serializeAllFormFields();
			parent::serializeAllFieldOptions();
			$this->insertFixedFields();
		}
		
		function fieldsTableExists() {
			global $wpdb;
			return ($wpdb->get_var("show tables like '". CCF_FIELDS_TABLE . "'") == CCF_FIELDS_TABLE);
		}
		
		function formsTableExists() {
			global $wpdb;
			return ($wpdb->get_var("show tables like '". CCF_FORMS_TABLE . "'") == CCF_FORMS_TABLE);
		}
		
		function stylesTableExists() {
			global $wpdb;
			return ($wpdb->get_var("show tables like '". CCF_STYLES_TABLE . "'") == CCF_STYLES_TABLE);
		}
		
		function fieldOptionsTableExists() {
			global $wpdb;
			return ($wpdb->get_var("show tables like '". CCF_FIELD_OPTIONS_TABLE . "'") == CCF_FIELD_OPTIONS_TABLE);
		}
		
		function userDataTableExists() {
			global $wpdb;
			return ($wpdb->get_var("show tables like '". CCF_USER_DATA_TABLE . "'") == CCF_USER_DATA_TABLE);
		}
		
		function createTables() {
			global $wpdb;
			if(!$this->formsTableExists()) {
				$sql1 = " CREATE TABLE `".CCF_FORMS_TABLE."` (
						`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
						`form_slug` VARCHAR( 100 ) NOT NULL ,
						`form_title` VARCHAR( 200 ) NOT NULL ,
						`form_action` TEXT NOT NULL ,
						`form_method` VARCHAR( 4 ) NOT NULL ,
						`form_fields` VARCHAR( 200 ) NOT NULL ,
						`submit_button_text` VARCHAR( 200 ) NOT NULL ,
						`custom_code` TEXT NOT NULL ,
						PRIMARY KEY ( `id` )
						) ENGINE = MYISAM AUTO_INCREMENT=1 ";
				$wpdb->query($sql1);
			} if(!$this->userDataTableExists()) {
				$sql7 = " CREATE TABLE `".CCF_USER_DATA_TABLE."` (
						`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
						`data_time` INT( 11 ) NOT NULL DEFAULT '0',
						`data_formid` INT( 11 ) NOT NULL ,
						`data_formpage` VARCHAR ( 250 ) NOT NULL ,
						`data_value` LONGTEXT NOT NULL ,
						PRIMARY KEY ( `id` )
						) ENGINE = MYISAM AUTO_INCREMENT=1 ";
				$wpdb->query($sql7);
			} if(!$this->fieldOptionsTableExists()) {
				$sql5 = " CREATE TABLE `".CCF_FIELD_OPTIONS_TABLE."` (
						`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
						`option_slug` VARCHAR( 100 ) NOT NULL ,
						`option_label` VARCHAR( 200 ) NOT NULL ,
						`option_value` VARCHAR( 100 ) NOT NULL ,
						PRIMARY KEY ( `id` )
						) ENGINE = MYISAM AUTO_INCREMENT=1 ";
				$wpdb->query($sql5);
			} if(!$this->fieldsTableExists()) {
				$sql2 = "CREATE TABLE `".CCF_FIELDS_TABLE."` (
						`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
						`field_slug` VARCHAR( 50 ) NOT NULL ,
						`field_label` VARCHAR( 200 ) NOT NULL ,
						`field_type` VARCHAR( 25 ) NOT NULL ,
						`field_value` TEXT NOT NULL ,
						`field_maxlength` INT ( 5 )  NOT NULL DEFAULT '0',
						`user_field` INT ( 1 )  NOT NULL DEFAULT '1',
						PRIMARY KEY ( `id` )
						) ENGINE = MYISAM AUTO_INCREMENT=1 ";
				$wpdb->query($sql2);
			} if(!$this->stylesTableExists()) {
				$sql3 = "CREATE TABLE `".CCF_STYLES_TABLE."` (
						`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
						`style_slug` VARCHAR( 30 ) NOT NULL ,
						`input_width` VARCHAR( 10 ) NOT NULL DEFAULT '200px',
						`textarea_width` VARCHAR( 10 ) NOT NULL DEFAULT '200px',
						`textarea_height` VARCHAR( 10 ) NOT NULL DEFAULT '100px',
						`form_borderwidth` VARCHAR( 10 ) NOT NULL DEFAULT '0px',
						`label_width` VARCHAR( 10 ) NOT NULL DEFAULT '200px',
						`form_width` VARCHAR( 10 ) NOT NULL DEFAULT '100%',
						`submit_width` VARCHAR( 10 ) NOT NULL DEFAULT 'auto',
						`submit_height` VARCHAR( 10 ) NOT NULL DEFAULT '40px',
						`label_fontsize` VARCHAR( 10 ) NOT NULL DEFAULT '1em',
						`title_fontsize` VARCHAR( 10 ) NOT NULL DEFAULT '1.2em',
						`field_fontsize` VARCHAR( 10 ) NOT NULL DEFAULT '1.3em',
						`submit_fontsize` VARCHAR( 10 ) NOT NULL DEFAULT '1.1em',
						`field_bordercolor` VARCHAR( 10 ) NOT NULL DEFAULT '999999',
						`form_borderstyle` VARCHAR( 30 ) NOT NULL DEFAULT 'none',
						`form_bordercolor` VARCHAR( 20 ) NOT NULL DEFAULT '',
						`field_fontcolor` VARCHAR( 20 ) NOT NULL DEFAULT '333333',
						`label_fontcolor` VARCHAR( 20 ) NOT NULL DEFAULT '333333',
						`title_fontcolor` VARCHAR( 20 ) NOT NULL DEFAULT '333333',
						`submit_fontcolor` VARCHAR( 20 ) NOT NULL DEFAULT '333333',
						`form_fontfamily` VARCHAR( 150 ) NOT NULL DEFAULT 'Tahoma, Verdana, Arial',
						PRIMARY KEY ( `id` )
						) ENGINE = MYISAM AUTO_INCREMENT=1 ";
				$wpdb->query($sql3);
			}
			return true;
		}
		
		function updateTables() {
			global $wpdb;
			if (!$this->columnExists('user_field', CCF_FIELDS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FIELDS_TABLE . "` ADD `user_field` INT( 1 ) NOT NULL DEFAULT '1'");
			if (!$this->columnExists('form_style', CCF_FORMS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FORMS_TABLE . "` ADD `form_style` INT( 10 ) NOT NULL DEFAULT '0'");
			if (!$this->columnExists('form_email', CCF_FORMS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FORMS_TABLE . "` ADD `form_email` VARCHAR( 50 ) NOT NULL");
			if (!$this->columnExists('form_success_message', CCF_FORMS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FORMS_TABLE . "` ADD `form_success_message` TEXT NOT NULL");
			if (!$this->columnExists('form_thank_you_page', CCF_FORMS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FORMS_TABLE . "` ADD `form_thank_you_page` VARCHAR ( 200 ) NOT NULL");
			if (!$this->columnExists('field_backgroundcolor', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `field_backgroundcolor` VARCHAR( 20 ) NOT NULL DEFAULT 'f5f5f5'");
			if (!$this->columnExists('field_borderstyle', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `field_borderstyle` VARCHAR( 20 ) NOT NULL DEFAULT 'solid'");
			if (!$this->columnExists('form_success_title', CCF_FORMS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FORMS_TABLE . "` ADD `form_success_title` VARCHAR( 150 ) NOT NULL DEFAULT '".__('Form Success!', 'custom-contact-forms')."'");
			if (!$this->columnExists('form_padding', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `form_padding` VARCHAR( 20 ) NOT NULL DEFAULT '8px'");
			if (!$this->columnExists('form_margin', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `form_margin` VARCHAR( 20 ) NOT NULL DEFAULT '7px'");
			if (!$this->columnExists('title_margin', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `title_margin` VARCHAR( 20 ) NOT NULL DEFAULT '4px'");
			if (!$this->columnExists('label_margin', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `label_margin` VARCHAR( 20 ) NOT NULL DEFAULT '6px'");
			if (!$this->columnExists('textarea_backgroundcolor', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `textarea_backgroundcolor` VARCHAR( 20 ) NOT NULL DEFAULT 'f5f5f5'");
			if (!$this->columnExists('success_popover_bordercolor', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `success_popover_bordercolor` VARCHAR( 20 ) NOT NULL DEFAULT 'efefef'");
			if (!$this->columnExists('dropdown_width', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `dropdown_width` VARCHAR( 20 ) NOT NULL DEFAULT 'auto'");
			if (!$this->columnExists('success_popover_fontsize', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `success_popover_fontsize` VARCHAR( 20 ) NOT NULL DEFAULT '12px'");
			
			if (!$this->columnExists('submit_background', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `submit_background` VARCHAR ( 200 ) NOT NULL");
            if (!$this->columnExists('submit_background_repeat', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `submit_background_repeat` VARCHAR ( 25 ) NOT NULL");
			
			if (!$this->columnExists('success_popover_title_fontsize', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `success_popover_title_fontsize` VARCHAR( 20 ) NOT NULL DEFAULT '1.3em'");
			if (!$this->columnExists('success_popover_height', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `success_popover_height` VARCHAR( 20 ) NOT NULL DEFAULT '200px'");
			if (!$this->columnExists('success_popover_fontcolor', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `success_popover_fontcolor` VARCHAR( 20 ) NOT NULL DEFAULT '333333'");
			if (!$this->columnExists('success_popover_title_fontcolor', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `success_popover_title_fontcolor` VARCHAR( 20 ) NOT NULL DEFAULT '333333'");
			if (!$this->columnExists('field_instructions', CCF_FIELDS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FIELDS_TABLE . "` ADD `field_instructions` TEXT NOT NULL");
			if (!$this->columnExists('field_options', CCF_FIELDS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FIELDS_TABLE . "` ADD `field_options` TEXT NOT NULL");
			if (!$this->columnExists('field_required', CCF_FIELDS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FIELDS_TABLE . "` ADD `field_required` INT( 1 ) NOT NULL DEFAULT '0'");
			if (!$this->columnExists('form_backgroundcolor', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `form_backgroundcolor` VARCHAR( 20 ) NOT NULL DEFAULT 'ffffff'");
			if (!$this->columnExists('field_borderround', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `field_borderround` VARCHAR( 20 ) NOT NULL DEFAULT '6px'");
			if (!$this->columnExists('tooltip_backgroundcolor', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `tooltip_backgroundcolor` VARCHAR( 20 ) NOT NULL DEFAULT '000000'");
			if (!$this->columnExists('tooltip_fontsize', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `tooltip_fontsize` VARCHAR( 20 ) NOT NULL DEFAULT '12px'");
			if (!$this->columnExists('tooltip_fontcolor', CCF_STYLES_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_STYLES_TABLE . "` ADD `tooltip_fontcolor` VARCHAR( 20 ) NOT NULL DEFAULT 'ffffff'");
			if (!$this->columnExists('field_class', CCF_FIELDS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FIELDS_TABLE . "` ADD `field_class` VARCHAR( 50 ) NOT NULL");
			if (!$this->columnExists('field_error', CCF_FIELDS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FIELDS_TABLE . "` ADD `field_error` VARCHAR( 300 ) NOT NULL");
			
			if (!$this->columnExists('form_access', CCF_FORMS_TABLE)) {
				$wpdb->query("ALTER TABLE `" . CCF_FORMS_TABLE . "` ADD `form_access` TEXT NOT NULL");
				// This makes all forms accessible when upgrading from CCF versions older than 4.5.0
				$this->makeAllFormsAccessible();
			}
			if (!$this->columnExists('form_email_subject', CCF_FORMS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FORMS_TABLE . "` ADD `form_email_subject` VARCHAR(250) NOT NULL");
			if (!$this->columnExists('form_email_name', CCF_FORMS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FORMS_TABLE . "` ADD `form_email_name` VARCHAR(100) NOT NULL");
			if (!$this->columnExists('option_dead', CCF_FIELD_OPTIONS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FIELD_OPTIONS_TABLE . "` ADD `option_dead` INT( 1 ) NOT NULL DEFAULT '0'");
			if (!$this->columnExists('form_pages', CCF_FORMS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FORMS_TABLE . "` ADD `form_pages` VARCHAR(400) NOT NULL");
			if (!$this->columnExists('field_max_upload_size', CCF_FIELDS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FIELDS_TABLE . "` ADD `field_max_upload_size` INT( 11 ) NOT NULL");
			if (!$this->columnExists('field_allowed_file_extensions', CCF_FIELDS_TABLE))
				$wpdb->query("ALTER TABLE `" . CCF_FIELDS_TABLE . "` ADD `field_allowed_file_extensions` TEXT NOT NULL");
				
			$wpdb->query("ALTER TABLE `" . CCF_FORMS_TABLE . "` CHANGE `form_email` `form_email` TEXT NOT NULL");
			$wpdb->query("ALTER TABLE `" . CCF_FORMS_TABLE . "` CHANGE `form_fields` `form_fields` TEXT NOT NULL");
			$wpdb->query("ALTER TABLE `" . CCF_FIELDS_TABLE . "` CHANGE `field_label` `field_label` TEXT NOT NULL");
			$wpdb->query("ALTER TABLE `" . CCF_FIELDS_TABLE . "` CHANGE `field_options` `field_options` TEXT NOT NULL");
			$this->updateTableCharSets();
		}
		
		function updateTableCharSets() {
			global $wpdb;
			foreach ($GLOBALS['ccf_tables_array'] as $table) {
				$wpdb->query("ALTER TABLE `" . $table . "`  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
				$wpdb->query("ALTER TABLE `" . $table . "` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
			}
		}
		
		function insertFixedFields() {
			$captcha = array('field_slug' => 'captcha', 'field_label' => __('Type the numbers.', 'custom-contact-forms'), 'field_type' => 'Text', 'field_value' => '', 'field_maxlength' => '100', 'user_field' => 0, 'field_instructions' => __('Type the numbers displayed in the image above.', 'custom-contact-forms'));
			$recaptcha = array('field_slug' => 'recaptcha', 'field_label' => '', 'field_type' => 'Text', 'field_value' => '', 'field_maxlength' => '100', 'user_field' => 0, 'field_instructions' => __('Type the numbers displayed in the image above.', 'custom-contact-forms'));
			$ishuman = array('field_slug' => 'ishuman', 'field_label' => __('Check if you are human.', 'custom-contact-forms'), 'field_type' => 'Checkbox', 'field_value' => '1', 'field_maxlength' => '0', 'user_field' => 0, 'field_instructions' => __('This helps us prevent spam.', 'custom-contact-forms'));
			$fixedEmail = array('field_slug' => 'fixedEmail', 'field_required' => 1, 'field_label' => __('Your Email', 'custom-contact-forms'), 'field_type' => 'Text', 'field_value' => '', 'field_maxlength' => '100', 'user_field' => 0, 'field_instructions' => __('Please enter your email address.', 'custom-contact-forms'));
			$fixedWebsite = array('field_slug' => 'fixedWebsite', 'field_required' => 1, 'field_label' => __('Your Website', 'custom-contact-forms'), 'field_type' => 'Text', 'field_value' => '', 'field_maxlength' => '200', 'user_field' => 0, 'field_instructions' => __('Please enter your website.', 'custom-contact-forms'));
			$emailSubject = array('field_slug' => 'emailSubject', 'field_required' => 1, 'field_label' => __('Email Subject', 'custom-contact-forms'), 'field_type' => 'Text', 'field_value' => '', 'field_maxlength' => '200', 'user_field' => 0, 'field_instructions' => __('Please enter a subject for the email.', 'custom-contact-forms'));
			$reset = array('field_slug' => 'resetButton', 'field_type' => 'Reset', 'field_value' => __('Reset Form', 'custom-contact-forms'), 'user_field' => 0);
			$states = array('field_slug' => 'usaStates', 'field_label' => __('Select a State', 'custom-contact-forms'), 'field_type' => 'Dropdown', 'user_field' => 0);
			$countries = array('field_slug' => 'allCountries', 'field_label' => __('Select a Country', 'custom-contact-forms'), 'field_type' => 'Dropdown', 'user_field' => 0);
			if (!$this->fieldSlugExists('captcha'))
				$this->insertField($captcha, true);
			if (!$this->fieldSlugExists('recaptcha'))
				$this->insertField($recaptcha, true);
			if (!$this->fieldSlugExists('usaStates'))
				$this->insertField($states, true);
			if (!$this->fieldSlugExists('allCountries'))
				$this->insertField($countries, true);
			if (!$this->fieldSlugExists('ishuman'))
				$this->insertField($ishuman, true);
			if (!$this->fieldSlugExists('fixedEmail'))
				$this->insertField($fixedEmail, true);
			if (!$this->fieldSlugExists('fixedWebsite'))
				$this->insertField($fixedWebsite, true);
			if (!$this->fieldSlugExists('emailWebsite'))
				$this->insertField($emailSubject, true);
			if (!$this->fieldSlugExists('resetButton'))
				$this->insertField($reset, true);
		}
		
		function columnExists($column, $table) {
			global $wpdb;
			if (isset($this->cache[$table]) && !is_array($this->cache[$table]))
				$this->cache[$table] = array();
			if (empty($this->cache[$table]['columns']))
				$this->cache[$table]['columns'] = $wpdb->get_results('SHOW COLUMNS FROM ' . $table, ARRAY_A);
			$col_array = $this->cache[$table]['columns'];
			foreach ($col_array as $col) {
				if ($col['Field'] == $column)
					return true;
			}
			return false;
		}
		
		function makeAllFormsAccessible() {
			$forms = parent::selectAllForms();
			foreach ($forms as $form) {
				parent::updateForm(array('form_access_update' => 1, 'form_access' => array_values(parent::getRolesArray())), $form->id);
			}
		}
	}
}
?>