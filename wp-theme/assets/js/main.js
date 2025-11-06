/**
 * Main JavaScript for Asad Portfolio Manager Theme
 */

(function($) {
    'use strict';

    // Dark Mode Toggle
    function initDarkMode() {
        const darkModeToggle = $('#darkModeToggle');
        const html = $('html');

        // Check saved dark mode preference
        const darkMode = localStorage.getItem('darkMode') || asadTheme.darkMode;

        if (darkMode === 'true') {
            html.attr('data-theme', 'dark');
            darkModeToggle.find('i').removeClass('fa-moon').addClass('fa-sun');
        }

        darkModeToggle.on('click', function() {
            const currentTheme = html.attr('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.attr('data-theme', newTheme);
            localStorage.setItem('darkMode', newTheme === 'dark' ? 'true' : 'false');

            // Update icon
            if (newTheme === 'dark') {
                $(this).find('i').removeClass('fa-moon').addClass('fa-sun');
            } else {
                $(this).find('i').removeClass('fa-sun').addClass('fa-moon');
            }

            // Save to database via AJAX
            $.ajax({
                url: asadTheme.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'asad_toggle_dark_mode',
                    nonce: asadTheme.nonce,
                    dark_mode: newTheme === 'dark' ? 'true' : 'false'
                }
            });
        });
    }

    // Mobile Menu Toggle
    function initMobileMenu() {
        const mobileToggle = $('.mobile-menu-toggle');
        const navigation = $('.main-navigation');

        mobileToggle.on('click', function() {
            $(this).toggleClass('active');
            navigation.toggleClass('mobile-active');
            $('body').toggleClass('menu-open');
        });

        // Close menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.main-navigation, .mobile-menu-toggle').length) {
                navigation.removeClass('mobile-active');
                mobileToggle.removeClass('active');
                $('body').removeClass('menu-open');
            }
        });
    }

    // Search Toggle
    function initSearch() {
        const searchToggle = $('.search-toggle');
        const searchBox = $('.header-search');

        searchToggle.on('click', function() {
            searchBox.slideToggle(300);
            searchBox.find('input[type="search"]').focus();
        });
    }

    // Smooth Scroll
    function initSmoothScroll() {
        $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').on('click', function(e) {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') &&
                location.hostname == this.hostname) {

                let target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

                if (target.length) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 800);
                }
            }
        });
    }

    // Sticky Header
    function initStickyHeader() {
        const header = $('.site-header');
        const headerHeight = header.outerHeight();

        $(window).on('scroll', function() {
            if ($(this).scrollTop() > headerHeight) {
                header.addClass('scrolled');
            } else {
                header.removeClass('scrolled');
            }
        });
    }

    // Image Lazy Loading
    function initLazyLoading() {
        if ('loading' in HTMLImageElement.prototype) {
            $('img').attr('loading', 'lazy');
        } else {
            // Fallback for browsers that don't support lazy loading
            const images = $('img');
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = $(entry.target);
                        if (img.data('src')) {
                            img.attr('src', img.data('src'));
                            img.removeAttr('data-src');
                        }
                        imageObserver.unobserve(entry.target);
                    }
                });
            });

            images.each(function() {
                imageObserver.observe(this);
            });
        }
    }

    // Portfolio Filter (if portfolio items exist)
    function initPortfolioFilter() {
        const filterButtons = $('.portfolio-filter button');
        const portfolioItems = $('.portfolio-item');

        filterButtons.on('click', function() {
            const filter = $(this).data('filter');

            filterButtons.removeClass('active');
            $(this).addClass('active');

            if (filter === 'all') {
                portfolioItems.fadeIn(300);
            } else {
                portfolioItems.hide();
                $('.portfolio-item[data-category="' + filter + '"]').fadeIn(300);
            }
        });
    }

    // Back to Top Button
    function initBackToTop() {
        // Create back to top button if it doesn't exist
        if (!$('.back-to-top').length) {
            $('body').append('<button class="back-to-top" aria-label="Back to top"><i class="fas fa-arrow-up"></i></button>');
        }

        const backToTop = $('.back-to-top');

        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                backToTop.addClass('show');
            } else {
                backToTop.removeClass('show');
            }
        });

        backToTop.on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 800);
        });
    }

    // Form Validation
    function initFormValidation() {
        $('form[data-validate]').on('submit', function(e) {
            let isValid = true;
            const form = $(this);

            form.find('[required]').each(function() {
                const field = $(this);
                if (!field.val()) {
                    isValid = false;
                    field.addClass('error');
                } else {
                    field.removeClass('error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }

    // Animation on Scroll
    function initScrollAnimations() {
        const animatedElements = $('.animate-on-scroll');

        if (animatedElements.length) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        $(entry.target).addClass('animated');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            animatedElements.each(function() {
                observer.observe(this);
            });
        }
    }

    // Initialize all functions on document ready
    $(document).ready(function() {
        initDarkMode();
        initMobileMenu();
        initSearch();
        initSmoothScroll();
        initStickyHeader();
        initLazyLoading();
        initPortfolioFilter();
        initBackToTop();
        initFormValidation();
        initScrollAnimations();
    });

})(jQuery);
