<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/
if (!class_exists('CustomContactFormsDefaultDB')) {
	class CustomContactFormsDefaultDB extends CustomContactFormsDB {
	
		function CustomContactFormsDefaultDB($insert_default_content = true) {
			if ($insert_default_content)
				$this->insertDefaultContent();
		}
		
		function insertDefaultContent($overwrite = false) {
			$field_slugs = array('name' => 'ccf_name', 'message' => 'ccf_message',
			'phone' => 'ccf_phone', 'google' => 'ccf_google', 'date' => 'ccf_schedule_date', 'contact_method' => 'ccf_contact_method');
			$option_slugs = array('email' => 'ccf_email', 'phone' => 'ccf_phone', 'yes' => 'ccf_yes', 'nocontact' => 'ccf_no_contact', 'pleaseselect' => 'please_select');
			$form_slugs = array('contact_form' => 'ccf_contact_form');
			if ($overwrite) {
				foreach($field_slugs as $slug) parent::deleteField(0, $slug);
				foreach($option_slugs as $slug) parent::deleteFieldOption(0, $slug);
				foreach($form_slugs as $slug) parent::deleteForm(0, $slug);
			}
			$name_field = array('field_slug' => $field_slugs['name'], 'field_label' => __('Your Name:', 'custom-contact-forms'),
			'field_required' => 1, 'field_instructions' => __('Please enter your full name.', 'custom-contact-forms'),
			'field_maxlength' => '100', 'field_type' => 'Text');
			$date_field = array('field_slug' => $field_slugs['date'], 'field_label' => __('When Should I Contact You:', 'custom-contact-forms'),
			'field_required' => 0, 'field_instructions' => __('Please choose a date you would like to be contacted.', 'custom-contact-forms'),
			'field_maxlength' => '100', 'field_type' => 'Date');
			$message_field = array('field_slug' => $field_slugs['message'], 'field_label' => __('Your Message:', 'custom-contact-forms'),
			'field_required' => 0, 'field_instructions' => __('Enter any message or comment.', 'custom-contact-forms'),
			'field_maxlength' => 0, 'field_type' => 'Textarea');
			$phone_field = array('field_slug' => $field_slugs['phone'], 'field_label' => __('Your Phone Number:', 'custom-contact-forms'),
			'field_required' => 0, 'field_instructions' => __('Please enter your phone number.', 'custom-contact-forms'),
			'field_maxlength' => 30, 'field_type' => 'Text');
			$google_field = array('field_slug' => $field_slugs['google'], 'field_label' => __('Did you find my website through Google?', 'custom-contact-forms'),
			'field_required' => 0, 'field_instructions' => __('If you found my website through Google, check this box.', 'custom-contact-forms'),
			'field_maxlength' => 0, 'field_type' => 'Checkbox', 'field_value' => __('Yes', 'custom-contact-forms'));
			$contact_method_field = array('field_slug' => $field_slugs['contact_method'], 'field_label' => __('How should we contact you?', 'custom-contact-forms'),
			'field_required' => 1, 'field_instructions' => __('By which method we should contact you?', 'custom-contact-forms'),
			'field_maxlength' => 0, 'field_type' => 'Dropdown');
			$email_field = parent::selectField(0, 'fixedEmail');
			$website_field = parent::selectField(0, 'fixedWebsite');
			$captcha_field = parent::selectField(0, 'captcha');
			$reset_button = parent::selectField(0, 'resetButton');
			$pleaseselect_option = array('option_slug' => $option_slugs['pleaseselect'], 'option_dead' => 1, 'option_label' => __('Please Select:', 'custom-contact-forms'));
			$email_option = array('option_slug' => $option_slugs['email'], 'option_label' => __('By Email', 'custom-contact-forms'));
			$phone_option = array('option_slug' => $option_slugs['phone'], 'option_label' => __('By Phone', 'custom-contact-forms'));
			$nocontact_option = array('option_slug' => $option_slugs['nocontact'], 'option_label' => __('Do Not Contact Me', 'custom-contact-forms'));
			$yes_option = array('option_slug' => $option_slugs['yes'], 'option_label' => __('Yes, I did.', 'custom-contact-forms'));
			$contact_form = array('form_slug' => $form_slugs['contact_form'], 'form_title' => __('Contact Form', 'custom-contact-forms'), 'form_method' => 'Post',
			'submit_button_text' => __('Send Message', 'custom-contact-forms'), 'form_email' => get_option('admin_email'), 'form_success_message' => __('Thank you for filling out our contact form. We will be contacting you very soon.', 'custom-contact-forms'),
			'form_success_title' => __('Thank You!', 'custom-contact-forms'), 'form_access' => parent::getRolesArray(), 'form_style' => 0);
			$name_field_id = parent::insertField($name_field);
			if (empty($name_field_id)) {
				$f = parent::selectField('', $name_field['field_slug']);
				$name_field_id = $f->id;
			}
			$date_field_id = parent::insertField($date_field);
			if (empty($date_field_id)) {
				$f = parent::selectField('', $date_field['field_slug']);
				$date_field_id = $f->id;
			}
			$message_field_id = parent::insertField($message_field);
			if (empty($message_field_id)) {
				$f = parent::selectField('', $message_field['field_slug']);
				$message_field_id = $f->id;
			}
			$phone_field_id = parent::insertField($phone_field);
			if (empty($phone_field_id)) {
				$f = parent::selectField('', $phone_field['field_slug']);
				$phone_field_id = $f->id;
			}
			$google_field_id = parent::insertField($google_field);
			if (empty($google_field_id)) {
				$f = parent::selectField('', $google_field['field_slug']);
				$google_field_id = $f->id;
			}
			$contact_method_field_id = parent::insertField($contact_method_field);
			if (empty($contact_method_field_id)) {
				$f = parent::selectField('', $contact_method_field['field_slug']);
				$contact_method_field_id = $f->id;
			}
			$email_option_id = parent::insertFieldOption($email_option);
			$yes_option_id = parent::insertFieldOption($yes_option);
			$pleaseselect_option_id = parent::insertFieldOption($pleaseselect_option);
			$phone_option_id = parent::insertFieldOption($phone_option);
			$nocontact_option_id = parent::insertFieldOption($nocontact_option);
			$contact_form_id = parent::insertForm($contact_form);
			parent::addFieldOptionToField($pleaseselect_option_id, $contact_method_field_id);
			parent::addFieldOptionToField($email_option_id, $contact_method_field_id);
			parent::addFieldOptionToField($phone_option_id, $contact_method_field_id);
			parent::addFieldOptionToField($nocontact_option_id, $contact_method_field_id);
			parent::addFieldOptionToField($yes_option_id, $google_field_id);
			parent::addFieldToForm($name_field_id, $contact_form_id);
			parent::addFieldToForm($website_field->id, $contact_form_id);
			parent::addFieldToForm($email_field->id, $contact_form_id);
			parent::addFieldToForm($phone_field_id, $contact_form_id);
			parent::addFieldToForm($google_field_id, $contact_form_id);
			parent::addFieldToForm($contact_method_field_id, $contact_form_id);
			parent::addFieldToForm($date_field_id, $contact_form_id);
			parent::addFieldToForm($message_field_id, $contact_form_id);
			parent::addFieldToForm($captcha_field->id, $contact_form_id);
			parent::addFieldToForm($reset_button->id, $contact_form_id);
		}
	}
}
?>