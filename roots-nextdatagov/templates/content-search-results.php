<?php

$query = filter_var($_GET['q'], FILTER_SANITIZE_STRING);
$group = (filter_var($_GET['group'], FILTER_SANITIZE_STRING) ) ? filter_var($_GET['group'], FILTER_SANITIZE_STRING) : "site" ;

?>

<div class="single">
<div class="container">

<?php
// If q - search term and group information passed the call the usa search api
if(isset($query) && isset($group) && $group != 'site')
{
    usasearch_display_results($query, $group);
}

if(isset($query) && isset($group) && $group == 'site')
{
    usasearch_display_results($query, $group);
}


function usasearch_display_results($query = '', $group = ''){
    $search_version = get_site_option('search_version', '');
    $ckan_default_server = (get_option('ckan_default_server') != '') ? get_option('ckan_default_server') : 'catalog.data.gov/dataset';
    $ckan_default_server = strstr($ckan_default_server, '://') ? $ckan_default_server : ('//' . $ckan_default_server);
    // current page number
    $parts = explode('/', $_SERVER['REQUEST_URI']);
    $cur_page = $parts[2];
    // Get response from usasearch server.
    if($group == 'site'){

        $result_response = usasearch_fetch_results($query, NULL, $cur_page);
    }else{

        $result_response = usasearch_fetch_results($query, $group, $cur_page);
    }



    // Check if usasearch server responds successfully.
    if (is_wp_error($result_response) || $result_response['response']['message'] != 'OK') {
        if (is_wp_error($result_response))
        {
            error_log($result_response->get_error_message());
        }
        $error = 'Error connecting to search server.';
        return $error;
    }

    // this the total number of results
    $res = json_decode($result_response['body']);
    if ($search_version=='v2'){
    $res= $res->web;
    }
    $rows = $res->total;
    echo "<div class='search-results-alert'>
        <div class='results-count'>$rows results found for &#34;$query&#34;</div>
        You are searching in entire Data.gov site. Show results in <a href='" . $ckan_default_server . "?q=" . stripslashes( $query ) . "&sort=score+desc%2C+name+asc'> list of datasets </a>. </div>";
    ?>
<form role="search" method="get" style="display: block" class="search-form form-inline<?php if(is_front_page()): ?> col-md-12 col-lg-12<?php else:?> navbar-search navbar-nav  col-sm-6 col-md-6 col-lg-6<?php endif;?>" action="/search-results/1/">
    <div class="input-group">
        <?php if(!is_front_page()): ?>
        <label for="search-header" class="hide"><?php _e('Search for:', 'roots'); ?></label>
        <?php endif; ?>
        <input type="search" id="search-header" data-strings='{ "targets" : ["Monthly House Price Indexes", "Health Care Provider Charge Data", "Credit Card Complaints", "Manufacturing &amp; Trade Inventories &amp; Sales","Federal Student Loan Program Data"]}' value="<?php if (is_search()) { echo get_search_query(); } ?>" name="q" class="search-field form-control" placeholder="<?php _e('Search', 'roots'); ?> <?php bloginfo('name'); ?>">
        <input type="hidden" name="group" value="site">
                <span class="input-group-btn">
                    <button type="submit" class="search-submit btn btn-default">
                        <i class="fa fa-search"></i>
                        <span class="sr-only"><?php _e('Search', 'roots'); ?></span>
                    </button>
                </span>
    </div>
</form><br />
    <?php
    $count = $rows > 1000 ? 1000 : $rows;
    $total_pages = ceil( $rows / 20 );
    $paging_info = get_paging_info($count,20,$cur_page);
    if(empty ($cur_page)) $cur_page =1;
    $pager_count = "<p class='counter'>";
    $pager_count .=  "Page $cur_page of $total_pages";
    $pager_count .= "</p>";
    $pager = "<div class='pagination'>";
    $pager .= $pager_count;
    $pager .="<ul class='pagination'>";
    if($paging_info['curr_page'] > 1){
        $previous = $paging_info['curr_page']-1;

        // $pager .= "<br clear='both'/><li class='pager-first first'><a href='/search-results/1/?q=$query&group=$group' title='Page 1'> First </a></li>";
        $pager .= "<li class='pager-previous'><a href='/search-results/$previous/?q=$query&group=$group' title='Page $previous'> <span>Prev</span> </a></li>";
    }

    //setup starting point

    //$max is equal to number of links shown
    $max = 7;
    if($paging_info['curr_page'] < $max)
        $sp = 1;
    elseif($paging_info['curr_page'] >= ($paging_info['pages'] - floor($max / 2)) )
        $sp = $paging_info['pages'] - $max + 1;
    elseif($paging_info['curr_page'] >= $max)
        $sp = $paging_info['curr_page'] - floor($max/2);

    for($i = $sp; $i <= ($sp + $max -1);$i++){
        if($i > $paging_info['pages'])
            continue;
        if($paging_info['curr_page'] == $i){
            $pager .= "<li class='pager-current first'><span><strong> " .$i. "</strong></span></li>";
        }else{
            $pager .= "<li class='pager-item'><a href='/search-results/$i/?q=$query&group=$group' title='Page'".$i."><span>" .$i. "</span></a> </li>";
        }
    }

    if($paging_info['curr_page'] < $paging_info['pages']){
        $next = $paging_info['curr_page'] + 1;
        $last = $paging_info['pages'];

        $pager .= "<li class='pager-next'><a href='/search-results/$next/?q=$query&group=$group' title='Page '" .$next. "> <span>Next </span></a></li>";
        //$pager .= "<li class='pager-last last'><a href='/search-results/$last/?q=$query&group=$group' title='Page '".$last."'> Last </a></li>";

    }

    $pager .= "</div>";

    // Convert '\ue000' and '\ue001' in the response string to <strong> tag.
    $results = str_replace('\ue000','<strong>', $result_response['body']);
    $results = str_replace('\ue001','</strong>' , $results);

    $results = json_decode($results,true);
    if ($search_version=='v2'){
    $results= $results['web'];
    }
    if($rows > 0) {
        echo ' <div class="usasearch-results-wrap">';
        echo '<div class="search-results usasearch-results usasearch-boosted-results ">';
        // Display recommended results
        if ( isset( $results['boosted_results'] ) && $results['boosted_results'] != '' ) {
            foreach($results['boosted_results'] as $result){
                $title = $result['title'];
                $url = $result['url'];
                echo '<p style="color: #9E3030;">Recommended Results</p>';
                echo '<a class="search-results" href ="'.$url.'">'.$title.'</a><br />';
                echo '<p style="text-indent:20px;">'.$result['description'] ."<br /><br /></p>";
            }
        }
        echo '</div>';
        echo '</div>';
    }
    $input = array_map("unserialize", array_unique(array_map("serialize", $results['results'])));
    $usedVals = array();
    $outArray = array();
    foreach ($input as $arrayItem)
    {
        if (!in_array($arrayItem['url'],$usedVals))
        {
            $outArray[] = $arrayItem;
            $usedVals[] = $arrayItem['url'];
        }
    }
    foreach($outArray as $result){
        $title = $result['title'];
        if ($search_version=='v1'){
        $url = $result['unescapedUrl'];
        }
        if ($search_version=='v2'){
            $url = $result['url'];
        }
        $parse_url = parse_url($url);
        if($parse_url["host"]=="catalog.data.gov"){
            echo '<img src="'.get_bloginfo('template_directory').'/assets/img/dataset_icon.png" ><a class="search-results" href ="'.$url.'">'.$title.'</a><br />';
        } else {
            echo '<a class="search-results" href ="'.$url.'">'.$title.'</a><br />';
        }
        if ($search_version=='v1'){
            echo '<p style="text-indent:20px;">'.$result['content'] ."<br /><br /></p>";
        }
        if ($search_version=='v2'){
        echo '<p style="text-indent:20px;">'.$result['snippet'] ."<br /><br /></p>";
        }
    }

    // Display related terms

    if($results['related'] != NULL){
        echo "Related terms <br />";
        foreach($results['related'] as $result){
            $title = $result;
            $url = "/search-results/1/?q=$title&group=site";

            echo '<a class="search-results" href ="'.$url.'">'.$title.'</a><br />';

        }
    }

    echo $pager;

    /*echo "<div class='search-results-alert'>
        <div class='results-count'>$rows results found for &#34;$query&#34;</div>
        You are searching in entire Data.gov site. Show results in <a href='" . $protocol.$ckan_default_server . "?q=" . stripslashes( $query ) . "&sort=score+desc%2C+name+asc'> list of datasets </a>. </div>";*/
    $output = "";
    //$output = '<div style="text-align:right;"><img src ="../../../app/plugins/usa-search/images/binglogo_en.gif"></div>';
    $output .='</body></html>';
    echo $output;

}

