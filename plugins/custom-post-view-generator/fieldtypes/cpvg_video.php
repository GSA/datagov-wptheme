<?php
class cpvg_video{
    public function adminProperties() {
		$output_options1 = array('flowpayer'=>'Use Flash Player',
								 'html5tag'=>'Use Html5 tag');

		$output_options2 = array('1'  =>'Original Size',
								 '0.10'=>'Reduce 10%',
								 '0.20'=>'Reduce 20%',
								 '0.30'=>'Reduce 30%',
								 '0.40'=>'Reduce 40%',
								 '0.50'=>'Reduce 50%',
								 '0.60'=>'Reduce 60%',
								 '0.70'=>'Reduce 70%',
								 '0.80'=>'Reduce 80%',
								 '0.90'=>'Reduce 90%',
								 '1.10'=>'Enlarge 110%',
								 '1.20'=>'Enlarge 120%',
								 '1.30'=>'Enlarge 120%',
								 '1.40'=>'Enlarge 140%',
								 '1.50'=>'Enlarge 150%',
								 '1.60'=>'Enlarge 160%',
								 '1.70'=>'Enlarge 170%',
								 '1.80'=>'Enlarge 180%',
								 '1.90'=>'Enlarge 190%',
								 '2'  =>'Enlarge 200%');

		return array('cpvg_video' => array('label'=>'Video',
									  'options' => array($output_options1,$output_options2)));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {
		if(is_string($value) && $value=='NOT_SET'){
			//show something in the preview
			$video_url = "http://pseudo01.hddn.com/vod/demo.flowplayervod/flowplayer-700.flv";
			$cvpg_video_size = array(425,300);
		}else{
			if(wp_get_attachment_url($value)){
				$video_url = wp_get_attachment_url($value);
			}else{
				$video_url = $value;
			}

			//DETECT VIDEO SIZE
			$filename = get_attached_file($value);
			//many video formats
			require_once(CPVG_PLUGIN_DIR."/libs/getid3/getid3.php");
			$getid3_instance = new getID3;
			$getid3_instance_info = $getid3_instance->analyze($filename);
			if(isset($getid3_instance_info['video']['resolution_x']) && isset($getid3_instance_info['video']['resolution_y']) ){
				if($getid3_instance_info['video']['resolution_x']>0 && $getid3_instance_info['video']['resolution_y'] >0){
					$cvpg_video_size = array($getid3_instance_info['video']['resolution_x'],
											$getid3_instance_info['video']['resolution_y']);
				}
			}
			//ogg video
			if(!isset($cvpg_video_size)){
				require_once(CPVG_PLUGIN_DIR."/libs/oggclass/ogg.class.php");
				$ogg_video=new Ogg($filename,NOCACHING);
				if (isset($ogg_video->Streams['theora'])){
					$cvpg_video_size = array($ogg_video->Streams['theora']['width'],
											$ogg_video->Streams['theora']['height']);
				}

			}
			//width and height not detected
			if(!isset($cvpg_video_size)){
				$cvpg_video_size = array(320,240);
			}

			//Sets the video size
			$cvpg_video_size [0]*=floatval($output_options[2]);
			$cvpg_video_size [1]*=floatval($output_options[2]);
		}

		$html = "";

		switch ($output_options[1]){
			case 'html5tag':
			   $html.= "<video src='".$video_url."' controls='controls' width='".$cvpg_video_size[0]."' height='".$cvpg_video_size[1]."'>
							This browser does not support html5 video tag.
					    </video>";
				break;
			case 'flowpayer':
				$current_id = rand(0,500);
				$html.= "<a id='cpvg_player_container_".$current_id."' style='display:block;width:".$cvpg_video_size[0]."px;height:".$cvpg_video_size[1]."px;' href='".$video_url."'></a>";
				$html.="<script language='JavaScript'>
						flowplayer( 'cpvg_player_container_".$current_id."',
									'". CPVG_PLUGIN_URL . "/libs/flowplayer/flowplayer-3.2.7.swf',
										{
											clip: {
												autoPlay: false,
												autoBuffering: false
											}
										}
								  );
				</script>";
				break;
		}

		return $html;
	}
}
?>