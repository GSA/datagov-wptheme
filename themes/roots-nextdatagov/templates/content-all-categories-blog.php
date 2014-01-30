<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
<div class="subnav banner">
    <div class="container">
        <nav role="navigation" class="topic-subnav">
            <ul class="nav navbar-nav">
                <?php
                // show Links associated to a community
                // we need to build $args based either term_name or term_slug
                if(!empty($term_slug)){
                    $args = array(
                        'category_name'=> $term_slug, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
                    wp_list_bookmarks($args);
                }
                if (strcasecmp($term_name,$term_slug)!=0) {
                    $args = array(
                        'category_name'=> $term_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
                    wp_list_bookmarks($args);
                }
                ?>
            </ul></nav></div>
</div>
<div class="single">
    <div class="container">



        <?php
        global $categoryname;
        $category = get_the_category( $custompost );
        $categoryname=$category[0]->cat_name;


        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $terms_array = array('post-format-link','post-format-status','post-format-image','post-format-gallery');
        $args =  $args = array(
            'tax_query' => array(
                array(
                    'taxonomy' => 'post_format',
                    'field' => 'slug',
                    'terms' => $terms_array,
                    'operator' => 'NOT IN'
                )
            ),
            'posts_per_page' => 3,
            'paged' => $paged,
            'category_name'=> 'Uncategorized' );
        $apps = new WP_Query( $args );
        $my_post_count = $apps->post_count;
        $apps = new WP_Query( $args );
        $my_post_count = $apps->post_count;


        ?>
        <div class="catimg"  style="padding-top:30px;padding-bottom:10px; ">

            <span style="font-size:25px;text-transform:uppercase;color:#808080;line-height:40%;vertical-align:bottom;"><?php echo "DATA.GOV" ?></span></div>
        <div  class="horizontal_dotted_line_all"></div>
        <br>
        <?php

        if ($apps->have_posts()) : ?>
            <?php while ($apps->have_posts()) : $apps->the_post(); ?>



                <div id="cat-posts" class="single-cat-post ">



                    <!-- Content - Blog Post -->
                    <div class="category-wrapper_all">

                        <div class="new-cat-post" id="post-<?php the_ID(); ?>">


                            <div class=" title  "><?php the_title(); ?></div>
                            <div style="color:#808080; font-size:16px;margin-left:2px;  "><span style="text-transform:uppercase;"><?php $author = get_the_author(); echo $author;?></span>&nbsp;// <?php the_time('M jS Y ') ?> </div>
                            <br/>
                            <div class="body">
                                <?php
                                $words = explode(" ",strip_tags(get_the_content()));
                                $content = implode(" ",array_splice($words,0,50));
                                echo $content;
                                ?>
                                <br>
                                <a href="<?php echo get_permalink(); ?>" style="font-weight:bold;float:right;text-transform:uppercase;" class="<?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?> ">Continued</a>
                            </div>





                        </div>
                    </div>
                </div> <!-- posts -->

                <div  class="horizontal_dotted_line_all"></div>
                <br>

                <?php endwhile; ?>
            <nav class="post-nav">
                <?php your_pagination($apps) ;?>
            </nav>

            <br clear="all" />
            <?php else : ?>
            <h2 class="center">Not Found</h2>
            <p class="center">Sorry, but you are looking for something that isn't here.</p>

            <?php endif; ?>






    </div>
</div>


