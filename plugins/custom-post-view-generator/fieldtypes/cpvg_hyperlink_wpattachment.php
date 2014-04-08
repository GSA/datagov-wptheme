<?php
class cpvg_hyperlink_wpattachment{

    public function adminProperties() {
		$output_options1 = array('id'=>'Show Id',
								 'url'=>'Show Url',
								 'filepath'=>'Show File Path',
								 'filename'=>'Show Filname');

		return array('cpvg_hyperlink_wpattachment' => array('label'=>'Hiperlink (WP Attachment)',
											  'options' => array($output_options1)));

    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if($value=='NOT_SET'){
			//show something in the preview
			return "<a href='http://download.mozilla.org/?product=firefox-5.0&os=win&lang=en-US'>Firefox Installation Package</a>";
		}

		switch ($output_options[1]){
			case 'id': return "<a href='".wp_get_attachment_url($value)."'>$value</a>";
			case 'url': return "<a href='".wp_get_attachment_url($value)."'>".wp_get_attachment_url($value)."</a>";
			case 'filepath':
				$file_data = wp_get_attachment_metadata($value);
				return "<a href='".wp_get_attachment_url($value)."'>".$file_data['file']."</a>";
			case 'filename': return "<a href='".wp_get_attachment_url($value)."'>".basename(wp_get_attachment_url($value))."</a>";
		}

	}
}
?>