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
        $category = get_the_category(  );
        $cat_name=$category[0]->cat_name;


        $args = array(
            'category_name'=>$cat_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');

        wp_list_bookmarks($args); ?>
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

                            <div class="map-gallery-wrap">
                                <div class="map-align">
                                    <a href="http://www.arcgis.com/home/webmap/viewer.html?webmap=72c523c47ae241f0821f21773eb20709" target="_blank">
                                        <img title="Current Ocean Conditions Map" alt="Current Ocean Conditions Map"  src="http://www.arcgis.com/sharing/content/items/72c523c47ae241f0821f21773eb20709/info/thumbnail/Ocean.JPG" class="map-gallery-thumbnail">
                                        <div class="map-gallery-caption">Current Ocean Conditions Map</div></a></div>
                                <div class="map-align">
                                    <a href="http://www.arcgis.com/home/webmap/viewer.html?webmap=48b8cec7ebf04b5fbdcaf70d09daff21" target="_blank">
                                        <img title="Oceans" alt="Oceans" src="http://www.arcgis.com/sharing/content/items/48b8cec7ebf04b5fbdcaf70d09daff21/info/thumbnail/tempoceans.jpg" class="map-gallery-thumbnail">
                                        <div class="map-gallery-caption">Oceans</div></a></div>
                                <div class="map-align"><a href="http://www.arcgis.com/home/webmap/viewer.html?webmap=8156eea196514f7fb5f6a7a44a0b5947" target="_blank">
                                    <img title="National Marine Habitat Ranges by Lease Block" alt="National Marine Habitat Ranges by Lease Block" src="http://www.arcgis.com/sharing/content/items/8156eea196514f7fb5f6a7a44a0b5947/info/thumbnail/thumbnail.png" class="map-gallery-thumbnail">
                                    <div class="map-gallery-caption">National Marine Habitat Ranges by Lease Block</div></a></div>
                                <div class="map-align"><a href="http://www.arcgis.com/home/webmap/viewer.html?webmap=a452068c1f7f4561ba9c7fee2961d359" target="_blank">
                                    <img title="U.S. Maritime Limits and Boundaries" alt="U.S. Maritime Limits and Boundaries" src="http://www.arcgis.com/sharing/content/items/a452068c1f7f4561ba9c7fee2961d359/info/thumbnail/ago_downloaded.png" class="map-gallery-thumbnail">
                                    <div class="map-gallery-caption">U.S. Maritime Limits and Boundaries</div></a>
                                </div>
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