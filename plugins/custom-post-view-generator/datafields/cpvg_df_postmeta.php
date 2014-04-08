<?php
class cpvg_df_postmeta{
	public function adminProperties() {
		global $wpdb;
		$result = array('postmeta' => array());

		$wpdb->query("SELECT DISTINCT `meta_key` FROM $wpdb->postmeta");
		//Get custom taxonomies
		foreach($wpdb->last_result as $key => $value){
			if (substr($value->meta_key,0,1) != '_'){
				$result['postmeta'][$value->meta_key] = $value->meta_key;
			}
		}

		return $result;
    }

	public function getValue($field_name,$post_data,$custom_text){
		return get_post_meta($post_data->ID, $field_name,true);
    }
}
?>