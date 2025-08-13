(function($) {
    /**
     * Initialize Swiper for every matching carousel container within the given scope.
     */
    function initShopNewCarousel($scope) {
      var $carousels = $scope.find('.shop-new-carousel.swiper-container');
      if (!$carousels.length) {
        return;
      }
  
      $carousels.each(function() {
        var $carousel = $(this);
  
        // If there's already a Swiper instance attached, destroy it first.
        if ($carousel[0].swiper) {
          $carousel[0].swiper.destroy(true, true);
        }
  
        // Initialize Swiper with your desired settings.
        new Swiper($carousel[0], {
          slidesPerView: 1,
          spaceBetween: 20,
          loop: true,
          navigation: {
            nextEl: $carousel.find('.swiper-button-next')[0],
            prevEl: $carousel.find('.swiper-button-prev')[0],
          },
          pagination: {
            el: $carousel.find('.swiper-pagination')[0],
            clickable: true,
          },
          // Breakpoints for responsiveness:
          breakpoints: {
            640: {
              slidesPerView: 2,
            },
            768: {
              slidesPerView: 3,
            },
            1024: {
              slidesPerView: 4, // Show 4 elements on screens >= 1024px
            },
          },
        });
      });
    }
  
    /**
     * 1. Initialize for published/live pages (DOM ready).
     */
    $(document).ready(function() {
      initShopNewCarousel($(document));
    });
  
    /**
     * 2. Re-initialize in Elementor preview for this widget.
     *    "shop_new_carousel" is the get_name() value from your PHP widget class.
     */
    elementorFrontend.hooks.addAction(
      'frontend/element_ready/shop_new_carousel.default',
      function($scope) {
        initShopNewCarousel($scope);
      }
    );
  })(jQuery);
  