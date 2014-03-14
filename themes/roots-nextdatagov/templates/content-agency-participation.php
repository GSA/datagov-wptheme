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
                if (!empty($term_slug)) {
                    $args = array(
                        'category_name' => $term_slug, 'categorize' => 0, 'title_li' => 0, 'orderby' => 'rating');
                    wp_list_bookmarks($args);
                }
                if (strcasecmp($term_name, $term_slug) != 0) {
                    $args = array(
                        'category_name' => $term_name, 'categorize' => 0, 'title_li' => 0, 'orderby' => 'rating');
                    wp_list_bookmarks($args);
                }
                ?>
            </ul>
        </nav>
    </div>
</div>

<div class="single">
<div class="container">
<?php
while (have_posts()) {
    the_post();
    ?>



    <div id="appstitle" class="Appstitle" style="margin-left:-20px;"><?php the_title(); ?></div>


<?php } ?>



<div style="float:left;">
    This report is also available for download in the following formats: <a
        href="/media/federal-agency-participation.csv"> CSV </a> | <a href="/media/federal-agency-participation.xls">
        EXCEL </a><br/><br/>
</div>
<div style=""> <?php the_content(); ?>    </div>
<?php
$metric_sync = $wpdb->get_var("SELECT MAX(meta_value) FROM wp_postmeta WHERE meta_key = 'metric_sync_timestamp'");
echo '<div style="font-style:italic;clear:both;">';
echo "Data last updated on: " . date("m/d/Y H:i A", $metric_sync) . "<br />";
echo "</div>";
?>

<div id="open-data-sites-boxes" class="agencies">
    <div class="open-data-sites-box">
        <div class="region">Agencies and Subagencies:</div>
        <div class="numbers">
            <?php
            $total_agencycount = $wpdb->get_var("SELECT count(*) FROM wp_postmeta where meta_key = 'metric_count' and meta_value > 0;");
            $department_level = $wpdb->get_var("SELECT count(*) FROM wp_postmeta pm where pm.meta_key = 'metric_count' and pm.meta_value > 0 and post_id in(
                                                             SELECT ID from wp_posts where  post_type = 'metric_organization' and post_title = 'Department/Agency Level')");
            $total_agencies = $total_agencycount - $department_level;
            echo number_format($total_agencies);

            ?>

        </div>
    </div>
</div>
<div class="clear"></div>
<br/>
<br/>

<h3 class="fieldcontentregion agencytitle" style="margin-left:-1px;">Departments/Agencies/Organizations</h3>


<div class="view-content">
<table class="views-table cols-4 datasets_published_per_month_table">
<thead class="datasets_published_per_month_thead">
<tr class="datasets_published_per_month_row_tr_head">
    <th id="C_AgencyName" class="views-field views-field-title datasets_published_per_month_table_head_fields"
        scope="col" width="60%" align="left">Agency/Sub-Agency/Organization
    </th>
    <th id="C_NumberofDatasetsampToolspublishedbymonth"
        class="views-field views-field-field-creation-date datasets_published_per_month_table_head_fields" scope="col"
        width="20%"> Datasets
    </th>
    <th id="C_NumberofDatasetsampToolspublishedbymonth"
        class="views-field views-field-field-dataset-count datasets_published_per_month_table_head_fields" scope="col"
        width="20%"> Latest Entry
    </th>
</tr>

</thead>
<tbody class="datasets_published_per_month_tbody metrics">
<?php
$count = 0;

?>



<?php

$args = array(
    'orderby'          => 'title',
    'order'            => 'ASC',
    'post_type'        => 'metric_organization',
    'posts_per_page' => 500,
    'post_status'      => 'publish',
    'suppress_filters' => true,
    'meta_query'     => array(
        array(
            'key'   => 'metric_sector',
            'value' => 'Federal',
            'compare' => 'LIKE'
        )
    )
);
$query = null;
$query = new WP_Query($args);


