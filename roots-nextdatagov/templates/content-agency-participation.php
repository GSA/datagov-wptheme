<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
$total = 0;
?>
<?php include('category-subnav.php'); ?>

<div class="single">
<div class="container">
<?php
while (have_posts()) {
    the_post();
    ?>



    <div id="appstitle" class="Appstitle" style="margin-left:-20px;"><?php the_title(); ?></div>


<?php } ?>

<?php

$s3_config = get_option('tantan_wordpress_s3');

$s3_bucket = trim($s3_config['bucket'],'/');
$s3_prefix = trim($s3_config['object-prefix'],'/');

$s3_path = 'https://s3.amazonaws.com/'.$s3_bucket.'/'.$s3_prefix.'/';

?>

<div style="float:left;">
    This report is also available for download in the following formats: <a
        href="<?php echo $s3_path; ?>federal-agency-participation.csv"> CSV </a> | <a href="<?php echo $s3_path; ?>federal-agency-participation.xlsx">
        EXCEL </a><br/><br/>
</div>
<div style=""> <?php the_content(); ?>    </div>
<?php
$metric_sync = get_option('metrics_updated_gmt');
echo '<div style="font-style:italic;clear:both;">';
echo "Data last updated on: {$metric_sync} <br />";
echo "</div>";
?>

<div class="open-data-sites-boxes agencies">
    <div class="open-data-sites-box">
        <div class="region">Agencies and Subagencies:</div>
        <div class="numbers agencies_total_count">
            &nbsp;
        </div>
    </div>
</div>
<div class="clear2"></div>

<div class="open-data-sites-boxes agencies">
    <div class="open-data-sites-box">
        <div class="region">Publishers:</div>
        <div class="numbers publishers_total_count">
            &nbsp;
        </div>
    </div>
</div>
<div class="clear2"></div>
<br/>

<h3 class="fieldcontentregion agencytitle" style="margin-left:-1px;">Agencies/Publishers</h3>


<div class="view-content">
<table class="views-table cols-4 datasets_published_per_month_table">
<thead class="datasets_published_per_month_thead">
<tr class="datasets_published_per_month_row_tr_head">
    <th id="C_AgencyName" class="views-field views-field-title datasets_published_per_month_table_head_fields"
        scope="col" width="60%" align="left">Agency/Publisher
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

$args = array(
    'orderby'          => 'title',
    'order'            => 'ASC',
    'post_type'        => 'metric_organization',
    'posts_per_page'   => -1,
    'post_status'      => 'publish',
    'suppress_filters' => true,
    'meta_query'       => array(
        array(
            'key'     => 'metric_sector',
            'value'   => 'Federal',
            'compare' => 'LIKE'
        )
    )
);
$query = null;
$query = new WP_Query($args);


