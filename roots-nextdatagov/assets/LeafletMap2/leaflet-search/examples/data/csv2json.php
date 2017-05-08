<?php
/*
 * Leaflet Search Plugin
 * https://github.com/stefanocudini/leaflet-search
 * http://labs.easyblog.it/maps/leaflet-search
 *
 * Copyright 2013, Stefano Cudini - http://labs.easyblog.it/stefano-cudini/
 * Licensed under the MIT license.
 
 What's:
	 php code for convert cities15000.txt in cities15000.json, used in ajax-bulk example

  using geonames database:
  	http://download.geonames.org/export/dump/
 
*/

$data = array();
$m = 30000;	//max imports
$n = 0;	//counter
if ($csvFile = fopen('cities15000.raw.txt', 'r'))
{
	while ($row = fgetcsv($csvFile, 5000, "\t") and --$m)
	{
		if(!preg_match("#[^a-zA-Z0-9]#", $row[1]))	//filter accents name
		{
			$data[]= array('title'=>$row[1], 'loc'=>array( (float)$row[4], (float)$row[5]) );
			$n++;
		}
	}
	fclose($csvFile);
}

echo "generated $n records\n";

$json = json_encode($data);

file_put_contents('cities15000.json', $json);

?>
