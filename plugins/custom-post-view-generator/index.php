<?php
/*
Plugin Name: Custom Post Type View Generator
Plugin URI:
Description:
Version: 0.4.5
Author: Marco Constâncio
Author URI: http://www.betasix.net
*/
if (!defined('WP_PLUGIN_DIR'))
	define('WP_PLUGIN_DIR','/');

if (!defined('CPVG_PLUGIN_NAME'))
    define('CPVG_PLUGIN_NAME', 'custom-post-view-generator');

if (!defined('CPVG_PLUGIN_DIR'))
    define('CPVG_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . CPVG_PLUGIN_NAME);

if (!defined('CPVG_PLUGIN_URL'))
    define('CPVG_PLUGIN_URL', WP_PLUGIN_URL . '/' . CPVG_PLUGIN_NAME . '/');

if (!defined('CPVG_POST_TEMPLATE_DIR'))
    define('CPVG_POST_TEMPLATE_DIR', CPVG_PLUGIN_DIR. '/templates/post');

if (!defined('CPVG_LIST_TEMPLATE_DIR'))
    define('CPVG_LIST_TEMPLATE_DIR', CPVG_PLUGIN_DIR. '/templates/list');

if (!defined('CPVG_ADMIN_TEMPLATE_DIR'))
    define('CPVG_ADMIN_TEMPLATE_DIR', CPVG_PLUGIN_DIR. '/templates/admin');

if (!defined('CPVG_FIELDTYPES_DIR'))
    define('CPVG_FIELDTYPES_DIR', CPVG_PLUGIN_DIR. '/fieldtypes');

if (!defined('CPVG_PLUGINSCODE_DIR'))
    define('CPVG_PLUGINSCODE_DIR', CPVG_PLUGIN_DIR. '/pluginscode');

if (!defined('CPVG_DATAFIELDS_DIR'))
    define('CPVG_DATAFIELDS_DIR', CPVG_PLUGIN_DIR. '/datafields');

if (!defined('CPVG_PARAMETER_DIR'))
    define('CPVG_PARAMETER_DIR', CPVG_PLUGIN_DIR. '/parameters');

if (!defined('CPVG_POST_TEMPLATE_URL'))
    define('CPVG_POST_TEMPLATE_URL', WP_PLUGIN_URL . '/' . CPVG_PLUGIN_NAME . '/templates/post');

register_activation_hook(CPVG_PLUGIN_DIR.'/index.php','cpvg_activation');
register_deactivation_hook(CPVG_PLUGIN_DIR.'/index.php','cpvg_deactivation');

//require by some functions to be loaded here
cpvg_load_fieldtypes();

//FIX FOR WORDPRESS 3.4
add_action('wp_head', 'cpvg_load_scripts'); // to load jquery on the font page
add_action('admin_head', 'cpvg_load_admin_scripts'); // to load jquery on the admin page

function cpvg_load_scripts(){
	wp_enqueue_script('jquery');
}

function cpvg_load_admin_scripts(){
	//load_scripts();
	cpvg_load_scripts();
	wp_enqueue_script('jquery-ui');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-dialog');
}

if (is_admin()){
	//ADMIN LINKS
	add_action('admin_menu', 'cpvg_menu_pages');


    function cpvg_admin_scripts()
    {
        //JS
        wp_register_script('cpvg_functions', CPVG_PLUGIN_URL . 'cpvg_functions.min.js', false, null);
        wp_register_script('cpvg_flowplayer', CPVG_PLUGIN_URL . 'libs/flowplayer/flowplayer-3.2.6.min.js', false, null);
        wp_register_script('cpvg_jquery_tmpl', CPVG_PLUGIN_URL . 'libs/knockoutjs/jquery-tmpl.min.js', false, null);
        wp_register_script('cpvg_knockout', CPVG_PLUGIN_URL . 'libs/knockoutjs/knockout.min.js', false, null);

        wp_enqueue_script(array('jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable',
            'jquery-ui-dialog',
            'cpvg_flowplayer',
            'cpvg_jquery_tmpl', 'cpvg_knockout', 'cpvg_pagination', 'cpvg_functions'));

        //Necessary for Meta Boxes in List Views
        wp_enqueue_script(array('common', 'wp-lists', 'postbox'));
    }

    add_action('wp_enqueue_scripts', 'cpvg_admin_scripts');

    //Action for ajax calls
    add_action('wp_ajax_generate_preview', 'cpvg_generate_preview');
    add_action('wp_ajax_save_layout', 'cpvg_save_layout');
	add_action('wp_ajax_delete_layout', 'cpvg_delete_layout');
	add_action('wp_ajax_get_post_view_data', 'cpvg_get_post_view_data');
	add_action('wp_ajax_get_list_view_data', 'cpvg_get_list_view_data');
	add_action('wp_ajax_create_postpage', 'cpvg_create_post_page');

	//LOAD CSS
    add_action('admin_enqueue_scripts', 'cpvg_load_css');
}else{
    function cpvg_scripts()
    {
        wp_register_script('cpvg_flowplayer', CPVG_PLUGIN_URL . '/libs/flowplayer/flowplayer-3.2.6.min.js', false, null);
        wp_register_script('cpvg_pagination', CPVG_PLUGIN_URL . 'libs/smartpaginator/smartpaginator.min.js', false, null);
        wp_register_script('cpvg_tablesorter', CPVG_PLUGIN_URL . 'libs/tablesorter/jquery-tablesorter-min.js', array('jquery'), null);

        wp_enqueue_script(array('cpvg_flowplayer', 'cpvg_pagination', 'cpvg_tablesorter'));
    }

    add_action('wp_enqueue_scripts', 'cpvg_scripts');

    //USED IN POST VIEWS
    add_filter('the_content', 'cpvg_process_page', -999);

    //IF THE YOU ARE GETTING GARBAGE ON POST LISTING UNCOMMENT THE NEXT LINE
	//add_filter('the_excerpt', 'cpvg_process_excerpt',-999);

	//USED IN LIST VIEWS
	add_shortcode('cpvg_list', 'cpvg_process_list');
}

/*
 * Create tables, deletes current tables to prevent version incompatibility 
 */ 
function cpvg_activation(){
	cpvg_deactivation();
    /**
     * @var wpdb $wpdb
     */
    global $wpdb, $table_prefix;

    $wp_cpvg_table = $table_prefix . "cpvg_post_views";
    if($wpdb->get_var("show tables like '$wp_cpvg_table'") != $wp_cpvg_table) {
		$sql0  = "CREATE TABLE `". $wp_cpvg_table . "` ( ";
		$sql0 .= "  `id`       					int(11)      NOT NULL auto_increment,";
		$sql0 .= "  `name` 	varchar(255) NOT NULL default '', ";
		$sql0 .= "  `options`  text         NOT NULL default '', ";
		$sql0 .= "  UNIQUE KEY `id` (`id`) ";
		$sql0 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";

		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql0);
	}
	
	$wp_cpvg_table = $table_prefix."cpvg_list_views";
	if($wpdb->get_var("show tables like '$wp_cpvg_table'") != $wp_cpvg_table) {
		$sql0  = "CREATE TABLE `". $wp_cpvg_table . "` ( ";
		$sql0 .= "  `id`       					int(11)      NOT NULL auto_increment,";
		$sql0 .= "  `name` 	varchar(255) NOT NULL default '', ";
		$sql0 .= "  `options`  text         NOT NULL default '', ";
		$sql0 .= "  UNIQUE KEY `id` (`id`) ";
		$sql0 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";

		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql0);
	}		
}

