<?php
/**
 * AJAX WordPress Importer for 'compound' taxonomy from a CSV file.
 * Includes linking to 'developer' and 'city' taxonomies, image downloads,
 * and automatic translation linking for Polylang.
 * Version 3: Corrected the logic for finding/updating terms to ensure translations are not overwritten.
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
        'استيراد تصنيفات الكمبوندات (AJAX)',
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
        '1.2.0', // Version bump for the fix
        true
    );
    wp_localize_script('cob-cti-js', 'cobCTIAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cob_cti_ajax_nonce'),
        'status_option_name' => $cob_compound_importer_config['status_option_name'],
        'i18n' => [
            'confirm_new_import' => 'هل أنت متأكد أنك تريد بدء عملية استيراد جديدة؟ سيتم حذف أي تقدم لعملية سابقة وملف مؤقت إن وجد.',
            'confirm_resume' => 'سيتم متابعة عملية الاستيراد من النقطة التي توقفت عندها. هل أنت متأكد؟',
            'confirm_cancel' => 'هل أنت متأكد أنك تريد إلغاء العملية ومسح التقدم والملف المؤقت؟',
            'error_selecting_file' => 'يرجى اختيار ملف CSV لرفعه.',
            'preparing_import' => 'يتم تحضير العملية...',
            'import_complete' => '🎉 اكتملت عملية الاستيراد بنجاح! 🎉',
            'connection_error' => '❌ فشل في الاتصال بالخادم',
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
            <p><strong>مهم جداً:</strong> تم ضبط حجم الدفعة إلى <strong><?php echo esc_html($cob_compound_importer_config['batch_size']); ?></strong> لمعالجة صف واحد فقط لكل طلب، وذلك لتجنب أخطاء انتهاء المهلة.</p>
        </div>

        <?php if ($import_status && isset($import_status['progress']) && $import_status['progress'] < 100 && !empty($import_status['original_filename'])) : ?>
            <div id="cob-cti-resume-notice" class="notice notice-warning is-dismissible">
                <p>يوجد عملية استيراد سابقة للملف (<code><?php echo esc_html($import_status['original_filename']); ?></code>) لم تكتمل (<?php echo esc_html($import_status['progress']); ?>%). يمكنك متابعتها أو إلغائها.</p>
            </div>
        <?php endif; ?>

        <form id="cob-cti-importer-form" method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="compound_csv_file">اختر ملف CSV</label></th>
                    <td><input type="file" id="compound_csv_file" name="compound_csv_file" accept=".csv,text/csv"></td>
                </tr>
                <tr>
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
            <button type="button" id="cob-cti-resume" class="button" style="<?php echo ($import_status && $import_status['progress'] < 100) ? '' : 'display:none;'; ?>">متابعة الاستيراد</button>
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

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'صلاحية غير كافية.']);
    }

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
            if ($old_status && isset($old_status['temp_file_path']) && file_exists($old_status['temp_file_path'])) {
                wp_delete_file($old_status['temp_file_path']);
            }
            delete_option($cob_compound_importer_config['status_option_name']);

            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                wp_send_json_error(['message' => 'لم يتم رفع ملف أو حدث خطأ. رمز: ' . ($_FILES['csv_file']['error'] ?? 'N/A')]);
            }

            $uploaded_file = $_FILES['csv_file'];
            $original_filename = sanitize_file_name($uploaded_file['name']);
            $upload_overrides = ['test_form' => false, 'mimes' => ['csv' => 'text/csv', 'txt' => 'text/plain']];
            $move_file = wp_handle_upload($uploaded_file, $upload_overrides);

            if (!$move_file || isset($move_file['error'])) {
                wp_send_json_error(['message' => 'خطأ في رفع الملف: ' . ($move_file['error'] ?? 'غير معروف')]);
            }

            $file_path = $move_file['file'];
            $total_rows = 0;
            $headers = [];

            if (($handle = fopen($file_path, "r")) !== FALSE) {
                $header_row_data = fgetcsv($handle, 0, $cob_compound_importer_config['csv_delimiter']);
                if ($header_row_data !== FALSE) {
                    $headers = array_map('trim', $header_row_data);
                } else {
                    if (file_exists($file_path)) wp_delete_file($file_path);
                    wp_send_json_error(['message' => 'فشل في قراءة رأس ملف CSV.']);
                }
                while (fgetcsv($handle, 0, $cob_compound_importer_config['csv_delimiter']) !== FALSE) {
                    $total_rows++;
                }
                fclose($handle);
            } else {
                if (file_exists($file_path)) wp_delete_file($file_path);
                wp_send_json_error(['message' => 'فشل في فتح الملف لحساب الصفوف.']);
            }

            $selected_lang = isset($_POST['import_language']) ? sanitize_text_field($_POST['import_language']) : $cob_compound_importer_config['target_language'];

            $status = [
                'temp_file_path' => $file_path,
                'original_filename' => $original_filename,
                'total_rows' => $total_rows,
                'processed_rows' => 0,
                'imported_count' => 0,
                'updated_count' => 0,
                'failed_count' => 0,
                'progress' => 0,
                'language' => $selected_lang,
                'csv_headers' => $headers,
            ];
            update_option($cob_compound_importer_config['status_option_name'], $status, 'no');

            wp_send_json_success(['message' => 'تم التحضير بنجاح.', 'status' => $status, 'log' => $log_messages]);
            break;

        case 'run':
            $status = get_option($cob_compound_importer_config['status_option_name']);
            if (!$status || empty($status['temp_file_path']) || !file_exists($status['temp_file_path'])) {
                wp_send_json_error(['message' => 'لم يتم العثور على عملية استيراد صالحة.']);
            }

            if ($status['processed_rows'] >= $status['total_rows']) {
                wp_send_json_success(['status' => $status, 'log' => ["الاستيراد مكتمل بالفعل."], 'done' => true]);
            }

            $file_path = $status['temp_file_path'];
            $csv_headers = $status['csv_headers'];

            $config_for_run = $cob_compound_importer_config;
            $config_for_run['target_language'] = $status['language'];

            if (($handle = fopen($file_path, "r")) !== FALSE) {
                fgetcsv($handle);
                for ($i = 0; $i < $status['processed_rows']; $i++) {
                    if(fgetcsv($handle) === FALSE) break;
                }

                $raw_row_data = fgetcsv($handle, 0, $config_for_run['csv_delimiter']);
                if($raw_row_data !== FALSE) {
                    $status['processed_rows']++;

                    if (count($csv_headers) === count($raw_row_data)) {
                        $row_data_assoc = @array_combine($csv_headers, $raw_row_data);
                        $import_result = cob_import_single_compound_ajax($row_data_assoc, $config_for_run, $status['processed_rows']);

                        if (isset($import_result['log'])) $log_messages = array_merge($log_messages, $import_result['log']);
                        if ($import_result['status'] === 'imported') $status['imported_count']++;
                        elseif ($import_result['status'] === 'updated') $status['updated_count']++;
                        else $status['failed_count']++;

                    } else {
                        $log_messages[] = "({$status['processed_rows']}) خطأ: عدد الأعمدة لا يطابق الرأس. تخطي.";
                        $status['failed_count']++;
                    }
                } else {
                    $status['processed_rows'] = $status['total_rows'];
                }
                fclose($handle);
            }

            $status['progress'] = ($status['total_rows'] > 0) ? round(($status['processed_rows'] / $status['total_rows']) * 100) : 100;
            $done = ($status['processed_rows'] >= $status['total_rows']);
            if ($done) {
                wp_delete_file($status['temp_file_path']);
                $status['temp_file_path'] = null;
                $log_messages[] = "اكتمل الاستيراد. تم حذف الملف المؤقت.";
            }
            update_option($cob_compound_importer_config['status_option_name'], $status, 'no');
            wp_send_json_success(['status' => $status, 'log' => $log_messages, 'done' => $done]);
            break;

        case 'cancel':
        case 'get_status':
            $status = get_option($cob_compound_importer_config['status_option_name']);
            if($importer_action === 'cancel') {
                if ($status && !empty($status['temp_file_path']) && file_exists($status['temp_file_path'])) {
                    wp_delete_file($status['temp_file_path']);
                }
                delete_option($cob_compound_importer_config['status_option_name']);
                wp_send_json_success(['message' => 'تم إلغاء العملية ومسح الحالة بنجاح.']);
            } else {
                if ($status && $status['progress'] < 100 && !empty($status['original_filename'])) {
                    wp_send_json_success(['status' => $status]);
                } else {
                    delete_option($cob_compound_importer_config['status_option_name']);
                    wp_send_json_error(['message' => 'لا توجد عملية استيراد سابقة قابلة للاستئناف.']);
                }
            }
            break;
        default:
            wp_send_json_error(['message' => 'إجراء غير معروف.']);
    }
}


// 4. Import Single Compound (adapted for AJAX context)
function cob_import_single_compound_ajax($csv_row_data_assoc, &$config, $current_row_number_for_log) {
    $log = [];
    $return_status_details = ['status' => 'failed', 'term_id' => null, 'log' => []];

    // Extract variables from config
    $taxonomy_slug = $config['taxonomy_slug'];
    $current_import_language = $config['target_language'];
    $csv_column_map = $config['csv_column_map_' . $current_import_language];
    $source_id_meta_key = $config['source_id_meta_key'];

    // Extract data from CSV row using the map
    $source_id = trim($csv_row_data_assoc[$csv_column_map['id']] ?? '');
    if (empty($source_id)) {
        $log[] = "({$current_row_number_for_log}) <span style='color:red;'>خطأ: معرف المصدر فارغ. تخطي الصف.</span>";
        $return_status_details['log'] = $log;
        return $return_status_details;
    }

    $term_name = sanitize_text_field(trim($csv_row_data_assoc[$csv_column_map['name']] ?? ''));
    if (empty($term_name)) {
        $log[] = "({$current_row_number_for_log}) <span style='color:red;'>خطأ: اسم الكمبوند ('{$csv_column_map['name']}') فارغ. تخطي الصف.</span>";
        $return_status_details['log'] = $log;
        return $return_status_details;
    }

    $term_slug = sanitize_title(trim($csv_row_data_assoc[$csv_column_map['slug']] ?? ''));
    if(empty($term_slug)) $term_slug = sanitize_title($term_name);

    $term_description = wp_kses_post($csv_row_data_assoc[$csv_column_map['description']] ?? '');

    // --- CORRECTED LOGIC: Find or create the term with language awareness ---
    $wp_term_id = null;
    $term_in_current_lang_found = false;

    // First, get all terms with this source ID
    if(function_exists('pll_get_term_language')) {
        $all_terms_with_source_id = get_terms([
            'taxonomy' => $taxonomy_slug,
            'meta_key' => $source_id_meta_key,
            'meta_value' => $source_id,
            'hide_empty' => false,
        ]);

        if (!is_wp_error($all_terms_with_source_id) && !empty($all_terms_with_source_id)) {
            // Loop through them to find if one already exists for the current language
            foreach ($all_terms_with_source_id as $term_object) {
                if (pll_get_term_language($term_object->term_id) === $current_import_language) {
                    $wp_term_id = $term_object->term_id;
                    $term_in_current_lang_found = true;
                    break;
                }
            }
        }
    }

    // If a term in the current language was found, update it.
    if ($term_in_current_lang_found) {
        wp_update_term($wp_term_id, $taxonomy_slug, ['name' => $term_name, 'slug' => $term_slug, 'description' => $term_description]);
        $log[] = "({$current_row_number_for_log}) <span style='color:#00A86B;'>تم تحديث '{$term_name}' (ID: {$wp_term_id}) للغة '{$current_import_language}'.</span>";
        $return_status_details['status'] = 'updated';
    } else {
        // Otherwise, create a new term for this language
        $insert_result = wp_insert_term($term_name, $taxonomy_slug, ['slug' => $term_slug, 'description' => $term_description]);
        if (is_wp_error($insert_result)) {
            $log[] = "({$current_row_number_for_log}) <span style='color:red;'>خطأ استيراد '{$term_name}': " . esc_html($insert_result->get_error_message()) . "</span>";
            $return_status_details['log'] = $log;
            return $return_status_details;
        }
        $wp_term_id = $insert_result['term_id'];
        $log[] = "({$current_row_number_for_log}) <span style='color:lightgreen;'>تم استيراد '{$term_name}' كـ ID جديد: {$wp_term_id} للغة '{$current_import_language}'.</span>";
        $return_status_details['status'] = 'imported';
    }


    $return_status_details['term_id'] = $wp_term_id;

    if ($wp_term_id) {
        // Save source ID for linking
        update_term_meta($wp_term_id, $source_id_meta_key, $source_id);

        // Set term language
        if (function_exists('pll_set_term_language')) {
            pll_set_term_language($wp_term_id, $current_import_language);
        }

        // Link translations
        if (function_exists('pll_save_term_translations') && function_exists('pll_get_term_language')) {
            $translations_to_save = [];
            $all_terms_with_source_id_for_linking = get_terms([
                'taxonomy' => $taxonomy_slug,
                'meta_key' => $source_id_meta_key,
                'meta_value' => $source_id,
                'hide_empty' => false,
            ]);

            if(!is_wp_error($all_terms_with_source_id_for_linking) && count($all_terms_with_source_id_for_linking) > 1) {
                foreach($all_terms_with_source_id_for_linking as $term_object) {
                    $lang = pll_get_term_language($term_object->term_id);
                    if ($lang) {
                        $translations_to_save[$lang] = $term_object->term_id;
                    }
                }
                pll_save_term_translations($translations_to_save);
                $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&hookrightarrow; <span style='color:cyan;'>تم ربط الترجمات بنجاح.</span>";
            }
        }

        $developer_meta_key = $config['developer_meta_key'];
        $city_meta_key = $config['city_meta_key'];
        $developer_taxonomy_slug = $config['developer_taxonomy_slug'];
        $city_taxonomy_slug = $config['city_taxonomy_slug'];

        $developer_name_val = trim($csv_row_data_assoc[$csv_column_map['developer_name_csv_col']] ?? '');
        if ($developer_name_val) {
            $dev_term_id = cob_get_or_create_term_for_linking($developer_name_val, $developer_taxonomy_slug, $current_import_language);
            if ($dev_term_id) {
                update_term_meta($wp_term_id, $developer_meta_key, $dev_term_id);
            }
        }

        $city_name_val = trim($csv_row_data_assoc[$csv_column_map['city_name_csv_col']] ?? '');
        if ($city_name_val) {
            $city_term_id = cob_get_or_create_term_for_linking($city_name_val, $city_taxonomy_slug, $current_import_language);
            if ($city_term_id) {
                update_term_meta($wp_term_id, $city_meta_key, $city_term_id);
            }
        }

        // ... Image handling logic can be added here ...
    }

    return $return_status_details;
}

// 5. Helper function for linking, with language consistency fix.
if (!function_exists('cob_get_or_create_term_for_linking')) {
    function cob_get_or_create_term_for_linking($term_name, $taxonomy_slug, $language_code = null) {
        if (empty($term_name) || empty($taxonomy_slug)) {
            return null;
        }

        // If Polylang is active, find a term with the exact name AND language.
        if ($language_code && function_exists('pll_get_term_language')) {
            $all_terms = get_terms([
                'taxonomy' => $taxonomy_slug,
                'hide_empty' => false,
                'name' => $term_name,
            ]);

            if (!is_wp_error($all_terms)) {
                foreach ($all_terms as $term) {
                    if (strcasecmp($term->name, $term_name) == 0 && pll_get_term_language($term->term_id) === $language_code) {
                        return $term->term_id; // Found exact match in the right language.
                    }
                }
            }
        } else {
            // Fallback for when Polylang is not active.
            $existing_term = term_exists($term_name, $taxonomy_slug);
            if ($existing_term) {
                return is_array($existing_term) ? $existing_term['term_id'] : $existing_term;
            }
        }

        // If no term was found with the correct language, create a new one.
        $new_term = wp_insert_term($term_name, $taxonomy_slug, []);
        if (is_wp_error($new_term) || !isset($new_term['term_id'])) {
            return null; // Failed to create.
        }

        $term_id = $new_term['term_id'];

        // Set the language for the newly created term.
        if ($term_id && $language_code && function_exists('pll_set_term_language')) {
            pll_set_term_language($term_id, $language_code);
        }

        return $term_id;
    }
}
