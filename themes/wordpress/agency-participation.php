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
                </div>

                This report is also available for download in the following formats: <a href="/wp-content/uploads/agency-list.csv"> CSV </a> | <a href="/wp-content/uploads/agency-list.xls"> EXCEL </a><br/>
                <?php
                $metric_sync = $wpdb->get_var( "SELECT MAX(meta_value) FROM next_datagov.wp_postmeta WHERE meta_key = 'metric_sync_timestamp'");
                echo "Data last updated on: ". date("Y-m-d H:i A",$metric_sync)."<br />";



                $total_agencies = $wpdb->get_var( "SELECT count(*) FROM next_datagov.wp_posts where post_type = 'metric_organization' and post_title <> 'Department/Agency Level'");
                echo "Agencies and Subagencies: " .$total_agencies;



                ?>

                <h2 class="fieldcontentregion agencytitle">Departments/Agencies/Organizations</h2>
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
                            <?php $count=0; ?>

                            <?php
                            $args = array(
                                'orderby'          => '',
                                'order'            => 'ASC',
                                'post_type'        => 'metric_organization',
                                'posts_per_page' => 500,
                                'post_status'      => 'publish',
                                'suppress_filters' => true );

                            $query = null;
                            $query = new WP_Query($args);
                            $count_a = 0;


                            if( $query->have_posts() ) {

                                while ($query->have_posts()) : $query->the_post();

                                    $parent = get_post_meta($post->ID, 'parent_agency', TRUE );
                                    if($parent){
                                        echo '<tr class="datasets_published_per_month_row_tr_even odd">';
                                    }
                                    else
                                    {
                                        echo '<tr class="datasets_published_per_month_row_tr_even even">';
                                    }
                                    echo '<td class="datasets_published_per_month_table_row_fields" width="60%">'; echo '<a href="'.get_post_meta($post->ID, 'metric_url', TRUE ).'">'.get_the_title().'</a>';
                                    echo '</td>';

                                    echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">';
                                    if(get_post_meta($post->ID, 'metric_count', TRUE))
                                        echo get_post_meta($post->ID, 'metric_count', TRUE);
                                    else
                                        echo "-";

                                    echo '</td>';
                                    echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="center">';
                                    if(get_post_meta($post->ID, 'metric_last_entry', TRUE))
                                        echo get_post_meta($post->ID, 'metric_last_entry', TRUE);
                                    else
                                        echo "-";
                                    echo '</td>';
                                    echo '</tr>';


                                    ?>
                                    <?php endwhile;?>
                                <?php } ?>
                            </tbody>
                            <thead>
                            <tr>
                                <td width="60%">Total</td>
                                <td  width="20%" align="center">
                                    <?php
                                    $total = 0;
                                    if( $query->have_posts() ) {

                                        while ($query->have_posts()) : $query->the_post();
                                            $parent = get_post_meta($post->ID, 'parent_agency', TRUE );
                                            if($parent)
                                                $total=$total + get_post_meta($post->ID, 'metric_count', TRUE );

                                            ?>

                                            <?php endwhile;?>
                                        <?php } ?>

                                    <?php echo $total;?> </td>
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