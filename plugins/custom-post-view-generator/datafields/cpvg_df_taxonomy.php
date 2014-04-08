<?php
class cpvg_df_taxonomy{
	public function adminProperties() {
		/* Available fields: 'category', 'post_tag', 'nav_menu', 'link_category', 'post_format' and custom taxonomy */
		$result = array('taxonomy' => array('category' => 'Categories', 'post_tag' => 'Post Tags',
											'nav_menu' => 'Navigation Menus', 'link_category' => 'Link Catgories',
											'post_format' => 'Format'));

		//Get custom taxonomies
		$taxonomies=get_taxonomies('','objects');
		foreach ($taxonomies as $taxonomy) {
			$result['taxonomy'][$taxonomy->name] = $taxonomy->label;
		}

		return $result;
    }

	public function getValue($field_name,$post_data,$custom_text) {
		$result = array();
		$terms = get_the_terms($post_data->ID, $field_name);
		if(is_array($terms)){
			foreach($terms as $term){
				$result[] = $term->name;
			}
		}

		return serialize($result);
    }
}
?>