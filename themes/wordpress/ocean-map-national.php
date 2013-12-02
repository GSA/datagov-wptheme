<?php /*
Template Name: Ocean-Map-National
*/
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->


<?php get_template_part('header'); ?>

<body class="<?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?> single page">



<!-- Header Background Color, Image, or Visualization
================================================== -->
<div class="menu-container">
    <div class="header-next-top" >


        <?php get_template_part('navigation'); ?>



    </div>
</div>

<div class="next-header category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
</div>


<!-- Navigation & Search
================================================== -->

<div class="container">
    <div class="next-top category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">


        <?php get_template_part('category-search'); ?>

    </div> <!-- top -->

</div>

<div class="page-nav">
</div>

<div class="container">


    <div class="sixteen columns page-nav-items">


        <?php
        //add_action('init','arcgis_map_process_details');
        $category = get_the_category(  );
        $cat_name=$category[0]->cat_name;
        $args = array(
            'category_name'=>$cat_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
        wp_list_bookmarks($args);
        ?>
    </div>

    <!-- WordPress Content
    ================================================== -->
    <div class="category-content">

        <div class="content">
            <div class="sixteen columns">

                <div class="technical-wrapper">
                    <div id="regionalimg" class="imagearea-mapgallery" >
                        <div class="inner">
                            <h2 class="pane-title block-title">Featured Maps</h2>
                            <div id="regionalcontent" >
                                <p><span style="color: #666666; font-family: Arial, Helvetica, Verdana, 'Bitstream Vera Sans', sans-serif; font-size: 13px; line-height: 19px;">The following maps are from data sources that are available on a national scale of coverage.
                                    These data sources are already being used by some regional portals and are the basic building blocks of information for regional planning.
                                    These data include administrative boundaries, bathymetry, and other types of data that have already been identified as needed for regional planning efforts.</span></p>
                            </div>
                            <h3 class="fieldcontentregion" style="margin:10px; ">National Maps</h3>
                            <div class="map-gallery-wrap">
                            <?php
                            global $map_results;
                            $mapinfo = array();
                            $groupinfo = array();
                            $groupmapinfo = array();
                            for($i=0;$i<count($map_results);$i++){
                               if($map_results[$i]["info"]["type"]=="Map"){
                                   $mapinfo[$i] = array_merge($map_results[$i]["map_info"][0]);
                               }
                               if($map_results[$i]["info"]["type"]=="Group"){
                                   $groupinfo[$i] = array_merge($map_results[$i]["map_info"]);
                               }
                            }
                            for($j=0;$j<count($groupinfo);$j++){
                               unset($groupinfo[$j]["total_maps"]);
                              $groupmapinfo[] = array_merge($groupinfo[$j]);
                            }
                            $group = array();
                            foreach ($groupmapinfo as $array) {
                                $group = array_merge($group, $array);
                            }
                            $merged_maps = array_merge($mapinfo,$group);

                            $total_maps = count($merged_maps);

                            //code for pagination
                            $mapsperpage = (get_option('arcgis_maps_per_page') != '') ? get_option('arcgis_maps_per_page') : '8';
                            if(isset($map_results)) {
                                $total_pages = ceil($total_maps / $mapsperpage);
                            } else {
                                $total_pages = 1;
                                $total_maps = 0;
                            }
                            if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
                                $currentpage = (int) $_GET['currentpage'];
                            } else {
                                $currentpage = 1;
                            }
                            if ($currentpage > $total_pages) {
                                $currentpage  = $total_pages;
                            }
                            if ($currentpage < 1) {
                                $currentpage = 1;
                            }
                            $start = ($currentpage - 1) * $mapsperpage + 1;
                            $count =0;
                            $output = "";
                            for($i=$start-1; $i<$start-1+$mapsperpage; $i++){
                            if(isset($merged_maps[$i])) {
                                $output .= '<div class="map-align">';
                                $output .= '<a target=_blank href="'. $merged_maps[$i]["img_href"] . '">';
                                $output .= '<img class="map-gallery-thumbnail" src="'. $merged_maps[$i]["img_src"] . '" title="' . $merged_maps[$i]["title"] .'">';

                                $output .= '<div class="map-gallery-caption">'. $merged_maps[$i]["title"] . '</div>';
                                $output .= '</a>';
                                $output .= '</div>';
                            }
                                $count ++;
                            }
                            $output .= "</div><div class='item-list'><ul class='pager'>";
                            if($total_maps > $mapsperpage) {
                                $range = 10;
                                if ($currentpage > 1) {
                                    $output .= "<br clear='both'/><li class='pager-first first'><a href='?currentpage=1'><<< FIRST </a></li> ";
                                    $prevpage = $currentpage - 1;
                                    $output .= "<li class='pager-previous'><a href='?currentpage=$prevpage'>< PREVIOUS  </a> </li>";
                                }
                                for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
                                    if (($x > 0) && ($x <= $total_pages)) {
                                        if ($x == $currentpage) {
                                            if ($currentpage == 1) {
                                                $output .="<br clear='both'/><br/>";
                                            }
                                            if ($total_pages > 1) {
                                                $output .= "<li class='pager-current first'>$x</li>";
                                            }
                                        }
                                        else {
                                            $output .= "<li class='pager-item'><a href='?currentpage=$x'> $x </a></li>";
                                        }
                                    }
                                }
                                if ($currentpage != $total_pages) {
                                    $nextpage = $currentpage + 1;
                                    $output .= " <li class='pager-next'> <a href='?currentpage=$nextpage'>NEXT ></a></li> ";
                                    $output .= " <li class='pager-last last'><a href='?currentpage=$total_pages'>  LAST >>></a> </li>";
                                }
                            }
                            print $output;
                            ?>
                            </div>
                        </div>
                    </div>
                </div>
                &nbsp;
            </div>


            <?php get_template_part('footer'); ?>

        </div> <!-- content -->
    </div>    <script src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.joyride-2.1.js"></script>
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
