<?php
class cpvg_df_tag{
	public function adminProperties() {
		/* Available fields:
		'term_id', 'name', 'slug', 'term_group', 'term_taxonomy_id',
		'taxonomy', 'description', 'parent', 'count' */

		return array('tag'=>array('term_id' => 'ID', 'name' => 'Name', 'slug' => 'Slug',
								  'term_group' => 'Term Group', 'term_taxonomy_id' => 'Term Taxonomy Id',
								  'taxonomy' => 'Taxonomy', 'description' => 'Description', 'parent' => 'Parent', 'count' => 'Count'));

    }

	public function getValue($field_name,$post_data,$custom_text) {
		$tags_data = get_the_tags($post_data->ID);
		$result = array();

		if(is_array($tags_data)){
			foreach($tags_data as $tag_data){
				$result[] = $tag_data->$field_name;
			}
		}

		return serialize($result);
    }

	public function getParameterData($type='filter') {
		$param_data = array();

		switch($type){
			case 'filter':
				$param_data['tag']['fields'] = array('tag_id'=>'Tag ID', 'tag__and'=>'Tag IDs (AND)', 'tag__in'=>'Tag IDs (OR)', 'tag__not_in'=>'Tag IDs (NOT IN)');
				$param_data['tag']['message'] = 'Message for tag.';

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
