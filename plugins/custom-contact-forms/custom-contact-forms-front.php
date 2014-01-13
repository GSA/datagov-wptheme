<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/
if (!class_exists('CustomContactFormsFront')) {
	class CustomContactFormsFront extends CustomContactForms {
		var $form_errors = array();
		var $error_return;
		var $form_uploads = array();
		var $current_form;
		var $current_thank_you_message;

		function frontInit() {
			$this->processForms();
		}
		
		function includeDependencies() {
			$admin_options = parent::getAdminOptions();
			$include_defaults = false;
			$include_datepicker = false;
			// faster algorithm? this is in O(m*n) n = # of forms and m = # of posts
			if ($admin_options['form_page_inclusion_only'] == 1) {
				global $posts;
				$forms = parent::selectAllForms();
				$active_forms = array();
				foreach ($forms as $form) {
					$form_pages = parent::unserializeFormPageIds($form);
					foreach ($posts as $i => $p) {
						if (in_array($p->ID, $form_pages)) {
							$active_forms[] = $form;
							break;
						}
					}
				}
				
				if (!empty($active_forms)) {
					$include_defaults = true;
					if ($admin_options['enable_jquery'] == 1) {
						foreach ($active_forms as $form) {
							$fields = parent::getAttachedFieldsArray($form->id);
							foreach ($fields as $fid) {
								$field = parent::selectField($fid);
								if ($field->field_type == 'Date') {
									$include_datepicker = true;
									break;
								}
							}
						}
					}
				}
			} else {
				$include_defaults = true;
				$include_datepicker = true;
			}
			
			if ($include_defaults) {
				if ($admin_options['enable_jquery'] == 1) {
					if ($include_datepicker) {
						add_action('wp_print_styles', array(&$this, 'insertDatePickerStyles'), 1);
						add_action('wp_enqueue_scripts', array(&$this, 'insertDatePickerScripts'), 1);
					}
					add_action('wp_enqueue_scripts', array(&$this, 'insertFrontEndScripts'), 1);
				}
				add_action('wp_print_styles', array(&$this, 'insertFrontEndStyles'), 1);
			}
		}
	
		function insertFrontEndStyles() {
            wp_register_style('CCFStandardsCSS', plugins_url() . '/custom-contact-forms/css/custom-contact-forms-standards.css');
           	wp_register_style('CCFFormsCSS', plugins_url() . '/custom-contact-forms/css/custom-contact-forms.css');
           	wp_enqueue_style('CCFStandardsCSS');
			wp_enqueue_style('CCFFormsCSS');
		}
		
		function insertFrontEndScripts() { 
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-tools', plugins_url() . '/custom-contact-forms/js/jquery.tools.min.js');
			wp_enqueue_script('ccf-main', plugins_url() . '/custom-contact-forms/js/custom-contact-forms.js', '1.0');
		}
		
		function insertDatePickerScripts() {
			wp_enqueue_script('jquery-ui-datepicker', plugins_url() . '/custom-contact-forms/js/jquery.ui.datepicker.js', array('jquery-ui-core', 'jquery-ui-widget'));
			wp_enqueue_script('ccf-datepicker', plugins_url() . '/custom-contact-forms/js/custom-contact-forms-datepicker.js', '1.2');
		}
		
		function insertDatePickerStyles() {
			wp_register_style('ccf-jquery-ui', plugins_url() . '/custom-contact-forms/css/jquery-ui.css');
            wp_enqueue_style('ccf-jquery-ui');
		}
		
		function setFormError($key, $message) {
			$this->form_errors[$key] = $message;
		}
		
		function getFormError($key) {
			return $this->form_errors[$key];
		}
		
		function getAllFormErrors() {
			return $this->form_errors;
		}
		
		function shortCodeToForm($atts) {
			extract(shortcode_atts(array(
				'form' => 0,
			), $atts));
			$this_form = parent::selectForm($form);
			if (empty($this_form)) return '';
			$admin_options = parent::getAdminOptions();
			if ($admin_options['enable_form_access_manager'] == 1 && !$this->userCanViewForm($this_form))
				return esc_html($admin_options['default_form_bad_permissions']);
			
			return $this->getFormCode($this_form);
		}
		
		function emptyFormErrors() {
			$this->form_errors = array();
		}
		
		function contentFilter($content) {
			// THIS NEEDS TO REPLACE THE SHORTCODE ONLY ONCE
			$errors = $this->getAllFormErrors();
			if (!empty($errors)) {
				$admin_options = parent::getAdminOptions();
				$out = '<div id="custom-contact-forms-errors"><p>'.esc_html($admin_options['default_form_error_header']).'</p><ul>' . "\n";
				//$errors = $this->getAllFormErrors();
				foreach ($errors as $error) {
					$out .= '<li>'.esc_html($error).'</li>' . "\n";
				}
				$err_link = (!empty($this->error_return)) ? '<p><a href="'.esc_attr($this->error_return).'" title="'.__('Go Back', 'custom-contact-forms').'">&lt; ' . __('Go Back to Form.', 'custom-contact-forms') . '</a></p>' : '';
				$this->emptyFormErrors();
				return $out . '</ul>' . "\n" . $err_link . '</div>';
			}
			return $content;
		}
		
		function insertFormSuccessCode() {
			$admin_options = parent::getAdminOptions();
			if ($this->current_form !== 0) {
				$form = parent::selectForm($this->current_form);
				$success_message = (!empty($form->form_success_message)) ? $form->form_success_message : $admin_options['form_success_message'];
				$success_title = (!empty($form->form_success_title)) ? $form->form_success_title : $admin_options['form_success_message_title'];
			} else {
				$success_title = $admin_options['form_success_message_title'];
				$success_message = (empty($this->current_thank_you_message)) ? $admin_options['form_success_message'] : $this->current_thank_you_message;
			} if ($form->form_style != 0) {
				$style = parent::selectStyle($form->form_style);
				?>
                <style type="text/css">
					#ccf-form-success { z-index:10000; border-color:#<?php echo esc_attr(parent::formatStyle($style->success_popover_bordercolor)); ?>; height:<?php echo esc_attr($style->success_popover_height); ?>; }
					#ccf-form-success div { background-color:#<?php echo esc_attr(parent::formatStyle($style->success_popover_bordercolor)); ?>; }
					#ccf-form-success div h5 { color:#<?php echo esc_attr(parent::formatStyle($style->success_popover_title_fontcolor)); ?>; font-size:<?php echo esc_attr($style->success_popover_title_fontsize); ?>; }
					#ccf-form-success div a { color:#<?php echo esc_attr(parent::formatStyle($style->success_popover_title_fontcolor)); ?>; }
					#ccf-form-success p { font-size:<?php echo esc_attr($style->success_popover_fontsize); ?>; color:#<?php echo esc_attr(parent::formatStyle($style->success_popover_fontcolor)); ?>; }
				</style>
                <?php
			}
		?>
        	<div id="ccf-form-success">
            	<div>
            		<h5><?php echo esc_html($success_title); ?></h5>
                	<a href="javascript:void(0)" class="close">&times;</a>
                </div>
                <p><?php echo esc_html($success_message); ?></p>
                
            </div>

        <?php
		}
		
		function validEmail($email) {
			if (!@preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) return false;
			$email_array = explode("@", $email);
			$local_array = explode(".", $email_array[0]);
			for ($i = 0; $i < sizeof($local_array); $i++) {
				if (!@preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
					return false;
				}
			} if (!@preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
				$domain_array = explode(".", $email_array[1]);
				if (sizeof($domain_array) < 2) return false;
				for ($i = 0; $i < sizeof($domain_array); $i++) {
					if (!@preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
						return false;
					}
				}
			}
			return true;
		}
		
		function validWebsite($website) {
			return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $website);
		}
		
		function getFormCode($form, $is_widget_form = false) {
			ccf_utils::startSession();
			if (empty($form)) return '';
			$admin_options = parent::getAdminOptions();
			$form_key = time();
			$out = '';
			$form_styles = '';
			$style_class = (!$is_widget_form) ? ' customcontactform' : ' customcontactform-sidebar';
			$form_id = esc_attr('form-' . $form->id . '-'.$form_key);
			if ($form->form_style != 0) {
				$style = parent::selectStyle($form->form_style, '');
				$style_class = $style->style_slug;
			}
			$form_method = (empty($form->form_method)) ? 'post' : strtolower($form->form_method);
			$form_title = ccf_utils::decodeOption($form->form_title, 1, 1);
			$action = (!empty($form->form_action)) ? $form->form_action : $_SERVER['REQUEST_URI'];
			$file_upload_form = '';
			//$out .= '<form id="'.$form_id.'" method="'.$form_method.'" action="'.$action.'" class="'.$style_class.'">' . "\n";
			$out .= ccf_utils::decodeOption($form->custom_code, 1, 1) . "\n";
			if (!empty($form_title) && !$is_widget_form) $out .= '<h4 id="h4-' . esc_attr($form->id) . '-' . $form_key . '">' . esc_html($form_title) . '</h4>' . "\n";
			$fields = parent::getAttachedFieldsArray($form->id);
			$hiddens = '';
			$code_type = ($admin_options['code_type'] == 'XHTML') ? ' /' : '';
			$add_reset = '';
			foreach ($fields as $field_id) {
				$field = parent::selectField($field_id, '');
				$req = ($field->field_required == 1 or $field->field_slug == 'ishuman') ? '* ' : '';
				$req_long = ($field->field_required == 1) ? ' ' . __('(required)', 'custom-contact-forms') : '';
				$input_id = 'id="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'-'.$form_key.'"';
				$field_value = esc_attr( ccf_utils::decodeOption($field->field_value, 1, 1) );
				$instructions = (empty($field->field_instructions)) ? '' : 'title="' . esc_attr($field->field_instructions) . $req_long . '" ';
				$tooltip_class = (empty($field->field_instructions)) ? '' : 'ccf-tooltip-field';
				if ($admin_options['enable_widget_tooltips'] == 0 && $is_widget_form) $instructions = '';
				if (isset($_SESSION['ccf_fields'][$field->field_slug])) {
					if ($admin_options['remember_field_values'] == 1)
						$field_value = esc_attr( $_SESSION['ccf_fields'][$field->field_slug] );
				} if ($field->field_slug == 'captcha') {
					$out .= '<div>' . "\n" . $this->getCaptchaCode($field, $form->id) . "\n" . '</div>' . "\n";
				} elseif ( $field->field_slug == 'recaptcha' ) {
					$out .= '<div>' . "\n" . $this->getReCaptchaCode( $field, $form->id ) . "\n" . '</div>' . "\n";
				} elseif ($field->field_slug == 'usaStates') {
					$field->field_value = $field_value;
					$out .= '<div>' . "\n" . $this->getStatesCode($field, $form->id) . "\n" . '</div>' . "\n";
				} elseif ($field->field_slug == 'ishuman') {
					$field->field_value = $field_value;
					$out .= '<div>' . "\n" . $this->getIsHumanCode($field, $form->id) . "\n" . '</div>' . "\n";
				} elseif ($field->field_slug == 'allCountries') {
					$field->field_value = $field_value;
					$out .= '<div>' . "\n" . $this->getCountriesCode($field, $form->id) . "\n" . '</div>' . "\n";
				} elseif ($field->field_slug == 'resetButton') {
					$add_reset = ' <input type="reset" '.$instructions.' class="reset-button '.$field->field_class.' '.$tooltip_class.'" value="' . esc_attr($field->field_value) . '" />';
				} elseif ($field->field_type == 'Fieldset') {
					if(!empty($fieldset)) $out .= '</fieldset>' . "\n";
					$fieldset = true;					
					$out .= '<fieldset id="' . ccf_utils::decodeOption($field->field_slug, 1, 1) . '" class="'.esc_attr($field->field_class).'">'."\n".'<legend>'. $req .ccf_utils::decodeOption($field->field_label, 1, 1).'</legend>'."\n";
				} elseif ($field->field_type == 'Text') {
					$maxlength = (empty($field->field_maxlength) or $field->field_maxlength <= 0) ? '' : ' maxlength="'.esc_attr($field->field_maxlength).'"';
					$out .= '<div class="'.esc_attr($field->field_class).' '.$tooltip_class.'">'."\n".'<label for="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">'. $req .ccf_utils::decodeOption($field->field_label, 1, 1).'</label>'."\n".'<input '.$instructions.' '.$input_id.' type="text" name="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'" value="'.$field_value.'"'.$maxlength.''.$code_type.'>'."\n".'</div>' . "\n";
				} elseif ($field->field_type == 'File') {
					$file_upload_form = ' enctype="multipart/form-data" ';
					$out .= '<div class="'.esc_attr($field->field_class).' '.$tooltip_class.'">'."\n".'<label for="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">'. $req .ccf_utils::decodeOption($field->field_label, 1, 1).'</label>'."\n".'<input '.$instructions.' '.$input_id.' type="file" name="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'" value="'.$field_value.'"'.$code_type.'>'."\n".'</div>' . "\n";
				} elseif ($field->field_type == 'Date') {
					$maxlength = (empty($field->field_maxlength) or $field->field_maxlength <= 0) ? '' : ' maxlength="'.$field->field_maxlength.'"';
					$out .= '<div class="'.esc_attr($field->field_class).' ccf-datepicker '.$tooltip_class.'">'."\n".'<label for="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">'. $req .ccf_utils::decodeOption($field->field_label, 1, 1).'</label>'."\n".'<input '.$instructions.' '.$input_id.' type="text" name="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'" value="'.$field_value.'"'.$maxlength.''.$code_type.'>'."\n".'</div>' . "\n";
				} elseif ($field->field_type == 'Hidden') {
					$hiddens .= '<input type="hidden" name="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'" value="'.$field_value.'" '.$input_id.''.$code_type.'>' . "\n";
				} elseif ($field->field_type == 'Textarea') {
					$out .= '<div class="'.esc_attr($field->field_class).' '.$tooltip_class.'">'."\n".'<label for="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">'. $req .ccf_utils::decodeOption($field->field_label, 1, 1).'</label>'."\n".'<textarea '.$instructions.' '.$input_id.' rows="5" cols="40" name="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">'.$field_value.'</textarea>'."\n".'</div>' . "\n";
				} elseif ($field->field_type == 'Dropdown') {
					$field_options = '';
					$options = parent::getAttachedFieldOptionsArray($field->id);
					foreach ($options as $option_id) {
						$option = parent::selectFieldOption($option_id);
						$option_sel = (($field_value == $option->option_label || $field_value == $option->option_value) && !empty($field_value)) ? ' selected="selected"' : '';
						$option_value = (!empty($option->option_value)) ? ' value="' . esc_attr($option->option_value) . '"' : '';
						// Weird way of marking a state dead. TODO: Find another way.
						$option_value = ($option->option_dead == 1) ? ' value="' . CCF_DEAD_STATE_VALUE . '"' : $option_value;
						$field_options .= '<option'.$option_sel.''.$option_value.'>' . esc_attr($option->option_label) . '</option>' . "\n";
					}
					if (!empty($options)) {
						if (!$is_widget_form) $out .= '<div class="'.esc_attr($field->field_class).' '.$tooltip_class.'">'."\n".'<label for="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">'. $req .ccf_utils::decodeOption($field->field_label, 1, 1).'</label>'."\n".'<select '.$instructions.' '.$input_id.' name="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">'."\n".$field_options.'</select>'."\n".'</div>' . "\n";
						else  $out .= '<div class="'.esc_attr($field->field_class).' '.$tooltip_class.'">'."\n".'<label for="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">'. $req .ccf_utils::decodeOption($field->field_label, 1, 1).'</label>'."\n".'<select '.$instructions.' '.$input_id.' name="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">'."\n".$field_options.'</select>'."\n".'</div>' . "\n";
					}
				} elseif ($field->field_type == 'Radio') {
					$field_options = '';
					$options = parent::getAttachedFieldOptionsArray($field->id);
					foreach ($options as $option_id) {
						$option = parent::selectFieldOption($option_id);
						$option_sel = (($field_value == $option->option_label || $field_value == $option->option_value) && !empty($field_value)) ? ' checked="checked"' : '';
						$field_options .= '<div class="'.esc_attr($field->field_class).' '.$tooltip_class.'"><input'.$option_sel.' type="radio" '.$instructions.' name="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'" value="'.ccf_utils::decodeOption($option->option_value, 1, 1).'"'.$code_type.'> <label class="select" for="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">' . ccf_utils::decodeOption($option->option_label, 1, 1) . '</label></div>' . "\n";
					}
					$field_label = (!empty($field->field_label)) ? '<label for="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">'. $req .ccf_utils::decodeOption($field->field_label, 1, 1).'</label>' : '';
					if (!empty($options)) $out .= '<div class="'.esc_attr($field->field_class).' '.$tooltip_class.'">'."\n".$field_label."\n".$field_options."\n".'</div>' . "\n";
				} elseif ($field->field_type == 'Checkbox') {
					$field_options = '';
					$options = parent::getAttachedFieldOptionsArray($field->id);
					$z = 0;
					foreach ($options as $option_id) {
						$option = parent::selectFieldOption($option_id);
						$field_value_array = (!is_array($field_value)) ? array() : $field_value;
						$option_sel = (in_array($option->option_label, $field_value_array) || in_array($option->option_value, $field_value_array)) ? ' checked="checked"' : '';
						$check_value = (empty($option->option_value)) ? esc_html($option->option_label) : ccf_utils::decodeOption($option->option_value, 1, 1);
						$field_options .= '<div class="'.esc_attr($field->field_class).' '.$tooltip_class.'"><input'.$option_sel.' type="checkbox" '.$instructions.' name="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'['.$z.']" value="'.$check_value.'"'.$code_type.'> <label class="select" for="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">' . ccf_utils::decodeOption($option->option_label, 1, 1) . '</label></div>' . "\n";
						$z++;
					}
					$field_label = (!empty($field->field_label)) ? '<label for="'.ccf_utils::decodeOption($field->field_slug, 1, 1).'">'. $req .ccf_utils::decodeOption($field->field_label, 1, 1).'</label>' : '';
					if (!empty($options)) $out .= '<div class="'.esc_attr($field->field_class).' '.$tooltip_class.'">'."\n".$field_label."\n".$field_options."\n".'</div>' . "\n";
				}
			}

			if (!empty($file_upload_form))
				$out = '<input type="hidden" name="MAX_FILE_SIZE" value="'.(intval($admin_options['max_file_upload_size']) * 1000 * 1000).'" />' . "\n" . $out;
			$out = '<form id="'.$form_id.'" method="'.esc_attr($form_method).'" action="'.esc_url($action).'" class="'.esc_attr($style_class).'"'.$file_upload_form.'>' . "\n" . $out;
			$submit_text = (!empty($form->submit_button_text)) ? ccf_utils::decodeOption($form->submit_button_text, 1, 0) : __('Submit', 'custom-contact-forms');
			$out .= '<input name="form_page" value="'.esc_url($_SERVER['REQUEST_URI']).'" type="hidden"'.$code_type.'>'."\n".'<input type="hidden" name="fid" value="'.esc_attr($form->id).'"'.$code_type.'>'."\n".$hiddens."\n".'<input type="submit" id="submit-' . esc_attr($form->id) . '-'.$form_key.'" class="submit" value="' . $submit_text . '" name="customcontactforms_submit"'.$code_type.'>';
			if (!empty($add_reset)) $out .= $add_reset;
			if (!empty($fieldset)) $out .= '</fieldset>' . "\n";
			$out .= "\n" . '</form>';
			
			if ($form->form_style != 0) {
				$no_border = array('', '0', '0px', '0%', '0pt', '0em');
				$round_border = (!in_array($style->field_borderround, $no_border)) ? '-moz-border-radius:'.esc_attr($style->field_borderround).'; -khtml-border-radius:'.esc_attr($style->field_borderround).'; -webkit-border-radius:'.esc_attr($style->field_borderround).'; ' : '';
				$round_border_none = '-moz-border-radius:0px; -khtml-border-radius:0px; -webkit-border-radius:0px; ';
				$form_styles .= '<style type="text/css">' . "\n";
				$form_styles .= '#' . $form_id . " { width: ".esc_attr($style->form_width)."; text-align:left; padding:".esc_attr($style->form_padding)."; margin:".esc_attr($style->form_margin)."; border:".esc_attr($style->form_borderwidth)." ".esc_attr($style->form_borderstyle)." #".esc_attr(parent::formatStyle($style->form_bordercolor))."; background-color:#".esc_attr(parent::formatStyle($style->form_backgroundcolor))."; font-family:".esc_attr($style->form_fontfamily)."; } \n";
				$form_styles .= '#' . $form_id . " div { margin-bottom:6px; background-color:inherit; }\n";
				$form_styles .= '#' . $form_id . " div div { margin:0; background-color:inherit; padding:0; }\n";
				$form_styles .= '#' . $form_id . " h4 { padding:0; background-color:inherit; margin:".esc_attr($style->title_margin)." ".esc_attr($style->title_margin)." ".esc_attr($style->title_margin)." 0; color:#".esc_attr(parent::formatStyle($style->title_fontcolor))."; font-size:".esc_attr($style->title_fontsize)."; } \n";
				$form_styles .= '#' . $form_id . " label { padding:0; background-color:inherit; margin:".esc_attr($style->label_margin)." ".esc_attr($style->label_margin)." ".esc_attr($style->label_margin)." 0; display:block; color:#".esc_attr(parent::formatStyle($style->label_fontcolor))."; width:".esc_attr($style->label_width)."; font-size:".esc_attr($style->label_fontsize)."; } \n";
				$form_styles .= '#' . $form_id . " div div input { margin-bottom:2px; line-height:normal; }\n";
				$form_styles .= '#' . $form_id . " input[type=checkbox] { margin:0; }\n";
				$form_styles .= '#' . $form_id . " label.checkbox, #" . $form_id . " label.radio, #" . $form_id . " label.select { display:inline; } \n";
				$form_styles .= '#' . $form_id . " input[type=text], #" . $form_id . " select { ".$round_border." color:#".esc_attr(parent::formatStyle($style->field_fontcolor))."; margin:0; width:".esc_attr($style->input_width)."; font-size:".esc_attr($style->field_fontsize)."; background-color:#".esc_attr(parent::formatStyle($style->field_backgroundcolor))."; border:1px ".esc_attr($style->field_borderstyle)." #".esc_attr(parent::formatStyle($style->field_bordercolor))."; } \n";
				$form_styles .= '#' . $form_id . " select { ".$round_border_none." width:".esc_attr($style->dropdown_width)."; }\n";
				$form_styles .= '#' . $form_id . " .submit { color:#".esc_attr(parent::formatStyle($style->submit_fontcolor))."; width:".esc_attr($style->submit_width)."; height:".esc_attr($style->submit_height)."; font-size:".esc_attr($style->submit_fontsize)."; } \n";
				if (!empty($style->submit_background)) $form_styles .= '#' . $form_id . " .submit { background:url(" . esc_attr($style->submit_background) . ") " . esc_attr($style->submit_background_repeat) . " top left; border:0; }";
				$form_styles .= '#' . $form_id . " .reset-button { color:#".esc_attr(parent::formatStyle($style->submit_fontcolor))."; width:".esc_attr($style->submit_width)."; height:".esc_attr($style->submit_height)."; font-size:".esc_attr($style->submit_fontsize)."; } \n";
				$form_styles .= '#' . $form_id . " textarea { ".$round_border." color:#".esc_attr(parent::formatStyle($style->field_fontcolor))."; width:".esc_attr($style->textarea_width)."; margin:0; background-color:#".esc_attr(parent::formatStyle($style->textarea_backgroundcolor))."; font-family:".esc_attr($style->form_fontfamily)."; height:".esc_attr($style->textarea_height)."; font-size:".esc_attr($style->field_fontsize)."; border:1px ".esc_attr($style->field_borderstyle)." #".esc_attr(parent::formatStyle($style->field_bordercolor))."; } \n";
				$form_styles .= '.ccf-tooltip { background-color:#'.esc_attr(parent::formatStyle($style->tooltip_backgroundcolor)).'; font-family:'.esc_attr($style->form_fontfamily).'; font-color:#'.esc_attr(parent::formatStyle($style->tooltip_fontcolor)).'; font-size:'.esc_attr($style->tooltip_fontsize).'; }' . "\n"; 
				$form_styles .= '</style>' . "\n";
			}
			return $form_styles . $out;
		}
		
		function requiredFieldsArrayFromList($list) {
			if (empty($list)) return array();
			$list = str_replace(' ', '', $list);
			$array = explode(',', $list);
			foreach ($array as $k => $v) {
				if (empty($array[$k])) unset($array[$k]);
			}
			return $array;
		}
		
		function processFileUpload($field) {
			$errors = array();
			if (empty($_FILES[$field->field_slug])) $errors[] = __('An error occured while uploading: ', 'custom-contact-forms') . $field->field_slug;
			$admin_options = parent::getAdminOptions();
			if ($field->field_max_upload_size > 0 && $_FILES[$field->field_slug]['size'] > ($field->field_max_upload_size * 1000)) $errors[] = basename($_FILES[$field->field_slug]['name']) . __(' is too large of a file. The maximum file size for that field is ', 'custom-contact-forms') . $field->field_max_upload_size . __(' KB.', 'custom-contact-forms');
			$allowed_exts = unserialize($field->field_allowed_file_extensions);
			$ext = preg_replace('/.*\.(.*)/i', '$1', basename($_FILES[$field->field_slug]['name']));
			if (!empty($allowed_exts))
				if (!in_array($ext, $allowed_exts)) $errors[] = '.' . $ext . __(' is an invalid file extension.', 'custom-contact-forms');
			if (!empty($errors)) return $errors;
			
			// create necessary directories
			if (!is_dir(ABSPATH."wp-content/plugins/custom-contact-forms/uploads/".date("Y")))
				mkdir(ABSPATH."wp-content/plugins/custom-contact-forms/uploads/".date("Y"));
			if (!is_dir(ABSPATH . "wp-content/plugins/custom-contact-forms/uploads/".date("Y")."/".date("m")))
				mkdir(ABSPATH . "wp-content/plugins/custom-contact-forms/uploads/".date("Y")."/".date("m"));
			
			// check if file already exists
			$file_name = preg_replace('/(.*)\..*/i', '$1', basename($_FILES[$field->field_slug]['name']));
			$file_name_addon = ".";
			$i = 1;
			while (file_exists( ABSPATH . "wp-content/plugins/custom-contact-forms/uploads/".date("Y")."/".date("m")."/" . $file_name . $file_name_addon . $ext)) {
				$file_name_addon = ' ('.$i.').';
				$i++;
			}
			$target_path = ABSPATH . "wp-content/plugins/custom-contact-forms/uploads/".date("Y")."/".date("m")."/" . $file_name . $file_name_addon . $ext;
			$this->form_uploads[$field->field_slug] = ABSPATH . "wp-content/plugins/custom-contact-forms/uploads/".date("Y")."/".date("m")."/" . $file_name . $file_name_addon . $ext;
			if(!move_uploaded_file($_FILES[$field->field_slug]['tmp_name'], $target_path)) {
				// Error!
				$errors[] = __('An error occured while uploading: ', 'custom-contact-forms') . $field->field_slug;
			}
			return $errors;
		}
		
		function processForms() {
			if (isset($_POST['ccf_customhtml']) || isset($_POST['customcontactforms_submit'])) {
				// BEGIN define common language vars
				$lang = array();
				$lang['field_blank'] = __('You left this field blank: ', 'custom-contact-forms');
				$lang['form_page'] = __('Form Displayed on Page: ', 'custom-contact-forms');
				$lang['sender_ip'] = __('Sender IP: ', 'custom-contact-forms');
				// END define common language vars
			} if (isset($_POST['ccf_customhtml'])) {
				$admin_options = parent::getAdminOptions();
				$fixed_customhtml_fields = array('required_fields', 'success_message', 'thank_you_page', 'destination_email', 'ccf_customhtml');
				$req_fields = $this->requiredFieldsArrayFromList($_POST['required_fields']);
				$req_fields = array_map('trim', $req_fields);
				$body = '';
				foreach ($_POST as $key => $value) {
					if (!in_array($key, $fixed_customhtml_fields)) {
						if (in_array($key, $req_fields) && !empty($value)) {
							unset($req_fields[array_search($key, $req_fields)]);
						}
						$body .= ucwords(str_replace('_', ' ', htmlspecialchars($key))) . ': ' . htmlspecialchars($value) . "<br /><br />\n";
						$data_array[$key] = $value;
					}
				} foreach($req_fields as $err)
					$this->setFormError($err, $lang['field_blank'] . '"' . $err . '"');
				$errors = $this->getAllFormErrors();
				if (empty($errors)) {
					ccf_utils::load_module('export/custom-contact-forms-user-data.php');
					$data_object = new CustomContactFormsUserData(array('data_array' => $data_array, 'form_page' => $_SERVER['SERVER_NAME']. $_SERVER['REQUEST_URI'], 'form_id' => 0, 'data_time' => time()));
					parent::insertUserData($data_object);
					$body .= "<br />\n" . htmlspecialchars($lang['form_page']) . $_SERVER['SERVER_NAME']. $_SERVER['REQUEST_URI'] . "<br />\n" . $lang['sender_ip'] . $_SERVER['REMOTE_ADDR'] . "<br />\n";
					if ($admin_options['email_form_submissions'] == 1) {
						if (!class_exists('PHPMailer'))
							require_once(ABSPATH . "wp-includes/class-phpmailer.php"); 
						$mail = new PHPMailer();
						$mail->MailerDebug = false;
						if ($admin_options['mail_function'] == 'smtp') {
							$mail->IsSMTP();
							$mail->Host = $admin_options['smtp_host'];
							if ($admin_options['smtp_authentication'] == 1) {
								$mail->SMTPAuth = true;
								$mail->Username = $admin_options['smtp_username'];
								$mail->Password = $admin_options['smtp_password'];
								$mail->Port = $admin_options['smtp_port'];
							} else
								$mail->SMTPAuth = false;
						}
						$mail->From = $admin_options['default_from_email'];
						$mail->FromName = 'Custom Contact Forms';
						$dest_email_array = $this->getDestinationEmailArray($_POST['destination_email']);
						if (empty($dest_email_array)) $mail->AddAddress($admin_options['default_to_email']);
						else {
							foreach ($dest_email_array as $em)
								$mail->AddAddress($em);
						}
						$mail->Subject = $admin_options['default_form_subject'];
						$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
						$mail->MsgHTML(stripslashes($body));
						$mail->Send();
					} if ($_POST['thank_you_page']) {
						ccf_utils::redirect($_POST['thank_you_page']);
					}
					$this->current_thank_you_message = (!empty($_POST['success_message'])) ? $_POST['success_message'] : $admin_options['form_success_message'];
					$this->current_form = 0;
					add_action('wp_footer', array(&$this, 'insertFormSuccessCode'), 1);
				}
				unset($_POST);
			} elseif (isset($_POST['customcontactforms_submit'])) {
				ccf_utils::startSession();
				$this->error_return = $_POST['form_page'];
				$admin_options = parent::getAdminOptions();
				$fields = parent::getAttachedFieldsArray($_POST['fid']);
				$post_time = time();
				$form = parent::selectForm($_POST['fid']);
				$checks = array();
				$reply = (isset($_POST['fixedEmail'])) ? $_POST['fixedEmail'] : NULL;
				$fixed_subject = (isset($_POST['emailSubject'])) ? $_POST['emailSubject'] : NULL;
				$cap_name = 'ccf_captcha_' . $_POST['fid'];
				foreach ($fields as $field_id) {
					$field = parent::selectField($field_id, '');
					 if ($field->field_slug == 'ishuman') {
						if (!isset($_POST['ishuman']) || (isset($_POST['ishuman']) && $_POST['ishuman'] != 1)) {
							if (empty($field->field_error))
								$this->setFormError('ishuman', __('Only humans can use this form.', 'custom-contact-forms'));
							else $this->setFormError('ishuman', $field->field_error);
						}
					} elseif ($field->field_slug == 'captcha') {
						if ($_POST['captcha'] != $_SESSION[$cap_name]) {
							if (empty($field->field_error))
								$this->setFormError('captcha', __('You copied the number from the captcha field incorrectly.', 'custom-contact-forms'));
							else $this->setFormError('captcha', $field->field_error);
						}
					} elseif ( $field->field_slug == 'recaptcha' ) {
						require_once( CCF_BASE_PATH . 'modules/recaptcha/recaptchalib.php' );
						
						$resp = recaptcha_check_answer( $admin_options['recaptcha_private_key'], $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field'] );
						
						if ( ! $resp->is_valid ) {
							if ( empty( $field->field_error ) )
								$this->setFormError( 'recaptcha', __( 'You copied the text from the captcha field incorrectly.', 'custom-contact-forms' ) );
							else $this->setFormError( 'recaptcha', $field->field_error );
						}
						
					} elseif ($field->field_slug == 'fixedEmail' && $field->field_required == 1 && !empty($_POST['fixedEmail'])) {
						if (!$this->validEmail($_POST['fixedEmail'])) {
							if (empty($field->field_error))
								$this->setFormError('fixedEmail', __('The email address you provided is not valid.', 'custom-contact-forms'));
							else $this->setFormError('fixedEmail', $field->field_error);
						}
					} elseif ($field->field_slug == 'fixedWebsite' && $field->field_required == 1 && !empty($_POST['fixedWebsite'])) {
						if (!$this->validWebsite($_POST['fixedWebsite'])) {
							if (empty($field->field_error))
								$this->setFormError('fixedWebsite', __('The website address you provided is not valid.', 'custom-contact-forms'));
							else $this->setFormError('fixedWebsite', $field->field_error);
						}
					} else {
						$field_error_label = (empty($field->field_label)) ? $field->field_slug : $field->field_label;
						if ($field->field_required == 1 && $field->field_type != 'File' && !empty($_POST[$field->field_slug])) {
							if ($field->field_type == 'Dropdown' || $field->field_type == 'Radio' || $field->field_type == 'Checkbox') {
								// TODO: find better way to check for a dead state
								if ($_POST[$field->field_slug] == CCF_DEAD_STATE_VALUE) {
									if (empty($field->field_error))
										$this->setFormError($field->field_slug, $lang['field_blank'] . '"'.$field_error_label.'"');
									else $this->setFormError($field->field_slug, $field->field_error);
								}
							}
						} elseif ($field->field_required == 1 && $field->field_type != 'File' && empty($_POST[$field->field_slug])) {
							if (empty($field->field_error))
								$this->setFormError($field->field_slug, $lang['field_blank'] . '"'.$field_error_label.'"');
							else $this->setFormError($field->field_slug, $field->field_error);
						} else {
							// file field required and not found
							if ($field->field_required == 1 && $field->field_type == 'File' && empty($_FILES[$field->field_slug]['name'])) {
								if (empty($field->field_error))
									$this->setFormError($field->field_slug, $lang['field_blank'] . '"'.$field_error_label.'"');
								else $this->setFormError($field->field_slug, $field->field_error);
							}
							//file field found
							elseif ($field->field_type == 'File' && !empty($_FILES[$field->field_slug]['name'])) {
								$upload_result = $this->processFileUpload($field, $post_time);
								foreach ($upload_result as $err) {
									$this->setFormError($field->field_slug, $err);
								}
							}
						}
					} if ($field->field_type == 'Checkbox')
						$checks[] = $field->field_slug;
				} 
				$body = '';
				$data_array = array();
				foreach ($_POST as $key => $value) {
					$_SESSION['ccf_fields'][$key] = $value;
					//if (is_array($value)) $value = implode(', ', $value);
					$val2 = (is_array($value)) ? implode(', ', $value) : $value;
					$field = parent::selectField('', $key);
					if (!array_key_exists($key, $GLOBALS['ccf_fixed_fields']) || $key == 'fixedEmail' || $key == 'usaStates' || $key == 'fixedWebsite'|| $key == 'emailSubject' || $key == 'allCountries') {
						$mail_field_label = (empty($field->field_label)) ? $field->field_slug : $field->field_label;
						$body .= htmlspecialchars($mail_field_label) . ' - ' . htmlspecialchars($val2) . "<br />\n";
						$data_array[$key] = $value;
						
					} if (in_array($key, $checks)) {
						$checks_key = array_search($key, $checks);
						unset($checks[$checks_key]);
					}
				} foreach ($this->form_uploads as $name => $upload) {
					$file_url = preg_replace('/^.*(\/custom-contact-forms\/.*)$/i', plugins_url() . '$1', $upload);
					if (!array_key_exists($name, $GLOBALS['ccf_fixed_fields'])) $data_array[$name] = '[file link="'.$file_url.'"]'.basename($upload).'[/file]';
				} foreach ($checks as $check_key) {
					$field = parent::selectField('', $check_key);
					$lang['not_checked'] = __('Not Checked', 'custom-contact-forms');
					$data_array[$check_key] = $lang['not_checked'];
					$body .= ucwords(str_replace('_', ' ', htmlspecialchars($field->field_label))) . ' - ' . $lang['not_checked'] . "<br />\n";
				}
				$errors = $this->getAllFormErrors();
				if (empty($errors)) {
					ccf_utils::load_module('export/custom-contact-forms-user-data.php');
					unset($_SESSION['ccf_captcha_' . $_POST['fid']]);
					unset($_SESSION['ccf_fields']);
					$data_object = new CustomContactFormsUserData(array('data_array' => $data_array, 'form_page' => $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], 'form_id' => $form->id, 'data_time' => $post_time));
					parent::insertUserData($data_object);
					if ($admin_options['email_form_submissions'] == '1') {
						$body .= "<br />\n" . htmlspecialchars($lang['form_page']) . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . "<br />\n" . $lang['sender_ip'] . $_SERVER['REMOTE_ADDR'] . "<br />\n";
						if (!class_exists('PHPMailer'))
							require_once(ABSPATH . "wp-includes/class-phpmailer.php"); 
						$mail = new PHPMailer(false);
						$mail->MailerDebug = false;
						if ($admin_options['mail_function'] == 'smtp') {
							$mail->IsSMTP();
							$mail->Host = $admin_options['smtp_host'];
							if ($admin_options['smtp_authentication'] == 1) {
								$mail->SMTPAuth = true;
								$mail->Username = $admin_options['smtp_username'];
								$mail->Password = $admin_options['smtp_password'];
								$mail->Port = $admin_options['smtp_port'];
							} else
								$mail->SMTPAuth = false;
						}
						$dest_email_array = $this->getDestinationEmailArray($form->form_email);
						$from_name = (empty($admin_options['default_from_name'])) ? __('Custom Contact Forms', 'custom-contact-forms') : $admin_options['default_from_name'];
						if (!empty($form->form_email_name)) $from_name = $form->form_email_name;
						if (empty($dest_email_array)) $mail->AddAddress($admin_options['default_to_email']);
						else {
							foreach ($dest_email_array as $em)
								$mail->AddAddress($em);
						}
						foreach ($this->form_uploads as $file_upload) {
							$mail->AddAttachment($file_upload);
						}
						if ($reply != NULL && $this->validEmail($reply))
							$mail->From = $reply;
						else
							$mail->From = $admin_options['default_from_email'];
						$mail->FromName = $from_name;
						$mail->Subject = (!empty($form->form_email_subject)) ? $form->form_email_subject : $admin_options['default_form_subject'];
						if ($fixed_subject != NULL) $mail->Subject = $fixed_subject;
						$mail->AltBody = __("To view the message, please use an HTML compatible email viewer.", 'custom-contact-forms');
						$mail->CharSet = 'utf-8';
						$mail->MsgHTML(stripslashes($body));
						$mail->Send();
					} if (!empty($form->form_thank_you_page)) {
						ccf_utils::redirect(str_replace('&amp;', '&', $form->form_thank_you_page));
					}
					$this->current_form = $form->id;
					add_action('wp_footer', array(&$this, 'insertFormSuccessCode'), 1);
				}
				unset($_POST);
				$_POST = array();
			}
		}
		
		function getCaptchaCode($field_object, $form_id) {
			$admin_options = parent::getAdminOptions();
			$code_type = ($admin_options['code_type'] == 'XHTML') ? ' /' : '';
			if (empty($field_object->field_instructions)) {
				$instructions = '';
				$tooltip_class = '';
			} else {
				$instructions = 'title="'.$field_object->field_instructions.'"';
				$tooltip_class = 'ccf-tooltip-field';
			}
			$out = '<img width="96" height="24" alt="' . __('Captcha image for Custom Contact Forms plugin. You must type the numbers shown in the image', 'custom-contact-forms') . '" id="captcha-image" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/custom-contact-forms/image.php?fid='.$form_id.'"'.$code_type.'> 
			<div><label for="captcha'.$form_id.'">* '.$field_object->field_label.'</label> <input class="'.$field_object->field_class.' '.$tooltip_class.'" type="text" '.$instructions.' name="captcha" id="captcha'.$form_id.'" maxlength="20"'.$code_type.'></div>';
			return $out;
		}
		
		function getReCaptchaCode( $field_object, $form_id ) {
			ccf_utils::load_module( 'extra_fields/recaptcha_field.php' );
			$admin_options = parent::getAdminOptions();
			$recaptcha_field = new ccf_recaptcha_field( $admin_options['recaptcha_public_key'], $field_object->field_label, $field_object->field_slug, $field_object->field_class, $field_object->field_value, $field_object->field_instructions );
			return "\n" . $recaptcha_field->getCode();
		}
		
		function getIsHumanCode($field_object, $form_id) {
			$admin_options = parent::getAdminOptions();
			$code_type = ($admin_options['code_type'] == 'XHTML') ? ' /' : '';
			if (empty($field_object->field_instructions)) {
				$instructions = '';
				$tooltip_class = '';
			} else {
				$instructions = 'title="'.$field_object->field_instructions.'"';
				$tooltip_class = 'ccf-tooltip-field';
			}
			$out = '
			<div><input value="1" class="'.$field_object->field_class.' '.$tooltip_class.'" type="checkbox" '.$instructions.' name="ishuman" id="ishuman-'.$form_id.'"'.$code_type.'> <label for="ishuman-'.$form_id.'" class="checkbox">* '.$field_object->field_label.'</label></div>';
			return $out;
		}
		
		function userCanViewForm($form_object) {
			if (is_user_logged_in()) {
				global $current_user;
				$user_roles = $current_user->roles;
				$user_role = array_shift($user_roles);
			} else
				$user_role = 'Non-Registered User';
			$form_access_array = parent::getFormAccessArray($form_object->form_access);
			return parent::formHasRole($form_access_array, $user_role);
		}
		
		function getStatesCode($field_object, $form_id) {
			ccf_utils::load_module('extra_fields/states_field.php');
			$req = ($field_object->field_required == 1) ? '* ' : '';
			$states_field = new ccf_states_field($field_object->field_class, $form_id, $field_object->field_value, $field_object->field_instructions);
			return "\n".'<label for="'.ccf_utils::decodeOption($field_object->field_slug, 1, 1).'">'. $req .ccf_utils::decodeOption($field_object->field_label, 1, 1).'</label>'.$states_field->getCode();
		}
		
		function getDatePickerCode($field_object, $form_id, $xhtml_code) {
			ccf_utils::load_module('extra_fields/date_field.php');
			$req = ($field_object->field_required == 1) ? '* ' : '';
			$date_field = new ccf_date_field($field_object->field_class, $form_id, $field_object->field_value, $field_object->field_instructions, $xhtml_code);
			return "\n".'<label for="'.ccf_utils::decodeOption($field_object->field_slug, 1, 1).'">'. $req .ccf_utils::decodeOption($field_object->field_label, 1, 1).'</label>'.$date_field->getCode();
		}
		
		function getCountriesCode($field_object, $form_id) {
			ccf_utils::load_module('extra_fields/countries_field.php');
			$req = ($field_object->field_required == 1) ? '* ' : '';
			$countries_field = new ccf_countries_field($field_object->field_class, $form_id, $field_object->field_value, $field_object->field_instructions);
			return '<label for="'.ccf_utils::decodeOption($field_object->field_slug, 1, 1).'">'. $req .ccf_utils::decodeOption($field_object->field_label, 1, 1).'</label>' . "\n" . $countries_field->getCode();
		}
		
		function getDestinationEmailArray($str) {
			$str = str_replace(',', ';', $str);
			$email_array = explode(';', $str);
			$email_array2 = array();
			foreach ($email_array as $k => $v) {
				if (!empty($email_array[$k])) $email_array2[] = trim($v);
			}
			return $email_array2;
		}
	}
}
