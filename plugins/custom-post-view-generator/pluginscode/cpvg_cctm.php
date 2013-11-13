<?php
class cpvg_cctm{

    public function isEnabled(){
		return in_array("custom-content-type-manager/index.php",get_option("active_plugins"));
    }

	public function getCustomfields($custom_post_type){
		$custom_fields_data = array();
		$custom_post_data=get_post_types(array('_builtin'=>false),'object');
		$cvpg_cctm_data = get_option('cctm_data');

		if(!empty($custom_post_data)){
			$custom_fields = $custom_post_data[$custom_post_type]->custom_fields;
			$custom_fields_data = array();

			if(isset($custom_fields) && is_array($custom_fields)){
				foreach($custom_fields as $custom_field_idx => $custom_field_value){
					$custom_fields_data[$custom_field_value] = $cvpg_cctm_data['custom_field_defs'][$custom_field_value]['label'];
				}
				//array_walk($custom_fields, create_function('$val, $key, $obj', '$obj[$val["name"]] = $val["label"];'), &$custom_fields_data);
			}
		}
		return $custom_fields_data;
	}

	public function processPageAdditionalCode($singular_type_name, $data){
		return $data;
	}


}
?>