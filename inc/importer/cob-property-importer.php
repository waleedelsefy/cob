<?php
/**
 * AJAX WordPress Importer for 'properties' posts from a CSV file.
 *
 * Version 6: Corrected translation function calls and fixed undefined variable notices.
 * This version ensures all code runs within the correct WordPress hooks to prevent "too early" errors.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Get the importer configuration array.
 * This function prevents translation functions from running too early.
 *
 * @return array The configuration array.
 */
function cob_get_property_importer_config() {
    return [
        'post_type' => 'properties',
        'source_id_meta_key' => '_property_source_id',
        'csv_delimiter' => ',',
        'batch_size' => 1,
        'ajax_timeout_seconds' => 300,
        'status_option_name' => 'cob_property_importer_status',
        'taxonomies_map' => [
            'compound_name'           => 'compound',
            'compound_developer_name' => 'developer',
            'compound_area_name'      => 'city',
            'property_type_name'      => 'type',
            'finishing'               => 'finishing',
        ],
        'csv_column_map_en' => [
            'id' => 'id', 'name' => 'meta_title_en', 'slug' => 'all_slugs_en', 'description' => 'meta_description_en',
            'gallery_img_base' => 'Property_img', 'gallery_img_count' => 8,
        ],
        'csv_column_map_ar' => [
            'id' => 'id', 'name' => 'name', 'slug' => 'all_slugs_ar', 'description' => 'description',
            'gallery_img_base' => 'Property_img', 'gallery_img_count' => 8,
        ],
    ];
}

// 1. Register Admin Page & Enqueue Assets
add_action('admin_menu', 'cob_prop_importer_register_page');
function cob_prop_importer_register_page() {
    $hook = add_submenu_page(
        'tools.php',
        __('Property Importer (AJAX)', 'cob_theme'),
        __('Import Properties', 'cob_theme'),
        'manage_options',
        'cob-property-importer',
        'cob_prop_importer_render_page'
    );
    add_action("load-{$hook}", 'cob_prop_importer_enqueue_assets');
}

