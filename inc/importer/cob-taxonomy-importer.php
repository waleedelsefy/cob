<?php
/**
 * AJAX WordPress Importer for multiple taxonomies (e.g., 'developer', 'city') from a CSV file.
 * This is a more verbose and detailed version, restoring original logic for clarity.
 *
 * Instructions:
 * 1. Place this code in your theme's functions.php or a custom plugin.
 * 2. Create a JS file for the importer and update the path in cob_taxonomy_importer_enqueue_assets().
 * 3. Ensure the 'developer' and 'city' taxonomies are registered.
 * 4. Access via "Tools" > "Import Taxonomies (AJAX)".
 * 5. Verify CSV column names for each taxonomy in $cob_ajax_importer_config.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// --- Configuration for Taxonomy Importer ---
$cob_ajax_importer_config = [
    'csv_delimiter' => ',',
    'batch_size' => 1, // Process 1 row per AJAX request. Crucial for downloading images.
    'ajax_timeout_seconds' => 300,
    'status_option_name' => 'cob_taxonomy_importer_status',

    // Define settings for each supported taxonomy
    'taxonomies' => [
        'developer' => [
            'label' => __( 'Developer', 'cob_theme' ),
            'logo_image_meta_key' => '_developer_logo_id', // Meta key for the logo
            'csv_column_map_en' => [
                'id'                => 'id',
                'name'              => 'name_en',
                'slug'              => 'all_slugs_en',
                'description'       => 'description',
                'logo_url_csv_col'  => 'logo_path',
            ],
            'csv_column_map_ar' => [
                'id'                => 'id',
                'name'              => 'name_ar',
                'slug'              => 'all_slugs_ar',
                'description'       => 'description',
                'logo_url_csv_col'  => 'logo_path',
            ],
        ],
        'city' => [
            'label' => __( 'City', 'cob_theme' ),
            'logo_image_meta_key' => '_city_cover_image_id', // Meta key for the city image
            'csv_column_map_en' => [
                'id'                => 'id',
                'name'              => 'name_en',
                'slug'              => 'slug_en',
                'description'       => 'description_en',
                'logo_url_csv_col'  => 'image_url', // The column name in the CSV for the city image
            ],
            'csv_column_map_ar' => [
                'id'                => 'id',
                'name'              => 'name_ar',
                'slug'              => 'slug_ar',
                'description'       => 'description_ar',
                'logo_url_csv_col'  => 'image_url',
            ],
        ],
    ],
];


/**
 * Register Admin Page & Enqueue Assets for the Importer.
 */
add_action('admin_menu', 'cob_taxonomy_importer_register_page');
function cob_taxonomy_importer_register_page() {
    $hook = add_submenu_page(
        'tools.php',
        __( 'Taxonomy Importer (AJAX)', 'cob_theme' ),
        __( 'Import Taxonomies (AJAX)', 'cob_theme' ),
        'manage_options',
        'cob-taxonomy-importer-ajax',
        'cob_taxonomy_importer_render_page'
    );
    add_action("load-{$hook}", 'cob_taxonomy_importer_enqueue_assets');
}

