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
<div class="wrap container">
    <?php
    while( have_posts() ) {
        the_post();
        the_content();
    }
    ?>
</div>
<div style="display: none;">
    <div id="data_table_1" class="data_table_opengov">
        <?php
        displayTable($us_states_open_data,true);
        ?>
    </div>
</div>

<div style="display: none;">
    <div id="data_table_2"  class="data_table_opengov">
        <?php
        displayTable($us_counties_open_data,true);
        ?>
    </div>
</div>

<div style="display: none;">
    <div id="data_table_3" class="data_table_opengov">
        <?php
        displayTable($international_open_data,true);
        ?>
    </div>
</div>

<div style="display: none;">
    <div id="data_table_4" class="data_table_opengov">
        <?php
        displayTable("http://www.data.gov/media/2013/11/opendatasites1.csv",true);
        ?>
    </div>
</div>

<div style="display: none;">
    <div id="data_table_5" class="data_table_opengov">
        <?php
        displayTable("http://www.data.gov/media/2013/11/opendatasites1.csv",true);
        ?>
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
            $url=$column;
            if(strpos($column, "http") == true)
                echo "<td>".($url)."</td>";
            else
                echo "<td>".preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $column)."</td>";

        }
        echo '</tr>';
    }
    echo '</table>';
    fclose($handle);
}
?>