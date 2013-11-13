<?php
class cpvg_df_post{
	public function adminProperties() {
		/*  Available fields:
		'ID','post_author','post_date','post_date_gmt','post_content','post_title','post_excerpt',
		'post_status','comment_status','ping_status','post_password','post_name','to_ping','pinged',
		'post_modified','post_modified_gmt','post_content_filtered','guid','menu_order',
		'post_type','post_mime_type','comment_count','ancestors','filter'
		*/
		$fields = array('post' => array('ID'=>'ID', 'post_title'=>'Title', 'post_name'=>'Name', 'post_author'=>'Author Id',
									 'post_status'=>'Status', 'post_excerpt'=>'Post Excerpt', 'post_content'=>'Content',
									 'post_date'=>'Creation Date', 'post_modified'=>'Last modified', 'comment_status'=>'Comment Status',
									 'post_type'=>'Type', 'guid'=>'Post Url' , 'comment_count'=>'Comment count','_thumbnail_id'=>'Thumbnail ID'));
		return $fields;
    }

	public function getValue($field_name,$post_data,$custom_text) {
		return $post_data->$field_name;
    }
}
?>
