<?php
/**
 * AI Content Assistant
 * AI-powered content generation and optimization
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create AI content tables
 */
function asad_create_ai_content_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // AI generations history
    $table_name = $wpdb->prefix . 'asad_ai_generations';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20),
        generation_type varchar(50) NOT NULL,
        prompt text NOT NULL,
        generated_content longtext NOT NULL,
        model varchar(50) NOT NULL,
        tokens_used int(11),
        created_by bigint(20) NOT NULL,
        created_date datetime NOT NULL,
        PRIMARY KEY (id),
        KEY post_id (post_id),
        KEY generation_type (generation_type)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // AI settings
    $ai_settings_table = $wpdb->prefix . 'asad_ai_settings';
    $sql2 = "CREATE TABLE IF NOT EXISTS $ai_settings_table (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        setting_key varchar(100) NOT NULL,
        setting_value text NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY setting_key (setting_key)
    ) $charset_collate;";

    dbDelta($sql2);
}
add_action('after_switch_theme', 'asad_create_ai_content_tables');

/**
 * Get AI settings
 */
function asad_get_ai_settings() {
    return get_option('asad_ai_settings', array(
        'api_key' => '',
        'api_provider' => 'openai', // openai, anthropic, custom
        'default_model' => 'gpt-3.5-turbo',
        'temperature' => 0.7,
        'max_tokens' => 2000,
        'enabled' => false,
        'auto_seo' => true,
        'auto_tags' => true
    ));
}

/**
 * Update AI settings
 */
function asad_update_ai_settings($settings) {
    return update_option('asad_ai_settings', $settings);
}

/**
 * Generate content using AI
 */
function asad_generate_ai_content($prompt, $type = 'article', $options = array()) {
    $settings = asad_get_ai_settings();

    if (!$settings['enabled'] || empty($settings['api_key'])) {
        return array(
            'success' => false,
            'message' => 'AI is not configured. Please add your API key in settings.'
        );
    }

    $defaults = array(
        'temperature' => $settings['temperature'],
        'max_tokens' => $settings['max_tokens'],
        'model' => $settings['default_model'],
        'tone' => 'professional',
        'length' => 'medium'
    );

    $options = wp_parse_args($options, $defaults);

    // Build the system message based on type
    $system_messages = array(
        'article' => 'You are a professional content writer. Write engaging, SEO-optimized articles.',
        'meta_description' => 'You are an SEO expert. Write compelling meta descriptions under 160 characters.',
        'product_description' => 'You are a product copywriter. Write persuasive, benefit-focused product descriptions.',
        'social_post' => 'You are a social media manager. Write engaging social media posts.',
        'rewrite' => 'You are an editor. Rewrite the content to improve clarity and engagement.',
        'summarize' => 'You are an expert summarizer. Create concise summaries that capture key points.',
        'expand' => 'You are a content expander. Add more detail and depth to the content.',
        'translate' => 'You are a professional translator. Translate accurately while maintaining tone.',
        'keywords' => 'You are an SEO specialist. Generate relevant keywords and tags.',
        'headline' => 'You are a headline writer. Create compelling, click-worthy headlines.'
    );

    $system_message = isset($system_messages[$type]) ? $system_messages[$type] : $system_messages['article'];

    // Add tone and length instructions
    $tone_instructions = array(
        'professional' => 'Use a professional and authoritative tone.',
        'casual' => 'Use a casual and friendly tone.',
        'formal' => 'Use a formal and academic tone.',
        'creative' => 'Use a creative and engaging tone.',
        'persuasive' => 'Use a persuasive and compelling tone.'
    );

    $length_instructions = array(
        'short' => 'Keep it brief and concise (100-300 words).',
        'medium' => 'Write a moderate length piece (500-800 words).',
        'long' => 'Write a comprehensive piece (1000-1500 words).'
    );

    $full_system = $system_message;
    if (isset($tone_instructions[$options['tone']])) {
        $full_system .= ' ' . $tone_instructions[$options['tone']];
    }
    if (isset($length_instructions[$options['length']])) {
        $full_system .= ' ' . $length_instructions[$options['length']];
    }

    // Call the appropriate AI provider
    if ($settings['api_provider'] === 'openai') {
        $result = asad_call_openai($prompt, $full_system, $options, $settings);
    } else {
        $result = array(
            'success' => false,
            'message' => 'Unsupported AI provider'
        );
    }

    // Log the generation
    if ($result['success']) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'asad_ai_generations';

        $wpdb->insert(
            $table_name,
            array(
                'generation_type' => $type,
                'prompt' => $prompt,
                'generated_content' => $result['content'],
                'model' => $options['model'],
                'tokens_used' => $result['tokens_used'] ?? 0,
                'created_by' => get_current_user_id(),
                'created_date' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%d', '%d', '%s')
        );
    }

    return $result;
}

