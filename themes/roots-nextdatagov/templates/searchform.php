<form role="search" method="get" class="search-form form-inline<?php if(is_front_page()): ?> col-md-12 col-lg-12<?php else:?> navbar-right navbar-nav  col-sm-6 col-md-6 col-lg-6<?php endif;?>" action="<?php echo home_url('/'); ?>">
  <div class="input-group">
    <label for="search-header" class="hide"><?php _e('Search for:', 'roots'); ?></label>
    <input type="search" id="search-header" data-strings='{ "targets" : ["Monthly House Price Indexes", "Health Care Provider Charge Data", "Credit Card Complaints", "Manufacturing &amp; Trade Inventories & Sales","Federal Student Loan Program Data"]}' value="<?php if (is_search()) { echo get_search_query(); } ?>" name="s" class="search-field form-control" placeholder="<?php _e('Search', 'roots'); ?> <?php bloginfo('name'); ?>">
    <span class="input-group-btn">
      <button type="submit" class="search-submit btn btn-default">
           <i class="fa fa-search"></i>
           <span class="sr-only"><?php _e('Search', 'roots'); ?></span>
       </button>
    </span>
  </div>
</form>
