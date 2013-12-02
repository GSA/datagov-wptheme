<?php
/**
Template Name: USA Search
 */


// If q - search term and group information passed the call the usa search api
if(isset($_GET['q']) && isset($_GET['group']) )
{
    $query = $_GET['q'];
    $group = $_GET['group'];

    usasearch_display_results($query, $group);
}
else if(isset($_GET['q']) && !isset($_GET['group']))
{
    $query = $_GET['q'];
    // echo $query;
    usasearch_redirect_to_usasearch($query);
}


function usasearch_display_results($query = '', $group = ''){

    echo "<h1>USA Search Results </h1><br />";

    // current page number
    $parts = explode('/', $_SERVER['REQUEST_URI']);
    $cur_page = $parts[2];


    // Get response from usasearch server.
    $result_response = usasearch_fetch_results($query, $group, $cur_page);


    // Check if usasearch server responds successfully.
    if ($result_response['response']['message'] != 'OK') {
        $error = 'Error connecting to search server.';
        return $error;
    }

    // this the total number of results
    $res = json_decode($result_response['body']);

    $rows = $res->total;
    if($rows == 0){
        echo "Sorry, no results found. Try entering fewer or broader query terms.";
    }

    $count = $rows > 1000 ? 1000 : $rows;

    $paging_info = get_paging_info($count,10,$cur_page);

    if($paging_info['curr_page'] > 1){
        $previous = $paging_info['curr_page']-1;
        $pager = "<p>";
        $pager .= "<a href='/search-results/1/?q=$query&group=$group' title='Page 1'> First </a>";
        $pager .= "<a href='/search-results/$previous/?q=$query&group=$group' title='Page $previous'> Prev </a>";
    }

    //setup starting point

    //$max is equal to number of links shown
    $max = 7;
    if($paging_info['curr_page'] < $max)
        $sp = 1;
    elseif($paging_info['curr_page'] >= ($paging_info['pages'] - floor($max / 2)) )
        $sp = $paging_info['pages'] - $max + 1;
    elseif($paging_info['curr_page'] >= $max)
        $sp = $paging_info['curr_page']  - floor($max/2);

    if($paging_info['curr_page'] >= $max){
        $pager .= "<a href='/search-results/1/?q=$query&group=$group' title='Page 1'>1</a>";
    }

    for($i = $sp; $i <= ($sp + $max -1);$i++){
        if($i > $paging_info['pages'])
            continue;
        if($paging_info['curr_page'] == $i){
            $pager .= "<strong> " .$i. "</strong>";
        }else{
            $pager .= "<a href='/search-results/$i/?q=$query&group=$group' title='Page'".$i.">" .$i. "</a>";
        }
    }

    if($paging_info['curr_page'] < $paging_info['pages']){
        $next = $paging_info['curr_page'] + 1;
        $last = $paging_info['pages'];

        $pager .= "<a href='/search-results/$next/?q=$query&group=$group' title='Page '" .$next. "> Next </a>";
        $pager .= "<a href='/search-results/$last/?q=$query&group=$group' title='Page '".$last."'> Last </a>";

    }

    $pager .= "</p>";



    // Convert '\ue000' and '\ue001' in the response string to <strong> tag.
    $results = str_replace('\ue000', '<strong>', $result_response['body']);
    $results = str_replace('\ue001', '</strong>', $results);

    $results = json_decode($results,true);


    foreach($results['results'] as $result){
        $title = $result['title'];
        $url = $result['unescapedUrl'];

        echo '<a href ="'.$url.'">'.$title.'</a><br />';
        echo  $result['content'] ."<br /><br />";
    }

    echo $pager;

    $output  = '<br /><div style="text-align:center;"><img src ="/wp-content/plugins/usa-search/images/binglogo_en.gif">';
    $output .= "<div class='search-notice'>Search results were retrieved using the " . get_option('domain', 'search.usa.gov') . " API at " . date('M n Y - H:i a',time()) .
        "<br>* The USASearch Program and Federal Government cannot vouch for the data or analyses derived from these data after the data have been retrieved from USASearch.</div></div>";
    $output .='</body></html>';
    echo $output;

}

function get_paging_info($tot_rows,$pp,$curr_page)
{
    $pages = ceil($tot_rows / $pp); // calc pages

    $data = array(); // start out array
    $data['si']        = ($curr_page * $pp) - $pp; // what row to start at
    $data['pages']     = $pages;                   // add the pages
    $data['curr_page'] = $curr_page;               // Whats the current page

    return $data; //return the paging data

}


/**
 * Page callback function to redirect requests to catalog for data search.
 */
function usasearch_redirect_to_usasearch($query = '') {

    header("Location: http://catalog.data.gov/dataset?q=$query");
    exit;

}

function usasearch_fetch_results($query, $group = NULL,  $page = 0) {
    // Set action_domain from variables table or default to search.usa.gov.
    $action_domain = get_site_option('domain', 'search.usa.gov');
    // Set affiliate_name from variables table, checking for a value using ternary operator.
    $affiliate_name = (get_site_option('affiliate_name') != '') ? get_site_option('affiliate_name') : '';
    $api_key = get_site_option('api_key', '') ? get_site_option('api_key', '') : '';

    // Convert from zero-based numbering to one-based.
    if($page != 0)
        $page = $page;
    else
        $page = 1;

    // TODO put site into a variable
    $scope = $group?"site:www.data.gov/$group+":"";
    //$scope = $group?"site:www.data.gov/communities+":"";

    $query = "query=" . $scope . urlencode($query);
    $query .= "&affiliate=$affiliate_name";
    $query .= "&api_key=$api_key";
    $query .= "&page=$page" ;
    $query .= "&index=web";

    $response = wp_remote_get("http://$action_domain/api/search.json?$query");


    return $response;
}


