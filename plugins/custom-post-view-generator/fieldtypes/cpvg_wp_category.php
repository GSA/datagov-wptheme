<?php
class cpvg_wp_category{

    public function adminProperties() {
		$output_options1 = array(', '=>'Delimited by comma',
								 ' - '=>'Delimited by dash (with spaces)',
								 ' '=>'Delimited by space',
								 'ul'=>'Unordered List',
								 'ol'=>'Ordered List');

		$output_options2 = array('cat_ID'=>'Show ID(s)',
								 'cat_name'=>'Show Name(s)' ,
								 'category_nicename'=>'Show Nicename(s)');

		$output_options3 = array('text'=>'Show Text',
								 'hyperlink'=>'Show Hyperlink');

		return array('cpvg_wp_category' => array('label'=>'Wordpress Catgory ID(s)',
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
				$category_data = get_category($unserialized_value);
				if($output_options[3] == 'hyperlink'){
					//get_category_link( $category_id
					$values[]="<a href='".get_category_link($category_data->cat_ID)."'>".$category_data->$output_options[2]."</a>";
				}else{
					$values[]=$category_data->$output_options[2];
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

		/*if($output_options[2] == 'text'){
			switch ($output_options[1]){
				case 'id': return $value;
				case 'url': return wp_get_attachment_url($value);
				case 'filepath':
					$file_data = wp_get_attachment_metadata($value);
					return $file_data['file'];
				case 'filename': return basename(wp_get_attachment_url($value));
			}
		}else if($output_options[2] == 'hyperlink'){
			switch ($output_options[1]){
				case 'id': return "<a href='".wp_get_attachment_url($value)."'>$value</a>";
				case 'url': return "<a href='".wp_get_attachment_url($value)."'>".wp_get_attachment_url($value)."</a>";
				case 'filepath':
					$file_data = wp_get_attachment_metadata($value);
					return "<a href='".wp_get_attachment_url($value)."'>".$file_data['file']."</a>";
				case 'filename': return "<a href='".wp_get_attachment_url($value)."'>".basename(wp_get_attachment_url($value))."</a>";
			}
		}*/
	}
}
?>