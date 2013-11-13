<?php
class cpvg_hyperlink{

    public function adminProperties() {
		return array('cpvg_hyperlink' => array('label'=>'Hiperlink'));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			//show something in the preview
			return "<a href='http://www.google.com'>http://www.google.com</a>";
		}

		return "<a href='$value'>$value</a>";
	}
}
?>