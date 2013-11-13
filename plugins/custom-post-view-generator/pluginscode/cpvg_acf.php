<?php
class cpvg_acf{
    public function isEnabled(){
		return in_array("advanced-custom-fields/acf.php",get_option("active_plugins"));
    }

	public function getCustomfields($custom_post_type){
		global $wpdb;
		$custom_fields_data = array();

		foreach($wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'acf' AND post_status = 'publish'") as $post_key=>$post_row){
			foreach($wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = ".$post_row->ID." AND meta_key LIKE 'field\_%'") as $postmeta_key=>$postmeta_row){
		
				$value = unserialize($postmeta_row->meta_value);
				$custom_fields_data[$value['name']] = $value['label'];
			}
		}

		return $custom_fields_data;
	}

	public function processPageAdditionalCode($singular_type_name, $data){
		return $data;
	}
}
?>