/**
 * Call OpenAI API
 */
function asad_call_openai($prompt, $system_message, $options, $settings) {
    $api_key = $settings['api_key'];
    $model = $options['model'];

    $body = array(
        'model' => $model,
        'messages' => array(
            array('role' => 'system', 'content' => $system_message),
            array('role' => 'user', 'content' => $prompt)
        ),
        'temperature' => floatval($options['temperature']),
        'max_tokens' => intval($options['max_tokens'])
    );

    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode($body),
        'timeout' => 60
    ));

    if (is_wp_error($response)) {
        return array(
            'success' => false,
            'message' => 'API Error: ' . $response->get_error_message()
        );
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($body['error'])) {
        return array(
            'success' => false,
            'message' => 'OpenAI Error: ' . $body['error']['message']
        );
    }

    if (!isset($body['choices'][0]['message']['content'])) {
        return array(
            'success' => false,
            'message' => 'Invalid API response'
        );
    }

    return array(
        'success' => true,
        'content' => $body['choices'][0]['message']['content'],
        'tokens_used' => $body['usage']['total_tokens'] ?? 0
    );
}

/**
 * Generate blog post outline
 */
function asad_generate_post_outline($topic, $keywords = array()) {
    $prompt = "Create a detailed blog post outline for: $topic\n\n";

    if (!empty($keywords)) {
        $prompt .= "Include these keywords: " . implode(', ', $keywords) . "\n\n";
    }

    $prompt .= "Provide:\n";
    $prompt .= "1. A catchy title\n";
    $prompt .= "2. An introduction hook\n";
    $prompt .= "3. 5-7 main sections with subpoints\n";
    $prompt .= "4. A conclusion\n";
    $prompt .= "5. 3-5 FAQs\n";

    return asad_generate_ai_content($prompt, 'article', array('length' => 'short'));
}

/**
 * Generate meta description
 */
function asad_generate_meta_description($content, $keywords = array()) {
    $content_excerpt = wp_trim_words($content, 100);

    $prompt = "Write a compelling meta description (150-160 characters) for this content:\n\n$content_excerpt\n\n";

    if (!empty($keywords)) {
        $prompt .= "Include these keywords: " . implode(', ', $keywords);
    }

    return asad_generate_ai_content($prompt, 'meta_description', array('max_tokens' => 100));
}

/**
 * Generate keywords
 */
function asad_generate_keywords($content) {
    $content_excerpt = wp_trim_words($content, 200);

    $prompt = "Generate 10-15 relevant SEO keywords/tags for this content:\n\n$content_excerpt\n\n";
    $prompt .= "Return as comma-separated list.";

    return asad_generate_ai_content($prompt, 'keywords', array('max_tokens' => 200));
}

/**
 * Improve content
 */
function asad_improve_content($content, $improvement_type = 'general') {
    $types = array(
        'general' => 'Improve this content for better readability and engagement',
        'seo' => 'Optimize this content for SEO while maintaining readability',
        'grammar' => 'Fix grammar, spelling, and punctuation errors',
        'shorten' => 'Make this content more concise without losing key information',
        'expand' => 'Expand this content with more details and examples',
        'simplify' => 'Simplify this content for easier understanding'
    );

    $instruction = isset($types[$improvement_type]) ? $types[$improvement_type] : $types['general'];

    $prompt = "$instruction:\n\n$content";

    return asad_generate_ai_content($prompt, 'rewrite');
}

/**
 * Generate image alt text
 */
