<?php
class cpvg_rw{
    public function isEnabled(){
		return in_array("reed-write/reed-write.php",get_option("active_plugins"));
    }

	public function getCustomfields($custom_post_type){
		global $wpdb;
		$custom_fields_data = array();
		$custom_post_data=get_post_types(array('_builtin'=>false),'object');
		
		if(!empty($custom_post_data)){
			$custom_post_name = $custom_post_data[$custom_post_type]->labels->name;
			
			$cpt_post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '".$custom_post_name."' AND post_type = 'rw_content_type'");

			if(!is_null($cpt_post_id)){
				$custom_fields = get_post_meta($cpt_post_id,"fields",false);
				$custom_fields = $custom_fields[0];

				if(isset($custom_fields) && is_array($custom_fields)){
					foreach($custom_fields as $custom_field_idx => $custom_field_value){
						$custom_fields_data[cpvg_sanitize_title_with_underscores($custom_field_value['name'])] = $custom_field_value['name'];
					}
					//array_walk($custom_fields, create_function('$val, $key, $obj', '$obj[$val["name"]] = $val["name"];'), &$custom_fields_data);
				}
			}
		}

		return $custom_fields_data;
	}

	public function processPageAdditionalCode($singular_type_name, $data){
		// The Content Types plugin sanatizes values, so we have to unsanatized them
		$custom_post_data=get_post_types(array('_builtin'=>false),'object');

		if(isset($custom_post_data[$singular_type_name])){
			$plural_type_name = $custom_post_data[$singular_type_name]->labels->name;

			global $wpdb;
			$cpt_post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '".$plural_type_name."' AND post_type = 'rw_content_type'");

			if(isset($cpt_post_id)){
				$post_meta_data = get_post_meta($cpt_post_id,'fields');
				$values = array();

				foreach($post_meta_data[0] as $field_data){
					if(isset($field_data['type_option_data'])){
						$unsanitized_values_aux = explode("\n",$field_data['type_option_data']);

						if(!$unsanitized_values_aux[0]){
							$unsanitized_values = null;
						}else{
							$unsanitized_values = array();
							foreach($unsanitized_values_aux as $value){
								$unsanitized_values[sanitize_title_with_dashes($value)] = trim($value);
							}
						}

						foreach($data['fields'] as $data_field_idx => $data_field_value){
							if($data_field_value['name'] == $field_data['name'] && !is_null($unsanitized_values)){
								$data['fields'][$data_field_idx]['additional_data']['reed_write_plugin_data'] = $unsanitized_values;
							}
						}
					}
				}

			}
		}

		return $data;
	}
	public function getCategories($post_data){
		$result = null;
		foreach ( get_object_taxonomies($post_data->post_type) as $tax_name ) {
			$taxonomy = get_taxonomy($tax_name);
			$result = wp_get_object_terms($post_data->ID,$tax_name);
		}
		return $result;
	}
}
?>
