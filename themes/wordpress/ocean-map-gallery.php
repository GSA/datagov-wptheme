<?php /*
Template Name: Ocean-Map-Gallery
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
<div class="menu-container">
    <div class="header-next-top" >


        <?php get_template_part('navigation'); ?>



    </div>
</div>


<!-- Header Background Color, Image, or Visualization
================================================== -->

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

                        <h2 class="pane-title block-title">Map Gallery</h2>

                        <!-- Top -->
                        <p><span style="color: #666666; font-family: Arial, Helvetica, Verdana, 'Bitstream Vera Sans', sans-serif; font-size: 13px; line-height: 19px;">Planning for ocean, coastal, and Great Lakes management involves the creation of maps. Much of the data available through the Ocean Community are spatial, i.e. can be displayed on a map.This gallery allows you to visualize the data that may be most useful for regional planning.As more data become available, we will be adding to this gallery. We have divided the data in to those data sets that are national in scale and those that are regional.</span></p>
                        <!-- left Side -->
                        <div class="col1" style="float:left;width 45%;">
                        <a href="ocean-map-national"><img src="/wp-content/themes/wordpress/images/map_gallery_national_map.png" alt="Map Gallery National Category"></a> <br><h2>National</h2>
                        </div>
                        <!-- Right Side -->
                        <div class="col1" style="float:left;width 45%;">
                        <a href="ocean-map-regional"><img src="/wp-content/themes/wordpress/images/map_gallery_state_map.png" alt="Map Gallery Regional/State Categoty"></a><br><h2>Regional/State</h2>
                        </div>
                        <br clear ="all" />
                        <!-- Bottom -->

                        <p style="padding: 25px; width: 60%; border-style: dashed; border-color: rgb(0, 0, 0); -moz-border-top-colors: none; -moz-border-right-colors: none; -moz-border-bottom-colors: none; -moz-border-left-colors: none; -moz-border-image: none; border-width: 1px 0pt; font-size: 1.2em; text-align: center; margin: 0pt auto;">Create your own maps at the <a style="font-size: 1em;" href="http://www.geoplatform.gov/">Federal Geospatial Platform.</a></p>

                </div>
            </div>

        </div> <!-- content -->
        <?php get_template_part('footer'); ?>
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