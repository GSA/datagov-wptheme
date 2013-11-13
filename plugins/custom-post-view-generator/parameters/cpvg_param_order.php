<?php
class cpvg_param_order{
	public function getParameterData($type='order') {
		$param_data = array();
		if ($type == 'order'){
			$param_data['order']['fields'] = array('order'=>'Order Type', 'orderby' => 'Order Parameter');
			$param_data['order']['mutiple_choices'] = array();
			
			$param_data['order']['choices'] = array();

			$param_data['order']['message'] = array("When creating mutiple order parameters for this section, choose diferent a parameter for each filter.");		
			
			$param_data['order']['choices']['order'] = array('ASC'=>'Ascending','DESC'=>'Descending');
			$param_data['order']['choices']['orderby'] = array('none'=>'No order',
															   'rand'=>'Random order',
															   'ID'=>'Order by post id',
															   'author'=>'Order by author',
															   'title'=>'Order by title',
															   'date'=>'Order by date',
															   'modified'=>'Order by last modified date',
															   'parent'=>'Order by post/page parent id',
															   'comment_count'=>'Order by number of comments',
															   'menu_order'=>'Order by page Order',
															   'meta_value'=>'Order by meta Value',
															   'meta_value_num'=>'Order by numeric meta value');
		}
		return $param_data;
	}

	public function applyParameterData($type,$data) {
		switch($data['parameter']){			
			case 'order':
			case 'orderby':
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
