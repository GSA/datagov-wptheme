<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pshirodkar
 * Date: 11/18/13
 * Time: 4:30 PM
 * To change this template use File | Settings | File Templates.
 *
 * Plugin Name: USA search
 * Description: This plugin implements the USA search functionality.
*/



function usasearch_display_results($query = '', $group = ''){

    $html = "<div class='usasearch-results-wrap'>";
    $html .= '<form action="usa-search.php" method="post" name="options">';
    $html .='<h2>USA Search results</h2>';
    $html .= '<table class="search-form-table" width="100%" cellpadding="10">
				<tbody>
					<tr>
						<td scope="row" aligni="left">
						   <label>Entr your keywords</label>
						   <input type="text" name="query" value="'.$query.'" size="60">
						   <imput type="hidden" name="group" value="'.$group.'">
						</td>
					</tr>
				</tbody>
 			</table>';

    $html .= '<input type="submit" name="Submit" value="Search in '.$group.'">
			</form>';

    echo $html;

    // Get response from usasearch server.
    $result_response = usasearch_fetch_results($query, $group);
    //var_dump($result_response);

    $page = $_GET['page'];
    // Display pager.
    if (!(isset($page)))
    {
        $page = 1;
    }

    // this the total number of results
    $rows = $result_response['total'];
    $rows = $rows > 1000 ? 1000 : $rows;


    //This is the number of results displayed per page
    $page_rows =10;

    //This tells us the page number of our last page
    $last = ceil($rows/$page_rows);

    //this makes sure the page number isn't below one, or more than our maximum pages
    if ($page < 1)
    {
        $page = 1;
    }
    elseif ($page > $last)
    {
        $page = $last;
    }

    // Get response from usasearch server.
    $result_response = usasearch_fetch_results($query, $group);

    // Convert '\ue000' and '\ue001' in the response string to <strong> tag.
    $results = str_replace('\ue000', '<strong>', $result_response['results']);
    $results = str_replace('\ue001', '</strong>', $results);


    foreach($results as $result){
        $title = $result['title'];
        $url = $result['unescapedUrl'];

        echo '<a href ="'.$url.'">'.$title.'</a><br />';
        echo $result['content'] ."<br />";
    }


    echo " --Page $page of $last-- <p>";

    if ($page == 1)
    {
    }
    else
    {
        echo " <a href='/wp-content/plugins/usa-search.php?q=$query&group=$group&page=1'> <<-First</a> ";
        echo " ";
        $previous = $page-1;
        echo " <a href='/wp-content/plugins/usa-search.php?q=$query&group=$group&page=$previous'> <-Previous</a> ";
    }
    //just a spacer
    echo " ---- ";

    if ($page == $last)
    {
    }
    else {
        $next = $page+1;
        echo " <a href='/wp-content/plugins/usa-search.php?q=$query&group=$group&page=$next'>Next -></a> ";
        echo " ";
        echo " <a href='/wp-content/plugins/usa-search.php?q=$query&group=$group&page=$last'>Last ->></a> ";
    }

}

/**
 * Page callback function to redirect requests to catalog for data search.
 */
function usasearch_redirect_to_usasearch($query = '') {

    header("Location: http://catalog.data.gov/dataset?q=$query");
    exit;

}

function usasearch_fetch_results($query, $group = NULL) {
    // Set action_domain from variables table or default to search.usa.gov.
    $action_domain = get_option('domain', 'search.usa.gov');
    // Set affiliate_name from variables table, checking for a value using ternary operator.
    $affiliate_name = (get_option('affiliate_name') != '') ? get_option('affiliate_name') : '';
    $api_key = get_option('api_key', '') ? get_option('api_key', '') : '';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
    // Convert from zero-based numbering to one-based.
    $page++;

    // TODO put site into a variable
    $scope = $group?"site:www.data.gov/$group+":"";
    //$scope = $group?"site:www.data.gov/communities+":"";

    $query = "query=" . $scope . urlencode($query);
    $query .= "&affiliate=$affiliate_name";
    $query .= "&api_key=$api_key";
    $query .= "&page=$page" ;
    $query .= "&index=web";

    $response = wp_remote_get("http://$action_domain/api/search.json?$query", array( 'timeout' => 120, 'httpversion' => '1.1' ));
    $body = json_decode(wp_remote_retrieve_body(&$response), true);

    return $body;
}


if(isset($_GET['q']) && isset($_GET['group']) )
{
  $query = $_GET['q'];
  $group = $_GET['group'];

usasearch_display_results($query, $group);
}
else if(isset($_GET['q']) && !isset($_GET['group']))
{
    $query = $_GET['q'];
    echo $query;
    usasearch_redirect_to_usasearch($query);
}

// USA search settings
add_action('admin_menu', 'search_configuration');

function search_configuration() {

    add_menu_page('USA Search Settings', 'USA Search Settings', 'administrator', 'search_config', 'usa_search_settings');

}


function usa_search_settings() {

    $affiliate_name = (get_option('affiliate_name') != '') ? get_option('affiliate_name') : 'datagov';
    $api_key = (get_option('api_key') != '') ? get_option('api_key') : '703cf139fb10f62e63511ff7f7e2b9c5';
    $domain = (get_option('domain') != '') ? get_option('domain') : 'search.usa.gov';



    $html = '<form action="options.php" method="post" name="options">
			<h2>USA Search Settings</h2>' . wp_nonce_field('update-options');

    $html .= '<table class="form-table" width="100%" cellpadding="10">
				<tbody>
					<tr>
						<td scope="row" aligni="left">
							<label>Affiliate name:</label>
							<input type="text" name="affiliate_name" size="60" value="' . $affiliate_name . '">
						</td>
					</tr>
					<tr>
						<td scope="row" aligni="left">
						   <label>API key</label>
						   <input type="text" name="api_key" size="60" value="' . $api_key . '">
						</td>
					</tr>
					<tr>
						<td scope="row" aligni="left">
						   <label>Search Domain</label>
						   <input type="text" name="domain" size="60" value="' . $domain . '">
						</td>
					</tr>

				</tbody>
 			</table>';

    $html .= '<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="affiliate_name,api_key,domain" />
			<input type="submit" name="Submit" value="Update" />
			</form>';

    echo $html;
}
?>