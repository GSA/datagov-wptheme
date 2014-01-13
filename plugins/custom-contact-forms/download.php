<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/
error_reporting(0);
if (!empty($_GET['location']) && preg_match('/^export\/ccf[^\/^\.]+\.(sql|csv)$/i', $_GET['location'])) {
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . basename($_GET['location']));
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . filesize($_GET['location']));
	ob_clean();
	flush();
	echo file_get_contents($_GET['location']);
}
exit();
?>