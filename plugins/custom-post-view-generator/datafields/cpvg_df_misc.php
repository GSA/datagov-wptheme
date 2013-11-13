<?php
class cpvg_df_misc{
	public function adminProperties() {
		$heading_options = array('h1' => 'Heading 1',
								 'h2' => 'Heading 2',
								 'h3' => 'Heading 3',
								 'h4' => 'Heading 4',
								 'h5' => 'Heading 5',
								 'h6' => 'Heading 6');

		$extra_options_settings = array('misc.custom_html_field'=>array('label'=>true,
																		 'type'=>false,
																		 'options'=>false,	   
																		 'hide_empty'=>false,
																		 'append_field_type'=>true,
																		 'form_fields' => array(
																				'cpvg_custom_html_area'=>array('type'=>'textarea','label'=>'Html Content','append_field_type'=>'true')
																		 )),
										'misc.custom_html'=>array('label'=>false,
																	'type'=>false,
																	'options'=>false,	   
																	'hide_empty'=>false,
																	'append_field_type'=>true,
																	'form_fields' => array(
																	'cpvg_custom_html_area'=>array('type'=>'textarea','label'=>'Html Content','append_field_type'=>'true')
																	)),											 	
										'misc.custom_text_field'=>array('label'=>true,
																		 'type'=>false,
																		 'options'=>false,	   
																		 'hide_empty'=>false,
																		 'append_field_type'=>false,
																		 'form_fields' => array(
																				'cpvg_custom_text'=>array('type'=>'input','label'=>'Text'),
																				'cpvg_custom_text_bold'=>array('type'=>'checkbox','label'=>'Bold','value'=>'b'),
																				'cpvg_custom_text_italic'=>array('type'=>'checkbox','label'=>'Italic','value'=>'em'), 
																				'cpvg_custom_text_strikethrough'=>array('type'=>'checkbox','label'=>'Strikethrough','value'=>'del'),
																				'cpvg_custom_text_superscript'=>array('type'=>'checkbox','label'=>'Superscript','value'=>'sup'),
																				'cpvg_custom_text_subscript'=>array('type'=>'checkbox','label'=>'Subscript','value'=>'sub'),
																				'line_change'=>array(),
																				'cpvg_custom_text_element'=>array('type'=>'select','label'=>'Element Type','options'=>array('span'=>'Span','div'=>'Div','p'=>'Paragraph')),
																				'cpvg_custom_text_element_id'=>array('type'=>'input','label'=>'Element ID'),
																				'cpvg_custom_text_element_class'=>array('type'=>'input','label'=>'Element Class'),
																 )),																	 
										'misc.custom_text'=>array('label'=>false,
																 'type'=>false,
																 'options'=>false,	   
																 'hide_empty'=>false,
																 'append_field_type'=>false,
																 'form_fields' => array(
																		'cpvg_custom_text'=>array('type'=>'input','label'=>'Text'),
																		'cpvg_custom_text_bold'=>array('type'=>'checkbox','label'=>'Bold','value'=>'b'),
																		'cpvg_custom_text_italic'=>array('type'=>'checkbox','label'=>'Italic','value'=>'em'), 
																		'cpvg_custom_text_strikethrough'=>array('type'=>'checkbox','label'=>'Strikethrough','value'=>'del'),
																		'cpvg_custom_text_superscript'=>array('type'=>'checkbox','label'=>'Superscript','value'=>'sup'),
																		'cpvg_custom_text_subscript'=>array('type'=>'checkbox','label'=>'Subscript','value'=>'sub'),
																		'line_change'=>array(),
																		'cpvg_custom_text_element'=>array('type'=>'select','label'=>'Element Type','options'=>array('span'=>'Span','div'=>'Div','p'=>'Paragraph')),
																		'cpvg_custom_text_element_id'=>array('type'=>'input','label'=>'Element ID'),
																		'cpvg_custom_text_element_class'=>array('type'=>'input','label'=>'Element Class'),
																 )),															 													 
									   'misc.heading'=>array('label'=>false,
															 'type'=>false,
															 'options'=>false,		
															 'hide_empty'=>false,														 
															 'form_fields' => array(
																'cpvg_heading_text'=>array('type'=>'input','label'=>'Heading Text'),
																'cpvg_heading_type'=>array('type'=>'select','label'=>'Heading Type','options'=>$heading_options)
															  )),									  
									   'misc.hr'=>array('label'=>false,
														'type'=>false,
														'options'=>false,
														'hide_empty'=>false,
														'form_fields' => array())				  
									   );

		return array('misc' => array('custom_html_field'=>'Custom Html Field',
									 'custom_html'=>'Custom Html',
									 'custom_text_field'=>'Custom Text Field',
									 'custom_text'=>'Custom Text',
									 'heading'=>'Heading',
									 'hr'=>'Horizontal Line'), 
									 
					 'cvpg_datafield_extra_data'=>array($extra_options_settings));
    }

