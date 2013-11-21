<?php
/*
Plugin Name: Metric Count
Description: This plugin makes API call to Ckan and stores dataset count for each organization.
*/

/** Include PHPExcel */
require_once 'Classes/PHPExcel.php';


add_action('admin_menu', 'metric_configuration');

function metric_configuration() {

    add_menu_page('Metric Count Settings', 'Metric Count Settings', 'administrator', 'metric_config', 'metric_count_settings');

}

function metric_count_settings(){

    $ckan_access_pt = (get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/';
    $org_server = (get_option('org_server') != '') ? get_option('org_server') : 'http://idm.data.gov/fed_agency.json';

    $html = '<form action="options.php" method="post" name="options">
			<h2>Metric Count Settings</h2>' . wp_nonce_field('update-options');

    $html .= '<table class="form-table" width="100%" cellpadding="10">
				<tbody>
					<tr>
						<td scope="row" aligni="left">
							<label>CKAN Metadata Access Point</label>
							<input type="text" name="ckan_access_pt" size="60" value="' . $ckan_access_pt . '">
						</td>
					</tr>
					<tr>
						<td scope="row" aligni="left">
						   <label>Organization Server Address</label>
						   <input type="text" name="org_server" size="60" value="' . $org_server . '">
						</td>
					</tr>
				</tbody>
 			</table>';

    $html .= '<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="ckan_access_pt,org_server" />
			<input type="submit" name="Submit" value="Update" />
			</form>';

    echo $html;
}


function ckan_metric_get_taxonomies() {

    $url = (get_option('org_server') != '') ? get_option('org_server') : 'http://idm.data.gov/fed_agency.json';

    $response =  wp_remote_get($url);
    $body = json_decode(wp_remote_retrieve_body(&$response), TRUE);
    $taxonomies = $body['taxonomies'];
    //var_dump($taxonomies); exit;
    return $taxonomies;
}

function ckan_metric_convert_structure($taxonomies) {

    $ret = array();

    // This should be the ONLY loop that go thru all taxonomies.
    foreach ($taxonomies as $taxonomy) {

        $taxonomy = $taxonomy['taxonomy'];

        if (strlen($taxonomy['unique id']) == 0) { // bad ones
            continue;
        }
        if ($taxonomy['unique id'] != $taxonomy['term']) { // ignore 3rd level ones
            continue;
        }
        if (!isset($ret[$taxonomy['vocabulary']])) { // Make sure we got $ret[$sector]
            $ret[$taxonomy['vocabulary']] = array();
        }
        if (strlen($taxonomy['Sub Agency']) != 0) { // it is subagency
            if (!isset($ret[$taxonomy['vocabulary']][$taxonomy['Federal Agency']])) {
                // Make sure we got $ret[$sector][$unit]
                $ret[$taxonomy['vocabulary']][$taxonomy['Federal Agency']] = array(
                    // use [ ] to indicate this is agency with subs. e.g [,sub_id]
                    'id' => "[," . $taxonomy['unique id'] . "]",
                    'is_cfo' => $taxonomy['is_cfo'],
                    'subs' => array(),
                );
            }
            else {
                // Add sub id to existing agency entry, e.g. [id,sub_id1,sub_id2] or [,sub_id1,sub_id2]
                $ret[$taxonomy['vocabulary']][$taxonomy['Federal Agency']]['id'] = "[" . trim($ret[$taxonomy['vocabulary']][$taxonomy['Federal Agency']]['id'],"[]") . "," . $taxonomy['unique id'] ."]";
            }
            // Add term to parent's subs
            $ret[$taxonomy['vocabulary']][$taxonomy['Federal Agency']]['subs'][$taxonomy['Sub Agency']] = array(
                'id' => $taxonomy['unique id'],
                'is_cfo' => $taxonomy['is_cfo'],
            );
        }
        else { // This is agecny
            if (!isset($ret[$taxonomy['vocabulary']][$taxonomy['Federal Agency']])) {
                // Has not been set by its subunits before
                $ret[$taxonomy['vocabulary']][$taxonomy['Federal Agency']] = array(
                    'id' => $taxonomy['unique id'], // leave it without [ ] if no subs.
                    'subs' => array(),
                );
            }
            else {
                // Has been added by subunits before. so let us change it from [,sub_id1,sub_id2] to [id,sub_id1,sub_id2]
                $ret[$taxonomy['vocabulary']][$taxonomy['Federal Agency']]['id'] = "[" . $taxonomy['unique id'] . trim($ret[$taxonomy['vocabulary']][$taxonomy['Federal Agency']]['id'], "[]") . "]";
            }
        }
    }


    return $ret;

}

function get_ckan_metric_info() {

    $taxonomies = ckan_metric_get_taxonomies();
    $structure = ckan_metric_convert_structure($taxonomies);
    $count = 0;

    // Instantiate a new PHPExcel object
    $objPHPExcel = new PHPExcel();
    // Set the active Excel worksheet to sheet 0
    $objPHPExcel->setActiveSheetIndex(0);
    // Initialise the Excel row number
    $rowcount = 1;

    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowcount, 'Agency Name');
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowcount, 'Sub-Agency Name');
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowcount, 'Datasets');
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowcount, 'Last Entry');
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowcount)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$rowcount)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C'.$rowcount)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$rowcount)->getFont()->setBold(true);



    $rowcount++;


    chdir('../wp-content/uploads/');

    $fp_csv = fopen('agency-list.csv', 'w');

    if($fp_csv == false ){
        die("unable to create file");
    }
    fputcsv($fp_csv, array('Agency Name', 'Sub-Agency Name', 'Datasets', 'Last Entry'));


    if(!empty($structure['Federal Organization'])) {
        foreach ($structure['Federal Organization'] as $unit => $unit_info) {


            $item = array(
                'name' => $unit,
                'id' => $unit_info['id'],
                'is_cfo' => $unit_info['is_cfo'],
                'subs' => $unit_info['subs'],
            );

            $orgs = trim($item['id'], "[,]");
            $orgs = explode(",", $orgs);

            $parent_org = $orgs[0];

            $a = array();

            foreach ($orgs as $org) {
                if (strlen($org) == 0) {
                    continue;
                }
                $a[] = "organization:" . urlencode($org);
            }

            $orgs = implode("+OR+", $a);

            $parent_nid = create_metric_content($item['is_cfo'], $item['name'], $item['id'], $orgs);


            //dataset published per month
            if(sizeof($item['subs']) > 0 && strlen($parent_org) > 0){
                create_metric_content($item['is_cfo'], $item['name'], $parent_org, $orgs, $parent_nid, 1, $fp_csv,  $objPHPExcel, $rowcount);
                $rowcount++;
            }

            if(sizeof($item['subs']) > 0 && strlen($parent_org) > 0){
                create_metric_content($item['is_cfo'], "Department/Agency Level", $parent_org, "organization:" . urlencode($parent_org), $parent_nid, 0,$fp_csv, $objPHPExcel, $rowcount, $item['name']);
                $rowcount++;
            }


            foreach($item['subs'] as $key=>$value) {
                $orgs = 'organization:' . urlencode($value['id']);
                create_metric_content($value['is_cfo'], $key, $value['id'], $orgs, $parent_nid, 0, $fp_csv, $objPHPExcel, $rowcount, $item['name']);
                $rowcount++;
            }
        }
    }

    fclose($fp_csv);

    // Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    // Write the Excel file to filename some_excel_file.xlsx in the current directory
    $objWriter->save('agency-list.xls');


}

