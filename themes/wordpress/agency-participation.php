<?php /*
Template Name: Agency Participation
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
<div class="content">
<div class="sixteen columns">
    <?php
    while( have_posts() ) {
        the_post();
        ?>



        <div id="appstitle" class="Appstitle"  style="margin-left:-30px;"><?php the_title();?></div>


        <?php }?>


</div>
<div style="float: LEFT;">
    This report is also available for download in the following formats: <a href="/wp-content/uploads/federal-agency-participation.csv"> CSV </a> | <a href="/wp-content/uploads/federal-agency-participation.xls"> EXCEL </a><br/><br/>
</div><div style="float: right;margin-left:280px;"> <?php the_content(); ?>    </div>
<?php
$metric_sync = $wpdb->get_var( "SELECT MAX(meta_value) FROM next_datagov.wp_postmeta WHERE meta_key = 'metric_sync_timestamp'");
echo '<div style="font-style:italic;clear:both;">';
echo "Data last updated on: ". date("m/d/Y H:i A",$metric_sync)."<br />";
echo "</div>";
?>

<div id="open-data-sites-boxes" class="agencies">
    <div class="open-data-sites-box">
        <div class="region">Agencies and Subagencies:</div><div class="numbers">
        <?php
        $total_agencycount = $wpdb->get_var("SELECT count(*) FROM next_datagov.wp_postmeta where meta_key = 'metric_count' and meta_value > 0;");
        $department_level = $wpdb->get_var( "SELECT count(*) FROM next_datagov.wp_posts where post_type = 'metric_organization' and post_title = 'Department/Agency Level'");
        $total_agencies = $total_agencycount - $department_level;
        echo  $total_agencies;

        ?>

    </div>
    </div>
</div>
<div class="clear"> </div>

<h3 class="fieldcontentregion agencytitle" style="margin-left:-1px;">Departments/Agencies/Organizations</h3>



<div class="view-content">
<table class="views-table cols-4 datasets_published_per_month_table">
<thead class="datasets_published_per_month_thead">
<tr class="datasets_published_per_month_row_tr_head">
    <th id="C_AgencyName" class="views-field views-field-title datasets_published_per_month_table_head_fields" scope="col" width="60%" align="left" >Agency/Sub-Agency/Organization </th>
    <th id="C_NumberofDatasetsampToolspublishedbymonth" class="views-field views-field-field-creation-date datasets_published_per_month_table_head_fields" scope="col" width="20%" > Datasets </th>
    <th id="C_NumberofDatasetsampToolspublishedbymonth" class="views-field views-field-field-dataset-count datasets_published_per_month_table_head_fields" scope="col" width="60%" > Latest Entry</th>
</tr>

</thead>
<tbody class="datasets_published_per_month_tbody">
<?php
$count=0;

?>



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
$query = new WP_Query( $args );


if( $query->have_posts() ) {

    while ($query->have_posts()) : $query->the_post();

        $id =  $post->ID;
        $parent = get_post_meta($post->ID, 'parent_agency', TRUE);
        $parent_org = get_post_meta($post->ID, 'parent_organization', TRUE);
        $agency_title = get_the_title();




        if($parent){

            $subargs = array(
                'orderby'          => 'title',
                'order'            => 'ASC',
                'post_type'        => 'metric_organization',
                'posts_per_page' => 500,
                'post_status'      => 'publish',
                'suppress_filters' => true,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'parent_organization',
                        'value' => $id,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => 'metric_sector',
                        'value' => 'Federal',
                        'compare' => 'LIKE'
                    )
                )
            );

            $subquery = null;
            $subquery = new WP_Query($subargs);
            $agency_title = get_the_title();
            $dataset_count = get_post_meta($post->ID, 'metric_count', TRUE);
            $last_entry = get_post_meta($post->ID, 'metric_last_entry', TRUE);


            if($dataset_count > 0){

                echo '<tr class="datasets_published_per_month_row_tr_even odd">';
                echo '<td class="datasets_published_per_month_table_row_fields" width="60%">'; echo '<a href="'.get_post_meta($post->ID, 'metric_url', TRUE ).'">'.$agency_title.'</a>';
                echo '</td>';

                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">';echo get_post_meta($post->ID, 'metric_count', TRUE);
                echo '</td>';

                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">'; echo get_post_meta($post->ID, 'metric_last_entry', TRUE);
                echo '</td>';
                echo '</tr>';
            }

            if( $subquery->have_posts() ) {

                while ($subquery->have_posts()) : $subquery->the_post();

                    $title = get_the_title();
                    $dataset_count = get_post_meta($post->ID, 'metric_count', TRUE);
                    $last_entry = get_post_meta($post->ID, 'metric_last_entry', TRUE);



                    if($title != $agency_title && $dataset_count > 0 && $title == 'Department/Agency Level'){

                        echo '<tr class="datasets_published_per_month_row_tr_even even">';

                        echo '<td style="text-indent: 10px;" class="datasets_published_per_month_table_row_fields" width="60%">'; echo '<a href="'.get_post_meta($post->ID, 'metric_url', TRUE ).'">'.get_the_title().'</a>';
                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">'; echo get_post_meta($post->ID, 'metric_count', TRUE);
                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">';
                        if(get_post_meta($post->ID, 'metric_last_entry', TRUE))
                            echo get_post_meta($post->ID, 'metric_last_entry', TRUE);
                        else
                            echo "-";
                        echo '</td>';
                        echo '</tr>';

                    }

                endwhile;
            }


            if( $subquery->have_posts() ) {

                while ($subquery->have_posts()) : $subquery->the_post();

                    $title = get_the_title();
                    $dataset_count = get_post_meta($post->ID, 'metric_count', TRUE);
                    $last_entry = get_post_meta($post->ID, 'metric_last_entry', TRUE);



                    if($title != $agency_title && $dataset_count > 0 && $title != 'Department/Agency Level'){


                        echo '<tr class="datasets_published_per_month_row_tr_even even">';


                        echo '<td style="text-indent: 10px;"class="datasets_published_per_month_table_row_fields" width="60%">';  echo '<a href="'.get_post_meta($post->ID, 'metric_url', TRUE ).'">'.get_the_title().'</a>';
                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">'; echo get_post_meta($post->ID, 'metric_count', TRUE);
                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">';
                        if(get_post_meta($post->ID, 'metric_last_entry', TRUE))
                            echo get_post_meta($post->ID, 'metric_last_entry', TRUE);
                        else
                            echo "-";
                        echo '</td>';
                        echo '</tr>';

                    }


                endwhile;
            }


        }
        else if(!$parent_org){


            $dataset_count = get_post_meta($post->ID, 'metric_count', TRUE);
            $agency_title = get_the_title();
            $last_entry = get_post_meta($post->ID, 'metric_last_entry', TRUE);



            if($dataset_count > 0){



                echo '<tr class="datasets_published_per_month_row_tr_even odd">';
                echo '<td class="datasets_published_per_month_table_row_fields" width="60%">'; echo '<a href="'.get_post_meta($post->ID, 'metric_url', TRUE ).'">'.get_the_title().'</a>';
                echo '</td>';

                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">';echo get_post_meta($post->ID, 'metric_count', TRUE);
                echo '</td>';

                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">';
                if(get_post_meta($post->ID, 'metric_last_entry', TRUE))
                    echo get_post_meta($post->ID, 'metric_last_entry', TRUE);
                else
                    echo "-";
                echo '</td>';
                echo '</tr>';
            }

        }


    endwhile;
}

?>



</tbody>
</table>
</div>
</div>
<h3 class="fieldcontentregion agencytitle">Other Agencies</h3>
<div class="sixteen columns">
    <div class="view-content">
        <table class="views-table cols-4 datasets_published_per_month_table">
            <thead class="datasets_published_per_month_thead">
            <tr class="datasets_published_per_month_row_tr_head">
                <th id="C_AgencyName" class="views-field views-field-title datasets_published_per_month_table_head_fields" scope="col" width="60%" align="left" >Agency/Sub-Agency/Organization </th>
                <th id="C_NumberofDatasetsampToolspublishedbymonth" class="views-field views-field-field-creation-date datasets_published_per_month_table_head_fields" scope="col" width="20%" > Datasets </th>
                <th id="C_NumberofDatasetsampToolspublishedbymonth" class="views-field views-field-field-dataset-count datasets_published_per_month_table_head_fields" scope="col" width="60%" > Latest Entry</th>
            </tr>

            </thead>
            <tbody class="datasets_published_per_month_tbody">


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
                        'value' => 'Other',
                        'compare' => 'LIKE'
                    )
                )
            );
            $query = null;
            $query = new WP_Query( $args );


            if( $query->have_posts() ) {

                while ($query->have_posts()) : $query->the_post();

                    $id =  $post->ID;
                    $parent = get_post_meta($post->ID, 'parent_agency', TRUE);
                    $parent_org = get_post_meta($post->ID, 'parent_organization', TRUE);
                    $agency_title = get_the_title();


                    if($parent){

                        $subargs = array(
                            'orderby'          => 'title',
                            'order'            => 'ASC',
                            'post_type'        => 'metric_organization',
                            'posts_per_page' => 500,
                            'post_status'      => 'publish',
                            'suppress_filters' => true,
                            'meta_query' => array(
                                'relation' => 'AND',
                                array(
                                    'key' => 'parent_organization',
                                    'value' => $id,
                                    'compare' => 'LIKE'
                                ),
                                array(
                                    'key' => 'metric_sector',
                                    'value' => 'Other',
                                    'compare' => 'LIKE'
                                )
                            )
                        );


                        $subquery = null;
                        $subquery = new WP_Query($subargs);
                        $agency_title = get_the_title();
                        $dataset_count = get_post_meta($post->ID, 'metric_count', TRUE);
                        $last_entry = get_post_meta($post->ID, 'metric_last_entry', TRUE);


                        if($dataset_count > 0){

                            echo '<tr class="datasets_published_per_month_row_tr_even odd">';
                            echo '<td class="datasets_published_per_month_table_row_fields" width="60%">'; echo '<a href="'.get_post_meta($post->ID, 'metric_url', TRUE ).'">'.$agency_title.'</a>';
                            echo '</td>';

                            echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">';echo get_post_meta($post->ID, 'metric_count', TRUE);
                            echo '</td>';

                            echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">'; echo get_post_meta($post->ID, 'metric_last_entry', TRUE);
                            echo '</td>';
                            echo '</tr>';
                        }


                        if( $subquery->have_posts() ) {

                            while ($subquery->have_posts()) : $subquery->the_post();

                                $title = get_the_title();
                                $dataset_count = get_post_meta($post->ID, 'metric_count', TRUE);
                                $last_entry = get_post_meta($post->ID, 'metric_last_entry', TRUE);

                                if($title != $agency_title && $dataset_count > 0 && $title != 'Department/Agency Level'){

                                    echo '<tr class="datasets_published_per_month_row_tr_even even">';

                                    echo '<td style="text-indent: 10px;" class="datasets_published_per_month_table_row_fields" width="60%">'; echo '<a href="'.get_post_meta($post->ID, 'metric_url', TRUE ).'">'.get_the_title().'</a>';
                                    echo '</td>';

                                    echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">'; echo get_post_meta($post->ID, 'metric_count', TRUE);
                                    echo '</td>';

                                    echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">';
                                    if(get_post_meta($post->ID, 'metric_last_entry', TRUE))
                                        echo get_post_meta($post->ID, 'metric_last_entry', TRUE);
                                    else
                                        echo "-";
                                    echo '</td>';
                                    echo '</tr>';

                                }

                            endwhile;
                        }

                    }
                    else if(!$parent_org){


                        $dataset_count = get_post_meta($post->ID, 'metric_count', TRUE);

                        $last_entry = get_post_meta($post->ID, 'metric_last_entry', TRUE);


                        if($dataset_count > 0){

                            echo '<tr class="datasets_published_per_month_row_tr_even odd">';
                            echo '<td class="datasets_published_per_month_table_row_fields" width="60%">'; echo '<a href="'.get_post_meta($post->ID, 'metric_url', TRUE ).'">'.$agency_title.'</a>';
                            echo '</td>';

                            echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">';echo get_post_meta($post->ID, 'metric_count', TRUE);
                            echo '</td>';

                            echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">'; echo get_post_meta($post->ID, 'metric_last_entry', TRUE);
                            echo '</td>';
                            echo '</tr>';
                        }


                    }
                endwhile;
            }

            ?>
            </tbody>
        </table>
    </div>
</div>
<h3 class="fieldcontentregion agencytitle">Summary</h3>
<div class="sixteen columns">

    <div class="view-content">

        <table class="views-table cols-4 datasets_published_per_month_table">
            <thead>
            <tr>
                <td width="60%">Total</td>
                <td  width="20%" align="center">
                    <?php
                    $total = 0;


                    $args = array(
                        'orderby'          => '',
                        'order'            => 'ASC',
                        'post_type'        => 'metric_organization',
                        'posts_per_page' => 500,
                        'post_status'      => 'publish',
                        'suppress_filters' => true


                    );

                    $query = new WP_Query( $args );

                    if( $query->have_posts() ) {

                        while ($query->have_posts()) : $query->the_post();
                            $parent = get_post_meta($post->ID, 'parent_agency', TRUE );
                            $parent_node = get_post_meta($post->ID, 'parent_organization', TRUE );
                            $cfo = get_post_meta($post->ID, 'metric_sector', TRUE);
                            if($parent || !$parent_node){

                                $total=$total + get_post_meta($post->ID, 'metric_count', TRUE );

                            }


                            ?>

                            <?php endwhile;?>
                        <?php } ?>

                    <?php


                    echo $total;

                    ?> </td>
                <td  width="20%" align="center">
                    <?php

                    $last_entry = $wpdb->get_var( "SELECT MAX(meta_value) FROM next_datagov.wp_postmeta WHERE meta_key = 'metric_last_entry'");

                    echo $last_entry;



                    ?>




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



