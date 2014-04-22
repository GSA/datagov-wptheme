jQuery(document).ready(function () {
    /*modify error message on div change*/
    jQuery("#media-upload-error").on("DOMSubtreeModified", function (e) {
        jQuery("#media-upload-error p:contains('HTTP error.')").replaceWith("The media file was rejected, possibly becasue it is infected with a virus");
    });
})
