<?php
class cpvg_df_category{
	public function adminProperties() {
		/* Available fields:
		'term_id', 'name', 'slug', 'term_group', 'term_taxonomy_id', 'taxonomy', 'description',
		'parent', 'count', 'cat_ID', 'category_count', 'category_description',
		'cat_name', 'category_nicename', 'category_parent'
		*/
		return array('category' => array('cat_ID'=>'ID', 'cat_name'=>'Name' , 'category_nicename'=>'Nicename',
										 'term_id'=>'Term ID' , 'term_group'=>'Term Groups' , 'parent'=>'Parent Id',
										 'count'=>'Post Count' , 'category_description'=>'Description'));
    }

	public function getValue($field_name,$post_data) {
		$result = array();
		$category_data = array();

		$category_data = wp_get_post_categories($post_data->ID);
		if(is_array($category_data)){
			foreach($category_data as $c){
				$cat = get_category( $c );
				$result[] = $cat->$field_name;
			}
		}

		return serialize($result);
    }
}
?>
