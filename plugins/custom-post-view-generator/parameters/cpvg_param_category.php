<?php
class cpvg_param_category{
	public function getParameterData($type='filter') {
		$param_data = array();

		switch($type){
			case 'filter':
				$param_data['category'] = array('fields' => array(), 'choices' => array());

				$param_data['category']['fields'] = array('cat'=>'Category ID','category__and'=>'Categories IDs (AND)',
														   'category__in'=>'Categories IDs (OR)', 'category__not_in'=>'Categories IDs (NOT IN)');

				$param_data['category']['mutiple_choices'] = array('category__and','category__in','category__not_in');

				$param_data['category']['message'] = array("When a multi select list is presented, the custom value field will accept a comma separed list of values.",
														   "When a single select list is presented, the custom value field will treat the input text as a single value.",
														   "In the category slug parameter you can use a comma separed list of category slugs.",
														   "All filters will be ignored if you create more than one filter for this section.",
														   "Some custom options plugin store category data in the taxinomy table, so check that section.");

				//existing category ids that are going to be displayed on a select box
				$categories_data = get_categories();

				foreach($param_data['category']['fields'] as $cat_field_name => $cat_field_label){
					foreach($categories_data as $category_data){
						$param_data['category']['choices'][$cat_field_name][$category_data->cat_ID] = $category_data->name." (ID: ".$category_data->cat_ID.")";
					}
				}

				//no choices - a input box will be displayed
				$param_data['category']['fields']['category_name'] = 'Category Slug';
			break;
		}

		return $param_data;
	}


	public function applyParameterData($type,$data) {
		switch($type){
			case 'filter':
				switch($data['parameter']){
					case 'cat': // NOT MULTIPLE
						if(is_array($data['value'])){
							$data['value'] = implode(",",$data['value']);
						}
						return array($data['parameter'] => $data['value']);
					default:
						return array($data['parameter'] => $data['value']);
				}
			break;
		}
		return array();

	}
}
?>