<?php
class cpvg_wp_attachment_serialized{

    public function adminProperties() {
		$output_options1 = array(', '=>'Delimited by comma',
								 ' - '=>'Delimited by dash (with spaces)',
								 ' '=>'Delimited by space',
								 'ul'=>'Unordered List',
								 'ol'=>'Ordered List');


		$output_options2 = array('id'=>'Show Id',
								 'url'=>'Show Url',
								 'filepath'=>'Show File Path',
								 'filename'=>'Show Filename',
								 'alt' => 'Show Alternative text',
								 'post_excerpt' => 'Show Caption',
								 'post_title' => 'Show Title',
								 );

		$output_options3 = array('text'=>'Show Text',
								 'hyperlink'=>'Show Hyperlink');

		return array('cpvg_wp_attachment_serialized' => array('label' => 'Wordpress Attachment ID(s) (Serialized)',
														   'options' => array($output_options1,$output_options2,$output_options3)));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			$values = array(cpvg_random_text_value(),cpvg_random_text_value(),cpvg_random_text_value());
		}else{
			$values = unserialize($value);
		}

		if(is_array($values)){
			//REQUIRED CODE TO DELIVER NON SANATIZED VALUES SAVED BY THE reed write plugin by iambriansreed (WHEN USED)
			if($additional_data['reed_write_plugin_data']){
				foreach($values as $idx => $value){
					$values[$idx] = $additional_data['reed_write_plugin_data'][$value];
				}
			}

			//remove null and empty values
			$values = array_filter($values, 'strlen');

			$attachment = new cpvg_wp_attachment();
			foreach ($values as $key => $value) {
				$values[$key] = $attachment->processValue($value,array(null,',','post_title','text'),null);
			}

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
