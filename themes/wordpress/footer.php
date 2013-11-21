<div class="sixteen columns footer">
    <div class="twelve columns alpha">
        <p>
            <?php
            $args = array(
                'category_name'=>'footer', 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating','before'=>' ','after'=>' ');
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
