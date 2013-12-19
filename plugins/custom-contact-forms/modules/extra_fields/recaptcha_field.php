<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/
if ( ! class_exists( 'ccf_recaptcha_field' ) ) {
	class ccf_recaptcha_field {
        
		var $field_code = '';
		
		function __construct( $public_key, $label = NULL, $slug = NULL, $class = NULL, $initial_value = NULL, $field_instructions = NULL ) {
			$class_attr = ($class == NULL) ? '' : $class;
			$label = ( ! empty( $label ) ) ? '<div><label for="' . ccf_utils::decodeOption( $slug, 1, 1 ) . '">* ' . ccf_utils::decodeOption( $label, 1, 1 ) . '</label></div>' : '';
			
			if ($field_instructions == NULL) {
				$instructions_attr = '';
				$tooltip_class = '';
			} else {
				$instructions_attr = ' title="' . esc_attr( $field_instructions ) . '" ';
				$tooltip_class = 'ccf-tooltip-field';
			}
            
			ob_start();
			?>
			<div id="recaptcha_widget" style="display:none" class="<?php echo esc_attr( $class_attr ); ?>">
				<div class="right">
					<img class="logo" width="75" height="80" src="<?php echo plugins_url(); ?>/custom-contact-forms/images/recaptcha-logo-white.png" />
					<div class="reload"><a href="javascript:Recaptcha.reload()"><img width="25" height="18" src="<?php echo plugins_url(); ?>/custom-contact-forms/images/refresh.png" alt="Reload captcha" /></a></div>
					<div class="recaptcha_only_if_image audio"><a href="javascript:Recaptcha.switch_type('audio')"><img width="25" height="15" src="<?php echo plugins_url(); ?>/custom-contact-forms/images/audio.png" alt="Play audio captcha" /></a></div>
					<!--<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')"><img width="25" height="18" src="<?php echo plugins_url(); ?>/custom-contact-forms/images/refresh.png" alt="Reload captcha" /></a></div>
					--><div class="help"><a href="javascript:Recaptcha.showhelp()"><img width="25" height="16" src="<?php echo plugins_url(); ?>/custom-contact-forms/images/help.png" alt="Recaptcha help" /></a></div>
				</div>
				<div class="left">
					<div id="recaptcha_image"></div>
					<div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>
					<?php echo $label; ?>
					<input value="<?php if ( ! empty( $initial_value  ) ) echo esc_attr( $initial_value ); ?>" type="text" id="recaptcha_response_field" name="recaptcha_response_field" class="<?php echo $tooltip_class; ?>" <?php echo $instructions_attr; ?> />
				</div>
				
			  </div>
			 
			  <script type="text/javascript"
				 src="http://www.google.com/recaptcha/api/challenge?k=<?php echo $public_key; ?>">
			  </script>
			  <noscript>
				<iframe src="http://www.google.com/recaptcha/api/noscript?k=<?php echo $public_key; ?>"
					 height="300" width="500" frameborder="0"></iframe><br>
				<textarea name="recaptcha_challenge_field" rows="3" cols="40">
				</textarea>
				<input type="hidden" name="recaptcha_response_field"
					 value="manual_challenge">
			  </noscript>
			<?php
			$this->field_code = ob_get_clean();
		}
		
		function getCode() {
			return $this->field_code;
		}
	}
}
?>