<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ujoseph
 * Date: 5/16/14
 * Time: 5:23 PM
 * To change this template use File | Settings | File Templates.
 */
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
        displayTable("http://www.data.gov/media/2013/11/opendatasites1.csv",true);
        ?>
    </div>
</div>

<div style="display: none;">
    <div id="data_table_2" class="data_table_opengov">
        <?php
        displayTable("http://www.data.gov/media/2013/11/opendatasites1.csv",true);
        ?>
    </div>
</div>

<div style="display: none;">
    <div id="data_table_3" class="data_table_opengov">
        <?php
        displayTable("http://www.data.gov/media/2013/11/opendatasites1.csv",true);
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
            echo "<td>$column</td>";
        }
        echo '</tr>';
    }
    echo '</table>';
    fclose($handle);
}
?>