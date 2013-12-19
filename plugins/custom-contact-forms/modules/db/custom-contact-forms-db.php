<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/
if (!class_exists('CustomContactFormsDB')) {
	class CustomContactFormsDB {
		
		function formatStyle($style) {
			return str_replace('#', '', str_replace(';', '', $style));
		}
		
		function insertForm($form) {
			global $wpdb;
			if (empty($form) or empty($form['form_slug']) or $this->formSlugExists($this->formatSlug($form['form_slug']))) return false;
			$form['form_slug'] = $this->formatSlug($form['form_slug']);
			$form['form_access'] = serialize($form['form_access']);
			$skip_encode = array('form_access');
			foreach ($form as $key => $value)
				if (!in_array($key, $skip_encode))
					$form[$key] = ccf_utils::encodeOption($value);
			$wpdb->insert(CCF_FORMS_TABLE, $form);
			return $wpdb->insert_id;
		}
		
		function insertField($field, $fixed = false, $skip_encode = array('field_allowed_file_extensions')) {
			global $wpdb;
			if (empty($field) or empty($field['field_slug']) or (array_key_exists($this->formatSlug($field['field_slug']), $GLOBALS['ccf_fixed_fields']) && !$fixed) or $this->fieldSlugExists($this->formatSlug($field['field_slug'])))
				return false;
			$field['field_slug'] = $this->formatSlug($field['field_slug']);
			if (isset($field['field_allowed_file_extensions']))
				$field['field_allowed_file_extensions'] = $this->formatFileExtensions($field['field_allowed_file_extensions']);
			foreach ($field as $key => $value)
				if (!is_array($value) && !in_array($key, $skip_encode))
					$field[$key] = ccf_utils::encodeOption($value);
			$wpdb->insert(CCF_FIELDS_TABLE, $field);
			return $wpdb->insert_id;
		}
		
		function insertFieldOption($option) {
			global $wpdb;
			if (empty($option) or empty($option['option_slug']) or empty($option['option_label']) or $this->fieldOptionsSlugExists($this->formatSlug($option['option_slug']))) return false;
			$option['option_slug'] = $this->formatSlug($option['option_slug']);
			foreach ($option as $key => $value)
				$option[$key] = ccf_utils::encodeOption($value);
			$wpdb->insert(CCF_FIELD_OPTIONS_TABLE, $option);
			return $wpdb->insert_id;
		}
		
		function insertStyle($style) {
			global $wpdb;
			if (empty($style) or empty($style['style_slug']) or $this->styleSlugExists($this->formatSlug($style['style_slug']))) return false;
			$style['style_slug'] = $this->formatSlug($style['style_slug']);
			foreach ($style as $key => $value)
					$style[$key] = ccf_utils::encodeOption($value);
			$wpdb->insert(CCF_STYLES_TABLE, $style);
			return $wpdb->insert_id;
		}
		
		function serializeAllFormFields() {
			$forms = $this->selectAllForms();
			foreach ($forms as $form) {
				$fields = $form->form_fields;
				if (!is_serialized($fields)) {
					$this->updateForm(array('form_fields' => $this->old_getAttachedFieldsArray($fields)), $form->id);
				}
			}
		}
		
		function serializeAllFieldOptions() {
			$fields = $this->selectAllFields();
			foreach ($fields as $field) {
				$options = $field->field_options;
				if (!is_serialized($options)) {
					$this->updateField(array('field_options' => $this->old_getAttachedFieldsArray($options)), $field->id);
				}
			}
		}
		
		function updateForm($form, $fid, $skip_encode = array('form_access', 'form_fields')) {
			global $wpdb;
			if (!empty($form['form_slug'])) {
				$test = $this->selectForm('', $this->formatSlug($form['form_slug']));
				if (!empty($test) and $test->id != $fid) return false;
				$form['form_slug'] = $this->formatSlug($form['form_slug']);
			}
			if (!empty($form['form_access_update'])) {
				if (isset($form['form_access'])) $form['form_access'] = serialize($form['form_access']);
				else $form['form_access'] = serialize(array());
				unset($form['form_access_update']);
			} elseif (!empty($form['form_access'])) unset($form['form_access']);
			
			if (isset($form['form_fields']))
				$form['form_fields'] = serialize(array_unique($form['form_fields']));
			foreach ($form as $key => $value)
				if (!in_array($key, $skip_encode))
					$form[$key] = ccf_utils::encodeOption($value);
			$wpdb->update(CCF_FORMS_TABLE, $form, array('id' => $fid));
			return true;
		}
		
		function updateField($field, $fid, $skip_encode = array('field_options', 'field_allowed_file_extensions')) {
			global $wpdb;
			if (!empty($field['field_slug'])) {
				$test = $this->selectField('', $this->formatSlug($field['field_slug']));
				if ((!empty($test) and $test->id != $fid) or array_key_exists($this->formatSlug($field['field_slug']), $GLOBALS['ccf_fixed_fields']))
					return false;
				$field['field_slug'] = $this->formatSlug($field['field_slug']);
			} if (isset($field['field_options']))
				$field['field_options'] = serialize(array_unique($field['field_options']));
			if (isset($field['field_allowed_file_extensions']))
				$field['field_allowed_file_extensions'] = $this->formatFileExtensions($field['field_allowed_file_extensions']);
			foreach ($field as $key => $value)
				if (!in_array($key, $skip_encode))
					$field[$key] = ccf_utils::encodeOption($value);
			$wpdb->update(CCF_FIELDS_TABLE, $field, array('id' => $fid));
			return true;
		}
		
		function updateFieldOption($option, $oid) {
			global $wpdb;
			if (!empty($option['option_slug'])) {
				$test = $this->selectFieldOption('', $this->formatSlug($option['option_slug']));
				if (!empty($test) and $test->id != $oid)
					return false;
				$option['option_slug'] = $this->formatSlug($option['option_slug']);
			}
			foreach ($option as $key => $value)
					$option[$key] = ccf_utils::encodeOption($value);
			$wpdb->update(CCF_FIELD_OPTIONS_TABLE, $option, array('id' => $oid));
			return true;
		}
		
		function updateStyle($style, $sid) {
			global $wpdb;
			if (empty($style['style_slug'])) return false;
			$test = $this->selectStyle('', $this->formatSlug($style['style_slug']));
			if (!empty($test) and $test->id != $sid) // if style_slug is different then make sure it is unique
				return false;
			$style['style_slug'] = $this->formatSlug($style['style_slug']);
			foreach ($style as $key => $value)
					$style[$key] = ccf_utils::encodeOption($value);
			$wpdb->update(CCF_STYLES_TABLE, $style, array('id' => $sid));
			return true;
		}
		
		function deleteForm($fid, $slug = NULL) {
			global $wpdb;
			$where_params = ($slug == NULL) ? "id='$fid'" : "form_slug='$slug'";
			$wpdb->query("DELETE FROM " . CCF_FORMS_TABLE . ' WHERE ' . $where_params);
			return true;
		}
		
		function deleteField($fid, $slug = NULL) {
			global $wpdb;
			$this->detachFieldAll($fid);
			$where_params = ($slug == NULL) ? "id='$fid'" : "field_slug='$slug'";
			$wpdb->query("DELETE FROM " . CCF_FIELDS_TABLE . ' WHERE ' . $where_params);
			return false;
		}
		
		function query($query) {
			global $wpdb;
			if (empty($query)) return false;
			return ($wpdb->query($query) != false) ? $wpdb->insert_id : false;
		}
		
		function deleteStyle($sid, $slug = NULL) {
			global $wpdb;
			$this->detachStyleAll($sid);
			$where_params = ($slug == NULL) ? "id='$sid'" : "style_slug='$slug'";
			$wpdb->query("DELETE FROM " . CCF_STYLES_TABLE . ' WHERE ' . $where_params);
			return true;
		}
		
		function deleteFieldOption($oid, $slug = NULL) {
			global $wpdb;
			$this->detachFieldOptionAll($oid);
			$where_params = ($slug == NULL) ? "id='$oid'" : "option_slug='$slug'";
			$wpdb->query("DELETE FROM " . CCF_FIELD_OPTIONS_TABLE . ' WHERE ' . $where_params);
			return true;
		}
		
		function deleteUserData($uid) {
			global $wpdb;
			$wpdb->query("DELETE FROM " . CCF_USER_DATA_TABLE . " WHERE id='$uid'");
			return true;
		}
		
		function selectAllFromTable($table, $output_type = OBJECT) {
			global $wpdb;
			return $wpdb->get_results('SELECT * FROM ' . $table, $output_type);
		}
		
		function selectAllForms() {
			global $wpdb;
			return $wpdb->get_results("SELECT * FROM " . CCF_FORMS_TABLE . " ORDER BY form_slug ASC");	
		}
		
		function selectAllFields() {
			global $wpdb;
			return $wpdb->get_results("SELECT * FROM " . CCF_FIELDS_TABLE . " ORDER BY field_slug ASC");	
		}
		
		function selectAllFieldOptions($user_option = -1) {
			global $wpdb;
			return $wpdb->get_results("SELECT * FROM " . CCF_FIELD_OPTIONS_TABLE . " ORDER BY option_slug ASC");	
		}
		
		function selectAllStyles() {
			global $wpdb;
			return $wpdb->get_results("SELECT * FROM " . CCF_STYLES_TABLE . " ORDER BY style_slug ASC");	
		}
		
		function selectAllUserData($form_id = NULL) {
			global $wpdb;
			$where = ($form_id != NULL) ? " WHERE data_formid = '$form_id' " : '';
			return $wpdb->get_results("SELECT * FROM " . CCF_USER_DATA_TABLE . " $where ORDER BY data_time DESC");	
		}
		
		function selectForm($fid, $form_slug = '') {
			global $wpdb;
			$extra = (!empty($form_slug)) ? " or form_slug = '$form_slug'" : '';
			return $wpdb->get_row("SELECT * FROM " . CCF_FORMS_TABLE . " WHERE id='$fid' $extra");
		}
		
		function selectStyle($sid, $style_slug = '') {
			global $wpdb;
			$extra = (!empty($style_slug)) ? " or style_slug = '$style_slug'" : '';
			return $wpdb->get_row("SELECT * FROM " . CCF_STYLES_TABLE . " WHERE id='$sid' $extra");
		}
		
		function selectField($fid, $field_slug = '') {
			global $wpdb;
			$extra = (!empty($field_slug)) ? " or field_slug = '$field_slug'" : '';
			return $wpdb->get_row("SELECT * FROM " . CCF_FIELDS_TABLE . " WHERE id='$fid'" . $extra);
		}
		
		function selectFieldOption($oid, $option_slug = '') {
			global $wpdb;
			$extra = (!empty($option_slug)) ? " or option_slug = '$option_slug'" : '';
			return $wpdb->get_row("SELECT * FROM " . CCF_FIELD_OPTIONS_TABLE . " WHERE id='$oid'" . $extra);
		}
		
		function selectUserData($uid) {
			global $wpdb;
			return $wpdb->get_row("SELECT * FROM " . CCF_USER_DATA_TABLE . " WHERE id='$uid'");
		}
		
		function addFieldToForm($field_id, $form_id) {
			$field = $this->selectField($field_id);
			if (empty($field)) return false;
			$form = $this->selectForm($form_id);
			if (empty($form)) return false;
			$fields = $this->getAttachedFieldsArray($form_id);
			if (!in_array($field_id, $fields)) {
				$fields[] = $field_id;
				$this->updateForm(array('form_fields' => $fields), $form_id);
				return true;
			}
			return false;
		}
		
		function addFieldOptionToField($option_id, $field_id) {
			$option = $this->selectFieldOption($option_id);
			if (empty($option)) return false;
			$field = $this->selectField($field_id);
			if (empty($field)) return false;
			$options = $this->getAttachedFieldOptionsArray($field_id);
			if (!in_array($option_id, $options)) {
				$options[] = $option_id;
				$this->updateField(array('field_options' => $options), $field_id);
				return true;
			}
			return false;
		}
		
		function unserializeFormPageIds($form) {
			$pids = str_replace(' ', '', $form->form_pages);
			return explode(',', $pids);
		}
		
		function getAttachedFieldsArray($form_id) {
			$form = $this->selectForm($form_id);
			$out = unserialize($form->form_fields);
			if (!is_array($out)) return array();
			return (empty($out)) ? array() : $out;
		}
		
		function getAttachedFieldOptionsArray($field_id) {
			$field = $this->selectField($field_id);
			$out = unserialize($field->field_options);
			if (!is_array($out)) return array();
			return (empty($out)) ? array() : $out;
		}
		
		function old_getAttachedFieldsArray($fields) {
			if (empty($fields)) return array();
			$last_char = $fields[strlen($fields)-1];
			if ($last_char != ',') $fields .= ',';
			$out = explode(',', $fields);
			if (!empty($out)) array_pop($out);
			return $out;
		}
		
		function old_getAttachedFieldOptionsArray($options) {
			if (empty($options)) return array();
			$last_char = $options[strlen($options)-1];
			if ($last_char != ',') $options .= ',';
			$out = explode(',', $options);
			if (!empty($out)) array_pop($out);
			return $out;
		}
		
		function detachField($field_id, $form_id) {
			$fields = $this->getAttachedFieldsArray($form_id);
			if (!empty($fields) && in_array($field_id, $fields)) {
				unset($fields[array_search($field_id, $fields)]);
				$this->updateForm(array('form_fields' => $fields), $form_id);
				return true;
			}
			return false;
		}

		function detachFieldOption($option_id, $field_id) {
			$options = $this->getAttachedFieldOptionsArray($field_id);
			//var_dump($options);
			if (!empty($options) && in_array($option_id, $options)) {
				unset($options[array_search($option_id, $options)]);
				$this->updateField(array('field_options' => $options), $field_id);
				return true;
			}
			return false;
		}
				
		function detachFieldAll($field_id) {
			$forms = $this->selectAllForms();
			foreach ($forms as $form)
				$this->detachField($field_id, $form->id);
			return true;
		}
		
		function detachFieldOptionAll($option_id) {
			$fields = $this->selectAllFields();
			foreach ($fields as $field)
				$this->detachFieldOption($option_id, $field->id);
			return true;
		}
		
		function detachStyleAll($style_id) {
			$forms = $this->selectAllForms();
			foreach ($forms as $form) {
				if ($form->form_style == $style_id) {
					$this->updateForm(array('form_style' => 0), $form->id);
				}
			}
			return true;
		}
		
		function formatSlug($slug) {
			$slug = preg_replace('/[^a-z_ A-Z0-9\s]/', '', $slug);
			return str_replace(' ', '_', $slug);	
		}
		
		function formatFileExtensions($str) {
			$str = str_replace('.', '', $str);
			$str = str_replace(' ', '', $str);
			$arr = explode(',', $str);
			foreach ($arr as $k => $v) {
				if (empty($v)) unset($arr[$k]);
			}
			return serialize($arr);
		}
		
		function fieldSlugExists($slug) {
			$test = $this->selectField('', $slug);
			return (!empty($test));
		}
		
		function styleSlugExists($slug) {
			$test = $this->selectStyle('', $slug);
			return (!empty($test));
		}
		
		function formSlugExists($slug) {
			$test = $this->selectForm('', $slug);
			return (!empty($test));
		}
		
		function fieldOptionsSlugExists($slug) {
			$test = $this->selectFieldOption('', $slug);
			return (!empty($test));
		}
		
		function insertUserData($data_object) {
			global $wpdb;
			$wpdb->insert(CCF_USER_DATA_TABLE, array('data_time' => $data_object->getDataTime(), 'data_formid' => $data_object->getFormID(), 'data_formpage' => $data_object->getFormPage(), 'data_value' => $data_object->getEncodedData()));
			return $wpdb->insert_id;
		}
		
		function emptyAllTables() {
			$fields = $this->selectAllFields();
			$forms = $this->selectAllForms();
			$user_data = $this->selectAllUserData();
			$styles = $this->selectAllStyles();
			$options = $this->selectAllFieldOptions();
			foreach ($fields as $field) $this->deleteField($field->id);
			foreach ($forms as $form) $this->deleteForm($form->id);
			foreach ($user_data as $data) $this->deleteUserData($data->id);
			foreach ($styles as $style) $this->deleteStyle($style->id);
			foreach ($options as $option) $this->deleteFieldOption($option->id);
		}
	
		
		function formHasRole($form_access_array, $role) {
			return (in_array(strtolower($role), $form_access_array));
		}
		
		function getRolesArray() {
			global $wp_roles;
			$out = $wp_roles->get_names();
			$out[] = __('Non-Registered User', 'custom-contact-forms');
			return $out;
		}
		
		function getFormAccessArray($form_access_string) {
			$arr = unserialize($form_access_string);
			if (!empty($arr)) $arr = array_map('strtolower', $arr);
			if ($arr == false || empty($form_access_string)) $arr = array();
			return $arr;
		}
	}
}
?>