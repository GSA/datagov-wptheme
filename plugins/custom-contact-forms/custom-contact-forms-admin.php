<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/
if (!class_exists('CustomContactFormsAdmin')) {
	class CustomContactFormsAdmin extends CustomContactForms {
		var $action_complete = '';
	
		function adminInit() {
			$this->downloadExportFile();
			$this->downloadCSVExportFile();
			$this->runImport();
		}
		
		function insertUsagePopover() {
			ccf_utils::load_module('usage_popover/custom-contact-forms-usage-popover.php');
		}
		
		function insertQuickStartPopover() {
			ccf_utils::load_module('usage_popover/custom-contact-forms-quick-start-popover.php');
		}
		
		function isPluginAdminPage() {
			$pages = array('custom-contact-forms', 'ccf-settings', 'ccf-saved-form-submissions');
			return (in_array($GLOBALS['ccf_current_page'], $pages));
		}
		
		function appendToActionLinks($action_links, $plugin_file) {
			static $link_added = false;
			if (!$link_added && basename($plugin_file) == 'custom-contact-forms.php') {
				$new_link = '<a style="font-weight:bold;" href="admin.php?page=custom-contact-forms" title="' . __('Manage Custom Contact Forms', 'custom-contact-forms') . '">' . __('Settings', 'custom-contact-forms') . '</a>';
				array_unshift($action_links, $new_link);
				$link_added = true;
			}
			return $action_links;
		}
		
		function downloadExportFile() {
			if (isset($_POST['ccf_export'])) {
				//chmod('modules/export/', 0777);
				ccf_utils::load_module('export/custom-contact-forms-export.php');
				$transit = new CustomContactFormsExport(parent::getAdminOptionsName());
				$transit->exportAll();
				$file = $transit->exportToFile();
				ccf_utils::redirect(plugins_url() . '/custom-contact-forms/download.php?location=export/' . $file);
			}
		}
		
		function downloadCSVExportFile() {
			if (isset($_POST['ccf_export_all_csv'])) {
				ccf_utils::load_module('export/custom-contact-forms-export.php');
				$transit = new CustomContactFormsExport(parent::getAdminOptionsName());
				$transit->exportSavedFormSubmissionsToCSV();
				$file = $transit->exportCSVToFile();
				ccf_utils::redirect(plugins_url() . '/custom-contact-forms/download.php?location=export/' . $file);
			} elseif (isset($_POST['ccf_export_form_csv']) && isset($_POST['csv_form_id']) && !empty($_POST['csv_form_id'])) {
				ccf_utils::load_module('export/custom-contact-forms-export.php');
				$transit = new CustomContactFormsExport(parent::getAdminOptionsName());
				$transit->exportSavedFormSubmissionsToCSV($_POST['csv_form_id']);
				$file = $transit->exportCSVToFile();
				ccf_utils::redirect(plugins_url() . '/custom-contact-forms/download.php?location=export/' . $file);
			}
		}
		
		function runImport() {
			if (isset($_POST['ccf_clear_import']) || isset($_POST['ccf_merge_import'])) {
				//chmod('modules/export/', 0777);
				ccf_utils::load_module('export/custom-contact-forms-export.php');
				$transit = new CustomContactFormsExport(parent::getAdminOptionsName());
				$settings['import_general_settings'] = ($_POST['ccf_import_overwrite_settings'] == 1) ? true : false;
				$settings['import_forms'] = ($_POST['ccf_import_forms'] == 1) ? true : false;
				$settings['import_fields'] = ($_POST['ccf_import_fields'] == 1) ? true : false;
				$settings['import_field_options'] = ($_POST['ccf_import_field_options'] == 1) ? true : false;
				$settings['import_styles'] = ($_POST['ccf_import_styles'] == 1) ? true : false;
				$settings['import_saved_submissions'] = ($_POST['ccf_import_saved_submissions'] == 1) ? true : false;
				$settings['mode'] = ($_POST['ccf_clear_import']) ? 'clear_import' : 'merge_import';
				$transit->importFromFile($_FILES['import_file'], $settings);
				ccf_utils::redirect('options-general.php?page=custom-contact-forms');
			}
		}
		
		function contactAuthor($name, $email, $website, $message, $type, $host, $ccf_version, $wp_version) {
			if (empty($message)) return false;
			if (!class_exists('PHPMailer'))
				require_once(ABSPATH . "wp-includes/class-phpmailer.php"); 
			$mail = new PHPMailer();
			$body = "Name: $name<br />\n";
			$body .= "Email: $email<br />\n";
			$body .= "Website: $website<br />\n";
			$body .= "CCF Version: $ccf_version<br />\n";
			$body .= "WP Version: $wp_version<br />\n";
			$body .= "Host: $host<br />\n";
			$body .= "Message: $message<br />\n";
			$body .= "Message Type: $type<br />\n";
			$body .= 'Sender IP: ' . $_SERVER['REMOTE_ADDR'] . "<br />\n";
			$admin_options = parent::getAdminOptions();
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
			$mail->From = $email;
			$mail->FromName = 'Custom Contact Forms';
			$mail->AddAddress('admin@taylorlovett.com');
			$mail->Subject = "CCF Message: $type";
			$mail->CharSet = "utf-8";
			$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
			$mail->MsgHTML($body);
			$mail->Send();
			return true;
		}
		
		function displayPluginNewsFeed() {
            include_once(ABSPATH . WPINC . '/feed.php');
            $rss = @fetch_feed('http://www.taylorlovett.com/category/custom-contact-forms/feed');
			if (!is_wp_error($rss) ) {
                $maxitems = $rss->get_item_quantity(5);
                $rss_items = $rss->get_items(0, $maxitems); 
            }
            ?>
            <ul>
            	<?php if ($maxitems == 0) echo '<li>' . __('Nothing to show.', 'custom-contact-forms') . '</li>';
                else
                foreach ( $rss_items as $item ) : ?>
                <li>
                	<div class="news-header">
                    	<a href="<?php echo esc_attr($item->get_permalink()); ?>"><?php echo esc_html($item->get_title()); ?></a> <span class="date"><?php echo esc_html($item->get_date('j F, Y')); ?></span>
                    </div>
                    <div class="news-content">
                   		<?php echo $item->get_content(); ?>
                    </div>
                </li>
                <?php endforeach; ?>
                
            </ul>
		<?php
		}
		
		
		function getFieldsForm($show_field_type = false) {
			$fields = parent::selectAllFields();
			$out = '';
			foreach ($fields as $field) {
				$type = ($show_field_type) ? ' ('.$field->field_type.')' : '';
				$out .= '<option value="'.$field->id.'">'.$field->field_slug . $type . '</option>' . "\n";
			}
			return $out;
		}
		
		function handleAJAX() {
			if (!wp_verify_nonce($_POST['nonce'], 'ccf_nonce')) exit(__('Invalid request.', 'custom-contact-forms'));
			$output = $this->handleAdminPostRequests();
			$response = json_encode( $output );
			header("Content-Type: application/json");
			exit($response);
			return true;
		}

		
		function getFieldOptionsForm() {
			$options = parent::selectAllFieldOptions();
			$out = '';
			foreach ($options as $option) {
				$out .= '<option value="'.$option->id.'">'.$option->option_slug.'</option>' . "\n";
			}
			return $out;
		}
		
		function insertBackEndStyles() {
            wp_register_style('ccf-standards', plugins_url() . '/custom-contact-forms/css/custom-contact-forms-standards.css');
            wp_register_style('ccf-jquery-ui', plugins_url() . '/custom-contact-forms/css/jquery-ui.css');
            wp_register_style('ccf-admin', plugins_url() . '/custom-contact-forms/css/custom-contact-forms-admin.css');
			wp_register_style('ccf-colorpicker', plugins_url() . '/custom-contact-forms/css/colorpicker.css');
            wp_enqueue_style('ccf-jquery-ui');
			wp_enqueue_style('ccf-standards');
			wp_enqueue_style('ccf-admin');
			wp_enqueue_style('ccf-colorpicker');
		}
		
		function insertAdminScripts() {
			$js_version = '2.0.5';
			$admin_options = parent::getAdminOptions();
			$js_lang = array(
				'attaching' => __('Attaching', 'custom-contact-forms'),
				'detaching' => __('Detaching', 'custom-contact-forms'),
				'update_button' => __('Save', 'custom-contact-forms'),
				'attach_button' => __('Attach', 'custom-contact-forms'),
				'saving' => __('Saving', 'custom-contact-forms'),
				'more_options' => __('More Options', 'custom-contact-forms'),
				'expand' => __('Expand', 'custom-contact-forms'),
				'click_to_confirm' => __('Click to Confirm', 'custom-contact-forms'),
				'selected_tab' => (isset($_POST['selected_tab'])) ? $_POST['selected_tab'] : 0,
				'delete_confirm' => __('Are you sure you want to delete this?', 'custom-contact-forms'),
				'error' => __('An error has occured. Please try again later.', 'custom-contact-forms'),
				'nothing_to_show' => __('Nothing to show.', 'custom-contact-forms'),
				'nothing_attached' => __('Nothing Attached!', 'custom-contact-forms'),
				'no_fields' => __('No Fields', 'custom-contact-forms'),
				'nonce' => wp_create_nonce('ccf_nonce')
			);
			$js_ajax = array('plugin_dir' => plugins_url() . '/custom-contact-forms',
							'url' => admin_url('admin-ajax.php'),
							'nonce' => wp_create_nonce('ccf-ajax-nonce'));
			wp_enqueue_script('jquery');
			wp_deregister_script('jquery-form');
			wp_register_script('jquery-form', plugins_url() . '/custom-contact-forms/js/jquery.form.js', $js_version);
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_script('jquery-ui-dialog');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-tools', plugins_url() . '/custom-contact-forms/js/jquery.tools.min.js');
			wp_enqueue_script('jquery-ui-widget', plugins_url() . '/custom-contact-forms/js/jquery.ui.widget.js');
			//wp_enqueue_script('jquery-ui-dialog', plugins_url() . '/custom-contact-forms/js/jquery.ui.dialog.js', array('jquery', 'jquery-ui-core', 'jquery-ui-tabs'));
			//wp_enqueue_script('ccf-pagination', plugins_url() . '/custom-contact-forms/js/jquery.pagination.js');
			wp_enqueue_script('ccf-admin-inc', plugins_url() . '/custom-contact-forms/js/custom-contact-forms-admin-inc.js', $js_version);
			wp_enqueue_script('ccf-admin', plugins_url() . '/custom-contact-forms/js/custom-contact-forms-admin.js', $js_version);
			if ($admin_options['admin_ajax'] == 1) {
				wp_enqueue_script('ccf-admin-ajax', plugins_url() . '/custom-contact-forms/js/custom-contact-forms-admin-ajax.js', array('jquery-form'), $js_version);
				wp_localize_script('ccf-admin-ajax', 'ccfLang', $js_lang);
				wp_localize_script('ccf-admin-ajax', 'ccfAjax', $js_ajax);
			}
			/*wp_enqueue_script('ccf-colorpicker', plugins_url() . '/custom-contact-forms/js/colorpicker.js');
			wp_enqueue_script('ccf-eye', plugins_url() . '/custom-contact-forms/js/eye.js');
			wp_enqueue_script('ccf-utils', plugins_url() . '/custom-contact-forms/js/utils.js');
			wp_enqueue_script('ccf-layout', plugins_url() . '/custom-contact-forms/js/layout.js?ver=1.0.2');*/
			wp_localize_script('ccf-admin-inc', 'ccfLang', $js_lang);
			wp_localize_script('ccf-admin-inc', 'ccfAjax', $js_ajax);
			wp_localize_script('ccf-admin', 'ccfLang', $js_lang);
		}
		
		function handleAdminPostRequests() {
			//print_r($_POST);
			//print_r(unserialize(stripslashes($_POST[attached_array])));
			$out = array('success' => true);
			if (isset($_POST['object_create'])) {
				if ($_POST['object_type'] == 'form') {
					if (parent::insertForm($_POST['object']) != false)
						$this->action_complete = __('A new form was successfully created!', 'custom-contact-forms');
				} elseif ($_POST['object_type'] == 'field') {
					if (parent::insertField($_POST['object']) != false)
						$this->action_complete = __('A new field was successful created!', 'custom-contact-forms');
				} elseif ($_POST['object_type'] == 'field_option') {
					if (parent::insertFieldOption($_POST['object']) != false)
						$this->action_complete = __('A new field option was successful created!', 'custom-contact-forms');
				} elseif ($_POST['object_type'] == 'style')  {
					if (parent::insertStyle($_POST['object']) != false)
						$this->action_complete = __('A new style was successful created!', 'custom-contact-forms');
				}
				return $out;
			}
			
			if (isset($_POST['attached_save'])) {
				if ($_POST['object_type'] == 'form') {
					parent::updateForm(array('form_fields' => unserialize(stripslashes($_POST[attached_array]))), $_POST['object_id']);
				} elseif ($_POST['object_type'] == 'field') {
					parent::updateField(array('field_options' => unserialize(stripslashes($_POST[attached_array]))), $_POST['object_id']);
				}
				return $out;
			}
			
			if (isset($_POST['object_bulk_apply'])) {
				$out['object_bulk_action'] = $_POST['object_bulk_action'];
				if ($_POST['object_bulk_action'] == 'edit') {
					foreach ($_POST['objects'] as $obj) {
						if (isset($obj['object_do']) && $obj['object_do'] == 1) {
							if ($obj['object_type'] == 'form') {
								parent::updateForm($obj['values'], $obj['object_id']);
							} elseif ($obj['object_type'] == 'field') {
								parent::updateField($obj['values'], $obj['object_id']);
							} elseif ($obj['object_type'] == 'field_option') parent::updateFieldOption($obj['values'], $obj['object_id']);
							elseif ($obj['object_type'] == 'style') parent::updateStyle($obj['values'], $obj['object_id']);
							$out['objects'][] = $obj;
						}
					}
				}
				
				elseif ($_POST['object_bulk_action'] == 'delete') {
					foreach ($_POST['objects'] as $obj) {
						if (isset($obj['object_do']) && $obj['object_do'] == 1) {
							if ($obj['object_type'] == 'form') parent::deleteForm($obj['object_id']);
							elseif ($obj['object_type'] == 'field') parent::deleteField($obj['object_id']);
							elseif ($obj['object_type'] == 'field_option') parent::deleteFieldOption($obj['object_id']);
							elseif ($obj['object_type'] == 'style') parent::deleteStyle($obj['object_id']);
							elseif ($obj['object_type'] == 'form_submission') {
								parent::deleteUserData($obj['object_id']);
							}
							$out['objects'][] = $obj;
						}
					}
				}
			}
			return $out;
		}
		
		function rateMeForm() {
			?>
            <form class="rate-me" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="TXYVDCH955V28">
                <a href="http://wordpress.org/extend/plugins/custom-contact-forms" title="<?php _e("Rate This Plugin", 'custom-contact-forms'); ?>">
                <?php _e("We need your help to continue development! Please <span>rate this plugin</span> to show your support.", 'custom-contact-forms'); ?></a>
			    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                <img alt="Donate to Custom Contact Forms plugin" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>
            <?php
		}
		
		function printAdminPage() {
			$admin_options = parent::getAdminOptions();
			if ($admin_options['show_install_popover'] == 1) {
				$admin_options['show_install_popover'] = 0;
				?>
                <script type="text/javascript" language="javascript">
					$j(document).ready(function() {
						$j("#ccf-usage-popover").dialog('open');
					});
				</script>
                <?php
				update_option(parent::getAdminOptionsName(), $admin_options);
			}
			$this->handleAdminPostRequests();
			if (isset($_POST['insert_default_content'])) {
				ccf_utils::load_module('db/custom-contact-forms-default-db.php');
				$this->action_complete = __('Default content has been inserted!', 'custom-contact-forms');
				new CustomContactFormsDefaultDB();
			} elseif (isset($_POST['contact_author'])) {
				$this->action_complete = __('Your message has been sent!', 'custom-contact-forms');
				$this_url = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $_SERVER['SERVER_NAME'];
				$this->contactAuthor($_POST['name'], $_POST['email'], $this_url, $_POST['message'], $_POST['type'], $_POST['host'], $_POST['ccf-version'], $_POST['wp-version']);
			} elseif (isset($_GET['clear_tables']) && $_GET['clear_tables'] == 1) {
				parent::emptyAllTables();
			}
			$styles = parent::selectAllStyles();
			$style_options = '<option value="0">Default</option>';
			foreach ($styles as $style)
				$style_options .= '<option value="'.$style->id.'">'.$style->style_slug.'</option>';
			?>
			<div id="customcontactforms-admin">
			  <div class="plugin-header">
				<h2>
					<?php _e("Custom Contact Forms", 'custom-contact-forms'); ?>
				</h2>
             	<div class="links">
                	<a href="javascript:void(0)" class="quick-start-button">Quick Start Guide</a> - <a href="javascript:void(0)" class="usage-popover-button">Plugin Usage Manual</a>
              	</div>
              </div>
              <div id="ccf-tabs">
			  <ul id="plugin-nav">
				<li><a href="#forms"><?php _e("Forms", 'custom-contact-forms'); ?></a></li>
                <li><a href="#fields"><?php _e("Fields", 'custom-contact-forms'); ?></a></li>
				<li><a href="#field-options"><?php _e("Field Options", 'custom-contact-forms'); ?></a></li>
				<li><a href="#styles"><?php _e("Styles", 'custom-contact-forms'); ?></a></li>
				<li><a href="#support"><?php _e("Support", 'custom-contact-forms'); ?></a></li>
				<li><a href="#advanced"><?php _e("Advanced", 'custom-contact-forms'); ?></a></li>
				<li><a href="#news"><?php _e("News", 'custom-contact-forms'); ?></a></li>
			  </ul>
			  <a class="gravity" href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=125082">Are you looking for a more customizable, reliable, and secure contact form solution for WordPress? Try <span>Gravity Forms</span>.</a>
			  <a class="genesis" href="http://www.shareasale.com/r.cfm?b=241369&u=481196&m=28169&urllink=&afftrack="><?php _e('Custom Contact Forms works best with any of the 20+ ', 'custom-contact-forms'); ?><span><?php _e('Genesis', 'custom-contact-forms'); ?></span> <?php _e('Wordpress child themes. The', 'custom-contact-forms'); ?> <span><?php _e('Genesis Framework', 'custom-contact-forms'); ?></span> <?php _e('empowers you to quickly and easily build incredible websites with WordPress.', 'custom-contact-forms'); ?></a>
			
			<form class="blog-horizontal-form" method="post" action="http://www.aweber.com/scripts/addlead.pl">
            	<input type="hidden" name="meta_web_form_id" value="1578604781" />
				<input type="hidden" name="meta_split_id" value="" />
				<input type="hidden" name="listname" value="ccf-plugin" />
				<input type="hidden" name="redirect" value="http://www.taylorlovett.com/wordpress-plugins/tutorials-offers-tips/" id="redirect_5832e41084448adb07da67a35dc83c27" />
				<input type="hidden" name="meta_adtracking" value="CCF_-_Wordpress_Plugins_Horizontal" />
				<input type="hidden" name="meta_message" value="1" />
				<input type="hidden" name="meta_required" value="name,email" />
				<span><?php _e('WP Blogging Tips, Downloads, SEO Tricks & Exclusive Tutorials', 'custom-contact-forms'); ?></span>
                <input type="text" name="name" value="Your Name" onclick="value=''" />
                <input type="text" name="email" value="Your Email" onclick="value=''" />
                <input type="submit" value="Sign Up for Free" />
            </form>
            
			<?php if (!empty($this->action_complete)) { ?>
			<div id="message" class="updated below-h2">
				<p><?php echo esc_html($this->action_complete); ?></p>
			</div>
			<?php } ?>  
              <div id="forms">
			  <div id="create-forms" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("Create A Form", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
				  <form id="ccf-create-form" method="post" action="<?php menu_page_url("custom-contact-forms", 1); ?>">
                  <input value="forms" name="selected_tab" type="hidden" />
					<ul class="left">
					  <li>
						<label for="object[form_slug]">*
						<?php _e("Form Slug:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" class="ccf-width250" maxlength="100" name="object[form_slug]" />
						<br />
						<?php _e("This is just a unique way for CCF to refer to your form. Must be unique from other slugs and contain only underscores and alphanumeric characters.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="object[form_title]">
						<?php _e("Form Title:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" type="text" maxlength="200" name="object[form_title]" />
						<?php _e("This text is displayed above the form as the heading.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="object[form_action]">
						<?php _e("Form Style:", 'custom-contact-forms'); ?>
						</label>
						<select name="object[form_style]" class="form_style_input">
						  <?php echo $style_options; ?>
						</select></li>
					  <li>
						<label for="object[submit_button_text]">
						<?php _e("Submit Button Text:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" type="text" maxlength="200" name="object[submit_button_text]" />
					  </li>
					  <li>
						<label for="object[form_email]">
						<?php _e("Form Destination Email:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" type="text" name="object[form_email]" />
						<br />
						<?php _e("Will receive all submissions from this form; if left blank it will use the default specified in general settings. You can have form submissions sent to multiple emails by separating them with semicolons.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="object[form_email_subject]">
						<?php _e("Form Email Subject:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" type="text" name="object[form_email_subject]" />
						<br />
						<?php _e("When submitted and configured accordingly, the form will send an email with this subject.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="object[form_email_name]">
						<?php _e("Form Email Name:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" type="text" name="object[form_email_name]" />
						<br />
						<?php _e("When submitted and configured accordingly, the form will send an email with this as the email 'from name'.", 'custom-contact-forms'); ?>
					  </li>
                    </ul>
                    <ul class="right">
					  <li>
						<label for="object[form_success_message]">
						<?php _e("Form Success Message:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" type="text" name="object[form_success_message]" />
						<br />
						<?php _e("Will be displayed in a popover when the form is filled out successfully when no custom success page is specified; if left blank it will use the default specified in general settings.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="object[form_success_title]">
						<?php _e("Form Success Message Title:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" type="text" name="object[form_success_title]" />
						<br />
						<?php _e("Will be displayed in a popover when the form is filled out successfully when no custom success page is specified; if left blank it will use the default specified in general settings.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="object[form_thank_you_page]">
						<?php _e("Custom Success URL:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" type="text" name="object[form_thank_you_page]" />
						<br />
						<?php _e("If this is filled out, users will be sent to this page when they successfully fill out this form. If it is left blank, a popover showing the form's 'success message' will be displayed on form success.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
					  <label for="form_access"><?php _e('Who Can View This Form:', ''); ?></label>
                            
                            <?php
							$roles = parent::getRolesArray();
							$i = 0;
							foreach ($roles as $role) {
								if ($i == 3) echo '<br />';
								?>
								<div class="role">
								 <input type="checkbox" checked="checked" name="object[form_access][]" value="<?php echo esc_attr($role); ?>" /> 
								 <?php echo esc_html($role); ?>
								</div>
								<?php
								$i++;
							}
							?><br />
							<?php _e('Choose which types of users should be able to view this form.', 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<input type="hidden" name="object_type" value="form" />
						<input type="submit" class="create-button" value="<?php _e("Create Form", 'custom-contact-forms'); ?>" name="object_create" />
					  </li>
					  <li class="attach"><span class="ccf-red">*</span> <?php _e('You should go to the form manager to attach fields to this form after you create it.', 'custom-contact-forms'); ?></li>
					</ul>
				  </form>
                  <div class="ccf-clear"></div>
				</div>
              </div>
              
              <h3 class="manage-h3">
				<?php _e("Manage Forms", 'custom-contact-forms'); ?>
			  </h3>
			  <form class="ccf-edit-ajax" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
              <input type="hidden" name="selected_tab" value="forms" />
			  <table class="widefat post" id="manage-forms" cellspacing="0">
				<thead>
				  <tr>
					<th scope="col" class="manage-column check-col"><input type="checkbox" class="checkall" /></th>
					<th scope="col" class="manage-column form-code"><?php _e("Form Display Code", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column form-slug"><?php _e("Theme Display Code", 'custom-contact-forms'); ?></th>
                    <th scope="col" class="manage-column form-slug"><?php _e("Slug", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column form-title"><?php _e("Title", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column form-style"><?php _e("Style", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column form-expand"></th>
				  </tr>
				</thead>
				
				<tbody>
				  <?php
			$forms = parent::selectAllForms();
			for ($i = 0; $i < count($forms); $i++) {
			$form_methods = '<option>Post</option><option>Get</option>';
			$form_methods = str_replace('<option>'.$forms[$i]->form_method.'</option>',  '<option selected="selected">'.$forms[$i]->form_method.'</option>', $form_methods);
			$add_fields = $this->getFieldsForm(true);
			$this_style = parent::selectStyle($forms[$i]->form_style, '');
			if ($this_style != NULL)
				$sty_opt = str_replace('<option value="'.$forms[$i]->form_style.'">'.$this_style->style_slug.'</option>', '<option value="'.$forms[$i]->form_style.'" selected="selected">'.$this_style->style_slug.'</option>', $style_options);
			else
				$sty_opt = $style_options;
			?>
				  <tr class="row-form-<?php echo esc_attr($forms[$i]->id); ?> <?php if ($i % 2 == 0) echo 'ccf-evenrow'; ?>">
					<td><input type="checkbox" class="object-check" value="1" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][object_do]" /></td>
					<td><input type="text" class="ccf-width175" value="[customcontact form=<?php echo esc_attr($forms[$i]->id); ?>]" name="post_code_<?php echo esc_attr($forms[$i]->id); ?>" /></td>
					<td><input type="text" class="ccf-width125" value="&lt;?php if (function_exists('serveCustomContactForm')) { serveCustomContactForm(<?php echo esc_attr($forms[$i]->id); ?>); } ?&gt;" name="theme_code_<?php echo esc_attr($forms[$i]->id); ?>" /></td>
                    <td><input type="text" class="ccf-width175" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_slug]" value="<?php echo esc_attr($forms[$i]->form_slug); ?>" /></td>
					<td><input type="text" class="ccf-width175" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_title]" value="<?php echo esc_attr($forms[$i]->form_title); ?>" /></td>
					<td><select name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_style]" class="form_style_input">
						<?php echo $sty_opt; ?>
					  </select></td>
					<td><input class="object-id" type="hidden" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][object_id]" value="<?php echo esc_attr($forms[$i]->id); ?>" />
					  <input type="hidden" class="object-type" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][object_type]" value="form" />
                      <input type="button" class="single-save" value="<?php _e('Save', 'custom-contact-forms'); ?>" /> 
                      <input type="button" class="single-delete" value="<?php _e('Delete', 'custom-contact-forms'); ?>" />
					  <input type="button" class="form-options-expand-link" value="<?php _e('Options', 'custom-contact-forms'); ?>" />
					  <div class="loading-img-container"><img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="ccf-hide loading-img-inner-form-<?php echo esc_attr($forms[$i]->id); ?>" /></div>
                    </td>
				  </tr>
				  <tr class="row-form-<?php echo esc_attr($forms[$i]->id); ?> <?php if ($i % 2 == 0) echo 'ccf-evenrow'; ?>">
					<td class="form-extra-options ccf-center ccf-hide" colspan="8">
                    
                    	<div class="left">
                        	<span>Email</span>
                            <ul>
                            	<li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("Form submissions will be emailed to this address.", 'custom-contact-forms'); ?>">(?)</a> Destination Email:</label> <input type="text" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_email]" class="ccf-width250" value="<?php echo esc_attr($forms[$i]->form_email); ?>" /></li>
                            	<li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("This is the form email subject sent to the destination email address. If left blank, the default from General Settings will be used.", 'custom-contact-forms'); ?>">(?)</a> Email Subject:</label> <input class="ccf-width250" type="text" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_email_subject]" maxlength="250" value="<?php echo esc_attr($forms[$i]->form_email_subject); ?>" /></li>
                                <li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("This is the from name of the email sent on successful form submission. If left blank, the default from General Settings will be used.", 'custom-contact-forms'); ?>">(?)</a> Email From Name:</label> <input class="ccf-width250" type="text" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_email_name]" maxlength="100" value="<?php echo esc_attr($forms[$i]->form_email_name); ?>" /></li>
                            </ul>
                        	<span>Advanced</span>
                            <ul>
                            	<li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("The Form Method is the method by which information is transfer through your form. If you aren't an expert with HTML and PHP, leave this as Post.", 'custom-contact-forms'); ?>">(?)</a> Method:</label> <select name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_method]">
								<?php echo $form_methods; ?>
							  </select></li>
                              	<li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("This lets you process your forms through alternate scripts. If you use a service like InfusionSoft or Aweber, set this to be the same form action as the code provided to you by that service, otherwise leave this blank.", 'custom-contact-forms'); ?>">(?)</a> Form Action:</label> <input class="ccf-width250" type="text" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_action]" value="<?php echo esc_attr($forms[$i]->form_action); ?>" /></li>
                            	<li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("Insert the page id's that your form will be used on. This will make it so the plugin will only load JS and CSS files on these select pages. This will improve your site's load time.", 'custom-contact-forms'); ?>">(?)</a> Form Pages:</label> <input class="ccf-width250" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_pages]" type="text" value="<?php echo esc_attr($forms[$i]->form_pages); ?>" /></li>
                            	<li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("If you want to show this form to only certain types of users, you can uncheck boxes accordingly. To show this form to anyone, check all the boxes. This will only take effect if 'Form Access Capabilities' is enabled in general settings.", 'custom-contact-forms'); ?>">(?)</a> Form Access:</label> 
                                	<ul><?php
							$roles = parent::getRolesArray();
							$access_array = parent::getFormAccessArray($forms[$i]->form_access);
							foreach ($roles as $role) {
								?>
								 <li><input type="checkbox" <?php if (parent::formHasRole($access_array, $role)) { echo 'checked="checked"'; } ?> name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_access][]" value="<?php echo esc_attr($role); ?>" /> 
								<?php echo esc_html($role); ?>
                                </li>
                                <?php
							}
							?></ul><input name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_access_update]" type="hidden" value="1" />
                                </li>
                                <li></li>
                            </ul>
                        </div>
                        <div class="right">
                        	<span>Successful Submission</span>
                            <ul>
                            	<li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("If this is filled out, users will be sent to this thank you page when they successfully fill out this form. If it is left blank, a popover showing the form's 'success message' will be displayed on form success.", 'custom-contact-forms'); ?>">(?)</a> Custom Success URL:</label> <input class="ccf-width250" type="text" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_thank_you_page]" value="<?php echo esc_attr($forms[$i]->form_thank_you_page); ?>" /></li>
                                <li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("This will be displayed as the header in a popover when the form is filled out successfully when no custom success page is specified; if left blank it will use the default specified in general settings.", 'custom-contact-forms'); ?>">(?)</a> Success Message Title:</label> <input class="ccf-width250" type="text" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_success_title]" value="<?php echo esc_attr($forms[$i]->form_success_title); ?>" /></li>
                                <li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("This will be displayed in a popover when the form is filled out successfully when no custom success page is specified; if left blank it will use the default specified in general settings.", 'custom-contact-forms'); ?>">(?)</a> Success Message:</label> <textarea name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][form_success_message]"><?php echo esc_attr($forms[$i]->form_success_message); ?></textarea></li>
                            </ul>
                            
                            <span>Customization</span>
                            <ul>
                            	<li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("This text will display on the form submit button.", 'custom-contact-forms'); ?>">(?)</a> Button Text:</label> <input class="ccf-width250" type="text" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][submit_button_text]" value="<?php echo esc_attr($forms[$i]->submit_button_text); ?>" /></li>
                                <li><label><a href="javascript:void(0)" class="toollink" title="<?php _e("This field allows you to insert HTML directly after the starting <form> tag.", 'custom-contact-forms'); ?>">(?)</a> Custom Code:</label> <textarea name="objects[<?php echo esc_attr($forms[$i]->id); ?>][values][custom_code]"><?php echo esc_attr($forms[$i]->custom_code); ?></textarea></li>
                            </ul>
                        </div>
                        <div class="ccf-clear"></div>
                        <div class="fattach">
							
							<div class="attach">
							<p><label><span>
							  <?php _e("Add A Field:", 'custom-contact-forms'); ?>
							  </span></label></p>
							  <select class="onObject<?php echo esc_attr($forms[$i]->id); ?> attach-object field-dropdown objectTypeForm" name="objects[<?php echo esc_attr($forms[$i]->id); ?>][attach]">
								<?php echo $add_fields; ?>
							  </select> <input class="attach-button" type="button" value="<?php _e('Attach', 'custom-contact-forms'); ?>" />
                              <p>
							  <span class="ccf-red ccf-bold">*</span>
							  <?php _e("Attach fixed fields or ones you", 'custom-contact-forms'); ?>
							  <a href="#create-fields">
							  <?php _e("create", 'custom-contact-forms'); ?>
							  </a>. </p></div>
                              
                              <div class="attached">
								<p><span>
							  <?php _e("Attached Fields:", 'custom-contact-forms'); ?>
							  </span></p>
							  <?php
				$attached_fields = parent::getAttachedFieldsArray($forms[$i]->id);
				
					echo '<ul class="onObject' . esc_attr($forms[$i]->id) . ' sortable field-list ccfsort" id="' . esc_attr($form->form_slug) . '">';
					foreach($attached_fields as $attached_field) {
						$this_field = parent::selectField($attached_field, '');
						?>
                        <li class="field<?php echo esc_attr($this_field->id); ?> ui-state-default"><span>&times;</span> <?php
      echo esc_html($this_field->field_slug);?> (<?php echo esc_html($this_field->field_type);?>)</li>
                        <?php
					}
					echo '</ul>';
			  ?>
                              <input class="attached-update-button" type="button" value="<?php _e('Save Field Configuration', 'custom-contact-forms'); ?>" />
              			<img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="ccf-hide loading-img-field-config-form-<?php echo esc_attr($forms[$i]->id); ?>" />
							
							</div>
                            <div class="ccf-clear"></div>
                              </div>
                    
                  </td>
				  </tr>
				<?php
			}
			$remember_check = ($admin_options['remember_field_values'] == 0) ? 'selected="selected"' : '';
			$remember_fields = '<option value="1">'.__('Yes', 'custom-contact-forms').'</option><option '.$remember_check.' value="0">'.__('No', 'custom-contact-forms').'</option>';
			$border_style_options = '<option>solid</option><option>dashed</option>
			<option>grooved</option><option>double</option><option>dotted</option><option>ridged</option><option>none</option>
			<option>inset</option><option>outset</option>';
			?>
				</tbody>
                
				<tfoot>
				  <tr>
					<th scope="col" class="manage-column check-col"><input type="checkbox" class="checkall" /></th>
					<th scope="col" class="manage-column form-code"><?php _e("Form Display Code", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column form-slug"><?php _e("Theme Display Code", 'custom-contact-forms'); ?></th>
                    <th scope="col" class="manage-column form-slug"><?php _e("Slug", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column form-title"><?php _e("Title", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column form-style"><?php _e("Style", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column form-expand"></th>
				  </tr>
				</tfoot>
			  </table>
			  <select class="bulk-dropdown" name="object_bulk_action">
			  <option value="0"><?php _e('Bulk Actions', 'custom-contact-forms'); ?></option>
				<option value="edit"><?php _e('Save', 'custom-contact-forms'); ?></option>
				<option value="delete"><?php _e('Delete', 'custom-contact-forms'); ?></option></select> 
			  <input type="submit" name="object_bulk_apply" class="bulk-apply" value="<?php _e('Apply', 'custom-contact-forms'); ?>" /> <img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="loading-img" />
			  </form>
              </div>
			  
			  <div id="fields">
            	
			  <div id="create-fields" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("Create A Form Field", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
				  <form id="ccf-create-field" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                  <input type="hidden" name="selected_tab" value="fields" />
					<ul class="left">
					  <li>
						<label for="field_slug">*
						<?php _e("Field Slug:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" name="object[field_slug]" type="text" maxlength="40" />
						<br />
						<?php _e("This is just a unique way for CCF to refer to your field. Must be unique from other slugs and contain only underscores and alphanumeric characters.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="field_label">
						<?php _e("Field Label:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" name="object[field_label]" type="text" maxlength="100" />
						<br />
						<?php _e("The field label is displayed next to the field and is visible to the user.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="field_type">*
						<?php _e("Field Type:", 'custom-contact-forms'); ?>
						</label>
						<select name="object[field_type]" class="field-type-selector">
						  <option>Text</option>
                          <option>Date</option>
                          <option>File</option>
						  <option>Textarea</option>
						  <option>Hidden</option>
						  <option>Checkbox</option>
						  <option>Radio</option>
						  <option>Dropdown</option>
						  <option>Fieldset</option>
						</select>
					  </li>
					  <li>
						<label for="field_value">
						<?php _e("Initial Value:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" name="object[field_value]" type="text" maxlength="50" />
						<br />
						<?php _e("This is the initial value of the field. If you set the type as checkbox, it is recommend you set this to what the checkbox is implying. For example if I were creating the checkbox 
						'Are you human?', I would set the initial value to 'Yes'.", 'custom-contact-forms'); ?>
						<?php _e("If you set the field type as 'Dropdown' or 'Radio', you should enter the slug of the", 'custom-contact-forms'); ?>
						<a href="#manage-field-options" title="<?php _e("Create a Field Option", 'custom-contact-forms'); ?>"><?php _e("field option", 'custom-contact-forms'); ?></a>
						<?php _e("you would like initially selected.", 'custom-contact-forms'); ?>
						</li>
					  <li>
						<label for="field_maxlength">
						<?php _e("Max Length:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" size="10" name="object[field_maxlength]" type="text" maxlength="4" />
						<br />
						<?php _e("0 for no limit; only applies to Text fields", 'custom-contact-forms'); ?>
					  </li>
                    </ul>
                    <ul class="right">
					  <li>
						<label for="field_required">*
						<?php _e("Required Field:", 'custom-contact-forms'); ?>
						</label>
						<select name="object[field_required]">
						  <option value="0">
						  <?php _e("No", 'custom-contact-forms'); ?>
						  </option>
						  <option value="1">
						  <?php _e("Yes", 'custom-contact-forms'); ?>
						  </option>
						</select>
						<br />
						<?php _e("If a field is required and a user leaves it blank, the plugin will display an error message (which you can customize using 'Field Error') explaining the problem.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="field_instructions">
						<?php _e("Field Instructions:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" name="object[field_instructions]" type="text" />
						<br />
						<?php _e("If this is filled out, a tooltip popover displaying this text will show when the field is selected.", 'custom-contact-forms'); ?>
					  </li>
                      <li>
						<label for="field_class">
						<?php _e("Field Class:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" name="object[field_class]" type="text" />
						<br />
						<?php _e("If you manage your own .css stylesheet, you can use this to attach a class to this field. Leaving this blank will do nothing.", 'custom-contact-forms'); ?>
					  </li>
                      <li>
						<label for="field_error">
						<?php _e("Field Error:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" name="object[field_error]" type="text" />
						<br />
						<?php _e("If a user leaves this field blank and the field is required, this error message will be shown. A generic default will show if left blank.", 'custom-contact-forms'); ?>
					  </li>
                      <li class="file-fields">
						<label for="field_max_upload_size">
						<?php _e("Max File Size Allowed:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" name="object[field_max_upload_size]" value="5000" type="text" /> <?php _e('KB', 'custom-contact-forms'); ?>
						<br />
						<?php _e("If a user tries to upload a file larger than the max upload size, an error message will be displayed.", 'custom-contact-forms'); ?>
                      </li>
                      <li class="file-fields">
                        <label for="field_allowed_file_extensions">
						<?php _e("Allowed File Extensions for Upload:", 'custom-contact-forms'); ?>
						</label>
						<input class="ccf-width250" name="object[field_allowed_file_extensions]" type="text" />
						<br />
						<?php _e("If a user tries to upload a file with an extension not in this list, an error will be shown. Separate file extensions with a comma. Ex: doc, jpg, jpeg, txt. If left blank, all extensions will be allowed.", 'custom-contact-forms'); ?>
                      </li>
					  <li>
						<input type="hidden" name="object[user_field]" value="1" />
						<input type="hidden" name="object_type" value="field" />
						<input type="submit" value="<?php _e("Create Field", 'custom-contact-forms'); ?>" name="object_create" class="create-button" />
					  </li>
					  <li class="attach"><span class="ccf-red">*</span> <?php _e('If this is a dropdown or radio field, you should go to the field manager below to attach field options after you create it.', 'custom-contact-forms'); ?></li>
					  
					</ul>
				  </form>
                  <div class="ccf-clear"></div>
				</div>
			  </div>
              
              <h3 class="manage-h3">
				<?php _e("Manage User Fields", 'custom-contact-forms'); ?>
			  </h3>
			  <form class="ccf-edit-ajax" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
              <input type="hidden" name="selected_tab" value="fields" />
			  <table class="widefat post" id="manage-fields" cellspacing="0">
				<thead>
				  <tr>
					<th scope="col" class="manage-column check-col"><input type="checkbox" class="checkall" /></th>
					<th scope="col" class="manage-column field-slug"><?php _e("Slug", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-label"><?php _e("Label", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-type"><?php _e("Type", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-value"><?php _e("Initial Value", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-required"><?php _e("Required", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-expand"></th>
				  </tr>
				</thead>
				<tbody>
				  <?php
			$fields = parent::selectAllFields();
			for ($i = 0, $z = 0; $i < count($fields); $i++, $z++) {
			if ($fields[$i]->user_field == 0) { $z--; continue; }
			$attached_options = parent::getAttachedFieldOptionsArray($fields[$i]->id);
			$field_types = '<option>Text</option><option>Date</option><option>File</option><option>Textarea</option><option>Hidden</option><option>Checkbox</option><option>Radio</option><option>Dropdown</option><option>Fieldset</option>';
			$field_types = str_replace('<option>'.$fields[$i]->field_type.'</option>',  '<option selected="selected">'.$fields[$i]->field_type.'</option>', $field_types);
			
			?>
				<tr class="row-field-<?php echo esc_attr($fields[$i]->id); ?> <?php if ($z % 2 == 1) echo ' ccf-evenrow'; ?>">
					<td><input class="object-check" type="checkbox" value="1" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][object_do]" /></td>
					<td><input type="text" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_slug]" class="ccf-width125" maxlength="50" value="<?php echo esc_attr($fields[$i]->field_slug); ?>" /></td>
					<td><input type="text" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_label]" class="ccf-width200" maxlength="100" value="<?php echo esc_attr($fields[$i]->field_label); ?>" /></td>
					<td><select name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_type]">
						<?php echo $field_types; ?>
					  </select></td>
					<td><input type="text" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_value]" maxlength="50" class="ccf-width100" value="<?php echo esc_attr($fields[$i]->field_value); ?>" /></td>
					<td><select name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_required]">
						<option value="1">
						<?php _e("Yes", 'custom-contact-forms'); ?>
						</option>
						<option value="0" <?php if ($fields[$i]->field_required != 1) echo 'selected="selected"'; ?>>
						<?php _e("No", 'custom-contact-forms'); ?>
						</option>
					  </select></td>
					<td><input type="hidden" class="object-type" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][object_type]" value="field" />
					  <input type="hidden" class="object-id" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][object_id]" value="<?php echo esc_attr($fields[$i]->id); ?>" />
					  <input type="button" class="single-save" value="<?php _e('Save', 'custom-contact-forms'); ?>" /> 
                      <input type="button" class="single-delete" value="<?php _e('Delete', 'custom-contact-forms'); ?>" />
					  <input type="button" class="fields-options-expand-link" value="<?php _e('Options', 'custom-contact-forms'); ?>">
				  	  <div class="loading-img-container"><img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="ccf-hide loading-img-inner-field-<?php echo esc_attr($fields[$i]->id); ?>" /></div>
                    </td>
                  </tr>
				  <?php $show_field_options = ($fields[$i]->field_type == 'Radio' || $fields[$i]->field_type == 'Dropdown' || $fields[$i]->field_type == 'Checkbox') ? true : false; ?>
				  <tr class="row-field-<?php echo esc_attr($fields[$i]->id); ?> <?php if ($z % 2 == 1) echo 'ccf-evenrow'; ?>">
					<td class="fields-extra-options ccf-hide" colspan="8">
                      <div class="one">
                      	<label for="field_instructions">
						<a href="javascript:void(0)" class="toollink" title="<?php _e('If this is filled out, a tooltip popover displaying this text will show when the field is selected.', 'custom-contact-forms'); ?>">(?)</a>
						<?php _e("Field Instructions:", 'custom-contact-forms'); ?>
						</label>
						<textarea class="ccf-width250" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_instructions]"><?php echo esc_attr($fields[$i]->field_instructions); ?></textarea>
                      </div>
                      <div class="two">
                      	<label for="field_error">
						<a href="javascript:void(0)" class="toollink" title="<?php _e('This lets you customize the error message displayed when this field is required and left blank.', 'custom-contact-forms'); ?>">(?)</a>
					    <?php _e("Field Error:", 'custom-contact-forms'); ?>
						</label>
						<textarea class="ccf-width250" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_error]"><?php echo esc_attr($fields[$i]->field_error); ?></textarea> 
					  </div>
                      <div class="three">
                      	<label for="field_class">
                        <a href="javascript:void(0)" class="toollink" title="<?php _e('If you manage a .CSS file for your theme, you could create a class in that file and add it to this field. If the form attaching this field is using a "Form Style" other than the default, styles inherited from the "Field Class" might be overwritten.', 'custom-contact-forms'); ?>">(?)</a>
					  	<?php _e("Field Class:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" class="ccf-width75" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_class]" value="<?php echo esc_attr($fields[$i]->field_class); ?>" />
						<br />
                        <?php if ($fields[$i]->field_type != 'Dropdown' && $fields[$i]->field_type != 'Radio' && $fields[$i]->field_type != 'Checkbox') { ?>
                        <label for="field_maxlength"><a href="javascript:void(0)" class="toollink" title="<?php _e('Max length allows you to set a cap on the amount of characters a user can submit.', 'custom-contact-forms'); ?>">(?)</a>
					  	<?php _e('Max Length:', 'custom-contact-forms'); ?>
                        </label>
					  	<input type="text" class="ccf-width75" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_maxlength]" value="<?php echo esc_attr($fields[$i]->field_maxlength); ?>" />
					  	<br />
						<?php } ?>
                        <?php if ($fields[$i]->field_type == 'File') { ?>
                      	<label for="field_max_upload_size"><a href="javascript:void(0)" class="toollink" title="<?php _e('If a user tries to upload a file greater than the value in this field, an error will be shown. Upload size is in KB. If this is left blank or set to 0, then there will be no maximum file size for this field.', 'custom-contact-forms'); ?>">(?)</a>
					    <?php _e("Max Upload Size:", 'custom-contact-forms'); ?></label>
                        <input type="text" class="ccf-width75" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_max_upload_size]" value="<?php echo esc_attr($fields[$i]->field_max_upload_size); ?>" /><?php _e('KB', 'custom-contact-forms'); ?>
                        <br />
                        <label for="field_allowed_file_extensions"><a href="javascript:void(0)" class="toollink" title="<?php _e('If a user tries to upload a file with an extension not in this list, an error will be shown. If this is left blank, then all file extensions will be accepted. Separate file extensions with a comma. Ex: doc, jpg, jpeg, bmp, gif, txt', 'custom-contact-forms'); ?>">(?)</a>
					    <?php _e("Allowed File Extensions:", 'custom-contact-forms'); ?></label>
                        <input type="text" class="ccf-width75" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_allowed_file_extensions]" value="<?php $exts = unserialize($fields[$i]->field_allowed_file_extensions); echo (!empty($exts)) ? esc_attr(@implode(', ', $exts)) : ''; ?>" />
                      <?php } ?>
                      </div>
					  <?php 
			if ($show_field_options) { ?>
            		  <div class="ccf-clear"></div>
            		  <div class="fattach">
							
							<div class="attach">
							<p><label><span>
							  <?php _e("Add A Field Option:", 'custom-contact-forms'); ?>
							  </span></label></p>
							  <select class="onObject<?php echo esc_attr($fields[$i]->id); ?> attach-object field-option-dropdown objectTypeField" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][attach]">
								<?php
								$options = parent::selectAllFieldOptions();
								foreach ($options as $option) {
									?>
                                    <option value="<?php echo esc_attr($option->id); ?>"><?php echo esc_attr($option->option_slug); ?></option>
                                    <?php
								}
								?>
							  </select> <input class="attach-button" type="button" value="<?php _e('Attach', 'custom-contact-forms'); ?>" />
                              <p>
							  <span class="ccf-red ccf-bold">*</span>
							  <?php _e("Attach field options you ", 'custom-contact-forms'); ?>
							  <a href="#field-options">
							  <?php _e("create", 'custom-contact-forms'); ?>
							  </a>. </p></div>
                              
                              <div class="attached">
								<p><span>
							  <?php _e("Attached Field Options:", 'custom-contact-forms'); ?>
							  </span></p>
							  <?php
				$attached_options = parent::getAttachedFieldOptionsArray($fields[$i]->id);
				
					echo '<ul class="onObject'.esc_attr($fields[$i]->id).' sortable field-option-list ccfsort" id="'.esc_attr($field->field_slug) . '">';
					foreach($attached_options as $attached_option) {
						$this_option = parent::selectFieldOption($attached_option, '');
						?>
                        <li class="field<?php echo esc_attr($this_option->id); ?> ui-state-default"><span>&times;</span> <?php
      echo esc_html($this_option->option_slug);?></li>
                        <?php
					}
					echo '</ul>';
			  ?>
                              <input class="attached-update-button" type="button" value="<?php _e('Save Option Configuration', 'custom-contact-forms'); ?>" />
              			<img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="ccf-hide loading-img-field-config-field-<?php echo esc_attr($fields[$i]->id); ?>" />
							
							</div></div>
                            <div class="ccf-clear"></div>
					  <?php } ?>
					</td>
				  </tr>
				<?php
			}
			?>
				</tbody>
				
				<tfoot>
				  <tr>
					<th scope="col" class="manage-column check-col"><input type="checkbox" class="checkall" /></th>
					<th scope="col" class="manage-column field-slug"><?php _e("Slug", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-label"><?php _e("Label", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-type"><?php _e("Type", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-value"><?php _e("Initial Value", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-required"><?php _e("Required", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-expand"></th>
				  </tr>
				</tfoot>
			  </table>
			  <select class="bulk-dropdown" name="object_bulk_action">
				<option value="0"><?php _e('Bulk Actions', 'custom-contact-forms'); ?></option>
				<option value="edit"><?php _e('Save', 'custom-contact-forms'); ?></option>
				<option value="delete"><?php _e('Delete', 'custom-contact-forms'); ?></option>
			  </select> <input type="submit" name="object_bulk_apply" class="bulk-apply" value="<?php _e('Apply', 'custom-contact-forms'); ?>" /> <img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="loading-img" />
			  </form>
              
              <h3 class="manage-h3">
				<?php _e("Manage Fixed Fields", 'custom-contact-forms'); ?>
			  </h3>
			  <form class="ccf-edit-ajax" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
              <input type="hidden" name="selected_tab" value="fields" />
			  <table class="widefat post" id="manage-fixed-fields" cellspacing="0">
				<thead>
				  <tr>
					<th scope="col" class="manage-column check-col"><input type="checkbox" class="checkall" /></th>
					<th scope="col" class="manage-column field-slug"><?php _e("Slug", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-label"><?php _e("Label", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-type"><?php _e("Type", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-value"><?php _e("Initial Value", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-value"><?php _e("Required", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-expand"></th>
					</tr>
				</thead>
				<tbody>
				  <?php
			$fields = parent::selectAllFields();
			for ($i = 0, $z = 0; $i < count($fields); $i++, $z++) {
			if ($fields[$i]->user_field == 1) { $z--; continue;}
			$field_types = '<option>Text</option><option>Textarea</option><option>Hidden</option><option>Checkbox</option><option>Fieldset</option>';
			$field_types = str_replace('<option>'.$fields[$i]->field_type.'</option>',  '<option selected="selected">'.$fields[$i]->field_type.'</option>', $field_types);
			
			?>
				  <tr class="row-field-<?php echo esc_attr($fields[$i]->id); ?> <?php if ($z % 2 == 0) echo 'ccf-evenrow'; ?>">
					<td><input class="object-check" type="checkbox" value="1" name="objects[<?php echo esc_attr($fields[$i]->id	); ?>][object_do]" /></td>
					<td><?php echo esc_attr($fields[$i]->field_slug); ?></td>
					<td><?php if ($fields[$i]->field_slug == 'resetButton') { _e('None', 'custom-contact-forms'); } else { ?>
					  <input type="text" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_label]" maxlength="100" value="<?php echo esc_attr($fields[$i]->field_label); ?>" />
					  <?php } ?></td>
					<td><?php echo esc_attr($fields[$i]->field_type); ?>
					<td><?php if ($fields[$i]->field_type != 'Checkbox') { ?>
					  <input type="text" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_value]" class="ccf-width75" maxlength="50" value="<?php echo esc_attr($fields[$i]->field_value); ?>" />
					  <?php } else {
			echo esc_attr($fields[$i]->field_value);
			?>
					  <?php } ?>
					</td>
					<td><?php if ($fields[$i]->field_slug == 'fixedEmail' || $fields[$i]->field_slug == 'emailSubject' || $fields[$i]->field_slug == 'fixedWebsite' || $fields[$i]->field_slug == 'usaStates' || $fields[$i]->field_slug == 'datePicker' || $fields[$i]->field_slug == 'allCountries') { ?>
					  <select name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_required]">
						<option value="1">
						<?php _e("Yes", 'custom-contact-forms'); ?>
						</option>
						<option <?php if($fields[$i]->field_required != 1) echo 'selected="selected"'; ?> value="0">
						<?php _e("No", 'custom-contact-forms'); ?>
						</option>
					  </select>
					  <?php } else {
			if ($fields[$i]->field_slug == 'resetButton') {
			echo '-';
			} else {
			_e("Yes", 'custom-contact-forms');
			}
			}
			?>
					</td>
					<td>
                    	<input type="hidden" class="object-type" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][object_type]" value="field" />
					  <input type="hidden" class="object-id" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][object_id]" value="<?php echo esc_attr($fields[$i]->id); ?>" />
					  <input type="button" class="single-save" value="<?php _e('Save', 'custom-contact-forms'); ?>" /> 
					  <input type="button" class="fixed-fields-options-expand-link" value="<?php _e('Options', 'custom-contact-forms'); ?>">
				  	  <div class="loading-img-container"><img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="ccf-hide loading-img-inner-field-<?php echo esc_attr($fields[$i]->id); ?>" /></div>
                    </td>
				  </tr>
				  <tr class="row-field-<?php echo esc_attr($fields[$i]->id); ?> <?php if ($z % 2 == 0) echo 'ccf-evenrow'; ?>">
					<td class="fixed-fields-extra-options ccf-hide" colspan="8">
                      <?php if ($fields[$i]->field_slug == 'resetButton') { ?>
                      <label for="field_class">
                        <a href="javascript:void(0)" class="toollink" title="<?php _e('If you manage a .CSS file for your theme, you could create a class in that file and add it to this field. If the form attaching this field is using a "Form Style" other than the default, styles inherited from the "Field Class" might be overwritten.', 'custom-contact-forms'); ?>">(?)</a>
					  	<?php _e("Field Class:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" class="ccf-width75" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_class]" value="<?php echo esc_attr($fields[$i]->field_class); ?>" />
					    
                      <?php } else { ?>
                      <div class="one">
                      	<label for="field_instructions">
						<a href="javascript:void(0)" class="toollink" title="<?php _e('If this is filled out, a tooltip popover displaying this text will show when the field is selected.', 'custom-contact-forms'); ?>">(?)</a>
						<?php _e("Field Instructions:", 'custom-contact-forms'); ?>
						</label>
						<textarea class="ccf-width250" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_instructions]"><?php echo esc_attr($fields[$i]->field_instructions); ?></textarea>
                      </div>
                      <div class="two">
                      	<label for="field_error">
						<a href="javascript:void(0)" class="toollink" title="<?php _e('This lets you customize the error message displayed when this field is required and left blank.', 'custom-contact-forms'); ?>">(?)</a>
					    <?php _e("Field Error:", 'custom-contact-forms'); ?>
						</label>
						<textarea class="ccf-width250" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_error]"><?php echo esc_attr($fields[$i]->field_error); ?></textarea> 
					  </div>
                      <div class="three">
                        
                      	<label for="field_class">
                        <a href="javascript:void(0)" class="toollink" title="<?php _e('If you manage a .CSS file for your theme, you could create a class in that file and add it to this field. If the form attaching this field is using a "Form Style" other than the default, styles inherited from the "Field Class" might be overwritten.', 'custom-contact-forms'); ?>">(?)</a>
					  	<?php _e("Field Class:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" class="ccf-width75" name="objects[<?php echo esc_attr($fields[$i]->id); ?>][values][field_class]" value="<?php echo esc_attr($fields[$i]->field_class); ?>" />
					    
                        <br />
                        <?php if ($fields[$i]->field_type != 'Checkbox' && $fields[$i]->field_slug != 'allCountries' && $fields[$i]->field_slug != 'usaStates') { ?>
					  	<label for="field_maxlength">
                        <a href="javascript:void(0)" class="toollink" title="<?php _e('If you manage a .CSS file for your theme, you could create a class in that file and add it to this field. If the form attaching this field is using a "Form Style" other than the default, styles inherited from the "Field Class" might be overwritten.', 'custom-contact-forms'); ?>">(?)</a>
					  	<?php _e("Max Length:", 'custom-contact-forms'); ?>
						</label>
                        <input type="text" class="ccf-width50" name="objects[<?php echo $i; ?>][values][field_maxlength]" value="<?php echo esc_attr($fields[$i]->field_maxlength); ?>" />
					  	<?php } ?>
                      </div>
					  <?php } ?>
                      <div class="field_descrip"><?php echo $GLOBALS['ccf_fixed_fields'][$fields[$i]->field_slug]; ?></div>
                    </td>
				  </tr>
				<?php
			}
			?>
				</tbody>
				
				<tfoot>
				  <tr>
					<th scope="col" class="manage-column check-col"><input type="checkbox" class="checkall" /></th>
					<th scope="col" class="manage-column field-slug"><?php _e("Slug", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-label"><?php _e("Label", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-type"><?php _e("Type", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-value"><?php _e("Initial Value", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-value"><?php _e("Required", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column field-expand"></th>
				  </tr>
				</tfoot>
			  </table>
			  <select class="bulk-dropdown" name="object_bulk_action">
				<option value="0"><?php _e('Bulk Actions', 'custom-contact-forms'); ?></option>
				<option value="edit"><?php _e('Save', 'custom-contact-forms'); ?></option>
			  </select> <input type="submit" class="bulk-apply" name="object_bulk_apply" value="<?php _e('Apply', 'custom-contact-forms'); ?>" /> <img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="loading-img" />
			  </form>
              </div>
              
			  <div id="field-options">
			  <div id="manage-field-options" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("Manage Field Options", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
                      <form class="ccf-edit-ajax" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                      <input type="hidden" name="selected_tab" value="field-options" />
					  <table cellpadding="0" cellspacing="0">
						<thead>
						  <tr>
							<th><input class="checkall" type="checkbox" /></th>
							<th><?php _e("Slug", 'custom-contact-forms'); ?></th>
							<th><?php _e("Label", 'custom-contact-forms'); ?></th>
							<th><?php _e("Value", 'custom-contact-forms'); ?></th>
							<th><?php _e("Is Dead", 'custom-contact-forms'); ?></th>
                            <th></th>
						  </tr>
						</thead>
						<tfoot>
						  <tr>
							<th><input class="checkall" type="checkbox" /></th>
							<th><?php _e("Slug", 'custom-contact-forms'); ?></th>
							<th><?php _e("Label", 'custom-contact-forms'); ?></th>
							<th><?php _e("Value", 'custom-contact-forms'); ?></th>
							<th><?php _e("Is Dead", 'custom-contact-forms'); ?></th>
                            <th></th>
						  </tr>
						</tfoot>
						<tbody>
                        <?php
                $options = parent::selectAllFieldOptions(1);
				$option_count = count($options);
                $i = 0;
                foreach ($options as $option) {
                ?>
                        <tr class="row-field_option-<?php echo esc_attr($option->id); ?> <?php if ($i % 2 == 1) echo 'evenrow-field-options'; ?>">
                            <td><input type="checkbox" class="object-check" name="objects[<?php echo esc_attr($option->id); ?>][object_do]" value="1" /> </td>
							<td><input type="text" maxlength="20" name="<?php ?>objects[<?php echo esc_attr($option->id); ?>][values][option_slug]" value="<?php echo esc_attr($option->option_slug); ?>" class="ccf-width50" /></td>
                            <td><input type="text" name="objects[<?php echo esc_attr($option->id); ?>][values][option_label]" value="<?php echo esc_attr($option->option_label); ?>" class="ccf-width100" /></td>
                            <td><input type="text" name="objects[<?php echo esc_attr($option->id); ?>][values][option_value]" value="<?php echo esc_attr($option->option_value); ?>" class="ccf-width100" /></td>
                            <td><select name="objects[<?php echo esc_attr($option->id); ?>][values][option_dead]"><option value="0"><?php _e('No', 'custom-contact-forms'); ?></option><option <?php if ($option->option_dead == 1) echo 'selected="selected"'; ?> value="1"><?php _e('Yes', 'custom-contact-forms'); ?></option></select></td>
						  	<td>
                            	<input type="hidden" class="object-type" name="objects[<?php echo esc_attr($option->id); ?>][object_type]" value="field_option" />
					  			<input type="hidden" class="object-id" name="objects[<?php echo esc_attr($option->id); ?>][object_id]" value="<?php echo esc_attr($option->id); ?>" />
					  			<input type="button" class="single-save" value="<?php _e('Save', 'custom-contact-forms'); ?>" /> 
                      			<input type="button" class="single-delete" value="<?php _e('Delete', 'custom-contact-forms'); ?>" />
					  			<div class="loading-img-container"><img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="ccf-hide loading-img-inner-field_option-<?php echo esc_attr($option->id); ?>" /></div>
                            </td>
                        </tr>
                        <?php
                $i++;
                } if (empty($options)) {
                ?>
                        <tr>
                          <td class="ccf-center"><?php _e("No field options have been created.", 'custom-contact-forms'); ?></td>
                        </tr>
                        <?php
                }
                ?>
						</tbody>
                      </table>
					  <select class="bulk-dropdown" name="object_bulk_action">
					  <option value="0"><?php _e('Bulk Actions', 'custom-contact-forms'); ?></option>
						<option value="edit"><?php _e('Save', 'custom-contact-forms'); ?></option>
						<option value="delete"><?php _e('Delete', 'custom-contact-forms'); ?></option></select> 
					  <input type="submit" class="bulk-apply" name="object_bulk_apply" value="<?php _e('Apply', 'custom-contact-forms'); ?>" /> <img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="loading-img" />
					  </form>
				</div>
			  </div>
			  
              
			  <div id="create-field-options" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("Create A Field Option", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
                      <form id="ccf-create-field-option" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
                      <input type="hidden" name="selected_tab" value="field-options" />
                        <ul>
                          <li>
                            <label for="object[option_slug]">*
                            <?php _e("Option Slug:", 'custom-contact-forms'); ?>
                            </label>
                            <input class="ccf-width250" maxlength="20" type="text" name="object[option_slug]" />
                            <br />
                            <?php _e("Used to identify this option, solely for admin purposes; must be unique, and contain only letters, numbers, and underscores. Example: 'slug_one'", 'custom-contact-forms'); ?>
                          </li>
                          <li>
                            <label for="object[option_label]">*
                            <?php _e("Option Label:", 'custom-contact-forms'); ?>
                            </label>
                            <input class="ccf-width250" type="text" name="object[option_label]" />
                            <br />
                            <?php _e("This is what is shown to the user in the dropdown or radio field. Example: 'United States'", 'custom-contact-forms'); ?>
                          </li>
                          <li>
                            <label for="object[option_value]">
                            <?php _e("Option Value:", 'custom-contact-forms'); ?>
                            </label>
                            <input class="ccf-width250" type="text" name="object[option_value]" />
                            <br />
                            <?php _e("This is the actual value of the option which is not shown to the user. This can be the same thing as the label. An example pairing of label => value is: 'The color green' => 'green' or 'Yes' => '1'. If you don't know what this is, leave it blank.", 'custom-contact-forms'); ?>
                          </li>
						  <li>
							<label for="object[option_dead]"><?php _e("Is Dead Option:", 'custom-contact-forms'); ?></label>
							<select name="object[option_dead]"><option value="0"><?php _e('No', 'custom-contact-forms'); ?></option><option value="1"><?php _e('Yes', 'custom-contact-forms'); ?></option></select>
							<br />
                            <?php _e("A dead option is something like 'Please Select One'. This is a useful tool for required dropdown fields. If a dead option is submitted by a user for a required dropdown field, then the user will have to go back and fill out the field again.", 'custom-contact-forms'); ?>
						  </li>
                          <li>
							<input name="object_type" type="hidden" value="field_option" />
                            <input type="submit" class="object-action" name="object_create" value="<?php _e("Create Field Option", 'custom-contact-forms'); ?>" />
                          
						  </li>
                        </ul>
                      </form>
                  </div>
			  </div>
              </div>
			  
              
			  <div id="styles">
			  <div id="create-styles" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("Create A Style for Your Forms", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
				  <p>
					<?php _e("Use this manager to create styles for your forms. Each field is already filled out with nice look defaults. It is recommended you simply input a slug and click create to see the defaults before you start changing values.", 'custom-contact-forms'); ?>
				  </p>
				  <form id="ccf-create-style" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                  <input type="hidden" name="selected_tab" value="styles" />
					<ul class="style_left">
					  <li>
						<label for="style_slug">*
						<?php _e("Style Slug:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="30" class="ccf-width75" name="object[style_slug]" />
						<?php _e("(Must be unique)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="title_fontsize">
						<?php _e("Title Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="1.2em" class="ccf-width75" name="object[title_fontsize]" />
						<?php _e("(ex: 10pt, 10px, 1em)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="title_fontcolor">
						<?php _e("Title Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="333333" class="ccf-width75 colorfield" name="object[title_fontcolor]" />
						<?php _e("(ex: FF0000)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="label_width">
						<?php _e("Label Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="200px" class="ccf-width75" name="object[label_width]" />
						<?php _e("(ex: 100px or 20%)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="label_fontsize">
						<?php _e("Label Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="1em" class="ccf-width75" name="object[label_fontsize]" />
						<?php _e("(ex: 10px, 10pt, 1em)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="label_fontcolor">
						<?php _e("Label Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="333333" class="ccf-width75 colorfield" name="object[label_fontcolor]" />
						<?php _e("(ex: FF0000)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="input_width">
						<?php _e("Text Field Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="200px" class="ccf-width75" name="object[input_width]" />
						<?php _e("(ex: 100px or 100%)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="textarea_width">
						<?php _e("Textarea Field Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="200px" class="ccf-width75" name="object[textarea_width]" />
						<?php _e("(ex: 100px or 100%)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="textarea_height">
						<?php _e("Textarea Field Height:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="90px" class="ccf-width75" name="object[textarea_height]" />
						<?php _e("(ex: 100px or 100%)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="field_fontsize">
						<?php _e("Field Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="1.3em" class="ccf-width75" name="object[field_fontsize]" />
						<?php _e("(ex: 10px, 10pt, 1em", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="field_fontcolor">
						<?php _e("Field Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="333333" class="ccf-width75 colorfield" name="object[field_fontcolor]" />
						<?php _e("(ex: 333333)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="field_borderstyle">
						<?php _e("Field Border Style:", 'custom-contact-forms'); ?>
						</label>
						<select class="ccf-width75" name="object[field_borderstyle]">
						  <?php echo str_replace('<option>solid</option>', '<option selected="selected">solid</option>', $border_style_options); ?>
						</select>
					  </li>
					  <li>
						<label for="form_margin">
						<?php _e("Form Margin:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="7px" class="ccf-width75" name="object[form_margin]" />
						<?php _e("(ex: 5px or 1em)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="label_margin">
						<?php _e("Label Margin:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="5px" class="ccf-width75" name="object[label_margin]" />
						<?php _e("(ex: 5px or 1em)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="textarea_backgroundcolor">
						<?php _e("Textarea Background Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="f5f5f5" class="ccf-width75 colorfield" name="object[textarea_backgroundcolor]" />
						<?php _e("(ex: FF0000)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="success_popover_fontcolor">
						<?php _e("Success Popover Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="333333" class="ccf-width75 colorfield" name="object[success_popover_fontcolor]" />
						<?php _e("(ex: 333333)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="success_popover_title_fontsize">
						<?php _e("Success Popover Title Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="15px" class="ccf-width75" name="object[success_popover_title_fontsize]" />
						<?php _e("(ex: 12px, 1em, 100%)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="form_backgroundcolor">
						<?php _e("Form Background Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="ffffff" class="ccf-width75 colorfield" name="object[form_backgroundcolor]" />
						<?php _e("(ex: 12px, 1em, 100%)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="tooltip_backgroundcolor">
						<?php _e("Tooltip Background Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="000000" class="ccf-width75 colorfield" name="object[tooltip_backgroundcolor]" />
						<?php _e("(ex: 000000 or black)", 'custom-contact-forms'); ?>
					  </li>
                      <li>
						<label for="form_borderwidth">
						<?php _e("Form Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="100%" class="ccf-width75" name="object[form_width]" />
						<?php _e("(ex: 100px or 50%)", 'custom-contact-forms'); ?>
					  </li>
                      <li>
						<label for="input_width">
						<?php _e("Field Border Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="999999" class="ccf-width75 colorfield" name="object[field_bordercolor]" />
						<?php _e("(ex: 100px or 100%)", 'custom-contact-forms'); ?>
					  </li>
					</ul>
					<ul class="style_right">
					  
					  <li>
						<label for="form_borderstyle">
						<?php _e("Form Border Style:", 'custom-contact-forms'); ?>
						</label>
						<select class="ccf-width75" name="object[form_borderstyle]">
						  <?php echo $border_style_options; ?>
						</select>
					  </li>
					  <li>
						<label for="form_bordercolor">
						<?php _e("Form Border Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="ffffff" class="ccf-width75 colorfield" name="object[form_bordercolor]" />
						<?php _e("(ex: 000000)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="form_borderwidth">
						<?php _e("Form Border Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="0px" class="ccf-width75" name="object[form_borderwidth]" />
						<?php _e("(ex: 1px)", 'custom-contact-forms'); ?>
					  </li>
					
					  <li>
						<label for="form_borderwidth">
						<?php _e("Form Font Family:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="120" value="Verdana, tahoma, arial" class="ccf-width75" name="object[form_fontfamily]" />
						<?php _e("(ex: Verdana, Tahoma, Arial)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="submit_width">
						<?php _e("Button Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="auto" class="ccf-width75" name="object[submit_width]" />
						<?php _e("(ex: 100px, 30%, auto)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="submit_height">
						<?php _e("Button Height:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="30px" class="ccf-width75" name="object[submit_height]" />
						<?php _e("(ex: 100px or 30%)", 'custom-contact-forms'); ?>
					  </li>
                      <li>
						<label for="submit_background">
						<?php _e("Button Background:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="200" value="http://" class="ccf-width175" name="object[submit_background]" />
						<?php _e("(any URL)", 'custom-contact-forms'); ?>
					  </li>
                      <li>
						<label for="submit_background_repeat">
						<?php _e("Button Background Repeat:", 'custom-contact-forms'); ?>
						</label>
						<select name="objects[<?php echo esc_attr($style->id); ?>][values][submit_background_repeat]">
							<option value="no-repeat">No Repeat</option>
							<option value="repeat-x">Repeat X</option>
							<option value="repeat-y">Repeat Y</option>
							<option value="repeat">Repeat Both</option>
						</select>
					  </li>
					  <li>
						<label for="submit_fontsize">
						<?php _e("Button Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="1.1em" class="ccf-width75" name="object[submit_fontsize]" />
						<?php _e("(ex: 10px, 10pt, 1em)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="submit_fontcolor">
						<?php _e("Button Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="333333" class="ccf-width75 colorfield" name="object[submit_fontcolor]" />
						<?php _e("(ex: FF0000)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="field_backgroundcolor">
						<?php _e("Field Background Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="f5f5f5" class="ccf-width75 colorfield" name="object[field_backgroundcolor]" />
						<?php _e("(ex: FF0000)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="form_padding">
						<?php _e("Form Padding:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="8px" class="ccf-width75" name="object[form_padding]" />
						<?php _e("(ex: 5px or 1em)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="title_margin">
						<?php _e("Title Margin:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="5px" class="ccf-width75" name="object[title_margin]" />
						<?php _e("(ex: 5px or 1em)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="title_margin">
						<?php _e("Dropdown Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="auto" class="ccf-width75" name="object[dropdown_width]" />
						<?php _e("(ex: 30px, 20%, or auto)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="success_popover_bordercolor">
						<?php _e("Success Popover Border Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="efefef" class="ccf-width75 colorfield" name="object[success_popover_bordercolor]" />
						<?php _e("(ex: FF0000)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="success_popover_fontsize">
						<?php _e("Success Popover Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="12px" class="ccf-width75" name="object[success_popover_fontsize]" />
						<?php _e("(ex: 12px, 1em, 100%)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="success_popover_height">
						<?php _e("Success Popover Height:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="200px" class="ccf-width75" name="object[success_popover_height]" />
						<?php _e("(ex: 200px, 6em, 50%)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="field_borderround">
						<?php _e("Field Border Roundness:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="6px" class="ccf-width75" name="object[field_borderround]" />
						<?php _e("(ex: 6px, or 0px)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="tooltip_fontsize">
						<?php _e("Tooltip", 'custom-contact-forms'); ?>
						<a href="javascript:void(0)" class="toollink" title="<?php _e("A tooltip is the little box that fades in displaying 'Field Instructions' when a user selects a particular field.", 'custom-contact-forms'); ?>">(?)</a>
						<?php _e("Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="12px" class="ccf-width75" name="object[tooltip_fontsize]" />
						<?php _e("(ex: 12px, 1em, 100%)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="tooltip_fontcolor">
						<?php _e("Tooltip Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="ffffff" class="ccf-width75 colorfield" name="object[tooltip_fontcolor]" />
						<?php _e("(ex: ffffff or white)", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<input name="object_type" type="hidden" value="style" />
						<input type="submit" value="<?php _e("Create Style", 'custom-contact-forms'); ?>" name="object_create" class="create-button" />
					  </li>
					</ul>
				  </form>
                  <div class="ccf-clear"></div>
				</div>
			  </div>
			  <a name="manage-styles"></a>
			  <h3 class="manage-h3">
				<?php _e("Manage Form Styles", 'custom-contact-forms'); ?>
			  </h3>
			  <form class="ccf-edit-ajax" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
              <input type="hidden" name="selected_tab" value="styles" />
			  <table class="widefat post" id="manage-styles" cellspacing="0">
				<thead>
				  <tr>
					<th scope="col" class="manage-column"><input type="checkbox" class="checkall" /></th>
					<th scope="col" class="manage-column"></th>
					<th scope="col" class="manage-column"></th>
					<th scope="col" class="manage-column"></th>
					<th scope="col" class="manage-column"></th>
					<th scope="col" class="manage-column"></th>
				  </tr>
				</thead>
				<tbody>
				  <?php
			$styles = parent::selectAllStyles();
			$i = 0;
			foreach ($styles as $style) {
			?>
				  <tr class="row-style-<?php echo esc_attr($style->id); ?> <?php if ($i % 2 == 0) echo 'ccf-evenrow'; ?>">
					 <td> <label><input type="checkbox" class="object-check" value="1" name="objects[<?php echo esc_attr($style->id); ?>][object_do]" /> 
						* <?php _e("Slug:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="30" value="<?php echo esc_attr($style->style_slug); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][style_slug]" />
						<br />
						<label>
						<?php _e("Font Family:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="120" value="<?php echo esc_attr($style->form_fontfamily); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][form_fontfamily]" />
						<br />
						<label>
						<?php _e("Textarea Background", 'custom-contact-forms'); ?>
						<br />
						<?php _e("Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->textarea_backgroundcolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][textarea_backgroundcolor]" />
						<br />
						<label>
						<?php _e("Success Popover", 'custom-contact-forms'); ?>
						<br />
						<?php _e("Border Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->success_popover_bordercolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][success_popover_bordercolor]" />
						<br />
						<label>
						<?php _e("Tooltip", 'custom-contact-forms'); ?>
						<a href="javascript:void(0)" class="toollink" title="<?php _e("A tooltip is the little box that fades in displaying 'Field Instructions' when a user selects a particular field.", 'custom-contact-forms'); ?>">(?)</a>
						<?php _e("Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->tooltip_fontcolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][tooltip_fontcolor]" />
					  	<br />
                        <label>
						<?php _e("Button Background", 'custom-contact-forms'); ?><br />
                        <?php _e("Repeat:", 'custom-contact-forms'); ?>
						</label>
						<select name="objects[<?php echo esc_attr($style->id); ?>][values][submit_background_repeat]">
							<option <?php selected('no-repeat', $style->submit_background_repeat); ?> value="no-repeat">No Repeat</option>
							<option <?php selected('repeat-x', $style->submit_background_repeat); ?> value="repeat-x">Repeat X</option>
							<option <?php selected('repeat-y', $style->submit_background_repeat); ?> value="repeat-y">Repeat Y</option>
							<option <?php selected('repeat', $style->submit_background_repeat); ?> value="repeat">Repeat Both</option>
						</select>
					  	<br />
                        <input type="button" class="single-save" value="<?php _e('Save', 'custom-contact-forms'); ?>" /> <div class="loading-img-container"><img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="ccf-hide loading-img-inner-style-<?php echo esc_attr($style->id); ?>" /></div><br />
                      	<input type="button" class="single-delete" value="<?php _e('Delete', 'custom-contact-forms'); ?>" />
					  	<input class="object-type" type="hidden" name="objects[<?php echo esc_attr($style->id); ?>][object_type]" value="style" />
						<input class="object-id" name="objects[<?php echo esc_attr($style->id); ?>][object_id]" type="hidden" value="<?php echo esc_attr($style->id); ?>" />
                      </td>
					  <td><label>
						<?php _e("Form Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->form_width); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][form_width]" />
						<br />
						<label>
						<?php _e("Text Field Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->input_width); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][input_width]" />
						<br />
						<label>
						<?php _e("Textarea Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->textarea_width); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][textarea_width]" />
						<br />
						<label>
						<?php _e("Textarea Height:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->textarea_height); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][textarea_height]" />
						<br />
						<label>
						<?php _e("Dropdown Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->dropdown_width); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][dropdown_width]" />
						<br />
						<label>
						<?php _e("Label Margin:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->label_margin); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][label_margin]" />
						<br />
						<label>
						<?php _e("Success Popover", 'custom-contact-forms'); ?>
						<br />
						<?php _e("Height:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->success_popover_height); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][success_popover_height]" />
						<br />

                        <label>
						<?php _e("Button Background:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="200" value="<?php echo esc_attr($style->submit_background); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][submit_background]" />
						<br />
					  </td>
					  <td><label>
						<?php _e("Label Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->label_width); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][label_width]" />
						<br />
						<label>
						<?php _e("Button Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->submit_width); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][submit_width]" />
						<br />
						<label>
						<?php _e("Button Height:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->submit_height); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][submit_height]" />
						<br />
						<label>
						<?php _e("Field Background Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->field_backgroundcolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][field_backgroundcolor]" />
						<br />
						<label>
						<?php _e("Title Margin:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->title_margin); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][title_margin]" />
						<br />
						<label>
						<?php _e("Success Popover", 'custom-contact-forms'); ?>
						<br />
						<?php _e("Title Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->success_popover_title_fontsize); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][success_popover_title_fontsize]" />
						<label>
						<?php _e("Form Background Color:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" class="colorfield" maxlength="20" value="<?php echo esc_attr($style->form_backgroundcolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][form_backgroundcolor]" />
					  </td>
					  <td><label>
						<?php _e("Title Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->title_fontsize); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][title_fontsize]" />
						<br />
						<label>
						<?php _e("Label Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->label_fontsize); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][label_fontsize]" />
						<br />
						<label>
						<?php _e("Field Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->field_fontsize); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][field_fontsize]" />
						<br />
						<label>
						<?php _e("Button Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->submit_fontsize); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][submit_fontsize]" />
						<br />
						<label>
						<?php _e("Form Padding:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->form_padding); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][form_padding]" />
						<br />
						<label>
						<?php _e("Success Popover", 'custom-contact-forms'); ?>
						<br />
						<?php _e("Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->success_popover_fontsize); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][success_popover_fontsize]" />
						<br />
						<label>
						<?php _e("Tooltip", 'custom-contact-forms'); ?>
						<a href="javascript:void(0)" class="toollink" title="<?php _e("A tooltip is the little box that fades in displaying 'Field Instructions' when a user selects a particular field.", 'custom-contact-forms'); ?>">(?)</a>
						<?php _e("Background Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->tooltip_backgroundcolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][tooltip_backgroundcolor]" />
					  </td>
					  <td><label>
						<?php _e("Title Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->title_fontcolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][title_fontcolor]" />
						<br />
						<label>
						<?php _e("Label Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->label_fontcolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][label_fontcolor]" />
						<br />
						<label>
						<?php _e("Field Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->field_fontcolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][field_fontcolor]" />
						<br />
						<label>
						<?php _e("Button Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->submit_fontcolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][submit_fontcolor]" />
						<br />
						<label>
						<?php _e("Form Margin:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->form_margin); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][form_margin]" />
						<br />
						<label>
						<?php _e("Success Popover", 'custom-contact-forms'); ?>
						<br />
						<?php _e("Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->success_popover_fontcolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][success_popover_fontcolor]" />
						<br />
						<label>
						<?php _e("Tooltip Font Size:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->tooltip_fontsize); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][tooltip_fontsize]" />
					  </td>
					  <td><label>
						<?php _e("Form Border Style:", 'custom-contact-forms'); ?>
						</label>
						<select name="objects[<?php echo esc_attr($style->id); ?>][values][form_borderstyle]">
						  <?php echo str_replace('<option>'.esc_attr($style->form_borderstyle).'</option>', '<option selected="selected">'.esc_attr($style->form_borderstyle).'</option>', $border_style_options); ?>
						</select>
						<br />
						<label>
						<?php _e("Form Border Width:", 'custom-contact-forms'); ?>
						</label>
						<input type="text" maxlength="20" value="<?php echo esc_attr($style->form_borderwidth); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][form_borderwidth]" />
						<br />
						<label>
						<?php _e("Form Border Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->form_bordercolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][form_bordercolor]" />
						<br />
						<label>
						<?php _e("Field Border Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->field_bordercolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][field_bordercolor]" />
						<br />
						<label>
						<?php _e("Field Border Style:", 'custom-contact-forms'); ?>
						</label>
						<select name="objects[<?php echo esc_attr($style->id); ?>][values][field_borderstyle]">
						  <?php echo str_replace('<option>'.esc_attr($style->field_borderstyle).'</option>', '<option selected="selected">'.esc_attr($style->field_borderstyle).'</option>', $border_style_options); ?>
						</select>
						<br />
						<label>
						<?php _e("Success Popover", 'custom-contact-forms'); ?>
						<br />
						<?php _e("Title Font Color:", 'custom-contact-forms'); ?>
						</label>
						<input class="colorfield" type="text" maxlength="20" value="<?php echo esc_attr($style->success_popover_title_fontcolor); ?>" name="objects[<?php echo esc_attr($style->id); ?>][values][success_popover_title_fontcolor]" />
						<br />
						<label>
						<?php _e("Field Border Roundness:", 'custom-contact-forms'); ?>
						</label>
						<input name="objects[<?php echo esc_attr($style->id); ?>][values][field_borderround]" value="<?php echo esc_attr($style->field_borderround); ?>" type="text" maxlength="20" />
						
					  </td>
				  </tr>
				  <?php
			$i++;
			}
			?>
				</tbody>
				<tfoot>
				  <tr>
					<th scope="col" class="manage-column"><input type="checkbox" class="checkall" /></th>
					<th scope="col" class="manage-column"></th>
					<th scope="col" class="manage-column"></th>
					<th scope="col" class="manage-column"></th>
					<th scope="col" class="manage-column"></th>
					<th scope="col" class="manage-column"></th>
				  </tr>
				</tfoot>
			  </table>
			  <select class="bulk-dropdown" name="object_bulk_action">
			  <option value="0"><?php _e('Bulk Actions', 'custom-contact-forms'); ?></option>
				<option value="edit"><?php _e('Save', 'custom-contact-forms'); ?></option>
				<option value="delete"><?php _e('Delete', 'custom-contact-forms'); ?></option></select> 
			  <input type="submit" name="object_bulk_apply" class="bulk-apply" value="<?php _e('Apply', 'custom-contact-forms'); ?>" /> <img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="loading-img" />
			  </form>
              </div>
              <div id="support">
			  <div id="contact-author" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("Report a Bug/Suggest a Feature", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
				  <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                  <input type="hidden" name="selected_tab" value="support" />
					<ul>
					  <li>
						<label for="name">
						<?php _e("Your Name:", 'custom-contact-forms'); ?>
						</label>
						<input id="name" type="text" name="name" maxlength="100" />
					  </li>
					  <li>
						<label for="email">
						<?php _e("Your Email:", 'custom-contact-forms'); ?>
						</label>
						<input id="email" type="text" value="<?php echo esc_attr(get_option('admin_email')); ?>" name="email" maxlength="100" />
					  </li>
                    </ul>
                    <ul>
                      <li>
						<label for="host">
						<?php _e("Who Hosts Your Website?", 'custom-contact-forms'); ?>
						</label>
						<input id="host" type="text" name="host" maxlength="150" />
					  </li>
					  <li>
						<label for="type">*
						<?php _e("Purpose of this message:", 'custom-contact-forms'); ?>
						</label>
						<select id="type" name="type">
						  <option>
						  <?php _e("Bug Report", 'custom-contact-forms'); ?>
						  </option>
						  <option>
						  <?php _e("Suggest a Feature", 'custom-contact-forms'); ?>
						  </option>
						  <option>
						  <?php _e("Plugin Question", 'custom-contact-forms'); ?>
						  </option>
						</select>
					  </li>
                    </ul>
                    <ul>
					  <li>
						<label for="ccf-version">
						<?php _e("Version of Custom Contact Forms?", 'custom-contact-forms'); ?>
						</label>
						<input id="ccf-version" type="text" name="ccf-version" maxlength="50" />
					  </li>
					  <li>
						<label for="wp-version">
						<?php _e("Version of WordPress?", 'custom-contact-forms'); ?>
						</label>
						<input id="wp-version" type="text" name="wp-version" maxlength="50" />
					  </li>
					</ul>
                    <ul class="message">
                      <li>
						<label for="message">*
						<?php _e("Your Message:", 'custom-contact-forms'); ?>
						</label>
						<textarea id="message-textarea" name="message"></textarea>
					  </li>
                    </ul>
					<p>
					  <input type="submit" name="contact_author" value="<?php _e("Send Message", 'custom-contact-forms'); ?>" />
					</p>
				  </form>
				</div>
			  </div>
              </div>
              
              <div id="advanced">
			  <div id="custom-html" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("Custom HTML Forms", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
				  <p>
					<?php _e("If you know HTML and simply want to use this plugin to process form requests, this feature is for you. 
					The following HTML is a the framework to which you must adhere. In order for your form to work you MUST do the following: a) Keep the form action/method the same (yes the action is supposed to be empty), b) Include all the hidden fields shown below, c) provide a 
					hidden field with a success message or thank you page (both hidden fields are included below, you must choose one or the other and fill in the value part of the input field appropriately.", 'custom-contact-forms'); ?>
				  </p>
				  <textarea id="custom_html_textarea">
&lt;form method=&quot;post&quot; action=&quot;&quot;&gt;
&lt;input type=&quot;hidden&quot; name=&quot;ccf_customhtml&quot; value=&quot;1&quot; /&gt;
&lt;input type=&quot;hidden&quot; name=&quot;success_message&quot; value=&quot;<?php _e("Thank you for filling out our form!", 'custom-contact-forms'); ?>&quot; /&gt;
&lt;input type=&quot;hidden&quot; name=&quot;thank_you_page&quot; value=&quot;http://www.google.com&quot; /&gt;
&lt;input type=&quot;hidden&quot; name=&quot;destination_email&quot; value=&quot;<?php echo esc_attr($admin_options['default_to_email']); ?>&quot; /&gt;
&lt;input type=&quot;hidden&quot; name=&quot;required_fields&quot; value=&quot;field_name1, field_name2&quot; /&gt;

&lt;!-- <?php _e("Paste all this code into a page or post and edit it to your liking. It is recommended you only use this feature if you are experienced with HTML. The destination_email field specifies where emails will be sent on successful submission; you can have the form send to multiple addresses by separating email's with semi-colons (i.e. email@google.com; email2@google.com). The success_message field will add a popover containing the message when the form is completed successfully, the thank_you_page field will force the user to be redirected to that specific page on successful form completion. The required_fields hidden field is optional; to use it separate the field names you want required by commas. Remember to use underscores instead of spaces in field names!", 'custom-contact-forms'); ?> --&gt;

&lt;/form&gt;</textarea>
				</div>
			  </div>
              </div>
              
              <div id="news">
			  <div id="plugin-news" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("Custom Contact Forms Plugin News", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
				  <?php $this->displayPluginNewsFeed(); ?>
				</div>
			  </div>
              </div>
              <?php $this->rateMeForm(); ?>
			  <?php $this->insertUsagePopover(); ?>
              <?php $this->insertQuickStartPopover(); ?>
              </div>
			</div>
<?php
		}
		
		function printFormSubmissionsPage() {
			$this->handleAdminPostRequests();
			if (isset($admin_options['show_install_popover']) && $admin_options['show_install_popover'] == 1) {
				$admin_options['show_install_popover'] = 0;
				?>
                <script type="text/javascript" language="javascript">
					$j(document).ready(function() {
						showCCFUsagePopover();
					});
				</script>
                <?php
				update_option(parent::getAdminOptionsName(), $admin_options);
			} /*if ($_POST['form_submission_delete']) {
				if (parent::deleteUserData($_POST['uid']) != false)
					$this->action_complete = __('A form submission has be successfully deleted!', 'custom-contact-forms');
			}*/
			ccf_utils::load_module('export/custom-contact-forms-user-data.php');
			$user_data_array = parent::selectAllUserData();
			?>
			<div id="customcontactforms-admin">
			  <div class="plugin-header">
				<h2>
					<?php _e("Custom Contact Forms", 'custom-contact-forms'); ?>
				</h2>
             	<div class="links">
                	<a href="javascript:void(0)" class="quick-start-button">Quick Start Guide</a> - <a href="javascript:void(0)" class="usage-popover-button">Plugin Usage Manual</a>
              	</div>
              </div>
			  <a class="gravity" href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=125082">Are you looking for a more customizable, reliable, and secure contact form solution for WordPress? Try <span>Gravity Forms</span>.</a>
			  <a class="genesis" href="http://www.shareasale.com/r.cfm?b=241369&u=481196&m=28169&urllink=&afftrack=">Custom Contact Forms works best with any of the 20+ <span>Genesis</span> Wordpress child themes. The <span>Genesis Framework</span> empowers you to quickly and easily build incredible websites with WordPress.</a>
			
			<form class="blog-horizontal-form" method="post" action="http://www.aweber.com/scripts/addlead.pl">
            	<input type="hidden" name="meta_web_form_id" value="1578604781" />
				<input type="hidden" name="meta_split_id" value="" />
				<input type="hidden" name="listname" value="ccf-plugin" />
				<input type="hidden" name="redirect" value="http://www.taylorlovett.com/wordpress-plugins/tutorials-offers-tips/" id="redirect_5832e41084448adb07da67a35dc83c27" />
				<input type="hidden" name="meta_adtracking" value="CCF_-_Wordpress_Plugins_Horizontal" />
				<input type="hidden" name="meta_message" value="1" />
				<input type="hidden" name="meta_required" value="name,email" />
				<span>WP Blogging Tips, Downloads, SEO Tricks & Exclusive Tutorials</span>
                <input type="text" name="name" value="Your Name" onclick="value=''" />
                <input type="text" name="email" value="Your Email" onclick="value=''" />
                <input type="submit" value="Sign Up for Free" />
            </form>
			<?php if (!empty($this->action_complete)) { ?>
			<div id="message" class="updated below-h2">
				<p><?php echo $this->action_complete; ?></p>
			</div>
			<?php } ?>
			  <h3 class="hndle"><span>
				  <?php _e("Saved Form Submissions", 'custom-contact-forms'); ?>
				  </span></h3>
			  <form class="ccf-edit-ajax" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
			  <table class="widefat post" id="form-submissions-table" cellspacing="0">
				<thead>
				  <tr>
					<th scope="col" class="manage-column ccf-width25"><input type="checkbox" class="checkall" /></th>
					<th scope="col" class="manage-column ccf-width250"><?php _e("Date Submitted", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column ccf-width150"><?php _e("Form Submitted", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column ccf-width250"><?php _e("Form Page", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column ccf-width100"><?php _e("Form ID", 'custom-contact-forms'); ?></th>
                    <th scope="col" class="manage-column"></th>
				  </tr>
				</thead>
				<tbody>
                  <?php
			$i = 0;
			foreach ($user_data_array as $data_object) {
			$data = new CustomContactFormsUserData(array('form_id' => $data_object->data_formid, 'data_time' => $data_object->data_time, 'form_page' => $data_object->data_formpage, 'encoded_data' => $data_object->data_value));	
			?>
				  <tr class="row-form_submission-<?php echo esc_attr($data_object->id); ?> submission-top <?php if ($i % 2 == 0) echo 'ccf-evenrow'; ?>">
					<td><input type="checkbox" class="object-check" value="1" name="objects[<?php echo esc_attr($data_object->id); ?>][object_do]" /></td>
					<td><?php echo esc_html(date('F d, Y h:i:s A', $data->getDataTime())); ?></td>
					<td><?php
			if ($data->getFormID() > 0) {
			$data_form = parent::selectForm($data->getFormID());
			$this_form = (!empty($data_form->form_slug)) ? $data_form->form_slug : '-';
			echo esc_html($this_form);
			} else
			_e('Custom HTML Form', 'custom-contact-forms');
			?>
					</td>
					<td><?php echo esc_html($data->getFormPage()); ?> </td>
                    <td><?php echo esc_html($data->getFormID()); ?> </td>
					<td class="ccf-alignright">
						<input type="button" class="submission-content-expand-button" value="<?php _e('Expand', 'custom-contact-forms'); ?>" />
						<input type="button" class="single-delete" value="<?php _e('Delete', 'custom-contact-forms'); ?>" />
					  <input class="object-id" type="hidden" name="objects[<?php echo esc_attr($data_object->id); ?>][object_id]" value="<?php echo esc_attr($data_object->id); ?>" />
					  <input type="hidden" class="object-type" name="objects[<?php echo esc_attr($data_object->id); ?>][object_type]" value="form_submission" />
                      <div class="loading-img-container"><img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" width="16" height="16" class="ccf-hide loading-img-inner-form_submission-<?php echo esc_attr($data_object->id); ?>" /></div>
					 </td>
				  </tr>
				  <tr class="ccf-hide row-form_submission-<?php echo esc_attr($data_object->id); ?> submission-content <?php if ($i % 2 == 0) echo 'ccf-evenrow'; ?>">
					<td colspan="6"><ul>
						<?php
			$data_array = $data->getDataArray();
			foreach ($data_array as $item_key => $item_value) {
			?>
						<li>
						  <div><?php echo esc_html($item_key); ?></div>
						  <p><?php echo $data->parseUserData($item_value); ?></p>
						</li>
						<?php
			}
			?>
					  </ul></td>
				  </tr>
				  <?php
			$i++;
			}
			?>
				</tbody>
				<tfoot>
				  <tr>
					<th scope="col" class="manage-column25"><input type="checkbox" class="checkall" /></th>
					<th scope="col" class="manage-column ccf-width250"><?php _e("Date Submitted", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column ccf-width150"><?php _e("Form Submitted", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column ccf-width250"><?php _e("Form Page", 'custom-contact-forms'); ?></th>
                    <th scope="col" class="manage-column ccf-width100"><?php _e("Form ID", 'custom-contact-forms'); ?></th>
					<th scope="col" class="manage-column"></th>
				  </tr>
				</tfoot>
			  </table>
              
			  <select class="bulk-dropdown" name="object_bulk_action">
				<option value="0"><?php _e('Bulk Actions', 'custom-contact-forms'); ?></option>
				<option value="delete"><?php _e('Delete', 'custom-contact-forms'); ?></option>
			  </select> <input type="submit" class="bulk-apply" name="object_bulk_apply" value="<?php _e('Apply', 'custom-contact-forms'); ?>" /> <img src="<?php echo plugins_url(); ?>/custom-contact-forms/images/wpspin_light.gif" class="loading-img" width="16" height="16" />
			  
			  
			  
			  </form>
			  <?php $this->insertUsagePopover(); ?>
              <?php $this->insertQuickStartPopover(); ?>
			</div>
			<?php
		}
		
		function printSettingsPage() {
			$admin_options = parent::getAdminOptions();
			if ($admin_options['show_install_popover'] == 1) {
				$admin_options['show_install_popover'] = 0;
				?>
                <script type="text/javascript" language="javascript">
					$j(document).ready(function() {
						showCCFUsagePopover();
					});
				</script>
                <?php
				update_option(parent::getAdminOptionsName(), $admin_options);
			} if (isset($_POST['general_settings'])) {
				$_POST['settings'] = array_map(array('ccf_utils', 'encodeOption'), $_POST['settings']);
				$admin_options = array_merge($admin_options, $_POST['settings']);
				$admin_options['show_widget_categories'] = $_POST['settings']['show_widget_categories'];
				$admin_options['show_widget_singles'] = $_POST['settings']['show_widget_singles'];
				$admin_options['show_widget_pages'] = $_POST['settings']['show_widget_pages'];
				$admin_options['show_widget_archives'] = $_POST['settings']['show_widget_archives'];
				$admin_options['show_widget_home'] = $_POST['settings']['show_widget_home'];
				$this->action_complete = __('Your settings have been successfully saved!', 'custom-contact-forms');
				update_option(parent::getAdminOptionsName(), $admin_options);
			} elseif (isset($_POST['configure_mail'])) {
				$_POST['mail_config'] = array_map(array('ccf_utils', 'encodeOption'), $_POST['mail_config']);
				$admin_options = array_merge($admin_options, $_POST['mail_config']);
				$this->action_complete = __('Your mail settings have been successfully saved!', 'custom-contact-forms');
				update_option(parent::getAdminOptionsName(), $admin_options);
			}
			ccf_utils::load_module('export/custom-contact-forms-export.php');
			?>
			<div id="customcontactforms-admin">
			  <div class="plugin-header">
				<h2>
					<?php _e("Custom Contact Forms", 'custom-contact-forms'); ?>
				</h2>
             	<div class="links">
                	<a href="javascript:void(0)" class="quick-start-button">Quick Start Guide</a> - <a href="javascript:void(0)" class="usage-popover-button">Plugin Usage Manual</a>
              	</div>
              </div>
			  <a class="gravity" href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=125082">Are you looking for a more customizable, reliable, and secure contact form solution for WordPress? Try <span>Gravity Forms</span>.</a>
			  <a class="genesis" href="http://www.shareasale.com/r.cfm?b=241369&u=481196&m=28169&urllink=&afftrack=">Custom Contact Forms works best with any of the 20+ <span>Genesis</span> Wordpress child themes. The <span>Genesis Framework</span> empowers you to quickly and easily build incredible websites with WordPress.</a>
			
			<form class="blog-horizontal-form" method="post" action="http://www.aweber.com/scripts/addlead.pl">
            	<input type="hidden" name="meta_web_form_id" value="1578604781" />
				<input type="hidden" name="meta_split_id" value="" />
				<input type="hidden" name="listname" value="ccf-plugin" />
				<input type="hidden" name="redirect" value="http://www.taylorlovett.com/wordpress-plugins/tutorials-offers-tips/" id="redirect_5832e41084448adb07da67a35dc83c27" />
				<input type="hidden" name="meta_adtracking" value="CCF_-_Wordpress_Plugins_Horizontal" />
				<input type="hidden" name="meta_message" value="1" />
				<input type="hidden" name="meta_required" value="name,email" />
				<span>WP Blogging Tips, Downloads, SEO Tricks & Exclusive Tutorials</span>
                <input type="text" name="name" value="Your Name" onclick="value=''" />
                <input type="text" name="email" value="Your Email" onclick="value=''" />
                <input type="submit" value="Sign Up for Free" />
            </form>
			
			<?php if (!empty($this->action_complete)) { ?>
			<div id="message" class="updated below-h2">
				<p><?php echo $this->action_complete; ?></p>
			</div>
			<?php } ?>
			
			  <div id="general-settings" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("General Settings", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
				  <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
					<ul class="gleft">
					  <li>
						<label for="email_form_submissions">
						<?php _e("Email Form Submissions:", 'custom-contact-forms'); ?>
						</label>
						<select name="settings[email_form_submissions]">
						  <option value="1">
						  <?php _e("Yes", 'custom-contact-forms'); ?>
						  </option>
						  <option value="0" <?php if ($admin_options['email_form_submissions'] == 0) echo 'selected="selected"'; ?>>
						  <?php _e("No", 'custom-contact-forms'); ?>
						  </option>
						</select>
					  </li>
					  <li class="descrip">
						<?php _e("When a user fills out one of your forms, the info submitted is saved in the Saved Form Submission section of the admin panel for you to view. If this is enabled, you will also be sent an email containing the submission info.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="default_to_email">
						<?php _e("Default Email:", 'custom-contact-forms'); ?>
						</label>
						<input name="settings[default_to_email]" value="<?php echo esc_attr($admin_options['default_to_email']); ?>" type="text" maxlength="100" />
					  </li>
					  <li class="descrip">
						<?php _e("Form emails will be sent <span>to</span> this address, if no destination email is specified by the form.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="enable_jquery">
						<?php _e("Front End JQuery:", 'custom-contact-forms'); ?>
						</label>
						<select name="settings[enable_jquery]">
						  <option value="1">
						  <?php _e("Enabled", 'custom-contact-forms'); ?>
						  </option>
						  <option <?php if ($admin_options['enable_jquery'] != 1) echo 'selected="selected"'; ?> value="0">
						  <?php _e("Disabled", 'custom-contact-forms'); ?>
						  </option>
						</select>
					  </li>
					  <li class="descrip">
						<?php _e("Some plugins don't setup JQuery correctly, so when any other plugin uses JQuery (whether correctly or not), JQuery works for neither plugin. This plugin uses JQuery correctly. If another plugin isn't using JQuery correctly but is more important to you than this one: disable this option. 99% of this plugin's functionality will work without JQuery, just no field instruction tooltips.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="default_from_email">
						<?php _e("Default From Email:", 'custom-contact-forms'); ?>
						</label>
						<input name="settings[default_from_email]" value="<?php echo esc_attr($admin_options['default_from_email']); ?>" type="text" maxlength="100" />
					  </li>
					  <li class="descrip">
						<?php _e("Form emails will be sent <span>from</span> this address. It is recommended you provide a real email address that has been created through your host.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="default_from_name">
						<?php _e("Default From Name:", 'custom-contact-forms'); ?>
						</label>
						<input name="settings[default_from_name]" value="<?php echo esc_attr($admin_options['default_from_name']); ?>" type="text" maxlength="100" />
					  </li>
					  <li class="descrip">
						<?php _e("This setting allows you to specify the name under which form emails are sent. The default is 'Custom Contact Forms'.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="default_form_subject">
						<?php _e("Default Email Subject:", 'custom-contact-forms'); ?>
						</label>
						<input name="settings[default_form_subject]" value="<?php echo esc_attr($admin_options['default_form_subject']); ?>" type="text" />
					  </li>
					  <li class="descrip">
						<?php _e("Default subject to be included in all form emails.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="enable_dashboard_widget">
						<?php _e("Enable Dashboard Widget:", 'custom-contact-forms'); ?>
						</label>
						<select name="settings[enable_dashboard_widget]">
						  <option value="1">
						  <?php _e("Enabled", 'custom-contact-forms'); ?>
						  </option>
						  <option value="0" <?php if ($admin_options['enable_dashboard_widget'] == 0) echo 'selected="selected"'; ?>>
						  <?php _e("Disabled", 'custom-contact-forms'); ?>
						  </option></select>
					 </li>
					  <li class="descrip">
						<?php _e("Enabling this will display a widget on your dashboard that shows the latest form submissions.", 'custom-contact-forms'); ?>
					  </li>
                      <li>
						<label for="dashboard_access">
						<?php _e("Dashboard Widget Accessibility:", 'custom-contact-forms'); ?>
						</label>
						<select name="settings[dashboard_access]">
						  <option value="2">
						  <?php _e("Only admins can view", 'custom-contact-forms'); ?>
						  </option>
                          <option value="1" <?php if ($admin_options['dashboard_access'] == 1) echo 'selected="selected"'; ?>>
						  <?php _e("All roles except subscribers can view", 'custom-contact-forms'); ?>
						  </option>
						  <option value="0" <?php if ($admin_options['dashboard_access'] == 0) echo 'selected="selected"'; ?>>
						  <?php _e("All roles can view", 'custom-contact-forms'); ?>
						  </option></select>
					 </li>
					  <li class="descrip">
						<?php _e("If you are using the dashboard widget, this allows you to disallow certain users from viewing it.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="code_type">
						<?php _e("Use Code Type:", 'custom-contact-forms'); ?>
						</label>
						<select name="settings[code_type]">
						  <option>XHTML</option>
						  <option <?php if ($admin_options['code_type'] == 'HTML') echo 'selected="selected"'; ?>>HTML</option>
						</select>
					  </li>
					  <li class="descrip">
						<?php _e("This lets you switch the form code between HTML and XHTML.", 'custom-contact-forms'); ?>
					  </li>
					  
					  <li>
						<label for="recaptcha_public_key">
						<?php _e("reCaptcha Public Key:", 'custom-contact-forms'); ?>
						</label>
						<input name="settings[recaptcha_public_key]" value="<?php echo esc_attr( $admin_options['recaptcha_public_key'] ); ?>" type="text" />
					  </li>
					  <li class="descrip">
						<?php _e( 'This key comes from <a href="http://google.com/recaptcha">reCaptcha</a> and allows you to use the recaptcha fixed field.', 'custom-contact-forms' ); ?>
					  </li>
					  
					  <li>
						<label for="recaptcha_private_key">
						<?php _e("reCaptcha Private Key:", 'custom-contact-forms'); ?>
						</label>
						<input name="settings[recaptcha_private_key]" value="<?php echo esc_attr( $admin_options['recaptcha_private_key'] ); ?>" type="text" />
					  </li>
					  <li class="descrip">
						<?php _e( 'This key comes from <a href="http://google.com/recaptcha">reCaptcha</a> and allows you to use the recaptcha fixed field.', 'custom-contact-forms' ); ?>
					  </li>
                      
					</ul>
					<ul class="gright">
					  <li>
						<label for="form_success_message_title">
						<?php _e("Default Form Success Message Title:", 'custom-contact-forms'); ?>
						</label>
						<input name="settings[form_success_message_title]" value="<?php echo esc_attr($admin_options['form_success_message_title']); ?>" type="text"/>
					  </li>
					  <li class="descrip">
						<?php _e("If someone fills out a form for which a success message title is not provided and a custom success page is not provided, the plugin will show a popover using this field as the window title.", 'custom-contact-forms'); ?>
					  </li>
                      
					  <li>
						<label for="form_success_message">
						<?php _e("Default Form Success Message:", 'custom-contact-forms'); ?>
						</label>
						<input name="settings[form_success_message]" value="<?php echo esc_attr($admin_options['form_success_message']); ?>" type="text"/>
					  </li>
					  <li class="descrip">
						<?php _e("If someone fills out a form for which a success message is not provided and a custom success page is not provided, the plugin will show a popover containing this message.", 'custom-contact-forms'); ?>
					  </li>
                      <li>
						<label for="default_form_error_header">
						<?php _e("Default Form Error Header:", 'custom-contact-forms'); ?>
						</label>
						<input name="settings[default_form_error_header]" value="<?php echo esc_attr($admin_options['default_form_error_header']); ?>" type="text" />
					  </li>
					  <li class="descrip">
						<?php _e("When a form is filled out incorrectly, this message will be displayed followed by the individual field error messages.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="remember_field_values">
						<?php _e("Remember Field Values:", 'custom-contact-forms'); ?>
						</label>
						<select name="settings[remember_field_values]">
						  <option value="1">
						  <?php _e("Yes", 'custom-contact-forms'); ?>
						  </option>
						  <option <?php if ($admin_options['remember_field_values'] == 0) echo 'selected="selected"'; ?> value="0">
						  <?php _e("No", 'custom-contact-forms'); ?>
						  </option>
						</select>
					  </li>
					  <li class="descrip">
						<?php _e("Selecting yes will make form fields remember how they were last filled out.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="enable_widget_tooltips">
						<?php _e("Tooltips in Widget:", 'custom-contact-forms'); ?>
						</label>
						<select name="settings[enable_widget_tooltips]">
						  <option value="1">
						  <?php _e("Enabled", 'custom-contact-forms'); ?>
						  </option>
						  <option <?php if ($admin_options['enable_widget_tooltips'] == 0) echo 'selected="selected"'; ?> value="0">
						  <?php _e("Disabled", 'custom-contact-forms'); ?>
						  </option>
						</select>
					  </li>
					  <li class="descrip">
						<?php _e("Enabling this shows tooltips containing field instructions on forms in the widget.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="form_page_inclusion_only">
						<?php _e("Restrict Frontend JS and CSS to Form Pages Only:", 'custom-contact-forms'); ?>
						</label>
						<select name="settings[form_page_inclusion_only]">
						  <option value="1">
						  <?php _e("Yes", 'custom-contact-forms'); ?>
						  </option>
						  <option value="0" <?php if ($admin_options['form_page_inclusion_only'] == 0) echo 'selected="selected"'; ?>>
						  <?php _e("No", 'custom-contact-forms'); ?>
						  </option></select>
                      </li>
					  <li class="descrip">
						<?php _e("Within each form in the form manager, you can specify the page id's on which that form will be used. If you set this to 'Yes', the plugin will only include CSS and JS files on pages/posts where a CCF form is inserted. If this is set to 'No', CSS and JS files for this plugin will be included on every page of your site except in the admin area.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="default_form_bad_permissions">
						<?php _e("Default Permissions Error:", 'custom-contact-forms'); ?>
						</label>
						<input name="settings[default_form_bad_permissions]" value="<?php echo esc_attr($admin_options['default_form_bad_permissions']); ?>" type="text" />
					  </li>
					  <li class="descrip">
						<?php _e("When someone doesn't have the proper permissions to use a form, this message is displayed in place of the form. You control who can view each form with the form access manager which is located inside the form manager.", 'custom-contact-forms'); ?>
					  </li>
					  <li>
						<label for="enable_form_access_manager">
						<?php _e("Form Access Capabilities:", 'custom-contact-forms'); ?>
						</label>
						<select name="settings[enable_form_access_manager]">
						  <option value="0">
						  <?php _e("Disabled", 'custom-contact-forms'); ?>
						  </option>
						  <option value="1" <?php if ($admin_options['enable_form_access_manager'] == 1) echo 'selected="selected"'; ?>>
						  <?php _e("Enabled", 'custom-contact-forms'); ?>
						  </option>
						</select>
					  </li>
					  <li class="descrip">
						<?php _e("The form access manager within each form allows you to control who can view your form. However, that will not take effect on any of your forms unless this is enabled.", 'custom-contact-forms'); ?>
					  </li>
                      <li>
						<label for="max_file_upload_size">
						<?php _e("Maximum File Upload Size:", 'custom-contact-forms'); ?>
						</label>
						<input name="settings[max_file_upload_size]" class="ccf-width75" type="text" value="<?php echo esc_attr($admin_options['max_file_upload_size']); ?>" /> <?php _e("MB"); ?>
						  
                      </li>
					  <li class="descrip">
						<?php _e("When creating file fields, you can specify maximum upload sizes for each individual field. This setting lets you set an overall cap for security reasons. It is recommended you leave this at 10 MB.", 'custom-contact-forms'); ?>
					  </li>
					  <li class="show-widget"><b>
						<?php _e("Show Sidebar Widget:", 'custom-contact-forms'); ?>
						</b></li>
					  <li>
						<label>
						<input value="1" type="checkbox" name="settings[show_widget_home]" <?php if ($admin_options['show_widget_home'] == 1) echo 'checked="checked"'; ?> />
						<?php _e("On Homepage", 'custom-contact-forms'); ?>
						</label>
						<label>
						<input value="1" type="checkbox" name="settings[show_widget_pages]" <?php if ($admin_options['show_widget_pages'] == 1) echo 'checked="checked"'; ?> />
						<?php _e("On Pages", 'custom-contact-forms'); ?>
						</label>
						<label>
						<input value="1" type="checkbox" name="settings[show_widget_singles]" <?php if ($admin_options['show_widget_singles'] == 1) echo 'checked="checked"'; ?> />
						<?php _e("On Single Posts", 'custom-contact-forms'); ?>
						</label>
						<br />
						<label>
						<input value="1" type="checkbox" name="settings[show_widget_categories]" <?php if ($admin_options['show_widget_categories'] == 1) echo 'checked="checked"'; ?> />
						<?php _e("On Categories", 'custom-contact-forms'); ?>
						</label>
						<label>
						<input value="1" type="checkbox" name="settings[show_widget_archives]" <?php if ($admin_options['show_widget_archives'] == 1) echo 'checked="checked"'; ?> />
						<?php _e("On Archives", 'custom-contact-forms'); ?>
						</label>
					  </li>
                      
					  <li>
						<input type="submit" value="<?php _e("Update", 'custom-contact-forms'); ?>" name="general_settings" />
					  </li>
					</ul>
				  </form>
				</div>
			  </div>
			  <div id="configure-mail" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("Mail Settings", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
                	<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                	<p><?php _e("There are two ways you can send emails: using the PHP mail() function or using SMTP (secure/insecure). If you choose to use the PHP mail() function you can ignore all the other options. For some people Wordpress's default way of sending mail does not work; if for some reason your mail is being sent you should try the SMTP option.", 'custom-contact-forms'); ?></p>
                	<label for="mail_function"><?php _e("* Send My Emails Using the Following:", 'custom-contact-forms'); ?></label>
                    <select name="mail_config[mail_function]">
					  <option value="default"><?php _e("Wordpress Default", 'custom-contact-forms'); ?></option>
					  <option <?php if ($admin_options['mail_function'] == 'smtp') echo 'selected="selected"'; ?> value="smtp"><?php _e("SMTP", 'custom-contact-forms'); ?></option>
					</select> <?php _e("(If mail isn't sending, try toggling this option.)", 'custom-contact-forms'); ?>
                    <div>
                        <ul class="left">
                            <li><label for="smtp_host"><?php _e("SMTP Host:", 'custom-contact-forms'); ?></label> <input class="ccf-width125" type="text" size="10" name="mail_config[smtp_host]" value="<?php echo esc_attr($admin_options['smtp_host']); ?>" /></li>
                            <li><label for="smtp_port"><?php _e("SMTP Port:", 'custom-contact-forms'); ?></label> <input class="ccf-width125" type="text" size="10" name="mail_config[smtp_port]" value="<?php echo esc_attr($admin_options['smtp_port']); ?>" /></li>
                            <li><label for="smtp_encryption"><?php _e("Encryption:", 'custom-contact-forms'); ?></label> <select name="mail_config[smtp_encryption]">
                            <option value="none"><?php _e("None", 'custom-contact-forms'); ?></option>
                            <option <?php if ($admin_options['smtp_encryption'] == 'ssl') echo 'selected="selected"'; ?> value="ssl"><?php _e("SSL", 'custom-contact-forms'); ?></option>
                            <option <?php if ($admin_options['smtp_encryption'] == 'tls') echo 'selected="selected"'; ?> value="tls"><?php _e("TLS", 'custom-contact-forms'); ?></option>
                            </select></li>
                        </ul>
                        <ul class="right">
                            <li><label for="smtp_authentication"><?php _e("SMTP Authentication:", 'custom-contact-forms'); ?></label> <select name="mail_config[smtp_authentication]"><option value="0"><?php _e("None Needed", 'custom-contact-forms'); ?></option><option <?php if ($admin_options['smtp_authentication'] == 1) echo 'selected="selected"'; ?> value="1"><?php _e("Use SMTP Username/Password", 'custom-contact-forms'); ?></option></select></li>
                            <li><label for="smtp_username"><?php _e("SMTP Username:", 'custom-contact-forms'); ?></label> <input class="ccf-width125" type="text" size="10" name="mail_config[smtp_username]" value="<?php echo esc_attr($admin_options['smtp_username']); ?>" /></li>
                            <li><label for="smtp_password"><?php _e("SMTP Password:", 'custom-contact-forms'); ?></label> <input class="ccf-width125" type="text" size="10" name="mail_config[smtp_password]" value="<?php echo esc_attr($admin_options['smtp_password']); ?>" /></li>
                        </ul>
                    </div>
                    <input type="submit" name="configure_mail" value="<?php _e("Save Mail Sending Options", 'custom-contact-forms'); ?>" />
                    </form>
                </div>
              </div>
			  <div id="export" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("Export", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
				  <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                  
                  <div class="left">
                  	<p>
					  <?php _e("Preforming this export will create a file of the form 
						ccf-export-xxxx.sql on your web server. The file created contains SQL that 
						will recreate all the plugin data on any Wordpress installation. After Custom Contact Forms creates the export file, you will be prompted to download it. You can use this file as a backup in case your Wordpress database gets ruined. Unfortunately, this export will not contain an files uploaded via your forms.", 'custom-contact-forms'); ?>
					</p>
					<input type="submit" name="ccf_export" value="<?php _e("Export All CCF Plugin Content", 'custom-contact-forms'); ?>" />	
                  </div>
                  <div class="right">
                  	<p><?php _e("You can export your form submissions in to a convienent format. Clicking the button below will prompt you to download a .CSV file that contains all your saved form submissions. Since this export contains multiple forms that are assumed to have different fields, this export will only contain the value of each field and not the name of that field.", 'custom-contact-forms'); ?></p>
                    	<input type="submit" name="ccf_export_all_csv" value="<?php _e('Export All Saved Form Submissions to CSV', 'custom-contact-forms'); ?>" />
				  </div>
                  <div class="divider ccf-clear"></div>
                  <p><?php _e("You can also export only submissions from certain forms in to CSV format. This CSV export will probably more useful to you because it will contain the name of fields as well as the values. This export works best on forms that have fields that have remained completely constant throughout submission.", 'custom-contact-forms'); ?></p>
                    	<label for="csv_form_id">Form ID:</label> <input id="csv_form_id" type="text" size="5" name="csv_form_id" /> <input type="submit" name="ccf_export_form_csv" value="<?php _e("Export This Form's Submissions to CSV", 'custom-contact-forms'); ?>" />
                  
                  </form>
				</div>
			  </div>
			  <div id="import" class="postbox">
				<h3 class="hndle"><span>
				  <?php _e("Import", 'custom-contact-forms'); ?>
				  </span></h3>
				<div class="inside">
				  <form method="post" enctype="multipart/form-data" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
					
					<p>
					  <?php _e("Browse to a CCF .sql export file to import Custom Contact Form data from another Wordpress installation to this one. Pressing the 'Clear and Import' button deletes all current data and then imports the selected file; this will not work for merging to data!. Clearing all CCF data before importing prevents any conflicts from occuring. Before you attempt an import, you should always download a backup, by clicking the 'Export All' button.", 'custom-contact-forms'); ?>
					</p>
					<p class="choose_import">
					  <?php _e("Choose What You Want to Use from the Import File:", 'custom-contact-forms'); ?>
					</p>
					<ul>
					  <li>
						<input type="checkbox" checked="checked" name="ccf_import_overwrite_settings" value="1" />
						<label for="ccf_import_overwrite_settings">
						<?php _e("Use General Settings", 'custom-contact-forms'); ?>
						</label>
					  </li>
					  <li>
						<input type="checkbox" checked="checked" name="ccf_import_forms" value="1" />
						<label for="ccf_import_forms">
						<?php _e("Forms", 'custom-contact-forms'); ?>
						</label>
					  </li>
					  <li>
						<input type="checkbox" checked="checked" name="ccf_import_saved_submissions" value="1" />
						<label for="ccf_import_saved_submissions">
						<?php _e("Form Submissions", 'custom-contact-forms'); ?>
						</label>
					  </li>
					  <li>
						<input type="checkbox" checked="checked" name="ccf_import_fields" value="1" />
						<label for="ccf_import_fields">
						<?php _e("Fields", 'custom-contact-forms'); ?>
						</label>
					  </li>
					  <li>
						<input type="checkbox" checked="checked" name="ccf_import_forms" value="1" />
						<label for="ccf_import_forms">
						<?php _e("Forms", 'custom-contact-forms'); ?>
						</label>
					  </li>
					  <li>
						<input type="checkbox" checked="checked" name="ccf_import_field_options" value="1" />
						<label for="ccf_import_field_options">
						<?php _e("Field Options", 'custom-contact-forms'); ?>
						</label>
					  </li>
					  <li>
						<input type="checkbox" checked="checked" name="ccf_import_styles" value="1" />
						<label for="ccf_import_styles">
						<?php _e("Styles", 'custom-contact-forms'); ?>
						</label>
					  </li>
					</ul>
					<p class="choose_import">
					  <label for="import_file">
					  <?php _e("Choose an Import File:", 'custom-contact-forms'); ?>
					  </label>
					  <input type="file" name="import_file" />
					</p>
					<input name="ccf_clear_import" type="submit" value="<?php _e("Clear and Import", 'custom-contact-forms'); ?>" />
					<input type="checkbox" name="ccf_import_confirm" value="1" />
					<?php _e('Yes, I want to do this and have created a backup.', 'custom-contact-forms'); ?>
				  </form>
				</div>
			  </div>
			  <?php $this->insertUsagePopover(); ?>
              <?php $this->insertQuickStartPopover(); ?>
			</div>
			<?php
		}
	}
}