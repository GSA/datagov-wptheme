<?php
class cpvg_hyperlink_url{

    public function adminProperties() {
		return array('cpvg_hyperlink_url' => array('label'=>'Hiperlink (URL)'));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if($value=='NOT_SET'){
			//show something in the preview
			return "<a href='http://www.google.com'>http://www.google.com</a>";
		}

		return "<a href='$value'>$value</a>";
	}
}
?>