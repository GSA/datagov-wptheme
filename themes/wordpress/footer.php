<div class="sixteen columns footer">
    <div class="twelve columns alpha">
        <p>
            <?php
            $page = get_bookmarks(array('category_name' => 'footer'));
            foreach ($page as $bookmark){
                if($bookmark->link_name == "Login")
                    $loginid = $bookmark->link_id;
                if($bookmark->link_name == "Logout")
                    $logoutid = $bookmark->link_id;
            }
            if ( is_user_logged_in() ) {
                $args = array('category_name'=>'footer', 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating','before'=>' ','after'=>' ','exclude'=>$loginid);
            } else {
                $args = array('category_name'=>'footer', 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating','before'=>' ','after'=>' ','exclude'=>$logoutid);
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
