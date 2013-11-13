<div class="sixteen columns">
    <ul class="next-nav">
        <li class="next-us-flag">Official US Government Website</li>
        <li class="next-primary"><a href="/">Data.Gov</a></li>

        <!-- Pulling in Global Links from WP -->
        <?php
        $args = array(
            'category_name'=>'primary', 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
        wp_list_bookmarks($args); ?>
        <span id="login" class="login">
            <a href="/wp-admin" style="height:15px;">&nbsp;
                <img src="<?php echo get_bloginfo('template_directory'); ?>/images/loginimage.png" height="20px" width="20px" alt="Login Link">
            </a>
        </span>
    </ul><!-- nav -->
</div> <!-- sixteen columns -->