	public function getValue($field_name,$post_data,$extra_options) {
		if($field_name == "custom_html_field" || $field_name == "custom_html"){
			return $this->processCustomHtml($field_name,$post_data,$extra_options);
		}else if($field_name == "custom_text_field" || $field_name == "custom_text"){
			return $this->processCustomText($field_name,$post_data,$extra_options);	
		}else if($field_name == "heading"){
			return "<".$extra_options["cpvg_heading_type"]." class='cpvg-heading'>".strip_tags($extra_options["cpvg_heading_text"])."</".$extra_options["cpvg_heading_type"].">";
		}else if($field_name == "hr"){
			return "<hr class='cpvg-horizontal-line'/>";
		}
    }

    public function processCustomText($field_name,$post_data,$extra_options) {
		$output = strip_tags($extra_options['cpvg_custom_text']);
		$element_param = "";
		
		foreach($extra_options['checkboxes'] as $format_option){
			$output="<".$format_option.">".$output."</".$format_option.">";			
		}
		
		$extra_options["cpvg_custom_text_element_id"] = trim($extra_options["cpvg_custom_text_element_id"]);
		$extra_options["cpvg_custom_text_element_class"] = trim($extra_options["cpvg_custom_text_element_class"]);
		
		if(!empty($extra_options["cpvg_custom_text_element_id"])){
			$element_param.=" id='".$extra_options["cpvg_custom_text_element_id"]."'";
		}
		if(!empty($extra_options["cpvg_custom_text_element_class"])){
			$element_param.=" class='".$extra_options["cpvg_custom_text_element_class"]."'";
		}
		
		return "<".$extra_options["cpvg_custom_text_element"].$element_param.">".$output."</".$extra_options["cpvg_custom_text_element"].">";
	}
	    
    public function processCustomHtml($field_name,$post_data,$extra_options) {
		$extra_options = $extra_options["cpvg_custom_html_area"];
		$output = $extra_options;
		
		$data = array();
		$data['post_data'] = $post_data;
		$data['post_type'] = $post_data->post_type;
		$data['field_data'] = get_post_custom($post_data->ID);
		$data['labels'] = array();
		$data['template_file'] = "";
		$data['fields'] = "";

		//Saves a instances of earch datafield class that will be user later
		$df_files = cpvg_get_extensions_files('php',CPVG_DATAFIELDS_DIR);
		$class_instaces = array();
		foreach($df_files as $df_file => $df_file_name){
			require_once CPVG_DATAFIELDS_DIR."/".$df_file .".php";
			$class = new $df_file();

			foreach($class->adminProperties() as $supported_section=>$supported_fields){
				$class_instaces[$supported_section] = $class;
			}
		}

		preg_match_all("/\[\[(.*?)\]\]/", $extra_options, $matches);

		foreach($matches[1] as $key => $value){
			$data['fields'] = array();

			$value_data = explode(";",$value);

			$section_name = explode(".",array_shift($value_data));
			if(count($section_name) == 2){
				$data['fields'][0]['section'] = $section_name[0];
				$data['fields'][0]['name'] = $section_name[1];
				$data['datafield_objects'][$section_name[0]] = $class_instaces[$section_name[0]];
			}
			$data['fields'][0]['label'] = '';
			$data['fields'][0]['type'] = array_shift($value_data);

			foreach($value_data as $index=>$value){
				$data['fields'][0]['options'.($index+1)] = $value;
			}

			//Loads data from custom post type plugins
			$pluginfiles = cpvg_get_pluginscode_files();
			foreach($pluginfiles as $pluginfile_name){
				include_once CPVG_PLUGINSCODE_DIR."/".$pluginfile_name.".php";

				$pluginfile_object = new $pluginfile_name();
				if ($pluginfile_object->isEnabled()) {
					$data = $pluginfile_object->processPageAdditionalCode($data['post_type'],$data);
					$labels = $pluginfile_object->getCustomfields($data['post_type']);
					if(!is_null($labels)){
						$data['labels'] = $labels;
					}
				}
			}
			$processed_data = cpvg_process_data($data,true,true);
			$output = str_replace($matches[0][$key],$processed_data[0]['value'],$output);
		}
		return $output;		
	}
}
?>
