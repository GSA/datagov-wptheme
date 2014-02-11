<div class="container">

    <div id="suggest-wrapper">
        <div class="col-md-12">
            <div id="suggest-dataset-form">
                <?php


                if ($page_id = get_page_by_path('suggest-dataset')) {

                    query_posts(array('page_id' => $page_id->ID)); // ID of the page including the form

                    if (have_posts()) : while (have_posts()) : the_post();
                        the_content();
                    endwhile; endif;

                    wp_reset_query();

                }
                ?>
            </div>
        </div>

    </div>
</div>