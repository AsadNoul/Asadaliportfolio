<?php
/**
 * Form Builder Template
 *
 * @package Asad_Portfolio_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

$all_forms = asad_get_all_forms();
?>

<div class="wrap asad-form-builder">
    <h1><?php _e('Contact Form Builder', 'asad-portfolio'); ?></h1>
    <p class="description"><?php _e('Create beautiful contact forms with a simple drag-and-drop interface.', 'asad-portfolio'); ?></p>

    <div class="asad-tabs-wrapper">
        <nav class="nav-tab-wrapper">
            <a href="#all-forms" class="nav-tab nav-tab-active"><?php _e('All Forms', 'asad-portfolio'); ?></a>
            <a href="#create-form" class="nav-tab"><?php _e('Create New Form', 'asad-portfolio'); ?></a>
            <a href="#submissions" class="nav-tab"><?php _e('Submissions', 'asad-portfolio'); ?></a>
        </nav>

        <!-- All Forms Tab -->
        <div id="all-forms" class="tab-content active">
            <div class="forms-header">
                <button class="button button-primary" id="createNewFormBtn">
                    <i class="fas fa-plus"></i> <?php _e('Create New Form', 'asad-portfolio'); ?>
                </button>
            </div>

            <div class="forms-list">
                <?php if ($all_forms && count($all_forms) > 0) : ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Form Name', 'asad-portfolio'); ?></th>
                                <th><?php _e('Shortcode', 'asad-portfolio'); ?></th>
                                <th><?php _e('Submissions', 'asad-portfolio'); ?></th>
                                <th><?php _e('Created', 'asad-portfolio'); ?></th>
                                <th><?php _e('Actions', 'asad-portfolio'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_forms as $form) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html($form->name); ?></strong></td>
                                    <td>
                                        <code>[asad_form id="<?php echo $form->id; ?>"]</code>
                                        <button class="button button-small copy-shortcode" data-shortcode='[asad_form id="<?php echo $form->id; ?>"]'>
                                            <i class="fas fa-copy"></i> <?php _e('Copy', 'asad-portfolio'); ?>
                                        </button>
                                    </td>
                                    <td>
                                        <?php
                                        $submissions = asad_get_form_submissions($form->id, 1);
                                        echo count($submissions);
                                        ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($form->created_at)); ?></td>
                                    <td>
                                        <button class="button button-small edit-form" data-form-id="<?php echo $form->id; ?>">
                                            <i class="fas fa-edit"></i> <?php _e('Edit', 'asad-portfolio'); ?>
                                        </button>
                                        <button class="button button-small view-submissions" data-form-id="<?php echo $form->id; ?>">
                                            <i class="fas fa-envelope"></i> <?php _e('Submissions', 'asad-portfolio'); ?>
                                        </button>
                                        <button class="button button-small button-danger delete-form" data-form-id="<?php echo $form->id; ?>">
                                            <i class="fas fa-trash"></i> <?php _e('Delete', 'asad-portfolio'); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <div class="no-forms-message">
                        <i class="fas fa-inbox fa-3x"></i>
                        <h3><?php _e('No forms yet', 'asad-portfolio'); ?></h3>
                        <p><?php _e('Create your first contact form to start collecting submissions.', 'asad-portfolio'); ?></p>
                        <button class="button button-primary button-hero" id="createFirstFormBtn">
                            <i class="fas fa-plus"></i> <?php _e('Create Your First Form', 'asad-portfolio'); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Create Form Tab -->
        <div id="create-form" class="tab-content">
            <div class="form-builder-container">
                <div class="form-settings-panel">
                    <h3><?php _e('Form Settings', 'asad-portfolio'); ?></h3>

                    <div class="setting-group">
                        <label for="form_name"><?php _e('Form Name', 'asad-portfolio'); ?></label>
                        <input type="text" id="form_name" class="regular-text" placeholder="<?php _e('e.g., Contact Form', 'asad-portfolio'); ?>" required>
                    </div>

                    <div class="setting-group">
                        <label for="submit_text"><?php _e('Submit Button Text', 'asad-portfolio'); ?></label>
                        <input type="text" id="submit_text" class="regular-text" value="<?php _e('Submit', 'asad-portfolio'); ?>">
                    </div>

                    <div class="setting-group">
                        <label for="success_message"><?php _e('Success Message', 'asad-portfolio'); ?></label>
                        <textarea id="success_message" rows="3" class="large-text"><?php _e('Thank you! Your form has been submitted successfully.', 'asad-portfolio'); ?></textarea>
                    </div>

                    <div class="setting-group">
                        <label>
                            <input type="checkbox" id="email_notifications" checked>
                            <?php _e('Send Email Notifications', 'asad-portfolio'); ?>
                        </label>
                    </div>

                    <div class="setting-group" id="notification_email_group">
                        <label for="notification_email"><?php _e('Notification Email', 'asad-portfolio'); ?></label>
                        <input type="email" id="notification_email" class="regular-text" value="<?php echo get_option('admin_email'); ?>">
                    </div>

                    <h3><?php _e('Available Fields', 'asad-portfolio'); ?></h3>
                    <div class="available-fields">
                        <button class="field-button" data-type="text">
                            <i class="fas fa-font"></i> <?php _e('Text', 'asad-portfolio'); ?>
                        </button>
                        <button class="field-button" data-type="email">
                            <i class="fas fa-envelope"></i> <?php _e('Email', 'asad-portfolio'); ?>
                        </button>
                        <button class="field-button" data-type="tel">
                            <i class="fas fa-phone"></i> <?php _e('Phone', 'asad-portfolio'); ?>
                        </button>
                        <button class="field-button" data-type="textarea">
                            <i class="fas fa-align-left"></i> <?php _e('Textarea', 'asad-portfolio'); ?>
                        </button>
                        <button class="field-button" data-type="select">
                            <i class="fas fa-list"></i> <?php _e('Dropdown', 'asad-portfolio'); ?>
                        </button>
                        <button class="field-button" data-type="checkbox">
                            <i class="fas fa-check-square"></i> <?php _e('Checkbox', 'asad-portfolio'); ?>
                        </button>
                        <button class="field-button" data-type="radio">
                            <i class="fas fa-dot-circle"></i> <?php _e('Radio', 'asad-portfolio'); ?>
                        </button>
                        <button class="field-button" data-type="number">
                            <i class="fas fa-hashtag"></i> <?php _e('Number', 'asad-portfolio'); ?>
                        </button>
                    </div>
                </div>

                <div class="form-builder-canvas">
                    <h3><?php _e('Form Preview', 'asad-portfolio'); ?></h3>
                    <div class="form-fields-container" id="formFieldsContainer">
                        <p class="empty-form-message"><?php _e('Drag and drop fields here or click on a field type to add it.', 'asad-portfolio'); ?></p>
                    </div>

                    <div class="form-actions">
                        <button class="button button-primary button-large" id="saveFormBtn">
                            <i class="fas fa-save"></i> <?php _e('Save Form', 'asad-portfolio'); ?>
                        </button>
                        <button class="button button-large" id="clearFormBtn">
                            <?php _e('Clear All', 'asad-portfolio'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submissions Tab -->
        <div id="submissions" class="tab-content">
            <div class="submissions-header">
                <label for="submissions_form_select"><?php _e('Select Form:', 'asad-portfolio'); ?></label>
                <select id="submissions_form_select">
                    <option value=""><?php _e('-- Select a form --', 'asad-portfolio'); ?></option>
                    <?php foreach ($all_forms as $form) : ?>
                        <option value="<?php echo $form->id; ?>"><?php echo esc_html($form->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="button" id="exportSubmissionsBtn">
                    <i class="fas fa-download"></i> <?php _e('Export CSV', 'asad-portfolio'); ?>
                </button>
            </div>

            <div id="submissionsContainer">
                <p class="description"><?php _e('Select a form to view its submissions.', 'asad-portfolio'); ?></p>
            </div>
        </div>
    </div>
</div>

<style>
.asad-form-builder {
    margin-top: 20px;
}

.forms-header {
    margin: 20px 0;
}

.no-forms-message {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-forms-message i {
    color: #ddd;
    margin-bottom: 20px;
}

.copy-shortcode {
    margin-left: 10px;
}

.form-builder-container {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 20px;
    margin-top: 20px;
}

.form-settings-panel {
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    height: fit-content;
}

.setting-group {
    margin-bottom: 20px;
}

.setting-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
}

.available-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 15px;
}

.field-button {
    padding: 12px;
    background: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
    text-align: left;
}

.field-button:hover {
    background: #2271b1;
    color: #fff;
    border-color: #2271b1;
}

.field-button i {
    margin-right: 5px;
}

.form-builder-canvas {
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.form-fields-container {
    min-height: 400px;
    padding: 20px;
    background: #f9f9f9;
    border: 2px dashed #ddd;
    border-radius: 5px;
    margin: 20px 0;
}

.empty-form-message {
    text-align: center;
    color: #999;
    padding: 60px 20px;
}

.form-field-item {
    background: #fff;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    position: relative;
}

.field-controls {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    gap: 5px;
}

.form-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
}

.submissions-header {
    display: flex;
    gap: 10px;
    align-items: center;
    margin: 20px 0;
    padding: 15px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
}

#submissions_form_select {
    flex: 1;
    max-width: 300px;
}

@media (max-width: 768px) {
    .form-builder-container {
        grid-template-columns: 1fr;
    }

    .available-fields {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    let formFields = [];
    let currentFormId = null;

    // Add field to form
    $('.field-button').on('click', function() {
        const fieldType = $(this).data('type');
        addFieldToForm(fieldType);
    });

    function addFieldToForm(type) {
        const fieldId = 'field_' + Date.now();
        const field = {
            id: fieldId,
            type: type,
            name: fieldId,
            label: type.charAt(0).toUpperCase() + type.slice(1),
            placeholder: '',
            required: false,
            options: type === 'select' || type === 'checkbox' || type === 'radio' ? ['Option 1', 'Option 2'] : null
        };

        formFields.push(field);
        renderFormPreview();
    }

    function renderFormPreview() {
        const container = $('#formFieldsContainer');
        container.empty();

        if (formFields.length === 0) {
            container.html('<p class="empty-form-message"><?php _e('Drag and drop fields here or click on a field type to add it.', 'asad-portfolio'); ?></p>');
            return;
        }

        formFields.forEach((field, index) => {
            const fieldHtml = `
                <div class="form-field-item" data-index="${index}">
                    <div class="field-controls">
                        <button class="button button-small edit-field" data-index="${index}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="button button-small button-danger remove-field" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="field-preview">
                        <label><strong>${field.label}</strong> ${field.required ? '<span style="color:red;">*</span>' : ''}</label>
                        <input type="${field.type}" class="regular-text" placeholder="${field.placeholder}" disabled>
                    </div>
                </div>
            `;
            container.append(fieldHtml);
        });

        // Remove field
        $('.remove-field').on('click', function() {
            const index = $(this).data('index');
            formFields.splice(index, 1);
            renderFormPreview();
        });
    }

    // Save form
    $('#saveFormBtn').on('click', function() {
        const formName = $('#form_name').val();
        if (!formName) {
            alert('<?php _e('Please enter a form name.', 'asad-portfolio'); ?>');
            return;
        }

        if (formFields.length === 0) {
            alert('<?php _e('Please add at least one field to the form.', 'asad-portfolio'); ?>');
            return;
        }

        const settings = {
            submit_text: $('#submit_text').val(),
            success_message: $('#success_message').val(),
            email_notifications: $('#email_notifications').is(':checked'),
            notification_email: $('#notification_email').val()
        };

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> <?php _e('Saving...', 'asad-portfolio'); ?>');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'asad_save_form',
                nonce: '<?php echo wp_create_nonce('asad-admin-nonce'); ?>',
                form_id: currentFormId,
                name: formName,
                fields: JSON.stringify(formFields),
                settings: JSON.stringify(settings)
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            complete: function() {
                $('#saveFormBtn').prop('disabled', false).html('<i class="fas fa-save"></i> <?php _e('Save Form', 'asad-portfolio'); ?>');
            }
        });
    });

    // Tab navigation
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('href');
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.tab-content').removeClass('active').hide();
        $(target).addClass('active').fadeIn(300);
    });

    // Create new form button
    $('#createNewFormBtn, #createFirstFormBtn').on('click', function() {
        $('.nav-tab[href="#create-form"]').click();
    });

    // Copy shortcode
    $('.copy-shortcode').on('click', function() {
        const shortcode = $(this).data('shortcode');
        navigator.clipboard.writeText(shortcode).then(function() {
            alert('<?php _e('Shortcode copied!', 'asad-portfolio'); ?>');
        });
    });
});
</script>