/*
 * Delete tables 
 */ 
function cpvg_deactivation(){
	global $wpdb, $table_prefix;
	$wp_cpvg_table = $table_prefix."cpvg_post_views";
	$wpdb->query("DROP TABLE ".$wp_cpvg_table);
	$wp_cpvg_table = $table_prefix."cpvg_list_views";
	$wpdb->query("DROP TABLE ".$wp_cpvg_table);
}

/*
 * This method id required because the wp_register_style cause a RTL bug
 */ 
function cpvg_load_css(){
	wp_register_style('cpvg_style', CPVG_PLUGIN_URL . 'cpvg_style.css');
	wp_enqueue_style(array('wp-jquery-ui-dialog','cpvg_style'));
} 

/*
* Unless the post excerpt was defined by the user,
* this function will return an part of content field to prevent
* from garbage being displayed because of the the_content filter
*/
function cpvg_process_excerpt($content = '')
{
    global $post;
    $output = $post->post_excerpt;
	if($output != ""){
		return $output;
	}else{
		return substr(strip_tags($post->post_content), 0, 200);
	}
}

//Create links in the workpress admin
function cpvg_menu_pages() {
    add_menu_page('CPT View Generator','CPT View Generator', 'manage_options','cpvg_topmenu','cpvg_post_views');
    add_submenu_page('cpvg_topmenu','Post Views','Post Views','manage_options','cpvg_topmenu','cpvg_post_views');
    add_submenu_page('cpvg_topmenu','List Views','List Views','manage_options','cpvg_list_views','cpvg_list_views');
    add_submenu_page('cpvg_topmenu','Help','Help','manage_options','cpvg_help1','cpvg_help');
}

/***************************************************** ADMIN WINDOWS ******************************************************/

