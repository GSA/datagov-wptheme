<?php /*
Template Name: Ocean-Map-Regional
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
                    <div id="regionalimg" class="imagearea-mapgallery" >
                        <div class="inner">
                            <h2 class="pane-title block-title">Featured Maps</h2>
                            <div id="regionalcontent" >
                                <p><span style="color: #666666; font-family: Arial, Helvetica, Verdana, 'Bitstream Vera Sans', sans-serif; font-size: 13px; line-height: 19px;">The following maps are from data sources that are regional in scope.These are some of the data sources available in the Ocean Community that are most useful in the identified region.This includes several of the human use atlases now available.</span></p>
                            </div>
                            <div class="map-gallery-wrap">
                                <div class="map-align"><a href="http://www.arcgis.com/home/webmap/viewer.html?webmap=9c18550c87944accb309083eb9d12561" target="_blank"><img title="Oregon Submarine Cables" alt="Oregon Submarine Cables" src="http://www.arcgis.com/sharing/content/items/9c18550c87944accb309083eb9d12561/info/thumbnail/ago_downloaded.png" class="map-gallery-thumbnail"><div class="map-gallery-caption">Oregon Submarine Cables</div></a></div>
                                <div class="map-align"><a href="http://www.arcgis.com/home/webmap/viewer.html?webmap=a0af6b3964a14046a24ccb76afe8ea59" target="_blank"><img title="Marine Reserves and Protected areas of Oregon" alt="Marine Reserves and Protected areas of Oregon" src="http://www.arcgis.com/sharing/content/items/a0af6b3964a14046a24ccb76afe8ea59/info/thumbnail/ago_downloaded.png" class="map-gallery-thumbnail"><div class="map-gallery-caption">Marine Reserves and Protected areas of Oregon</div></a></div>
                                <div class="map-align"><a href="http://www.arcgis.com/home/webmap/viewer.html?webmap=2abaac22ad3241148796ace1e34aab30" target="_blank"><img title="Arctic Ocean - Seafloor Bathymetry with Hillshade - UNH/CCOM-JHC" alt="Arctic Ocean - Seafloor Bathymetry with Hillshade - UNH/CCOM-JHC" src="http://www.arcgis.com/sharing/content/items/2abaac22ad3241148796ace1e34aab30/info/thumbnail/thumbnail.png" class="map-gallery-thumbnail"><div class="map-gallery-caption">Arctic Ocean - Seafloor Bathymetry with Hillshade - UNH/CCOM-JHC</div></a></div>
                                <div class="map-align"><a href="http://www.arcgis.com/home/webmap/viewer.html?webmap=a5cd5026efb1466f969488cfdd1a5809" target="_blank"><img title="North Carolina Offshore Renewable Energy Planning" alt="North Carolina Offshore Renewable Energy Planning" src="http://www.arcgis.com/sharing/content/items/a5cd5026efb1466f969488cfdd1a5809/info/thumbnail/thumbnail.png" class="map-gallery-thumbnail"><div class="map-gallery-caption">North Carolina Offshore Renewable Energy Planning</div></a></div>
                                <div class="map-align"><a href="http://www.arcgis.com/home/webmap/viewer.html?webmap=0aa00417d26e430c84caaa2ea930c596" target="_blank"><img title="South Carolina Offshore Renewable Energy Planning" alt="South Carolina Offshore Renewable Energy Planning" src="http://www.arcgis.com/sharing/content/items/0aa00417d26e430c84caaa2ea930c596/info/thumbnail/ago_downloaded.png" class="map-gallery-thumbnail"><div class="map-gallery-caption">South Carolina Offshore Renewable Energy Planning</div></a></div>
                                <div class="map-align"><a href="http://www.arcgis.com/home/webmap/viewer.html?webmap=151b60d286704dd19b51bf32e926c0f7" target="_blank"><img title="North-Atlantic Shipping and Whale Occurrences" alt="North-Atlantic Shipping and Whale Occurrences" src="http://www.arcgis.com/sharing/content/items/151b60d286704dd19b51bf32e926c0f7/info/thumbnail/thumbnail.png" class="map-gallery-thumbnail"><div class="map-gallery-caption">North-Atlantic Shipping and Whale Occurrences</div></a></div>
                            </div>
                        </div>
                    </div>
                </div>
                &nbsp;
            </div>

            <?php get_template_part('footer'); ?>
        </div> <!-- content -->
    </div>    <script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.cookie.js"></script>
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