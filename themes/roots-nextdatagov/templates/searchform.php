<form role="search" method="get" class="search-form form-inline" action="<?php echo home_url('/'); ?>">
  <div class="input-group">
    <input type="search" id="search-examples" data-strings='{ "targets" : ["91,101 datasets", "409 APIs", "349 apps","137 mobile apps","175 agencies"]}' value="<?php if (is_search()) { echo get_search_query(); } ?>" name="s" class="search-field form-control" placeholder="<?php _e('Search', 'roots'); ?> <?php bloginfo('name'); ?>">
    <label class="hide"><?php _e('Search for:', 'roots'); ?></label>
    <span class="input-group-btn">
      <button type="submit" class="search-submit btn btn-default"><?php _e('Search', 'roots'); ?></button>
    </span>
  </div>
</form>
