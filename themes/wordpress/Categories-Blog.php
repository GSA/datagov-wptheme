<?php /*
Template Name: Categories-Blog
*/
?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->


<?php get_template_part('header'); ?>
<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>

<body class="<?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?> cats">

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
            <div id="blog" class="blog" ">Blog Posts</div>
        <div class="sixteen columns">



            <?php
            global $cat_name;
            $category = get_the_category(  );
            $cat_name=$category[0]->cat_name;



            query_posts("category_name=$cat_name");
            ?>
            <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>

                <?php if( false == get_post_format() ){ ?>
                    <div id="cat-posts" class="single-cat-post horizontal_dotted_line">



                        <!-- Content - Blog Post -->
                        <div class="category-wrapper">
                            <div class="round" > <?php the_time('M jS ') ?><br><?php the_time('Y ') ?></div>
                            <div class="cat-post" id="post-<?php the_ID(); ?>">

                                <div class="core  ">
                                    <div class="<?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?> title  "><?php the_title(); ?></div>
                                    <div class="body">
                                        <?php
                                        $words = explode(" ",strip_tags(get_the_content()));
                                        $content = implode(" ",array_splice($words,0,20));
                                        echo $content;
                                        ?>
                                        <a href="<?php echo get_permalink(); ?>" style="font-weight:bold;" class="<?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?> ">Read More</a>
                                    </div>
                                </div>
                                <div class="meta1">
                                    <div id="blogauthor" >
                                        Curator:&nbsp;<?php $author = get_the_author(); echo $author;?>

                                    </div>
							 <span id="cauthor1" >
                                Categories:&nbsp;<span style="font-weight:bold;" class="authorname"><?php the_tags('', ', ', '<br />'); ?></span>
                            </span>
                                </div>
                                <div class="meta">

                                    <div>

                                        <span style="float:left; "  ><?php   $postid = get_the_ID(); $s=get_comments_number($postid); echo $s;?> &nbsp;<a id="comments" href="<?php echo get_permalink(); ?>" style="font-weight:bold;">Comment(s)</a></span>
                                        <div class="image">&nbsp;
                                        </div>
                                        <div class="corner <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?> ">

                                            <span class="topic" ><a href="<?php echo get_permalink(); ?>"> Add new comments</a></span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>  <!-- posts -->
                    <?php } ?>

                <?php endwhile; ?>

            <?php else : ?>
            <h2 class="center">Not Found</h2>
            <p class="center">Sorry, but you are looking for something that isn't here.</p>

            <?php endif; ?>








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