function create_metric_content($cfo, $title, $ckan_id, $orgs, $parent_node=0, $agency_level=0, $fp_csv = FALSE, $objPHPExcel = FALSE, $rowcount = 0, $parent_name='' ) {
    $results = array();

    if($ckan_id == 'epa-gov') {

        echo $cfo; exit;
    }

    if(strlen($ckan_id) != 0) {
        $url = (get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/';
        $url .= "api/action/package_search?q=($orgs)+AND+dataset_type:dataset&rows=1&sort=metadata_modified+desc";

        $response = wp_remote_get($url);
        $body = json_decode(wp_remote_retrieve_body(&$response), true);
        $count = $body['result']['count'];
        $last_entry = $body['result']['results'][0]['metadata_modified'];
        $last_entry = substr($last_entry, 0, 10);
        $metric_timestamp = time();
    }
    else
        $count = 0;

    if($agency_level != 0) {
        //get list of last 12 months
        $month = date("m");
        $startDate = date('Y-m-d', mktime(0,0,0,$month-11,1,date('Y')));
        $endDate = date("Y-m-d", mktime(0,0,0,$month ,date("t", $month),date('Y')));

        $time1  = strtotime($startDate);
        $time2  = strtotime($endDate);
        $tmp = date('mY', $time2);

        $months[] = array("month" => date('m', $time1), "year" => date('Y', $time1));

        while($time1 < $time2) {
            $time1 = strtotime(date('Y-m-d', $time1).' +1 month');
            if(date('mY', $time1) != $tmp && ($time1 < $time2)) {
                $months[] = array("month"    => date('m', $time1), "year"    => date('Y', $time1));
            }
        }
        $months[] = array("month" => date('m', $time2), "year" => date('Y', $time2));

        $dataset_count = array();
        $dataset_range = array();
        $i = 0;
        foreach($months as $key=>$date_arr) {
            $startDt = date('Y-m-d', mktime(0,0,0,$date_arr['month'],1,$date_arr['year']));
            $endDt = date('Y-m-t', mktime(0,0,0,$date_arr['month'], 1, $date_arr['year']));
            $range = "[" . $startDt . "T00:00:00Z%20TO%20" . $endDt . "T23:59:59Z]";

            $url = (get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/';
            $url .= "api/action/package_search?q=($orgs)+AND+dataset_type:dataset+AND+metadata_created:$range&rows=1";

            $response = wp_remote_get($url);
            $body = json_decode(wp_remote_retrieve_body(&$response), true);

            $dataset_count[$i] = $body['result']['count'];
            $dataset_range[$i] = $range;
            $i++;
        }
    }
    $content_id = get_page_by_title($title, OBJECT, 'metric_organization')->ID;




    if($title == "Department/Agency Level") {
        global $wpdb;
        $myrows = $wpdb->get_var( "SELECT id FROM `wp_posts` p
								   inner join wp_postmeta pm on pm.post_id = p.id
								   where post_title = 'Department/Agency Level' and post_type = 'metric_organization'
								   and meta_key = 'parent_organization' and meta_value = " . $parent_node );

        $content_id = $myrows;
    }

    if(sizeof($content_id) == 0) {

        $my_post = array(
            'post_title' => $title,
            'post_status' => 'publish',
            'post_type' => 'metric_organization'
        );

        $new_post_id = wp_insert_post($my_post);

        add_post_meta($new_post_id, 'metric_count', $count);


        if($cfo == 'Y')
            add_post_meta($new_post_id, 'metric_sector', 'Federal');
        else
            add_post_meta($new_post_id, 'metric_sector', 'Other');



        add_post_meta($new_post_id, 'ckan_unique_id', $ckan_id);
        add_post_meta($new_post_id, 'metric_last_entry', $last_entry);
        add_post_meta($new_post_id, 'metric_sync_timestamp', $metric_timestamp);
        add_post_meta($new_post_id, 'metric_url', ((get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/') . 'dataset?q=' . $orgs);


        if($parent_node != 0)
            add_post_meta($new_post_id, 'parent_organization', $parent_node);

        if($agency_level != 0){
            for($i=0; $i<12; $i++){
                $j = $i+1;
                add_post_meta($new_post_id,'month_'.$j.'_dataset_count', $dataset_count[$i]);
            }

            for($i=0; $i<12; $i++){
                $j = $i+1;
                add_post_meta($new_post_id,'month_'.$j.'_dataset_url', ((get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/') . 'dataset?q=('.$orgs.')+AND+dataset_type:dataset+AND+metadata_created:'.$dataset_range[$i]);
            }
            add_post_meta($new_post_id,'parent_agency', 1, true);
        }

    }
    else {
        $new_post_id = $content_id;
        $my_post = array(
            'ID' => $new_post_id,
            'post_status' => 'publish',
        );

        wp_update_post($my_post);
        update_post_meta($new_post_id, 'metric_count',  $count);
        update_post_meta($new_post_id, 'ckan_unique_id', $ckan_id);

        if($cfo == 'Y')
            update_post_meta($new_post_id, 'metric_sector', 'Federal');
        else
            update_post_meta($new_post_id, 'metric_sector', 'Other');

        update_post_meta($new_post_id, 'metric_last_entry', $last_entry);
        update_post_meta($new_post_id, 'metric_sync_timestamp', $metric_timestamp);
        update_post_meta($new_post_id, 'metric_url', ((get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/') . 'dataset?q=' . $orgs);

        if($parent_node != 0)
            update_post_meta($new_post_id, 'parent_organization', $parent_node);

        if($agency_level != 0){
            for($i=0; $i<12; $i++){
                $j = $i+1;
                update_post_meta($new_post_id,'month_'.$j.'_dataset_count', $dataset_count[$i]);
            }

            for($i=0; $i<12; $i++){
                $j = $i+1;
                update_post_meta($new_post_id,'month_'.$j.'_dataset_url', ((get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/') . 'dataset?q=(' . $orgs.')+AND+dataset_type:dataset+AND+metadata_created:'.$dataset_range[$i]);
            }
            update_post_meta($new_post_id,'parent_agency', 1, true);
        }

    }
    if($fp_csv){
        if($parent_name){
            $results['Agency'] = $parent_name;
            $results['SubAgency'] = $title;
        }else{
            $results['Agency'] = $title;
            $results['SubAgency'] = '';
        }

        $results['datasets'] = $count;
        if($count > 0)
            $results['last_entry'] = $last_entry;
        else
            $results['last_entry'] = 'NA';

        // $results['cfo'] = $cfo;

        fputcsv($fp_csv, $results);

    }

    if($objPHPExcel && $rowcount){
        if($parent_name){
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowcount, $parent_name);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowcount, $title);
            if($count > 0){
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowcount, $count);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowcount, $last_entry);
            }
            else{
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowcount, 0);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowcount, 'NA');
            }
        }else{
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowcount, $title);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowcount, '');
            if($count > 0){
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowcount, $count);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowcount, $last_entry);
            }
            else{
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowcount, 0);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowcount, 'NA');
            }

            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowcount)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('C'.$rowcount)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$rowcount)->getFont()->setBold(true);
        }


    }

    return $new_post_id;
}

register_activation_hook(__FILE__, 'my_activation');
add_action('metrics_daily_update', 'get_ckan_metric_info');

function my_activation() {
    wp_schedule_event(time(), 'daily', 'metrics_daily_update');
}

register_deactivation_hook(__FILE__, 'my_deactivation');

function my_deactivation() {
    wp_clear_scheduled_hook('metrics_daily_update');
}

//add_action('admin_init', 'get_ckan_metric_info');

?>
