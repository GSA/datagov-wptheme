<?php
class cpvg_date{
    public function adminProperties() {
		$output_options1 = array('%a, %d %b %Y'=>'Thu, 13 Jan 2011',
								 '%A, %d %B %Y'=>'Thursday, 13 January 2011',
								 '%d %b %Y'=>'13 Jan 2011',
								 '%d %B %Y'=>'13 January 2011',
								 '%m/%d/%y'=>'01/13/11','%m/%d/%Y'=>'01/13/2011',
								 '%d/%m/%y'=>'13/01/11','%d/%m/%Y'=>'13/01/2011',
								 '%e/%m/%y'=>'1/12/11','%m/%e/%y'=>'12/1/11',
								 '%m-%d-%y'=>'01-13-11','%m-%d-%Y'=>'01-13-2011',
								 '%d-%m-%y'=>'13-01-11','%d-%m-%Y'=>'13-01-2011',
								 '%m-%e-%y'=>'12-1-11','%e-%m-%y'=>'1-12-11');

		return array('cpvg_date' => array('label'=>'Date',
										  'options'=>array($output_options1)));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			//show something in the preview
			$value = time();
		}

		if(!is_numeric($value)){ //MYSQL DATE
			$value = strtotime($value);
		}

		//$output_options[1] -> date format from $output_options1
		return @strftime($output_options[1],$value);
	}
}
?>