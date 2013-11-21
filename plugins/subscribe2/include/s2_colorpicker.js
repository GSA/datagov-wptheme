// version 1.0 - original version
// version 1.1 - Update for Subscribe2 9.0 to remove unecessary code now WordPress 3.3 is minimum requirement
jQuery(document).ready(function () {
	jQuery(document).on('focus', '.colorpickerField', function () {
		if (jQuery(this).is('.s2_initialised') || this.id.search('__i__') !== -1) {
			return; // exit early, already initialized or not activated
		}
		jQuery(this).addClass('s2_initialised');
		var picker,
			field = jQuery(this).attr('id').substr(0, 20);
		jQuery('.s2_colorpicker').each(function () {
			if (jQuery(this).attr('id').search(field) !== -1) {
				picker = jQuery(this).attr('id');
				return false; // stop looping
			}
		});
		jQuery(this).on('focusin', function (event) {
			jQuery('.s2_colorpicker').hide();
			jQuery.farbtastic('#' + picker).linkTo(this);
			jQuery('#' + picker).slideDown();
		});
		jQuery(this).on('focusout', function (event) {
			jQuery('#' + picker).slideUp();
		});
		jQuery(this).trigger('focus');
	});
});
