<?php /*
Template Name: Datasets Published per Month
*/
?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->



<?php get_template_part('header'); ?>
<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php $category = get_the_category();
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
<body class="single page post-<?php the_ID(); ?>">



<!-- Header Background Color, Image, or Visualization

================================================== -->
<div class="menu-container">
    <div class="header-next-top" >


        <?php get_template_part('navigation'); ?>



    </div>
</div>
<div class="next-header category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
</div>


<!-- Navigation & Search
================================================== -->

<div class="container">
    <div class="next-top category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">


        <?php get_template_part('category-search'); ?>

    </div> <!-- top -->

</div>

<div class="page-nav">
</div>

<div class="container">


<div class="sixteen columns page-nav-items">
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

</div>

<!-- WordPress Content
================================================== -->

<div class="category-content">

<div class="content">

<div id="main-inner" class="dataset-inner" style="margin-top:40px;">
<div class="Appstitle" style="padding-left:5px; margin-bottom:10px">Datasets Published per Month</div>
<div class="sixteen columns">


<div class="view-content">
<table class="views-table cols-4 datasets_published_per_month_table">
<thead class="datasets_published_per_month_thead">
<tr class="datasets_published_per_month_row_tr_head">
    <th id="C_AgencyName" class="views-field views-field-title datasets_published_per_month_table_head_fields" scope="col" rowspan="2"> Agency Name </th>
    <th id="C_NumberofDatasetsampToolspublishedbymonth" class="views-field views-field-field-creation-date datasets_published_per_month_table_head_fields" scope="col" colspan="12"> Number of Datasets published by month </th>
    <th id="C_NumberofDatasetsampToolspublishedbymonth" class="views-field views-field-field-dataset-count datasets_published_per_month_table_head_fields" scope="col" rowspan="2"> Total in the Past 12 Months </th>

<tr class="datasets_published_per_month_row_tr_head" >
    <?php
    echo '<th></th>';
    for($i = 0; $i <= 11; $i++) {

        echo '<th class="datasets_published_per_month_table_head_calendar"  >';

        echo '<span class="datasets_published_month">';
        $current=12- date("m");
        if($i==0){
            $currentMonth = date('M');

        }
        else{
            $currentMonth = $currentMonth2;
        }
        $currentMonth2=Date('M', strtotime($currentMonth . " next month"));
        echo $currentMonth2;

        echo '<br>';

        $year = date("y");

        echo '<span class="datasets_published_year">';


        if($i>=$current){

            echo $year;
        }
        else
        {
            $previousyear = $year -1;
            echo $previousyear;



        }

        echo '</span>';
        echo '</th>';
    }
    ?>
</tr>
</thead>
<tbody class="datasets_published_per_month_tbody">
<div style="float: right;margin-left:280px;"> <?php the_content(); ?></div>
<?php
$metric_sync = $wpdb->get_var( "SELECT MAX(meta_value) FROM next_datagov.wp_postmeta WHERE meta_key = 'metric_sync_timestamp'");
echo '<div style="font-style:italic;">';
echo "Data last updated on: ". date("m/d/Y H:i A",$metric_sync)."<br /><br />";
?>


<?php $count=0; ?>

<?php
$args = array(
    'orderby'          => 'title',
    'order'            => 'ASC',
    'post_type'        => 'metric_organization',
    'posts_per_page' => 500,
    'post_status'      => 'publish',
    'suppress_filters' => true,
    'meta_query' => array(
        array(
            'key' => 'metric_sector',
            'value' => 'Federal',
            'compare' => 'LIKE'
        )
    )
);

$query = null;
$query = new WP_Query($args);
$count_a = 0;
if( $query->have_posts() ) {

    while ($query->have_posts()) : $query->the_post();

        $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
        $parent = get_post_meta($post->ID, "parent_agency", TRUE );
        $title = get_the_title();

        if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

            if($count/2==0){

                echo '<tr class="datasets_published_per_month_row_tr_even even">';
                echo '<td class="datasets_published_per_month_table_row_fields" style="color:#000000;text-align:left;">'; echo the_title();
                echo '</td>';
                if(get_post_meta($post->ID, 'month_1_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {

                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_1_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_1_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_2_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_2_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_2_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_3_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_3_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_3_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_4_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {

                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_4_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_4_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_5_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_5_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_5_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_6_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_6_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_6_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_7_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_7_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_7_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_8_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_8_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_8_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_9_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_9_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_9_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_10_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_10_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_10_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_11_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_11_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_11_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_12_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_12_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_12_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'metric_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'metric_url', TRUE ).'">'.get_post_meta($post->ID, 'metric_count', TRUE ).'</a>';
                }
                echo '</td>';
                echo '</tr>';
            }
            else
            {

                echo '<tr class="datasets_published_per_month_row_tr_odd odd">';
                echo '<td class="datasets_published_per_month_table_row_fields" style="color:#000000;text-align:left;">'; echo the_title();
                echo '</td>';


                if(get_post_meta($post->ID, 'month_1_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {

                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_1_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_1_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_2_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_2_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_2_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_3_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_3_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_3_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_4_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {

                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_4_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_4_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_5_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_5_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_5_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_6_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_6_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_6_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_7_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_7_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_7_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_8_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_8_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_8_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_9_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_9_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_9_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_10_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_10_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_10_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_11_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_11_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_11_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'month_12_dataset_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'month_12_dataset_url', TRUE ).'">'.get_post_meta($post->ID, 'month_12_dataset_count', TRUE ).'</a>';
                }
                echo '</td>';
                if(get_post_meta($post->ID, 'metric_count', TRUE )==0)
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'. '-';
                }
                else
                {
                    echo '<td class="datasets_published_per_month_table_row_fields">'.'<a class="link_dataset" href="'.get_post_meta($post->ID, 'metric_url', TRUE ).'">'.get_post_meta($post->ID, 'metric_count', TRUE ).'</a>';
                }
                echo '</td>';
                echo '</tr>';
            }

        }
        $count++;

        ?>
        <?php endwhile;?>
    <?php } ?>
</tbody>
<thead>
<tr>
<td style="text-align:left; ">Total</td>
<td> <?php $total1=0; ?>

    <?php

    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

                $total1=$total1 + get_post_meta($post->ID, 'month_1_dataset_count', TRUE );
            }

            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total1==0){
        echo "-";
    }
    else
    {
        echo $total1;
    }?>
