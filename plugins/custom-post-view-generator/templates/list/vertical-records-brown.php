<style type="text/css">
	/* TEMPLATE CSS */	
	.cpvg-record{border: 1px solid #684D0B;background-color: #AA7E15;padding: 10px;margin-top:10px;margin-bottom:10px; -moz-border-radius: 8px; -webkit-border-radius: 8px; -khtml-border-radius: 8px; border-radius: 8px; }

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
	.themecolor{ background-color: #AA7E15;border: 1px solid #684D0B; -moz-border-radius: 8px;  -webkit-border-radius: 8px; -khtml-border-radius: 8px; border-radius: 8px;}
	.themecolor.normal{ background-color: #7B5805;color: White;border: solid 1px #684D0B; }
	.themecolor.active{ background-color: #553F0C;color: #BFBFBF;border: solid 1px #684D0B; }
	.pager.themecolor .btn{ height:27px; background-color: #7B5805;color: White;border: solid 1px #684D0B; }
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
			echo "<div class='cpvg-record'>";
			foreach($record_data as $record){
				echo "<b>".$record['label']."</b>: ".$record['value']."<br>";
			}
			echo "</div>";
		}	
	echo "</div>";
?>
