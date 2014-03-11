// JavaScript Document
var tID;
var tDelay;
var tURL;

function autoLoadSite() {
    if (--tDelay == 0) {
        jQuery("span#extlink-counter").html("0");
        clearInterval(tID);
        window.location = tURL;
    } else {
        jQuery("span#extlink-counter").html(tDelay);                 
    }
}

jQuery (function($){

    $('body').delegate('#extlink-popup a.external', 'click', function(ev) { $('#cboxClose').trigger('click');});

});

jQuery( document ).ready(function() {
    jQuery("a.external").click(function(){
        var targetURL = (this.href.length > 50) ? this.href.substring(0, 40) + "..." : this.href;
        tURL = this.href;
        tDelay = 5;

        jQuery.colorbox({
            html: "<div id=\"extlink-popup\">" +
                "<h3 class=\"extlink-title\">You are exiting Data.gov</h3>" +
                "<div class=\"extlink-content\">" +
                "<p>You will be taken to the following site in <span id=\"extlink-counter\">" +
                tDelay + "</span> second(s).</p>" +
                "<p><a id=\"click\" href=\"" + tURL + "\">" + targetURL + "</a></p></div></div>",
            onCleanup: function(){clearInterval(tID)},

            opacity: "0.35",
            width: "400",
            height: "150",
            scrolling: false
        });
        tID = setInterval("autoLoadSite()", 1000);
        return false;
    });
});
jQuery(document).ready(function () {
jQuery(".colorbox-inline").colorbox({inline:true, width:"50%"});
});
/* jQuery tabs for ocean page */
jQuery(document).ready(function () {

$('ul#content-nav').each(function(){
  // For each set of tabs, we want to keep track of
  // which tab is active and it's associated content
  var $active, $content, $links = $(this).find('a');

  // If the location.hash matches one of the links, use that as the active tab.
  // If no match is found, use the first link as the initial active tab.
  $active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
  $active.addClass('active');
  $content = $($active.attr('href'));

  // Hide the remaining content
  $links.not($active).each(function () {
    $($(this).attr('href')).hide();
  });

  // Bind the click event handler
  $(this).on('click', 'a', function(e){
    // Make the old tab inactive.
    $active.removeClass('active');
    $content.hide();

    // Update the variables with the new link and content
    $active = $(this);
    $content = $($(this).attr('href'));

    // Make the tab active.
    $active.addClass('active');
    $content.show();

    // Prevent the anchor's default click action
    e.preventDefault();
  });
});
});
//Assigining the active link based on current url
/*$(document).ready(function () {
    var windowLocationPathname = window.location.pathname;
    $('.topic-subnav ul.nav').find('a[href^="' + windowLocationPathname + '"]').addClass('active');
});
*/
jQuery(function() {
    jQuery('.topic-subnav ul.nav a').each(function() {
        if (jQuery(this).attr('href')  ===  window.location.pathname) {
            jQuery(this).addClass('active');
        }
    });
});