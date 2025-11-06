/**
 * Contact Forms JavaScript
 *
 * @package Asad_Portfolio_Manager
 */

(function($) {
    'use strict';

    // Handle form submission
    $('.asad-contact-form').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const formId = form.find('input[name="form_id"]').val();
        const submitBtn = form.find('button[type="submit"]');
        const messageDiv = form.find('.asad-form-message');

        // Get form data
        const formData = {};
        form.find('input, select, textarea').each(function() {
            const field = $(this);
            const name = field.attr('name');

            if (name && name !== 'form_id') {
                if (field.attr('type') === 'checkbox') {
                    if (!formData[name]) {
                        formData[name] = [];
                    }
                    if (field.is(':checked')) {
                        formData[name].push(field.val());
                    }
                } else if (field.attr('type') === 'radio') {
                    if (field.is(':checked')) {
                        formData[name] = field.val();
                    }
                } else {
                    formData[name] = field.val();
                }
            }
        });

        // Validate required fields
        let isValid = true;
        form.find('[required]').each(function() {
            const field = $(this);
            field.removeClass('error');

            if (!field.val() || (Array.isArray(formData[field.attr('name')]) && formData[field.attr('name')].length === 0)) {
                field.addClass('error');
                isValid = false;
            }
        });

        if (!isValid) {
            showMessage(messageDiv, 'error', 'Please fill in all required fields.');
            return;
        }

        // Show loading state
        form.addClass('loading');
        submitBtn.prop('disabled', true);
        const originalBtnText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Sending...');

        // Get nonce
        const nonce = form.find('input[name^="asad_form_nonce_"]').val();

        // Submit via AJAX
        $.ajax({
            url: asadForms.ajaxUrl,
            type: 'POST',
            data: {
                action: 'asad_submit_form',
                nonce: nonce,
                form_id: formId,
                form_data: formData
            },
            success: function(response) {
                if (response.success) {
                    showMessage(messageDiv, 'success', response.data.message);
                    form[0].reset(); // Clear form

                    // Scroll to message
                    $('html, body').animate({
                        scrollTop: messageDiv.offset().top - 100
                    }, 500);

                    // Hide message after 5 seconds
                    setTimeout(function() {
                        messageDiv.slideUp(300, function() {
                            messageDiv.removeClass('success').hide();
                        });
                    }, 5000);
                } else {
                    showMessage(messageDiv, 'error', response.data.message);
                }
            },
            error: function(xhr, status, error) {
                showMessage(messageDiv, 'error', 'An error occurred. Please try again.');
                console.error('Form submission error:', error);
            },
            complete: function() {
                form.removeClass('loading');
                submitBtn.prop('disabled', false);
                submitBtn.html(originalBtnText);
            }
        });
    });

    // Show message function
    function showMessage(messageDiv, type, message) {
        messageDiv.removeClass('success error').addClass(type);

        const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
        messageDiv.html('<i class="fas fa-' + icon + '"></i> ' + message);

        messageDiv.slideDown(300);
    }

    // Real-time validation
    $('.asad-contact-form input, .asad-contact-form select, .asad-contact-form textarea').on('blur', function() {
        const field = $(this);

        if (field.attr('required')) {
            if (!field.val()) {
                field.addClass('error');
            } else {
                field.removeClass('error');
            }
        }

        // Email validation
        if (field.attr('type') === 'email' && field.val()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.val())) {
                field.addClass('error');
            } else {
                field.removeClass('error');
            }
        }

        // Phone validation (basic)
        if (field.attr('type') === 'tel' && field.val()) {
            const phoneRegex = /^[0-9+\-\s()]{7,}$/;
            if (!phoneRegex.test(field.val())) {
                field.addClass('error');
            } else {
                field.removeClass('error');
            }
        }

        // URL validation
        if (field.attr('type') === 'url' && field.val()) {
            try {
                new URL(field.val());
                field.removeClass('error');
            } catch {
                field.addClass('error');
            }
        }
    });

    // Remove error class on input
    $('.asad-contact-form input, .asad-contact-form select, .asad-contact-form textarea').on('input', function() {
        $(this).removeClass('error');
    });

    // Character counter for textareas (optional feature)
    $('.asad-contact-form textarea[maxlength]').each(function() {
        const textarea = $(this);
        const maxLength = textarea.attr('maxlength');

        if (maxLength) {
            const counter = $('<div class="char-counter">' + textarea.val().length + ' / ' + maxLength + '</div>');
            textarea.after(counter);

            textarea.on('input', function() {
                counter.text($(this).val().length + ' / ' + maxLength);
            });
        }
    });

    // Honeypot spam protection (optional)
    $('.asad-contact-form').each(function() {
        const form = $(this);

        // Add hidden honeypot field
        const honeypot = $('<input type="text" name="asad_hp_field" style="position:absolute;left:-9999px;width:1px;height:1px;" tabindex="-1" autocomplete="off">');
        form.prepend(honeypot);

        // Check honeypot on submit
        form.on('submit', function(e) {
            if (honeypot.val()) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    });

    // Smooth scroll to form if hash is present
    if (window.location.hash && $(window.location.hash).hasClass('asad-contact-form')) {
        $('html, body').animate({
            scrollTop: $(window.location.hash).offset().top - 100
        }, 800);
    }

    // Form analytics (optional - track form views)
    if (typeof gtag !== 'undefined') {
        $('.asad-contact-form').each(function() {
            const formId = $(this).data('form-id');
            gtag('event', 'form_view', {
                'form_id': formId
            });
        });
    }

})(jQuery);
