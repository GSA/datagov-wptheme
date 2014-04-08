<?php
class cpvg_multiple_values_vertical_bar{

    public function adminProperties() {
		$output_options1 = array(', '=>'Delimited by comma',
								 ' - '=>'Delimited by dash',
								 ' '=>'Delimited by space',
								 'ul'=>'Unordered List',
								 'ol'=>'Ordered List');

		return array('cpvg_multiple_values_vertical_bar' => array('label' => 'Multiple values (Vertical Bar)',
															 'options' => array($output_options1)));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			$values = array(cpvg_random_text_value(),cpvg_random_text_value(),cpvg_random_text_value());
		}else{
			$values = explode("|",$value);
		}

		//REQUIRED CODE TO DELIVER REMOVE EXTRA VALUE INSERTED BY THE Ultimate Post Type Manager by XYDAC (when used)
		$values = array_diff($values,array('xydac-null'));

		if(is_array($values)){
			//REQUIRED CODE TO DELIVER NON SANATIZED VALUES SAVED BY THE reed write by iambriansreed (WHEN USED)
			if($additional_data['reed_write_plugin_data']){
				foreach($values as $idx => $value){
					$values[$idx] = $additional_data['reed_write_plugin_data'][$value];
				}
			}

			//remove null and empty values
			$values = array_filter($values, 'strlen');

			switch($output_options[1]){
				case 'ul': return "<ul><li>".implode("</li><li>",$values)."</li></ul>";
				case 'ol': return "<ol><li>".implode("</li><li>",$values)."</li></ol>";
				default: return implode($output_options[1],$values);
			}
		}else{
			return $values; // NOT AN ARRAY
		}
	}
}
?>
