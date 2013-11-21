<?php
class S2_Form_widget extends WP_Widget {
	/**
	Declares the Subscribe2 widget class.
	*/
	function S2_Form_widget() {
		$widget_ops = array('classname' => 's2_form_widget', 'description' => __('Sidebar Widget for Subscribe2', 'subscribe2') );
		$control_ops = array('width' => 250, 'height' => 300);
		$this->WP_Widget('s2_form_widget', __('Subscribe2 Widget', 'subscribe2'), $widget_ops, $control_ops);
	}

	/**
	Displays the Widget
	*/
	function widget($args, $instance) {
		extract($args);
		$title = empty($instance['title']) ? __('Subscribe2', 'subscribe2') : $instance['title'];
		$div = empty($instance['div']) ? 'search' : $instance['div'];
		$widgetprecontent = empty($instance['widgetprecontent']) ? '' : $instance['widgetprecontent'];
		$widgetpostcontent = empty($instance['widgetpostcontent']) ? '' : $instance['widgetpostcontent'];
		$textbox_size = empty($instance['size']) ? 20 : $instance['size'];
		$hidebutton = empty($instance['hidebutton']) ? 'none' : $instance['hidebutton'];
		$postto = empty($instance['postto']) ? '' : $instance['postto'];
		$js = empty($instance['js']) ? '' : $instance['js'];
		$noantispam = empty($instance['noantispam']) ? '' : $instance['noantispam'];
		$hide = '';
		if ( $hidebutton == 'subscribe' || $hidebutton == 'unsubscribe' ) {
			$hide = " hide=\"" . $hidebutton . "\"";
		} elseif ( $hidebutton == 'link' ) {
			$hide = " link=\"" . __('(Un)Subscribe to Posts', 'subscribe2') . "\"";
		}
		$postid = '';
		if ( !empty($postto) ) {
			$postid = " id=\"" . $postto . "\"";
		}
		$size = " size=\"" . $textbox_size . "\"";
		$nojs = '';
		if ( $js ) {
			$nojs = " nojs=\"true\"";
		}
		if ( $noantispam ) {
			$noantispam = " noantispam=\"true\"";
		}
		$shortcode = "[subscribe2" . $hide . $postid . $size . $nojs . $noantispam . "]";
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "<div class=\"" . $div . "\">";
		$content = do_shortcode( $shortcode );
		if ( !empty($widgetprecontent) ) {
			echo $widgetprecontent;
		}
		echo $content;
		if ( !empty($widgetpostcontent) ) {
			echo $widgetpostcontent;
		}
		echo "</div>";
		echo $after_widget;
	}

	/**
	Saves the widgets settings.
	*/
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['div'] = strip_tags(stripslashes($new_instance['div']));
		$instance['widgetprecontent'] = stripslashes($new_instance['widgetprecontent']);
		$instance['widgetpostcontent'] = stripslashes($new_instance['widgetpostcontent']);
		$instance['size'] = intval(stripslashes($new_instance['size']));
		$instance['hidebutton'] = strip_tags(stripslashes($new_instance['hidebutton']));
		$instance['postto'] = stripslashes($new_instance['postto']);
		$instance['js'] = stripslashes($new_instance['js']);
		$instance['noantispam'] = stripslashes($new_instance['noantispam']);