//Generate the list view admin window
function cpvg_list_views() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

	require_once CPVG_ADMIN_TEMPLATE_DIR . "/cpvg_list_views.html";

	$meta_boxes_data = array('list_views'=>'List Views','fields'=>'Fields','parameters'=>'Parameters','finish'=>'Finish');
	$post_types=array_diff_assoc(get_post_types(array('_builtin'=>false),'names'),array('content-type'=>'content-type','rw_content_type'=>'rw_content_type','rw_taxonomy'=>'rw_taxonomy'));


	foreach($meta_boxes_data as $meta_boxes_id=>$meta_boxes_name){
		add_meta_box('cpvg-step-'.$meta_boxes_id,$meta_boxes_name,'cvpg_listview_metabox','cpvg-'.$meta_boxes_id,'normal','default');
	}
	?>

	<div id='cpvg-wrap' class='wrap cpvg-list-views metabox-holder'>
		<div id='icon-edit-pages' class='icon32'><br></div><h2>List Views</h2>
		<?php
			foreach($meta_boxes_data as $meta_boxes_id=>$meta_boxes_name){
				do_meta_boxes('cpvg-'.$meta_boxes_id,'normal', array("metabox"=>$meta_boxes_id,"post_type"=>$post_types));
			}
		?>
	</div>
	<?php
}

//Generate the list view admin metaboxes that compose the list view admin window
function cvpg_listview_metabox($data){
	require_once CPVG_ADMIN_TEMPLATE_DIR . "/cpvg_fieldtypes_form.html";
	require_once CPVG_ADMIN_TEMPLATE_DIR . "/cpvg_list_views.html";

    global $wpdb;
    $db_data = cpvg_get_dbfields_names("list");

	switch($data["metabox"]){
		case 'list_views':
			$template_files = cpvg_capitalize_array_values(cpvg_get_extensions_files("php",CPVG_LIST_TEMPLATE_DIR));
			$rows_data = $wpdb->get_results("SELECT ".$db_data['id_field']." ,".$db_data['name_field']." FROM ".$db_data['table_name']);

			$list_views = array();
			foreach($rows_data as $field_value){
				$list_views[] = $field_value->$db_data['name_field'];
			}
			?>
				<script type='text/javascript'>
					
					viewModel.setData('siteurl','<?php echo home_url(""); ?>');
					viewModel.setData('view_type','list');
					viewModel.setData('available_template_files',<?php echo json_encode($template_files); ?>,'assocArray');
					viewModel.setData('available_list_views',<?php echo json_encode($list_views); ?>,'arrayObservables');
		
				</script>
			<?php

		break;
		case 'fields':
			if(!isset($data["post_types"])){
				$data["post_types"] = array();
			}
			cpvg_fieldtypes_form($data["post_types"],"list");
		break;
	}
	echo "<div data-bind=\"template: { name:'cpvg_".$data["metabox"]."' }\"></div>";
}

//Generates a post views admin window
function cpvg_post_views() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

	$post_types=array_diff_assoc(get_post_types(array('_builtin'=>false),'names'),array('content-type'=>'content-type','rw_content_type'=>'rw_content_type','rw_taxonomy'=>'rw_taxonomy'));
	?>
		<div id='cpvg-wrap' class='wrap cpvg-post-views'>
			<div id='icon-edit-pages' class='icon32'><br></div><h2>Post Views</h2>
			<?php
				cpvg_fieldtypes_form($post_types,'post');
				echo "<div data-bind=\"template:'cpvg_fieldtypes_form'\"></div>";
			?>
		</div>
	<?php
}

