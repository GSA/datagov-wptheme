<?php
class cpvg_audio{

    public function adminProperties() {
		$output_options1 = array('flowpayer'=>'Use Flash Player',
								 'html5tag'=>'Use Html5 tag');

		$output_options2 = array('300'=>'Width: 300 px','325'=>'Width: 325 px','350'=>'Width: 350 px',
								 '375'=>'Width: 375 px','400'=>'Width: 400 px','425'=>'Width: 425 px',
								 '450'=>'Width: 450 px','475'=>'Width: 475 px','500'=>'Width: 500 px',
								 '525'=>'Width: 525 px','550'=>'Width: 550 px','575'=>'Width: 575 px',
								 '600'=>'Width: 600 px','625'=>'Width: 625 px','650'=>'Width: 650 px',
								 '675'=>'Width: 675 px','700'=>'Width: 700 px','725'=>'Width: 725 px',
								 '750'=>'Width: 750 px','775'=>'Width: 775 px','800'=>'Width: 800 px',
								 '825'=>'Width: 825 px','850'=>'Width: 850 px','875'=>'Width: 875 px',
								 '900'=>'Width: 900 px' );

		return array('cpvg_audio' => array('label'=>'Audio',
									  'options' => array($output_options1,$output_options2)));
    }

    public function processValue($value='NOT_SET',$output_options='',$additional_data) {

		if(is_string($value) && $value=='NOT_SET'){
			//show something in the preview
			$audio_url = "http://releases.flowplayer.org/data/fake_empire.mp3";
		}else{
			if(wp_get_attachment_url($value)){
				$audio_url = wp_get_attachment_url($value);
			}else{
				$audio_url = $value;
			}
		}

		$html = "";

		switch ($output_options[1]){
			case 'html5tag':
				$html.= "<audio src='".$audio_url."' controls='controls'>
							This browser does not support html5 audio tag.
					     </audio>";
				break;
			case 'flowpayer':
				$current_id = rand(0,500);
				$html.= "<div id='cpvg_audio_player_container_".$current_id."' style='display:block;width:".$output_options[2]."px;height:30px;' href='".$audio_url."'></div>";

				$html.="<script language='JavaScript'>
						flowplayer( 'cpvg_audio_player_container_".$current_id."',
									'". CPVG_PLUGIN_URL . "/libs/flowplayer/flowplayer-3.2.7.swf',
										{
											plugins: {
												controls: {
													fullscreen: false,
													height:30,
													autoHide: false
												}
											},
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