function cob_prop_importer_enqueue_assets() {
    $config = cob_get_property_importer_config();
    $js_path = get_stylesheet_directory_uri() . '/inc/importer/cob-property-importer.js';

    wp_enqueue_script('cob-prop-importer-js', $js_path, ['jquery'], '3.0.0', true);

    wp_localize_script('cob-prop-importer-js', 'cobPropImporter', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cob_prop_importer_nonce'),
        'i18n' => [
            'confirm_new_import' => __('Are you sure you want to start a new import?', 'cob_theme'),
            'confirm_resume' => __('Do you want to resume the previous import?', 'cob_theme'),
            'confirm_cancel' => __('Are you sure you want to cancel the process?', 'cob_theme'),
            'error_selecting_file' => __('Please select a CSV file.', 'cob_theme'),
            'preparing_import' => __('Preparing import...', 'cob_theme'),
            'import_complete' => __('🎉 Import completed successfully! 🎉', 'cob_theme'),
            'connection_error' => __('❌ Server connection error', 'cob_theme'),
            'processed' => __('Processed', 'cob_theme'),
            'of' => __('of', 'cob_theme'),
            'imported' => __('Imported', 'cob_theme'),
            'updated' => __('Updated', 'cob_theme'),
            'failed' => __('Failed', 'cob_theme'),
        ]
    ]);
    wp_add_inline_style('wp-admin', "
        .cob-progress-bar-container { border: 1px solid #ccc; padding: 2px; width: 100%; max-width: 600px; border-radius: 5px; background: #f1f1f1; margin-bottom:10px; }
        .cob-progress-bar { background-color: #0073aa; height: 24px; width: 0%; text-align: center; line-height: 24px; color: white; border-radius: 3px; transition: width 0.3s ease-in-out; }
        #importer-log { background: #1e1e1e; color: #f1f1f1; border: 1px solid #e5e5e5; padding: 10px; margin-top: 15px; max-height: 400px; overflow-y: auto; font-family: monospace; white-space: pre-wrap; border-radius: 4px; }
        .importer-source-choice, .importer-language-choice { margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; background: #fff; }
        #source-server-container, #source-upload-container { padding-left: 20px; }
    ");
}

// 2. Render Importer Page HTML
function cob_prop_importer_render_page() {
    $config = cob_get_property_importer_config();
    $import_status = get_option($config['status_option_name'], false);

    $imports_dir = WP_CONTENT_DIR . '/csv-imports/';
    $server_files = [];
    if (is_dir($imports_dir)) {
        $files = array_diff(scandir($imports_dir), ['..', '.']);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                $server_files[] = $file;
            }
        }
    } else {
        wp_mkdir_p($imports_dir);
    }
    ?>
    <div class="wrap">
        <h1><?php _e('Property Importer (AJAX)', 'cob_theme'); ?></h1>
        <p><?php _e('This tool imports and updates properties from a CSV file, with support for translation linking.', 'cob_theme'); ?></p>
        <div class="notice notice-warning">
            <p><strong><?php _e('Important:', 'cob_theme'); ?></strong> <?php printf(__('The batch size is set to %d to avoid timeouts.', 'cob_theme'), esc_html($config['batch_size'])); ?></p>
        </div>

        <?php if ($import_status && isset($import_status['progress']) && $import_status['progress'] < 100) : ?>
            <div id="resume-notice" class="notice notice-warning is-dismissible"><p><?php printf(__('A previous import for %s was not completed. You can resume or cancel it.', 'cob_theme'), '<code>' . esc_html($import_status['original_filename']) . '</code>'); ?></p></div>
        <?php endif; ?>

        <form id="cob-importer-form" method="post" enctype="multipart/form-data">
            <h2><?php _e('Step 1: Choose File Source', 'cob_theme'); ?></h2>
            <div class="importer-source-choice">
                <p><label><input type="radio" name="import_source" value="upload" checked> <?php _e('Upload file from your computer', 'cob_theme'); ?></label></p>
                <div id="source-upload-container">
                    <input type="file" id="property_csv" name="property_csv" accept=".csv,text/csv">
                </div>
                <hr>
                <p><label><input type="radio" name="import_source" value="server"> <?php _e('Select a file from the server', 'cob_theme'); ?></label></p>
                <div id="source-server-container" style="display:none;">
                    <?php if (!empty($server_files)) : ?>
                        <select id="server_csv_file" name="server_csv_file" style="min-width: 300px;">
                            <?php foreach ($server_files as $file) : ?><option value="<?php echo esc_attr($file); ?>"><?php echo esc_html($file); ?></option><?php endforeach; ?>
                        </select>
                        <p class="description"><?php printf(__('Path: %s', 'cob_theme'), '<code>' . esc_html($imports_dir) . '</code>'); ?></p>
                    <?php else : ?><p><?php printf(__('No CSV files found. Please upload files to %s', 'cob_theme'), '<code>/wp-content/csv-imports/</code>'); ?></p><?php endif; ?>
                </div>
            </div>

            <h2><?php _e('Step 2: Choose Import Language', 'cob_theme'); ?></h2>
            <div class="importer-language-choice">
                <select id="import_language" name="import_language" style="min-width: 300px;">
                    <option value="en">English</option>
                    <option value="ar" selected>العربية</option>
                </select>
            </div>

            <button type="submit" class="button button-primary"><?php _e('Start New Import', 'cob_theme'); ?></button>
            <button type="button" id="resume-import" class="button" style="<?php echo ($import_status && $import_status['progress'] < 100) ? '' : 'display:none;'; ?>"><?php _e('Resume Import', 'cob_theme'); ?></button>
            <button type="button" id="cancel-import" class="button button-secondary" style="<?php echo $import_status ? '' : 'display:none;'; ?>"><?php _e('Cancel & Reset', 'cob_theme'); ?></button>
        </form>

        <div id="importer-progress-container" style="display:none; margin-top: 20px;">
            <h3><?php _e('Import Progress', 'cob_theme'); ?></h3>
            <div class="cob-progress-bar-container"><div id="importer-progress-bar" class="cob-progress-bar">0%</div></div>
            <p id="importer-stats"></p>
            <h4><?php _e('Log:', 'cob_theme'); ?></h4><div id="importer-log"></div>
        </div>
    </div>
    <?php
}

// 3. AJAX Handler
add_action('wp_ajax_cob_run_property_importer', 'cob_ajax_run_property_importer_callback');
function cob_ajax_run_property_importer_callback() {
    $config = cob_get_property_importer_config();
    check_ajax_referer('cob_prop_importer_nonce', 'nonce');

    if (!current_user_can('manage_options')) { wp_send_json_error(['message' => __('Insufficient permissions.', 'cob_theme')]); }

    @set_time_limit($config['ajax_timeout_seconds']);
    @ini_set('memory_limit', '512M');
    wp_raise_memory_limit('admin');

    if (!function_exists('media_sideload_image')) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }

    $action = isset($_POST['importer_action']) ? sanitize_text_field($_POST['importer_action']) : '';
    $log_messages = []; // FIX: Initialize log messages array

    switch ($action) {
        case 'prepare':
            $old_status = get_option($config['status_option_name']);
            if ($old_status && !empty($old_status['file_path']) && file_exists($old_status['file_path'])) {
                wp_delete_file($old_status['file_path']);
            }
            delete_option($config['status_option_name']);

            $source_type = isset($_POST['source_type']) ? sanitize_text_field($_POST['source_type']) : 'upload';
            $file_path = '';
            $original_filename = '';

            if ($source_type === 'upload') {
                if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                    wp_send_json_error(['message' => __('No file uploaded or an error occurred.', 'cob_theme')]);
                }
                $move_file = wp_handle_upload($_FILES['csv_file'], ['test_form' => false, 'mimes' => ['csv' => 'text/csv']]);
                if (!$move_file || isset($move_file['error'])) {
                    wp_send_json_error(['message' => __('Error handling uploaded file:', 'cob_theme') . ' ' . ($move_file['error'] ?? 'Unknown error')]);
                }
                $file_path = $move_file['file'];
                $original_filename = sanitize_file_name($_FILES['csv_file']['name']);
            } elseif ($source_type === 'server') {
                $file_name = isset($_POST['file_name']) ? sanitize_file_name($_POST['file_name']) : '';
                $server_file_path = WP_CONTENT_DIR . '/csv-imports/' . $file_name;

                if (empty($file_name) || !file_exists($server_file_path) || !is_readable($server_file_path)) {
                    wp_send_json_error(['message' => __('File not found on server or is not readable.', 'cob_theme')]);
                }

                $upload_dir = wp_upload_dir();
                $temp_file_path = wp_unique_filename($upload_dir['path'], basename($server_file_path));
                $temp_file_full_path = $upload_dir['path'] . '/' . $temp_file_path;

                if (!copy($server_file_path, $temp_file_full_path)) {
                    wp_send_json_error(['message' => __('Failed to copy file from server to temporary directory.', 'cob_theme')]);
                }
                $file_path = $temp_file_full_path;
                $original_filename = $file_name;
            }

            $total_rows = 0;
            $headers = [];
            if (($handle = @fopen($file_path, "r")) !== FALSE) {
                $headers = array_map('trim', fgetcsv($handle, 0, $config['csv_delimiter']));
                while (fgetcsv($handle, 0, $config['csv_delimiter']) !== FALSE) $total_rows++;
                fclose($handle);
            } else {
                if (file_exists($file_path)) wp_delete_file($file_path);
                wp_send_json_error(['message' => __('Failed to open the uploaded file.', 'cob_theme')]);
            }

            $status = [
                'file_path' => $file_path, 'original_filename' => $original_filename, 'total_rows' => $total_rows,
                'processed' => 0, 'imported_count' => 0, 'updated_count' => 0, 'failed_count' => 0,
                'progress' => 0, 'language' => isset($_POST['import_language']) ? sanitize_text_field($_POST['import_language']) : 'en',
                'headers' => $headers,
            ];
            update_option($config['status_option_name'], $status, 'no');
            wp_send_json_success(['status' => $status, 'log' => [__('Preparation successful. Total rows:', 'cob_theme') . " {$total_rows}"]]);
            break;

        case 'run':
            $status = get_option($config['status_option_name']);
            if (!$status || empty($status['file_path']) || !file_exists($status['file_path'])) {
                wp_send_json_error(['message' => 'لم يتم العثور على عملية استيراد صالحة.']);
            }

            if ($status['processed'] >= $status['total_rows']) {
                wp_send_json_success(['status' => $status, 'log' => ["الاستيراد مكتمل بالفعل."], 'done' => true]);
            }

            $config['target_language'] = $status['language'];

            if (($handle = fopen($status['file_path'], "r")) !== FALSE) {
                fgetcsv($handle);
                for ($i = 0; $i < $status['processed']; $i++) { if(fgetcsv($handle) === FALSE) break; }

                $raw_row_data = fgetcsv($handle, 0, $config['csv_delimiter']);
                if($raw_row_data !== FALSE) {
                    $status['processed']++;
                    if (count($status['headers']) !== count($raw_row_data)) {
                        $log_messages[] = "({$status['processed']}) <span style='color:red;'>خطأ فادح: عدد الأعمدة لا يطابق الرأس. (وجد: " . count($raw_row_data) . ", متوقع: " . count($status['headers']) . ").</span>";
                        $status['failed_count']++;
                    } else {
                        $row_data = @array_combine($status['headers'], $raw_row_data);
                        $import_result = cob_import_single_property($row_data, $config, $status['processed']);

                        if (isset($import_result['log'])) $log_messages = array_merge($log_messages, $import_result['log']);
                        if ($import_result['status'] === 'imported') $status['imported_count']++;
                        elseif ($import_result['status'] === 'updated') $status['updated_count']++;
                        else $status['failed_count']++;
                    }
                } else {
                    $status['processed'] = $status['total_rows'];
                }
                fclose($handle);
            }

            $status['progress'] = ($status['total_rows'] > 0) ? round(($status['processed'] / $status['total_rows']) * 100) : 100;
            $done = ($status['processed'] >= $status['total_rows']);
            if ($done) {
                if (file_exists($status['file_path'])) {
                    wp_delete_file($status['file_path']);
                }
                $status['file_path'] = null;
                $log_messages[] = "اكتمل الاستيراد. تم حذف الملف المؤقت.";
            }

            update_option($config['status_option_name'], $status, 'no');
            wp_send_json_success(['status' => $status, 'log' => $log_messages, 'done' => $done]);
            break;

        case 'cancel':
            $status = get_option($config['status_option_name']);
            if ($status && !empty($status['file_path']) && file_exists($status['file_path'])) {
                wp_delete_file($status['file_path']);
            }
            delete_option($config['status_option_name']);
            wp_send_json_success(['message' => 'تم إلغاء العملية ومسح الحالة بنجاح.']);
            break;
        case 'get_status':
            $status = get_option($config['status_option_name']);
            if ($status && isset($status['progress']) && $status['progress'] < 100 && !empty($status['original_filename'])) {
                wp_send_json_success(['status' => $status]);
            } else {
                delete_option($config['status_option_name']);
                wp_send_json_error(['message' => 'لا توجد عملية استيراد سابقة قابلة للاستئناف.']);
            }
            break;
        default:
            wp_send_json_error(['message' => 'إجراء غير معروف.']);
    }
}


