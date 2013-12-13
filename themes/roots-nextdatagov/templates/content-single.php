<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<div class="subnav banner">
    <div class="container">
        <nav role="navigation" class="topic-subnav">
            <ul class="nav navbar-nav">
                <?php
                // show Links associated to a community
                // we need to build $args based either term_name or term_slug
                $args = array(
                              'category_name'=> $term_slug, 
                              'categorize'=>0, 
                              'title_li'=>0,
                              'orderby'=>'rating');

                wp_list_bookmarks($args);

                if (strcasecmp($term_name,$term_slug)!=0) {
                    $args = array(
                                  'category_name'=> $term_name, 
                                  'categorize'=>0, 
                                  'title_li'=>0,
                                  'orderby'=>'rating');
                    wp_list_bookmarks($args);
                }
                ?>
            </ul>
        </nav>
    </div>
</div>
<div class="wrap container">
<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta'); ?>
    </header>

    <div class="entry-content">

      <?php if ( has_post_thumbnail() ) : ?>
        <span class="inline-image">
            <?php the_post_thumbnail('medium'); ?>
        </span>
      <?php endif; ?>

      <?php the_content(); ?>
    </div>

    <?php if(get_post_format() == 'image'): ?>    
            <div class="dataset-link">
                <a class="btn btn-default pull-right" href="<?php the_field('link_to_dataset'); ?>">
                  <span class="glyphicon glyphicon-download"></span> View this Dataset
                </a>
            </div>            
    <?php endif;?>  

    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
  </article>
    <?php $format = get_post_format();
    if ( false === $format ){
        $format = 'standard';
    }

    ?>
    <?php if ($format=='standard'){?>

        <?php comments_template('/templates/comments.php'); ?>

    <?php } ?>


        
    



<?php endwhile; ?>
</div>