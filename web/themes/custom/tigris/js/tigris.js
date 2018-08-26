(function($, Drupal, window, document, undefined) {
    Drupal.behaviors.fixedNavbars = {
        attach: function (context, settings) {
            positionMainNav('nav.fixed-top', context);
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
        $(document.body).css('margin-top', $mainNav.outerHeight() + 'px');
      });
    }
  $(window).on('resize', function(){
    positionMainNav('nav.fixed-top', document.body);
  });
})(jQuery, Drupal, this, this.document);