function asad_generate_image_alt_text($image_url, $context = '') {
    $prompt = "Generate a descriptive alt text for an image";

    if (!empty($context)) {
        $prompt .= " in the context of: $context";
    }

    $prompt .= "\n\nImage URL: $image_url\n\n";
    $prompt .= "Keep it under 125 characters and be descriptive but concise.";

    return asad_generate_ai_content($prompt, 'meta_description', array('max_tokens' => 50));
}

/**
 * Get AI generation history
 */
function asad_get_ai_history($limit = 50, $type = null) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_ai_generations';

    if ($type) {
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE generation_type = %s ORDER BY created_date DESC LIMIT %d",
            $type, $limit
        ), ARRAY_A);
    }

    return $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY created_date DESC LIMIT %d",
        $limit
    ), ARRAY_A);
}

/**
 * Get AI usage statistics
 */
function asad_get_ai_statistics() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'asad_ai_generations';

    return array(
        'total_generations' => $wpdb->get_var("SELECT COUNT(*) FROM $table_name"),
        'total_tokens' => $wpdb->get_var("SELECT SUM(tokens_used) FROM $table_name"),
        'by_type' => $wpdb->get_results(
            "SELECT generation_type, COUNT(*) as count FROM $table_name GROUP BY generation_type",
            ARRAY_A
        ),
        'recent_activity' => $wpdb->get_results(
            "SELECT DATE(created_date) as date, COUNT(*) as count
             FROM $table_name
             WHERE created_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY DATE(created_date)
             ORDER BY date DESC",
            ARRAY_A
        )
    );
}

/**
 * AJAX: Generate content
 */
function asad_ajax_generate_ai_content() {
    check_ajax_referer('asad_ai_nonce', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Insufficient permissions');
    }

    $prompt = sanitize_textarea_field($_POST['prompt'] ?? '');
    $type = sanitize_text_field($_POST['type'] ?? 'article');
    $tone = sanitize_text_field($_POST['tone'] ?? 'professional');
    $length = sanitize_text_field($_POST['length'] ?? 'medium');

    if (empty($prompt)) {
        wp_send_json_error('Prompt is required');
    }

    $result = asad_generate_ai_content($prompt, $type, array(
        'tone' => $tone,
        'length' => $length
    ));

    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result['message']);
    }
}
add_action('wp_ajax_asad_generate_ai_content', 'asad_ajax_generate_ai_content');

/**
 * AJAX: Save AI settings
 */
function asad_ajax_save_ai_settings() {
    check_ajax_referer('asad_ai_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $settings = array(
        'api_key' => sanitize_text_field($_POST['api_key'] ?? ''),
        'api_provider' => sanitize_text_field($_POST['api_provider'] ?? 'openai'),
        'default_model' => sanitize_text_field($_POST['default_model'] ?? 'gpt-3.5-turbo'),
        'temperature' => floatval($_POST['temperature'] ?? 0.7),
        'max_tokens' => intval($_POST['max_tokens'] ?? 2000),
        'enabled' => isset($_POST['enabled']),
        'auto_seo' => isset($_POST['auto_seo']),
        'auto_tags' => isset($_POST['auto_tags'])
    );

    asad_update_ai_settings($settings);

    wp_send_json_success('Settings saved successfully');
}
add_action('wp_ajax_asad_save_ai_settings', 'asad_ajax_save_ai_settings');

/**
 * AJAX: Get AI history
 */
function asad_ajax_get_ai_history() {
    check_ajax_referer('asad_ai_nonce', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Insufficient permissions');
    }

    $history = asad_get_ai_history(50);
    wp_send_json_success($history);
}
add_action('wp_ajax_asad_get_ai_history', 'asad_ajax_get_ai_history');

/**
 * Add AI assistant meta box to post editor
 */
