<?php
class cpvg_df_page{
	public function adminProperties() {
		/*  Available fields:
		'ID','post_author','post_date','post_date_gmt','post_content','post_title','post_excerpt',
		'post_status','comment_status','ping_status','post_password','post_name','to_ping','pinged',
		'post_modified','post_modified_gmt','post_content_filtered','post_parent','guid','menu_order',
		'post_type','post_mime_type','comment_count','ancestors','filter'
		*/
		return array('page' => array('ID'=>'ID', 'post_title'=>'title' , 'post_name'=>'name' , 'post_author'=>'Author Id' ,
									'post_status'=>'Status' , 'post_content'=>'Content' , 'post_date'=>'Date' ,
									'post_modified'=>'Last modified' , 'comment_status'=>'Comment Status' ,	'post_type' => 'Type',
									'guid'=>'Guid', 'post_password'=>'Password' , 'post_parent'=>'Parent ID' ));
    }

	public function getValue($field_name,$post_data,$custom_text) {
		return $post_data->$field_name;
    }

	public function getParameterData($type='filter') {
		$param_data = array();

		switch($type){
			case 'filter':
				$param_data['page'] = array();

				//page WP_Query parameter, 'name' not needed since all id are provided and going to be displayed on a select box
				$param_data['page']['fields'] = array('page_id'=>'Page ID','post__in'=>'Post IDs (OR)','post__not_in'=>'Post IDs (NOT IN)','post_parent'=>'Page Parent ID');

				$param_data['page']['mutiple_choices'] = array('post__in','post__not_in');

				$param_data['page']['message'] = 'Message for page.';

				//existing pages ids that are going to be displayed on a select box
				$pages_data=get_posts(array('post_type'=>'page'));
				foreach($param_data['page']['fields'] as $page_field_name => $page_field_label){
					$param_data['page']['choices'][$page_field_name] = array();
					foreach($pages_data as $page_data){
						$param_data['page']['choices'][$page_field_name][$page_data->ID] = $page_data->post_title." (ID: ".$page_data->ID.")";
					}
				}

				//Fields that don't have choices, and a input box will be displayed to insert values
				$param_data['page']['fields']['pagename'] = 'Page Slug';
			break;
		}

		return $param_data;
	}

	public function applyParameterData($type,$data) {
		switch($type){
			case 'filter':
				switch($data['parameter']){
					case 'post__in':
					case 'post__not_in':
						if(is_string($data['value'])){
							$data['value'] = explode(",",$data['value']);
						}
						return array($data['parameter'] => $data['value']);

					case 'p':  // NOT MULTIPLE
					case 'page_id':  // NOT MULTIPLE
					case 'post_parent':  // NOT MULTIPLE
						if(is_array($data['value'])){
							$data['value'] = implode(",",$data['value']);
						}
						return array($data['parameter'] => $data['value']);

					case 'pagename':
					case 'name':
						return array($data['parameter'] => $data['value']);
				}
			break;
		}
		return array();
	}
}
?>