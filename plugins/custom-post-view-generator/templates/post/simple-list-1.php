<style type="text/css">
	.cpvg-field-name{ font-weight:bold; }
	.cpvg-field-value{ }
	.cpvg-field-value ul {
		/*padding:0px;
		margin:0px 0px 0px 20px;*/
	}
</style>
<?php
	foreach($record_data as $record){
		if(!isset($record['label'])){ //if there is no label then it is a heading or horizontal line or a similar element
			echo $record['value'];
		}else{
			echo "<span class='cpvg-field-record'><span class='cpvg-field-name'>".$record['label']."</span>: <span class='cpvg-field-value'>".$record['value']."</span></span><br>";
		}
	}
?>
