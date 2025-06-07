<?php
/**
 * AJAX WordPress Importer for 'properties' posts from a CSV file.
 *
 * This importer handles:
 * - Creating/updating property posts from a CSV file.
 * - Sideloading and setting a featured image from a URL.
 * - Sideloading and creating a gallery from multiple image URL columns.
 * - Finding or creating and then assigning taxonomies (developer, city, type, etc.).
 * - Automatically linking translations between languages using Polylang.
 * - Language-aware taxonomy assignment to prevent language mismatches.
 * - Support for resuming a failed import.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// --- Configuration ---
$cob_property_importer_config = [
    'post_type' => 'properties', // The CPT slug for properties.
    'source_id_meta_key' => '_property_source_id', // Meta key to store the original ID from CSV for linking translations.
    'csv_delimiter' => ',',
    'batch_size' => 1, // Process 1 row per AJAX request to avoid timeouts.
    'ajax_timeout_seconds' => 300,
    'status_option_name' => 'cob_property_importer_status',

    // Taxonomies to be assigned based on CSV columns.
    'taxonomies_map' => [
        'compound_name'           => 'compound',
        'compound_developer_name' => 'developer',
        'compound_area_name'      => 'city',
        'property_type_name'      => 'type',
        'finishing'               => 'finishing',
    ],

    // Column mapping based on your CSV sample.
    'csv_column_map_en' => [
        'id' => 'id',
        'name' => 'meta_title_en',
        'slug' => 'all_slugs_en',
        'description' => 'meta_description_en',
        'cover_image_url' => 'cover_image_url', // This is no longer used, gallery is used instead.
        'gallery_img_base' => 'Property_img', // Base name for gallery columns like Property_img[0]
        'gallery_img_count' => 8,
    ],
    'csv_column_map_ar' => [
        'id' => 'id',
        'name' => 'name',
        'slug' => 'all_slugs_ar',
        'description' => 'description',
        'cover_image_url' => 'cover_image_url',
        'gallery_img_base' => 'Property_img',
        'gallery_img_count' => 8,
    ],
];

// 1. Register Admin Page & Enqueue Assets
add_action('admin_menu', 'cob_prop_importer_register_page');
function cob_prop_importer_register_page() {
    $hook = add_submenu_page(
        'tools.php',
        'استيراد العقارات (AJAX)',
        'استيراد العقارات',
        'manage_options',
        'cob-property-importer',
        'cob_prop_importer_render_page'
    );
    add_action("load-{$hook}", 'cob_prop_importer_enqueue_assets');
}

function cob_prop_importer_enqueue_assets() {
    global $cob_property_importer_config;
    $js_path = get_stylesheet_directory_uri() . '/inc/importer/cob-property-importer.js';

    wp_enqueue_script('cob-prop-importer-js', $js_path, ['jquery'], '1.3.0', true);

    wp_localize_script('cob-prop-importer-js', 'cobPropImporter', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cob_prop_importer_nonce'),
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
        .cob-progress-bar-container { border: 1px solid #ccc; padding: 2px; width: 100%; max-width: 600px; border-radius: 5px; background: #f1f1f1; margin-bottom:10px; }
        .cob-progress-bar { background-color: #0073aa; height: 24px; width: 0%; text-align: center; line-height: 24px; color: white; border-radius: 3px; transition: width 0.3s ease-in-out; }
        #importer-log { background: #1e1e1e; color: #f1f1f1; border: 1px solid #e5e5e5; padding: 10px; margin-top: 15px; max-height: 400px; overflow-y: auto; font-family: monospace; white-space: pre-wrap; border-radius: 4px; }
    ");
}

// 2. Render Importer Page HTML
function cob_prop_importer_render_page() {
    global $cob_property_importer_config;
    $import_status = get_option($cob_property_importer_config['status_option_name'], false);
    ?>
    <div class="wrap">
        <h1>استيراد العقارات (معالجة بالـ AJAX)</h1>
        <p>تقوم هذه الأداة باستيراد وتحديث العقارات من ملف CSV، مع ربط التصنيفات والترجمات تلقائياً.</p>
        <div class="notice notice-warning">
            <p><strong>مهم جداً:</strong> تم ضبط حجم الدفعة إلى <strong><?php echo esc_html($cob_property_importer_config['batch_size']); ?></strong> لمعالجة صف واحد فقط لكل طلب، وذلك لتجنب أخطاء انتهاء المهلة.</p>
        </div>

        <?php if ($import_status && isset($import_status['progress']) && $import_status['progress'] < 100 && !empty($import_status['original_filename'])) : ?>
            <div id="resume-notice" class="notice notice-warning is-dismissible">
                <p>يوجد عملية استيراد سابقة للملف (<code><?php echo esc_html($import_status['original_filename']); ?></code>) لم تكتمل (<?php echo esc_html($import_status['progress']); ?>%). يمكنك متابعتها أو إلغائها.</p>
            </div>
        <?php endif; ?>

        <form id="cob-importer-form" method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="property_csv">اختر ملف CSV</label></th>
                    <td><input type="file" id="property_csv" name="property_csv" accept=".csv,text/csv"></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="import_language">لغة الاستيراد</label></th>
                    <td>
                        <select id="import_language" name="import_language">
                            <option value="en">English</option>
                            <option value="ar" selected>العربية</option>
                        </select>
                    </td>
                </tr>
            </table>
            <button type="submit" class="button button-primary">بدء استيراد جديد</button>
            <button type="button" id="resume-import" class="button" style="<?php echo ($import_status && $import_status['progress'] < 100) ? '' : 'display:none;'; ?>">متابعة الاستيراد</button>
            <button type="button" id="cancel-import" class="button button-secondary" style="<?php echo $import_status ? '' : 'display:none;'; ?>">إلغاء وإعادة تعيين</button>
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

// 3. AJAX Handler
add_action('wp_ajax_cob_run_property_importer', 'cob_ajax_run_property_importer_callback');
function cob_ajax_run_property_importer_callback() {
    global $cob_property_importer_config;
    check_ajax_referer('cob_prop_importer_nonce', 'nonce');

    if (!current_user_can('manage_options')) { wp_send_json_error(['message' => 'صلاحية غير كافية.']); }

    @set_time_limit($cob_property_importer_config['ajax_timeout_seconds']);
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
            $old_status = get_option($cob_property_importer_config['status_option_name']);
            if ($old_status && !empty($old_status['file_path']) && file_exists($old_status['file_path'])) {
                wp_delete_file($old_status['file_path']);
            }
            delete_option($cob_property_importer_config['status_option_name']);

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
            if (($handle = @fopen($file_path, "r")) !== FALSE) {
                $headers = array_map('trim', fgetcsv($handle, 0, $cob_property_importer_config['csv_delimiter']));
                while (fgetcsv($handle, 0, $cob_property_importer_config['csv_delimiter']) !== FALSE) {
                    $total_rows++;
                }
                fclose($handle);
            } else {
                wp_delete_file($file_path);
                wp_send_json_error(['message' => 'فشل في فتح الملف الذي تم رفعه.']);
            }

            $status = [
                'file_path' => $file_path,
                'original_filename' => sanitize_file_name($_FILES['csv_file']['name']),
                'total_rows' => $total_rows,
                'processed' => 0,
                'imported_count' => 0,
                'updated_count' => 0,
                'failed_count' => 0,
                'progress' => 0,
                'language' => isset($_POST['import_language']) ? sanitize_text_field($_POST['import_language']) : 'en',
                'headers' => $headers,
            ];
            update_option($cob_property_importer_config['status_option_name'], $status, 'no');
            wp_send_json_success(['status' => $status, 'log' => ["تم التحضير بنجاح. إجمالي الصفوف: {$total_rows}"]]);
            break;

        case 'run':
            $status = get_option($cob_property_importer_config['status_option_name']);
            if (!$status || empty($status['file_path']) || !file_exists($status['file_path'])) {
                wp_send_json_error(['message' => 'لم يتم العثور على عملية استيراد صالحة.']);
            }

            if ($status['processed'] >= $status['total_rows']) {
                wp_send_json_success(['status' => $status, 'log' => ["الاستيراد مكتمل بالفعل."], 'done' => true]);
            }

            $config_for_run = $cob_property_importer_config;
            $config_for_run['target_language'] = $status['language'];

            if (($handle = fopen($status['file_path'], "r")) !== FALSE) {
                fgetcsv($handle);
                for ($i = 0; $i < $status['processed']; $i++) { if(fgetcsv($handle) === FALSE) break; }

                $raw_row_data = fgetcsv($handle, 0, $config_for_run['csv_delimiter']);
                if($raw_row_data !== FALSE) {
                    $status['processed']++;

                    if (count($status['headers']) !== count($raw_row_data)) {
                        $log_messages[] = "({$status['processed']}) <span style='color:red;'>خطأ فادح: عدد الأعمدة لا يطابق الرأس. (وجد: " . count($raw_row_data) . ", متوقع: " . count($status['headers']) . "). يرجى مراجعة هذا الصف في ملف CSV.</span>";
                        $status['failed_count']++;
                    } else {
                        $row_data = @array_combine($status['headers'], $raw_row_data);
                        $import_result = cob_import_single_property($row_data, $config_for_run, $status['processed']);

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

            update_option($cob_property_importer_config['status_option_name'], $status, 'no');
            wp_send_json_success(['status' => $status, 'log' => $log_messages, 'done' => $done]);
            break;
        case 'cancel':
            $status = get_option($cob_property_importer_config['status_option_name']);
            if ($status && !empty($status['file_path']) && file_exists($status['file_path'])) {
                wp_delete_file($status['file_path']);
            }
            delete_option($cob_property_importer_config['status_option_name']);
            wp_send_json_success(['message' => 'تم إلغاء العملية ومسح الحالة بنجاح.']);
            break;
        case 'get_status':
            $status = get_option($cob_property_importer_config['status_option_name']);
            if ($status && isset($status['progress']) && $status['progress'] < 100 && !empty($status['original_filename'])) {
                wp_send_json_success(['status' => $status]);
            } else {
                delete_option($cob_property_importer_config['status_option_name']);
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
    $lang = $config['target_language'];
    $map = $config['csv_column_map_' . $lang];
    $post_type = $config['post_type'];
    $source_id_meta_key = $config['source_id_meta_key'];

    $source_id = trim($csv_row[$map['id']] ?? '');
    if (empty($source_id)) {
        $log[] = "({$row_num}) <span style='color:red;'>خطأ: معرف المصدر ('{$map['id']}') فارغ.</span>";
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
            'post_type' => $post_type,
            'meta_key' => $source_id_meta_key,
            'meta_value' => $source_id,
            'post_status' => 'any',
            'posts_per_page' => -1,
            'lang' => $lang, // Query for posts in the specific language
        ]);
        if ($existing_posts_query->have_posts()) {
            $post_id = $existing_posts_query->posts[0]->ID;
            $post_in_lang_exists = true;
        }
    } else {
        // Fallback if Polylang is not active
        $existing_post = get_posts(['post_type' => $post_type, 'meta_key' => $source_id_meta_key, 'meta_value' => $source_id, 'numberposts' => 1]);
        if($existing_post) {
            $post_id = $existing_post[0]->ID;
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
        $post_id = wp_insert_post($post_data);
        if (is_wp_error($post_id)) {
            $log[] = "({$row_num}) <span style='color:red;'>فشل إنشاء '{$post_title}': " . $post_id->get_error_message() . "</span>";
            return ['status' => 'failed', 'log' => $log];
        }
        $log[] = "({$row_num}) <span style='color:lightgreen;'>تم إنشاء '{$post_title}' كمنشور جديد (ID: {$post_id}).</span>";
        $result_status = 'imported';
    }

    if ($post_id) {
        update_post_meta($post_id, $source_id_meta_key, $source_id);
        if (function_exists('pll_set_post_language')) {
            pll_set_post_language($post_id, $lang);
        }

        if (function_exists('pll_save_post_translations') && function_exists('pll_get_post_language')) {
            $translations_to_save = [];
            $all_posts_with_id = get_posts(['post_type' => $post_type, 'meta_key' => $source_id_meta_key, 'meta_value' => $source_id, 'numberposts' => -1, 'fields' => 'ids']);
            if (count($all_posts_with_id) > 1) {
                foreach ($all_posts_with_id as $p_id) {
                    $p_lang = pll_get_post_language($p_id);
                    if($p_lang) $translations_to_save[$p_lang] = $p_id;
                }
                if(count($translations_to_save) > 1) {
                    pll_save_post_translations($translations_to_save);
                    $log[] = "({$row_num}) &nbsp;&nbsp;&hookrightarrow; <span style='color:cyan;'>تم ربط الترجمات بنجاح.</span>";
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
                $log[] = "({$row_num}) &nbsp;&nbsp;&hookrightarrow; جاري تحميل الصورة: " . basename($img_url);
                $att_id = media_sideload_image($img_url, $post_id, $post_title, 'id');
                if(!is_wp_error($att_id)) {
                    $gallery_ids[] = $att_id;
                    if(function_exists('pll_set_post_language')) pll_set_post_language($att_id, $lang);
                } else {
                    $log[] = "({$row_num}) &nbsp;&nbsp;&hookrightarrow; <span style='color:orange;'>فشل تحميل الصورة: " . esc_html($att_id->get_error_message()) . "</span>";
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

    return ['status' => $result_status, 'log' => $log, 'term_id' => $post_id];
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