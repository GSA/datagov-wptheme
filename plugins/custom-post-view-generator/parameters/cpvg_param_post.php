<?php
class cpvg_param_post{
	public function getParameterData($type='filter') {
		$param_data = array();

		switch($type){
			case 'filter':
				$param_data['post'] = array();

				//post and page WP_Query parameter, 'name' not needed since all id are provided and going to be displayed on a select box
				$param_data['post']['fields'] = array('p'=>'Post ID','post__in'=>'Post IDs (OR)','post__not_in'=>'Post IDs (NOT IN)');

				$param_data['post']['mutiple_choices'] = array('post__in','post__not_in','post_status');

				$param_data['post']['message'] = array("When a multi select list is presented, the custom value field will accept a comma separed list of values.",
													   "When a single select list is presented, the custom value field will treat the input text as a single value.",
													   "When creating mutiple filters for this section, choose diferent a parameter for each filter.",
													   "The Post Slug parameter will only accept one slug.");
				//existing post ids that are going to be displayed on a select box
				$posts_data=get_posts(array('post_status'=>'any'));
				foreach($param_data['post']['fields'] as $post_field_name => $post_field_label){
					$param_data['post']['choices'][$post_field_name] = array();
					foreach($posts_data as $post_data){
						$param_data['post']['choices'][$post_field_name][$post_data->ID] = $post_data->post_title." (ID: ".$post_data->ID.")";
					}
				}

				$param_data['post']['mutiple_choices'][] = 'post_status';
				$param_data['post']['fields']['post_status'] = 'Post Status';
				$param_data['post']['choices']['post_status'] = array('publish'=>'Pusblished (publish)', 'pending'=>'Pending (pending)', 'draft'=>'Draft (draft)',
																	  'auto-draft'=>'Auto Draft (auto-draft)', 'future'=>'Future (future)', 'private'=>'Private (private)',
																	  'inherit'=>'Revision (inherit)', 'trash'=>'Trash (trash)', 'any' => 'Any (any)');

				//Fields that don't have choices, and a input box will be displayed to insert values
				$param_data['post']['fields']['name'] = 'Post Slug';
			break;
		}

		return $param_data;
	}

	public function applyParameterData($type,$data) {
		switch($type){
			case 'filter':
				switch($data['parameter']){
					case 'p':  // NOT MULTIPLE
						if(is_array($data['value'])){
							$data['value'] = implode(",",$data['value']);
						}
						return array($data['parameter'] => $data['value']);
					case 'post__in':
					case 'post__not_in':
						if(is_string($data['value'])){
							$data['value'] = explode(",",$data['value']);
						}
						return array($data['parameter'] => $data['value']);
					case 'post_status':
					case 'name':
						return array($data['parameter'] => $data['value']);
				}
			break;
		}
		return array();
	}
}
?>
