(function($, Drupal, window, document, undefined) {
    Drupal.behaviors.tigrisNavbar = {
        attach: function (context, settings) {
          let navbar = new Menu('nav.navbar-custom.fixed-top');
          $(context).find(navbar.element).once('tigrisNavbar').each(function() {
            navbar.position(context);
          }).trigger('scroll');
          $(window).on('resize', function() {
            navbar.position(context);
          });
          $(window).on('scroll', function() {
            navbar.changeColor(context, 'main')
          });
        }
    };
    Drupal.behaviors.transitionImages = {
      attach: function (context, settings) {
        $(window).on('load', function() {
          $(context).find('.hero').addClass('loaded');
        });
      }
    };
    function Menu(nav) {
      this.element = $(nav);
      this.changeColor = function(context, color = 'shade') {
        if ($(window).scrollTop() >= ($(context).find('header.hero').outerHeight() - this.getHeight())) {
          $(nav).addClass('bg-'+ color);
        }
        if($(window).scrollTop() < ($(context).find('header.hero').outerHeight() - this.getHeight())) {
          $(nav).removeClass('bg-'+ color);
        }
      }.bind(this);
      this.getWidth = function() {
        return $(nav).outerWidth();
      };
      this.getHeight = function() {
        return $(nav).outerHeight();
      };
      this.position = function (context) {
        let $toolbar = $(context).find('#toolbar-bar');
        if($(document.body).hasClass('toolbar-fixed')) {
          // Position nav away from toolbar
          $(nav).css('top', $toolbar.outerHeight() + "px");
        }
        if(!$(context).find('header').hasClass('hero')) {
          $(context).find('.layout-container').css('margin-top', this.getHeight());
        }
      }.bind(this);
    }


})(jQuery, Drupal, this, this.document);