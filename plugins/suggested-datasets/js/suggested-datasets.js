jQuery(document).ready(function () {
    jQuery('.comments-trigger').click(function () {
        jQuery(this).hide();
        jQuery('.' + jQuery(this).attr('rel')).show('slow');
    });
});