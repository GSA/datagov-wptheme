<?php
/**
 * Plugin Name: Suggest a Dataset
 * Plugin URI: http://reisystems.com
 * Description: Suggest a Dataset
 * Version: 0.3
 * Author: Alex Perfilov
 * Author URI: http://reisystems.com
 * License:  GPL3
 */

include_once('model/SuggestedDatasetsPlugin.class.php');

if (is_admin()) {
    $sgd = new SuggestedDatasetsPlugin();
}