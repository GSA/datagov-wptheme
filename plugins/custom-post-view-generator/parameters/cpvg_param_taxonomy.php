<?php
class cpvg_param_taxonomy{
	public function getParameterData($type='filter') {
		$param_data = array();

		switch($type){
			case 'filter':
				$param_data['taxonomy'] = array('fields' => array(), 'choices' => array());
				$param_data['taxonomy']['mutiple_choices'] = array();

				$param_data['taxonomy']['message'] = array("When a multi select list is presented, the custom value field will accept a comma separed list of values.",
														   "When a single select list is presented, the custom value field will treat the input text as a single value.",
														   "When creating mutiple filters for this section, choose diferent a parameter for each filter.");

				$taxonomies=get_taxonomies('','objects');
				foreach ($taxonomies as $taxonomy) {
					$param_data['taxonomy']['fields'][$taxonomy->name] = $taxonomy->label;
					$param_data['taxonomy']['mutiple_choices'][] = $taxonomy->name;

					foreach (get_terms($taxonomy->name) as $term){
						$param_data['taxonomy']['choices'][$taxonomy->name][$term->term_id] = $term->name;
					}
				}

				$param_data['taxonomy']['operator'] = array('IN'=>'In', 'NOT IN'=>'Not in', 'AND' => 'And');
			break;
		}
		return $param_data;
	}


	public function applyParameterData($type='filter',$data) {
		switch($type){
			case 'filter':
				return 	array('tax_query' => array(
									array(
										'taxonomy' => $data['parameter'],
										'field' => 'id',
										'terms' => $data['value'],
										'operator' => $data['operator']
									)
								));
			break;
		}
	}
}
?>