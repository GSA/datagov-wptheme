<div class="sixteen columns footer">
    <div class="twelve columns alpha">
        <p>
            <?php
            if ( is_user_logged_in() ) {
                $args = array('category_name'=>'footer', 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating','before'=>' ','after'=>' ','exclude'=>'269');
            } else {
                $args = array('category_name'=>'footer', 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating','before'=>' ','after'=>' ','exclude'=>'273');
            }
            wp_list_bookmarks($args);
            ?>
        </p>
    </div>
    <div class="four columns omega right-align">
        <p>Next.Data.Gov</p>
    </div>
</div>

<!-- End Document
================================================== -->
<?php wp_footer(); ?>
</body>
</html>
