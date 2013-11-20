
<?php get_header(); ?>
<?php
$term_name = get_term_by('id', get_query_var('cat'), 'category')->name;

$term_slug = get_query_var('category_name');
?>
<?php $category = get_the_category();
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>

<body class="<?php $category = get_the_category(); echo $category[0]->cat_name; ?>">

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
    <div class="clear">&nbsp;</div>
    <!-- box containter for communities -->
    <div class="<?php $category = get_the_category(); echo $category[0]->cat_name; ?>_outer community_box">

        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('category_container') ) : ?><?php endif; ?>

    </div>
    <div class="bottom_blocks">
        <div id="container4">
            <div id="container3">
                <div id="container2">
                    <div id="container1">
                        <div id="col1">
                            <h1><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('category_box_1') ) : ?><?php endif; ?></h1>
                        </div>
                        <div id="col2">
                            <!-- Column two start -->
                            <h1><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('category_box_2') ) : ?><?php endif; ?></h1>
                            <!-- Column two end -->
                        </div>
                        <div id="col3">
                            <!-- Column three start -->
                            <h1><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('category_box_3') ) : ?><?php endif; ?></h1>
                            <!-- Column three end -->
                        </div>
                        <div id="col4">
                            <!-- Column four start -->
                            <h1><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('category_box_4') ) : ?><?php endif; ?></h1>
                            <!-- Column four end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- WordPress Content
    ================================================== -->

    <div class="content">

        <div class="sixteen columns">
            <div id="posts">

                <!-- lead-image -->
                <!-- source -->
                <!-- title -->
                <!-- body -->
                <!-- timestamp -->
                <!-- topic -->

                <?php global $query_string; ?>
                <?php query_posts( $query_string . '&meta_key=community_content&meta_value=No' ); ?>

                <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>

                    <?php if (get_post_format() == 'status'): ?>

                        <!-- Content - Tweet -->
                        <div class="post small tweet">
                            <div class="core">
                                <div class="tweet-author">
                                    <div class="author-image">
                                        <img alt="" src="<?php the_field('twitter_photo'); ?>" height="40" width="40">
                                    </div>
                                    <div class="author-details">
                                        <?php the_field('persons_name'); ?> - <a href="<?php the_field('link_to_tweet'); ?>">@<?php the_field('twitter_handle'); ?></a>
                                    </div>
                                </div>
                                <div class="body">
                                    <?php the_content('Read the rest of this entry »'); ?>
                                </div>
                            </div>
                            <div class="meta">
                                <div class="timestamp"><?php the_time('F jS, Y') ?></div>
                                <div class="corner <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
                                    <div class="block"></div>
                                    <div class="topic"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></div>
                                </div>
                            </div>
                        </div>

                        <?php elseif (get_post_format() == 'link'): ?>

                        <!-- Content - Link -->
                        <div class="post small link">
                            <div class="core">
                                <div class="source"><a href="<?php the_field('link_to_url'); ?>"><?php the_field('source'); ?></a></div>
                                <div class="body">
                                    <?php the_content('Read the rest of this entry »'); ?>
                                </div>
                            </div>
                            <div class="meta">
                                <div class="timestamp"><?php the_time('F jS, Y') ?></div>
                                <div class="corner <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
                                    <div class="block"></div>
                                    <div class="topic"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></div>
                                </div>
                            </div>
                        </div>

                        <?php elseif (get_post_format() == 'image'): ?>

                        <!-- Content - dataset -->
                        <div class="post small dataset">
                            <div class="lead">
                                <div class="lead-image"><a href="<?php the_field('link_to_dataset'); ?>" target="_blank"><img class="scale-with-grid" src="<?php the_field('dataset_image'); ?>"></a></div>
                            </div>
                            <div class="core">
                                <div class="title"><a href="<?php the_field('link_to_dataset'); ?>" target="_blank"><?php the_title(); ?></a></div>
                                <div class="body">
                                    <?php the_content('Read the rest of this entry »'); ?>
                                </div>
                            </div>
                            <div class="meta">
                                <div class="timestamp"><?php the_time('F jS, Y') ?></div>
                                <div class="corner <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
                                    <div class="block"></div>
                                    <div class="topic"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></div>
                                </div>
                            </div>
                        </div>

                        <?php elseif (get_post_format() == 'gallery'): ?>

                        <!-- Content - Slide -->
                        <div class="post small blog" id="post-<?php the_ID(); ?>">
                            <?php
                            $imagefile=get_field_object('field_52432c4d9b06f');

                            ?>
                            <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>">
                            <div class="core">
                                <div class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                                <div class="body">
                                    <?php the_content('Read the rest of this entry »'); ?>
                                </div>
                            </div>
                            <div class="meta">
                                <div class="timestamp"><?php the_time('F jS, Y') ?></div>
                                <div class="corner <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
                                    <div class="block"></div>
                                    <div class="topic"><a href="<?php $category = get_the_category(); echo get_category_link($category[0]->cat_ID);?>">
                                        <?php $category = get_the_category(); echo $category[0]->cat_name; ?></a></div>
                                </div>
                            </div>
                        </div>
                        <?php elseif (get_post_format() == ''): ?>

                        <!-- Content - Blog Post -->
                        <div class="post small blog" id="post-<?php the_ID(); ?>">

                            <div class="core">
                                <div class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                                <div class="body">
                                    <?php the_content('Read the rest of this entry »'); ?>
                                </div>
                            </div>
                            <div class="meta">
                                <div class="timestamp"><?php the_time('F jS, Y') ?></div>
                                <div class="corner <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
                                    <div class="block"></div>
                                    <div class="topic">
  			              <?php 
					$slug = $wp_query->query_vars['category_name'];
					echo get_category_by_slug($slug)->name; 
				      ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php endif; ?>

                    <?php endwhile; ?>

                <?php else : ?>
                <h2 class="center">Not Found</h2>
                <p class="center">Sorry, but you are looking for something that isn't here.</p>
                <?php include (TEMPLATEPATH . "/searchform.php"); ?>
                <?php endif; ?>


            </div> <!-- posts -->
        </div> <!-- sixteen columns -->
<?php get_footer(); ?>