//Generate fieldtypes form used in the post and list views admin pages
function cpvg_fieldtypes_form($post_types,$view_type='post'){
	require_once CPVG_ADMIN_TEMPLATE_DIR . "/cpvg_fieldtypes_form.html";
	require_once WP_PLUGIN_DIR."/../../wp-includes/link-template.php";

?>
<script type='text/javascript'>
	<?php
		//List of Template Files
		$template_files = cpvg_capitalize_array_values(cpvg_get_extensions_files("php",CPVG_POST_TEMPLATE_DIR));

		$object_types = array_diff_assoc(get_post_types(array('_builtin'=>false)),array('content-type'=>'content-type','rw_content_type'=>'rw_content_type','rw_taxonomy'=>'rw_taxonomy'));
		$objects_data = array();
		//POST VIEWS
		foreach ($object_types  as $post_type) {
			$custom_fields_data = cpvg_get_customfields($post_type);
			if(!empty($custom_fields_data)){
				$object_data = array();
				foreach($custom_fields_data  as $field_id=>$field_name){
					if(!empty($field_name)){
						$object_data[] = $field_name;
					}
				}
			}
			if(post_type_supports($post_type,'editor')){
				$object_data[] = 'Content Editor';
			}
			$objects_data[$post_type] = $object_data;
		}
		//List of Custom Post Types
		$object_types = cpvg_capitalize_array_values($object_types);
	?>

	//MANDATORY
	<?php
		if($view_type == "post"){
			echo "viewModel.setData('view_type','".$view_type."');\n";
			echo "viewModel.setData('siteurl','".home_url("")."');\n";
			echo "viewModel.setData('available_template_files',".json_encode($template_files).",'assocArray');\n";
		}
		if(in_array("acf",array_keys($object_types))){
			echo "viewModel.setData('acf_enabled','true');\n";
		}else{
			echo "viewModel.setData('acf_enabled','false');\n";
		}

		$js_posttypes = array_merge($object_types,array('post'=>'Post','page'=>'Page'));
		$js_posttypes = array_diff_assoc($js_posttypes, array('acf'=>'Acf'));

		echo "viewModel.setData('available_post_types',".json_encode($js_posttypes).",'assocArray');\n";
		echo "viewModel.setData('available_custom_fields',".json_encode(array_merge($objects_data,array('field_sections'=>array_keys($objects_data)))).",'json');\n";
		echo "viewModel.setAvailableFieldTypes(".cpvg_load_fieldtypes(true).");\n";

		//DATAFIELDS
		$datafields_files = cpvg_get_extensions_files("php",CPVG_DATAFIELDS_DIR);
		$objects_data = array();

		foreach($datafields_files as $datafield_file => $datafield_name){
			require_once CPVG_DATAFIELDS_DIR."/".$datafield_file.".php";
			$datafield_object = new $datafield_file();

			$objects_data = array_merge_recursive($objects_data,$datafield_object->adminProperties());
			$object_types[str_replace("cpvg_","",$datafield_file)] = ucwords(str_replace("cpvg_","",$datafield_file));
		}

		if($view_type == "list"){
			$parameters_files = cpvg_get_extensions_files("php",CPVG_PARAMETER_DIR);

			foreach(array('filter','order','pagination','usersorting') as $param_name){
				$filter_data = array();
				foreach($parameters_files as $parameters_file => $parameters_name){
					require_once CPVG_PARAMETER_DIR."/".$parameters_file.".php";
					$parameter_object = new $parameters_file();
					$filter_data = array_merge_recursive($filter_data,$parameter_object->getParameterData($param_name));
				}
				echo "viewModel.parseParamConfig('".$param_name."',".json_encode($filter_data).");\n";
			}
		}

		$cvpg_datafield_settings = $objects_data['cvpg_datafield_extra_data'][0];		
		echo "viewModel.setData('available_extra_data',".json_encode($cvpg_datafield_settings).",'');\n";

		$objects_data = array_diff_assoc($objects_data, array('cvpg_datafield_extra_data'=>$cvpg_datafield_settings));
		echo "viewModel.setData('available_fields',".json_encode(array_merge($objects_data,array('field_sections'=>array_keys($objects_data)))).",'json');\n";
	?>
</script>
<?php

}

/************************************************* LOAD FILES/CLASSES ************************************************************************/

//Load the plugincode files - files necessary to extract the fields created by a custom post type field
function cpvg_get_pluginscode_files(){
	$find_strings = array();
	$replace_strings = array();
	$files = array();

	if ($handle = opendir(CPVG_PLUGINSCODE_DIR)) {
		while (false !== ($file = readdir($handle))) {
			$explode_result = explode(".", $file);
			if(end($explode_result) == "php") {
				$files[] = preg_replace("/\\.[^.\\s]{3,4}$/", "", str_replace($find_strings,$replace_strings,$file));
			}
		}
		closedir($handle);
	}

	return $files;
}

//Load the fieldtypes files
function cpvg_load_fieldtypes($output_json=false){
	$types_options = array();
	$find_strings = array();
	$replace_strings = array();
	$files = array();
	if ($handle = opendir(CPVG_FIELDTYPES_DIR)) {
		while (false !== ($file = readdir($handle))) {
			if(end(explode(".", $file)) == "php") {
				$files[] = preg_replace("/\\.[^.\\s]{3,4}$/", "", str_replace($find_strings,$replace_strings,$file));
			}
		}
		closedir($handle);
	}

	asort($files);

	foreach($files as $fieldtype_name){
		include_once CPVG_FIELDTYPES_DIR."/".$fieldtype_name.".php";
		$fieldtype_object = new $fieldtype_name();
		$types_options = array_merge($types_options,$fieldtype_object->adminProperties());
	}

	if($output_json){
		if (version_compare(PHP_VERSION, '5.3.0', '<')) {
			$types_options = json_encode($types_options);
		}else{
			$types_options = json_encode($types_options,JSON_HEX_TAG);
		}
	}

	return $types_options;
}

