<?php
class cpvg_param_usersorting{
	public function getParameterData($type='usersorting') {
		
		$param_data = array();
		if ($type == 'usersorting'){
			$param_data['usersorting']['fields'] = array('usersorting_choice'=>'User Sorting');

			
			$param_data['usersorting']['choices'] = array();

			$param_data['usersorting']['message'] = array("Create only one pagination parameter for each list.");		
			
			$param_data['usersorting']['choices']['usersorting_choice'] = array('usersorting_disabled'=>'Disabled', 'usersorting_enabled'=>'Enabled');			
		}																	

		return $param_data;
	}

	public function applyParameterData($type,$data) {
		switch($data['parameter']){			
			case 'usersorting_choice':
			default:
				if(is_array($data['value'])){
					$data['value'] = implode(",",$data['value']);
				}
				return array($data['parameter'] => $data['value']);
		}
		return array();
	}
}
?>