		return $instance;
	}

	/**
	Creates the edit form for the widget.
	*/
	function form($instance) {
		// set some defaults, getting any old options first
		$options = get_option('widget_subscribe2widget');
		if ( $options === false ) {
			$defaults = array('title' => 'Subscribe2', 'div' => 'search', 'widgetprecontent' => '', 'widgetpostcontent' => '', 'size' => 20, 'hidebutton' => 'none', 'postto' => '', 'js' => '', 'noantispam' => '');
		} else {
			$defaults = array('title' => $options['title'], 'div' => $options['div'], 'widgetprecontent' => $options['widgetprecontent'], 'widgetpostcontent' => $options['widgetpostcontent'], 'size' => $options['size'], 'hidebutton' => $options['hidebutton'], 'postto' => $options['postto'], 'js' => $options['js'], 'noantispam' => $options['noantispam']);
			delete_option('widget_subscribe2widget');
		}
		// code to obtain old settings too
		$instance = wp_parse_args( (array) $instance, $defaults);

		$title = htmlspecialchars($instance['title'], ENT_QUOTES);
		$div = htmlspecialchars($instance['div'], ENT_QUOTES);
		$widgetprecontent = htmlspecialchars($instance['widgetprecontent'], ENT_QUOTES);
		$widgetpostcontent = htmlspecialchars($instance['widgetpostcontent'], ENT_QUOTES);
		$size = htmlspecialchars($instance['size'], ENT_QUOTES);
		$hidebutton = htmlspecialchars($instance['hidebutton'], ENT_QUOTES);
		$postto = htmlspecialchars($instance['postto'], ENT_QUOTES);
		$js = htmlspecialchars($instance['js'], ENT_QUOTES);
		$noantispam  = htmlspecialchars($instance['noantispam'], ENT_QUOTES);

		global $wpdb, $mysubscribe2;
		$sql = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type='page' AND post_status='publish'";

		echo "<div>\r\n";
		echo "<p><label for=\"" . $this->get_field_id('title') . "\">" . __('Title', 'subscribe2') . ":\r\n";
		echo "<input class=\"widefat\" id=\"" . $this->get_field_id('title') . "\" name=\"" . $this->get_field_name('title') . "\" type=\"text\" value=\"" . $title . "\" /></label></p>\r\n";
		echo "<p><label for=\"" . $this->get_field_id('div') . "\">" . __('Div class name', 'subscribe2') . ":\r\n";
		echo "<input class=\"widefat\" id=\"" . $this->get_field_id('div') . "\" name=\"" . $this->get_field_name('div') . "\" type=\"text\" value=\"" . $div . "\" /></label></p>\r\n";
		echo "<p><label for=\"" . $this->get_field_id('widgetprecontent') . "\">" . __('Pre-Content', 'subscribe2') . ":\r\n";
		echo "<textarea class=\"widefat\" id=\"" . $this->get_field_id('widgetprecontent') . "\" name=\"" . $this->get_field_name('widgetprecontent') . "\" rows=\"2\" cols=\"25\">" . $widgetprecontent . "</textarea></label></p>\r\n";
		echo "<p><label for=\"" . $this->get_field_id('widgetpostcontent') . "\">" . __('Post-Content', 'subscribe2') . ":\r\n";
		echo "<textarea class=\"widefat\" id=\"" . $this->get_field_id('widgetpostcontent') . "\" name=\"" . $this->get_field_name('widgetpostcontent') . "\" rows=\"2\" cols=\"25\">" . $widgetpostcontent . "</textarea></label></p>\r\n";
		echo "<p><label for=\"" . $this->get_field_id('size') . "\">" . __('Text Box Size', 'subscribe2') . ":\r\n";
		echo "<input class=\"widefat\" id=\"" . $this->get_field_id('size') . "\" name=\"" . $this->get_field_name('size') . "\" type=\"text\" value=\"" . $size . "\" /></label></p>\r\n";
		echo "<p>" . __('Display options', 'subscribe2') . ":<br />\r\n";
		echo "<label for=\"" . $this->get_field_id('hidebutton') . "complete\"><input id=\"" . $this->get_field_id('hidebutton') . "complete\" name=\"" . $this->get_field_name('hidebutton') . "\" type=\"radio\" value=\"none\"". checked('none', $hidebutton, false) . "/> " . __('Show complete form', 'subscribe2') . "</label>\r\n";
		echo "<br /><label for=\"" . $this->get_field_id('hidebutton') . "subscribe\"><input id=\"" . $this->get_field_id('hidebutton') . "subscribe\" name=\"" . $this->get_field_name('hidebutton') . "\" type=\"radio\" value=\"subscribe\"". checked('subscribe', $hidebutton, false) . "/> " . __('Hide Subscribe button', 'subscribe2') . "</label>\r\n";
		echo "<br /><label for=\"" . $this->get_field_id('hidebutton') . "unsubscribe\"><input id=\"" . $this->get_field_id('hidebutton') . "unsubscribe\" name=\"" . $this->get_field_name('hidebutton') . "\" type=\"radio\" value=\"unsubscribe\"". checked('unsubscribe', $hidebutton, false) . "/> " . __('Hide Unsubscribe button', 'subscribe2') . "</label>\r\n";
		if ( '1' == $mysubscribe2->subscribe2_options['ajax'] ) {
			echo "<br /><label for=\"" . $this->get_field_id('hidebutton') . "ajax\"><input id=\"" . $this->get_field_id('hidebutton') . "ajax\" name=\"" . $this->get_field_name('hidebutton') . "\" type=\"radio\" value=\"link\"". checked('link', $hidebutton, false) . "/>" . __('Show as link', 'subscribe2') . "</label>\r\n";
		}
		echo "</p>\r\n";
		echo "<p><label for=\"" . $this->get_field_id('postto') . "\">" . __('Post form content to page', 'subscribe2') . ":\r\n";
		echo "<select class=\"widefat\" id=\"" . $this->get_field_id('postto') . "\" name=\"" . $this->get_field_name('postto') . "\">\r\n";
		echo "<option value=\"" . $mysubscribe2->subscribe2_options['s2page'] . "\">" . __('Use Subscribe2 Default', 'subscribe2') . "</option>\r\n";
		echo "<option value=\"home\"";
		if ( $postto === 'home' ) { echo " selected=\"selected\""; }
		echo ">" . __('Use Home Page', 'subscribe2') . "</option>\r\n";
		echo "<option value=\"self\"";
		if ( $postto === 'self' ) { echo " selected=\"selected\""; }
		echo ">" . __('Use Referring Page', 'subscribe2') . "</option>\r\n";
		$mysubscribe2->pages_dropdown($postto);
		echo "</select></label></p>\r\n";
		echo "<p><label for=\"" . $this->get_field_id('js') . "\">" . __('Disable JavaScript', 'subscribe2') . ":\r\n";
		echo "<input id=\"" . $this->get_field_id('js') . "\" name =\"" . $this->get_field_name('js') . "\" value=\"true\" type=\"checkbox\"" . checked('true', $js, false) . "/>";
		echo "</label></p>\r\n";
		echo "<p><label for=\"" . $this->get_field_id('noantispam') . "\">" . __('Disable Anti-spam measures', 'subscribe2') . ":\r\n";
		echo "<input id=\"" . $this->get_field_id('noantispam') . "\" name =\"" . $this->get_field_name('noantispam') . "\" value=\"true\" type=\"checkbox\"" . checked('true', $noantispam, false) . "/>";
		echo "</label></p>\r\n";
		echo "</div>\r\n";
	}
} // End S2_Form_widget class
?>