function get_paging_info($tot_rows,$pp,$curr_page)
{
    $pages = ceil($tot_rows / $pp); // calc pages

    $data = array(); // start out array
    $data['si'] = ($curr_page * $pp) - $pp; // what row to start at
    $data['pages'] = $pages; // add the pages
    $data['curr_page'] = $curr_page; // Whats the current page

    return $data; //return the paging data

}

/**
 * Page callback function to redirect requests to catalog for data search.
 */
function usasearch_redirect_to_usasearch($query = '') {
    header("Location: //catalog.data.gov/dataset?q=$query");
    exit;

}

function usasearch_fetch_results($query, $group = NULL, $page = 0) {
    // Set action_domain from variables table or default to search.usa.gov.
    $action_domain = get_site_option('domain', 'search.usa.gov');
    // Set affiliate_name from variables table, checking for a value using ternary operator.
    $affiliate_name = (get_site_option('affiliate_name') != '') ? get_site_option('affiliate_name') : '';
    $api_key = get_site_option('api_key', '') ? get_site_option('api_key', '') : '';
    $search_version = get_site_option('search_version', '');
    // Convert from zero-based numbering to one-based.

    $page = intval($page);

    if ($page != 0) {
        $page = $page;
    } else {
        $page = 1;
    }

    // TODO put site into a variable
    $scope = $group?"site:www.data.gov/$group+":"";
    //$scope = $group?"site:www.data.gov/communities+":"";
    if ($search_version=='v1'){
    $query = "query=" . $scope . urlencode($query);
    $query .= "&affiliate=$affiliate_name";
    $query .= "&api_key=$api_key";
    $query .= "&page=$page" ;
    $query .= "&index=web";



    //echo "the value of the query in usa search function is==> "."http://$action_domain/api/search.json?$query";

        $response = wp_remote_get("https://$action_domain/api/search.json?$query");
    }
    // new Api Query
    if ($search_version=='v2'){
        $page=($page-1)*10;
        $query = "query=" . $scope . urlencode($query);
        $query .= "&affiliate=$affiliate_name";
        $query .= "&access_key=$api_key";
        $query .= "&offset=$page";
        $query .= "&limit=20";
        //echo "https://$action_domain/api/v2/search?$query";
        $response = wp_remote_get("https://$action_domain/api/v2/search?$query");
    }
    return $response;
}
?>

</div>
</div>
