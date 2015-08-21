<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;

$total = array();

?>
<?php include('category-subnav.php'); ?>

<div class="single">
<div class="container">

<div id="main-inner" class="dataset-inner" style="margin-top:20px;">
<div class="Appstitle" style="padding-left:5px; margin-bottom:10px;margin-left:-5px;">Datasets Published per Month</div>
<div class="view-content">

<div style="float: right;margin-left:280px;"> <?php the_content(); ?></div>
<?php
$metric_sync = get_option('metrics_updated_gmt');
echo '<div style="font-style:italic;">';
echo "Data last updated on: {$metric_sync}<br /><br />
</div>";
?>

<table class="views-table cols-4 datasets_published_per_month_table">
<thead class="datasets_published_per_month_thead">
<tr class="datasets_published_per_month_row_tr_head">
    <th id="C_AgencyName" class="views-field views-field-title datasets_published_per_month_table_head_fields"
        scope="col" rowspan=""> Agency Name
    </th>
    <th
        class="views-field views-field-field-creation-date datasets_published_per_month_table_head_fields" scope="col"
        colspan="12" style="text-align:center"
    ;> Number of Datasets published by month </th>
    <th
        class="views-field views-field-field-dataset-count datasets_published_per_month_table_head_fields" scope="col"
        rowspan="2"> Total in the Past 12 Months
    </th>
    <th
        class="views-field views-field-field-dataset-count datasets_published_per_month_table_head_fields" scope="col"
        rowspan="2"> Total datasets
    </th>
</tr>
<tr class="datasets_published_per_month_row_tr_head">
    <?php
    echo '<th></th>';
    for ($i = 0; $i <= 11; $i++) {

        echo '<th class="datasets_published_per_month_table_head_calendar"  >';

        echo '<span class="datasets_published_month">';
        $current = 12 - date("m");
        if ($i == 0) {
            $currentMonth = date('M');

        } else {
            $currentMonth = $currentMonth2;
        }
        $currentMonth2 = Date('M', strtotime($currentMonth . " next month"));
        echo $currentMonth2;
        echo '</span>';
        echo '<br/>';

        $year = date("y");

        echo '<span class="datasets_published_year">';


        if ($i >= $current) {

            echo $year;
        } else {
            $previousyear = $year - 1;
            echo $previousyear;


        }

        echo '</span>';
        echo '</th>';
    }
    ?>
</tr>
</thead>
<tbody class="datasets_published_per_month_tbody">

<?php $count = 0; ?>

