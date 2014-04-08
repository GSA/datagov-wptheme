<?php
class cpvg_boolean{

    public function adminProperties() {
		$output_options1 = array('True_False'=>'True/False','Yes_No'=>'Yes/No', 'Enabled_Disabled' => 'Enabled/Disabled');

		return array('cpvg_boolean' => array('label'=>'Boolean',
											 'options' => array($output_options1)));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			$value = rand(0,1);
		}

		$options = explode("_",$output_options[1]);

		if((bool) $value){
			return $options[0];
		}else{
			return $options[1];
		}
	}
}
?>