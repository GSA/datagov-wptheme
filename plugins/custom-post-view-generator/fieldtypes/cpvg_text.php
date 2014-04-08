<?php
class cpvg_text{
    public function adminProperties() {

		$output_options1 = array('no_modification' => 'No modification',
								 'ucwords'=>'Capitalize all words',
								 'ucfirst'=>'Capitalize first character',
								 'strtolower'=>'Lowercase all text',
								 'lcfirst' => 'Lowercase first character',
								 'strtoupper'=>'Uppercase all text',
								 'ucfirst'=>'Uppercase first character');

		$output_options2 = array('no_modification' => 'No modification',
								 '_'=>'Remove underscores',
								 '-'=>'Remove slashes',
								 '_,-'=>'Remove underscores and slashes');

		return array('cpvg_text' => array('label'=>'Text',
										  'options'=>array($output_options1,$output_options2)));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		
		if(is_string($value) && $value=='NOT_SET'){
			return cpvg_random_text_value();
		}else{

			$return_value = $value;
			//REQUIRED CODE TO DELIVER NON SANATIZED VALUES SAVED BY THE reed write by iambriansreed (WHEN USED)
			if(isset($additional_data['reed_write_plugin_data'][$value])){
				$return_value = $additional_data['reed_write_plugin_data'][$value];
			}

			if($output_options[2] != 'no_modification' && $output_options[2] != NULL){
				$output_options[2] = explode(",",$output_options[2]);
				$replacement_array = array_fill(0, count($output_options[2]), ' ');
				$return_value = str_replace($output_options[2],$replacement_array, $return_value);
			}

			if($output_options[1] != 'no_modification' && $output_options[1] != NULL){
				$return_value = $output_options[1]($return_value);
			}

			return $return_value;
		}
	}
}
?>
