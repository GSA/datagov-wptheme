$j(document).ready(function(){
							
	$j('.checkall').live("click", function() {
		var checked_status = this.checked;
		$j(this).parents("form").find("input.object-check ,input.checkall").each(function() {
			if ($j(this).attr("type") == "checkbox")
				this.checked = checked_status;
		});
	});
	
	$j('.form-options-expand-link').click(function() {
		$j(this)
			.parent()
			.parent()
			.next()
			.find(".form-extra-options:first")
			.toggle();
	});
	
	$j('.submission-content-expand-button').click(function() {
		$j(this)
		.parentsUntil("tr")
		.parent()
		.next()
		.toggle();
	});
	
	
	$j('.fixed-fields-options-expand-link').click(function() {
		$j(this)
			.parent()
			.parent()
			.next()
			.find(".fixed-fields-extra-options:first")
			.toggle();
	});
	
	$j('.fields-options-expand-link').click(function() {
		$j(this)
			.parent()
			.parent()
			.next()
			.find(".fields-extra-options:first")
			.toggle();
	});
	
	$j("#ccf-usage-popover").dialog({
		height: 420,
		width:600,
		modal: true,
		autoOpen: false
	});
		
	$j(".usage-popover-button").click(function() { $j("#ccf-usage-popover").dialog('open'); });
	
	$j("#ccf-quick-start-popover").dialog({
		height: 420,
		width:600,
		modal: true,
		autoOpen: false
	});
		
	$j(".quick-start-button").click(function() { $j("#ccf-quick-start-popover").dialog('open'); });
	
	$j("a[title].toollink").tooltip({
		position: "bottom left",
		offset: [-2, 10],
		effect: "fade",
		tipClass: 'ccf-tooltip',
		opacity: 1.0							
	});
	
	
	$j("#customcontactforms-admin #create-fields .field-type-selector").change(function () {
		$j("#customcontactforms-admin #create-fields .field-type-selector option:selected").each(function () {
			if ($j(this).text() == "File") {
				$j("#customcontactforms-admin #create-fields .file-fields").fadeIn("slow");
			} else {
				$j("#customcontactforms-admin #create-fields .file-fields").hide();	
			}
		});
	}).trigger('change');

	var $tabs = $j( "#customcontactforms-admin #ccf-tabs" ).tabs();
	if (ccfLang.selected_tab != 0) $tabs.tabs('select', '#' + ccfLang.selected_tab);
	
	var itemList = $j('.sortable');
	itemList.sortable({
		placeholder: "ui-state-highlight", 
		update: function(event, ui) {
			input_target = "#input_" + ui.item.parent().attr("id");
			$j(input_target).val(ui.item.parent().sortable('toArray').toString());      
			return; 
		}
	});
});