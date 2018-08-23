(function($, Drupal, window, document, undefined) {
Drupal.behaviors.carouselModal = {
  attach: function (context, settings) {
    // Initialize Modal Object, set the Index after clicking, and then create the Modal HTML
    $(context).once('carouselModal').each(function(){
        let carouselModal = new Modal(settings.carousel.modalData['data-container'], settings.carousel.modalData);
    $('a.portfolio-project').on('click', function(e) {
        e.preventDefault();
        carouselModal.setIndex($(this).data('index'));
        carouselModal.create();
      });
      console.log(carouselModal);

      $(document.body).on('click', '#'+ carouselModal.id + ' .modal-arrow' ,function(e){
        e.preventDefault();
        if($(this).hasClass('modal-previous-arrow') && portfolioModal.getIndex() !== 0) {
        	carouselModal.changeResult(carouselModal.getIndex() - 1);
        }
        else if($(this).hasClass('modal-next-arrow') && portfolioModal.getIndex() < portfolioModal.items.length - 1) {
        	carouselModal.changeResult(carouselModal.getIndex() + 1);
        }
      });
    });


  }
};

// Modal Objects with items in the form of items[index][data-title], items[index][data-html] and/or items[data-container]
function Modal(id, items) {
  this.id = id;
  this.items = items;
  this.container = $('<div>', {
    'id': this.id,
    'class': 'modal fade',
    'tabindex': -1,
    'role': 'dialog'
  });
  this.setIndex = function(i) {
    this.currentIndex = i;
  };
  this.getIndex = function () {
    if(this.currentIndex !== 'undefined') {
      
      return this.currentIndex;
    } else {
      return false;
    }
  };
  this.getHTML = function() {
    modalHtml = '<div class="modal-dialog modal-lg" role="document">\n' +
        '<div class="modal-content">\n' +
        '<a href="#" class="modal-arrow modal-previous-arrow"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>' +
        '<div class="modal-box">' +
        '<div class="modal-header">\n' +
        '<button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>\n' +
        '</div>\n' +
        '<div class="modal-body">\n' + items[this.getIndex()]['data-html'] +
        '</div>\n' +
        '</div>' +
        '<a href="#" class="modal-arrow modal-next-arrow"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>' +
        '</div><!-- /.modal-content -->\n' +
        '</div><!-- /.modal-dialog -->\n';
    return modalHtml;
  };
  this.create = function() {
    let $modal = this.container.html(this.getHTML());
    if($('#'+this.id).length === 0) {
      $modal.appendTo(document.body);
    }
    this.hideArrows();
  };
  this.changeResult = function(i) {
    this.currentIndex = i;
    this.container.html(this.getHTML());
    this.hideArrows();
  };
  this.hideArrows = function() {
    if(this.getIndex() !== false) {
      if(this.getIndex() === this.items.length - 1) {
        this.container.find('.modal-next-arrow').addClass('hide');
      } else if (this.getIndex() === 0) {
        this.container.find('.modal-previous-arrow').addClass('hide');
      } else {
        this.container.find('.modal-arrow').removeClass('hide');
      }
    }
  }
}
})(jQuery, Drupal, this, this.document);