// 4. Import Single Property Post
function cob_import_single_property($csv_row, &$config, $row_num) {
    $log = [];
    $result_status = 'failed';

    $lang = $config['target_language'];
    $map = $config['csv_column_map_' . $lang];
    $post_type = $config['post_type'];
    $source_id_meta_key = $config['source_id_meta_key'];

    $source_id = trim($csv_row['id'] ?? '');
    if (empty($source_id)) {
        $log[] = "({$row_num}) <span style='color:red;'>خطأ: عمود 'id' فارغ.</span>";
        return ['status' => 'failed', 'log' => $log];
    }

    $post_title = sanitize_text_field(trim($csv_row[$map['name']] ?? ''));
    if (empty($post_title)) {
        $log[] = "({$row_num}) <span style='color:red;'>خطأ: اسم العقار ('{$map['name']}') فارغ.</span>";
        return ['status' => 'failed', 'log' => $log];
    }

    $post_slug = sanitize_title(trim($csv_row[$map['slug']] ?? ''));
    if(empty($post_slug)) $post_slug = sanitize_title($post_title);

    $post_content = wp_kses_post($csv_row[$map['description']] ?? '');

    $post_id = null;
    $post_in_lang_exists = false;

    if (function_exists('pll_get_post_language')) {
        $existing_posts_query = new WP_Query([
            'post_type' => $post_type, 'meta_key' => $source_id_meta_key, 'meta_value' => $source_id,
            'post_status' => 'any', 'posts_per_page' => 1, 'lang' => $lang, 'fields' => 'ids'
        ]);
        if ($existing_posts_query->have_posts()) {
            $post_id = $existing_posts_query->posts[0];
            $post_in_lang_exists = true;
        }
    }

    $post_data = ['post_title' => $post_title, 'post_name' => $post_slug, 'post_content' => $post_content, 'post_type' => $post_type, 'post_status' => 'publish'];

    if ($post_in_lang_exists) {
        $post_data['ID'] = $post_id;
        wp_update_post($post_data);
        $log[] = "({$row_num}) <span style='color:#00A86B;'>تم تحديث '{$post_title}' (ID: {$post_id}).</span>";
        $result_status = 'updated';
    } else {
        $post_id = wp_insert_post($post_data, true);
        if (is_wp_error($post_id)) {
            $log[] = "({$row_num}) <span style='color:red;'>فشل إنشاء '{$post_title}': " . $post_id->get_error_message() . "</span>";
            return ['status' => 'failed', 'log' => $log];
        }
        $log[] = "({$row_num}) <span style='color:lightgreen;'>تم إنشاء '{$post_title}' كمنشور جديد (ID: {$post_id}).</span>";
        $result_status = 'imported';
    }

    if ($post_id) {
        update_post_meta($post_id, $source_id_meta_key, $source_id);
        if (function_exists('pll_set_post_language')) { pll_set_post_language($post_id, $lang); }

        if (function_exists('pll_save_post_translations')) {
            $translations = [];
            $all_posts_with_id = get_posts(['post_type' => $post_type, 'meta_key' => $source_id_meta_key, 'meta_value' => $source_id, 'numberposts' => -1, 'fields' => 'ids']);
            if (count($all_posts_with_id) > 1) {
                foreach ($all_posts_with_id as $p_id) {
                    $p_lang = pll_get_post_language($p_id);
                    if($p_lang) $translations[$p_lang] = $p_id;
                }
                if(count($translations) > 1) {
                    pll_save_post_translations($translations);
                    $log[] = "({$row_num}) &nbsp;&nbsp;&hookrightarrow; <span style='color:cyan;'>تم ربط الترجمات.</span>";
                }
            }
        }

        foreach($config['taxonomies_map'] as $csv_col => $tax_slug) {
            if (!empty($csv_row[$csv_col])) {
                $term_name = trim($csv_row[$csv_col]);
                $term_id = cob_get_or_create_term_for_linking($term_name, $tax_slug, $lang);
                if($term_id) {
                    wp_set_object_terms($post_id, (int)$term_id, $tax_slug, false);
                }
            }
        }

        $gallery_ids = [];
        for ($i = 0; $i < $map['gallery_img_count']; $i++) {
            $img_url = trim($csv_row[$map['gallery_img_base'].'['.$i.']'] ?? '');
            if($img_url && filter_var($img_url, FILTER_VALIDATE_URL)) {
                $att_id = media_sideload_image($img_url, $post_id, $post_title, 'id');
                if(!is_wp_error($att_id)) {
                    $gallery_ids[] = $att_id;
                    if(function_exists('pll_set_post_language')) pll_set_post_language($att_id, $lang);
                }
            }
        }
        if(!empty($gallery_ids)) {
            if(!has_post_thumbnail($post_id)){
                set_post_thumbnail($post_id, $gallery_ids[0]);
                $log[] = "({$row_num}) &nbsp;&nbsp;&hookrightarrow; <span style='color:lightgreen;'>تم تعيين الصورة الأولى كصورة بارزة.</span>";
            }
            update_post_meta($post_id, '_property_gallery_ids', $gallery_ids);
            $log[] = "({$row_num}) &nbsp;&nbsp;&hookrightarrow; <span style='color:lightgreen;'>تم حفظ " . count($gallery_ids) . " صورة للمعرض.</span>";
        }
    }

    return ['status' => $result_status, 'log' => $log];
}


