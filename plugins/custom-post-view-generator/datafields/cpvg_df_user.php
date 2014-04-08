<?php
class cpvg_df_user{
	public function adminProperties() {
		/* Available fields:
		'ID', 'user_login', 'user_pass', 'user_nicename', 'user_email',
		'user_url', 'user_registered', 'user_activation_key', 'user_status', 'display_name'
		*/
		return array('user' => array('ID'=>'ID', 'user_login'=>'User login' , 'user_email'=>'Email',
									 'user_status'=>'User status' , 'display_name'=>'Display name' ,
									 'user_url'=>'User url','user_nicename'=>'Nicename'));
    }

	public function getValue($field_name,$post_data,$custom_text) {
		$user_data = get_user_by('id',$post_data->post_author);
		return $user_data->$field_name;
    }
}
?>