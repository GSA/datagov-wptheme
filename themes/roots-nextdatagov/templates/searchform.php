<form role="search" method="get" class="search-form form-inline<?php if(is_front_page()): ?> no-padding col-md-12 col-lg-12<?php else:?> navbar-right navbar-nav  col-sm-6 col-md-6 col-lg-6<?php endif;?>" action="//catalog.data.gov/dataset">
  <div class="input-group">
    <?php if(!is_front_page()): ?>
      <label for="search-header" class="sr-only"><?php _e('Search for:', 'roots'); ?></label>
    <?php endif; ?>    

    <?php 
        $example_searches      = array("targets" => array("Monthly House Price Indexes", 
                                                          "Health Care Provider Charge Data", 
                                                          "Credit Card Complaints", 
                                                          "Manufacturing &amp; Trade Inventories &amp; Sales",
                                                          "Federal Student Loan Program Data"));     
        $example_searches_text = 'Example searches: ' . implode(", ", $example_searches['targets']);
    ?>

    <input type="search" id="search-header" title="<?php echo $example_searches_text ?>" data-strings='<?php echo json_encode($example_searches); ?>' value="<?php if (is_search()) { echo get_search_query(); } ?>" name="q" class="search-field form-control" placeholder="<?php _e('Search', 'roots'); ?> <?php bloginfo('name'); ?>">
      <span class="input-group-btn">
      <button type="submit" class="search-submit btn btn-default">
           <i class="fa fa-search"></i>
           <span class="sr-only"><?php _e('Search', 'roots'); ?></span>
       </button>
    </span>
  </div>
</form>
