<?php
class cpvg_single_image_url{

    public function adminProperties() {
		return array('cpvg_single_image_url' => array('label'=>'Single Image Url'));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
				$value = CPVG_PLUGIN_URL."/wordpress-logo.png";
		}

		return $value = "<img src='".$value."'/>";
	}

}
?>