<?php
class cpvg_multiple_images_url_comma{

    public function adminProperties() {

		$output_options1 = array('none'=>'Not delimited',
								 'space'=>'Delimited by space',
								 'new_line'=>'Delimited by new line',
								 'span'=>'Delimited by span',
								 'div'=>'Delimited by div');

		return array('cpvg_multiple_images_url_comma' => array('label'=>'Muliple Image Urls (Comma)',
											   				   'options'=>array($output_options1)));

    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			$value = CPVG_PLUGIN_URL."/wordpress-logo.png,".CPVG_PLUGIN_URL."/wordpress-logo.png";
		}

		$delimiters = array('none'=>'','space'=>' ','new_line'=>'<br />',
							'span'=>'<span class=\'cpvg_spacer\'></span>',
							'div'=>'<div class=\'cpvg_spacer\'></div>');
		$images=array();

		$urls = explode(',',trim($value));

		foreach($urls as $url){
			//$output_options[1] -> image size from $output_options1
			$images[] = "<img src='".$url."'/>";
		}

		return implode($delimiters[$output_options[1]],$images);
	}
}
?>