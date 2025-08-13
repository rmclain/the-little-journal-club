/**
 * Crafty Chic Navigation JavaScript
 * Handles mobile menu toggle and smooth interactions
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        initCraftyNavigation();
    });
    
    function initCraftyNavigation() {
        // Mobile menu toggle
        $('.crafty-nav-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Hamburger menu clicked'); // Debug log
            toggleMobileMenu($(this));
        });
        
        // Mobile dropdown toggle
        $('.crafty-nav-item.has-dropdown .crafty-nav-link').on('click', function(e) {
            if ($(window).width() <= 768) {
                e.preventDefault();
                toggleMobileDropdown($(this).parent());
            }
        });
        
        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.crafty-nav-container').length) {
                closeMobileMenu();
            }
        });
        
        // Handle window resize
        $(window).on('resize', function() {
            if ($(window).width() > 768) {
                closeMobileMenu();
                $('.crafty-nav-item').removeClass('active');
            }
        });
        
        // Smooth scroll for anchor links
        $('.crafty-nav-link[href*="#"], .crafty-nav-dropdown-link[href*="#"]').on('click', function(e) {
            var target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                smoothScrollTo(target);
                closeMobileMenu();
            }
        });
        
        // Update cart count when cart changes (for WooCommerce)
        $(document.body).on('added_to_cart removed_from_cart updated_cart_totals', function() {
            updateCartCount();
        });
        
        // Add loading animation
        addLoadingAnimation();
        
        // Initialize accessibility features
        initAccessibility();
        
        // Debug log to confirm initialization
        console.log('Crafty Navigation initialized');
    }
    
    function toggleMobileMenu($toggle) {
        var $menu = $toggle.closest('.crafty-nav-container').find('.crafty-nav-menu');
        var isActive = $toggle.hasClass('active');
        
        console.log('Toggle mobile menu - isActive:', isActive, 'Menu found:', $menu.length); // Debug log
        
        if (isActive) {
            closeMobileMenu();
        } else {
            openMobileMenu($toggle, $menu);
        }
    }
    
    function openMobileMenu($toggle, $menu) {
        console.log('Opening mobile menu'); // Debug log
        $toggle.addClass('active');
        $menu.addClass('active');
        $('body').addClass('nav-open');
        
        // Add staggered animation to menu items
        $menu.find('.crafty-nav-item').each(function(index) {
            $(this).css({
                'animation-delay': (index * 0.1) + 's',
                'animation-duration': '0.5s',
                'animation-name': 'slideInFromRight',
                'animation-fill-mode': 'both'
            });
        });
    }
    
    function closeMobileMenu() {
        console.log('Closing mobile menu'); // Debug log
        $('.crafty-nav-toggle').removeClass('active');
        $('.crafty-nav-menu').removeClass('active');
        $('.crafty-nav-item').removeClass('active');
        $('body').removeClass('nav-open');
        
        // Reset animations
        $('.crafty-nav-item').css({
            'animation': 'none'
        });
    }
    
    function toggleMobileDropdown($item) {
        var isActive = $item.hasClass('active');
        
        // Close other dropdowns
        $('.crafty-nav-item').not($item).removeClass('active');
        
        // Toggle current dropdown
        if (isActive) {
            $item.removeClass('active');
        } else {
            $item.addClass('active');
        }
    }
    
    function smoothScrollTo($target) {
        var offset = $target.offset().top - 100; // Account for fixed header
        
        $('html, body').animate({
            scrollTop: offset
        }, 800, 'easeInOutCubic');
    }
    
    function addLoadingAnimation() {
        // Add CSS animations dynamically
        var styles = `
            @keyframes slideInFromRight {
                from {
                    opacity: 0;
                    transform: translateX(30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes fadeInScale {
                from {
                    opacity: 0;
                    transform: scale(0.9);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
            
            .crafty-nav-container {
                animation: fadeInScale 0.6s ease-out;
            }
            
            .crafty-nav-link::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                width: 0;
                height: 2px;
                background: linear-gradient(90deg, #e8a87c, #c27d54);
                transition: all 0.3s ease;
                transform: translateX(-50%);
            }
            
            .crafty-nav-link:hover::after {
                width: 80%;
            }
            
            @media (max-width: 768px) {
                .crafty-nav-link::after {
                    display: none;
                }
            }
        `;
        
        $('<style>').prop('type', 'text/css').html(styles).appendTo('head');
    }
    
    function initAccessibility() {
        // Add ARIA attributes
        $('.crafty-nav-toggle').attr({
            'aria-expanded': 'false',
            'aria-controls': 'crafty-nav-menu',
            'aria-label': 'Toggle navigation menu'
        });
        
        $('.crafty-nav-menu').attr('id', 'crafty-nav-menu');
        
        // Update ARIA states on menu toggle
        $('.crafty-nav-toggle').on('click', function() {
            var expanded = $(this).hasClass('active');
            $(this).attr('aria-expanded', expanded);
        });
        
        // Keyboard navigation
        $('.crafty-nav-link, .crafty-nav-dropdown-link').on('keydown', function(e) {
            var $this = $(this);
            var $items = $('.crafty-nav-link, .crafty-nav-dropdown-link');
            var currentIndex = $items.index($this);
            
            switch(e.keyCode) {
                case 37: // Left arrow
                    e.preventDefault();
                    focusMenuItem(currentIndex - 1, $items);
                    break;
                case 39: // Right arrow
                    e.preventDefault();
                    focusMenuItem(currentIndex + 1, $items);
                    break;
                case 40: // Down arrow
                    e.preventDefault();
                    if ($this.hasClass('crafty-nav-link') && $this.siblings('.crafty-nav-dropdown').length) {
                        $this.siblings('.crafty-nav-dropdown').find('.crafty-nav-dropdown-link').first().focus();
                    } else {
                        focusMenuItem(currentIndex + 1, $items);
                    }
                    break;
                case 38: // Up arrow
                    e.preventDefault();
                    if ($this.hasClass('crafty-nav-dropdown-link')) {
                        var $parentLink = $this.closest('.crafty-nav-item').find('.crafty-nav-link');
                        $parentLink.focus();
                    } else {
                        focusMenuItem(currentIndex - 1, $items);
                    }
                    break;
                case 27: // Escape
                    e.preventDefault();
                    closeMobileMenu();
                    break;
            }
        });
    }
    
    function focusMenuItem(index, $items) {
        if (index < 0) index = $items.length - 1;
        if (index >= $items.length) index = 0;
        $items.eq(index).focus();
    }
    
    function updateCartCount() {
        // Update cart count via AJAX for WooCommerce
        if (typeof wc_add_to_cart_params !== 'undefined') {
            $.ajax({
                type: 'POST',
                url: wc_add_to_cart_params.ajax_url,
                data: {
                    action: 'get_cart_count'
                },
                success: function(response) {
                    if (response.success) {
                        var count = parseInt(response.data.count);
                        var $cartCount = $('.crafty-nav-cart-count');
                        
                        if (count > 0) {
                            $cartCount.text(count).show();
                        } else {
                            $cartCount.hide();
                        }
                        
                        // Add a small animation
                        $cartCount.addClass('updated');
                        setTimeout(function() {
                            $cartCount.removeClass('updated');
                        }, 300);
                    }
                }
            });
        }
    }
    
    // Custom easing function
    $.easing.easeInOutCubic = function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t + b;
        return c/2*((t-=2)*t*t + 2) + b;
    };
    
    // Add scroll effect to navigation
    $(window).on('scroll', function() {
        var scrollTop = $(window).scrollTop();
        var $nav = $('.crafty-nav-container');
        
        if (scrollTop > 100) {
            $nav.addClass('scrolled');
        } else {
            $nav.removeClass('scrolled');
        }
    });
    
    // Add scroll effect styles
    $('<style>').prop('type', 'text/css').html(`
        .crafty-nav-container.scrolled {
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }
        
        .crafty-nav-container.scrolled::before {
            opacity: 1;
        }
    `).appendTo('head');
    
})(jQuery);