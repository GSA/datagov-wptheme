<?php
class cpvg_param_tag{
	public function getParameterData($type='filter') {
		$param_data = array();
		switch($type){
			case 'filter':
				$param_data['tag']['fields'] = array('tag_id'=>'Tag ID', 'tag__and'=>'Tag IDs (AND)', 'tag__in'=>'Tag IDs (OR)', 'tag__not_in'=>'Tag IDs (NOT IN)');

				$param_data['user']['mutiple_choices'] = array('tag_id','tag__and','tag__in','tag__not_in');

				$param_data['tag']['message'] = array("When a multi select list is presented, the custom value field will accept a comma separed list of values.",
													  "When a single select list is presented, the custom value field will treat the input text as a single value.",
													  "When creating mutiple filters for this section, choose diferent a parameter for each filter.",
													  "Use a comma separed list of values for the slug parameters.",
													  "Some custom options plugins store tag data in the taxinomy table, so check that section.");

				$tags_data = get_tags();
				foreach($tags_data as $tag_data){
					foreach($param_data['tag']['fields'] as $field_index => $field_value){
						$param_data['tag']['choices'][$field_index][$tag_data->term_id] = $tag_data->name." (ID:".$tag_data->term_id.")";
					}
				}

				$param_data['tag']['fields']['tag'] = 'Tag Slug';
				$param_data['tag']['fields']['tag_slug__and'] = 'Tag Slugs (AND)';
				$param_data['tag']['fields']['tag_slug__in'] = 'Tag Slugs (OR)';
			break;
		}

		return $param_data;
	}

	public function applyParameterData($type,$data) {
		switch($type){
			case 'filter':
				switch($data['parameter']){
					case 'tag_id':
					case 'tag':
						if(is_array($data['value'])){
							$data['value'] = implode(",",$data['value']);
						}
						return array($data['parameter'] => $data['value']);
					case 'tag__and':
					case 'tag__in':
					case 'tag__not_in':
					case 'tag_slug__and':
					case 'tag_slug__in':
						if(is_string($data['value'])){
							$data['value'] = explode(",",$data['value']);
						}
						return array($data['parameter'] => $data['value']);
				}
			break;
		}
		return array();
	}
}
?>
