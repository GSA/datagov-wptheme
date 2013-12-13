<?php
/*
Plugin Name: Metric Count
Description: This plugin makes API call to Ckan AND stores dataset count for each organization.
*/

/** Include PHPExcel */
require_once 'Classes/PHPExcel.php';
require_once 'Classes/PHPExcel/IOFactory.php';

$results = array();


add_action('admin_menu', 'metric_configuration');

/**
 *
 */
function metric_configuration()
{
    add_menu_page('Metric Count Settings', 'Metric Count Settings', 'administrator', 'metric_config', 'metric_count_settings');
}

/**
 *
 */
function metric_count_settings()
{
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


/**
 * @return mixed
 */
function ckan_metric_get_taxonomies()
{
    $url = (get_option('org_server') != '') ? get_option('org_server') : 'http://idm.data.gov/fed_agency.json';

    $response = wp_remote_get($url);
    $body = json_decode(wp_remote_retrieve_body($response), TRUE);
    $taxonomies = $body['taxonomies'];

    return $taxonomies;
}


/**
 * @param $taxonomies
 * @return array
 */
function ckan_metric_convert_structure($taxonomies)
{

//    var_dump($taxonomies);die();
//      array(8) {
//         [20] =>
//              array(6) {
//                'vocabulary' =>
//                		string(20) "Federal Organization"
//                'term' =>
//                		string(12) "ocfo-gsa-gov"
//                'Federal Agency' =>
//                		string(31) "General Services Administration"
//                'Sub Agency' =>
//                		string(37) "Office of the Chief Financial Officer"
//                'unique id' =>
//                		string(12) "ocfo-gsa-gov"
//                'is_cfo' =>
//                		string(1) "Y"
//              }
//              [28] =>
//              array(6) {
//                'vocabulary' =>
//                		string(20) "Federal Organization"
//                'term' =>
//                		string(11) "pbs-gsa-gov"
//                'Federal Agency' =>
//                		string(31) "General Services Administration"
//                'Sub Agency' =>
//                		string(24) "Public Buildings Service"
//                'unique id' =>
//                		string(11) "pbs-gsa-gov"
//                'is_cfo' =>
//                		string(1) "Y"
//              }
//              [35] =>
//            array(6) {
//                'vocabulary' =>
//                		string(20) "Federal Organization"
//                'term' =>
//                		string(13) "ocsit-gsa-gov"
//                'Federal Agency' =>
//                		string(31) "General Services Administration"
//                'Sub Agency' =>
//                		string(54) "Office of Citizen Services and Innovative Technologies"
//                'unique id' =>
//                		string(13) "ocsit-gsa-gov"
//                'is_cfo' =>
//                		string(1) "Y"
//              }
//              [64] =>
//              array(6) {
//                'vocabulary' =>
//                		string(20) "Federal Organization"
//                'term' =>
//                		string(11) "ogp-gsa-gov"
//                'Federal Agency' =>
//                		string(31) "General Services Administration"
//                'Sub Agency' =>
//                		string(31) "Office of Governmentwide Policy"
//                'unique id' =>
//                		string(11) "ogp-gsa-gov"
//                'is_cfo' =>
//                		string(1) "Y"
//              }
//            [132] =>
//              array(6) {
//                'vocabulary' =>
//                		string(20) "Federal Organization"
//                'term' =>
//                		string(11) "fas-gsa-gov"
//                'Federal Agency' =>
//                		string(31) "General Services Administration"
//                'Sub Agency' =>
//                		string(27) "Federal Acquisition Service"
//                'unique id' =>
//                		string(11) "fas-gsa-gov"
//                'is_cfo' =>
//                		string(1) "Y"
//              }
//              [133] =>
//              array(6) {
//                'vocabulary' =>
//                		string(20) "Federal Organization"
//                'term' =>
//                		string(7) "gsa-gov"
//                'Federal Agency' =>
//                		string(31) "General Services Administration"
//                'Sub Agency' =>
//                		string(0) ""
//                'unique id' =>
//                		string(7) "gsa-gov"
//                'is_cfo' =>
//                		string(1) "Y"
//              }
//              [138] =>
//              array(6) {
//                'vocabulary' =>
//                		string(20) "Federal Organization"
//                'term' =>
//                		string(11) "cpo-gsa-gov"
//                'Federal Agency' =>
//                		string(31) "General Services Administration"
//                'Sub Agency' =>
//                		string(34) "Office of the Chief People Officer"
//                'unique id' =>
//                		string(11) "cpo-gsa-gov"
//                'is_cfo' =>
//                		string(1) "Y"
//              }
//              [157] =>
//              array(6) {
//                'vocabulary' =>
//                		string(20) "Federal Organization"
//                'term' =>
//                		string(11) "opi-gsa-gov"
//                'Federal Agency' =>
//                		string(31) "General Services Administration"
//                'Sub Agency' =>
//                		string(33) "Office of Performance Improvement"
//                'unique id' =>
//                		string(11) "opi-gsa-gov"
//                'is_cfo' =>
//                		string(1) "Y"
//              }
//        }

    $return = array();
    // This should be the ONLY loop that go through all taxonomies.
    foreach ($taxonomies as $taxonomy) {
        $taxonomy = $taxonomy['taxonomy'];

//        ignore bad ones
        if (strlen($taxonomy['unique id']) == 0) {
            continue;
        }

//        ignore 3rd level ones
        if ($taxonomy['unique id'] != $taxonomy['term']) {
            continue;
        }

//        Make sure we got $return[$sector], ex. $return['Federal Organization']
        if (!isset($return[$taxonomy['vocabulary']])) {
            $return[$taxonomy['vocabulary']] = array();
        }

        if (strlen($taxonomy['Sub Agency']) != 0) {
//        This is sub-agency
//            $return['Federal Organization']['National Archives and Records Administration']
            if (!isset($return[$taxonomy['vocabulary']][$taxonomy['Federal Agency']])) {
//                Make sure we got $return[$sector][$unit]
                $return[$taxonomy['vocabulary']][$taxonomy['Federal Agency']] = array(
                    // use [ ] to indicate this is agency with subs. e.g [,sub_id]
                    'id' => "[," . $taxonomy['unique id'] . "]",
                    'is_cfo' => $taxonomy['is_cfo'],
                    'subs' => array(),
                );
            } else {
//                Add sub id to existing agency entry, e.g. [id,sub_id1,sub_id2] or [,sub_id1,sub_id2]
                $return[$taxonomy['vocabulary']][$taxonomy['Federal Agency']]['id']
                    = "[" . trim($return[$taxonomy['vocabulary']][$taxonomy['Federal Agency']]['id'], "[]") . "," . $taxonomy['unique id'] . "]";
            }

//            Add term to parent's subs
            $return[$taxonomy['vocabulary']][$taxonomy['Federal Agency']]['subs'][$taxonomy['Sub Agency']] = array(
                'id' => $taxonomy['unique id'],
                'is_cfo' => $taxonomy['is_cfo'],
            );
        } else {
//        ELSE this is ROOT agency
            if (!isset($return[$taxonomy['vocabulary']][$taxonomy['Federal Agency']])) {
//                Has not been set by its subunits before
                $return[$taxonomy['vocabulary']][$taxonomy['Federal Agency']] = array(
                    'id' => $taxonomy['unique id'], // leave it without [ ] if no subs.
                    'is_cfo' => $taxonomy['is_cfo'],
                    'subs' => array(),
                );
            } else {
//                Has been added by subunits before. so let us change it from [,sub_id1,sub_id2] to [id,sub_id1,sub_id2]
                $return[$taxonomy['vocabulary']][$taxonomy['Federal Agency']]['id'] = "[" . $taxonomy['unique id'] . trim($return[$taxonomy['vocabulary']][$taxonomy['Federal Agency']]['id'], "[]") . "]";
            }
        }
    }

//    array(3) {
//      'id' =>
//      string(96) "[gsa-gov,ocfo-gsa-gov,pbs-gsa-gov,ocsit-gsa-gov,ogp-gsa-gov,fas-gsa-gov,cpo-gsa-gov,opi-gsa-gov]"
//      'is_cfo' =>
//      string(1) "Y"
//      'subs' =>
//      array(7) {
//            'Office of the Chief Financial Officer' =>
//        array(2) {
//          'id' =>
//          	string(12) "ocfo-gsa-gov"
//          'is_cfo' =>
//          	string(1) "Y"
//        }
//        'Public Buildings Service' =>
//        array(2) {
//          'id' =>
//          	string(11) "pbs-gsa-gov"
//          'is_cfo' =>
//          	string(1) "Y"
//        }
//        'Office of Citizen Services and Innovative Technologies' =>
//        array(2) {
//          'id' =>
//          	string(13) "ocsit-gsa-gov"
//          'is_cfo' =>
//          	string(1) "Y"
//        }
//        'Office of Governmentwide Policy' =>
//        array(2) {
//          'id' =>
//          	string(11) "ogp-gsa-gov"
//          'is_cfo' =>
//          	string(1) "Y"
//        }
//        'Federal Acquisition Service' =>
//        array(2) {
//          'id' =>
//          	string(11) "fas-gsa-gov"
//          'is_cfo' =>
//          	string(1) "Y"
//        }
//        'Office of the Chief People Officer' =>
//        array(2) {
//          'id' =>
//          	string(11) "cpo-gsa-gov"
//          'is_cfo' =>
//          	string(1) "Y"
//        }
//        'Office of Performance Improvement' =>
//        array(2) {
//          'id' =>
//          	string(11) "opi-gsa-gov"
//          'is_cfo' =>
//          	string(1) "Y"
//        }
//      }
//    }
    
    return $return;
}

/**
 *
 */
function get_ckan_metric_info()
{
    $taxonomies = ckan_metric_get_taxonomies();

    $structure = ckan_metric_convert_structure($taxonomies);

    global $results;

    if (!empty($structure['Federal Organization'])) {

        foreach ($structure['Federal Organization'] as $unit => $unit_info) {

            $item = array(
                'name' => $unit,
                'id' => $unit_info['id'],
                'is_cfo' => $unit_info['is_cfo'],
                'subs' => $unit_info['subs'],
            );

            $organizations = trim($item['id'], "[]");

            $organizations = explode(",", $organizations);

//            !!!!!!
//            That's wrong for
//            [,ostp-eop-gov,ustr-eop-gov,wh-eop-gov,ceq-eop-gov,omb-eop-gov,ondcp-eop-gov]
//            [,msha-dol-gov,bls-gov,brb-dol-gov,ecab-dol-gov,whd-dol-gov,esba-dol-gov,eta-dol-gov,ojc-dol-gov,osha-dol-gov,oalj-dol-gov,oasp-dol-gov]
//            !!!!!!
            $parent_org = $organizations[0];

//            HACK  ostp-eop-gov  =>  eop-gov
            if (!strlen($parent_org) && isset($organizations[1])) {
                $parent_org = substr($organizations[1], strpos($organizations[1], '-')+1);
            }

            $organization_solr_filter = array();

            foreach ($organizations as $org) {
                if (strlen($org) == 0) {
                    continue;
                }
//                $organization_solr_filter[] = 'organization:' . urlencode($org);
                $organization_solr_filter[] = '(' . urlencode($org).')';
            }

//            $organizations = implode('+OR+', $organization_solr_filter);
            $organizations = 'organization:('.join('+OR+', $organization_solr_filter).')';

            $parent_nid = create_metric_content($item['is_cfo'], $item['name'], $item['id'], $organizations);

//            Mark parent agency
            if (sizeof($item['subs']) > 0 && strlen($parent_org) > 0) {
                create_metric_content($item['is_cfo'], $item['name'], $parent_org, $organizations, $parent_nid, 1, '', 0);
            }

            if (sizeof($item['subs']) > 0 && strlen($parent_org) > 0) {
                create_metric_content($item['is_cfo'], 'Department/Agency Level', $parent_org, 'organization:' . urlencode($parent_org), $parent_nid, 0, $item['name'], 0, 1);
            }

            foreach ($item['subs'] as $key => $value) {
                $organizations = 'organization:' . urlencode($value['id']);
                create_metric_content($value['is_cfo'], $key, $value['id'], $organizations, $parent_nid, 0, $item['name'], 1, 1);
            }
        }
    }

    asort($results);
    chdir('../media/');

//    Write CSV result file
    $fp_csv = fopen('federal-agency-participation.csv', 'w');

    if ($fp_csv == false) {
        die("unable to create file");
    }

    fputcsv($fp_csv, array('Agency Name', 'Sub-Agency Name', 'Datasets', 'Last Entry'));

    foreach ($results as $record) {
        fputcsv($fp_csv, $record);
    }
    fclose($fp_csv);

    // Instantiate a new PHPExcel object
    $objPHPExcel = new PHPExcel();
    // Set the active Excel worksheet to sheet 0
    $objPHPExcel->setActiveSheetIndex(0);
    // Initialise the Excel row number
    $row = 1;

    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $row, 'Agency Name');
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $row, 'Sub-Agency Name');
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $row, 'Datasets');
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $row, 'Last Entry');
    $row++;

    foreach ($results as $record) {
        if ($record) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $row, $record[0]);
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $row, $record[1]);
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $row, $record[2]);
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $row, $record[3]);
            $row++;
        }
    }

    // Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    // Write the Excel file to filename some_excel_file.xlsx in the current directory
    $objWriter->save('federal-agency-participation.xls');
}