function cob_taxonomy_importer_enqueue_assets() {
    global $cob_ajax_importer_config;
    $js_path = get_stylesheet_directory_uri() . '/inc/importer/cob-taxonomy-importer.js'; // **IMPORTANT**: Update this path

    wp_enqueue_script(
        'cob-taxonomy-importer-js',
        $js_path,
        ['jquery'],
        '1.2.0', // Version bumped for detailed version
        true
    );
    wp_localize_script('cob-taxonomy-importer-js', 'cobTaxonomyImporterAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cob_taxonomy_importer_ajax_nonce'),
        'status_option_name' => $cob_ajax_importer_config['status_option_name'],
        'i18n' => [ // Internationalization strings
            'confirm_new_import' => __( 'Are you sure you want to start a new import? Any previous progress will be cleared.', 'cob_theme' ),
            'confirm_resume' => __( 'This will resume the import from where it left off. Are you sure?', 'cob_theme' ),
            'confirm_cancel' => __( 'Are you sure you want to cancel the import and clear progress?', 'cob_theme' ),
            'error_selecting_file' => __( 'Please select a CSV file to upload.', 'cob_theme' ),
            'preparing_import' => __( 'Preparing import...', 'cob_theme' ),
            'import_complete' => __( '🎉 Import completed successfully! 🎉', 'cob_theme' ),
            'connection_error' => __( '❌ Server connection error', 'cob_theme' ),
        ]
    ]);
    wp_add_inline_style('wp-admin', "
        .cob-taxonomy-importer-progress-bar-container { border: 1px solid #ccc; padding: 2px; width: 100%; max-width: 600px; border-radius: 5px; background: #f1f1f1; margin-bottom:10px; }
        .cob-taxonomy-importer-progress-bar { background-color: #0073aa; height: 24px; width: 0%; text-align: center; line-height: 24px; color: white; border-radius: 3px; transition: width 0.3s ease-in-out; }
        #cob-taxonomy-importer-log { background: #1e1e1e; color: #f1f1f1; border: 1px solid #e5e5e5; padding: 10px; margin-top: 15px; max-height: 400px; overflow-y: auto; font-family: monospace; white-space: pre-wrap; border-radius: 4px; }
    ");
}

/**
 * Render Importer Page HTML.
 */
function cob_taxonomy_importer_render_page() {
    global $cob_ajax_importer_config;
    $import_status = get_option($cob_ajax_importer_config['status_option_name'], false);
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p><?php esc_html_e( 'Import and update taxonomy terms (like developers or cities) from a CSV file.', 'cob_theme' ); ?></p>

        <?php if ($import_status && isset($import_status['progress']) && $import_status['progress'] < 100) : ?>
            <div id="cob-taxonomy-importer-resume-notice" class="notice notice-warning is-dismissible">
                <p><?php printf( esc_html__( 'A previous import for %s (%s) was not completed. You can resume or cancel it.', 'cob_theme' ), '<strong>' . esc_html($import_status['taxonomy_label']) . '</strong>', '<code>' . esc_html($import_status['original_filename']) . '</code>' ); ?></p>
            </div>
        <?php endif; ?>

        <form id="cob-taxonomy-importer-form" method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="taxonomy_selector"><?php esc_html_e( 'Select Type to Import', 'cob_theme' ); ?></label></th>
                    <td>
                        <select id="taxonomy_selector" name="taxonomy_selector">
                            <?php foreach ($cob_ajax_importer_config['taxonomies'] as $slug => $details) : ?>
                                <option value="<?php echo esc_attr($slug); ?>"><?php echo esc_html($details['label']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e( 'Choose whether you are importing Developers or Cities.', 'cob_theme' ); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="csv_file"><?php esc_html_e( 'Select CSV File', 'cob_theme' ); ?></label></th>
                    <td><input type="file" id="csv_file" name="csv_file" accept=".csv,text/csv"></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="target_language_selector"><?php esc_html_e( 'Import Language', 'cob_theme' ); ?></label></th>
                    <td>
                        <select id="target_language_selector" name="target_language_selector">
                            <option value="en">English</option>
                            <option value="ar">العربية</option>
                        </select>
                    </td>
                </tr>
            </table>
            <button type="submit" id="cob-taxonomy-importer-start-new" class="button button-primary"><?php esc_html_e( 'Start New Import', 'cob_theme' ); ?></button>
            <button type="button" id="cob-taxonomy-importer-resume" class="button" style="<?php echo ($import_status && $import_status['progress'] < 100) ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Resume Import', 'cob_theme' ); ?></button>
            <button type="button" id="cob-taxonomy-importer-cancel" class="button button-secondary" style="<?php echo $import_status ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Cancel and Reset', 'cob_theme' ); ?></button>
        </form>

        <div id="cob-taxonomy-importer-progress-container" style="display:none; margin-top: 20px;">
            <h3><?php esc_html_e( 'Import Progress', 'cob_theme' ); ?></h3>
            <div class="cob-taxonomy-importer-progress-bar-container"><div id="cob-taxonomy-importer-progress-bar" class="cob-taxonomy-importer-progress-bar">0%</div></div>
            <p id="cob-taxonomy-importer-stats"></p>
            <h4><?php esc_html_e( 'Log:', 'cob_theme' ); ?></h4><div id="cob-taxonomy-importer-log"></div>
        </div>
    </div>
    <?php
}

/**
 * AJAX Handler for the Importer.
 */
add_action('wp_ajax_cob_taxonomy_importer_ajax_handler', 'cob_taxonomy_importer_ajax_handler_callback');
function cob_taxonomy_importer_ajax_handler_callback() {
    global $cob_ajax_importer_config;
    check_ajax_referer('cob_taxonomy_importer_ajax_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __( 'Insufficient permissions.', 'cob_theme' )]);
    }

    @set_time_limit( $cob_ajax_importer_config['ajax_timeout_seconds'] );
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
            $old_status = get_option($cob_ajax_importer_config['status_option_name']);
            if ($old_status && !empty($old_status['temp_file_path']) && file_exists($old_status['temp_file_path'])) {
                wp_delete_file($old_status['temp_file_path']);
            }
            delete_option($cob_ajax_importer_config['status_option_name']);

            if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                wp_send_json_error(['message' => __( 'No file uploaded or upload error.', 'cob_theme' )]);
            }

            $move_file = wp_handle_upload($_FILES['csv_file'], ['test_form' => false, 'mimes' => ['csv' => 'text/csv']]);
            if (!$move_file || isset($move_file['error'])) {
                wp_send_json_error(['message' => __( 'Error handling uploaded file: ', 'cob_theme' ) . ($move_file['error'] ?? 'Unknown error')]);
            }

            $log_messages[] = sprintf(esc_html__( "File uploaded successfully to: %s", 'cob_theme' ), esc_html($move_file['file']));

            $taxonomy_slug = isset($_POST['taxonomy_slug']) && array_key_exists($_POST['taxonomy_slug'], $cob_ajax_importer_config['taxonomies']) ? sanitize_key($_POST['taxonomy_slug']) : 'developer';
            $taxonomy_config = $cob_ajax_importer_config['taxonomies'][$taxonomy_slug];
            $selected_lang = isset($_POST['import_language']) ? sanitize_text_field($_POST['import_language']) : 'en';

            $file_path = $move_file['file'];
            $total_rows = 0;
            $headers = [];
            if (($handle = fopen($file_path, "r")) !== FALSE) {
                $header_row = fgetcsv($handle, 0, $cob_ajax_importer_config['csv_delimiter']);
                if($header_row === FALSE) {
                    wp_delete_file($file_path);
                    wp_send_json_error(['message' => __( 'Could not read header from CSV file.', 'cob_theme' )]);
                }
                $headers = array_map('trim', $header_row);
                $log_messages[] = __( "CSV Headers Found: ", 'cob_theme' ) . implode(' | ', array_map('esc_html', $headers));
                while (fgetcsv($handle, 0, $cob_ajax_importer_config['csv_delimiter']) !== FALSE) $total_rows++;
                fclose($handle);
            } else {
                wp_delete_file($file_path);
                wp_send_json_error(['message' => __( 'Failed to open uploaded file.', 'cob_theme' )]);
            }

            $current_map = $taxonomy_config['csv_column_map_' . $selected_lang] ?? $taxonomy_config['csv_column_map_en'];
            // Detailed header validation
            $essential_cols = ['id', 'name', 'logo_url_csv_col'];
            foreach($essential_cols as $key) {
                if(!isset($current_map[$key]) || !in_array($current_map[$key], $headers)) {
                    wp_delete_file($file_path);
                    wp_send_json_error(['message' => sprintf(esc_html__("CRITICAL ERROR: Essential CSV column '%s' is missing from the file header. Import aborted.", 'cob_theme'), esc_html($current_map[$key]))]);
                }
            }
            foreach($current_map as $key => $col_name) {
                if(!in_array($col_name, $headers)) {
                    $log_messages[] = "<span style='color:orange;'>" . sprintf(esc_html__("Warning: Expected column '%s' for '%s' not found in CSV header.", 'cob_theme'), esc_html($col_name), esc_html($key)) . "</span>";
                }
            }


            $status = [
                'taxonomy_slug'     => $taxonomy_slug,
                'taxonomy_label'    => $taxonomy_config['label'],
                'temp_file_path'    => $file_path,
                'original_filename' => sanitize_file_name($_FILES['csv_file']['name']),
                'total_rows'        => $total_rows,
                'processed_rows'    => 0,
                'imported_count'    => 0,
                'updated_count'     => 0,
                'failed_count'      => 0,
                'progress'          => 0,
                'language'          => $selected_lang,
                'csv_headers'       => $headers,
            ];
            update_option($cob_ajax_importer_config['status_option_name'], $status, 'no');
            $log_messages[] = sprintf(esc_html__("File ready. Total data rows to process: %d", 'cob_theme'), $total_rows);
            wp_send_json_success(['status' => $status, 'log' => $log_messages]);
            break;

        case 'run':
            // This part is largely the same as the simplified version, as it's the core loop.
            $status = get_option($cob_ajax_importer_config['status_option_name']);
            if (!$status || empty($status['temp_file_path']) || !file_exists($status['temp_file_path'])) {
                wp_send_json_error(['message' => __( 'No valid import process found. Please start over.', 'cob_theme' )]);
            }

            if ($status['processed_rows'] >= $status['total_rows']) {
                wp_send_json_success(['status' => $status, 'log' => [__( "Import already completed.", 'cob_theme' )], 'done' => true]);
            }

            $taxonomy_config = $cob_ajax_importer_config['taxonomies'][$status['taxonomy_slug']];
            $current_import_config = [
                'taxonomy_slug'       => $status['taxonomy_slug'],
                'target_language'     => $status['language'],
                'logo_image_meta_key' => $taxonomy_config['logo_image_meta_key'] ?? '',
                'csv_column_map'      => $taxonomy_config['csv_column_map_' . $status['language']] ?? $taxonomy_config['csv_column_map_en'],
            ];

            if (($handle = fopen($status['temp_file_path'], "r")) !== FALSE) {
                fgetcsv($handle);
                for ($i = 0; $i < $status['processed_rows']; $i++) fgetcsv($handle);

                $processed_in_this_batch = 0;
                while ($processed_in_this_batch < $cob_ajax_importer_config['batch_size'] && ($raw_row_data = fgetcsv($handle, 0, $cob_ajax_importer_config['csv_delimiter'])) !== FALSE) {
                    $status['processed_rows']++;
                    $row_data_assoc = @array_combine($status['csv_headers'], $raw_row_data);

                    if (!$row_data_assoc) {
                        $log_messages[] = "({$status['processed_rows']}) <span style='color:red;'>Error: Column count mismatch. Skipping.</span>";
                        $status['failed_count']++;
                        continue;
                    }

                    $import_result = cob_import_single_term_ajax($row_data_assoc, $current_import_config, $status['processed_rows']);
                    $log_messages = array_merge($log_messages, $import_result['log']);
                    if ($import_result['status'] === 'imported') $status['imported_count']++;
                    elseif ($import_result['status'] === 'updated') $status['updated_count']++;
                    elseif ($import_result['status'] === 'failed') $status['failed_count']++;
                    $processed_in_this_batch++;
                }
                fclose($handle);
            }

            $status['progress'] = ($status['total_rows'] > 0) ? round(($status['processed_rows'] / $status['total_rows']) * 100) : 100;
            $done = ($status['processed_rows'] >= $status['total_rows']);
            if ($done) {
                wp_delete_file($status['temp_file_path']);
                $status['temp_file_path'] = null;
                $log_messages[] = __( "Import finished. Temporary file deleted.", 'cob_theme' );
            }
            update_option($cob_ajax_importer_config['status_option_name'], $status, 'no');
            wp_send_json_success(['status' => $status, 'log' => $log_messages, 'done' => $done]);
            break;

        case 'cancel':
        case 'get_status':
            $status = get_option($cob_ajax_importer_config['status_option_name']);
            if ($importer_action === 'cancel') {
                if ($status && !empty($status['temp_file_path']) && file_exists($status['temp_file_path'])) {
                    wp_delete_file($status['temp_file_path']);
                }
                delete_option($cob_ajax_importer_config['status_option_name']);
                wp_send_json_success(['message' => __( 'Import process cancelled and status cleared.', 'cob_theme' )]);
            } else { // get_status
                if ($status && $status['progress'] < 100 && !empty($status['original_filename'])) {
                    wp_send_json_success(['status' => $status]);
                } else {
                    delete_option($cob_ajax_importer_config['status_option_name']);
                    wp_send_json_error(['message' => __( 'No resumable import process found.', 'cob_theme' )]);
                }
            }
            break;

        default:
            wp_send_json_error(['message' => __( 'Unknown importer action.', 'cob_theme' )]);
    }
}

/**
 * Import or Update a Single Taxonomy Term (Detailed Version).
 */
function cob_import_single_term_ajax($csv_row_data, $config, $row_num) {
    $log = [];
    $result = ['status' => 'failed', 'term_id' => null, 'log' => []];

    $taxonomy_slug = $config['taxonomy_slug'];
    $map = $config['csv_column_map'];
    $lang = $config['target_language'];
    $logo_meta_key = $config['logo_image_meta_key'];

    $source_id = trim($csv_row_data[$map['id']] ?? '');
    if (empty($source_id)) {
        $log[] = "({$row_num}) <span style='color:red;'>Error: Source ID is empty. Skipping.</span>";
        $result['log'] = $log;
        return $result;
    }

    $term_name = sanitize_text_field(trim($csv_row_data[$map['name']] ?? ''));
    $term_slug_source = trim($csv_row_data[$map['slug']] ?? '');
    $term_slug = !empty($term_slug_source) ? sanitize_title($term_slug_source) : sanitize_title($term_name);
    $term_description = wp_kses_post($csv_row_data[$map['description']] ?? '');

    $term_args = [
        'slug' => $term_slug,
        'description' => $term_description,
    ];

    $existing_term = term_exists($term_slug, $taxonomy_slug);
    if (!$existing_term && !empty($term_name)) {
        $existing_term = term_exists($term_name, $taxonomy_slug);
    }

    $term_id = null;

    if ($existing_term) {
        $term_id = $existing_term['term_id'];
        $current_term = get_term($term_id, $taxonomy_slug);
        $needs_update = false;

        if ($current_term && !is_wp_error($current_term)) {
            if ($current_term->name !== $term_name) $needs_update = true;
            if ($current_term->description !== $term_description) $needs_update = true;
            if ($current_term->slug !== $term_slug && !empty($term_slug)) $needs_update = true;
        } else {
            $needs_update = true;
        }

        if($needs_update) {
            wp_update_term($term_id, $taxonomy_slug, ['name' => $term_name] + $term_args);
            $log[] = "({$row_num}) <span style='color:#00A86B;'>" . sprintf(esc_html__( "Updated %s '%s'.", 'cob_theme' ), esc_html($taxonomy_slug), esc_html($term_name)) . "</span>";
        } else {
            $log[] = "({$row_num}) <span style='color:lightblue;'>" . sprintf(esc_html__( "%s '%s' exists and data is current. Checking image.", 'cob_theme' ), esc_html(ucfirst($taxonomy_slug)), esc_html($term_name)) . "</span>";
        }
        $result['status'] = 'updated';

    } else {
        $insert_result = wp_insert_term($term_name, $taxonomy_slug, $term_args);
        if (is_wp_error($insert_result)) {
            $log[] = "({$row_num}) <span style='color:red;'>" . sprintf(esc_html__( "Error importing %s '%s': %s", 'cob_theme' ), esc_html($taxonomy_slug), esc_html($term_name), esc_html($insert_result->get_error_message())) . "</span>";
            $result['log'] = $log;
            return $result;
        }
        $term_id = $insert_result['term_id'];
        $log[] = "({$row_num}) <span style='color:lightgreen;'>" . sprintf(esc_html__( "Imported new %s '%s'.", 'cob_theme' ), esc_html($taxonomy_slug), esc_html($term_name)) . "</span>";
        $result['status'] = 'imported';
    }

    $result['term_id'] = $term_id;

    if ($term_id) {
        if (function_exists('pll_set_term_language') && $lang && $lang !== 'default') {
            pll_set_term_language($term_id, $lang);
        }

        if (!empty($logo_meta_key)) {
            $image_url = !empty($csv_row_data[$map['logo_url_csv_col']]) ? esc_url_raw(trim($csv_row_data[$map['logo_url_csv_col']])) : null;
            if ($image_url && filter_var($image_url, FILTER_VALIDATE_URL)) {
                $attachment_id = media_sideload_image($image_url, 0, $term_name . ' Image', 'id');
                if (!is_wp_error($attachment_id)) {
                    update_term_meta($term_id, $logo_meta_key, $attachment_id);
                    $log[] = "&nbsp;&nbsp;&hookrightarrow; " . sprintf(esc_html__("Linked image for '%s'.", 'cob_theme'), esc_html($term_name));
                    if (function_exists('pll_set_post_language') && $lang && $lang !== 'default') {
                        pll_set_post_language($attachment_id, $lang);
                    }
                } else {
                    $log[] = "<span style='color:orange;'>&nbsp;&nbsp;&hookrightarrow; " . sprintf(esc_html__("Failed to download image: %s", 'cob_theme'), $attachment_id->get_error_message()) . "</span>";
                }
            } elseif ($image_url) {
                $log[] = "<span style='color:orange;'>&nbsp;&nbsp;&hookrightarrow; " . sprintf(esc_html__("Invalid image URL provided: %s", 'cob_theme'), esc_html($image_url)) . "</span>";
            }
        }
    }

    $result['log'] = $log;
    return $result;
}
