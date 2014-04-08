<?php
class cpvg_param_postmeta{
	public function getParameterData($type='filter') {
		$param_data = array();

		switch($type){
			case 'filter':
				global $wpdb;
				$results = array();
				$wpdb->query("SELECT DISTINCT `meta_key` FROM $wpdb->postmeta");

				$param_data['postmeta'] = array('fields' => array());
				$param_data['postmeta']['message'] = array("This section allows creating mutiple filters with the same section and parameter.",
														   "Do not use commas for mutiple values. Create a new filter instead.",
														   "Make sure you select the correct type for the operator that you select.",
														   "Take in account that some custom post plugins that use this table, might store values in a encoded or sanitized format.",
														   "TIP: The 'Like' parameter does text partial matching without any wildcard character.");

				foreach($wpdb->last_result as $key => $value){
					if (substr($value->meta_key,0,1) != '_'){
						$param_data['postmeta']['fields'][$value->meta_key] = $value->meta_key;
					}
				}

				$param_data['postmeta']['operator'] = array('='=>'Equal', '!='=>'Not Equal', '>'=>'Greater Than',
															'>='=>'Greater than or equal to', '<'=>'Less Than',
															'<='=>'Less than or equal to', 'LIKE'=>'Like',
															'NOT LIKE'=>'Not Like','IN'=>'In','NOT IN'=>'Not It',
															'BETWEEN'=>'Between', 'NOT BETWEEN'=>'Not Between');

				$param_data['postmeta']['type'] = array('CHAR'=>'Character', 'NUMERIC'=>'Numeric', 'BINARY'=>'Binary',
														'DATE'=>'Data', 'DATETIME'=>'Data/Time', 'DECIMAL'=>'Decimal',
														'SIGNED'=>'Signmer', 'TIME'=>'Time', 'UNSIGNED'=>'Unsigned');

			break;
		}
		return $param_data;
	}

	public function applyParameterData($type='filter',$data) {
		switch($type){
			case 'filter':
				return 	array('meta_query' => array(
									array(
										'key' => $data['parameter'],
										'value' => $data['value'],
										'type' => $data['type'],
										'compare' => $data['operator']
									)
								));
			break;
		}
	}
}
?>