if ($query->have_posts()) {

    while ($query->have_posts()) : $query->the_post();

        $id         = $post->ID;
        $parent     = get_post_meta($post->ID, 'parent_agency', true);
        $parent_org = get_post_meta($post->ID, 'parent_organization', true);
        $agency_title = get_the_title();


        if ($parent) {

            $subargs = array(
                'orderby'          => 'title',
                'order'            => 'ASC',
                'post_type'        => 'metric_organization',
                'posts_per_page' => 500,
                'post_status'      => 'publish',
                'suppress_filters' => true,
                'meta_query'     => array(
                    'relation' => 'AND',
                    array(
                        'key'   => 'parent_organization',
                        'value' => $id,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key'   => 'metric_sector',
                        'value' => 'Federal',
                        'compare' => 'LIKE'
                    )
                )
            );

            $subquery      = null;
            $subquery      = new WP_Query($subargs);
            $agency_title  = get_the_title();
            $dataset_count = get_post_meta($post->ID, 'metric_count', true);
            $last_entry    = get_post_meta($post->ID, 'metric_last_entry', true);


            if ($dataset_count > 0) {

                $parentName = $post->post_name;

                echo '<tr class="datasets_published_per_month_row_tr_odd odd parent-agency" rel="' . $parentName . '">';
                echo '<td class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;">';
                echo '<a style="color: #4295B0;" href="' . get_post_meta($post->ID, 'metric_url', true) . '">' . $agency_title . '</a>';
                echo <<<END
                    <div class="more-link metrics" rel="$parentName">
                        <a href="javascript:void(0)" alt="Read more" class="agencyExpand"><i></i></a>
                        <a href="javascript:void(0)" class="agencyHide" alt="hide"><i></i></a>
                    </div>
END;
                echo '</td>';

                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';

                echo number_format(get_post_meta($post->ID, 'metric_count', true));

                echo '</td>';

                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                echo get_post_meta($post->ID, 'metric_last_entry', true);
                echo '</td>';
                echo '</tr>';
            }


            /**
             * $title == 'Department/Agency Level'
             */
            if ($subquery->have_posts()) {

                while ($subquery->have_posts()) : $subquery->the_post();

                    $title         = get_the_title();
                    $dataset_count = get_post_meta($post->ID, 'metric_count', true);
                    $last_entry    = get_post_meta($post->ID, 'metric_last_entry', true);


                    if ($title != $agency_title && $dataset_count > 0 && $title == 'Department/Agency Level') {

                        echo '<tr class="datasets_published_per_month_row_tr_even even sub-agency ' . $parentName . '">';

                        echo '<td style="text-indent: 10px;" class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;">';
                        echo '<a style="color: #4295B0;" href="' . get_post_meta($post->ID, 'metric_url', true) . '">' . get_the_title() . '</a>';
                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        echo number_format(get_post_meta($post->ID, 'metric_count', true));

                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        if (get_post_meta($post->ID, 'metric_last_entry', true))
                            echo get_post_meta($post->ID, 'metric_last_entry', true);
                        else
                            echo "-";
                        echo '</td>';
                        echo '</tr>';

                    }

                endwhile;
            }


            /**
             * $title != 'Department/Agency Level'
             */
            if ($subquery->have_posts()) {

                while ($subquery->have_posts()) : $subquery->the_post();

                    $title         = get_the_title();
                    $dataset_count = get_post_meta($post->ID, 'metric_count', true);
                    $last_entry    = get_post_meta($post->ID, 'metric_last_entry', true);


                    if ($title != $agency_title && $dataset_count > 0 && $title != 'Department/Agency Level') {


                        echo '<tr class="datasets_published_per_month_row_tr_even even sub-agency ' . $parentName . '">';


                        echo '<td style="text-indent: 10px;"class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;">';
                        echo '<a style="color: #4295B0;" href="' . get_post_meta($post->ID, 'metric_url', true) . '">' . get_the_title() . '</a>';
                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        echo number_format(get_post_meta($post->ID, 'metric_count', true));

                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        if (get_post_meta($post->ID, 'metric_last_entry', true))
                            echo get_post_meta($post->ID, 'metric_last_entry', true);
                        else
                            echo "-";
                        echo '</td>';
                        echo '</tr>';

                    }


                endwhile;
            }


        } elseif (!$parent_org) {


            $dataset_count = get_post_meta($post->ID, 'metric_count', true);
            $agency_title  = get_the_title();
            $last_entry    = get_post_meta($post->ID, 'metric_last_entry', true);


            if ($dataset_count > 0) {


                echo '<tr class="datasets_published_per_month_row_tr_even odd">';
                echo '<td class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;">';
                echo '<a style="color: #4295B0;" href="' . get_post_meta($post->ID, 'metric_url', true) . '">' . get_the_title() . '</a>';
                echo '</td>';

                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                echo number_format(get_post_meta($post->ID, 'metric_count', true));

                echo '</td>';

                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                if (get_post_meta($post->ID, 'metric_last_entry', true))
                    echo get_post_meta($post->ID, 'metric_last_entry', true);
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

<h3 class="fieldcontentregion agencytitle" style="margin-left:-1px;font-weight:bold; ">Other Agencies</h3>

<div class="view-content">
    <table class="views-table cols-4 datasets_published_per_month_table">
        <thead class="datasets_published_per_month_thead">
        <tr class="datasets_published_per_month_row_tr_head">
            <th id="C_AgencyName" class="views-field views-field-title datasets_published_per_month_table_head_fields"
                scope="col" width="60%" align="left">Agency/Sub-Agency/Organization
            </th>
            <th id="C_NumberofDatasetsampToolspublishedbymonth"
                class="views-field views-field-field-creation-date datasets_published_per_month_table_head_fields"
                scope="col" width="20%"> Datasets
            </th>
            <th id="C_NumberofDatasetsampToolspublishedbymonth"
                class="views-field views-field-field-dataset-count datasets_published_per_month_table_head_fields"
                scope="col" width="20%"> Latest Entry
            </th>
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
            'meta_query'     => array(
                array(
                    'key'   => 'metric_sector',
                    'value' => 'Other',
                    'compare' => 'LIKE'
                )
            )
        );
        $query = null;
        $query = new WP_Query($args);


        if ($query->have_posts()) {

            while ($query->have_posts()) : $query->the_post();

                $id         = $post->ID;
                $parent     = get_post_meta($post->ID, 'parent_agency', true);
                $parent_org = get_post_meta($post->ID, 'parent_organization', true);
                $agency_title = get_the_title();


                if ($parent) {

                    $subargs = array(
                        'orderby'          => 'title',
                        'order'            => 'ASC',
                        'post_type'        => 'metric_organization',
                        'posts_per_page' => 500,
                        'post_status'      => 'publish',
                        'suppress_filters' => true,
                        'meta_query'     => array(
                            'relation' => 'AND',
                            array(
                                'key'   => 'parent_organization',
                                'value' => $id,
                                'compare' => 'LIKE'
                            ),
                            array(
                                'key'   => 'metric_sector',
                                'value' => 'Other',
                                'compare' => 'LIKE'
                            )
                        )
                    );


                    $subquery      = null;
                    $subquery      = new WP_Query($subargs);
                    $agency_title  = get_the_title();
                    $dataset_count = get_post_meta($post->ID, 'metric_count', true);
                    $last_entry    = get_post_meta($post->ID, 'metric_last_entry', true);


                    if ($dataset_count > 0) {

                        $parentName = $post->post_name;

                        echo '<tr class="datasets_published_per_month_row_tr_odd odd parent-agency" rel="' . $parentName . '">';
                        echo '<td class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;" >';
                        echo '<a style="color: #4295B0;" href="' . get_post_meta($post->ID, 'metric_url', true) . '">' . $agency_title . '</a>';
                        echo <<<END
                            <div class="more-link metrics" rel="$parentName">
                                <a href="javascript:void(0)" alt="Read more" class="agencyExpand"><i></i></a>
                                <a href="javascript:void(0)" class="agencyHide" alt="hide"><i></i></a>
                            </div>
END;
                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        echo number_format(get_post_meta($post->ID, 'metric_count', true));

                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        echo get_post_meta($post->ID, 'metric_last_entry', true);
                        echo '</td>';
                        echo '</tr>';
                    }


                    if ($subquery->have_posts()) {

                        while ($subquery->have_posts()) : $subquery->the_post();

                            $title         = get_the_title();
                            $dataset_count = get_post_meta($post->ID, 'metric_count', true);
                            $last_entry    = get_post_meta($post->ID, 'metric_last_entry', true);

                            if ($title != $agency_title && $dataset_count > 0 && $title != 'Department/Agency Level') {

                                echo '<tr class="datasets_published_per_month_row_tr_even even sub-agency ' . $parentName . '">';

                                echo '<td style="text-indent: 10px;" class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;" >';
                                echo '<a style="color: #4295B0;" href="' . get_post_meta($post->ID, 'metric_url', true) . '">' . get_the_title() . '</a>';
                                echo '</td>';

                                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                                echo number_format(get_post_meta($post->ID, 'metric_count', true));

                                echo '</td>';

                                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                                if (get_post_meta($post->ID, 'metric_last_entry', true))
                                    echo get_post_meta($post->ID, 'metric_last_entry', true);
                                else
                                    echo "-";
                                echo '</td>';
                                echo '</tr>';

                            }

                        endwhile;
                    }

                } else if (!$parent_org) {


                    $dataset_count = get_post_meta($post->ID, 'metric_count', true);

                    $last_entry = get_post_meta($post->ID, 'metric_last_entry', true);


                    if ($dataset_count > 0) {

                        echo '<tr class="datasets_published_per_month_row_tr_odd odd">';
                        echo '<td class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;" >';
                        echo '<a style="color: #4295B0;"  href="' . get_post_meta($post->ID, 'metric_url', true) . '">' . $agency_title . '</a>';
                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        echo number_format(get_post_meta($post->ID, 'metric_count', true));

                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        echo get_post_meta($post->ID, 'metric_last_entry', true);
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

<h3 class="fieldcontentregion agencytitle"
    style="font-family: 'Abel',Helvetica,sans-serif;clear: both;padding-top: 12px;margin-left:-1px;font-weight:bold;  ">
    Summary</h3>


<div class="view-content">

    <table class="views-table cols-4 datasets_published_per_month_table">
        <thead>
        <tr>
            <td width="60%" style="text-align: left;">Total</td>
            <td width="20%" align="center">
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

                $query = new WP_Query($args);

                if ($query->have_posts()) {

                    while ($query->have_posts()) : $query->the_post();
                        $parent      = get_post_meta($post->ID, 'parent_agency', true);
                        $parent_node = get_post_meta($post->ID, 'parent_organization', true);
                        $cfo         = get_post_meta($post->ID, 'metric_sector', true);
                        if ($parent || !$parent_node) {

                            $total = $total + get_post_meta($post->ID, 'metric_count', true);

                        }


                        ?>

                    <?php endwhile; ?>
                <?php } ?>

                <?php


                echo number_format($total);



                ?> </td>
            <td width="20%" align="center">
                <?php

                $last_entry = $wpdb->get_var("SELECT MAX(STR_TO_DATE(meta_value, '%m/%d/%Y')) FROM wp_postmeta WHERE meta_key = 'metric_last_entry'");
                list($y, $m, $d) = explode('-', $last_entry);
                $last_entry = "$m/$d/$y";
                echo $last_entry;



                ?>


            </td>
        </tr>
        </thead>
    </table>


</div>
</div>

<style type="text/css">
    .agencyExpand {
        display: none;
    }
</style>

<script type="text/javascript">
    jQuery(function ($) {
        $('.agencyExpand').on('click', function () {
            $('.' + $(this).parent().attr('rel')).show('slow');
            $(this).parent().children('.agencyHide').show();
            $(this).hide();
        });
        $('.agencyHide').on('click', function () {
            $('.' + $(this).parent().attr('rel')).hide('slow');
            $(this).parent().children('.agencyExpand').show();
            $(this).hide();
        });
    });
</script>