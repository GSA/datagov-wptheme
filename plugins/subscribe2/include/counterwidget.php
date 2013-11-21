<?php
class S2_Counter_widget extends WP_Widget {
	/**
	Declares the S2_Counter_widget class.
	*/
	function S2_Counter_widget() {
		$widget_options = array('classname' => 's2_counter', 'description' => __('Subscriber Counter widget for Subscribe2', 'subscribe2') );
		$control_options = array('width' => 250, 'height' => 500);
		$this->WP_Widget('s2_counter', __('Subscribe2 Counter', 'subscribe2'), $widget_options, $control_options);
	}

	/**
	Displays the Widget
	*/
	function widget($args, $instance) {
		extract($args);

		$title = empty($instance['title']) ? 'Subscriber Count' : $instance['title'];
		$s2w_bg = empty($instance['s2w_bg']) ? '#e3dacf' : $instance['s2w_bg'];
		$s2w_fg = empty($instance['s2w_fg']) ? '#345797' : $instance['s2w_fg'];
		$s2w_width = empty($instance['s2w_width']) ? '82' : $instance['s2w_width'];
		$s2w_height = empty($instance['s2w_height']) ? '16' : $instance['s2w_height'];
		$s2w_font = empty($instance['s2w_font']) ? '11' : $instance['s2w_font'];

		echo $before_widget;
		echo $before_title . $title . $after_title;
		global $mysubscribe2;
		$registered = $mysubscribe2->get_registered();
		$confirmed = $mysubscribe2->get_public();
		$count = (count($registered) + count($confirmed));
		echo "<ul><div style=\"text-align:center; background-color:" . $s2w_bg . "; color:" . $s2w_fg . "; width:" . $s2w_width . "px; height:" . $s2w_height . "px; font:" . $s2w_font . "pt Verdana, Arial, Helvetica, sans-serif; vertical-align:middle; padding:3px; border:1px solid #444;\">";
		echo $count;
		echo "</div></ul>";
		echo $after_widget;
	}

	/**
	Saves the widgets settings.
	*/
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['s2w_bg'] = strip_tags(stripslashes($new_instance['s2w_bg']));
		$instance['s2w_fg'] = strip_tags(stripslashes($new_instance['s2w_fg']));
		$instance['s2w_width'] = strip_tags(stripslashes($new_instance['s2w_width']));
		$instance['s2w_height'] = strip_tags(stripslashes($new_instance['s2w_height']));
		$instance['s2w_font'] = strip_tags(stripslashes($new_instance['s2w_font']));

		return $instance;
	}

	/**
	Creates the edit form for the widget.
	*/
	function form($instance) {
		// set some defaults
		$options = get_option('widget_s2counter');
		if ( $options === false ) {
			$defaults = array('title'=>'Subscriber Count', 's2w_bg'=>'#e3dacf', 's2w_fg'=>'#345797', 's2w_width'=>'82', 's2w_height'=>'16', 's2w_font'=>'11');
		} else {
			$defaults = array('title'=>$options['title'], 's2w_bg'=>$options['s2w_bg'], 's2w_fg'=>$options['s2w_fg'], 's2w_width'=>$options['s2w_width'], 's2w_height'=>$options['s2w_height'], 's2w_font'=>$options['s2w_font']);
			delete_option('widget_s2counter');
		}
		$instance = wp_parse_args( (array) $instance, $defaults);
		// Be sure you format your options to be valid HTML attributes.
		$s2w_title = htmlspecialchars($instance['title'], ENT_QUOTES);
		$s2w_bg = htmlspecialchars($instance['s2w_bg'], ENT_QUOTES);
		$s2w_fg = htmlspecialchars($instance['s2w_fg'], ENT_QUOTES);
		$s2w_width = htmlspecialchars($instance['s2w_width'], ENT_QUOTES);
		$s2w_height = htmlspecialchars($instance['s2w_height'], ENT_QUOTES);
		$s2w_font = htmlspecialchars($instance['s2w_font'], ENT_QUOTES);
		echo "<div>\r\n";
		echo "<fieldset><legend><label for=\"" . $this->get_field_id('title') . "\">" . __('Widget Title', 'subscribe2') . "</label></legend>\r\n";
		echo "<input type=\"text\" name=\"" . $this->get_field_name('title') . "\" id=\"" . $this->get_field_id('title') . "\" value=\"" . $s2w_title . "\" />\r\n";
		echo "</fieldset>\r\n";

		echo "<fieldset>\r\n";
		echo "<legend>" . __('Color Scheme', 'subscribe2') . "</legend>\r\n";
		echo "<label>\r\n";
		echo "<input type=\"text\" name=\"" . $this->get_field_name('s2w_bg') . "\" id=\"" . $this->get_field_id('s2w_bg') . "\" maxlength=\"6\" value=\"" . $s2w_bg . "\" class=\"colorpickerField\" style=\"width:60px;\" /> " . __('Body', 'subscribe2') . "</label><br />\r\n";
		echo "<label>\r\n";
		echo "<input type=\"text\" name=\"" . $this->get_field_name('s2w_fg') . "\" id=\"" . $this->get_field_id('s2w_fg') . "\" maxlength=\"6\" value=\"" . $s2w_fg . "\" class=\"colorpickerField\" style=\"width:60px;\" /> " . __('Text', 'subscribe2') . "</label><br />\r\n";
		echo "<div class=\"s2_colorpicker\" id =\"" . $this->get_field_id('s2_colorpicker') . "\"></div>";
		echo "</fieldset>";

		echo "<fieldset>\r\n";
		echo "<legend>" . __('Width, Height and Font Size', 'subscribe2') . "</legend>\r\n";
		echo "<table style=\"border:0; padding:0; margin:0 0 12px 0; border-collapse:collapse;\" align=\"center\">\r\n";
		echo "<tr><td><label for=\"" . $this->get_field_id('s2w_width') . "\">" . __('Width', 'subscribe2') . "</label></td>\r\n";
		echo "<td><input type=\"text\" name=\"" . $this->get_field_name('s2w_width') . "\" id=\"" . $this->get_field_id('s2w_width') . "\" value=\"" . $s2w_width . "\" /></td></tr>\r\n";
		echo "<tr><td><label for=\"" . $this->get_field_id('s2w_height') . "\">" . __('Height', 'subscribe2') . "</label></td>\r\n";
		echo "<td><input type=\"text\" name=\"" . $this->get_field_name('s2w_height') . "\" id=\"" . $this->get_field_id('s2w_height') . "\" value=\"" . $s2w_height . "\" /></td></tr>\r\n";
		echo "<tr><td><label for=\"" . $this->get_field_id('s2w_font') . "\">" . __('Font', 'subscribe2') . "</label></td>\r\n";
		echo "<td><input type=\"text\" name=\"" . $this->get_field_name('s2w_font') . "\" id=\"" . $this->get_field_id('s2w_font') . "\" value=\"" . $s2w_font . "\" /></td></tr>\r\n";
		echo "</table></fieldset></div>\r\n";
	}
}// end S2_Counter_widget class
?>