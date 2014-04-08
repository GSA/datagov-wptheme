<?php
class cpvg_param_time{
	public function getParameterData($type='filter') {
		$param_data = array();

		switch($type){
			case 'filter':
				$param_data['time']['fields'] = array('year'=>'Year (4 Digit)', 'monthnum'=>'Month (1-12)','w'=>'Week of the year (0-53)',
													  'day'=>'Day of the month (1-31)', 'hour'=>'Hour (0-23)', 'minute'=>'Minute (0-60)',
													  'second'=>'Second (0-60)', 'custom_date' => 'Custom Date');

				//$param_data['post']['mutiple_choices'] = array('monthnum','w','day','hour','minute','second');

				$param_data['time']['choices']['monthnum']=array('1'=>'January', '2'=>'February', '3'=>'March', '4'=>'April', '5'=>'May', '6'=>'June', '7'=>'July', '8'=>'August', '9'=>'September', '10'=>'October', '11'=>'November', '12'=>'December');
				$param_data['time']['choices']['w']=array('1'=>'Week 1', '2'=>'Week 2', '3'=>'Week 3', '4'=>'Week 4', '5'=>'Week 5', '6'=>'Week 6', '7'=>'Week 7', '8'=>'Week 8', '9'=>'Week 9', '10'=>'Week 10', '11'=>'Week 11', '12'=>'Week 12', '13'=>'Week 13', '14'=>'Week 14', '15'=>'Week 15', '16'=>'Week 16', '17'=>'Week 17', '18'=>'Week 18', '19'=>'Week 19', '20'=>'Week 20', '21'=>'Week 21', '22'=>'Week 22', '23'=>'Week 23', '24'=>'Week 24', '25'=>'Week 25', '26'=>'Week 26', '27'=>'Week 27', '28'=>'Week 28', '29'=>'Week 29', '30'=>'Week 30', '31'=>'Week 31', '32'=>'Week 32', '33'=>'Week 33', '34'=>'Week 34', '35'=>'Week 35', '36'=>'Week 36', '37'=>'Week 37', '38'=>'Week 38', '39'=>'Week 39', '40'=>'Week 40', '41'=>'Week 41', '42'=>'Week 42', '43'=>'Week 43', '44'=>'Week 44', '45'=>'Week 45', '46'=>'Week 46', '47'=>'Week 47', '48'=>'Week 48', '49'=>'Week 49', '50'=>'Week 50', '51'=>'Week 51', '52'=>'Week 52', '53'=>'Week 53');
				$param_data['time']['choices']['day']=array('1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'9', '10'=>'10', '11'=>'11', '12'=>'12', '13'=>'13', '14'=>'14', '15'=>'15', '16'=>'16', '17'=>'17', '18'=>'18', '19'=>'19', '20'=>'20', '21'=>'21', '22'=>'22', '23'=>'23', '24'=>'24', '25'=>'25', '26'=>'26', '27'=>'27', '28'=>'28', '29'=>'29', '30'=>'30', '31'=>'31', '32'=>'32');
				$param_data['time']['choices']['hour']=array('0'=>'0:00', '1'=>'1:00', '2'=>'2:00', '3'=>'3:00', '4'=>'4:00', '5'=>'5:00', '6'=>'6:00', '7'=>'7:00', '8'=>'8:00', '9'=>'9:00', '10'=>'10:00', '11'=>'11:00', '12'=>'12:00', '13'=>'13:00', '14'=>'14:00', '15'=>'15:00', '16'=>'16:00', '17'=>'17:00', '18'=>'18:00', '19'=>'19:00', '20'=>'20:00', '21'=>'21:00', '22'=>'22:00', '23'=>'23:00');
				$param_data['time']['choices']['minute']=array('0'=>'0', '1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'9', '10'=>'10', '11'=>'11', '12'=>'12', '13'=>'13', '14'=>'14', '15'=>'15', '16'=>'16', '17'=>'17', '18'=>'18', '19'=>'19', '20'=>'20', '21'=>'21', '22'=>'22', '23'=>'23', '24'=>'24', '25'=>'25', '26'=>'26', '27'=>'27', '28'=>'28', '29'=>'29', '30'=>'30', '31'=>'31', '32'=>'32', '33'=>'33', '34'=>'34', '35'=>'35', '36'=>'36', '37'=>'37', '38'=>'38', '39'=>'39', '40'=>'40', '41'=>'41', '42'=>'42', '43'=>'43', '44'=>'44', '45'=>'45', '46'=>'46', '47'=>'47', '48'=>'48', '49'=>'49', '50'=>'50', '51'=>'51', '52'=>'52', '53'=>'53', '54'=>'54', '55'=>'55', '56'=>'56', '57'=>'57', '58'=>'58', '59'=>'59', '60'=>'60');
				$param_data['time']['choices']['second']=array('0'=>'0', '1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'9', '10'=>'10', '11'=>'11', '12'=>'12', '13'=>'13', '14'=>'14', '15'=>'15', '16'=>'16', '17'=>'17', '18'=>'18', '19'=>'19', '20'=>'20', '21'=>'21', '22'=>'22', '23'=>'23', '24'=>'24', '25'=>'25', '26'=>'26', '27'=>'27', '28'=>'28', '29'=>'29', '30'=>'30', '31'=>'31', '32'=>'32', '33'=>'33', '34'=>'34', '35'=>'35', '36'=>'36', '37'=>'37', '38'=>'38', '39'=>'39', '40'=>'40', '41'=>'41', '42'=>'42', '43'=>'43', '44'=>'44', '45'=>'45', '46'=>'46', '47'=>'47', '48'=>'48', '49'=>'49', '50'=>'50', '51'=>'51', '52'=>'52', '53'=>'53', '54'=>'54', '55'=>'55', '56'=>'56', '57'=>'57', '58'=>'58', '59'=>'59', '60'=>'60');

				$param_data['time']['message'] = array("When creating mutiple filters for this section, choose diferent a parameter for each filter.",
													   "When creating mutiple filters for this section, the query will return all posts that match ALL the filters.",
													   "To select a time range use Custom Date paremeter and use comma separated values: \">=2011-10-15\" or \">=2011-10-15, <2011-10-20\"");
			break;
		}

		return $param_data;
	}

	public function applyParameterData($type,$data) {
		switch($type){
			case 'filter':
				switch($data['parameter']){
					case 'custom_date':
						$date_values = explode(",",$data['value']);
						$output = "";
						foreach($date_values as $date_value){
							$date_value = trim($date_value);
							if(!empty($date_value)){
								preg_match("([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})",$date_value,$data_matches);
								$date_found = $data_matches[0];
								if(isset($data_matches[0])){
									if(!empty($data_matches[0])){
										$comparator = trim(str_replace($data_matches[0],"",$date_value));
									}
								}
								$output.= " AND post_date ".$comparator." '".$data_matches[0]."'";
							}
						}
						return array('custom_date'=> $output);
						//return array('custom_date'=>" AND post_date ".implode(" AND post_date ",explode(",",$data['value'])));
					default:
						if(is_array($data['value'])){
							return array($data['parameter'] => implode("",$data['value']));
						}else{
							return array($data['parameter'] => $data['value']);
						}
				}
			break;
		}
		return array();
	}
}
?>