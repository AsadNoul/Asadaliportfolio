/**
 * Admin JavaScript for Asad Portfolio Manager Theme
 */

(function($) {
    'use strict';

    // Tab Navigation
    function initTabs() {
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            const target = $(this).attr('href');

            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');

            $('.tab-content').removeClass('active').hide();
            $(target).addClass('active').fadeIn(300);
        });
    }

    // Plugin Manager Functions
    function initPluginManager() {
        // Activate Plugin
        $(document).on('click', '.activate-plugin', function() {
            const button = $(this);
            const plugin = button.data('plugin');

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Activating...');

            $.ajax({
                url: asadAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'asad_activate_plugin',
                    nonce: asadAdmin.nonce,
                    plugin: plugin
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.data.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification(response.data.message, 'error');
                        button.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Activate');
                    }
                },
                error: function() {
                    showNotification('An error occurred. Please try again.', 'error');
                    button.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Activate');
                }
            });
        });

        // Deactivate Plugin
        $(document).on('click', '.deactivate-plugin', function() {
            const button = $(this);
            const plugin = button.data('plugin');

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deactivating...');

            $.ajax({
                url: asadAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'asad_deactivate_plugin',
                    nonce: asadAdmin.nonce,
                    plugin: plugin
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.data.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification(response.data.message, 'error');
                        button.prop('disabled', false).html('<i class="fas fa-times-circle"></i> Deactivate');
                    }
                },
                error: function() {
                    showNotification('An error occurred. Please try again.', 'error');
                    button.prop('disabled', false).html('<i class="fas fa-times-circle"></i> Deactivate');
                }
            });
        });

        // Delete Plugin
        $(document).on('click', '.delete-plugin', function() {
            if (!confirm('Are you sure you want to delete this plugin? This action cannot be undone.')) {
                return;
            }

            const button = $(this);
            const plugin = button.data('plugin');

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

            $.ajax({
                url: asadAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'asad_delete_plugin',
                    nonce: asadAdmin.nonce,
                    plugin: plugin
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.data.message, 'success');
                        button.closest('.plugin-card').fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        showNotification(response.data.message, 'error');
                        button.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete');
                    }
                },
                error: function() {
                    showNotification('An error occurred. Please try again.', 'error');
                    button.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete');
                }
            });
        });

        // Search WP.org Plugins
        $('#searchWPOrgBtn').on('click', function() {
            const searchTerm = $('#pluginSearchWPOrg').val();

            if (!searchTerm) {
                showNotification('Please enter a search term.', 'warning');
                return;
            }

            const button = $(this);
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Searching...');

            $.ajax({
                url: asadAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'asad_search_plugins',
                    nonce: asadAdmin.nonce,
                    search: searchTerm
                },
                success: function(response) {
                    if (response.success) {
                        displayPluginResults(response.data.plugins);
                    } else {
                        showNotification(response.data.message, 'error');
                    }
                    button.prop('disabled', false).html('<i class="fas fa-search"></i> Search');
                },
                error: function() {
                    showNotification('An error occurred. Please try again.', 'error');
                    button.prop('disabled', false).html('<i class="fas fa-search"></i> Search');
                }
            });
        });

        // Plugin Search (local)
        $('#pluginSearchInput').on('keyup', function() {
            const searchValue = $(this).val().toLowerCase();
            $('.plugin-card').each(function() {
                const pluginName = $(this).find('h3').text().toLowerCase();
                const pluginDesc = $(this).find('.desc').text().toLowerCase();

                if (pluginName.includes(searchValue) || pluginDesc.includes(searchValue)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    }

    // Theme Manager Functions
    function initThemeManager() {
        // Activate Theme
        $(document).on('click', '.activate-theme', function() {
            const button = $(this);
            const theme = button.data('theme');

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Activating...');

            $.ajax({
                url: asadAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'asad_activate_theme',
                    nonce: asadAdmin.nonce,
                    theme: theme
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.data.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification(response.data.message, 'error');
                        button.prop('disabled', false).html('<i class="fas fa-check"></i> Activate');
                    }
                },
                error: function() {
                    showNotification('An error occurred. Please try again.', 'error');
                    button.prop('disabled', false).html('<i class="fas fa-check"></i> Activate');
                }
            });
        });

        // Delete Theme
        $(document).on('click', '.delete-theme', function() {
            if (!confirm('Are you sure you want to delete this theme? This action cannot be undone.')) {
                return;
            }

            const button = $(this);
            const theme = button.data('theme');

            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

            $.ajax({
                url: asadAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'asad_delete_theme',
                    nonce: asadAdmin.nonce,
                    theme: theme
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.data.message, 'success');
                        button.closest('.theme-card').fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        showNotification(response.data.message, 'error');
                        button.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete');
                    }
                },
                error: function() {
                    showNotification('An error occurred. Please try again.', 'error');
                    button.prop('disabled', false).html('<i class="fas fa-trash"></i> Delete');
                }
            });
        });

        // Search Themes
        $('#searchThemesBtn').on('click', function() {
            const searchTerm = $('#themeSearchWPOrg').val();

            if (!searchTerm) {
                showNotification('Please enter a search term.', 'warning');
                return;
            }

            const button = $(this);
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Searching...');

            $.ajax({
                url: asadAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'asad_search_themes',
                    nonce: asadAdmin.nonce,
                    search: searchTerm
                },
                success: function(response) {
                    if (response.success) {
                        displayThemeResults(response.data.themes);
                    } else {
                        showNotification(response.data.message, 'error');
                    }
                    button.prop('disabled', false).html('<i class="fas fa-search"></i> Search');
                },
                error: function() {
                    showNotification('An error occurred. Please try again.', 'error');
                    button.prop('disabled', false).html('<i class="fas fa-search"></i> Search');
                }
            });
        });
    }

    // Theme Editor Functions
    function initThemeEditor() {
        let currentFile = '';
        let codeEditor = null;

        // Initialize CodeMirror
        if ($('#codeEditor').length && typeof wp !== 'undefined' && wp.codeEditor) {
            const editorSettings = wp.codeEditor.defaultSettings;
            editorSettings.codemirror.lineNumbers = true;
            editorSettings.codemirror.lineWrapping = true;
            editorSettings.codemirror.theme = 'default';

            codeEditor = wp.codeEditor.initialize('codeEditor', editorSettings);
        }

        // File Tree Click
        $(document).on('click', '.file-item', function() {
            const filePath = $(this).data('file');
            loadFile(filePath, codeEditor);

            $('.file-item').removeClass('active');
            $(this).addClass('active');
        });

        // Save File
        $('#saveFileBtn').on('click', function() {
            if (!currentFile) {
                showNotification('No file selected.', 'warning');
                return;
            }

            const content = codeEditor ? codeEditor.codemirror.getValue() : $('#codeEditor').val();
            saveFile(currentFile, content);
        });
    }

    // Load File Content
    function loadFile(filePath, editor) {
        $.ajax({
            url: asadAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'asad_get_file_content',
                nonce: asadAdmin.nonce,
                file: filePath
            },
            success: function(response) {
                if (response.success) {
                    if (editor) {
                        editor.codemirror.setValue(response.data.content);
                    } else {
                        $('#codeEditor').val(response.data.content);
                    }
                    $('#currentFileName').text(filePath);
                    $('#saveFileBtn').prop('disabled', false);
                    currentFile = filePath;
                } else {
                    showNotification(response.data.message, 'error');
                }
            },
            error: function() {
                showNotification('Failed to load file.', 'error');
            }
        });
    }

    // Save File Content
    function saveFile(filePath, content) {
        $('#saveFileBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: asadAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'asad_save_file_content',
                nonce: asadAdmin.nonce,
                file: filePath,
                content: content
            },
            success: function(response) {
                if (response.success) {
                    showNotification(response.data.message, 'success');
                    $('#editorStatus').html('<i class="fas fa-check"></i> Saved successfully. Backup created: ' + response.data.backup);
                } else {
                    showNotification(response.data.message, 'error');
                }
                $('#saveFileBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save File');
            },
            error: function() {
                showNotification('Failed to save file.', 'error');
                $('#saveFileBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save File');
            }
        });
    }

    // Display Plugin Search Results
    function displayPluginResults(plugins) {
        const container = $('#wpOrgPluginsResults');
        container.empty();

        if (plugins.length === 0) {
            container.html('<p>No plugins found.</p>');
            return;
        }

        plugins.forEach(function(plugin) {
            const html = `
                <div class="plugin-card">
                    <div class="plugin-card-top">
                        ${plugin.icon ? '<img src="' + plugin.icon + '" alt="' + plugin.name + '" style="max-width: 64px;">' : ''}
                        <div class="name column-name">
                            <h3>${plugin.name}</h3>
                        </div>
                        <div class="desc column-description">
                            <p>${plugin.short_description}</p>
                        </div>
                        <div class="plugin-meta">
                            <span class="version"><strong>Version:</strong> ${plugin.version}</span>
                            <span class="rating"><strong>Rating:</strong> ${plugin.rating}/100 (${plugin.num_ratings} ratings)</span>
                        </div>
                    </div>
                    <div class="plugin-card-bottom">
                        <button class="button button-primary install-plugin-wporg" data-slug="${plugin.slug}">
                            <i class="fas fa-download"></i> Install
                        </button>
                    </div>
                </div>
            `;
            container.append(html);
        });
    }

    // Display Theme Search Results
    function displayThemeResults(themes) {
        const container = $('#wpOrgThemesResults');
        container.empty();

        if (themes.length === 0) {
            container.html('<p>No themes found.</p>');
            return;
        }

        themes.forEach(function(theme) {
            const html = `
                <div class="theme-card">
                    <div class="theme-screenshot">
                        ${theme.screenshot ? '<img src="' + theme.screenshot + '" alt="' + theme.name + '">' : '<div class="no-screenshot"><i class="fas fa-image fa-3x"></i></div>'}
                    </div>
                    <div class="theme-card-content">
                        <h3>${theme.name}</h3>
                        <p class="theme-version">Version: ${theme.version}</p>
                        <p class="theme-description">${theme.description}</p>
                    </div>
                    <div class="theme-actions">
                        <button class="button button-primary install-theme-wporg" data-slug="${theme.slug}">
                            <i class="fas fa-download"></i> Install
                        </button>
                    </div>
                </div>
            `;
            container.append(html);
        });
    }

    // Header & Footer Settings
    function initHeaderFooterSettings() {
        $('#saveHeaderSettings, #saveFooterSettings').on('click', function() {
            showNotification('Settings saved! Go to Appearance > Customize to see live changes.', 'info');
        });
    }

    // Show Notification
    function showNotification(message, type) {
        const notificationClass = type === 'success' ? 'notice-success' :
                                  type === 'error' ? 'notice-error' :
                                  type === 'warning' ? 'notice-warning' : 'notice-info';

        const notification = $('<div class="notice ' + notificationClass + ' is-dismissible"><p>' + message + '</p></div>');

        $('.wrap').prepend(notification);

        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Initialize all functions on document ready
    $(document).ready(function() {
        initTabs();
        initPluginManager();
        initThemeManager();
        initThemeEditor();
        initHeaderFooterSettings();
    });

})(jQuery);
