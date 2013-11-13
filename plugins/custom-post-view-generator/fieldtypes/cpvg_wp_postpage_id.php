<?php
class cpvg_wp_postpage_id{

    public function adminProperties() {
		$output_options1 = array('ID'=>'Show Id',
								 'guid'=>'Show Url',
								 'post_name'=>'Show Name',
								 'post_title'=>'Show Title');

		$output_options2 = array('text'=>'Set Text',
								 'hyperlink'=>'Set as Hyperlink');

		return array('cpvg_wp_postpage_id' => array('label'=>'Wordpress Post/Page ID',
												  'options' => array($output_options1,$output_options2)));

    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			//show something in the preview
			return rand(1,99);
		}else{
			$post_data = get_post($value);
			if($output_options[2] == 'text'){
				return $post_data->$output_options[1];
			}else if($output_options[2] == 'hyperlink'){
				return "<a href='".$post_data->guid."'>".$post_data->$output_options[1]."</a>";
			}
		}
	}
}
?>