//Load the extension files - templates, datafields
function cpvg_get_extensions_files($file_type="php",$dir=CPVG_POST_TEMPLATE_DIR){
	$files = array();
	$find_strings = array("-","_","cpvg");
	$replace_strings = array(" "," "," ");

	if ($handle = opendir($dir)) {
		while (false !== ($file = readdir($handle))) {
			if(end(explode(".", $file)) == $file_type) {
				$files[preg_replace("/\\.[^.\\s]{3,4}$/", "", $file)] = trim(preg_replace("/\\.[^.\\s]{3,4}$/", "", str_replace($find_strings,$replace_strings,$file)));
			}
		}
		closedir($handle);
	}
	asort($files);

	return $files;
}

/*********************************** DATA GETTERS *************************************************************/

//Retrieves all the existing custom post fields using the correspodent pluginscode file
function cpvg_get_customfields($custom_post_type){
	$pluginfiles = cpvg_get_pluginscode_files();

	foreach($pluginfiles as $pluginfile_name){
		include_once CPVG_PLUGINSCODE_DIR."/".$pluginfile_name.".php";

		$pluginfile_object = new $pluginfile_name();
		if ($pluginfile_object->isEnabled()){
			return $pluginfile_object->getCustomfields($custom_post_type);
		}
	}

	return array();
}

/*********************************** HTML/DATA PROCESSING ***********************************/

//Builds the list view html from the data stored in the db.
//Uses the function that is used to process a post view: the cpvg_process_page function
function cpvg_process_list($params){
	global $table_prefix,$wpdb;

	$fields = array();
	$html = "";

	$db_data = cpvg_get_dbfields_names("list");
	$list_data = $wpdb->get_var("SELECT ".$db_data['options_field']." FROM ".$db_data['table_name']." WHERE ".$db_data['name_field']." = '".$params['name']."'");

	if($list_data){
		$list_data = json_decode($list_data,true);
		$list_data['datafield_objects'] = array();

		//Arranges the necessary data to be used in the cpvg_process_page function
		foreach($list_data['fields'] as $index=>$value){
			$section_name = explode(".",$value['name']);
			$list_data['fields'][$index]['section'] = $section_name[0];
			$list_data['fields'][$index]['name'] = $section_name[1];
		}

		//Saves a instances of earch datafield class that will be user later
		$datafields_files = cpvg_get_extensions_files("php",CPVG_DATAFIELDS_DIR);
		$objects_data = array();
		foreach($datafields_files as $datafield_file => $datafield_name){
			require_once CPVG_DATAFIELDS_DIR."/".$datafield_file.".php";
			$datafield_object = new $datafield_file();

			foreach($datafield_object->adminProperties() as $section_name => $section_data){
				$list_data['datafield_objects'][$section_name] = $datafield_object;
			}
		}

		//Gets alls the parameters for the WP_Query
		$parameters_files = cpvg_get_extensions_files("php",CPVG_PARAMETER_DIR);
		$parameter_data = array();
		
		
		foreach($parameters_files as $parameters_file => $parameters_name){
			require_once CPVG_PARAMETER_DIR."/".$parameters_file.".php";
			$parameter_object = new $parameters_file();

			foreach($parameter_object->getParameterData() as $section_name => $param_data){
				$parameter_data[$section_name] = $parameter_object;
			}
		}
		//Merge all the parameters with the $query_args var
		$query_args = array('post_type'=>$list_data['post_type']);
		if(isset($list_data['param'])){
			foreach($list_data['param'] as $param_type => $param_records){
				foreach($param_records as $param_record_index => $param_record_data){
					$query_args = array_merge_recursive($parameter_data[$param_record_data['section']]->applyParameterData($param_type,$param_record_data),$query_args);
				}
			}
		}
	
		if(isset($query_args['posts_per_page'])){
			$query_args = array_diff_assoc($query_args,array('posts_per_page'=>$query_args['posts_per_page']));
		}
		if(isset($query_args['usersorting_choice'])){
			$query_args = array_diff_assoc($query_args,array('usersorting_choice'=>$query_args['usersorting_choice']));
		}		

		//Sets a custom filter required by the custom_date parameter if necssary
		if(isset($query_args['custom_date'])){
			$custom_date_param = $query_args['custom_date'];
			$query_args = array_diff_assoc($query_args,array('custom_date'=>$custom_date_param));
			$custom_date_function = create_function('$where', '$where.="'.$custom_date_param.'"; return $where;');
		}

		if(isset($custom_date_function)){
			add_filter('posts_where', $custom_date_function);
		}

		if(isset($query_args['author'])){
			$query_args['author'] = implode(",",$query_args['author']);
		}
		
		$query_args['posts_per_page'] = 9999;
		$query_args['usersorting_choice'] = 0;

		//Performs query
		$query_result = new WP_Query($query_args);

		//Removes a custom filter if the custom_date parameter was used
		if(isset($custom_date_function)){
			remove_filter('posts_where',$custom_date_function);
		}

		//Process all post data
		$posts_data = $query_result->posts;

		$records_data = array();
		foreach($posts_data as $post_data){
			$list_data['field_data'] = get_post_custom($post_data->ID);
			$list_data['post_data'] = $post_data;
			$list_data['labels'] = array();
			$pluginfiles = cpvg_get_pluginscode_files();

			foreach($pluginfiles as $pluginfile_name){
				include_once CPVG_PLUGINSCODE_DIR."/".$pluginfile_name.".php";

				$pluginfile_object = new $pluginfile_name();
				if ($pluginfile_object->isEnabled()) {
					$list_data = $pluginfile_object->processPageAdditionalCode($post_data->post_type,$list_data);
					$labels = $pluginfile_object->getCustomfields($post_data->post_type);
					if(!is_null($labels)){
						$list_data['labels'] = $labels;
					}
				}
			}
			$records_data[] = cpvg_process_data($list_data,true);
		}

		//Apply theme
		ob_start();
		if(file_exists(CPVG_LIST_TEMPLATE_DIR.'/'.$list_data['template_file'].".php")){
			require CPVG_LIST_TEMPLATE_DIR.'/'.$list_data['template_file'].".php";
		}else{
			//DISPLAYS DATA EVEN IF NO TEMPLATE WAS SELECTED
			foreach($records_data as $record_data){
				foreach($record_data as $record){
					echo "<b>".$record['label']."</b>: ".$record['value'];
				}
			}
		}
		$html.= ob_get_contents();
		ob_end_clean();
	}else{
		$html.= "No list view with the name '".$params['name']."' was found.";
	}

	return $html;
}

