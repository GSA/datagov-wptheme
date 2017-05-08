<?php
/*
 * Demo serverside file for Leaflet Search Plugin
 * https://github.com/stefanocudini/leaflet-search
 *
 * http://labs.easyblog.it/maps/leaflet-search
 *
 * Copyright 2013, Stefano Cudini - http://labs.easyblog.it/stefano-cudini/
 * Licensed under the MIT license.
 
 What's:
	 php code for testing jsonp and ajax features
	 
	 receive get parameters:
	 	q		 :	search text
	 	callback :	callback name for jsonp request

 Example Ajax:
 	request:
 		search.php?q=dark
 	response:
		[{"loc":[41.34419,13.242145],"title":"darkblue"},{"loc":[41.67919,13.122145],"title":"darkred"}]

 Example Jsonp:
 	request:
 		search.php?q=dark&callback=L.Control.Search.callJsonp
 	response:
		L.Control.Search.callJsonp([{"loc":[41.34419,13.242145],"title":"darkblue"},{"loc":[41.67919,13.122145],"title":"darkred"}])

 Example Bulk data:
 	request:
 		search.php?q=roma&cities=1
 	response:
		[{"title":"Romainville","loc":[48.8854,2.43482]},{"title":"Roma","loc":[41.89474,12.4839]},{"title":"Roman","loc":[46.91667,26.91667]}]


 Example Ajax Empty Result:
 	request:
 		search.php?q=xx
 	response:
		{"ok":1,"results":[]}

 Example Error Result:
 	request:
 		search.php?s=dark
 	response:
		{"ok":0,"errmsg":"specify query parameter"}

*/

if(!isset($_GET['q']) or empty($_GET['q']))
	die( json_encode(array('ok'=>0, 'errmsg'=>'specify q parameter') ) );

$data = json_decode('[
	{"loc":[41.575330,13.102411], "title":"aquamarine"},
	{"loc":[41.575730,13.002411], "title":"black"},
	{"loc":[41.807149,13.162994], "title":"blue"},
	{"loc":[41.507149,13.172994], "title":"chocolate"},
	{"loc":[41.847149,14.132994], "title":"coral"},
	{"loc":[41.219190,13.062145], "title":"cyan"},
	{"loc":[41.344190,13.242145], "title":"darkblue"},	
	{"loc":[41.679190,13.122145], "title":"darkred"},
	{"loc":[41.329190,13.192145], "title":"darkgray"},
	{"loc":[41.379290,13.122545], "title":"dodgerblue"},
	{"loc":[41.409190,13.362145], "title":"gray"},
	{"loc":[41.794008,12.583884], "title":"green"},	
	{"loc":[41.805008,12.982884], "title":"greenyellow"},
	{"loc":[41.536175,13.273590], "title":"red"},
	{"loc":[41.516175,13.373590], "title":"rosybrown"},
	{"loc":[41.506175,13.173590], "title":"royalblue"},
	{"loc":[41.836175,13.673590], "title":"salmon"},
	{"loc":[41.796175,13.570590], "title":"seagreen"},
	{"loc":[41.436175,13.573590], "title":"seashell"},
	{"loc":[41.336175,13.973590], "title":"silver"},
	{"loc":[41.236175,13.273590], "title":"skyblue"},
	{"loc":[41.546175,13.473590], "title":"yellow"},
	{"loc":[41.239190,13.032145], "title":"white"}
]',true);	//SIMULATE A DATABASE data
//the searched field is: title

if(isset($_GET['cities']))	//SIMULATE A BIG DATABASE, for ajax-bulk.html example
	$data = json_decode( file_get_contents('cities15000.json'), true);
//load big data store, cities15000.json (about 14000 records)

function searchInit($text)	//search initial text in titles
{
	$reg = "/^".$_GET['q']."/i";	//initial case insensitive searching
	return (bool)@preg_match($reg, $text['title']);
}
$fdata = array_filter($data, 'searchInit');	//filter data
$fdata = array_values($fdata);	//reset $fdata indexs

$JSON = json_encode($fdata,true);

#if($_SERVER['REMOTE_ADDR']=='127.0.0.1') sleep(1);
//simulate connection latency for localhost tests
@header("Content-type: application/json; charset=utf-8");

if(isset($_GET['callback']) and !empty($_GET['callback']))	//support for JSONP request
	echo $_GET['callback']."($JSON)";
else
	echo $JSON;	//AJAX request


?>
