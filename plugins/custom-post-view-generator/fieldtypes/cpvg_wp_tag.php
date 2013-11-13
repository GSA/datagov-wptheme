<?php
class cpvg_wp_tag{

    public function adminProperties() {
		$output_options1 = array(', '=>'Delimited by comma',
								 ' - '=>'Delimited by dash (with spaces)',
								 ' '=>'Delimited by space',
								 'ul'=>'Unordered List',
								 'ol'=>'Ordered List');

		$output_options2 = array('term_id'=>'Show ID(s)',
								 'name'=>'Show Name(s)' ,
								 'slug'=>'Show Slug(s)');

		$output_options3 = array('text'=>'Show Text',
								 'hyperlink'=>'Show Hyperlink');

		return array('cpvg_wp_tag' => array('label'=>'Wordpress Tag ID(s)',
											'options' => array($output_options1,$output_options2,$output_options3)));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			//show something in the preview
			return rand(1,99);
		}else{
			$values = array();
			$unserialized_values = unserialize($value);

			foreach($unserialized_values as $unserialized_value){
				$tag_data = get_tag($unserialized_value);

				if($output_options[3] == 'hyperlink'){
					//get_tag_link( $tag_id
					$values[]="<a href='".get_tag_link($tag_data->term_id)."'>".$tag_data->$output_options[2]."</a>";
				}else{
					$values[]=$tag_data->$output_options[2];
				}
			}

			$values =  array_filter($values, 'strlen');
			switch($output_options[1]){
				case 'ul': return "<ul><li>".implode("</li><li>",$values)."</li></ul>";
				case 'ol': return "<ol><li>".implode("</li><li>",$values)."</li></ol>";
				default: return implode($output_options[1],$values);
			}
			//$category_data = wp_get_post_categories($value);
			/*if($output_options[2] == 'text'){
				return $post_data->$output_options[1];
			}else if($output_options[2] == 'hyperlink'){
				return "<a href='".$post_data->guid."'>".$post_data->$output_options[1]."</a>";
			}*/

		}
	}
}
?>