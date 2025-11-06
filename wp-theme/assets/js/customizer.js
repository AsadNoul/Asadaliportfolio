/**
 * Customizer Live Preview JavaScript
 */

(function($) {
    'use strict';

    // Dark Mode
    wp.customize('asad_dark_mode', function(value) {
        value.bind(function(to) {
            $('html').attr('data-theme', to ? 'dark' : 'light');
        });
    });

    // Primary Color
    wp.customize('asad_primary_color', function(value) {
        value.bind(function(to) {
            $(':root').css('--primary-color', to);
        });
    });

    // Secondary Color
    wp.customize('asad_secondary_color', function(value) {
        value.bind(function(to) {
            $(':root').css('--secondary-color', to);
        });
    });

    // Accent Color
    wp.customize('asad_accent_color', function(value) {
        value.bind(function(to) {
            $(':root').css('--accent-color', to);
        });
    });

    // Background Color
    wp.customize('asad_bg_color', function(value) {
        value.bind(function(to) {
            $(':root').css('--bg-color', to);
        });
    });

    // Text Color
    wp.customize('asad_text_color', function(value) {
        value.bind(function(to) {
            $(':root').css('--text-color', to);
        });
    });

    // Primary Font
    wp.customize('asad_font_primary', function(value) {
        value.bind(function(to) {
            $(':root').css('--font-primary', "'" + to + "', sans-serif");
            $('body').css('font-family', "'" + to + "', sans-serif");
        });
    });

    // Secondary Font
    wp.customize('asad_font_secondary', function(value) {
        value.bind(function(to) {
            $(':root').css('--font-secondary', "'" + to + "', sans-serif");
            $('h1, h2, h3, h4, h5, h6').css('font-family', "'" + to + "', sans-serif");
        });
    });

    // Font Size
    wp.customize('asad_font_size', function(value) {
        value.bind(function(to) {
            $(':root').css('--font-size-base', to + 'px');
            $('body').css('font-size', to + 'px');
        });
    });

    // Line Height
    wp.customize('asad_line_height', function(value) {
        value.bind(function(to) {
            $('body').css('line-height', to);
        });
    });

    // Footer Text
    wp.customize('asad_footer_text', function(value) {
        value.bind(function(to) {
            $('.site-info').html(to);
        });
    });

    // Container Width
    wp.customize('asad_container_width', function(value) {
        value.bind(function(to) {
            $('.container').css('max-width', to + 'px');
        });
    });

    // Custom CSS
    wp.customize('asad_custom_css', function(value) {
        value.bind(function(to) {
            if ($('#asad-custom-css').length) {
                $('#asad-custom-css').html(to);
            } else {
                $('head').append('<style id="asad-custom-css">' + to + '</style>');
            }
        });
    });

})(jQuery);