function asad_add_ai_assistant_meta_box() {
    $settings = asad_get_ai_settings();

    if (!$settings['enabled']) {
        return;
    }

    add_meta_box(
        'asad_ai_assistant',
        'AI Content Assistant',
        'asad_ai_assistant_meta_box_callback',
        array('post', 'page'),
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'asad_add_ai_assistant_meta_box');

/**
 * AI assistant meta box callback
 */
function asad_ai_assistant_meta_box_callback($post) {
    wp_nonce_field('asad_ai_assistant_meta', 'asad_ai_assistant_nonce');
    ?>
    <div class="asad-ai-assistant-box">
        <p><strong>Quick AI Actions:</strong></p>

        <button type="button" class="button button-small asad-ai-action" data-action="generate-title">
            Generate Title
        </button>

        <button type="button" class="button button-small asad-ai-action" data-action="generate-meta">
            Generate Meta Description
        </button>

        <button type="button" class="button button-small asad-ai-action" data-action="generate-keywords">
            Generate Keywords
        </button>

        <button type="button" class="button button-small asad-ai-action" data-action="improve-content">
            Improve Content
        </button>

        <hr style="margin: 15px 0;">

        <p><strong>Content Generator:</strong></p>
        <textarea id="asad-ai-prompt" rows="3" style="width: 100%;" placeholder="Enter your prompt..."></textarea>

        <select id="asad-ai-tone" style="width: 100%; margin-top: 5px;">
            <option value="professional">Professional</option>
            <option value="casual">Casual</option>
            <option value="formal">Formal</option>
            <option value="creative">Creative</option>
            <option value="persuasive">Persuasive</option>
        </select>

        <select id="asad-ai-length" style="width: 100%; margin-top: 5px;">
            <option value="short">Short (100-300 words)</option>
            <option value="medium" selected>Medium (500-800 words)</option>
            <option value="long">Long (1000-1500 words)</option>
        </select>

        <button type="button" class="button button-primary" id="asad-ai-generate" style="width: 100%; margin-top: 10px;">
            Generate Content
        </button>

        <div id="asad-ai-result" style="margin-top: 15px; display: none;">
            <p><strong>Generated Content:</strong></p>
            <div id="asad-ai-content" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;"></div>
            <button type="button" class="button" id="asad-ai-insert" style="margin-top: 10px;">
                Insert into Editor
            </button>
        </div>

        <div id="asad-ai-loading" style="display: none; text-align: center; margin: 15px 0;">
            <div class="spinner is-active"></div>
            <p>Generating content...</p>
        </div>
    </div>

    <style>
        .asad-ai-assistant-box .button-small {
            font-size: 11px;
            padding: 4px 8px;
            margin: 2px;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        const aiNonce = '<?php echo wp_create_nonce('asad_ai_nonce'); ?>';

        // Quick actions
        $('.asad-ai-action').on('click', function() {
            const action = $(this).data('action');
            let prompt = '';

            const title = $('#title').val();
            const content = wp.editor.getContent('content');

            switch(action) {
                case 'generate-title':
                    prompt = 'Generate a catchy, SEO-optimized title for this content:\n\n' + content.substring(0, 500);
                    break;
                case 'generate-meta':
                    prompt = 'Generate a meta description for: ' + title + '\n\n' + content.substring(0, 500);
                    break;
                case 'generate-keywords':
                    prompt = 'Generate SEO keywords for: ' + title + '\n\n' + content.substring(0, 500);
                    break;
                case 'improve-content':
                    prompt = 'Improve this content for better readability and SEO:\n\n' + content;
                    break;
            }

            $('#asad-ai-prompt').val(prompt);
            $('#asad-ai-generate').click();
        });

        // Generate content
        $('#asad-ai-generate').on('click', function() {
            const prompt = $('#asad-ai-prompt').val();
            const tone = $('#asad-ai-tone').val();
            const length = $('#asad-ai-length').val();

            if (!prompt) {
                alert('Please enter a prompt');
                return;
            }

            $('#asad-ai-loading').show();
            $('#asad-ai-result').hide();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'asad_generate_ai_content',
                    nonce: aiNonce,
                    prompt: prompt,
                    type: 'article',
                    tone: tone,
                    length: length
                },
                success: function(response) {
                    $('#asad-ai-loading').hide();

                    if (response.success) {
                        $('#asad-ai-content').html('<pre style="white-space: pre-wrap;">' + response.data.content + '</pre>');
                        $('#asad-ai-result').show();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    $('#asad-ai-loading').hide();
                    alert('Error generating content. Please try again.');
                }
            });
        });

        // Insert content
        $('#asad-ai-insert').on('click', function() {
            const content = $('#asad-ai-content pre').text();
            wp.editor.insert('content', content);
            $('#asad-ai-result').hide();
            $('#asad-ai-prompt').val('');
        });
    });
    </script>
    <?php
}