<?php
$args = array(
    'orderby'          => 'title',
    'order'            => 'ASC',
    'post_type'        => 'metric_organization',
    'posts_per_page'   => 500,
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
$count_a = 0;
if ($query->have_posts()) {

    while ($query->have_posts()) : $query->the_post();

        $parent_node = get_post_meta($post->ID, "parent_organization", true);
        $parent      = get_post_meta($post->ID, "parent_agency", true);
        $title       = get_the_title();

        if (($parent || !$parent_node) && $title != "Department/Agency Level") {

            if ($count / 2 == 0) {

                echo '<tr class="datasets_published_per_month_row_tr_even even">';
                echo '<td class="datasets_published_per_month_table_row_fields" style="color:#000000;text-align:left;">';
                echo the_title();
                echo '</td>';

                for ($i = 1; $i < 13; $i++) {
                    if ($currentMonthDatasetCount = get_post_meta(
                        $post->ID,
                        "month_{$i}_dataset_count",
                        true
                    )
                    ) {
                        $total[$i] += $currentMonthDatasetCount;
                        echo '<td class="datasets_published_per_month_table_row_fields">' . '<a class="link_dataset" href="' . get_post_meta(
                                $post->ID,
                                "month_{$i}_dataset_url",
                                true
                            ) . '">' . number_format($currentMonthDatasetCount) . '</a>';
                    } else {
                        echo '<td class="datasets_published_per_month_table_row_fields">' . '-';
                    }
                    echo '</td>';
                }

                if ($lastYearDatasetCount = $get_post_meta($post->ID, 'last_year_dataset_count', true)) {
                    $total[13] += $lastYearDatasetCount;
                    echo '<td class="datasets_published_per_month_table_row_fields">' .
                        '<a class="link_dataset" href="' . get_post_meta(
                            $post->ID,
                            'last_year_dataset_url',
                            true
                        ) . '">' . number_format($lastYearDatasetCount) . '</a>';
                } else {
                    echo '<td class="datasets_published_per_month_table_row_fields">' . '-';
                }
                echo '</td>';

                if ($totalDatasetCount = get_post_meta($post->ID, 'metric_count', true)) {
                    $total[13] += $totalDatasetCount;
                    echo '<td class="datasets_published_per_month_table_row_fields">' . '<a class="link_dataset" href="' . get_post_meta(
                            $post->ID,
                            'metric_url',
                            true
                        ) . '">' . number_format($totalDatasetCount) . '</a>';
                } else {
                    echo '<td class="datasets_published_per_month_table_row_fields">' . '-';
                }
                echo '</td>';

                echo '</tr>';
            } else {

                echo '<tr class="datasets_published_per_month_row_tr_odd odd">';
                echo '<td class="datasets_published_per_month_table_row_fields" style="color:#000000;text-align:left;">';
                echo the_title();
                echo '</td>';

                for ($i = 1; $i < 13; $i++) {
                    if ($currentMonthDatasetCount = get_post_meta(
                        $post->ID,
                        "month_{$i}_dataset_count",
                        true
                    )
                    ) {
                        $total[$i] += $currentMonthDatasetCount;
                        echo '<td class="datasets_published_per_month_table_row_fields">' . '<a class="link_dataset" href="' . get_post_meta(
                                $post->ID,
                                "month_{$i}_dataset_url",
                                true
                            ) . '">' . number_format($currentMonthDatasetCount) . '</a>';
                    } else {
                        echo '<td class="datasets_published_per_month_table_row_fields">' . '-';
                    }
                    echo '</td>';
                }

                if ($lastYearDatasetCount = get_post_meta($post->ID, 'last_year_dataset_count', true)) {
                    $total[13] += $lastYearDatasetCount;
                    echo '<td class="datasets_published_per_month_table_row_fields datasets_last_year">' . '<a class="link_dataset" href="' . get_post_meta(
                            $post->ID,
                            'last_year_dataset_url',
                            true
                        ) . '">' . number_format($lastYearDatasetCount) . '</a>';
                } else {
                    echo '<td class="datasets_published_per_month_table_row_fields datasets_last_year">' . '-';
                }
                echo '</td>';

                if ($totalDatasetCount = get_post_meta($post->ID, 'metric_count', true)) {
                    $total[14] += $totalDatasetCount;
                    echo '<td class="datasets_published_per_month_table_row_fields datasets_total">' . '<a class="link_dataset" href="' . get_post_meta(
                            $post->ID,
                            'metric_url',
                            true
                        ) . '">' . number_format($totalDatasetCount) . '</a>';
                } else {
                    echo '<td class="datasets_published_per_month_table_row_fields datasets_total">' . '-';
                }
                echo '</td>';

                echo '</tr>';
            }

        }
        $count++;

        ?>
    <?php endwhile; ?>
<?php } ?>
</tbody>
<thead>
<tr>
    <td style="text-align:left; ">Total</td>
    <?php
    for ($i = 1; $i < 13; $i++) {
        echo '<td>' . ($total[$i] ? number_format($total[$i]) : '-') . '</td>';
    }
    echo '<td class="last-year">' . ($total[13] ? number_format($total[13]) : '-') . '</td>';
    echo '<td class="total">' . ($total[14] ? number_format($total[14]) : '-') . '</td>';
    ?>
</tr>
</thead>
</table>

</div>
</div>
</div>
</div>