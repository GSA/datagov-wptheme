<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;


?>
<?php include('category-subnav.php'); ?>

<div class="single">
<div class="container">

<div id="main-inner" class="dataset-inner" style="margin-top:20px;">
<div class="Appstitle" style="padding-left:5px; margin-bottom:10px;margin-left:-5px;">Datasets Published per Month - Full History </div>
<div class="view-content">

<div style="float: right;margin-left:280px;"> <?php the_content(); ?></div>
<?php

$metrics = get_metrics_per_month_full();

if (!$metrics) {
  echo "<h4>No data found. Please run full metrics calculator</h4>";
} else {
  $metrics_sync = gmdate("m/d/Y h:i A", strtotime($metrics['updated_at'])) . ' GMT';

  echo '<div style="font-style:italic;">';
  echo "Data last updated on: {$metrics_sync}<br /><br />
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
        colspan="<?php echo sizeof($metrics['total_by_month']);?>" style="text-align:center"
        ;> Number of Datasets published by month
      </th>
      <th
        class="views-field views-field-field-dataset-count datasets_published_per_month_table_head_fields" scope="col"
        rowspan="2"> Total datasets
      </th>
    </tr>
    <tr class="datasets_published_per_month_row_tr_head">
      <?php
      echo '<th></th>';
      foreach($metrics['total_by_month'] as $date=>$value) {
        list($month, $year) = explode(' ', $date);
        echo '<th class="datasets_published_per_month_table_head_calendar"  >';
        echo '  <span class="datasets_published_month">'.$month.'</span><br/>';
        echo '  <span class="datasets_published_year">'.$year.'</span>';
        echo '</th>';
      }
      ?>
    </tr>
    </thead>
    <tbody class="datasets_published_per_month_tbody">

    <?php
    foreach($metrics['organizations'] as $organization){
      if (!$organization['total']) {
        continue;
      }
      echo <<<END
      <tr class="datasets_published_per_month_row_tr_odd odd">
        <td class="datasets_published_per_month_table_row_fields" style="color:#000000;text-align:left;">{$organization['title']}</td>
END;
        foreach($organization['metrics'] as $metric) {
          $count = number_format($metric['count']);
          echo <<<END
          <td class="datasets_published_per_month_table_row_fields">
          <a class="link_dataset" href="{$metric['web_url']}">{$count}</a>
        </td>
END;
        }

      $total = number_format($organization['total']);
      echo <<<END
          <td class="datasets_published_per_month_table_row_fields">
          <a class="link_dataset" href="{$organization['web_url']}">{$total}</a>
        </td>
END;

      echo '</tr>';
    }
    ?>

    </tbody>
    <thead>
    <tr>
      <td style="text-align:left; ">Total</td>
      <?php
      foreach($metrics['total_by_month'] as $date=>$value){
        echo '<td>' . ($value ? number_format($value) : '-') . '</td>';
      }
      echo '<td class="total">' . ($metrics['total'] ? number_format($metrics['total']) : '-') . '</td>';
      ?>
    </tr>
    </thead>
  </table>

  <?php
}
?>

</div>
</div>
</div>
</div>
