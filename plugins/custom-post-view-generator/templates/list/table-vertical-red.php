<style type="text/css">
	/* TEMPLATE CSS */
	.cpvg-table{ margin-top:10px;border: 1px solid #AF0E0E; border-collapse: collapse; }
	.cpvg-table td { border: 1px solid #AF0E0E; padding: 2px;	}
	.cpvg-table th { border: 1px solid #AF0E0E; background-color: #EE1313; padding: 2px; color: #F5F5F5; width:30%; }
	.cpvg-table td ul { }
	.cpvg-table .cpvg-table-blank{ border-left: 1px solid #FFFFFF; border-right: 1px solid #FFFFFF; }
	.cpvg-page{ border: 1px solid #AF0E0E; margin: 10px; }
	
	/* PAGINATION CSS */
	.pager{ font-family: "Bitstream Cyberbit","MS Georgia","Times New Roman",Bodoni,Garamond,"Minion Web","ITC Stone Serif","Helvetica";height: 32px;padding: 0;margin: 0;padding-top: 5px;padding-left: 3px; margin-bottom: 18px;}
	.pager div.short{ float: right;margin: 0;padding: 0;margin-right: 10px;width: 74px; }
	.pager div.short input{ width: 28px;height: 20px;border: none;float: left; }
	.pager ul{ list-style: none;padding: 0;margin: 0;float: left;margin-right: 4px; }
	.pager ul li{ display: inline;margin-left: 2px; }
	.pager ul li a.normal{ text-decoration: none;display: inline-table;width: 20px;height: 20px;text-align: center; }
	.pager span{ margin-left: 4px;float: left; }
	.pager .btn{ display: block;text-align: center;float: left;padding: 0;margin: 0;margin-left: 4px;cursor: pointer; }
	.pager.themecolor .btn{ height: 24px; }
	.pager ul li a.active{ text-decoration: none;display: inline-table;width: 20px;height: 20px;text-align: center; }

	/* PAGINATION THEME CSS */
	.themecolor{ background-color: #EE1313;border: 1px solid #AF0E0E; }
	.themecolor.normal{ background-color: #B70A0A;color: White;border: solid 1px #AF0E0E; }
	.themecolor.active{ background-color: #8C0C0C;color: White;border: solid 1px #AF0E0E; }
	.pager.themecolor .btn{ height:28px; background-color: #B70A0A;color: White;border: solid 1px #AF0E0E; }
</style>

<script type='text/javascript'>
jQuery(document).ready(function(){
	<?php if(isset($pagination)){ ?>
		jQuery('#cpvg-paginator').smartpaginator({ totalrecords: <?php $val = count($records_data)/$pagination; if(is_int($val)){ echo $val; }else{ echo intval($val)+1; } ?>, 
												    recordsperpage: 1,
												    datacontainer: 'cpvg-records',
												    dataelement: 'table',
												    vertical_th: true }); 
	<?php } ?>
});
</script>

<?php 
	//NOTE: THE PHP DOES THE GROUPING SO THAT THE TABLE HAVE IDENTICAL TH COLUMN WIDTH FOR EACH PAGE
	if(isset($records_data[0])){
		//PAGINATION DIV
		echo "<div id='cpvg-paginator'></div>\n\n";

		//RECORDS TABLE RECORDS			
		echo "<div id='cpvg-records'><table class='cpvg-table'>\n";
		$num_records = count($records_data);
		foreach($records_data as $record_index => $record_data){
			foreach($record_data as $record){
				echo "<tr><th>".$record['label']."</th><td>".$record['value']."</td>\n";
			}
			
			if(($num_records-1) != $record_index){ 
				if(isset($pagination)){
					if(is_int(($record_index+1)/$pagination) ){ 
						echo "</table>\n<table class='cpvg-table'>";
					}else{ 
						echo "<tr><td colspan='2' class='cpvg-table-blank'></td></tr>\n";
					}
				}else{ 
					echo "<tr><td colspan='2' class='cpvg-table-blank'></td></tr>\n";
				}
			}
		}
		echo "</table></div>";
	}
?>
