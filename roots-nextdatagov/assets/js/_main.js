// Modified http://paulirish.com/2009/markup-based-unobtrusive-comprehensive-dom-ready-execution/
// Only fires on body class (working off strictly WordPress body_class)
var $j = jQuery;
var ExampleSite = {
  // All pages
  common: {
    init: function () {
      // JS here
      $(function () {
        $('a[href*="#"]:not([href="#"])').click(function () {
          if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
              $('html,body').animate({
                scrollTop: target.offset().top
              }, 1000);
            }
          }
        });
      });
    },
    finalize: function () {
    }
  },
  // Home page
  home: {
    init: function () {
      // JS here
      var
        $demo = jQuery('.frontpage-search #search-header'),
        strings = JSON.parse($demo.attr('data-strings')).targets,
        randomString;

      randomString = function () {
        return strings[Math.floor(Math.random() * strings.length)];
      };

      $demo.attr('placeholder', randomString());
      setInterval(function () {
        $demo.attr('placeholder', randomString());
      }, 5500);
    }
  },
  // Impact page
  post_type_archive_impact: {
    init: function () {
      console.log('impactModal init');
      // JS here
      // http://getbootstrap.com/javascript/
      if (!$('#impactModal').length) {
        return;
      }

      $('.Impact-wrapper .thumbnail').each(
        function () {
          $(this).attr('data-toggle', 'modal').attr('data-target', '#impactModal');
          $(this).css('overflow', 'hidden').css('height', '400px').css('cursor', 'pointer');
        }
      );

      function setTooltip(btn, message) {
        $(btn).tooltip('hide')
          .attr('data-original-title', message)
          .tooltip('show');
      }

      function hideTooltip(btn) {
        setTimeout(function () {
          $(btn).tooltip('hide');
        }, 1000);
      }

      $('#impactModal').on('show.bs.modal', function (event) {
        var impactLauncher = $(event.relatedTarget); // Button that triggered the modal
        var modal = $(this);

        modal.find('.modal-title').text($(impactLauncher).find('.impact-title').first().text());
        modal.find('.modal-body .impact-img').html('');
        modal.find('.modal-body .impact-content').html('');
        modal.find('.modal-body .impact-img').append(
          $(impactLauncher).find('.impact-icon').clone().addClass('text-center thumbnail')
        );

        modal.find('.modal-body .impact-content').append(
          $(impactLauncher).find('.impact-content').clone().show()
        );

        var href = $(impactLauncher).find('.permalink').attr('href');
        modal.find('.modal-footer .permalink-btn')
          .attr('href', href);
          // .attr('data-clipboard-text', href);

        // $('.modal-footer .permalink-btn').tooltip({
        //   trigger: 'click',
        //   placement: 'right'
        // });
        //
        // var clipboard = new Clipboard('.modal-footer .permalink-btn');
        //
        // clipboard.on('success', function (e) {
        //   setTooltip(e.trigger, 'Copied!');
        //   hideTooltip(e.trigger);
        // });
        //
        // clipboard.on('error', function (e) {
        //   setTooltip(e.trigger, 'Failed!');
        //   hideTooltip(e.trigger);
        // });

      });
    }
  },
  // Applications page
  applications: {
    init: function () {
      // JS here
      // http://getbootstrap.com/javascript/
      if (!$('#appDescription').length) {
        return;
      }
      // var app_icon = $('<span>').addClass('glyphicon glyphicon-phone').attr('aria-hidden', true);
      $('.app-icon img.scale-with-grid').error(function () {
        // $(this).replaceWith(app_icon.clone());
        $(this).hide();
      });
      $('.Apps-wrapper .thumbnail').each(
        function () {
          $(this).attr('data-toggle', 'modal').attr('data-target', '#appDescription');
          $(this).css('overflow', 'hidden').css('height', '200px').css('cursor', 'pointer');
          $(this).parents('.webcontainer').removeClass('col-md-4').addClass('col-md-3');
          var a = $('<a>').text($(this).find('.app-title a').text());
          $(this).find('.app-title a').hide();
          $(this).find('.app-title').append(a);
          $(this).find('.content').hide();
          // }
        }
      );

      $('#appDescription').on('show.bs.modal', function (event) {
        var thumbnail = $(event.relatedTarget); // Button that triggered the modal
        var modal = $(this);
        modal.find('.modal-title').text($(thumbnail).find('.app-title a').first().text());
        modal.find('.modal-body').html('');
        modal.find('.modal-body').append(
          $(thumbnail).find('.app-icon').clone().addClass('text-center'),
          $(thumbnail).find('.content').clone().show()
        );
        modal.find('a.go-to-app').attr('href', $(thumbnail).attr('data-app-url'));
      });
    }
  },
  // About page
  about: {
    init: function () {
      // JS here
    }
  }
};

var UTIL = {
  fire: function (func, funcname, args) {
    var namespace = ExampleSite;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function () {

    UTIL.fire('common');

    $j.each(document.body.className.replace(/-/g, '_').split(/\s+/), function (i, classnm) {
      UTIL.fire(classnm);
    });

    UTIL.fire('common', 'finalize');
  }
};

$j(document).ready(UTIL.loadEvents);
