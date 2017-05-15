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

<div class = "container-fluid">
<div class = "col-md-8" style="padding-left:0px">
  This report is also available for download in the following formats:
  <a target="_blank" href="<?php echo $s3_path; ?>agency-participation-full-by-metadata_created.csv"> CSV </a> |
  <a target="_blank" href="<?php echo $s3_path; ?>agency-participation-full-by-metadata_created.json"> JSON </a>
  <br><br>
</div>
<div class = "col-md-4">
    <a class="Published-Per-Month-Link" title="Datasets Published Per Month" href="/metric">Go Back to Metrics Page</a>
</div>
</div>

<br><br>
<div> <?php the_content(); ?></div>
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
    <ul class="year" >
        <?php $start= 2013;
        $current = date('Y');
        for ($i=$start;$i<= $current; $i++){
            if ($i == $current){
              $display_minus="inline-block";
              $display_plus="none";
            } else {
              $display_minus='none';
              $display_plus="inline-block";
            }
                echo "<li class='active minus_li_{$i}' style='display:{$display_minus}'><a><i class='fa fa-minus-circle minus{$i}'><span style='margin-left:5px;'>".$i."</span></i></a></li>";
                echo "<li class='plus_li_{$i}'style='display:{$display_plus};'><a><i class='fa fa-plus-circle plus{$i}'><span style='margin-left:5px;'>".$i."</span></i></a></li>";
        }
        ?>
    </ul>

<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper">
<div id="DataTables_Table_0_filter" class="dataTables_filter">
  <label>
    Search:
    <input type="search" id="searchInput" class placeholder aria-controls="DataTables_Table_0" onkeyup="searchFunction()">
  </label>
</div>
<div class="topscroll">
  <div class="upscroll"></div>
