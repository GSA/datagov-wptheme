<style type="text/css">
	/* TEMPLATE CSS */		
	.cpvg-record-div{ color: #EFEFEF; background-color: #1A1A1A; -moz-border-radius: 8px; -webkit-border-radius: 8px; -khtml-border-radius: 8px; border-radius: 8px; }
	.cpvg-record-div .cpvg-table{ border:0px; padding:5px; margin-top:5px; margin-bottom:8px; }    
	.cpvg-table td{ border:0px;}
	.cpvg-table th{ width: 30%;  color: #BFBFBF; }
    
	/* PAGINATION CSS */
	.pager{ font-family: "Bitstream Cyberbit","MS Georgia","Times New Roman",Bodoni,Garamond,"Minion Web","ITC Stone Serif","Helvetica"; -webkit-background-size: 100%;-o-background-size: 100%;-khtml-background-size: 100%;-moz-border-radius: 8px;-webkit-border-radius: 8px;height: 32px;padding: 0;margin: 0 0 10px 0;padding-top: 5px;padding-left: 3px; }
	.pager div.short{ float: right;margin: 0;padding: 0;margin-right: 10px;width: 74px; }
	.pager div.short input{ width: 28px;height: 20px; border: none;float: left; }
	.pager ul{ list-style: none;padding: 0;margin: 0;float: left;margin-right: 4px; }
	.pager ul li{ display: inline;margin-left: 2px; }
	.pager ul li a.normal{ text-decoration: none;display: inline-table;width: 20px;height: 20px;text-align: center;border-radius: 4px;-moz-border-radius: 4px; }
	.pager span{ margin-left: 4px;float: left; }
	.pager .btn{ display: block;text-align: center;float: left;padding: 0;margin: 0;margin-left: 4px;cursor: pointer;border-radius: 4px;-moz-border-radius: 4px; }
	.pager.themecolor .btn{ height: 25px; }
	.pager ul li a.active{ text-decoration: none;display: inline-table;width: 20px;height: 20px;text-align: center;border-radius: 4px;-moz-border-radius: 4px; }

	/* PAGINATION THEME CSS */
	.themecolor{ background-color: #1A1A1A; color: #888888; -moz-border-radius: 8px;  -webkit-border-radius: 8px; -khtml-border-radius: 8px; border-radius: 8px;}
	.themecolor.normal{ background-color: #7F7F7F;color: White;border: solid 1px #BFBFBF; }
	.themecolor.active{ background-color: #4D4D4D;color: #BFBFBF;border: solid 1px #BFBFBF; }
	.pager.themecolor .btn{ background-color: #7F7F7F;color: White;border: solid 1px #BFBFBF; }
</style>

<script type='text/javascript'>
jQuery(document).ready(function(){
	<?php if(isset($pagination)){ ?>
		jQuery('#cpvg-paginator').smartpaginator({ totalrecords: <?php echo count($records_data); ?>, 
												   recordsperpage: <?php echo $pagination; ?>,
												   datacontainer: 'cpvg-records',
												   vertical_th: true }); 
	<?php } ?>
});
</script>

<?php
	//PAGINATION DIV
	echo "<div id='cpvg-paginator'></div>";
	//RECORDS
	echo "<div id='cpvg-records'>";
	foreach($records_data as $record_index => $record_data){
		echo "<div class='cpvg-record-div'><table class='cpvg-table'>\n";
		foreach($record_data as $record){
			echo "<tr><th>".$record['label']."</th><td>".$record['value']."</td></tr>";
		}
		echo "</table></div>\n";
	}
	echo "</div>";
?>