/**
 * @param $cfo
 * @param $title
 * @param $ckan_id
 * @param $organizations
 * @param int $parent_node
 * @param int $agency_level
 * @param string $parent_name
 * @param int $sub_agency
 * @param int $export
 * @return mixed
 */
function create_metric_content($cfo, $title, $ckan_id, $organizations, $parent_node = 0, $agency_level = 0, $parent_name = '', $sub_agency = 0, $export = 0)
{
    global $results;

    if (strlen($ckan_id) != 0) {
        $url = (get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/';
        $url .= "api/3/action/package_search?fq=($organizations)+AND+dataset_type:dataset&rows=1&sort=metadata_modified+desc";

//        echo $url.PHP_EOL;

        $response = wp_remote_get($url);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        $count = $body['result']['count'];

        if ($count) {
            $last_entry = $body['result']['results'][0]['metadata_modified'];
//        2013-12-12T07:39:40.341322

            $last_entry = substr($last_entry, 0, 10);
//        2013-12-12

        }
    } else {
        $count = 0;
    }

    $metric_sync_timestamp = time();

    if ($cfo == 'Y' && $title != 'Department/Agency Level') {
        //get list of last 12 months
        $month = date('m');

        $startDate = mktime(0, 0, 0, $month - 11, 1, date('Y'));
        $endDate = mktime(0, 0, 0, $month, date('t'), date('Y'));

        $tmp = date('mY', $endDate);

        while (true) {
            $months[] = array(
                'month' => date('m', $startDate),
                'year' => date('Y', $startDate)
            );

            if ($tmp == date('mY', $startDate)) {
                break;
            }

            $startDate = mktime(0, 0, 0, date('m', $startDate)+1, 15, date('Y', $startDate));
        }

        $dataset_count = array();
        $dataset_range = array();

        $i = 1;

        foreach ($months as $date_arr) {
            $startDt = date('Y-m-d', mktime(0, 0, 0, $date_arr['month'], 1, $date_arr['year']));
            $endDt = date('Y-m-t', mktime(0, 0, 0, $date_arr['month'], 1, $date_arr['year']));

            $range = "[" . $startDt . "T00:00:00Z%20TO%20" . $endDt . "T23:59:59Z]";

            $url = (get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/';
            $url .= "api/3/action/package_search?q=($organizations)+AND+dataset_type:dataset+AND+metadata_created:$range&rows=1";

            $response = wp_remote_get($url);
            $body = json_decode(wp_remote_retrieve_body($response), true);

            $dataset_count[$i] = $body['result']['count'];
            $dataset_range[$i] = $range;
            $i++;
        }
    }

    $content_id = get_page_by_title($title, OBJECT, 'metric_organization')->ID;

    if ($sub_agency) {
        global $wpdb;
        $myrows = $wpdb->get_var("SELECT id FROM `wp_posts` p
                   INNER JOIN wp_postmeta pm ON pm.post_id = p.id
                   WHERE post_title = '" . $title . "' AND post_type = 'metric_organization'
                   AND meta_key = 'ckan_unique_id' AND meta_value = '" . $ckan_id . "'");
        $content_id = $myrows;
    }

    if ($title == 'Department/Agency Level') {
        global $wpdb;
        $myrows = $wpdb->get_var("SELECT id FROM `wp_posts` p
                   INNER JOIN wp_postmeta pm ON pm.post_id = p.id
                   WHERE post_title = 'Department/Agency Level' AND post_type = 'metric_organization'
                   AND meta_key = 'parent_organization' AND meta_value = " . $parent_node);

        $content_id = $myrows;
    }

    if (sizeof($content_id) == 0) {

        $my_post = array(
            'post_title' => $title,
            'post_status' => 'publish',
            'post_type' => 'metric_organization'
        );

        $new_post_id = wp_insert_post($my_post);

        add_post_meta($new_post_id, 'metric_count', $count);


        if ($cfo == 'Y' && $title != 'Department/Agency Level') {
            for ($i = 1; $i < 13; $i++) {
                add_post_meta($new_post_id, 'month_' . $i . '_dataset_count', $dataset_count[$i]);
            }

            for ($i = 1; $i < 13; $i++) {
                add_post_meta($new_post_id, 'month_' . $i . '_dataset_url',
                    ((get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/')
                    . 'dataset?q=(' . $organizations . ')+AND+dataset_type:dataset+AND+metadata_created:' . $dataset_range[$i]);
            }

        }
        if ($cfo == 'Y') {
            add_post_meta($new_post_id, 'metric_sector', 'Federal');
        } else {
            add_post_meta($new_post_id, 'metric_sector', 'Other');
        }

        list($Y, $m, $d) = explode('-', $last_entry);
        $last_entry = "$m/$d/$Y";

        add_post_meta($new_post_id, 'ckan_unique_id', $ckan_id);
        add_post_meta($new_post_id, 'metric_last_entry', $last_entry);
        add_post_meta($new_post_id, 'metric_sync_timestamp', $metric_sync_timestamp);
        add_post_meta($new_post_id, 'metric_url', ((get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/') . 'dataset?q=' . $organizations);


        if ($parent_node != 0) {
            add_post_meta($new_post_id, 'parent_organization', $parent_node);
        }

        if ($agency_level != 0) {
            add_post_meta($new_post_id, 'parent_agency', 1, true);
        }

        $flag = false;
        if ($count > 0) {
            if ($export != 0) {
                $results[] = array($parent_name, $title, $count, $last_entry);
            }

            if ($parent_node == 0 && $flag == false) {
                $parent_name = $title;
                $title = '';

                $results[] = array($parent_name, $title, $count, $last_entry);
            }
        }
    } else {
        $new_post_id = $content_id;
        $my_post = array(
            'ID' => $new_post_id,
            'post_status' => 'publish',
        );

        wp_update_post($my_post);
        update_post_meta($new_post_id, 'metric_count', $count);
        update_post_meta($new_post_id, 'ckan_unique_id', $ckan_id);

        if ($cfo == 'Y' && $title != 'Department/Agency Level') {
            for ($i = 1; $i < 13; $i++) {
                update_post_meta($new_post_id, 'month_' . $i . '_dataset_count', $dataset_count[$i]);
            }

            for ($i = 1; $i < 13; $i++) {
                update_post_meta($new_post_id, 'month_' . $i . '_dataset_url',
                    ((get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/')
                    . 'dataset?q=(' . $organizations . ')+AND+dataset_type:dataset+AND+metadata_created:' . $dataset_range[$i]);
            }
        }

        if ($cfo == 'Y') {
            update_post_meta($new_post_id, 'metric_sector', 'Federal');
        } else {
            update_post_meta($new_post_id, 'metric_sector', 'Other');
        }

        update_post_meta($new_post_id, 'metric_last_entry', $last_entry);
        update_post_meta($new_post_id, 'metric_sync_timestamp', $metric_sync_timestamp);
        update_post_meta($new_post_id, 'metric_url', ((get_option('ckan_access_pt') != '') ? get_option('ckan_access_pt') : 'http://catalog.data.gov/') . 'dataset?q=' . $organizations);

        if ($parent_node != 0) {
            update_post_meta($new_post_id, 'parent_organization', $parent_node);
        }

        if ($agency_level != 0) {
            update_post_meta($new_post_id, 'parent_agency', 1, true);
        }

        $flag = false;
        if ($count > 0) {
            if ($export != 0) {
                $results[] = array($parent_name, $title, $count, $last_entry);
            }

            if ($parent_node == 0 && $flag == false) {
                $parent_name = $title;
                $title = '';

                $results[] = array($parent_name, $title, $count, $last_entry);
            }
        }
    }

    return $new_post_id;
}

register_activation_hook(__FILE__, 'my_activation');
add_action('metrics_daily_update', 'get_ckan_metric_info');

/**
 *
 */
function my_activation()
{
    wp_schedule_event(time(), 'daily', 'metrics_daily_update');
}

register_deactivation_hook(__FILE__, 'my_deactivation');

/**
 *
 */
function my_deactivation()
{
    wp_clear_scheduled_hook('metrics_daily_update');
}