</div>
  <div class="scroll" style="overflow:auto; width:100%;">
  <table class="views-table cols-4 datasets_published_per_month_table_full dataTable" style="width:100%;" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info">
    <thead class="datasets_published_per_month_thead">
    <tr class="datasets_published_per_month_row_tr_head" style="width:100%" role="row">
      <th id="C_AgencyName" class="views-field views-field-title datasets_published_per_month_table_head_fields"
          scope="col" rowspan=""> Agency Name
      </th>
      <th id="C_AgencyName" class="views-field views-field-title datasets_published_per_month_table_head_fields"
          scope="col" rowspan=""> Organization Type
      </th>
      <th
        class="views-field views-field-field-creation-date datasets_published_per_month_table_head_fields" scope="col"
        colspan="" style="text-align:left"
        ;> Number of Datasets published by month
      </th>
      <th
        class="views-field views-field-field-dataset-count datasets_published_per_month_table_head_fields" scope="col"
        rowspan="2"> Total Datasets
      </th>
    </tr>
    <tr class="datasets_published_per_month_row_tr_head" role="row">
      <?php
      echo '<th></th>';
      echo '<th></th>';
      foreach($metrics['total_by_month'] as $date=>$value) {
        list($month, $year) = explode(' ', $date);
          if ($year == date('Y') ){
            $display = '';
          }
          else{
            $display = 'none';
          }
        echo "<th class='datasets_published_per_month_table_head_calendar showCol th_{$year}' style='display:{$display};'>";
        echo "  <span class='datasets_published_month'>".$month.'</span><br/>';
        echo "  <span class='datasets_published_year'>".$year.'</span>';
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
      if($organization['organization_type'] === 'Federal-Other'){
        $organization['organization_type'] = 'Other Federal';
      } else if($organization['organization_type'] === 'Other'){
        $organization['organization_type'] = 'Other Non-Federal';
      }
      echo <<<END
      <tr class="datasets_published_per_month_row_tr_odd odd" role="row">
        <td class="datasets_published_per_month_table_row_fields" style="color:#000000;text-align:left;">{$organization['title']}</td>
        <td class="datasets_published_per_month_table_row_fields" style="color:#000000;text-align:left;">{$organization['organization_type']}</td>
END;
        foreach($organization['metrics'] as $metric) {
          $count = number_format($metric['count']);
          list($month, $year) = explode(' ', $metric['title']);
          $current = date('Y');
          if($year == $current){
            $display = "";
          } else {
            $display = "none";
          }
          echo <<<END
          <td class="datasets_published_per_month_table_row_fields td_{$year}" style="display:{$display}">
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
    <tr style="display: none;" id="no_match">
      <td>No Matching Records Found</td>
    </tr>
    </tbody>
    <tfoot id="tfoot">
    <tr>
      <td id="id_foot" style="text-align:left; ">Total</td>
      <td></td>
      <?php
      foreach($metrics['total_by_month'] as $date=>$value){
        list($month, $year) = explode(' ', $date);
        if ($year == date('Y') ){
          $display = '';
        }
        else{
          $display = 'none';
        }
        echo "<td id='id_foot' class='tf_{$year}' style='display:{$display};'>" . ($value ? number_format($value) : '-') . '</td>';
      }
      echo '<td id="id_foot" class="total">' . ($metrics['total'] ? number_format($metrics['total']) : '-') . '</td>';
      ?>
    </tr>
    </tfoot>
  </table>

  <?php
}
?>

</div>
<div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria></div>

</div>
</div>
</div>

<style type="text/css">
  .Published-Per-Month-Link {
      background-color: #efefef;
      padding: 10px 30px;
      border: 1px solid #E8E8E8;
      margin-right: 3%;
  }
  .Published-Per-Month-Link:hover {
    background-color: white;
    text-decoration: none;
  }
  .year li:nth-child(2) { border-left:0px; }
</style>
<script type="text/javascript">
  jQuery(function ($) {
  <?php $start= 2013;
    $current = date('Y');
    for($i=$start;$i<= $current; $i++){
      ${$i} = 0;
      foreach($metrics['total_by_month'] as $date => $value) {
        list($month, $year) = explode(' ', $date);
        if($year == $i){
          ${$i} += 1;
        } 
      }
      if($current == $i) {
        echo "$('.views-field-field-creation-date').attr('colspan', ${$i});";
      }
      echo <<<DATES
      $(".plus{$i}").on("click", function() {
        var new_colspan = $(".views-field-field-creation-date").prop("colspan") + ${$i};
        $(".minus_li_{$i}").show();
        $(".plus_li_{$i}").hide();
        $(".th_{$i}").show();
        $(".td_{$i}").show();
        $(".tf_{$i}").show();
        if($(".views-field-field-creation-date").prop("colspan") == 1) {
          new_colspan--;
        }
        $(".views-field-field-creation-date").attr('colspan', new_colspan);
        if(new_colspan > 0){
          $('.views-field-field-creation-date').show();
        }
      });
      $(".minus{$i}").on("click", function() {
        var new_colspan = $(".views-field-field-creation-date").prop("colspan") - ${$i};
        $(".plus_li_{$i}").show();
        $(".minus_li_{$i}").hide();
        $(".th_{$i}").hide();
        $(".td_{$i}").hide();
        $(".tf_{$i}").hide();
        $(".views-field-field-creation-date").attr('colspan', new_colspan);
        if(new_colspan === 0){
          $('.views-field-field-creation-date').hide();
        }
      });
DATES;
    }
  ?>
  })

  function searchFunction() {
    var input, filter, table, tr, td, i;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("DataTables_Table_0");
    tr = table.getElementsByTagName("tr");
    var anyMatch = 0;
    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[0];
      if (td) {
        console.log(td.id);
        if (td.innerHTML.toUpperCase().indexOf(filter) > -1 || td.id === "id_foot") {
          if(td.innerHTML.toUpperCase().indexOf(filter) > -1) {
            anyMatch++;
          }
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      } 
    }
    if(!anyMatch){
      document.getElementById('no_match').style.display = "";
    } else {
      document.getElementById('no_match').style.display = "none";
    }
    var match_message = "Showing " + anyMatch + " entries (filtered from " + (tr.length - 4) + " total entries)";
    document.getElementById('DataTables_Table_0_info').innerHTML = match_message;
  }  
 
</script>
