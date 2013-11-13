<?php
class cpvg_param_pagination{
	public function getParameterData($type='pagination') {
		
		$param_data = array();
		if ($type == 'pagination'){
			$param_data['pagination']['fields'] = array('posts_per_page'=>'Post per Page');
			
			
			$param_data['pagination']['choices'] = array();

			$param_data['pagination']['message'] = array("Create only one pagination parameter for each list.");		
			
			$param_data['pagination']['choices']['posts_per_page'] = array(	 '1'=>'1 Posts', '2'=>'2 Posts', '3'=>'3 Posts', '4'=>'4 Posts', '5'=>'5 Posts',
																			 '6'=>'6 Posts', '7'=>'7 Posts', '8'=>'8 Posts', '9'=>'9 Posts',
																			 '10'=>'10 Posts', '11'=>'11 Posts', '12'=>'12 Posts', '13'=>'13 Posts',
																			 '14'=>'14 Posts', '15'=>'15 Posts', '16'=>'16 Posts', '17'=>'17 Posts',
																			 '18'=>'18 Posts', '19'=>'19 Posts', '20'=>'20 Posts', '21'=>'21 Posts',
																			 '22'=>'22 Posts', '23'=>'23 Posts', '24'=>'24 Posts', '25'=>'25 Posts',
																			 '26'=>'26 Posts', '27'=>'27 Posts', '28'=>'28 Posts', '29'=>'29 Posts',
																			 '30'=>'30 Posts', '31'=>'31 Posts', '32'=>'32 Posts', '33'=>'33 Posts',
																			 '34'=>'34 Posts', '35'=>'35 Posts', '36'=>'36 Posts', '37'=>'37 Posts',
																			 '38'=>'38 Posts', '39'=>'39 Posts', '40'=>'40 Posts', '41'=>'41 Posts',
																			 '42'=>'42 Posts', '43'=>'43 Posts', '44'=>'44 Posts', '45'=>'45 Posts',
																			 '46'=>'46 Posts', '47'=>'47 Posts', '48'=>'48 Posts', '49'=>'49 Posts',
																			 '50'=>'50 Posts', '51'=>'51 Posts', '52'=>'52 Posts', '53'=>'53 Posts',
																			 '54'=>'54 Posts', '55'=>'55 Posts', '56'=>'56 Posts', '57'=>'57 Posts',
																			 '58'=>'58 Posts', '59'=>'59 Posts', '60'=>'60 Posts', '61'=>'61 Posts',
																			 '62'=>'62 Posts', '63'=>'63 Posts', '64'=>'64 Posts', '65'=>'65 Posts',
																			 '66'=>'66 Posts', '67'=>'67 Posts', '68'=>'68 Posts', '69'=>'69 Posts',
																			 '70'=>'70 Posts', '71'=>'71 Posts', '72'=>'72 Posts', '73'=>'73 Posts',
																			 '74'=>'74 Posts', '75'=>'75 Posts', '76'=>'76 Posts', '77'=>'77 Posts',
																			 '78'=>'78 Posts', '79'=>'79 Posts', '80'=>'80 Posts', '81'=>'81 Posts',
																			 '82'=>'82 Posts', '83'=>'83 Posts', '84'=>'84 Posts', '85'=>'85 Posts',
																			 '86'=>'86 Posts', '87'=>'87 Posts', '88'=>'88 Posts', '89'=>'89 Posts',
																			 '90'=>'90 Posts', '91'=>'91 Posts', '92'=>'92 Posts', '93'=>'93 Posts',
																			 '94'=>'94 Posts', '95'=>'95 Posts', '96'=>'96 Posts', '97'=>'97 Posts',
																			 '98'=>'98 Posts', '99'=>'99 Posts', '100'=>'100 Posts');			
		}																	

		return $param_data;
	}

	public function applyParameterData($type,$data) {
		switch($data['parameter']){			
			case 'posts_per_page':
			default:
				if(is_array($data['value'])){
					$data['value'] = implode(",",$data['value']);
				}
				return array($data['parameter'] => $data['value']);
		}
		return array();
	}
}
?>
