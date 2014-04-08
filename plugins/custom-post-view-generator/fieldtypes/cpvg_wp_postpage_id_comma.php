<?php
class cpvg_wp_postpage_id_comma{

    public function adminProperties() {
		$output_options1 = array(', '=>'Delimited by comma',
								 ' - '=>'Delimited by dash',
								 ' '=>'Delimited by space',
								 'ul'=>'Unordered List',
								 'ol'=>'Ordered List');

		$output_options2 = array('ID'=>'Show Id',
								 'guid'=>'Show Url',
								 'post_name'=>'Show Name',
								 'post_title'=>'Show Title');

		$output_options3 = array('text'=>'Set Text',
								 'hyperlink'=>'Set as Hyperlink');



		return array('cpvg_wp_postpage_id_comma' => array('label'=>'Wordpress Post/Page ID(s) (Comma)',
														  'options' => array($output_options1,$output_options2,$output_options3)));

    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			//show something in the preview
			return rand(1,99);
		}else{
			$output_values = array();
			$values = explode(",",$value);

			foreach($values as $value){
				$post_data = get_post($value);
				if($output_options[3] == 'text'){
					$output_values[] = $post_data->$output_options[1];
				}else if($output_options[3] == 'hyperlink'){
					$output_values[] = "<a href='".$post_data->guid."'>".$post_data->$output_options[2]."</a>";
				}
			}

			switch($output_options[1]){
				case 'ul': return "<ul><li>".implode("</li><li>",$output_values)."</li></ul>";
				case 'ol': return "<ol><li>".implode("</li><li>",$output_values)."</li></ol>";
				default: return implode($output_options[1],$output_values);
			}
		}
	}
}
?>