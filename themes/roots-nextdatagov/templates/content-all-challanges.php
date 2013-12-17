<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
<div class="subnav banner">
    <div class="container">
        <nav role="navigation" class="topic-subnav">
            <ul class="nav navbar-nav">
                <?php
                // show Links associated to a community
                // we need to build $args based either term_name or term_slug
                $args = array(
                    'category_name'=> $term_slug, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
                wp_list_bookmarks($args);
                if (strcasecmp($term_name,$term_slug)!=0) {
                    $args = array(
                        'category_name'=> $term_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
                    wp_list_bookmarks($args);
                }
                ?>
            </ul></nav></div>
</div>
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
        'meta_key' => 'field_challenge_end_date',
        'meta_query'  => array(
            array(         // restrict posts based on meta values
                'key'     => 'field_challenge_end_date',  // which meta to query
                'value'   => date("Ymd"),  // value for comparison
                'compare' => '>=',          // method of comparison
                'type'    => 'DATE'  )
        ),
        'tax_query'=>	array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'category',
                'terms' => $cat_slug,
                'field' => 'slug',
            ),
        )
    );

$apps = query_posts($args);
if(count($apps) > 0){
    ?>
<div class="upcomingC">
    <h1>Open Challenges</h1>
    <?php
    while( have_posts() ) {
        the_post();
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
                        <img class="scale-with-grid" src="test">
                        <?php }?>
                    <?php the_content() ?>
                </div>
            </div>
        </div>
        <?php

    }
    ?>
</div>
    <?php
}
?>


<?php
$args = array(
    'post_type' => 'challenge',
    'meta_key' => 'field_challenge_end_date',
    'meta_query'  => array(
        array(         // restrict posts based on meta values
            'key'     => 'field_challenge_end_date',  // which meta to query
            'value'   => date("Ymd"),  // value for comparison
            'compare' => '<',          // method of comparison
            'type'    => 'DATE'  )
    ),
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if(count($apps) > 0) {
    ?>
    <div class="closedC">
        <h1>Completed Challenges</h1>
        <?php
        while( have_posts() ) {
            the_post();
            $winner = get_field_object('field_5241b50e67153');
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
                            <img class="scale-with-grid" src="test">
                            <?php }  ?>
                        <?php the_content() ?>
                    </div>
                    <?php  if (empty ($winner['value'])  ) { ?>
                    <div><p>Winner!</p></div>
                    <?php  } ?>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}
?>
