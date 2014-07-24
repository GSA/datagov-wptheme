<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
$allowed_slug_arrays = array("climate-ecosystems","coastalflooding","energysupply","foodsupply","humanhealth","transportation","water","climate");

?>
<?php include('category-subnav.php'); ?>

<div class="container">
    <?php
    while( have_posts() ) {
        the_post();
        ?>
          <div class="Apps-wrapper">
          <div class="Apps-post" id="post-<?php the_ID(); ?>">
           <div id="appstitle" class="Appstitle" ><?php the_title();?></div>
        <?php the_content();   ?>
        <?php }?>
</div>
</div>

    <?php

    $args = array(
        'post_type' => 'challenge',
        'tax_query'=>	array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'category',
                'terms' => $cat_slug,
                'field' => 'slug',
            ),
        )
    );


    $apps = new WP_Query($args);
    $countOpen =0;
    if($apps->found_posts > 0) {
        while( $apps->have_posts() ) {
            $apps->the_post();
            $curr_date=strtotime(date('Ymd', time()));
            $start_date=strtotime(get_post_meta($post->ID, 'field_challenge_start_date', TRUE ));
            $end_date=strtotime(get_post_meta($post->ID, 'field_challenge_end_date', TRUE ));
            $winner= get_field_object('field_5241b50e67153');
            if(($curr_date<= $end_date)||(empty($end_date) && empty ($winner['value']))){
                $countOpen++;
            }
        }

    }
    if($countOpen > 0){
        ?>
        <div class="upcomingC">
            <div class="Appstitle" style="margin-bottom:-20px;">Open Challenges</div>
            <?php
            while( $apps->have_posts() ) {
                $apps->the_post();
                $curr_date=strtotime(date('Ymd', time()));
                $start_date=strtotime(get_post_meta($post->ID, 'field_challenge_start_date', TRUE ));
                $end_date=strtotime(get_post_meta($post->ID, 'field_challenge_end_date', TRUE ));
                $winner= get_field_object('field_5241b50e67153');
                if(($curr_date<= $end_date)||(empty($end_date) && empty ($winner['value']))){
                    ?>
                    <div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
                        <div class="core">
                            <div class="title"> <a href="<?php echo get_post_meta($post->ID, 'field_challenge_url', TRUE ); ?>">
                                <?php the_title() ?>
                            </a> <br/>
                            </div>
                            <div class="body">
                                <?php
                                $imagefile=get_field_object('field_5241b4eb20cea');
                                $image=  strlen($imagefile['value']['url']);
                                if ($image>0){ ?>
                                    <img class="scale-with-grid" src="<?php echo  $imagefile['value']['url']; ?>" style="float:right; margin-left:10px; height:80px;" alt="<?php echo $imagefile['value']['alt']; ?>">
                                    <?php }else{?>
                                    <img class="scale-with-grid noImage" src="">
                                    <?php }?>
                                <?php the_content() ?>
                            </div>
                        </div>
                    </div>
                    <?php

                }
            }
            ?>
        </div>
        <?php
    }
    ?>


    <?php
    $args2 = array(
        'post_type' => 'challenge',
        'meta_key' => 'field_challenge_start_date',
        'meta_value' => ' ',
        'meta_compare' => '!=',
        'tax_query'=>	array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'category',
                'terms' => $cat_slug,
                'field' => 'slug',
            ),
        )
    );



    $apps2 = new WP_Query($args2);
    $countCompleted =0;
    if($apps2->found_posts > 0) {
        while( $apps2->have_posts() ) {
            $apps2->the_post();
            $curr_date=strtotime(date('Ymd', time()));
            $start_date=strtotime(get_post_meta($post->ID, 'field_challenge_start_date', TRUE ));
            $end_date=strtotime(get_post_meta($post->ID, 'field_challenge_end_date', TRUE ));
            $winner= get_field_object('field_5241b50e67153');
            if(($curr_date> $end_date)||(empty($end_date) && !empty ($winner['value']))){
                $countCompleted++;
            }
        }

    }


    if($countCompleted > 0){
        ?>
        <div class="closedC">
            <div class="Appstitle">Completed Challenges</div>
            <?php
            while( $apps2->have_posts() ) {
                $apps2->the_post();
                $curr_date=strtotime(date('Ymd', time()));
                $start_date=strtotime(get_post_meta($post->ID, 'field_challenge_start_date', TRUE ));
                $end_date=strtotime(get_post_meta($post->ID, 'field_challenge_end_date', TRUE ));
                $winner= get_field_object('field_5241b50e67153');
                if(($curr_date> $end_date)||(empty($end_date) && !empty ($winner['value']))){
                    ?>
                    <div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
                        <div class="core">
                            <div class="title"> <a href="<?php echo get_post_meta($post->ID, 'field_challenge_url', TRUE ); ?>">
                                <?php the_title() ?>
                            </a><br/>
                            </div>
                            <div class="body">
                                <?php
                                $imagefile=get_field_object('field_5241b4eb20cea');
                                ?>
                                <?php
                                $image=  strlen($imagefile['value']['url']);
                                if ($image>0){ ?>
                                    <img class="scale-with-grid" src="<?php echo  $imagefile['value']['url']; ?>" style="float:right; margin-left:10px; height:80px;" alt="<?php echo $imagefile['value']['alt']; ?>">
                                    <?php }else{?>
                                    <img class="scale-with-grid noImage" src="">
                                    <?php }  ?>
                                <?php the_content() ?>
                            </div>
                            <?php  if (!empty ($winner['value'])  ) { ?>
                            <div><p style="margin-top:5px;"><img width="30px" height="30px" src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/ribbon.png">&nbsp;Winner Announced!</p></div>
                            <?php  } ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <?php
    }
    ?>
