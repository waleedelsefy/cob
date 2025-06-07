<?php
/**
 * AJAX WordPress Importer for 'compound' taxonomy from a CSV file.
 * Includes linking to 'developer' and 'city' taxonomies, image downloads,
 * and automatic translation linking for Polylang.
 * Version 4: Corrected logic for translation linking and improved logging.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// --- Configuration ---
$cob_compound_importer_config = [
    'taxonomy_slug' => 'compound',
    'target_language' => 'en',
    'csv_delimiter' => ',',
    'developer_meta_key' => 'compound_developer',
    'city_meta_key' => 'compound_city',
    'cover_image_meta_key' => '_compound_cover_image_id',
    'gallery_images_meta_key' => '_compound_gallery_ids',
    'source_id_meta_key' => '_source_id', // Meta key to store the original ID from CSV for linking translations.
    'developer_taxonomy_slug' => 'developer',
    'city_taxonomy_slug' => 'city',
    'batch_size' => 1,
    'ajax_timeout_seconds' => 300,
    'status_option_name' => 'cob_compound_taxonomy_importer_status',

    // Column mapping based on your CSV sample.
    'csv_column_map_en' => [
        'id' => 'id',
        'name' => 'meta_title_en',
        'slug' => 'all_slugs_en',
        'description' => 'meta_description_en',
        'parent_compound_id' => 'parent_compound_id',
        'developer_name_csv_col' => 'developer_name',
        'city_name_csv_col' => 'area_name',
        'cover_image_url_csv_col' => 'cover_image_url',
        'gallery_img_base_col' => 'compounds_img',
        'gallery_img_count' => 8,
    ],
    'csv_column_map_ar' => [
        'id' => 'id',
        'name' => 'name',
        'slug' => 'all_slugs_ar',
        'description' => 'meta_description_ar',
        'parent_compound_id' => 'parent_compound_id',
        'developer_name_csv_col' => 'developer_name',
        'city_name_csv_col' => 'area_name',
        'cover_image_url_csv_col' => 'cover_image_url',
        'gallery_img_base_col' => 'compounds_img',
        'gallery_img_count' => 8,
    ],
];

// 1. Register Admin Page & Enqueue Assets
add_action('admin_menu', 'cob_cti_register_page');
function cob_cti_register_page() {
    $hook = add_submenu_page(
        'tools.php',
        'استيراد تصنيفات الكمبوندات (AJAX)',
        'استيراد الكمبوندات (AJAX)',
        'manage_options',
        'cob-compound-taxonomy-importer-ajax',
        'cob_cti_render_page'
    );
    add_action("load-{$hook}", 'cob_cti_enqueue_assets');
}

function cob_cti_enqueue_assets() {
    global $cob_compound_importer_config;
    $js_path = get_stylesheet_directory_uri() . '/inc/importer/cob-compound-taxonomy-importer.js';

    wp_enqueue_script(
        'cob-cti-js',
        $js_path,
        ['jquery'],
        '1.3.0',
        true
    );

    wp_localize_script('cob-cti-js', 'cobCTIAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cob_cti_ajax_nonce'),
        'i18n' => [
            'confirm_new_import' => 'هل أنت متأكد أنك تريد بدء عملية استيراد جديدة؟',
            'confirm_resume' => 'هل تريد استئناف عملية الاستيراد السابقة؟',
            'confirm_cancel' => 'هل أنت متأكد من إلغاء العملية؟',
            'error_selecting_file' => 'يرجى اختيار ملف CSV.',
            'preparing_import' => 'يتم تحضير العملية...',
            'import_complete' => '🎉 اكتملت عملية الاستيراد بنجاح! 🎉',
            'connection_error' => '❌ فشل في الاتصال بالخادم',
            'processed' => 'تمت معالجة',
            'of' => 'من',
            'imported' => 'تم الاستيراد',
            'updated' => 'تم التحديث',
            'failed' => 'فشل',
        ]
    ]);
    wp_add_inline_style('wp-admin', "
        .cob-cti-progress-bar-container { border: 1px solid #ccc; padding: 2px; width: 100%; max-width: 600px; border-radius: 5px; background: #f1f1f1; margin-bottom:10px; }
        .cob-cti-progress-bar { background-color: #0073aa; height: 24px; width: 0%; text-align: center; line-height: 24px; color: white; border-radius: 3px; transition: width 0.3s ease-in-out; }
        #cob-cti-importer-log { background: #1e1e1e; color: #f1f1f1; border: 1px solid #e5e5e5; padding: 10px; margin-top: 15px; max-height: 400px; overflow-y: auto; font-family: monospace; white-space: pre-wrap; border-radius: 4px; }
    ");
}

// 2. Render Importer Page HTML
function cob_cti_render_page() {
    global $cob_compound_importer_config;
    $import_status = get_option($cob_compound_importer_config['status_option_name'], false);
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <p>استيراد وتحديث تصنيفات "الكمبوندات" مع ربط المطورين، المدن، وتنزيل الصور.</p>
        <div class="notice notice-warning">
            <p><strong>مهم جداً لتجنب أخطاء انتهاء المهلة (Timeout):</strong> تم ضبط حجم الدفعة (<code>batch_size</code>) إلى <strong><?php echo esc_html($cob_compound_importer_config['batch_size']); ?></strong> لمعالجة صف واحد فقط لكل طلب.</p>
        </div>

        <?php if ($import_status && isset($import_status['progress']) && $import_status['progress'] < 100 && !empty($import_status['original_filename'])) : ?>
            <div id="cob-cti-resume-notice" class="notice notice-warning is-dismissible">
                <p>يوجد عملية استيراد سابقة للملف (<code><?php echo esc_html($import_status['original_filename']); ?></code>) لم تكتمل (<?php echo esc_html($import_status['progress']); ?>%). يمكنك متابعتها أو إلغائها لبدء عملية جديدة.</p>
            </div>
        <?php endif; ?>

        <form id="cob-cti-importer-form" method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="compound_csv_file">اختر ملف CSV</label></th>
                    <td><input type="file" id="compound_csv_file" name="compound_csv_file" accept=".csv,text/csv"></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="target_language_selector">لغة الاستيراد</label></th>
                    <td>
                        <select id="target_language_selector" name="target_language_selector">
                            <option value="en" <?php selected($cob_compound_importer_config['target_language'], 'en'); ?>>English</option>
                            <option value="ar" <?php selected($cob_compound_importer_config['target_language'], 'ar'); ?>>العربية</option>
                        </select>
                    </td>
                </tr>
            </table>
            <button type="submit" id="cob-cti-start-new" class="button button-primary">بدء استيراد جديد</button>
            <button type="button" id="cob-cti-resume" class="button" style="<?php echo ($import_status && isset($import_status['progress']) && $import_status['progress'] < 100 && isset($import_status['total_rows']) && $import_status['total_rows'] > 0) ? '' : 'display:none;'; ?>">متابعة الاستيراد</button>
            <button type="button" id="cob-cti-cancel" class="button button-secondary" style="<?php echo $import_status ? '' : 'display:none;'; ?>">إلغاء وإعادة تعيين</button>
        </form>

        <div id="cob-cti-progress-container" style="display:none; margin-top: 20px;">
            <h3>تقدم العملية</h3>
            <div class="cob-cti-progress-bar-container"><div id="cob-cti-importer-progress-bar" class="cob-cti-progress-bar">0%</div></div>
            <p id="cob-cti-importer-stats"></p>
            <h4>سجل العمليات:</h4><div id="cob-cti-importer-log"></div>
        </div>
    </div>
    <?php
}

// 3. AJAX Handler
add_action('wp_ajax_cob_cti_ajax_handler', 'cob_cti_ajax_handler_callback');
function cob_cti_ajax_handler_callback() {
    global $cob_compound_importer_config;
    check_ajax_referer('cob_cti_ajax_nonce', 'nonce');

    if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'صلاحية غير كافية.']); }

    @set_time_limit($cob_compound_importer_config['ajax_timeout_seconds']);
    @ini_set('memory_limit', '512M');
    wp_raise_memory_limit('admin');

    if (!function_exists('media_sideload_image')) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }

    $importer_action = isset($_POST['importer_action']) ? sanitize_text_field($_POST['importer_action']) : '';
    $log_messages = [];

    switch ($importer_action) {
        case 'prepare':
            $old_status = get_option($cob_compound_importer_config['status_option_name']);
            if ($old_status && !empty($old_status['temp_file_path']) && file_exists($old_status['temp_file_path'])) {
                wp_delete_file($old_status['temp_file_path']);
            }
            delete_option($cob_compound_importer_config['status_option_name']);

            if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                wp_send_json_error(['message' => 'لم يتم رفع ملف أو حدث خطأ.']);
            }

            $move_file = wp_handle_upload($_FILES['csv_file'], ['test_form' => false, 'mimes' => ['csv' => 'text/csv']]);
            if (!$move_file || isset($move_file['error'])) {
                wp_send_json_error(['message' => 'خطأ في رفع الملف: ' . ($move_file['error'] ?? 'غير معروف')]);
            }

            $file_path = $move_file['file'];
            $total_rows = 0;
            $headers = [];
            if (($handle = fopen($file_path, "r")) !== FALSE) {
                $headers = array_map('trim', fgetcsv($handle, 0, $cob_compound_importer_config['csv_delimiter']));
                while (fgetcsv($handle, 0, $cob_compound_importer_config['csv_delimiter']) !== FALSE) {
                    $total_rows++;
                }
                fclose($handle);
            } else {
                wp_delete_file($file_path);
                wp_send_json_error(['message' => 'فشل في فتح الملف الذي تم رفعه.']);
            }

            $status = [
                'temp_file_path' => $file_path,
                'original_filename' => sanitize_file_name($_FILES['csv_file']['name']),
                'total_rows' => $total_rows,
                'processed_rows' => 0,
                'imported_count' => 0,
                'updated_count' => 0,
                'failed_count' => 0,
                'progress' => 0,
                'language' => isset($_POST['import_language']) ? sanitize_text_field($_POST['import_language']) : 'en',
                'csv_headers' => $headers,
            ];
            update_option($cob_compound_importer_config['status_option_name'], $status, 'no');
            wp_send_json_success(['status' => $status, 'log' => ["تم التحضير بنجاح. إجمالي الصفوف: {$total_rows}"]]);
            break;

        case 'run':
            $status = get_option($cob_compound_importer_config['status_option_name']);
            if (!$status || empty($status['temp_file_path']) || !file_exists($status['temp_file_path'])) {
                wp_send_json_error(['message' => 'لم يتم العثور على عملية استيراد صالحة.']);
            }

            if ($status['processed_rows'] >= $status['total_rows']) {
                wp_send_json_success(['status' => $status, 'log' => ["الاستيراد مكتمل بالفعل."], 'done' => true]);
            }

            $config_for_run = $cob_compound_importer_config;
            $config_for_run['target_language'] = $status['language'];

            if (($handle = fopen($status['temp_file_path'], "r")) !== FALSE) {
                fgetcsv($handle);
                for ($i = 0; $i < $status['processed_rows']; $i++) { if(fgetcsv($handle) === FALSE) break; }

                $raw_row_data = fgetcsv($handle, 0, $config_for_run['csv_delimiter']);
                if($raw_row_data !== FALSE) {
                    $status['processed_rows']++;

                    if (count($status['csv_headers']) !== count($raw_row_data)) {
                        $log_messages[] = "({$status['processed_rows']}) <span style='color:red;'>خطأ فادح: عدد الأعمدة لا يطابق الرأس. (وجد: " . count($raw_row_data) . ", متوقع: " . count($status['csv_headers']) . "). يرجى مراجعة هذا الصف في ملف CSV.</span>";
                        $status['failed_count']++;
                    } else {
                        $row_data_assoc = @array_combine($status['csv_headers'], $raw_row_data);
                        $import_result = cob_import_single_compound_ajax($row_data_assoc, $config_for_run, $status['processed_rows']);

                        if (isset($import_result['log'])) $log_messages = array_merge($log_messages, $import_result['log']);
                        if ($import_result['status'] === 'imported') $status['imported_count']++;
                        elseif ($import_result['status'] === 'updated') $status['updated_count']++;
                        else $status['failed_count']++;
                    }
                } else {
                    $status['processed_rows'] = $status['total_rows'];
                }
                fclose($handle);
            }

            $status['progress'] = ($status['total_rows'] > 0) ? round(($status['processed_rows'] / $status['total_rows']) * 100) : 100;
            $done = ($status['processed_rows'] >= $status['total_rows']);
            if ($done) {
                if (isset($status['temp_file_path']) && file_exists($status['temp_file_path'])) {
                    wp_delete_file($status['temp_file_path']);
                }
                $status['temp_file_path'] = null;
                $log_messages[] = "اكتمل الاستيراد. تم حذف الملف المؤقت.";
            }
            update_option($cob_compound_importer_config['status_option_name'], $status, 'no');
            wp_send_json_success(['status' => $status, 'log' => $log_messages, 'done' => $done]);
            break;

        case 'cancel':
            $status = get_option($cob_compound_importer_config['status_option_name']);
            if ($status && !empty($status['temp_file_path']) && file_exists($status['temp_file_path'])) {
                wp_delete_file($status['temp_file_path']);
            }
            delete_option($cob_compound_importer_config['status_option_name']);
            wp_send_json_success(['message' => 'تم إلغاء العملية ومسح الحالة بنجاح.']);
            break;

        case 'get_status':
            $status = get_option($cob_compound_importer_config['status_option_name']);
            if ($status && isset($status['progress']) && $status['progress'] < 100 && !empty($status['original_filename'])) {
                wp_send_json_success(['status' => $status]);
            } else {
                delete_option($cob_compound_importer_config['status_option_name']);
                wp_send_json_error(['message' => 'لا توجد عملية استيراد سابقة قابلة للاستئناف.']);
            }
            break;

        default:
            wp_send_json_error(['message' => 'إجراء غير معروف.']);
    }
}

// 4. Import Single Compound (Corrected Logic)
function cob_import_single_compound_ajax($csv_row_data_assoc, &$config, $current_row_number_for_log) {
    $log = [];
    $return_status_details = ['status' => 'failed', 'term_id' => null, 'log' => []];

    $taxonomy_slug = $config['taxonomy_slug'];
    $current_import_language = $config['target_language'];
    $csv_column_map = $config['csv_column_map_' . $current_import_language];
    $source_id_meta_key = $config['source_id_meta_key'];

    $source_id = trim($csv_row_data_assoc[$csv_column_map['id']] ?? '');
    if (empty($source_id)) {
        $log[] = "({$current_row_number_for_log}) <span style='color:red;'>خطأ: معرف المصدر ('{$csv_column_map['id']}') فارغ.</span>";
        return ['status' => 'failed', 'log' => $log];
    }

    $term_name = sanitize_text_field(trim($csv_row_data_assoc[$csv_column_map['name']] ?? ''));
    if (empty($term_name)) {
        $log[] = "({$current_row_number_for_log}) <span style='color:red;'>خطأ: اسم الكمبوند ('{$csv_column_map['name']}') فارغ.</span>";
        return ['status' => 'failed', 'log' => $log];
    }

    $term_slug = sanitize_title(trim($csv_row_data_assoc[$csv_column_map['slug']] ?? ''));
    if(empty($term_slug)) $term_slug = sanitize_title($term_name);

    $term_description = wp_kses_post($csv_row_data_assoc[$csv_column_map['description']] ?? '');

    $wp_term_id = null;
    $term_in_current_lang_found = false;

    if(function_exists('pll_get_term_language')) {
        $all_terms_with_source_id = get_terms(['taxonomy' => $taxonomy_slug, 'meta_key' => $source_id_meta_key, 'meta_value' => $source_id, 'hide_empty' => false]);
        if (!is_wp_error($all_terms_with_source_id) && !empty($all_terms_with_source_id)) {
            foreach ($all_terms_with_source_id as $term_object) {
                if (pll_get_term_language($term_object->term_id) === $current_import_language) {
                    $wp_term_id = $term_object->term_id;
                    $term_in_current_lang_found = true;
                    break;
                }
            }
        }
    }

    if ($term_in_current_lang_found) {
        wp_update_term($wp_term_id, $taxonomy_slug, ['name' => $term_name, 'slug' => $term_slug, 'description' => $term_description]);
        $log[] = "({$current_row_number_for_log}) <span style='color:#00A86B;'>تم تحديث '{$term_name}' (ID: {$wp_term_id}) للغة '{$current_import_language}'.</span>";
        $return_status_details['status'] = 'updated';
    } else {
        $insert_result = wp_insert_term($term_name, $taxonomy_slug, ['slug' => $term_slug, 'description' => $term_description]);
        if (is_wp_error($insert_result)) {
            $log[] = "({$current_row_number_for_log}) <span style='color:red;'>خطأ استيراد: " . esc_html($insert_result->get_error_message()) . "</span>";
            return ['status' => 'failed', 'log' => $log];
        }
        $wp_term_id = $insert_result['term_id'];
        $log[] = "({$current_row_number_for_log}) <span style='color:lightgreen;'>تم استيراد '{$term_name}' كـ ID جديد: {$wp_term_id} للغة '{$current_import_language}'.</span>";
        $return_status_details['status'] = 'imported';
    }

    if ($wp_term_id) {
        update_term_meta($wp_term_id, $source_id_meta_key, $source_id);
        if (function_exists('pll_set_term_language')) { pll_set_term_language($wp_term_id, $current_import_language); }

        if (function_exists('pll_save_term_translations') && function_exists('pll_get_term_language')) {
            $translations_to_save = [];
            $all_terms_for_linking = get_terms([
                'taxonomy' => $taxonomy_slug, 'meta_key' => $source_id_meta_key, 'meta_value' => $source_id, 'hide_empty' => false,
            ]);

            if(!is_wp_error($all_terms_for_linking) && count($all_terms_for_linking) > 1) {
                foreach($all_terms_for_linking as $term_object) {
                    $lang = pll_get_term_language($term_object->term_id);
                    if ($lang) {
                        $translations_to_save[$lang] = $term_object->term_id;
                    }
                }
                pll_save_term_translations($translations_to_save);
                $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&hookrightarrow; <span style='color:cyan;'>تم ربط الترجمات بنجاح.</span>";
            }
        }

        $cover_image_url = trim($csv_row_data_assoc[$csv_column_map['cover_image_url_csv_col']] ?? '');
        if ($cover_image_url && filter_var($cover_image_url, FILTER_VALIDATE_URL)) {
            $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&hookrightarrow; جاري تحميل صورة الغلاف...";
            $attachment_id = media_sideload_image($cover_image_url, 0, $term_name, 'id');
            if (!is_wp_error($attachment_id)) {
                update_term_meta($wp_term_id, $config['cover_image_meta_key'], $attachment_id);
                if(function_exists('pll_set_post_language')) { pll_set_post_language($attachment_id, $current_import_language); }
                $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&hookrightarrow; <span style='color:lightgreen;'>نجح تحميل صورة الغلاف (ID: {$attachment_id}).</span>";
            } else {
                $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&hookrightarrow; <span style='color:orange;'>فشل تحميل صورة الغلاف: " . esc_html($attachment_id->get_error_message()) . "</span>";
            }
        }
    }

    $return_status_details['term_id'] = $wp_term_id;
    $return_status_details['log'] = $log;
    return $return_status_details;
}

// 5. Helper function for linking, with language consistency fix.
if (!function_exists('cob_get_or_create_term_for_linking')) {
    function cob_get_or_create_term_for_linking($term_name, $taxonomy_slug, $language_code = null) {
        if (empty($term_name) || empty($taxonomy_slug)) { return null; }

        if ($language_code && function_exists('pll_get_term_language')) {
            $all_terms = get_terms(['taxonomy' => $taxonomy_slug, 'name' => $term_name, 'hide_empty' => false]);
            if (!is_wp_error($all_terms)) {
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
