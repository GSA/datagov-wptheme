<?php /*
Template Name: FAQs
*/
?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->


<?php get_template_part('header'); ?>

<body class="single">
<div class="banner disclaimer">
    <p>This is a demonstration site exploring the future of Data.gov. <span id="stop-disclaimer"> Give us your feedback on <a href="https://twitter.com/usdatagov">Twitter</a>, <a href="http://quora.com">Quora</a></span>, <a href="https://github.com/GSA/datagov-design/">Github</a>, or <a href="http://www.data.gov/contact-us">contact us</a></p>
</div>


<!-- Header Background Color, Image, or Visualization
test
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




    <!-- WordPress Content
    ================================================== -->
    <div class="category-content">

        <div class="content">
            <div class="inner">
                <h2 class="pane-title block-title">Frequently Asked Questions</h2>
                <div class="pane-content content">

                    <p>Following are some Frequently Asked Questions, we hope to add to this list as we hear from you.</p>
                </div>
            </div>


            <div class="sixteen columns">



                <div class=" view-display-id-ogpl_ocean_faq_block ">
                    <div class="view-header">
                        <a id="faq_top"></a>
                        <p>
                        <h2 style="color: #284A78; font-family: Georgia,Times New Roman,Times,serif;font-size: 142.85%;">Questions</h2>
                    </div>


                    <div class="item-list">
                        <ol>
                            <?php
                            global $cat_name;
                            $category = get_the_category(  );
                            $cat_name=$category[0]->slug;




//WordPress loop for custom post type
                            $my_query = new WP_Query("post_type=qa_faqs&posts_per_page=-1&faq_category=$cat_name");

                            while ($my_query->have_posts()) : $my_query->the_post(); ?>


                                <li  >
                                    <a href="" style="color: #4295B0;font: bold 12px Arial,Helvetica,sans-serif; "><?php the_title(); ?></a></li>






                                <?php endwhile;  wp_reset_query(); ?>
                        </ol>
                    </div>
                </div>




                <div class="separator-mini-700"> </div>


                <?php
//WordPress loop for custom post type
                $my_query = new WP_Query("post_type=qa_faqs&posts_per_page=-1&faq_category=$cat_name");
                while ($my_query->have_posts()) : $my_query->the_post(); ?>

                    <div class="views-field views-field-nothing-1">
<span class="field-content">

<?php the_title(); ?>
</span>
                    </div>



                    <span class="field-content">

<p><?php the_content(); ?></p>
<p>
    <a class="faq-top" href="#faq_top">Return to Top</a>
</p>
</span>








                    <?php endwhile;  wp_reset_query(); ?>





            </div> <!-- sixteen columns -->

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