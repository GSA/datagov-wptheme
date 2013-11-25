<span id="search-prompt" class="col-md-5 col-lg-5">Search 89,234 datasets including</span>
<form role="search" method="get" class="search-form form-inline col-md-7 col-lg-7 pull-right" action="<?php echo home_url('/'); ?>">
  <div class="input-group">
    <input type="search" id="search-examples" data-strings='{ "targets" : ["Monthly House Price Indexes", "Health Care Provider Charge Data", "Credit Card Complaints", "Manufacturing & Trade Inventories & Sales","Federal Student Loan Program Data"]}' value="<?php if (is_search()) { echo get_search_query(); } ?>" name="s" class="search-field form-control" placeholder="<?php _e('Search', 'roots'); ?> <?php bloginfo('name'); ?>">
    <label class="hide"><?php _e('Search for:', 'roots'); ?></label>
    <span class="input-group-btn">
      <button type="submit" class="search-submit btn btn-default"><?php _e('Search', 'roots'); ?></button>
    </span>
  </div>
</form>
