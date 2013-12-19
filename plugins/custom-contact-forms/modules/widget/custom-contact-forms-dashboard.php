<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/
if (!class_exists('CustomContactFormsDashboard')) {
	class CustomContactFormsDashboard extends CustomContactFormsAdmin {
		function install() {
			if (is_user_logged_in() && $this->userCanViewWidget()) {
				wp_add_dashboard_widget('custom-contact-forms-dashboard', __('Custom Contact Forms - Saved Form Submissions', 'custom-contact-forms'), array(&$this, 'display'));	
			}
		}
		
		function isDashboardPage() {
			return (is_admin() && preg_match('/((index\.php)|(wp-admin\/?))$/', $_SERVER['REQUEST_URI']));
		}
		
		function userCanViewWidget() {
			global $current_user;
			if (!isset($current_user) || empty($current_user)) return false;
			$perms = parent::getAdminOptions();
			$widget_perms = $perms['dashboard_access'];
			$user_roles = $current_user->roles;
			$user_role = @array_shift($user_roles);
			$user_role = @ucwords($user_role);
			if ($widget_perms == 2) {
				if ($user_role != "Administrator") return false;
			} else if ($widget_perms == 1) {
				if ($user_role == "Subscriber" || !in_array($user_role, parent::getRolesArray())) return false;
			} else {
				/* all roles are allowed so just return true */
			}
			return true;
		}
		
		function insertDashboardStyles() {
			if (!$this->userCanViewWidget()) return;
			wp_register_style('ccf-dashboard', plugins_url() . '/custom-contact-forms/css/custom-contact-forms-dashboard.css');
            wp_register_style('ccf-jquery-ui', plugins_url() . '/custom-contact-forms/css/jquery-ui.css');
            wp_enqueue_style('ccf-jquery-ui');
			wp_enqueue_style('ccf-dashboard');
		}
		
		function insertDashboardScripts() {
			if (!$this->userCanViewWidget()) return;
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-widget', plugins_url() . '/custom-contact-forms/js/jquery.ui.widget.js');
			wp_enqueue_script('jquery-ui-dialog');
			wp_register_script('ccf-dashboard', plugins_url() . '/custom-contact-forms/js/custom-contact-forms-dashboard.js', array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-dialog'));
            wp_enqueue_script('ccf-dashboard');
		}
		
		function display() {
			ccf_utils::load_module('export/custom-contact-forms-user-data.php');
			$user_data_array = parent::selectAllUserData();
			?>
			<table id="ccf-dashboard" cellpadding="0" cellspacing="0">
			  <thead>
				<tr>
				  <th>Date</th>
				  <th>Form</th>
				  <th>Form Location</th>
				  <th></th>
				</tr>
			  </thead>
			  <tbody>
			<?php
			if (empty($user_data_array)) {
				?>
               <tr>
               	 <td colspan="4"><?php _e('No submissions to display.', 'custom-contact-forms'); ?></td> 
               </tr>
                <?php
			}
			$i = 0;
			foreach ($user_data_array as $data_object) {
				if ($i > 3) break;
				$data = new CustomContactFormsUserData(array('form_id' => $data_object->data_formid, 'data_time' => $data_object->data_time, 'form_page' => $data_object->data_formpage, 'encoded_data' => $data_object->data_value));	
				?>
				<tr class="<?php if ($i % 2 == 1) echo 'even'; ?>">
					<td class="date"><?php echo date('m/d/y', $data->getDataTime()); ?></td>
					<td class="slug">
					<?php
					if ($data->getFormID() > 0) {
						$data_form = parent::selectForm($data->getFormID());
						$this_form = (!empty($data_form->form_slug)) ? $data_form->form_slug : '-';
					} else
						$this_form = __('Custom HTML Form', 'custom-contact-forms');
					if (strlen($this_form) > 13) echo substr($this_form, 0, 13) . '...';
					else echo $this_form;
					?>
					</td>
					<td class="form-page">
					<?php
					if (strlen($data->getFormPage()) > 30) echo substr($data->getFormPage(), 0, 30) . '...';
					else echo $data->getFormPage();
					?>
					</td>
					<td>
						<input class="ccf-view-submission" type="button" value="<?php _e('View', 'custom-contact-forms'); ?>" />
						<div class="ccf-view-submission-popover" title="<?php _e('CCF Saved Form Submission', 'custom-contact-forms'); ?>">
							<div class="top">
								<div class="left">
								<p><?php _e('Form Submitted:', 'custom-contact-forms'); ?> <span><?php echo ($this_form == '-') ? __('Not Found', 'custom-contact-forms') : $this_form; ?></span></p>
								<p><?php _e('Form Location:', 'custom-contact-forms'); ?> <span>
								<?php
									if (strlen($data->getFormPage()) > 70) echo substr($data->getFormPage(), 0, 70) . '...';
									else echo $data->getFormPage();
								?></span></p></div>
								<div class="right"><span><?php echo date('F j, Y, g:i a', $data->getDataTime()); ?></span></div>
							</div>
							<div class="separate"></div>
							<ul>
								<?php
								$data_array = $data->getDataArray();
								foreach ($data_array as $item_key => $item_value) {
								?>
								<li>
								  <div><?php echo $item_key; ?></div>
								  <p><?php echo $data->parseUserData($item_value); ?></p>
								</li>
								<?php
								}
								?>
							</ul>
							<div class="separate"></div>
                            <a class="button" href="admin.php?page=ccf-saved-form-submissions"><?php _e('View All Submissions', 'custom-contact-forms'); ?></a>
						</div>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			  </tbody>
			</table>
			<a href="admin.php?page=ccf-saved-form-submissions"><?php _e('View All Submissions', 'custom-contact-forms'); ?></a>
			<?php
		}
	}
}
?>