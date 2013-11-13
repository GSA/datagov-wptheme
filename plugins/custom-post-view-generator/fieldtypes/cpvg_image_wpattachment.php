<?php
class cpvg_image_wpattachment{

    public function adminProperties() {

		$output_options1 = array('full'=>'Full Size',
							     'large'=>'Large Size',
								 'medium'=>'Medium Size',
								 'thumbnail'=>'Thumbnail Size');

		$output_options2 = array('none'=>'Not delimited',
								 'space'=>'Delimited by space',
								 'new_line'=>'Delimited by new line',
								 'span'=>'Delimited by span',
								 'div'=>'Delimited by div');

		return array('cpvg_image_wpattachment' => array('label'=>'Image(s) (Wordpress Attachment)',
												   'options'=>array($output_options1,$output_options2)));

    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			//show something in the preview
			return "<img src='".CPVG_PLUGIN_URL."/wordpress-logo.png'/>";
		}else{

		$delimiters = array('none'=>'','space'=>' ','new_line'=>'<br />',
							'span'=>'<span class=\'cpvg_spacer\'></span>',
							'div'=>'<div class=\'cpvg_spacer\'></div>');

		$images=array();

		//wp attachments are always numbers, so any other character is used as a delimiter
		$ids = explode('.',str_replace(array(',',';','|',' ','-','/','\\','.'),'.',trim($value)));

		foreach($ids as $id){
			//$output_options[1] -> image size from $output_options1
			$images[] = wp_get_attachment_image($id,$output_options[1]);
		}
		//$output_options[2] -> multiple image delimiters from $output_options2
		return implode($delimiters[$output_options[2]],$images);
		}
	}
}
?>