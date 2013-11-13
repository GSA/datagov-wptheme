<?php /*
Template Name: Ocean-Regional-Planning-Efforts
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
                <div class="technical-wrapper" style="width:100%">

                    <div id="regionalimg" class="imagearea" >
                        <div class="inner">
                            <h2 class="pane-title block-title">Regional Planning Efforts</h2>

                            <img alt="Regional Planning Efforts" src="<?php echo get_bloginfo('template_directory'); ?>/images/img2.jpg" width="631" height="472" />
                            <div id="regionalcontent" >
                                <p>
                                    Marine planning is a science-based process that provides transparent information about ocean use and guarantees the public and stakeholders a voice early on in decisions affecting the uses of the marine environment. It is an inclusive, bottom-up approach that gives the Federal Government, States, and Tribes, with input from local communities, stakeholders, and the public, the ability to make informed decisions on how best to optimize the use of and protect the ocean, coasts, and Great Lakes.
                                </p>
                                <p>
                                    Under the National Ocean Policy, the United States is subdivided into nine regional planning areas. Within each region, Federal, State, and Tribal partners come together to form regional planning bodies. As regional planning efforts mature, reliable data and information will be critical in making informed decisions. Regions may choose to develop their own data portals to share region-specific data, maps, and decision-support tools. This community will maintain links to these region-specific portals as they come online, and we will work with the regions to enable discovery of and access to the data and information they need.

                                </p>
                                <p>
                                    Click on a region in the map or along the sidebar to view information on current regional planning efforts and access links to existing regional data portals. If you would like a particular regional planning effort to be added, please <a href="/ocean/page/ocean-feedback-form">let us know</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div id="regionsidebar">
                        <div class="panels-flexible-region-inside">
                            <div class="panel-pane pane-views pane-ocean-regional-planning">
                                <div class="inner">

                                    <?php
                                    $args = array(
                                        'orderby'          => '',
                                        'meta_key'         => 'field_alias',
                                        'orderby'          => 'meta_value',
                                        'order'            => 'ASC',
                                        'post_type'        => 'regional_planning',
                                        'post_status'      => 'publish',
                                        'suppress_filters' => true );
                                    $query = null;
                                    $query = new WP_Query($args);
                                    if( $query->have_posts() ) {
                                        echo '<h2 class="block-title">Planning Regions</h2>';
                                        echo '<div class="panecontent">';
                                        echo '<div class="item-list">';
                                        echo '<ul>';
                                        while ($query->have_posts()) : $query->the_post();
                                            $link="/ocean/page/regional-planning?field_alias_value=".get_post_meta($post->ID, 'field_alias',TRUE)
                                            ?>

                                            <li><a href="<?php echo $link?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>

                                            <?php
                                        endwhile;
                                    }?>
                                </div>
                            </div>
                        </div>
                    </div></div>
                &nbsp;
            </div>

            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Planning Regions') ) : ?>
            <?php endif; ?>
            <?php get_template_part('footer'); ?>
        </div> <!-- content -->
    </div>
</div>
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