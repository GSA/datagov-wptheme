<?php
class cpvg_param_user{
	public function getParameterData($type='filter') {
		$param_data = array();

		switch($type){
			case 'filter':
				$param_data['user']['fields'] = array('author'=>'Author IDs (OR)', 'author_not' => 'Author IDs (NOT IN)');

				$param_data['user']['choices'] = array('author'=>array());

				//There is bug/error (?) that the author_not will not will with multiple values
				//removed commented option when is fixed
				$param_data['user']['mutiple_choices'] = array('author'/*,'author_not'*/);

				$param_data['user']['message'] = array("When a multi select list is presented, the custom value field will accept a comma separed list of values.",
													   "When a single select list is presented, the custom value field will treat the input text as a single value.",
													   "When creating mutiple filters for this section, choose diferent a parameter for each filter.");

				//existing user ids that are going to be displayed on a select box
				$users_data = get_users();
				foreach($users_data as $user_data){
					foreach($param_data['user']['fields'] as $field_index => $field_value){
						$param_data['user']['choices'][$field_index][$user_data->ID] = $user_data->user_login." (ID:".$user_data->ID.")";
					}
				}

				$param_data['user']['fields']['author_name'] = 'Author Nice Name';
				foreach($users_data as $user_data){
					$param_data['user']['choices']['author_name'][$user_data->user_nicename] = $user_data->user_login." (".$user_data->user_nicename.")";
				}


			break;
		}

		return $param_data;
	}

	public function applyParameterData($type,$data) {
		switch($type){
			case 'filter':
				switch($data['parameter']){
					case 'author':
					case 'author_name':
						if(is_array($data['value'])){
							$data['value'] = implode(",",$data['value']);
						}
						return array($data['parameter'] => $data['value']);
					case 'author_not': // NOT MULTIPLE
						if(is_string($data['value'])){
							$data['value'] = explode(",",$data['value']);
						}

						foreach($data['value'] as $index => $value){
							$data['value'][$index] = "-".$value;
						}

						return array('author' => $data['value'] = implode(",",$data['value']));
				}
			break;
		}
		return array();
	}
}
?>