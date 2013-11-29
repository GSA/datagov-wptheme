<footer class="content-info container" role="contentinfo">




  <div class="row">
    <div class="col-lg-4">
      <?php dynamic_sidebar('sidebar-footer'); ?>
      <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></p>
    </div>
    <div class="col-lg-4">

        <ul class="social-nav pull-right nav navbar-nav">
            <li><a href="/contact/"><i class="fa fa-twitter"></i></a></li>
            <li><a href="/contact/"><i class="fa fa-stack-exchange"></i></a></li>  
            <li><a href="/contact/"><i class="fa fa-envelope"></i></a></li>    
        </ul>

    </div>    
  </div>
</footer>

<?php wp_footer(); ?>
