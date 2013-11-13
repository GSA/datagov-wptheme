jQuery(window).load(function(){
  jQuery('#posts').masonry({
    // options
    columnWidth: 287,
    itemSelector : '.post',
    isResizable: true,
    isAnimated: true,
    gutterWidth: 25
  });

  jQuery("#joyRideTipContent").joyride({
    autoStart: true,
    modal: true,
    cookieMonster: true,
    cookieName: 'datagov',
    cookieDomain: 'next.data.gov'
  });
});

jQuery(function () {
  var
    $demo = jQuery('#rotate-stats'),
    strings = JSON.parse($demo.attr('data-strings')).targets,
     randomString;

  randomString = function () {
    return strings[Math.floor(Math.random() * strings.length)];
  };

  $demo.fadeTo(randomString());
  setInterval(function () {
    $demo.fadeTo(randomString());
  }, 15000);
});
