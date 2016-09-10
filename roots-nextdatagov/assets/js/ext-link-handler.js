// JavaScript Document
jQuery(".colorbox-inline").colorbox({inline:true, width:"50%",opacity:0.7});

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

jQuery(function() {
    jQuery('.topic-subnav ul.nav a').each(function() {
        var str = jQuery(this).attr('href');
        var res = str.replace("/page/", "/");
        if (res ===  window.location.pathname) {
            jQuery(this).addClass('active');
        }
    });
    jQuery('.menu-communities ul li a').each(function() {
        if (!jQuery(this).attr('href')) {
            jQuery(this).addClass('inactive');
        }
    });
});
jQuery(document).ready(function() {
    if (document.domain.indexOf('data.gov') > -1) {
        document.domain="data.gov";
    }
    if (document.domain.indexOf('reisys.com') > -1) {
        document.domain="reisys.com";
    }
    jQuery(".ext-link").each(function(i) {
        if (!jQuery(this).attr('title')) {
            jQuery(this).attr('title', 'This link will direct you to an external website that may have different content and privacy policies from Data.gov.')
            jQuery(this).attr('aria-describedby', 'external_disclaimer');
        }
    });
    jQuery("#tribe-bar-search").attr("placeholder", "Search Events");
    jQuery("#tribe-bar-date").attr("placeholder", "Select a Date");

});
jQuery(document).ready(function() {
setTimeout(function() {



        // show tooltips for any element that has a class named "tooltips"
        // the content of the tooltip will be taken from the element's "title" attribute
        new $.Zebra_Tooltips($('.tooltips'));

},2000);
});
jQuery("#frame_embed").on("load", function () {
    jQuery("#frame_embed").contents().find(".masthead").hide();
    jQuery("#frame_embed").contents().find(".site-footer").hide();
    jQuery("#frame_embed").contents().find(".sub-nav").hide();

});

jQuery(document).ready(function() {
    /* Chrome fix for the scroll bar */
    var isChrome = window.chrome;
    if(isChrome) {
        var metrics =  jQuery('.datasets_published_per_month_table_full').DataTable( {
            "paging":   false,
            "ordering": false,
            "responsive": true,
            "autoWidth":false
            //"stateSave": true
        } );
    }else {
        var metrics =  jQuery('.datasets_published_per_month_table_full').DataTable( {
            "paging":   false,
            "ordering": false,
            "responsive": true,
            "autoWidth":true
            //"stateSave": true
        } );
    }
    metrics.columns.adjust().draw();
    metrics.columns( '.hideCol' ).visible( false );
    jQuery('.year li').on( 'click', function (e) {
        e.preventDefault();
        jQuery(this).toggleClass("active");
        jQuery(this).find('i').toggleClass('fa-plus-circle fa-minus-circle');
        var year_id = "."+$(this).text();
        metrics.columns(year_id).visible(!metrics.column(year_id).visible());
        var tablewidth=jQuery('.datasets_published_per_month_table_full').width();
        jQuery(".upscroll").css("width",tablewidth);
    });
    jQuery( ".datasets_published_per_month_table_full" ).wrap( "<div class='scroll' style='overflow:auto;'></div>" );
    jQuery( ".dataTables_filter" ).after( "<div class='topscroll'><div class='upscroll'></div></div>" );

} );
jQuery(function(){
    jQuery(".topscroll").scroll(function(){
        jQuery(".scroll").scrollLeft(jQuery(".topscroll").scrollLeft());
    });
    jQuery(".scroll").scroll(function(){
        jQuery(".topscroll").scrollLeft(jQuery(".scroll").scrollLeft());
    });
});
