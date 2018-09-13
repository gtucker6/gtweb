(function($, Drupal, window, document, undefined) {
    Drupal.behaviors.fixedNavbars = {
        attach: function (context, settings) {
            positionMainNav('nav.fixed-top', context);
        }
    };
    Drupal.behaviors.transitionImages = {
      attach: function (context, settings) {
        $(window).on('load', function() {
          $(context).find('.hero').addClass('loaded');
        });
      }
    };
    function positionMainNav(nav, context) {
      $(nav, context).once('fixedNavbars').each(function(){
        let $mainNav = $(this);
        let $toolbar = $(context).find('#toolbar-bar');
        if($(document.body).hasClass('toolbar-fixed')) {
          // Position nav away from toolbar
          $(this).css('top', $toolbar.outerHeight() + "px");

        }
        $(context).find('.layout-container').css('margin-top', $mainNav.outerHeight());
        //
      });
    }
  $(window).on('resize', function() {
    positionMainNav('nav.fixed-top', document.body);
  });
})(jQuery, Drupal, this, this.document);