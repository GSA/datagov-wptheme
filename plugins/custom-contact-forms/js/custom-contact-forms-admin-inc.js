$j = jQuery.noConflict();

(function($j) {
  var cache = [];
  // Arguments are image paths relative to the current page.
  $j.preloadImages = function() {
    var args_len = arguments.length;
    for (var i = args_len; i--;) {
      var cacheImage = document.createElement('img');
      cacheImage.src = arguments[i];
      cache.push(cacheImage);
    }
  }
})(jQuery)

var fx = {
	"initDebugWindow" : function() {
		if ($j(".debug-window").length == 0) {
			debug = $j("<div>").addClass("debug-window").appendTo("body");
			debug.click(function() { debug.remove(); });
			return debug;
		} else {
			return $j(".debug-window");
		}
	},
	
	"initSaveBox" : function(text) {
		if ($j(".save-box").length == 0) {
			box = $j("<div>").addClass("save-box").appendTo("body");
			$j("<a>")
				.attr("href", "#")
				.addClass("save-box-close-btn")
				.html("&times;")
				.click(function(event) { event.preventDefault(); $j(".save-box").fadeOut("slow"); })
				.appendTo(box);
			$j("<p>").html(text + ' <img src="' + ccfAjax.plugin_dir + '/images/wpspin_light.gif" />').appendTo(".save-box");
			return box;
		} else {
			return $j(".save-box");
		}
	},
	
	"boxOut": function(event) {
		if (event != undefined) event.preventDefault();
		$j(".modal-window").fadeOut("slow", function() { $j(this).remove(); });
	}
};