//Processes a post view. It is also called by the cpvg_process_list
//to process each post of the list
function cpvg_process_page(){
    global $wpdb, $post;

    $db_data = cpvg_get_dbfields_names("post");
	$custom_post_type_options = $wpdb->get_var("SELECT ".$db_data['options_field']."
											    FROM ".$db_data['table_name']."
											    WHERE ".$db_data['name_field']." = '".$post->post_type."'");

	if(empty($custom_post_type_options)){
		$content = get_the_content('Read more');
		return $content;
	}else{
		$data = json_decode($custom_post_type_options,true);
		$data['field_data'] = get_post_custom($post->ID);
		$data['post_data'] = $post;
		$data['labels'] = array();

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

		//Add the datafields data to the data array
		foreach($data['fields'] as $index=>$value){
			$section_name = explode(".",$value['name']);



			if(count($section_name) == 2){
				$data['fields'][$index]['section'] = $section_name[0];
				$data['fields'][$index]['name'] = $section_name[1];
				if(isset($class_instaces[$section_name[0]])){
					$data['datafield_objects'][$section_name[0]] = $class_instaces[$section_name[0]];
				}else{
					$data['datafield_objects'][$section_name[0]] = null;
				}
			}
		}

		//Loads data from custom post type plugins
		$pluginfiles = cpvg_get_pluginscode_files();
		foreach($pluginfiles as $pluginfile_name){
			include_once CPVG_PLUGINSCODE_DIR."/".$pluginfile_name.".php";

			$pluginfile_object = new $pluginfile_name();
			if ($pluginfile_object->isEnabled()) {
				$data = $pluginfile_object->processPageAdditionalCode($post->post_type,$data);
				$labels = $pluginfile_object->getCustomfields($post->post_type);
				if(!is_null($labels)){
					$data['labels'] = $labels;
				}
			}
		}
		//Process the values
		return cpvg_process_data($data);
	}
}

//Process each value
function cpvg_process_data($data=null,$external_template_processing=false){
	if(isset($data['template_file']) && isset($data['fields'])){
		$template =  CPVG_POST_TEMPLATE_DIR.'/'.$data['template_file'].".php"; ;
		$fields = $data['fields'];
	}else{
		//Admin Post View Window Preview 
		$template = CPVG_POST_TEMPLATE_DIR.'/'.$_POST['template'].".php";
		$fields = $_POST['fields']; 
	}

	$record_data = array();
	if(!empty($fields)){
		foreach($fields as $field_data){

			$field = array();
			$additional_data = array();
			$output_options = array(); $output_options_temp = array();
			$field_content = "";

			if($field_data['name'] == "Content Editor"){
				$field_content = $data['post_data']->post_content;
			}

			foreach ($field_data as $key => $value) {
				if (strpos($key, 'options') === 0) {
					$output_options_temp[] = $value;
				}
			}
			foreach($output_options_temp as $index => $value){
				$output_options[$index+1] = $value;
			}

			if(isset($data['field_data'])){
				$field_key = array_search($field_data['name'],$data['labels']);
				$post_types = get_post_types(array('_builtin'=>false));

				if(isset($data['field_data']["_thumbnail_id"])){
					$data['post_data']->_thumbnail_id =  $data['field_data']["_thumbnail_id"][0];
				}	

				if(isset($data['field_data'][$field_key][0]) && ($data['field_data'][$field_key][0])){
					if(in_array("acf",array_keys($post_types))){ //CODE NECESSARY FOR THE ACF PLUGIN
						$field_content = $data['field_data'][$field_key][0];
					}else{
						if($field_data['section'] == $data['post_type']){
							$field_content = $data['field_data'][$field_key][0];
						}
					}
					//CUSTOM POST TYPE
				}else if(isset($data['datafield_objects'][$field_data['section']])){
					// OTHER SECTION: POST, USER, ETC.

					if(!isset($field_data['extra_options'])){
						$field_data['extra_options'] = null;
					}
					$field_content = $data['datafield_objects'][$field_data['section']]->getValue($field_data['name'],$data['post_data'],$field_data['extra_options']);
				}
			}else{
				$field_content = 'NOT_SET';
			}

			if(isset($field_data['additional_data'])){
				$additional_data = $field_data['additional_data'];
			}

			if(class_exists($field_data['type'])){
				$fieldtype_object = new $field_data['type'];
				$field['value'] = $fieldtype_object->processValue($field_content,$output_options,$additional_data);
			}else{
				if($field_content == 'NOT_SET'){
					$field['value'] = cpvg_random_text_value();
				}else{
					$field['value'] = $field_content;
				}
			}

			$field = array_merge($field,$field_data);
			if($field['hide_empty']!='true'){
				$record_data[]=$field;
			}else{
				 if (!empty($field['value'])){
					$record_data[]=$field;
				 }
			}
		}
	}

	if($external_template_processing){
		//LIST VIEWS
		return $record_data;
	}else{
		//POST VIEWS
		ob_start();
		if(file_exists($template)){
			require $template;
		}else{
			//DISPLAYS DATA EVEN IF NO TEMPLATE WAS SELECTED
			foreach($record_data as $record){
				echo "<b>".$record['name']."</b>: ".$record['value']."<br/>";
			}
		}
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}

//Print the data in the Admin Post View Preview
function cpvg_generate_preview(){
	print cpvg_process_data();
	exit;
}

/***************************************************** ADMIN ACTIONS *****************************************************/
//Saves/Update the layout for the list or post view
function cpvg_save_layout(){
	if(isset($_POST['view_type']) && isset($_POST['view_value'])){
		if(!empty($_POST['view_value'])){
			$layout_data = array();
			$layout_data['template_file'] = $_POST['template'];
			$layout_data['fields'] = $_POST['fields'];
			$layout_data['param'] = $_POST['param'];

			if(isset($_POST['post_type'])){
				$layout_data['post_type'] = $_POST['post_type'];
			}

			global $table_prefix,$wpdb;
			$db_data = cpvg_get_dbfields_names($_POST['view_type']);
			//check_database($db_data['table_name']);

			$cpvg_id = $wpdb->get_var("SELECT id FROM ".$db_data['table_name']." WHERE ".$db_data['access_field']." = '".$_POST['view_value']."'");
			$processed_data = str_replace(array("\\\'"),array("'"),json_encode($layout_data));
			
			if($cpvg_id){
				
				$rows_affected = $wpdb->update($db_data['table_name'],array($db_data['options_field'] => $processed_data),
																	  array('id'=>$cpvg_id));

				if(isset($_POST['new_view_value'])){
					$rows_affected = $wpdb->update($db_data['table_name'],array($db_data['access_field'] => $_POST['new_view_value']),
																		  array('id'=>$cpvg_id));
				}

				print "Layout Updated.";
			}else{
				if(isset($_POST['new_view_value'])){
					$_POST['view_value'] = $_POST['new_view_value'];
				}

				$rows_affected = $wpdb->insert($db_data['table_name'],array($db_data['name_field'] => $_POST['view_value'],
																			$db_data['options_field'] => $processed_data));
				print "Layout Saved.";
			}
		}else{
			//print "No Post Type Was Selected.";
		}
	}
	exit;
}

//Deletes a layput
function cpvg_delete_layout(){
	if(isset($_POST['view_value'])){
		if(!empty($_POST['view_value'])){
			global $table_prefix,$wpdb;

			$db_data = cpvg_get_dbfields_names($_POST['view_type']);
			$result = $wpdb->query("DELETE FROM ".$db_data['table_name']." WHERE ".$db_data['access_field']." = '".$_POST['view_value']."'");

			if($result){
				print "Layout Deleted.";
			}else{
				print "ERROR: Layout not deleted.";
			}
		}else{
			print "No Post Type Was Selected.";
		}
	}
	exit;
}

//Create a page or post for the list view
function cpvg_create_post_page(){
	if($_POST['name'] != ""){
		$post_data = array(
			'post_title' => $_POST['name'],
			'post_content' => "[cpvg_list name='".$_POST['list_view_name']."']",
			'post_status' => "publish",
			'post_type' => $_POST['object_type']
		);
	}

	if(@wp_insert_post($post_data)){
		echo ucwords($_POST['object_type'])." created.\n";
	}else{
		echo "ERROR: ".ucwords($_POST['object_type'])." not created.\n";
	}
}

/*********************************** ADMIN PAGE HTML GENERATION ***********************************/
//Generates the help page from the readme.txt
function cpvg_help() {
    if (!current_user_can('manage_options')){ wp_die('You do not have sufficient permissions to access this page.'); }

	$readme_contents = file_get_contents(CPVG_PLUGIN_DIR.'/readme.txt');

	require_once CPVG_PLUGIN_DIR.'/parse-readme.php';
	$r = new Automattic_Readme;
	$readme = $r->parse_readme_contents($readme_contents);

	echo "<div id='cvpg-admin-help'>";
		echo "<h3>Description</h3>".$readme['sections']['description'];
		echo str_replace("h4","h3",substr($readme['sections']['installation'],strpos($readme['sections']['installation'],'<h4>Instructions</h4>'),-1)).">";
		echo $readme["remaining_content"];
		echo '<h3>Frequently Asked Questions</h3>'.$readme['sections']['frequently_asked_questions'];
	echo "</div>";
}

/****************************************** MISC **************************************************/

//Get the database table and field names
function cpvg_get_dbfields_names($option){
	global $table_prefix;
	$data = array();

	$data['name_field'] = "name";
	$data['options_field'] = "options";
	$data['id_field'] = "id";
	$data['access_field'] = $data['name_field'];

	switch($option){
		case 'post': $data['table_name'] = $table_prefix . "cpvg_post_views"; break;
		case 'list': $data['table_name'] = $table_prefix . "cpvg_list_views"; break;
	}

	return $data;
}

//Checke if the table exists
function check_database($table_name="cpvg"){
	global $wpdb;
	$wp_cpvg_table = $table_name;

	if($wpdb->get_var("show tables like '$wp_cpvg_table'") != $wp_cpvg_table) {
		$sql0  = "CREATE TABLE `". $wp_cpvg_table . "` ( ";
		$sql0 .= "  `id`       					int(11)      NOT NULL auto_increment,";
		$sql0 .= "  `name` 	varchar(255) NOT NULL default '', ";
		$sql0 .= "  `options`  text         NOT NULL default '', ";
		$sql0 .= "  UNIQUE KEY `id` (`id`) ";
		$sql0 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";

		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql0);
	}
}

//Returns the requested post view data
function cpvg_get_post_view_data(){
    global $wpdb;

    if(isset($_POST['view_value'])){
		$db_data = cpvg_get_dbfields_names($_POST['view_type']);
		$custom_post_type_options = $wpdb->get_var("SELECT ".$db_data['options_field']." FROM ".$db_data['table_name']." WHERE ".$db_data['name_field']." = '".$_POST['view_value']."'");
		print $custom_post_type_options;
	}
}

//Returns the requested list view data
function cpvg_get_list_view_data(){
    global $wpdb;
    if(isset($_POST['view_value'])){
		$db_data = cpvg_get_dbfields_names($_POST['view_type']);
		$custom_post_type_options = $wpdb->get_var("SELECT ".$db_data['options_field']." FROM ".$db_data['table_name']." WHERE ".$db_data['name_field']." = '".$_POST['view_value']."'");
		print $custom_post_type_options;
	}
}

/****************************************** Util **************************************************/
function cpvg_random_text_value(){
	$codelenght = 8;
    $newcode = '';
    while (strlen($newcode) < $codelenght) {
        $x=1; $y=4;
		$part = rand($x,$y);

		if($part==1){$a=48;$b=57;}  // Numbers
		if($part==2){$a=65;$b=90;}  // UpperCase
		if($part==3){$a=97;$b=122;} // LowerCase
		//if($part==4){$a=32;$b=31;} // Space

		$code_part=chr(rand($a,$b));
        $newcode .= $code_part;
    }
	return $newcode;
}

function cpvg_sanitize_title_with_underscores($string){
	return str_replace("-","_",sanitize_title_with_dashes($string));
}

function cpvg_get_between($input, $start, $end){
  $substr = substr($input, strlen($start)+strpos($input, $start), (strlen($input) - strpos($input, $end))*(-1));
  return $substr;
}


function cpvg_capitalize_array_values($array){
	foreach($array as $key=>$value){
		$array[$key] =  ucwords($value);
	}
	return $array;
}