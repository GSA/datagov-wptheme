<?php
class cpvg_wp_user_id{

    public function adminProperties() {
		$output_options1 = array('ID'=>'Show Id',
								 'user_login'=>'Show user login',
								 'user_email'=>'Show email',
								 'display_name'=>'Show display Name',
								 'user_nicename'=>'Show nicename');

		$output_options2 = array('no_modification'=>'No modification',
								 'hyperlink_wp'=>'Set as hyperlink to user posts',
								 'hyperlink_website'=>'Set as hyperlink to user website',
								 'hyperlink_email'=>'Set as hyperlink to user email');

		$output_options3 = array('no_modification'=>'No modification',
								 'append_email'=>'Append user email',
								 'append_email_hyperlink'=>'Append user email with hyperlink');

		return array('cpvg_wp_user_id' => array('label'=>'Wordpress User ID',
												'options' => array($output_options1,$output_options2,$output_options3)));

    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			//show something in the preview
			return rand(1,99);
		}else{
			$user_data = get_user_by('id',$value);
			$return_value = $user_data->$output_options[1];

			switch($output_options[2]){
				case 'hyperlink_wp': $return_value = "<a href='".get_author_posts_url($user_data->ID)."'>".$return_value."</a>"; break;
				case 'hyperlink_website': $return_value = "<a href='".$user_data->user_url."'>".$return_value."</a>"; break;
				case 'hyperlink_email': $return_value = "<a href='mailto:".$user_data->user_email."'>".$return_value."</a>"; break;
			}

			switch($output_options[3]){
				case 'append_email': $return_value.=" (".$user_data->user_email.")"; break;
				case 'append_email_hyperlink': $return_value.=" (<a href='mailto:".$user_data->user_email."'>".$user_data->user_email."</a>)"; break;
			}

			return $return_value;
		}
	}
}
?>