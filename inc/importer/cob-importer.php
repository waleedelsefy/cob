<?php
// FILE: /inc/importer/cob-importer.php
// FINAL COMPLETE CODE - v15.3 - Double-checking CSV column names and Taxonomy/Gallery handling

/**
 * ===================================================================
 * Advanced AJAX CSV Importer for Properties (v15.3 - Final Polish)
 * ===================================================================
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// 1. إنشاء صفحة الإدارة في لوحة التحكم
add_action('admin_menu', 'cob_register_importer_page_advanced');
function cob_register_importer_page_advanced() {
    $hook = add_submenu_page(
        'tools.php',
        'استيراد العقارات المتقدم',
        'استيراد العقارات',
        'manage_options',
        'cob-property-importer',
        'cob_render_importer_page_advanced'
    );
    add_action("load-{$hook}", 'cob_load_importer_assets');
}

// 2. تحميل ملفات الجافاسكربت والـ CSS الخاصة بالأداة
function cob_load_importer_assets() {
    add_action('admin_enqueue_scripts', function() {
        $js_path = get_stylesheet_directory_uri() . '/inc/importer/cob-importer.js';
        wp_enqueue_script(
            'cob-importer-js',
            $js_path,
            ['jquery'],
            '1.15.3', // Updated version
            true
        );
        wp_localize_script('cob-importer-js', 'cobImporter', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('cob_importer_ajax_nonce'),
        ]);
        wp_add_inline_style('wp-admin', "
            .cob-progress-bar-container { border: 1px solid #ccc; padding: 2px; width: 100%; max-width: 600px; border-radius: 5px; background: #f1f1f1; }
            .cob-progress-bar { background-color: #0073aa; height: 24px; width: 0%; text-align: center; line-height: 24px; color: white; border-radius: 3px; transition: width 0.3s ease-in-out; }
            #importer-log { background: #1e1e1e; color: #f1f1f1; border: 1px solid #e5e5e5; padding: 10px; margin-top: 15px; max-height: 300px; overflow-y: auto; font-family: monospace; white-space: pre-wrap; border-radius: 4px; }
            .importer-source-choice, .importer-language-choice { margin-bottom: 20px; }
            #source-server-container, #source-upload-container, #language-select-container { padding-left: 20px; }
        ");
    });
}

// 3. عرض صفحة الاستيراد (HTML)
function cob_render_importer_page_advanced() {
    // ... (HTML code from v14.3/15 - no changes needed for the HTML structure here) ...
    $import_status = get_option('cob_importer_status', []);
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
        <h1>استيراد العقارات (معالجة بالـ AJAX)</h1>
        <?php if (!empty($import_status) && isset($import_status['progress']) && $import_status['progress'] < 100 && $import_status['progress'] > 0 && !empty($import_status['file'])) : ?>
            <div class="notice notice-warning is-dismissible"><p>يوجد عملية استيراد سابقة لم تكتمل. يمكنك متابعتها أو إلغائها.</p></div>
        <?php endif; ?>
        <form id="cob-importer-form" method="post" enctype="multipart/form-data">
            <h2>اختر مصدر الملف</h2>
            <div class="importer-source-choice">
                <p><label><input type="radio" name="import_source" value="upload" checked> رفع ملف</label></p>
                <div id="source-upload-container">
                    <table class="form-table"><tr><th scope="row"><label for="property_csv">اختر ملف CSV</label></th><td><input type="file" id="property_csv" name="property_csv" accept=".csv,text/csv"></td></tr></table>
                </div>
                <p><label><input type="radio" name="import_source" value="server"> اختيار من السيرفر</label></p>
                 <div id="source-server-container" style="display:none;">
                    <table class="form-table"><tr><th scope="row"><label for="server_csv_file">اختر ملف CSV</label></th><td>
                        <?php if (!empty($server_files)) : ?>
                            <select id="server_csv_file" name="server_csv_file" style="min-width: 300px;">
                                <?php foreach ($server_files as $file) : ?><option value="<?php echo esc_attr($file); ?>"><?php echo esc_html($file); ?></option><?php endforeach; ?>
                            </select>
                            <p class="description">المسار: <code>/wp-content/csv-imports/</code></p>
                        <?php else : ?><p>لم يتم العثور على ملفات CSV في مجلد <code>/wp-content/csv-imports/</code>. يرجى إنشاء المجلد ووضع الملفات به.</p><?php endif; ?>
                    </td></tr></table>
                </div>
            </div>

            <?php if (function_exists('pll_languages_list')) : ?>
            <h2>اختر لغة الاستيراد</h2>
            <div class="importer-language-choice">
                 <div id="language-select-container">
                    <table class="form-table"><tr><th scope="row"><label for="import_language">اللغة</label></th><td>
                        <select id="import_language" name="import_language" style="min-width: 300px;">
                            <?php
                            $languages = pll_languages_list(['fields' => 'slug']);
                            $names = pll_languages_list(['fields' => 'name']);
                            $default_lang = pll_default_language();
                            foreach ($languages as $index => $lang_slug) {
                                echo '<option value="' . esc_attr($lang_slug) . '" ' . selected($default_lang, $lang_slug, false) . '>' . esc_html($names[$index]) . ' (' . esc_html($lang_slug) . ')</option>';
                            }
                            ?>
                        </select>
                        <p class="description">اختر اللغة التي سيتم استيراد هذه البيانات إليها.</p>
                    </td></tr></table>
                </div>
            </div>
            <?php else: ?>
                <div class="notice notice-info"><p><em>إضافة Polylang غير مفعلة. سيتم استيراد البيانات بدون تحديد لغة بشكل دقيق.</em></p></div>
            <?php endif; ?>


            <button type="submit" class="button button-primary">بدء استيراد جديد</button>
            <?php if (!empty($import_status) && isset($import_status['progress']) && $import_status['progress'] < 100 && !empty($import_status['file'])) : ?>
                <button type="button" id="resume-import" class="button">متابعة الاستيراد</button>
                <button type="button" id="cancel-import" class="button button-secondary">إلغاء وإعادة تعيين</button>
            <?php endif; ?>
        </form>
        <div id="importer-progress-container" style="display:none; margin-top: 20px;">
            <h3>تقدم العملية</h3>
            <div class="cob-progress-bar-container"><div id="importer-progress-bar" class="cob-progress-bar">0%</div></div>
            <p id="importer-stats"></p>
            <h4>سجل العمليات:</h4><div id="importer-log"></div>
        </div>
    </div>
    <?php
}

// 4. معالج الـ AJAX الرئيسي
add_action('wp_ajax_cob_run_importer', 'cob_ajax_run_importer_advanced');
function cob_ajax_run_importer_advanced() {
    check_ajax_referer('cob_importer_ajax_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'صلاحية غير كافية.']);
    }

    $action = isset($_POST['importer_action']) ? sanitize_text_field($_POST['importer_action']) : '';
    $import_language_from_post = isset($_POST['import_language']) ? sanitize_text_field($_POST['import_language']) : null;

    switch ($action) {
        case 'prepare':
            // ... (PHP code for 'prepare' case from v15.1 - no changes here) ...
            $source_type = isset($_POST['source_type']) ? sanitize_text_field($_POST['source_type']) : 'upload';
            $file_path = '';
            $log = [];

            if ($source_type === 'upload') {
                if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                    wp_send_json_error(['message' => 'لم يتم رفع أي ملف أو حدث خطأ أثناء الرفع.', 'error_code' => $_FILES['csv_file']['error'] ?? 'UNKNOWN_UPLOAD_ERROR']);
                }
                $log[] = 'بدء معالجة الملف المرفوع.';
                $allowed_mime_types = ['csv' => 'text/csv', 'txt' => 'text/plain'];
                $file_info = wp_check_filetype_and_ext($_FILES['csv_file']['tmp_name'], $_FILES['csv_file']['name'], $allowed_mime_types);
                if (false === $file_info['ext'] || false === $file_info['type']) {
                     wp_send_json_error(['message' => 'نوع الملف غير صالح. يرجى رفع ملف CSV فقط.']);
                }
                $upload = wp_handle_upload($_FILES['csv_file'], ['test_form' => false, 'mimes' => $allowed_mime_types]);
                if (empty($upload) || isset($upload['error'])) {
                     wp_send_json_error(['message' => 'خطأ في معالجة الملف المرفوع: ' . ($upload['error'] ?? 'غير معروف')]);
                }
                $file_path = $upload['file'];
                $log[] = 'تم رفع الملف بنجاح إلى: ' . $file_path;

            } elseif ($source_type === 'server') {
                $file_name = isset($_POST['file_name']) ? sanitize_file_name($_POST['file_name']) : '';
                $log[] = "البحث عن الملف المحدد من السيرفر: " . esc_html($file_name);
                $imports_dir = WP_CONTENT_DIR . '/csv-imports/';
                $server_file_path = realpath($imports_dir . $file_name);

                if (!$server_file_path || !file_exists($server_file_path)) {
                     wp_send_json_error(['message' => 'فشل العثور على الملف في المسار المحدد على السيرفر: ' . esc_html($imports_dir . $file_name)]);
                }
                if (strpos($server_file_path, realpath($imports_dir)) !== 0) {
                    wp_send_json_error(['message' => 'خطأ أمني: محاولة الوصول إلى ملف خارج المجلد المسموح به.']);
                }
                if (!is_readable($server_file_path)) {
                    wp_send_json_error(['message' => 'فشل في قراءة الملف، يرجى التحقق من صلاحيات الملف (Permissions) على السيرفر.']);
                }
                $log[] = 'تم العثور على الملف وقراءته ممكنة: ' . $server_file_path;

                $upload_dir_info = wp_upload_dir();
                $upload_dir = $upload_dir_info['path'];
                if (!is_writable($upload_dir)) {
                    wp_send_json_error(['message' => 'مجلد الرفع غير قابل للكتابة: ' . $upload_dir]);
                }
                $temp_file_name = 'import_' . time() . '_' . basename($server_file_path);
                $temp_file_path = $upload_dir . '/' . $temp_file_name;

                if (!copy($server_file_path, $temp_file_path)) {
                    wp_send_json_error(['message' => 'فشل في نسخ الملف من السيرفر إلى مجلد مؤقت.']);
                }
                $file_path = $temp_file_path;
                $log[] = 'تم نسخ الملف بنجاح إلى مسار مؤقت: ' . $file_path;
            }

            if (empty($file_path) || !file_exists($file_path)) {
                 wp_send_json_error(['message' => 'لم يتم تحديد ملف صالح للاستيراد.']);
            }

            $total_rows = 0;
            $handle = @fopen($file_path, "r");
            if ($handle) {
                while (fgets($handle) !== false) { $total_rows++; }
                fclose($handle);
                $log[] = 'تم حساب عدد الصفوف بنجاح.';
            } else {
                if (file_exists($file_path)) wp_delete_file($file_path);
                wp_send_json_error(['message' => 'فشل في فتح الملف لحساب عدد الصفوف: ' . $file_path]);
            }
            $total_rows = $total_rows > 0 ? $total_rows - 1 : 0;

            $status = [
                'file' => $file_path, 
                'total_rows' => $total_rows, 
                'processed' => 0, 'skipped' => 0, 'progress' => 0,
                'language' => $import_language_from_post 
            ];
            update_option('cob_importer_status', $status, false);
            wp_send_json_success(['message' => 'الملف جاهز للمعالجة للغة: ' . esc_html($import_language_from_post) .'. إجمالي الصفوف: ' . $total_rows, 'status' => $status, 'log' => $log]);
            break;

        case 'run':
            $status = get_option('cob_importer_status');
            if (empty($status) || empty($status['file']) || !file_exists($status['file'])) {
                wp_send_json_error(['message' => 'لم يتم العثور على عملية استيراد للبدء أو الملف المؤقت مفقود.']);
            }
            
            $current_import_language = $status['language'] ?? null;
            if (function_exists('pll_default_language') && ($current_import_language === 'default' || !$current_import_language )) {
                $current_import_language = pll_default_language(); 
            }
            if (function_exists('pll_languages_list') && $current_import_language && $current_import_language !== 'default' && !in_array($current_import_language, pll_languages_list(['fields' => 'slug']))){
                 wp_send_json_error(['message' => 'اللغة المحددة للاستيراد (' . esc_html($current_import_language) . ') غير صالحة أو غير مفعلة في Polylang.']);
            }
             if (!$current_import_language && function_exists('pll_default_language') ) { 
                $current_import_language = pll_default_language();
            }

            set_time_limit(0); 
            $handle = @fopen($status['file'], 'r');
            if (!$handle) {
                wp_send_json_error(['message' => 'فشل في فتح الملف المؤقت للمعالجة: ' . $status['file']]);
            }

            $headers_row = fgetcsv($handle);
            if($headers_row === false){
                fclose($handle);
                wp_send_json_error(['message' => 'فشل في قراءة سطر العناوين من الملف. تأكد أن الملف CSV صالح.']);
            }
            $headers = array_map('trim', $headers_row);
            $log_messages = [];
            $row_number_overall = $status['processed']; // Keep track of overall row for logging

            if ($status['processed'] === 0) {
                 $log_messages[] = "الأعمدة التي تم العثور عليها في الملف: " . implode(' | ', $headers);
                 if ($current_import_language && $current_import_language !== 'default') {
                    $log_messages[] = "سيتم استيراد البيانات إلى اللغة: " . esc_html($current_import_language);
                 } else {
                    $log_messages[] = "تحذير: لم يتم تحديد لغة للاستيراد (Polylang غير مفعل أو لم يتم الاختيار). سيتم استيراد البيانات بدون تحديد لغة.";
                 }
            }

            fseek($handle, 0);
            fgetcsv($handle); 

            for ($i = 0; $i < $status['processed']; $i++) {
                if (fgetcsv($handle) === false) { 
                    fclose($handle);
                    $status['processed'] = $status['total_rows']; 
                    $status['progress'] = 100;
                    update_option('cob_importer_status', $status, false);
                    // استخدام $row_number_overall هنا
                    wp_send_json_success([
                        'status' => $status, 
                        'log' => array_merge($log_messages, ["(" . $row_number_overall . ") تحذير: تم الوصول لنهاية الملف قبل إكمال كل الصفوف المتوقعة."]), 
                        'done' => true, 
                        'message' => 'اكتمل الاستيراد (مع تحذير نهاية الملف).'
                    ]);
                    return;
                }
            }

            $batch_size = 5;
            $processed_in_batch = 0;
            // $row_number_overall is already tracking the overall row number

            while ($processed_in_batch < $batch_size && ($data = fgetcsv($handle)) !== FALSE) {
                $row_number_overall++; 
                if (count($headers) !== count($data)) {
                    $log_messages[] = "($row_number_overall) خطأ: عدد الأعمدة (" . count($data) . ") لا يطابق عدد العناوين (" . count($headers) . "). تم تخطي السطر.";
                    $status['skipped']++; $status['processed']++; $processed_in_batch++; continue;
                }
                $property_data = @array_combine($headers, $data);
                if ($property_data === false) {
                    $log_messages[] = "($row_number_overall) خطأ: فشل في دمج العناوين مع البيانات. تم تخطي السطر.";
                    $status['skipped']++; $status['processed']++; $processed_in_batch++; continue;
                }

                $post_title_key = 'name';
                if (isset($property_data['post_title'])) $post_title_key = 'post_title';
                
                if (empty($property_data[$post_title_key])) {
                    $log_messages[] = "($row_number_overall) خطأ: عمود '$post_title_key' (اسم العقار) فارغ أو غير موجود. تم تخطي السطر.";
                    $status['skipped']++; $status['processed']++; $processed_in_batch++; continue;
                }
                $name = sanitize_text_field($property_data[$post_title_key]);

                $post_slug_key = 'slug'; 
                $property_slug_from_csv = '';
                $existing_post_id = null;

                if (isset($property_data[$post_slug_key]) && !empty(trim($property_data[$post_slug_key]))) {
                    $property_slug_from_csv = sanitize_title(trim($property_data[$post_slug_key]));
                    
                    if (function_exists('pll_get_post_by_slug') && $current_import_language && $current_import_language !== 'default') {
                         $existing_post_id = pll_get_post_by_slug($property_slug_from_csv, 'properties', $current_import_language);
                    } elseif (function_exists('wpml_get_element_id_by_slug') && $current_import_language && $current_import_language !== 'default') { 
                        $post_type_for_wpml = 'post_properties'; 
                        $element_id = wpml_get_element_id_by_slug($property_slug_from_csv, $post_type_for_wpml, $current_import_language);
                        if ($element_id) { $existing_post_id = $element_id; }
                    } else { 
                        $query_args = ['name' => $property_slug_from_csv, 'post_type' => 'properties', 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids'];
                        $found_posts = get_posts($query_args);
                        if (!empty($found_posts)) {
                            $found_post_id_candidate = $found_posts[0];
                            if (function_exists('pll_get_post_language') && $current_import_language && $current_import_language !== 'default') {
                                if (pll_get_post_language($found_post_id_candidate, 'slug') === $current_import_language) {
                                    $existing_post_id = $found_post_id_candidate;
                                }
                            } else { 
                                $existing_post_id = $found_post_id_candidate;
                            }
                        }
                    }

                    if ($existing_post_id) {
                        $log_messages[] = "($row_number_overall) فحص الـ Slug: عُثر على منشور بالـ Slug '$property_slug_from_csv'" . ($current_import_language && $current_import_language !== 'default' ? " في اللغة '$current_import_language'" : "") . " (ID: $existing_post_id).";
                    } else {
                        $log_messages[] = "($row_number_overall) فحص الـ Slug: لم يُعثر على منشور بالـ Slug '$property_slug_from_csv'" . ($current_import_language && $current_import_language !== 'default' ? " في اللغة '$current_import_language'" : "") . ".";
                    }
                } else {
                    $log_messages[] = "($row_number_overall) تحذير: عمود '$post_slug_key' فارغ. لن يتم التحقق أو التعيين المخصص للـ Slug.";
                }

                if ($existing_post_id) {
                    $status['skipped']++;
                    $log_messages[] = "($row_number_overall) تخطي: العقار بالـ Slug '$property_slug_from_csv' موجود مسبقاً (ID: $existing_post_id).";
                } else {
                    $post_content = isset($property_data['description']) ? wp_kses_post($property_data['description']) : (isset($property_data['post_content']) ? wp_kses_post($property_data['post_content']) : '');
                    $post_arr = ['post_title' => $name, 'post_content' => $post_content, 'post_type' => 'properties', 'post_status' => 'publish'];
                    if (!empty($property_slug_from_csv)) {
                        $post_arr['post_name'] = $property_slug_from_csv;
                    }
                    $post_id = wp_insert_post($post_arr);

                    if (is_wp_error($post_id)) {
                        $status['skipped']++;
                        $log_messages[] = "($row_number_overall) فشل في إدخال '$name': " . $post_id->get_error_message();
                    } else {
                        if ($current_import_language && $current_import_language !== 'default' && function_exists('pll_set_post_language')) {
                            pll_set_post_language($post_id, $current_import_language);
                            $log_messages[] = "($row_number_overall) تم تعيين لغة المنشور (ID: $post_id) إلى '$current_import_language'.";
                        }

                        update_post_meta($post_id, 'compound_map_path', isset($property_data['compound_map_path']) ? esc_url_raw(trim($property_data['compound_map_path'])) : '');
                        update_post_meta($post_id, 'bathrooms', isset($property_data['number_of_bathrooms']) ? intval($property_data['number_of_bathrooms']) : 0);
                        update_post_meta($post_id, 'bedrooms', isset($property_data['number_of_bedrooms']) ? intval($property_data['number_of_bedrooms']) : 0);
                        update_post_meta($post_id, 'resale', isset($property_data['resale']) ? sanitize_text_field(trim($property_data['resale'])) : '');
                        update_post_meta($post_id, 'compound_polygon_points', isset($property_data['compound_polygon_points']) ? sanitize_textarea_field(trim($property_data['compound_polygon_points'])) : '');
                        update_post_meta($post_id, 'min_unit_area', isset($property_data['min_unit_area']) ? intval($property_data['min_unit_area']) : 0);
                        update_post_meta($post_id, 'area', isset($property_data['max_unit_area']) ? intval($property_data['max_unit_area']) : 0);
                        update_post_meta($post_id, 'min_price', isset($property_data['min_price']) ? sanitize_text_field(trim($property_data['min_price'])) : '');
                        update_post_meta($post_id, 'max_price', isset($property_data['max_price']) ? sanitize_text_field(trim($property_data['max_price'])) : '');
                        update_post_meta($post_id, 'max_garden_area', isset($property_data['max_garden_area']) ? intval($property_data['max_garden_area']) : 0);

                        $taxonomies_to_map = [
                            'compound_area_name'      => 'city',
                            'compound_developer_name' => 'developer',
                            'compound_name'  => 'compound',
                            'finishing'      => 'finishing',
                            'property_type_name'      => 'type',
                        ];

                        foreach ($taxonomies_to_map as $csv_column_name => $taxonomy_slug) {
                            if (isset($property_data[$csv_column_name]) && !empty(trim($property_data[$csv_column_name]))) {
                                $term_name = sanitize_text_field(trim($property_data[$csv_column_name]));
                                $term_id_to_set = null;
                                
                                $existing_term = term_exists($term_name, $taxonomy_slug);
                                if ($existing_term) {
                                    if (is_array($existing_term)) $term_id_to_set = $existing_term['term_id'];
                                    else $term_id_to_set = $existing_term;
                                } else {
                                    $new_term = wp_insert_term($term_name, $taxonomy_slug);
                                    if (!is_wp_error($new_term) && isset($new_term['term_id'])) {
                                        $term_id_to_set = $new_term['term_id'];
                                        $log_messages[] = "($row_number_overall) تم إنشاء المصطلح '$term_name' (ID: $term_id_to_set) للتصنيف '$taxonomy_slug'.";
                                    } else {
                                        $log_messages[] = "($row_number_overall) فشل في إنشاء المصطلح '$term_name' للتصنيف '$taxonomy_slug'. الخطأ: " . (is_wp_error($new_term) ? $new_term->get_error_message() : 'Unknown error');
                                    }
                                }
                                
                                if ($term_id_to_set) {
                                    if (function_exists('pll_set_term_language') && $current_import_language && $current_import_language !== 'default' && !pll_get_term_language((int)$term_id_to_set)) {
                                        pll_set_term_language((int)$term_id_to_set, $current_import_language);
                                        $log_messages[] = "($row_number_overall) تم تعيين لغة المصطلح '$term_name' (ID: $term_id_to_set) إلى '$current_import_language'.";
                                    }
                                    if ($taxonomy_slug === 'developer' && isset($property_data['developer_image_url']) && !empty(trim($property_data['developer_image_url']))) {
                                        $dev_image_url = esc_url_raw(trim($property_data['developer_image_url']));
                                        if (filter_var($dev_image_url, FILTER_VALIDATE_URL)) {
                                            update_term_meta((int)$term_id_to_set, 'developer_image', $dev_image_url); 
                                            $log_messages[] = "($row_number_overall) تم تحديث صورة المطور '$term_name' (ID: $term_id_to_set).";
                                        }
                                    }
                                    $term_result = wp_set_object_terms($post_id, (int)$term_id_to_set, $taxonomy_slug, true);
                                    if (is_wp_error($term_result)) {
                                        $log_messages[] = "($row_number_overall) خطأ في ربط التصنيف '$taxonomy_slug' بالمصطلح '$term_name' (ID: $term_id_to_set): " . $term_result->get_error_message();
                                    } else {
                                        $log_messages[] = "($row_number_overall) تم ربط التصنيف '$taxonomy_slug' بالمصطلح '$term_name' (ID: $term_id_to_set).";
                                    }
                                }
                            }
                        }
                        
                        // **معالجة الصور من أعمدة Property_img[0] إلى Property_img[6]**
                        $gallery_image_urls_from_csv = [];
                        for ($i = 0; $i <= 6; $i++) {
                            $img_col_name = 'Property_img[' . $i . ']'; // **استخدام اسم العمود كما هو من الصورة**
                            if (isset($property_data[$img_col_name]) && !empty(trim($property_data[$img_col_name]))) {
                                $gallery_image_urls_from_csv[] = trim($property_data[$img_col_name]);
                            }
                        }

                        if (!empty($gallery_image_urls_from_csv)) {
                            if (!function_exists('media_sideload_image')) {
                                require_once(ABSPATH . 'wp-admin/includes/media.php');
                                require_once(ABSPATH . 'wp-admin/includes/file.php');
                                require_once(ABSPATH . 'wp-admin/includes/image.php');
                            }
                            $gallery_ids = [];
                            if(count($gallery_image_urls_from_csv) > 0){
                                $log_messages[] = "($row_number_overall) بدء معالجة الصور لـ '$name'. عدد الروابط: " . count($gallery_image_urls_from_csv);
                            }

                            foreach ($gallery_image_urls_from_csv as $index => $image_url) {
                                if (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) {
                                    $log_messages[] = "($row_number_overall) تخطي رابط صورة غير صالح أو فارغ: '" . esc_html($image_url) . "'";
                                    continue;
                                }
                                $attachment_id = media_sideload_image($image_url, $post_id, $name, 'id');
                                if (!is_wp_error($attachment_id)) {
                                    $gallery_ids[] = $attachment_id;
                                    if ($current_import_language && $current_import_language !== 'default' && function_exists('pll_set_post_language')) {
                                        pll_set_post_language($attachment_id, $current_import_language);
                                    }
                                    $log_messages[] = "($row_number_overall) تم تنزيل الصورة بنجاح: '" . esc_html(basename($image_url)) . "' (ID: $attachment_id).";
                                    if ($index === 0 && !has_post_thumbnail($post_id)) {
                                        set_post_thumbnail($post_id, $attachment_id);
                                        $log_messages[] = "($row_number_overall) تم تعيين الصورة (ID: $attachment_id) كصورة بارزة.";
                                    }
                                } else {
                                    $log_messages[] = "($row_number_overall) فشل في تنزيل الصورة: '" . esc_html(basename($image_url)) . "'. الخطأ: " . $attachment_id->get_error_message();
                                }
                            }
                            if (!empty($gallery_ids)) {
                                update_post_meta($post_id, '_cob_gallery_images', $gallery_ids); 
                                $log_messages[] = "($row_number_overall) تم حفظ " . count($gallery_ids) . " صورة في معرض العقار (Meta Key: _cob_gallery_images).";
                            }
                        } else {
                             $log_messages[] = "($row_number_overall) لا توجد أعمدة صور (Property_img[X]) أو أنها فارغة لـ '$name'.";
                        }
                        $log_messages[] = "($row_number_overall) تم إدخال: '$name' (ID: $post_id).";
                    }
                }
                $status['processed']++; $processed_in_batch++;
            }
            fclose($handle);
            $status['progress'] = ($status['total_rows'] > 0) ? round(($status['processed'] / $status['total_rows']) * 100) : 100;
            update_option('cob_importer_status', $status, false);

            if ($status['processed'] >= $status['total_rows']) {
                 wp_send_json_success(['status' => $status, 'log' => $log_messages, 'done' => true, 'message' => 'اكتمل الاستيراد!']);
            } else {
                wp_send_json_success(['status' => $status, 'log' => $log_messages, 'done' => false]);
            }
            break;

        case 'cancel':
            $status = get_option('cob_importer_status');
            if (!empty($status) && !empty($status['file']) && file_exists($status['file'])) {
                wp_delete_file($status['file']);
            }
            delete_option('cob_importer_status');
            wp_send_json_success(['message' => 'تم إلغاء العملية ومسح الملف المؤقت بنجاح.']);
            break;
    }
    wp_send_json_error(['message' => 'حدث خطأ غير معروف أو وصل الطلب لحالة غير متوقعة.']);
}