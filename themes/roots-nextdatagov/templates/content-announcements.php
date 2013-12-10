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
                $args = array(
                    'category_name'=> $term_slug, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
                wp_list_bookmarks($args);
                if (strcasecmp($term_name,$term_slug)!=0) {
                    $args = array(
                        'category_name'=> $term_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
                    wp_list_bookmarks($args);
                }
                ?>
            </ul></nav></div>
</div>
<div class="container">
<!-- Featured News -->
<?php $category = get_the_category();
                $cat_slug = $category[0]->slug;
               
                ?>
<?php
               
                $args = array(

                    'announcements_and_news'=>'	new_features-10',
                );
                $apps = new WP_Query( $args );
                if( $apps->have_posts() ) {

                    while( $apps->have_posts() ) {

                        $apps->the_post();
                        ?>
<div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
  <div class="core">
    <div class="title">
      <?php the_title() ?>
    </div>
    <div class="body">
      <?php the_content() ?>
    </div>
    <br clear="all" />
  </div>
</div>
<?php
                    }
                }

                ?>
<br clear="all" />
<!-- Announcements -->
<h1>Announcements</h1>
<?php $category = get_the_category();
                $cat_slug = $category[0]->slug;
                //echo $cat_name;
                ?>
<?php //query_posts('category_name='.$cat_name ); ?>
<?php
                /*$args = array(
                                 'post_type' => 'posts',
                               'tax_query'=>	array(
                                'relation' => 'AND',
                            array(
                            'taxonomy' => 'announcements_and_news',
                            'terms' => 'announcement-10',
                            'field' => 'slug',
                            ),
                            array(
                            'taxonomy' => 'category',
                            'terms' => $cat_slug,
                            'field' => 'slug',
                            ),
                        )
                    );
            */
                $args = array(

                    'announcements_and_news'=>'announcement-10',
                );
                $apps = new WP_Query( $args );
                if( $apps->have_posts() ) {

                    while( $apps->have_posts() ) {

                        $apps->the_post();
                        ?>
<div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
  <div class="core">
    <div class="title"> <a href="<?php echo get_post_meta($post->ID, 'link_to_url', TRUE ); ?>">
      <?php the_title() ?>
      </a> </div>
    <?php $postdate=strtotime(get_post_meta($post->ID, 'field_original_post_date', TRUE )); ?>
    <span><?php echo date("m/d/y", $postdate); ?></span>
    <div class="body">
      <?php the_content() ?>
    </div>
    <br clear="all" />
  </div>
</div>
<?php
                    }
                }

                ?>

</div>