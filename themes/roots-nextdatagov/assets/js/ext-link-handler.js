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

jQuery(document).bind('ready',function(){
    jQuery("a.external").click(function(){
        var targetURL = (this.href.length > 50) ? this.href.substring(0, 40) + "..." : this.href;
        tURL = this.href;
        tDelay = 7;

        jQuery.colorbox({
            html: "<div id=\"extlink-popup\">" +
                "<h3 class=\"align-center extlink-title\">You are exiting Data.gov</h3>" +
                "<div class=\"align-center extlink-content\">" +
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

