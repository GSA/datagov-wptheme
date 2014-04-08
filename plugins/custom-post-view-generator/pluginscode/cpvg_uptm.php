<?php
class cpvg_uptm{

    public function isEnabled(){
		return in_array("ultimate-post-type-manager/index.php",get_option("active_plugins"));
    }

	public function getCustomfields($custom_post_type){
		$custom_fields = get_option("xydac_cpt_".$custom_post_type);

		if(is_array($custom_fields)){
			foreach($custom_fields as $custom_field_idx => $custom_field_value){
					$custom_fields_data[$custom_field_value['field_name']] = $custom_field_value['field_label'];
			}
			//array_walk($custom_fields, create_function('$val, $key, $obj', '$obj[$val["field_name"]] = $val["field_label"];'), &$custom_fields_data);
		}else{
			return array();
		}

		return $custom_fields_data;
	}

	public function processPageAdditionalCode($singular_type_name, $data){
		unset($data['field_data']['_edit_last']);
		unset($data['field_data']['_edit_lock']);

		foreach($data['field_data'] as $field_name => $field_value){
			$field_value = unserialize($field_value[0]);

			if(is_array($field_value)){	$field_type = array_keys($field_value); }

			switch($field_type[0]){
				case 'link': $data['field_data'][$field_name] = array($field_value['link']['link_url']); break;
				case 'textarea': $data['field_data'][$field_name] = array($field_value['textarea']); break;
				case 'richtextarea': $data['field_data'][$field_name] = array($field_value['richtextarea']); break;
				case 'checkbox': $data['field_data'][$field_name] = array(implode("|",array_values($field_value['checkbox']))); break;
				case 'image': $data['field_data'][$field_name] = array($field_value['image']['img_url']); break;
				case 'text': $data['field_data'][$field_name] = array($field_value['text']); break;
				case 'radiobutton': $data['field_data'][$field_name] = array($field_value['radiobutton']); break;
				case 'combobox': $data['field_data'][$field_name] = array($field_value['combobox']); break;
				case 'gallery':
					$data['field_data'][$field_name] = array();
					foreach($field_value['gallery'] as $gallery_photo){
						$data['field_data'][$field_name][] = $gallery_photo['img_url'];
					}
					$data['field_data'][$field_name] = array(implode(",",$data['field_data'][$field_name]));
				break;
			}
		}

		return $data;
	}

	public function getCategories($post_data){

	}

}
?>