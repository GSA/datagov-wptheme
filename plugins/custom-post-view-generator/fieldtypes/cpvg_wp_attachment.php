<?php
class cpvg_wp_attachment{

    public function adminProperties() {
		$output_options1 = array(', '=>'Delimited by comma',
								 ' - '=>'Delimited by dash (with spaces)',
								 ' '=>'Delimited by space',
								 'ul'=>'Unordered List',
								 'ol'=>'Ordered List');

		$output_options2 = array('id'=>'Show Id',
								 'url'=>'Show Url',
								 'filepath'=>'Show File Path',
								 'filename'=>'Show Filename',
								 'alt' => 'Show Alternative text',
								 'post_excerpt' => 'Show Caption',
								 'post_title' => 'Show Title',
								 );

		$output_options3 = array('text'=>'Show Text',
								 'hyperlink'=>'Show Hyperlink');

		return array('cpvg_wp_attachment' => array('label'=>'Wordpress Attachment ID(s)',
											       'options' => array($output_options1,$output_options2,$output_options3)));

    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {

		if(is_string($value) && $value=='NOT_SET'){
			//show something in the preview
			$values = array(rand(1,99));
		}else{
			$values = array();
			$ids = explode('.',str_replace(array(',',';','|',' ','-','/','\\','.'),'.',trim($value)));
			foreach($ids as $id){
				$value = "";

				switch ($output_options[2]){
					case 'id':
						$value = $id; break;
					case 'url':
						$value= wp_get_attachment_url($id); break;
					case 'filepath':
						$file_data = wp_get_attachment_metadata($id);
						$value = $file_data['file'];  break;
					case 'filename':
						$value = basename(wp_get_attachment_url($id)); break;
					case 'alt':
						$value = get_post_meta($id, '_wp_attachment_image_alt', true);
						break;
					default:
						$attachment_data = @get_post($id);
						$value = $attachment_data->$output_options[2];
						break;
				}

				if(!empty($value)){
					if($output_options[3] == 'hyperlink'){
						$value = "<a href='".wp_get_attachment_url($id)."'>".$value."</a>";
					}
					$values[] = $value;
				}
			}
		}

		$values =  array_filter($values, 'strlen');
		switch($output_options[1]){
			case 'ul': return "<ul><li>".implode("</li><li>",$values)."</li></ul>";
			case 'ol': return "<ol><li>".implode("</li><li>",$values)."</li></ol>";
			default: return implode($output_options[1],$values);
		}
	}
}
?>