<?php
/**
Template Name: USA Search
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->



<?php
$query = filter_var($_GET['q'], FILTER_SANITIZE_STRING);
$group = filter_var($_GET['group'], FILTER_SANITIZE_STRING);

if(isset($query) && $group == 'catalog')
{
    usasearch_redirect_to_usasearch($query);
}?>

<?php get_template_part('header'); ?>
<?php
$term_name = $group;
$term_slug = strtolower($term_name);



?>
<body class="<?php echo $term_slug; ?> single page">

<div class="banner disclaimer">
    <p>This is a demonstration site exploring the future of Data.gov. <span id="stop-disclaimer"> Give us your feedback on <a href="https://twitter.com/usdatagov">Twitter</a>, <a href="http://quora.com">Quora</a></span>, <a href="https://github.com/GSA/datagov-design/">Github</a>, or <a href="http://www.data.gov/contact-us">contact us</a></p>
</div>


<!-- Header Background Color, Image, or Visualization
================================================== -->
<div class="menu-container">
    <div class="header-next-top" >


        <?php get_template_part('navigation'); ?>



    </div>
</div>

<div class="next-header category <?php echo $term_slug;  ?>">
</div>


<!-- Navigation & Search
================================================== -->

<div class="container">
    <div class="next-top category <?php echo $term_slug;  ?>">


        <?php get_template_part('category-search'); ?>

    </div> <!-- top -->

</div>

<div class="page-nav">
</div>

<div class="container">


<div class="sixteen columns page-nav-items">


    <?php


    // show Links associated to a community
    // we need to build $args based either term_name or term_slug
    $args = array(
        'category_name'=> $term_slug, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
    wp_list_bookmarks($args);
    if (strcasecmp($term_name,$term_slug)!=0) {
        $args = array(
            'category_name'=> $term_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
        wp_list_bookmarks($args);
    }
    ?>
</div>

<!-- WordPress Content
================================================== -->
<div class="category-content">

<div class="content">

<div class="sixteen columns">
<?php
// If q - search term and group information passed the call the usa search api
if(isset($query) && isset($group) && $group != 'site')
{
    usasearch_display_results($query, $group);
}

if(isset($query)  && isset($group) && $group == 'site')
{
    usasearch_display_results($query, $group);
}


function usasearch_display_results($query = '', $group = ''){

    echo "<h1 class='pane-title block-title' style='margin-top:40px;font-family: 'Lato','HelveticaNeue','Helvetica'>Search Results </h1><br />";

    if($group != 'site'){
        echo "You are searching <strong>$query</strong> in <strong>$group</strong> community. Show search results in <a href=/search-results/1/?q=$query&group=site>next.data.gov</a>. <br /><br />";
    }


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
    $pager = "<div class='item-list'><ul class='pager'>";
    if($paging_info['curr_page'] > 1){
        $previous = $paging_info['curr_page']-1;

        $pager .= "<br clear='both'/><li class='pager-first first'><a href='/search-results/1/?q=$query&group=$group' title='Page 1'> First </a></li>";
        $pager .= "<li class='pager-previous'><a href='/search-results/$previous/?q=$query&group=$group' title='Page $previous'> Prev </a></li>";
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
        $pager .= "<li class='pager-current first'><a href='/search-results/1/?q=$query&group=$group' title='Page 1'>1</a></li>";
    }

    for($i = $sp; $i <= ($sp + $max -1);$i++){
        if($i > $paging_info['pages'])
            continue;
        if($paging_info['curr_page'] == $i){
            $pager .= "<li class='pager-current first'><strong> " .$i. "</strong></li>";
        }else{
            $pager .= "<li class='pager-item'><a href='/search-results/$i/?q=$query&group=$group' title='Page'".$i.">" .$i. "</a> </li>";
        }
    }

    if($paging_info['curr_page'] < $paging_info['pages']){
        $next = $paging_info['curr_page'] + 1;
        $last = $paging_info['pages'];

        $pager .= "<li class='pager-next'><a href='/search-results/$next/?q=$query&group=$group' title='Page '" .$next. "> Next </a></li>";
        $pager .= "<li class='pager-last last'><a href='/search-results/$last/?q=$query&group=$group' title='Page '".$last."'> Last </a></li>";

    }

    $pager .= "</div>";



    // Convert '\ue000' and '\ue001' in the response string to <strong> tag.
    $results = str_replace('\ue000','<strong>', $result_response['body']);
    $results = str_replace('\ue001','</strong>' , $results);

    $results = json_decode($results,true);

    // Display recommended results
    if($results['boosted_results'] != ''){
        foreach($results['boosted_results'] as $result){
            $title = $result['title'];
            $url = $result['url'];

            echo '<a class="search-results"  href ="'.$url.'">'.$title.'</a><br />';
            echo  '<p style="text-indent:20px;">'.$result['description'] ."<br /><br /></p>";
        }
    }


    foreach($results['results'] as $result){
        $title = $result['title'];
        $url = $result['unescapedUrl'];

        echo '<a class="search-results"  href ="'.$url.'">'.$title.'</a><br />';
        echo  '<p style="text-indent:20px;">'.$result['content'] ."<br /><br /></p>";
    }

    // Display related terms

    if($results['related'] != NULL){
        echo "Related terms <br />";
        foreach($results['related'] as $result){
            $title = $result;
            $url = "/search-results/1/?q=$title&group=site";

            echo '<a class="search-results"  href ="'.$url.'">'.$title.'</a><br />';

        }
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
?>
</div> <!-- sixteen columns -->
</div>
<?php get_template_part('footer'); ?>

</div> <!-- content -->
</div>
</div><!-- container -->

<script src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.joyride-2.1.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/modernizr.mq.js"></script>

<script>
    $(window).load(function(){
        $('#posts').masonry({
            // options
            columnWidth: 287,
            itemSelector : '.post',
            isResizable: true,
            isAnimated: true,
            gutterWidth: 25
        });

        $("#joyRideTipContent").joyride({
            autoStart: true,
            modal: true,
            cookieMonster: true,
            cookieName: 'datagov',
            cookieDomain: 'next.data.gov'
        });
    });
</script>
<script>
    function changePic() {

        var square = document.getElementByClass("round");

        square.style.backgroundImage = url(value);
    }
</script>
<script>
    $(function () {
        var
            $demo = $('#rotate-stats'),
            strings = JSON.parse($demo.attr('data-strings')).targets,
            randomString;

        randomString = function () {
            return strings[Math.floor(Math.random() * strings.length)];
        };

        $demo.fadeTo(randomString());
        setInterval(function () {
            $demo.fadeTo(randomString());
        }, 15000);
    });
</script>

<script src="<?php echo get_bloginfo('template_directory'); ?>/js/v1.js"></script>
<script src="<?php echo get_bloginfo('template_directory'); ?>/js/autosize.js"></script>

<!-- End Document
================================================== -->
</body>


</html>
