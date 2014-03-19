<div class="container">

    <div id="contact-wrapper">

        <div class="row intro">

            <div class="container">
                <p>
                    <?php
                    $categories = get_categories();

                    foreach ($categories as $category) {
                        echo '<h3><a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a></h3>';
                        $pages = get_pages('cat=' . $category->term_id . '&posts_per_page=-1&orderby=title&order=ASC'); //change this
                        if (sizeof($pages)) {
                            echo '<ul>';
                            foreach ($pages as $page) {
                                if (strlen(trim($page->post_title))) {
                                    echo '<li><a href="' . get_permalink($page->ID) . '">' . $page->post_title . '</a></li>';
                                }
                            }
                            echo '</ul>';
                        }
                        echo '<br />';
                    }

                    ?>
                </p>
            </div>

        </div>

    </div>

</div>