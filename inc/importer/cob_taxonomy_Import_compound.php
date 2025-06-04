<?php
/**
 * AJAX WordPress Importer for 'compound' taxonomy from a CSV file.
 * Includes linking to 'developer' and 'city' taxonomies, image downloads.
 *
 * Instructions:
 * 1. Place this code in your theme's functions.php or a custom plugin.
 * 2. Create a JS file (e.g., your-theme/js/cob-compound-taxonomy-importer.js) with the provided JS code.
 * 3. Update JS_PATH in cob_compound_importer_enqueue_assets() to the correct path of your JS file.
 * 4. Ensure 'developer' and 'city' taxonomies are registered.
 * 5. Access via "Tools" > "استيراد تصنيفات المركبات (AJAX)".
 * 6. Verify CSV column names and taxonomy slugs in $cob_compound_importer_config.
 * 7. Backup your database before any import.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// --- Configuration ---
$cob_compound_importer_config = [
    'taxonomy_slug' => 'compound',
    'target_language' => 'en', // Default, will be overridden by form selection
    'csv_delimiter' => ',',
    'developer_meta_key' => 'compound_developer',
    'city_meta_key' => 'compound_city',
    'cover_image_meta_key' => '_compound_cover_image_id',
    'gallery_images_meta_key' => '_compound_gallery_ids',
    'developer_taxonomy_slug' => 'developer',
    'city_taxonomy_slug' => 'city',
    'batch_size' => 5, // Number of rows to process per AJAX request
    'status_option_name' => 'cob_compound_taxonomy_importer_status', // WP option to store import status

    'csv_column_map_en' => [
        'id' => 'id', 'name' => 'name', 'slug' => 'slug', 'description' => 'description',
        'parent_compound_id' => 'parent_compound_id',
        'developer_name_csv_col' => 'developer_name', 'city_name_csv_col' => 'area_name',
        'cover_image_url_csv_col' => 'cover_image_url',
        'gallery_img_base_col' => 'compounds_img', 'gallery_img_count' => 8,
    ],
    'csv_column_map_ar' => [
        'id' => 'id', 'name' => 'name_ar', 'slug' => 'slug_ar', 'description' => 'meta_description_ar',
        'parent_compound_id' => 'parent_compound_id',
        'developer_name_csv_col' => 'developer_name', 'city_name_csv_col' => 'area_name',
        'cover_image_url_csv_col' => 'cover_image_url',
        'gallery_img_base_col' => 'compounds_img', 'gallery_img_count' => 8,
    ],
];

// 1. Register Admin Page & Enqueue Assets
add_action('admin_menu', 'cob_cti_register_page'); // cti: Compound Taxonomy Importer
function cob_cti_register_page() {
    $hook = add_submenu_page(
        'tools.php',
        'استيراد تصنيفات المركبات (AJAX)',
        'استيراد تصنيفات المركبات (AJAX)',
        'manage_options',
        'cob-compound-taxonomy-importer-ajax',
        'cob_cti_render_page'
    );
    add_action("load-{$hook}", 'cob_cti_enqueue_assets');
}

function cob_cti_enqueue_assets() {
    global $cob_compound_importer_config;
    // **IMPORTANT**: Update this path to where you save the JS file.
    // **هام**: قم بتحديث هذا المسار إلى المكان الذي ستحفظ فيه ملف الجافاسكريبت.
    $js_path = get_stylesheet_directory_uri() . '/inc/importer/cob-compound-taxonomy-importer.js'; // Example path

    wp_enqueue_script(
        'cob-cti-js',
        $js_path,
        ['jquery'],
        '1.0.0', // Version of your script
        true
    );
    wp_localize_script('cob-cti-js', 'cobCTIAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cob_cti_ajax_nonce'),
        'status_option_name' => $cob_compound_importer_config['status_option_name'],
        'i18n' => [ // Internationalization strings for JS
            'confirm_new_import' => 'هل أنت متأكد أنك تريد بدء عملية استيراد جديدة؟ سيتم حذف أي تقدم لعملية سابقة وملف مؤقت إن وجد.',
            'confirm_resume' => 'سيتم متابعة عملية الاستيراد من النقطة التي توقفت عندها. هل أنت متأكد؟',
            'confirm_cancel' => 'هل أنت متأكد أنك تريد إلغاء العملية ومسح التقدم والملف المؤقت؟',
            'error_selecting_file' => 'يرجى اختيار ملف CSV لرفعه أو من القائمة.',
            'preparing_import' => 'يتم تحضير العملية...',
            'import_complete' => '🎉 اكتملت عملية الاستيراد بنجاح! 🎉',
            'error_unknown_prepare' => 'حدث خطأ غير معروف أثناء التحضير.',
            'error_unknown_processing' => 'حدث خطأ غير معروف أثناء المعالجة.',
            'connection_error' => '❌ فشل في الاتصال بالخادم',
            'resuming_import' => 'جاري متابعة عملية الاستيراد السابقة...',
            'processed_of' => 'تم معالجة',
            'from' => 'من',
            'skipped' => 'تم تخطي',
            'for_language' => 'للغة:',
            'processed_rows_error' => 'تحذير: تم الوصول لنهاية الملف قبل إكمال كل الصفوف المتوقعة.',
            'import_cancelled_successfully' => 'تم إلغاء العملية ومسح الملف المؤقت بنجاح.',
            'error_cancelling' => 'حدث خطأ أثناء الإلغاء.',
            'error_connecting_cancel' => 'خطأ في الاتصال أثناء محاولة الإلغاء.',
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
        <p>استيراد وتحديث تصنيفات "المركبات" مع ربط المطورين، المدن، وتنزيل الصور.</p>

        <?php if ($import_status && isset($import_status['progress']) && $import_status['progress'] < 100 && $import_status['progress'] >= 0 && !empty($import_status['original_filename'])) : ?>
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

    // Ensure media handling functions are available for 'run' action
    if (!function_exists('media_sideload_image')) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
    }
    @ini_set('memory_limit', '512M');
    wp_raise_memory_limit('admin');


    $importer_action = isset($_POST['importer_action']) ? sanitize_text_field($_POST['importer_action']) : '';
    $log_messages = [];

    switch ($importer_action) {
        case 'prepare':
            // Clean up any previous import status/file
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

            $log_messages[] = "تم رفع الملف بنجاح: " . esc_html($original_filename) . " إلى " . esc_html($move_file['file']);
            $file_path = $move_file['file'];
            $total_rows = 0;
            $headers = [];

            if (($handle = fopen($file_path, "r")) !== FALSE) {
                $header_row_data = fgetcsv($handle, 0, $cob_compound_importer_config['csv_delimiter']);
                if ($header_row_data !== FALSE) {
                    $headers = array_map('trim', $header_row_data);
                    $log_messages[] = "رأس CSV (الأعمدة): " . implode(' | ', $headers);
                } else {
                    wp_delete_file($file_path);
                    wp_send_json_error(['message' => 'فشل في قراءة رأس ملف CSV.']);
                }
                while (fgetcsv($handle, 0, $cob_compound_importer_config['csv_delimiter']) !== FALSE) {
                    $total_rows++;
                }
                fclose($handle);
            } else {
                wp_delete_file($file_path); // Clean up if open failed
                wp_send_json_error(['message' => 'فشل في فتح الملف لحساب الصفوف.']);
            }

            $selected_lang = isset($_POST['import_language']) ? sanitize_text_field($_POST['import_language']) : $cob_compound_importer_config['target_language'];
            $current_column_map_key = 'csv_column_map_' . $selected_lang;
            $current_csv_column_map = $cob_compound_importer_config[$current_column_map_key] ?? $cob_compound_importer_config['csv_column_map_en'];

            // Validate headers against selected column map
            foreach ($current_csv_column_map as $internal_key => $csv_col_name_mapped) {
                if (!in_array($csv_col_name_mapped, $headers)) {
                    $optional_cols_keys = ['parent_compound_id', 'slug', 'description']; // Add more if needed
                    if (in_array($internal_key, $optional_cols_keys, true) || strpos($internal_key, '_url_csv_col') !== false || strpos($internal_key, '_name_csv_col') !== false) {
                        $log_messages[] = "<span style='color:orange;'>تحذير: عمود CSV المتوقع '{$csv_col_name_mapped}' (لـ {$internal_key}) غير موجود في رأس الملف. قد لا يتم استيراد هذه البيانات.</span>";
                    } else if ($internal_key === 'id' || $internal_key === 'name') { // Essential
                        wp_delete_file($file_path);
                        wp_send_json_error(['message' => "خطأ: عمود CSV الإلزامي '{$csv_col_name_mapped}' (لـ {$internal_key}) غير موجود. تحقق من الإعدادات وملف CSV.", 'log' => $log_messages]);
                    }
                }
            }


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
                'csv_headers' => $headers, // Store headers for use in 'run'
                'source_to_wp_term_id_map' => [], // To track parent IDs during import
                'processed_source_ids_recursion_check' => [], // For recursion check within a batch if needed
            ];
            update_option($cob_compound_importer_config['status_option_name'], $status, 'no'); // 'no' for autoload

            $log_messages[] = "الملف جاهز للمعالجة. إجمالي الصفوف (باستثناء الرأس): " . $total_rows;
            wp_send_json_success(['message' => 'تم التحضير بنجاح.', 'status' => $status, 'log' => $log_messages]);
            break;

        case 'run':
            $status = get_option($cob_compound_importer_config['status_option_name']);
            if (!$status || empty($status['temp_file_path']) || !file_exists($status['temp_file_path'])) {
                wp_send_json_error(['message' => 'لم يتم العثور على عملية استيراد صالحة للبدء أو الملف المؤقت مفقود. يرجى البدء من جديد.']);
            }

            if ($status['processed_rows'] >= $status['total_rows']) {
                wp_send_json_success(['status' => $status, 'log' => ["الاستيراد مكتمل بالفعل."], 'done' => true]);
            }

            $file_path = $status['temp_file_path'];
            $csv_headers = $status['csv_headers'];
            $processed_in_this_batch = 0;

            $current_config_for_import_func = [
                'taxonomy_slug'           => $cob_compound_importer_config['taxonomy_slug'],
                'target_language'         => $status['language'],
                'csv_delimiter'           => $cob_compound_importer_config['csv_delimiter'],
                'developer_meta_key'      => $cob_compound_importer_config['developer_meta_key'],
                'city_meta_key'           => $cob_compound_importer_config['city_meta_key'],
                'cover_image_meta_key'    => $cob_compound_importer_config['cover_image_meta_key'],
                'gallery_images_meta_key' => $cob_compound_importer_config['gallery_images_meta_key'],
                'developer_taxonomy_slug' => $cob_compound_importer_config['developer_taxonomy_slug'],
                'city_taxonomy_slug'      => $cob_compound_importer_config['city_taxonomy_slug'],
                'csv_column_map'          => $cob_compound_importer_config['csv_column_map_' . $status['language']] ?? $cob_compound_importer_config['csv_column_map_en'],
                // Pass maps for parent lookups
                'source_to_wp_term_id_map_global' => &$status['source_to_wp_term_id_map'], // Pass by reference
                'processed_source_ids_recursion_check_global' => &$status['processed_source_ids_recursion_check'], // Pass by reference
            ];


            if (($handle = fopen($file_path, "r")) !== FALSE) {
                // Skip header
                fgetcsv($handle, 0, $cob_compound_importer_config['csv_delimiter']);
                // Skip already processed rows
                for ($i = 0; $i < $status['processed_rows']; $i++) {
                    if (fgetcsv($handle, 0, $cob_compound_importer_config['csv_delimiter']) === FALSE) {
                        // Reached EOF unexpectedly
                        $log_messages[] = "($status[processed_rows]) تحذير: تم الوصول لنهاية الملف قبل إكمال كل الصفوف المتوقعة.";
                        $status['processed_rows'] = $status['total_rows']; // Mark as done
                        break;
                    }
                }

                while ($processed_in_this_batch < $cob_compound_importer_config['batch_size'] && $status['processed_rows'] < $status['total_rows']) {
                    $raw_row_data = fgetcsv($handle, 0, $cob_compound_importer_config['csv_delimiter']);
                    if ($raw_row_data === FALSE) { // End of file
                        $log_messages[] = "تم الوصول إلى نهاية الملف.";
                        $status['processed_rows'] = $status['total_rows']; // Mark as done
                        break;
                    }
                    $status['processed_rows']++; // Increment before processing the actual row number
                    $current_row_number_for_log = $status['processed_rows'];


                    if (count($csv_headers) !== count($raw_row_data)) {
                        $log_messages[] = "({$current_row_number_for_log}) خطأ: عدد الأعمدة (" . count($raw_row_data) . ") لا يطابق الرأس (" . count($csv_headers) . "). تخطي الصف.";
                        $status['failed_count']++;
                        $processed_in_this_batch++;
                        continue;
                    }
                    $row_data_assoc = @array_combine($csv_headers, $raw_row_data);
                    if ($row_data_assoc === false) {
                        $log_messages[] = "({$current_row_number_for_log}) خطأ: فشل في دمج العناوين مع البيانات. تخطي الصف.";
                        $status['failed_count']++;
                        $processed_in_this_batch++;
                        continue;
                    }

                    // Call the modified import function
                    $import_result = cob_import_single_compound_ajax($row_data_assoc, $current_config_for_import_func, $current_row_number_for_log);

                    if (isset($import_result['log'])) $log_messages = array_merge($log_messages, $import_result['log']);

                    if ($import_result['status'] === 'imported') $status['imported_count']++;
                    elseif ($import_result['status'] === 'updated') $status['updated_count']++;
                    elseif ($import_result['status'] === 'failed') $status['failed_count']++;
                    // 'skipped_as_parent' is handled internally by the map

                    $processed_in_this_batch++;
                }
                fclose($handle);
            } else {
                wp_send_json_error(['message' => 'فشل في إعادة فتح الملف المؤقت للمعالجة.']);
            }

            $status['progress'] = ($status['total_rows'] > 0) ? round(($status['processed_rows'] / $status['total_rows']) * 100) : 100;
            update_option($cob_compound_importer_config['status_option_name'], $status, 'no');

            $done = ($status['processed_rows'] >= $status['total_rows']);
            wp_send_json_success(['status' => $status, 'log' => $log_messages, 'done' => $done]);
            break;

        case 'cancel':
            $status = get_option($cob_compound_importer_config['status_option_name']);
            if ($status && isset($status['temp_file_path']) && file_exists($status['temp_file_path'])) {
                wp_delete_file($status['temp_file_path']);
            }
            delete_option($cob_compound_importer_config['status_option_name']);
            wp_send_json_success(['message' => 'تم إلغاء العملية ومسح الحالة بنجاح.']);
            break;

        case 'get_status': // For resuming
            $status = get_option($cob_compound_importer_config['status_option_name']);
            if ($status && isset($status['progress']) && $status['progress'] < 100 && !empty($status['original_filename'])) {
                wp_send_json_success(['status' => $status, 'log' => ["تم استرجاع الحالة السابقة للملف: " . $status['original_filename']]]);
            } else {
                wp_send_json_error(['message' => 'لا توجد عملية استيراد سابقة قابلة للاستئناف.']);
            }
            break;

        default:
            wp_send_json_error(['message' => 'إجراء غير معروف.']);
    }
}


// 4. Import Single Compound (adapted for AJAX context)
// This function now takes associative row data and the config.
// It uses references to global-like status arrays for parent tracking.
function cob_import_single_compound_ajax($csv_row_data_assoc, $config, $current_row_number_for_log) {
    // Extract from config
    $taxonomy_slug = $config['taxonomy_slug'];
    $developer_meta_key = $config['developer_meta_key'];
    $city_meta_key = $config['city_meta_key'];
    $cover_image_meta_key = $config['cover_image_meta_key'];
    $gallery_images_meta_key = $config['gallery_images_meta_key'];
    $developer_taxonomy_slug = $config['developer_taxonomy_slug'];
    $city_taxonomy_slug = $config['city_taxonomy_slug'];
    $current_import_language = $config['target_language'];
    $csv_column_map = $config['csv_column_map']; // Language-specific map

    // References to status arrays for parent tracking
    $source_to_wp_term_id_map = &$config['source_to_wp_term_id_map_global'];
    $processed_source_ids_recursion_check = &$config['processed_source_ids_recursion_check_global'];

    $log = [];
    $return_status_details = ['status' => 'failed', 'term_id' => null, 'log' => []];

    // Extract data from $csv_row_data_assoc using $csv_column_map
    $source_id = $csv_row_data_assoc[$csv_column_map['id']] ?? null;
    if (empty($source_id)) {
        $log[] = "({$current_row_number_for_log}) خطأ: معرف المصدر ('{$csv_column_map['id']}') فارغ. تخطي الصف.";
        $return_status_details['log'] = $log;
        return $return_status_details;
    }

    // Check if already processed (e.g. as a parent in a previous batch or earlier in this batch)
    if (isset($source_to_wp_term_id_map[$source_id])) {
        $log[] = "({$current_row_number_for_log}) ملاحظة: المصدر ID {$source_id} تم معالجته مسبقاً (ربما كأصل). تخطي إعادة المعالجة الكاملة.";
        // We might still want to update its meta if this is its primary processing turn,
        // but for now, if it's in the map, we consider it handled to avoid duplicate processing.
        $return_status_details['status'] = 'skipped_as_parent';
        $return_status_details['term_id'] = $source_to_wp_term_id_map[$source_id];
        $return_status_details['log'] = $log;
        return $return_status_details;
    }

    // Basic recursion check within the same overall import process (less critical for batch but good for complex CSVs)
    if (isset($processed_source_ids_recursion_check[$source_id]) && $processed_source_ids_recursion_check[$source_id] === 'processing_now') {
        $log[] = "({$current_row_number_for_log}) تحذير: تبعية دائرية محتملة للمصدر ID {$source_id} داخل نفس الدفعة أو الاستدعاء. يتم التخطي.";
        $return_status_details['log'] = $log;
        return $return_status_details;
    }
    $processed_source_ids_recursion_check[$source_id] = 'processing_now';


    $term_name        = sanitize_text_field(trim($csv_row_data_assoc[$csv_column_map['name']] ?? 'Unnamed Compound'));
    $term_slug        = !empty($csv_row_data_assoc[$csv_column_map['slug']] ?? '') ? sanitize_title(trim($csv_row_data_assoc[$csv_column_map['slug']])) : sanitize_title($term_name);
    $term_description = wp_kses_post($csv_row_data_assoc[$csv_column_map['description']] ?? '');
    $parent_source_id = !empty($csv_row_data_assoc[$csv_column_map['parent_compound_id']] ?? '') ? trim($csv_row_data_assoc[$csv_column_map['parent_compound_id']]) : null;

    $developer_name_val = !empty($csv_row_data_assoc[$csv_column_map['developer_name_csv_col']] ?? '') ? sanitize_text_field(trim($csv_row_data_assoc[$csv_column_map['developer_name_csv_col']])) : null;
    $city_name_val      = !empty($csv_row_data_assoc[$csv_column_map['city_name_csv_col']] ?? '') ? sanitize_text_field(trim($csv_row_data_assoc[$csv_column_map['city_name_csv_col']])) : null;
    $cover_image_url    = !empty($csv_row_data_assoc[$csv_column_map['cover_image_url_csv_col']] ?? '') ? esc_url_raw(trim($csv_row_data_assoc[$csv_column_map['cover_image_url_csv_col']])) : null;

    $gallery_images_urls = [];
    if (isset($csv_column_map['gallery_img_base_col']) && isset($csv_column_map['gallery_img_count'])) {
        for ($i = 0; $i < $csv_column_map['gallery_img_count']; $i++) {
            $gallery_col_name = $csv_column_map['gallery_img_base_col'] . '[' . $i . ']';
            if (isset($csv_row_data_assoc[$gallery_col_name]) && !empty(trim($csv_row_data_assoc[$gallery_col_name]))) {
                $gallery_images_urls[] = trim($csv_row_data_assoc[$gallery_col_name]);
            }
        }
    }

    $parent_wp_term_id = 0;
    if ($parent_source_id && $parent_source_id != $source_id) {
        if (isset($source_to_wp_term_id_map[$parent_source_id])) { // Check if parent was processed in a previous batch/row
            $parent_wp_term_id = $source_to_wp_term_id_map[$parent_source_id];
            if (!$parent_wp_term_id) { // Should not happen if mapped, but defensive
                $log[] = "({$current_row_number_for_log}) تنبيه: الأصل مصدر ID {$parent_source_id} موجود في الخريطة ولكن بدون WP Term ID صالح لـ '{$term_name}'.";
            }
        } else {
            // Parent not yet processed in this import run. It might exist in DB from a previous import or manually.
            // For AJAX, true recursive import of a parent NOT YET in this CSV run's map is complex.
            // We'll rely on term_exists for parents not in our current import map.
            $log[] = "({$current_row_number_for_log}) ملاحظة: الأصل مصدر ID {$parent_source_id} لـ '{$term_name}' لم تتم معالجته بعد في هذه الدفعة. سيتم البحث عنه في قاعدة البيانات.";
            // Attempt to find by slug/name from a hypothetical parent term in DB (this part is tricky without full CSV data in memory)
            // For now, if not in map, it means it wasn't processed yet by *this* import run.
            // The term_exists check for the child term will use parent_wp_term_id = 0 if parent not found in map.
        }
    }


    $wp_term_id = null;
    $term_args = ['name' => $term_name, 'slug' => $term_slug, 'description' => $term_description, 'parent' => (int)$parent_wp_term_id];

    $existing_term = term_exists($term_slug, $taxonomy_slug, (int)$parent_wp_term_id);
    if (empty($term_slug) && !empty($term_name)) {
        $existing_term_by_name = term_exists($term_name, $taxonomy_slug, (int)$parent_wp_term_id);
        if ($existing_term_by_name) $existing_term = $existing_term_by_name;
    }

    if ($existing_term && is_array($existing_term) && isset($existing_term['term_id'])) {
        $wp_term_id = $existing_term['term_id'];
        $update_result = wp_update_term($wp_term_id, $taxonomy_slug, $term_args);
        if (is_wp_error($update_result)) {
            $log[] = "({$current_row_number_for_log}) تنبيه: '{$term_name}' (ID: {$wp_term_id}) موجود، فشل تحديثه: " . esc_html($update_result->get_error_message());
        } else {
            $log[] = "({$current_row_number_for_log}) <span style='color:#00A86B;'>تم تحديث '{$term_name}' (ID: {$wp_term_id}).</span>";
            $return_status_details['status'] = 'updated';
        }
    } else {
        $insert_result = wp_insert_term($term_name, $taxonomy_slug, $term_args);
        if (is_wp_error($insert_result)) {
            $log[] = "({$current_row_number_for_log}) <span style='color:red;'>خطأ استيراد '{$term_name}': " . esc_html($insert_result->get_error_message()) . "</span>";
            $processed_source_ids_recursion_check[$source_id] = 'failed_insert';
            $return_status_details['log'] = $log;
            return $return_status_details;
        } else {
            $wp_term_id = $insert_result['term_id'];
            $log[] = "({$current_row_number_for_log}) <span style='color:lightgreen;'>تم استيراد '{$term_name}' (Slug: {$term_slug}) كـ ID: {$wp_term_id}.</span>";
            $return_status_details['status'] = 'imported';
        }
    }
    $return_status_details['term_id'] = $wp_term_id;

    if ($wp_term_id) {
        if (function_exists('pll_set_term_language') && $current_import_language && $current_import_language !== 'default') {
            pll_set_term_language($wp_term_id, $current_import_language);
        }

        if ($developer_name_val) {
            $dev_term_id = cob_get_or_create_term_for_linking($developer_name_val, $developer_taxonomy_slug, $current_import_language);
            if ($dev_term_id) {
                update_term_meta($wp_term_id, $developer_meta_key, $dev_term_id);
                $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&hookrightarrow; ربط '{$term_name}' بمطور '{$developer_name_val}' (Term ID: {$dev_term_id}).";
            }
        }
        if ($city_name_val) {
            $city_term_id = cob_get_or_create_term_for_linking($city_name_val, $city_taxonomy_slug, $current_import_language);
            if ($city_term_id) {
                update_term_meta($wp_term_id, $city_meta_key, $city_term_id);
                $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&hookrightarrow; ربط '{$term_name}' بمدينة '{$city_name_val}' (Term ID: {$city_term_id}).";
            }
        }

        if ($cover_image_url && filter_var($cover_image_url, FILTER_VALIDATE_URL)) {
            $attachment_id = media_sideload_image($cover_image_url, 0, $term_name . ' Cover', 'id');
            if (!is_wp_error($attachment_id)) {
                update_term_meta($wp_term_id, $cover_image_meta_key, $attachment_id);
                $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&hookrightarrow; تنزيل وربط صورة غلاف لـ '{$term_name}' (Att ID: {$attachment_id}).";
                if (function_exists('pll_set_post_language') && $current_import_language && $current_import_language !== 'default') {
                    pll_set_post_language($attachment_id, $current_import_language);
                }
            } else {
                $log[] = "({$current_row_number_for_log}) <span style='color:orange;'>&nbsp;&nbsp;&hookrightarrow; فشل تنزيل صورة غلاف '{$term_name}' من {$cover_image_url}. الخطأ: " . esc_html($attachment_id->get_error_message()) . "</span>";
            }
        }

        if (!empty($gallery_images_urls)) {
            $gallery_attachment_ids = [];
            $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&hookrightarrow; بدء معالجة صور إضافية لـ '{$term_name}'...";
            foreach ($gallery_images_urls as $index => $gallery_url) {
                if ($gallery_url && filter_var($gallery_url, FILTER_VALIDATE_URL)) {
                    $gallery_attachment_id = media_sideload_image($gallery_url, 0, $term_name . ' Gallery ' . ($index + 1), 'id');
                    if (!is_wp_error($gallery_attachment_id)) {
                        $gallery_attachment_ids[] = $gallery_attachment_id;
                        // $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&nbsp;&nbsp;&hookrightarrow; تنزيل صورة إضافية (Att ID: {$gallery_attachment_id}).";
                        if (function_exists('pll_set_post_language') && $current_import_language && $current_import_language !== 'default') {
                            pll_set_post_language($gallery_attachment_id, $current_import_language);
                        }
                    } else {
                        $log[] = "({$current_row_number_for_log}) <span style='color:orange;'>&nbsp;&nbsp;&nbsp;&nbsp;&hookrightarrow; فشل تنزيل صورة إضافية من {$gallery_url}. الخطأ: " . esc_html($gallery_attachment_id->get_error_message()) . "</span>";
                    }
                } else {
                    $log[] = "({$current_row_number_for_log}) <span style='color:orange;'>&nbsp;&nbsp;&nbsp;&nbsp;&hookrightarrow; رابط صورة إضافية غير صالح: " . esc_html($gallery_url) . "</span>";
                }
            }
            if (!empty($gallery_attachment_ids)) {
                update_term_meta($wp_term_id, $gallery_images_meta_key, $gallery_attachment_ids);
                $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&hookrightarrow; تم حفظ " . count($gallery_attachment_ids) . " صورة إضافية لـ '{$term_name}'.";
            }
        }
    }

    $source_to_wp_term_id_map[$source_id] = $wp_term_id; // Store mapping for potential parent lookups
    $processed_source_ids_recursion_check[$source_id] = 'completed_batch_item';
    $return_status_details['log'] = $log;
    return $return_status_details;
}

// Helper function (cob_get_or_create_term_for_linking) remains the same as in the previous version.
// Ensure it's included if not already.
if (!function_exists('cob_get_or_create_term_for_linking')) {
    function cob_get_or_create_term_for_linking($term_name, $taxonomy_slug, $language_code = null) {
        if (empty($term_name) || empty($taxonomy_slug)) {
            return null;
        }
        $term_id = null;
        $existing_term = term_exists($term_name, $taxonomy_slug);

        if ($existing_term) {
            $term_id = is_array($existing_term) ? $existing_term['term_id'] : $existing_term;
        } else {
            $new_term_args = [];
            $new_term = wp_insert_term($term_name, $taxonomy_slug, $new_term_args);
            if (!is_wp_error($new_term) && isset($new_term['term_id'])) {
                $term_id = $new_term['term_id'];
                // Log this from the calling function for context
            } else {
                // Log error from calling function
                return null;
            }
        }
        if ($term_id && $language_code && $language_code !== 'default' && function_exists('pll_set_term_language')) {
            $current_term_lang = pll_get_term_language($term_id, 'slug');
            if ($current_term_lang !== $language_code) {
                pll_set_term_language($term_id, $language_code);
            }
        }
        return $term_id;
    }
}
?>
