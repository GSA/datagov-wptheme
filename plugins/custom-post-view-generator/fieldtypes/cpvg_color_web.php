<?php
class cpvg_color_web{

    public function adminProperties() {
		$output_options1 = array('square'=>'Colored Square',
								 'hexvalue'=>'Hex value',
								 'color_hexvalue'=>'Colored hex value');

		return array('cpvg_color_web' => array('label'=>'Color (Web)',
										  'options'=>array($output_options1)));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			//show something in the preview
			$colors = array("ffcc00","ffff99","cfeef6","b2ebc5","ffffff","d7ebff","dfceb9","b3ccc5","000000");
			$value = $colors[rand(1,8)];
		}

		$value = trim($value);
		$value = str_replace("#","",$value);
		//$output_options[1] -> color display from $output_options1
		switch($output_options[1]){
			case 'square':
				return "<div style='width:20px; height:20px; display:inline; background-color:#".$value.";margin:0px;padding:0px;'>&nbsp;&nbsp;&nbsp;&nbsp;</div>";
				break;
			case 'hexvalue':
				return "#".$value;
				break;
			case 'color_hexvalue':
				return "<div style='display:inline; color:#".$value.";'>#$value</div>";
				break;
		}
	}

}
?>
