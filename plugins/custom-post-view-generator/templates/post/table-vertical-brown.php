<style type="text/css">
	.cpvg-table{
		margin-top:10px;
		border: 1px solid #684D0B;
		border-collapse: collapse;
		color: #4D4D4D;
	}
	.cpvg-table td {
		border: 1px solid #684D0B;
		padding: 2px;
	}
	.cpvg-table tr {
		border: 1px solid #684D0B;
	}
	.cpvg-table th {
		border: 1px solid #684D0B;
		background-color: #AA7E15;
		padding-left: 2px;	
		color: #E5E5E5;	
	}
	.cpvg-table td ul {
		border: 1px solid #684D0B;
	}	
</style>

<?php
	$processed_data = array();
	$output = "";
	foreach($record_data as $record){
		if(!isset($record['label'])){
			//if there is no label then it is a heading or horizontal line or a similar element
			//this finishes the table and prints the element			
			if(!empty($processed_data)){
				$output.="<table class='cpvg-table'>".implode("",$processed_data)."</table>";
				$processed_data = array();
			}
			$output.=$record['value'];
		}else{
			$processed_data[]="<tr><th>".$record['label']."</th><td>".$record['value']."</td>";
		}
	}
	
	if(!empty($processed_data)){
		$output.="<table class='cpvg-table'>".implode("",$processed_data)."</table>";
		$processed_data = array();
	}		
	echo $output;
?>
