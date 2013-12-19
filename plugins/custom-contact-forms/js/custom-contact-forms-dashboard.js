/* Custom Contact Forms Dashboard Javascript */

$j = jQuery.noConflict();

$j(document).ready(function() {
	
	$j(".ccf-view-submission").each(function() {
		var submission_window = $j(this).next();
		submission_window.dialog({
			height: 420,
			width:600,
			modal: true,
			autoOpen: false
		});
		$j(this).click(function() { submission_window.dialog('open'); });
	});
});