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

<?php

$s3_config = get_option('tantan_wordpress_s3');

$s3_bucket = trim($s3_config['bucket'],'/');
$s3_prefix = trim($s3_config['object-prefix'],'/');

$s3_path = 'https://s3.amazonaws.com/'.$s3_bucket.'/'.$s3_prefix.'/';

?>

<div>
  This report is also available for download in the following formats:
  <a target="_blank" href="<?php echo $s3_path; ?>federal-agency-participation-full-by-metadata_created.csv"> CSV </a> |
  <a target="_blank" href="<?php echo $s3_path; ?>federal-agency-participation-full-by-metadata_created.json"> JSON </a>
  <br/><br/>
</div>

<div style="float: right;margin-left:280px;"> <?php the_content(); ?>
</div>
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
    <ul class="year">
        <?php $start= 2013;
        $current = date('Y');
        for ($i=$start;$i<= $current; $i++){
            if ($i == $current){
                echo '<li class="active"><a href="#"><i class="fa fa-minus-circle"><span style="margin-left:5px;">'.$i.'</span></i></a></li>';
            }else{
                echo '<li><a href="#"><i class="fa fa-plus-circle"><span style="margin-left:5px;">'.$i.'</span></i></a></li>';
            }
        }
        ?>
    </ul>


  <table class="views-table cols-4 datasets_published_per_month_table_full">
    <thead class="datasets_published_per_month_thead">
    <tr class="datasets_published_per_month_row_tr_head">
      <th id="C_AgencyName" class="views-field views-field-title datasets_published_per_month_table_head_fields"
          scope="col" rowspan=""> Agency Name
      </th>
      <th
        class="views-field views-field-field-creation-date datasets_published_per_month_table_head_fields" scope="col"
        colspan="<?php echo sizeof($metrics['total_by_month']);?>" style="text-align:left"
        ;> Number of Datasets published by month
      </th>
      <th
        class="views-field views-field-field-dataset-count datasets_published_per_month_table_head_fields" scope="col"
        rowspan="2"> Total Datasets
      </th>
    </tr>
    <tr class="datasets_published_per_month_row_tr_head">
      <?php
      echo '<th></th>';
      foreach($metrics['total_by_month'] as $date=>$value) {
        list($month, $year) = explode(' ', $date);
          if ($year == date('Y') ){
              echo '<th class="datasets_published_per_month_table_head_calendar showCol '.$year.'"  >';
          }
          else{
              echo '<th class="datasets_published_per_month_table_head_calendar hideCol '.$year.'"  >';
          }
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
    <tfoot>
    <tr>
      <td style="text-align:left; ">Total</td>
      <?php
      foreach($metrics['total_by_month'] as $date=>$value){
        echo '<td>' . ($value ? number_format($value) : '-') . '</td>';
      }
      echo '<td class="total">' . ($metrics['total'] ? number_format($metrics['total']) : '-') . '</td>';
      ?>
    </tr>
    </tfoot>
  </table>

  <?php
}
?>

</div>
</div>
</div>
</div>
