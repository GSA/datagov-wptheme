<header>
<div class="banner navbar navbar-default navbar-static-top" role="banner">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a>
    </div>

    <nav class="collapse navbar-collapse" role="navigation">
    
        <div>
            <?php if(!is_front_page()): ?>
              <?php get_search_form(); ?>
            <?php endif; ?>  
        </div>
    
        <div class="navbar-row">        
          <?php
            if (has_nav_menu('primary_navigation')) :
              wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav navbar-nav navbar-right'));
            endif;
          ?>
        </div>
  
    </nav>    
  </div>
</div>

<?php if(is_front_page()): ?>

<?php while (have_posts()) : the_post(); ?>

<div class="jumbotron">
  <div class="container">
    <?php the_content(); ?>
  </div>
</div>

<?php endwhile; ?>


<div class="header banner frontpage-search">
    <div class="container">
        <?php get_search_form(); ?>
    </div>
</div>
<?php endif; ?>


<?php if(!is_front_page()): ?>
<div class="header banner page-heading">
    <div class="container">
        <div class="page-header">
          <h1>
            <?php echo roots_title(); ?>
          </h1>
          
          <?php if (is_category()): ?>          
          <div class="tagline">
              <?php echo category_description(); ?>
          </div>
          <?php endif; ?>
          
        </div>
    </div>
</div>
<?php endif; ?>

</header>


