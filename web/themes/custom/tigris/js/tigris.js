(function($, Drupal, window, document, undefined) {
  Drupal.behaviors.accordionIcon = {
    attach: function (context, settings) {
      $(context).find('.accordian').once('accordionIcon').each(function() {
        let $icon = $(this).find('.icon-indicator');
        $icon.on('click', function() {
          let $iconImage = $(this).find('svg');
          if ($iconImage.hasClass('fa-plus')) {
            $iconImage.removeClass('fa-plus').addClass('fa-minus');
          }
          else {
            $iconImage.removeClass('fa-minus').addClass('fa-plus');
          }
        });
      });
    }
  };
  Drupal.behaviors.tigrisNavbar = {
      attach: function (context, settings) {
        let position = $(window).scrollTop();
        let navbar = new FixedMainMenu('nav.navbar-custom.fixed-top');
        navbar.changeColor(context, 'dark');
        $(context).find(navbar.element).once('tigrisNavbar').each(function () {
          navbar.position(context);
        }).trigger('scroll');
        $(window).on('resize', function () {
          navbar.position(context);
        });
        $(window).on('scroll', function () {
          navbar.changeColor(context, 'dark');
          let scroll = $(window).scrollTop();
          if (scroll > position) {
            $(navbar.element).addClass('js-invisible').removeClass('js-visible');
          }
          else {
            $(navbar.element).addClass('js-visible').removeClass('js-invisible');
          }
          position = scroll;
        });
      }
  };
  Drupal.behaviors.transitionLoad = {
    attach: function (context, settings) {
      $(window).on('load', function() {
        $(context).find('.hero').addClass('loaded');
      });
    }
  };
  function FixedMainMenu(nav) {
    this.element = $(nav);
    this.changeColor = function(context, color = 'shade') {
      if($(context).find('header.hero').length) {
        if ($(window).scrollTop() >= ($(context).find('header.hero').outerHeight() - this.getHeight())) {
          $(nav).addClass('bg-'+ color).addClass('has-color');
        }
        if($(window).scrollTop() < ($(context).find('header.hero').outerHeight() - this.getHeight())) {
          $(nav).removeClass('bg-'+ color).removeClass('has-color');
        }
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