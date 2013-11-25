// Modified http://paulirish.com/2009/markup-based-unobtrusive-comprehensive-dom-ready-execution/
// Only fires on body class (working off strictly WordPress body_class)
var $j = jQuery;
var ExampleSite = {
  // All pages
  common: {
    init: function() {
      // JS here


      var
        $demo = jQuery('#search-examples'),
        strings = JSON.parse($demo.attr('data-strings')).targets,
         randomString;

      randomString = function () {
        return strings[Math.floor(Math.random() * strings.length)];
      };

      $demo.attr('placeholder', randomString());
      setInterval(function () {
          $demo.attr('placeholder', randomString());
      }, 5500);

    },
    finalize: function() {}
  },
  // Home page
  home: {
    init: function() {
      // JS here
    }
  },
  // About page
  about: {
    init: function() {
      // JS here
    }
  }
};

var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = ExampleSite;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {

    UTIL.fire('common');

    $j.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });
    
    UTIL.fire('common', 'finalize');
  }
};

$j(document).ready(UTIL.loadEvents);