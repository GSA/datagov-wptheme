<?php include('category-subnav.php'); ?>

<div class="wrap container">
<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta-author'); ?>
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
    <?php
      $author_byline = false;
      if(function_exists('get_field')) {
        $author_byline = get_field('author_byline');
      }
      if(!empty($author_byline))?>
          <em><?php echo $author_byline;?></em>
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