</td>
<td> <?php $total2=0; ?>

    <?php
    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

                $total2=$total2 + get_post_meta($post->ID, 'month_2_dataset_count', TRUE );
            }
            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total2==0){
        echo "-";
    }
    else
    {
        echo $total2;
    }?>
</td>
<td> <?php $total3=0; ?>

    <?php
    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

                $total3=$total3 + get_post_meta($post->ID, 'month_3_dataset_count', TRUE );
            }
            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total3==0){
        echo "-";
    }
    else
    {
        echo $total3;
    }?>
</td>
<td> <?php $total4=0; ?>

    <?php
    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {


                $total4=$total4 + get_post_meta($post->ID, 'month_4_dataset_count', TRUE );
            }

            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total4==0){
        echo "-";
    }
    else
    {
        echo $total4;
    }?>
</td>
<td> <?php $total5=0; ?>

    <?php
    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

                $total5=$total5 + get_post_meta($post->ID, 'month_5_dataset_count', TRUE );
            }

            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total5==0){
        echo "-";
    }
    else
    {
        echo $total5;
    }?>
</td>
<td> <?php $total6=0; ?>

    <?php

    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {


                $total6=$total6 + get_post_meta($post->ID, 'month_6_dataset_count', TRUE );
            }

            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total6==0){
        echo "-";
    }
    else
    {
        echo $total6;
    }?>
</td>
<td> <?php $total7=0; ?>

    <?php
    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

                $total7=$total7 + get_post_meta($post->ID, 'month_7_dataset_count', TRUE );
            }

            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total7==0){
        echo "-";
    }
    else
    {
        echo $total7;
    }?>
</td>
<td> <?php $total8=0; ?>

    <?php
    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

                $total8=$total8 + get_post_meta($post->ID, 'month_8_dataset_count', TRUE );
            }

            ?>

            <?php endwhile;?>
        <?php }; ?>
    <?php
    if( $total8==0){
        echo "-";
    }
    else
    {
        echo $total8;
    }?>
</td>
<td> <?php $total9=0; ?>

    <?php
    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

                $total9=$total9 + get_post_meta($post->ID, 'month_9_dataset_count', TRUE );
            }

            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total9==0){
        echo "-";
    }
    else
    {
        echo $total9;
    }?>
</td>
<td> <?php $total10=0; ?>

    <?php
    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

                $total10=$total10 + get_post_meta($post->ID, 'month_10_dataset_count', TRUE );
            }

            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total10==0){
        echo "-";
    }
    else
    {
        echo $total10;
    }?>
</td>
<td> <?php $total11=0; ?>

    <?php
    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

                $total11=$total11 + get_post_meta($post->ID, 'month_11_dataset_count', TRUE );
            }

            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total11==0){
        echo "-";
    }
    else
    {
        echo $total11;
    }?>
</td>
<td> <?php $total12=0; ?>

    <?php
    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

                $total12=$total12 + get_post_meta($post->ID, 'month_12_dataset_count', TRUE );
            }

            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total12==0){
        echo "-";
    }
    else
    {
        echo $total12;
    }?>
</td>
<td> <?php $total=0; ?>

    <?php
    if( $query->have_posts() ) {

        while ($query->have_posts()) : $query->the_post();
            $parent_node = get_post_meta($post->ID, "parent_organization", TRUE );
            $parent = get_post_meta($post->ID, "parent_agency", TRUE );
            $title = get_the_title();

            if(($parent || !$parent_node ) && $title != "Department/Agency Level") {

                $total=$total + get_post_meta($post->ID, 'metric_count', TRUE );
            }
            ?>

            <?php endwhile;?>
        <?php } ?>
    <?php
    if( $total==0){
        echo "-";
    }
    else
    {
        echo $total;
    }?>
</td>
</tr>
</thead>
</table>

</div>









</div> <!-- sixteen columns -->

<?php get_template_part('footer'); ?>

</div> <!-- content -->
</div>
</div><!-- container -->
</div>
<script src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.joyride-2.1.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/modernizr.mq.js"></script>

<script>
    $(window).load(function(){
        $('#posts').masonry({
            // options
            columnWidth: 287,
            itemSelector : '.post',
            isResizable: true,
            isAnimated: true,
            gutterWidth: 25
        });

        $("#joyRideTipContent").joyride({
            autoStart: true,
            modal: true,
            cookieMonster: true,
            cookieName: 'datagov',
            cookieDomain: 'next.data.gov'
        });
    });
</script>


<script>
    $(function () {
        var
                $demo = $('#rotate-stats'),
                strings = JSON.parse($demo.attr('data-strings')).targets,
                randomString;

        randomString = function () {
            return strings[Math.floor(Math.random() * strings.length)];
        };

        $demo.fadeTo(randomString());
        setInterval(function () {
            $demo.fadeTo(randomString());
        }, 15000);
    });
</script>

<script src="<?php echo get_bloginfo('template_directory'); ?>/js/v1.js"></script>
<script src="<?php echo get_bloginfo('template_directory'); ?>/js/autosize.js"></script>

<!-- End Document
================================================== -->
</body>


</html>
