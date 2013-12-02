<footer class="content-info container" role="contentinfo">




  <div class="row">
  
    <!--
    <div class="col-lg-4">
      <?php dynamic_sidebar('sidebar-footer'); ?>
      <p>&copy; <?php echo date('Y') . ' '; bloginfo('name'); ?></p>
    </div>
    -->
    
    
    <div class="col-md-4 col-lg-4">
    
        <form role="search" method="get" class="search-form form-inline" action="<?php echo home_url('/'); ?>">
          <div class="input-group">
            <label class="hide" for="search-footer"><?php _e('Search for:', 'roots'); ?></label>
            <input type="search" id="search-footer" value="<?php if (is_search()) { echo get_search_query(); } ?>" name="s" class="search-field form-control" placeholder="<?php _e('Search', 'roots'); ?> <?php bloginfo('name'); ?>">
            <span class="input-group-btn">
              <button type="submit" class="search-submit btn btn-default"><?php _e('Search', 'roots'); ?></button>
            </span>
          </div>
        </form>    
    
        
    </div>
    
    <nav class="col-md-4 col-lg-4" role="navigation">    
    <?php
      if (has_nav_menu('primary_navigation')) :
        wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav navbar-nav'));
      endif;
    ?>    
    
    <?php
      if (has_nav_menu('footer_navigation')) :
        wp_nav_menu(array('theme_location' => 'footer_navigation', 'menu_class' => 'nav navbar-nav'));
      endif;
    ?>    
    </nav>
    
    <div class="col-md-4 col-lg-4">

        <ul class="social-nav pull-right nav navbar-nav">
            <li><a href="/contact/"><i class="fa fa-twitter"></i></a></li>
            <li><a href="/contact/"><i class="fa fa-stack-exchange"></i></a></li>  
            <li><a href="/contact/"><i class="fa fa-envelope"></i></a></li>    
        </ul>

    </div>    
  </div>
</footer>

<?php wp_footer(); ?>
