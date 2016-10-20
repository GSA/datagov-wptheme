<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ujoseph
 * Date: 5/16/14
 * Time: 5:23 PM
 */
$international_open_data = (get_option('international_open_data') != '') ? get_option('international_open_data') : '';
$us_states_open_data = (get_option('us_states_open_data') != '') ? get_option('us_states_open_data') : '';
$us_counties_open_data = (get_option('us_counties_open_data') != '') ? get_option('us_counties_open_data') : '';
?>
  <div class="wrap container content-page">
    <?php
    while( have_posts() ) {
      the_post();
      the_content();
    }
    ?>
  </div>

  <div id="data_table_1" class="data_table_opengov modal fade" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title" id="gridSystemModalLabel">US States Open Data</h4>
        </div>
        <div class="modal-body">
          <?php
          displayTable($us_states_open_data,true);
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>


  <div id="data_table_2"  class="data_table_opengov modal fade" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title" id="gridSystemModalLabel">US Counties Open Data</h4>
        </div>
        <div class="modal-body">
          <?php
          displayTable($us_counties_open_data,true);
          ?></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>



  <div id="data_table_3" class="data_table_opengov modal fade" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title" id="gridSystemModalLabel">International Open Data</h4>
        </div>
        <div class="modal-body">
          <?php
          displayTable($international_open_data,true);
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>



  <div id="data_table_4" class="data_table_opengov modal fade" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title" id="gridSystemModalLabel">&nbsp;</h4>
        </div>
        <div class="modal-body">
          <?php
          displayTable("https://www.data.gov/media/2013/11/opendatasites1.csv",true);
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>



  <div id="data_table_5" class="data_table_opengov modal fade" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title" id="gridSystemModalLabel">&nbsp;</h4>
        </div>
        <div class="modal-body">
          <?php
          displayTable("https://www.data.gov/media/2013/11/opendatasites1.csv",true);
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>


<?php
// Function that converts csv to html
function displayTable($filename, $header=false) {
  $handle = fopen($filename, "r");
  echo '<table>';
  //Header
  if ($header) {
    $csvcontents = fgetcsv($handle);
    echo '<tr>';
    foreach ($csvcontents as $headercolumn) {
      echo "<th>$headercolumn</th>";
    }
    echo '</tr>';
  }
  // Contents
  while ($csvcontents = fgetcsv($handle)) {
    echo '<tr>';
    foreach ($csvcontents as $column) {
      if(filter_var(trim($column), FILTER_VALIDATE_URL)){
        echo "<td><a target='_new' href ='".$column."'>".$column."</a></td>";
      } else
        echo "<td>".preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $column)."</td>";

    }
    echo '</tr>';
  }
  echo '</table>';
  fclose($handle);
}
?>