if ($query->have_posts()) {

    while ($query->have_posts()) : $query->the_post();

        $id           = $post->ID;
        $parent       = get_post_meta($post->ID, 'parent_agency', true);
        $parent_org   = get_post_meta($post->ID, 'parent_organization', true);
        $agency_title = get_the_title();


        if ($parent) {

            $subargs = array(
                'orderby'          => 'title',
                'order'            => 'ASC',
                'post_type'        => 'metric_organization',
                'posts_per_page'   => -1,
                'post_status'      => 'publish',
                'suppress_filters' => true,
                'meta_query'       => array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'parent_organization',
                        'value'   => $id,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key'     => 'metric_sector',
                        'value'   => 'Federal',
                        'compare' => 'LIKE'
                    )
                )
            );

            $subquery     = null;
            $subquery     = new WP_Query($subargs);
            $agency_title = get_the_title();

            $dataset_count = get_post_meta($post->ID, 'metric_count', true);
            $last_entry    = get_post_meta($post->ID, 'metric_last_entry', true);

            $total += $dataset_count;

            if ($dataset_count > 0) {

                $parentName = $post->post_name;

                echo '<tr class="datasets_published_per_month_row_tr_odd odd parent-agency" rel="' . $parentName . '">';
                echo '<td class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;">';
                echo '<a style="color: #4295B0;" href="' . get_post_meta(
                        $post->ID,
                        'metric_url',
                        true
                    ) . '">' . $agency_title . '</a>';
                echo <<<END
                    <div class="more-link metrics" rel="$parentName">
                        <a href="javascript:void(0)" alt="Read more" class="agencyExpand" title="expand"><i></i></a>
                        <a href="javascript:void(0)" class="agencyHide" alt="hide" title="collapse"><i></i></a>
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
             * $title != 'Department/Agency Level'
             */
            if ($subquery->have_posts()) {

                while ($subquery->have_posts()) : $subquery->the_post();

                    $title          = get_the_title();
                    $dataset_count  = get_post_meta($post->ID, 'metric_count', true);
                    $last_entry     = get_post_meta($post->ID, 'metric_last_entry', true);
                    $publisher      = get_post_meta($post->ID, 'metric_publisher', true);
                    $department_lvl = get_post_meta($post->ID, 'metric_department_lvl', true);
                    switch (true) {
                        case (bool)$department_lvl:
                            $class = 'department-lvl';
                            break;
                        case (bool)$publisher:
                            $class = 'publisher';
                            break;
                        default:
                            $class = 'sub-agency';
                    }

                    if ($dataset_count > 0 && $title != 'Department/Agency Level') {


                        echo '<tr class="datasets_published_per_month_row_tr_even even sub-agency ' . $parentName . '">';


                        echo '<td class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;">';
                        echo '<a class="' . $class . '" style="color: #4295B0;" href="' . get_post_meta(
                                $post->ID,
                                'metric_url',
                                true
                            ) . '">' . get_the_title() . '</a>';
                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        echo number_format(get_post_meta($post->ID, 'metric_count', true));

                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        if (get_post_meta($post->ID, 'metric_last_entry', true)) {
                            echo get_post_meta($post->ID, 'metric_last_entry', true);
                        } else {
                            echo "-";
                        }
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
                echo '<a style="color: #4295B0;" href="' . get_post_meta(
                        $post->ID,
                        'metric_url',
                        true
                    ) . '">' . get_the_title() . '</a>';
                echo '</td>';

                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                echo number_format(get_post_meta($post->ID, 'metric_count', true));

                echo '</td>';

                echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                if (get_post_meta($post->ID, 'metric_last_entry', true)) {
                    echo get_post_meta($post->ID, 'metric_last_entry', true);
                } else {
                    echo "-";
                }
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
        scope="col" width="60%" align="left">Agency/Publisher
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
    'posts_per_page'   => -1,
    'post_status'      => 'publish',
    'suppress_filters' => true,
    'meta_query'       => array(
        array(
            'key'     => 'metric_sector',
            'value'   => 'Other',
            'compare' => 'LIKE'
        )
    )
);
$query = null;
$query = new WP_Query($args);


if ($query->have_posts()) {

    while ($query->have_posts()) : $query->the_post();

        $id           = $post->ID;
        $parent       = get_post_meta($post->ID, 'parent_agency', true);
        $parent_org   = get_post_meta($post->ID, 'parent_organization', true);
        $agency_title = get_the_title();


        if ($parent) {

            $subargs = array(
                'orderby'          => 'title',
                'order'            => 'ASC',
                'post_type'        => 'metric_organization',
                'posts_per_page'   => -1,
                'post_status'      => 'publish',
                'suppress_filters' => true,
                'meta_query'       => array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'parent_organization',
                        'value'   => $id,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key'     => 'metric_sector',
                        'value'   => 'Other',
                        'compare' => 'LIKE'
                    )
                )
            );


            $subquery      = null;
            $subquery      = new WP_Query($subargs);
            $agency_title  = get_the_title();
            $dataset_count = get_post_meta($post->ID, 'metric_count', true);
            $last_entry    = get_post_meta($post->ID, 'metric_last_entry', true);

            $total += $dataset_count;

            if ($dataset_count > 0) {

                $parentName = $post->post_name;

                echo '<tr class="datasets_published_per_month_row_tr_odd odd parent-agency" rel="' . $parentName . '">';
                echo '<td class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;" >';
                echo '<a style="color: #4295B0;" href="' . get_post_meta(
                        $post->ID,
                        'metric_url',
                        true
                    ) . '">' . $agency_title . '</a>';

                echo <<<END
                            <div class="more-link metrics" rel="$parentName">
                                <a href="javascript:void(0)" alt="Read more" class="agencyExpand" title="Expand"><i></i></a>
                                <a href="javascript:void(0)" class="agencyHide" alt="hide" title="collapse"><i></i></a>
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

                    $title          = get_the_title();
                    $dataset_count  = get_post_meta($post->ID, 'metric_count', true);
                    $last_entry     = get_post_meta($post->ID, 'metric_last_entry', true);
                    $department_lvl = get_post_meta($post->ID, 'metric_department_lvl', true);

                    switch (true) {
                        case (bool)$department_lvl:
                            $class = 'department-lvl';
                            break;
                        case (bool)$publisher:
                            $class = 'publisher';
                            break;
                        default:
                            $class = 'sub-agency';
                    }

                    if ($dataset_count > 0 && $title != 'Department/Agency Level') {

                        echo '<tr class="datasets_published_per_month_row_tr_even even sub-agency ' . $parentName . '">';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;" >';
                        echo '<a class="' . $class . '" style="color: #4295B0;" href="' . get_post_meta(
                                $post->ID,
                                'metric_url',
                                true
                            ) . '">' . get_the_title() . '</a>';
                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        echo number_format($dataset_count);

                        echo '</td>';

                        echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                        if ($last_entry) {
                            echo $last_entry;
                        } else {
                            echo "-";
                        }
                        echo '</td>';
                        echo '</tr>';

                    }

                endwhile;
            }

        } else {
            if (!$parent_org) {


                $dataset_count = get_post_meta($post->ID, 'metric_count', true);

                $last_entry = get_post_meta($post->ID, 'metric_last_entry', true);

                $total += $dataset_count;

                if ($dataset_count > 0) {

                    echo '<tr class="datasets_published_per_month_row_tr_odd odd">';
                    echo '<td class="datasets_published_per_month_table_row_fields" width="60%" style="text-align: left;" >';
                    echo '<a style="color: #4295B0;"  href="' . get_post_meta(
                            $post->ID,
                            'metric_url',
                            true
                        ) . '">' . $agency_title . '</a>';
                    echo '</td>';

                    echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                    echo number_format($dataset_count);

                    echo '</td>';

                    echo '<td class="datasets_published_per_month_table_row_fields" width="20%" align="right">';
                    if ($last_entry) {
                        echo $last_entry;
                    } else {
                        echo "-";
                    }
                    echo '</td>';
                    echo '</tr>';
                }


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

                echo number_format($total);

                ?> </td>
            <td width="20%" align="center">
                <?php
                list ($metric_sync_date,) = explode(' ', $metric_sync);
                echo $metric_sync_date; ?>
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
        $('.more-link').each(function () {
            var rel = $(this).attr('rel');
            if (!$('.sub-agency.' + rel).size()) {
                $(this).remove()
            }
        });
        $('.agencies_total_count').html($('tr.sub-agency a.sub-agency').size() + $('tr.parent-agency').size());
        $('.publishers_total_count').html($('tr.sub-agency a.publisher').size());
        $('.total_dataset_count').html('<?php $cnt = get_option('ckan_total_count'); echo $cnt>1000?number_format($cnt):'&gt;100,000'?>');
    });
</script>