// 5. Helper function for linking, with language consistency fix.
if (!function_exists('cob_get_or_create_term_for_linking')) {
    function cob_get_or_create_term_for_linking($term_name, $taxonomy_slug, $language_code = null) {
        if (empty($term_name) || empty($taxonomy_slug)) { return null; }

        if ($language_code && function_exists('pll_get_term_language')) {
            $all_terms = get_terms(['taxonomy' => $taxonomy_slug, 'name' => $term_name, 'hide_empty' => false]);
            if (!is_wp_error($all_terms) && !empty($all_terms)) {
                foreach ($all_terms as $term) {
                    if (strcasecmp($term->name, $term_name) == 0 && pll_get_term_language($term->term_id) === $language_code) {
                        return $term->term_id;
                    }
                }
            }
        } else {
            $existing_term = term_exists($term_name, $taxonomy_slug);
            if ($existing_term) { return is_array($existing_term) ? $existing_term['term_id'] : $existing_term; }
        }

        $new_term = wp_insert_term($term_name, $taxonomy_slug, []);
        if (is_wp_error($new_term) || !isset($new_term['term_id'])) { return null; }

        $term_id = $new_term['term_id'];

        if ($term_id && $language_code && function_exists('pll_set_term_language')) {
            pll_set_term_language($term_id, $language_code);
        }
        return $term_id;
    }
}