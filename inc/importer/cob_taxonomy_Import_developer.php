<?php
/**
 * AJAX WordPress Importer for 'developer' taxonomy from a CSV file.
 * Includes downloading and linking a logo for each developer.
 *
 * Instructions:
 * 1. Place this code in your theme's functions.php or a custom plugin.
 * 2. Create a JS file (e.g., your-theme/js/cob-developer-importer.js) with the JS code provided separately.
 * 3. Update JS_PATH in cob_dev_importer_enqueue_assets() to the correct path of your JS file.
 * 4. Ensure the 'developer' taxonomy is registered.
 * 5. Access via "Tools" > "استيراد المطورين (AJAX)".
 * 6. Verify CSV column names and taxonomy slug in $cob_developer_importer_config.
 * 7. Backup your database before any import.
 * 8. **CRITICAL FOR PERFORMANCE/TIMEOUTS: 'batch_size' is set to 1.**
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// --- Configuration for Developer Importer ---
$cob_developer_importer_config = [
    'taxonomy_slug' => 'developer', // Slug for the "developer" taxonomy
    'target_language' => 'en',     // Default import language: 'en' or 'ar'
    'csv_delimiter' => ',',        // CSV delimiter

    // Meta key for storing the developer's logo attachment ID
    'logo_image_meta_key' => '_developer_logo_id',

    // ** CRITICAL **: For servers with low execution time limits, this MUST be 1.
    'batch_size' => 1, // Process 1 row per AJAX request.
    'ajax_timeout_seconds' => 300, // Attempt to set script execution time per batch (seconds).
    'status_option_name' => 'cob_developer_importer_status', // WP option to store import status

    // CSV Column Mapping for English/Default
    'csv_column_map_en' => [
        'id'                => 'id', // Unique source ID for the developer
        'name'              => 'name_en', // Developer name (English) - or 'name' if it's always English
        'slug'              => 'all_slugs_en', // Developer slug (English) - if empty, generated from name
        'description'       => 'description', // Developer description
        'logo_url_csv_col'  => 'logo_path', // CSV column for the developer logo URL
        // Add other meta fields you want to import here, e.g.:
        // 'meta_title_en_col' => 'meta_title_en',
        // 'meta_description_en_col' => 'meta_description_en',
        // 'official_name_col' => 'official_name',
    ],
    // CSV Column Mapping for Arabic
    'csv_column_map_ar' => [
        'id'                => 'id',
        'name'              => 'name_ar', // Developer name (Arabic)
        'slug'              => 'all_slugs_ar', // Developer slug (Arabic)
        'description'       => 'description', // Assuming description is multilingual or use a specific AR column
        'logo_url_csv_col'  => 'logo_path',
        // 'meta_title_ar_col' => 'meta_title_ar',
        // 'meta_description_ar_col' => 'meta_description_ar',
    ],
];

/**
 * Register Admin Page & Enqueue Assets for Developer Importer.
 */
add_action('admin_menu', 'cob_dev_importer_register_page');
function cob_dev_importer_register_page() {
    $hook = add_submenu_page(
        'tools.php',
        __( 'Developer Importer (AJAX)', 'cob_theme' ),
        __( 'Import Developers (AJAX)', 'cob_theme' ),
        'manage_options', // Or 'manage_terms' or a custom capability
        'cob-developer-importer-ajax',
        'cob_dev_importer_render_page'
    );
    add_action("load-{$hook}", 'cob_dev_importer_enqueue_assets');
}

function cob_dev_importer_enqueue_assets() {
    global $cob_developer_importer_config;
    // **IMPORTANT**: Update this path to where you save the JS file for the developer importer.
    $js_path = get_stylesheet_directory_uri() . '/inc/importer/cob-developer-importer.js'; // Example path

    wp_enqueue_script(
        'cob-dev-importer-js', // Unique handle for this script
        $js_path,
        ['jquery'],
        '1.0.0',
        true
    );
    wp_localize_script('cob-dev-importer-js', 'cobDevImporterAjax', [ // Unique object name
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cob_dev_importer_ajax_nonce'), // Unique nonce
        'status_option_name' => $cob_developer_importer_config['status_option_name'],
        'i18n' => [ // You can reuse or adapt i18n strings from the compound importer JS
            'confirm_new_import' => __( 'Are you sure you want to start a new developer import? Any previous progress or temporary file will be cleared.', 'cob_theme' ),
            'confirm_resume' => __( 'This will resume the developer import from where it left off. Are you sure?', 'cob_theme' ),
            'confirm_cancel' => __( 'Are you sure you want to cancel the developer import and clear progress/temporary file?', 'cob_theme' ),
            'error_selecting_file' => __( 'Please select a CSV file to upload.', 'cob_theme' ),
            'preparing_import' => __( 'Preparing developer import...', 'cob_theme' ),
            'import_complete' => __( '🎉 Developer import completed successfully! 🎉', 'cob_theme' ),
            'error_unknown_prepare' => __( 'An unknown error occurred during preparation.', 'cob_theme' ),
            'error_unknown_processing' => __( 'An unknown error occurred during processing.', 'cob_theme' ),
            'connection_error' => __( '❌ Server connection error', 'cob_theme' ),
            'resuming_import' => __( 'Resuming previous developer import...', 'cob_theme' ),
            'processed_of' => __( 'Processed', 'cob_theme' ),
            'from' => __( 'of', 'cob_theme' ),
            'skipped' => __( 'Failed/Skipped', 'cob_theme' ),
            'for_language' => __( 'for language:', 'cob_theme' ),
            'import_cancelled_successfully' => __( 'Developer import cancelled successfully.', 'cob_theme' ),
            'error_cancelling' => __( 'Error during cancellation.', 'cob_theme' ),
            'error_connecting_cancel' => __( 'Connection error while trying to cancel.', 'cob_theme' ),
        ]
    ]);
    // You can reuse the same inline style if the class names are the same, or define new ones.
    // For simplicity, assuming similar class names for progress bar and log.
    wp_add_inline_style('wp-admin', "
        .cob-dev-importer-progress-bar-container { border: 1px solid #ccc; padding: 2px; width: 100%; max-width: 600px; border-radius: 5px; background: #f1f1f1; margin-bottom:10px; }
        .cob-dev-importer-progress-bar { background-color: #0073aa; height: 24px; width: 0%; text-align: center; line-height: 24px; color: white; border-radius: 3px; transition: width 0.3s ease-in-out; }
        #cob-dev-importer-log { background: #1e1e1e; color: #f1f1f1; border: 1px solid #e5e5e5; padding: 10px; margin-top: 15px; max-height: 400px; overflow-y: auto; font-family: monospace; white-space: pre-wrap; border-radius: 4px; }
    ");
}

/**
 * Render Importer Page HTML for Developer Importer.
 */
function cob_dev_importer_render_page() {
    global $cob_developer_importer_config;
    $import_status = get_option($cob_developer_importer_config['status_option_name'], false);
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p><?php esc_html_e( 'Import and update developers from a CSV file. This tool will download logos and handle existing developers.', 'cob_theme' ); ?></p>
        <div class="notice notice-warning">
            <p><strong><?php esc_html_e( 'Important for avoiding timeouts:', 'cob_theme' ); ?></strong> <?php printf( esc_html__( 'The batch size is currently set to %d. If you experience timeouts, ensure this is 1. This processes one developer per request, which is crucial when downloading images.', 'cob_theme' ), esc_html($cob_developer_importer_config['batch_size']) ); ?></p>
        </div>

        <?php if ($import_status && isset($import_status['progress']) && $import_status['progress'] < 100 && $import_status['progress'] >= 0 && !empty($import_status['original_filename'])) : ?>
            <div id="cob-dev-importer-resume-notice" class="notice notice-warning is-dismissible">
                <p><?php printf( esc_html__( 'A previous developer import for file (%s) was not completed (%s%%). You can resume or cancel it to start a new one.', 'cob_theme' ), '<code>' . esc_html($import_status['original_filename']) . '</code>', esc_html($import_status['progress']) ); ?></p>
            </div>
        <?php endif; ?>

        <form id="cob-dev-importer-form" method="post" enctype="multipart/form-data">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="developer_csv_file"><?php esc_html_e( 'Select CSV File', 'cob_theme' ); ?></label></th>
                    <td><input type="file" id="developer_csv_file" name="developer_csv_file" accept=".csv,text/csv"></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="dev_target_language_selector"><?php esc_html_e( 'Import Language', 'cob_theme' ); ?></label></th>
                    <td>
                        <select id="dev_target_language_selector" name="dev_target_language_selector">
                            <option value="en" <?php selected($cob_developer_importer_config['target_language'], 'en'); ?>>English</option>
                            <option value="ar" <?php selected($cob_developer_importer_config['target_language'], 'ar'); ?>>العربية</option>
                        </select>
                    </td>
                </tr>
            </table>
            <button type="submit" id="cob-dev-importer-start-new" class="button button-primary"><?php esc_html_e( 'Start New Developer Import', 'cob_theme' ); ?></button>
            <button type="button" id="cob-dev-importer-resume" class="button" style="<?php echo ($import_status && isset($import_status['progress']) && $import_status['progress'] < 100 && isset($import_status['total_rows']) && $import_status['total_rows'] > 0) ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Resume Developer Import', 'cob_theme' ); ?></button>
            <button type="button" id="cob-dev-importer-cancel" class="button button-secondary" style="<?php echo $import_status ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Cancel and Reset', 'cob_theme' ); ?></button>
        </form>

        <div id="cob-dev-importer-progress-container" style="display:none; margin-top: 20px;">
            <h3><?php esc_html_e( 'Import Progress', 'cob_theme' ); ?></h3>
            <div class="cob-dev-importer-progress-bar-container"><div id="cob-dev-importer-progress-bar" class="cob-dev-importer-progress-bar">0%</div></div>
            <p id="cob-dev-importer-stats"></p>
            <h4><?php esc_html_e( 'Log:', 'cob_theme' ); ?></h4><div id="cob-dev-importer-log"></div>
        </div>
    </div>
    <?php
}

/**
 * AJAX Handler for Developer Importer.
 */
add_action('wp_ajax_cob_dev_importer_ajax_handler', 'cob_dev_importer_ajax_handler_callback'); // Unique action name
function cob_dev_importer_ajax_handler_callback() {
    global $cob_developer_importer_config;
    check_ajax_referer('cob_dev_importer_ajax_nonce', 'nonce'); // Unique nonce name

    if (!current_user_can('manage_options')) { // Or 'manage_terms'
        wp_send_json_error(['message' => __( 'Insufficient permissions.', 'cob_theme' )]);
    }

    $execution_time = isset($cob_developer_importer_config['ajax_timeout_seconds']) ? (int) $cob_developer_importer_config['ajax_timeout_seconds'] : 300;
    @set_time_limit( $execution_time );
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
            $old_status = get_option($cob_developer_importer_config['status_option_name']);
            if ($old_status && isset($old_status['temp_file_path']) && file_exists($old_status['temp_file_path'])) {
                wp_delete_file($old_status['temp_file_path']);
            }
            delete_option($cob_developer_importer_config['status_option_name']);

            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                wp_send_json_error(['message' => __( 'No file uploaded or upload error. Code: ', 'cob_theme' ) . ($_FILES['csv_file']['error'] ?? 'N/A')]);
            }

            $uploaded_file = $_FILES['csv_file'];
            $original_filename = sanitize_file_name($uploaded_file['name']);
            $upload_overrides = ['test_form' => false, 'mimes' => ['csv' => 'text/csv', 'txt' => 'text/plain']];
            $move_file = wp_handle_upload($uploaded_file, $upload_overrides);

            if (!$move_file || isset($move_file['error'])) {
                wp_send_json_error(['message' => __( 'Error handling uploaded file: ', 'cob_theme' ) . ($move_file['error'] ?? __( 'Unknown error', 'cob_theme' ))]);
            }

            $log_messages[] = sprintf(esc_html__( "File uploaded successfully: %s to %s", 'cob_theme' ), esc_html($original_filename), esc_html($move_file['file']));
            $file_path = $move_file['file'];
            $total_rows = 0;
            $headers = [];

            if (($handle = fopen($file_path, "r")) !== FALSE) {
                $header_row_data = fgetcsv($handle, 0, $cob_developer_importer_config['csv_delimiter']);
                if ($header_row_data !== FALSE) {
                    $headers = array_map('trim', $header_row_data);
                    $log_messages[] = __( "CSV Headers: ", 'cob_theme' ) . implode(' | ', array_map('esc_html', $headers));
                } else {
                    if (file_exists($file_path)) wp_delete_file($file_path);
                    wp_send_json_error(['message' => __( 'Failed to read CSV header.', 'cob_theme' )]);
                }
                while (fgetcsv($handle, 0, $cob_developer_importer_config['csv_delimiter']) !== FALSE) {
                    $total_rows++;
                }
                fclose($handle);
            } else {
                if (file_exists($file_path)) wp_delete_file($file_path);
                wp_send_json_error(['message' => __( 'Failed to open file to count rows.', 'cob_theme' )]);
            }

            $selected_lang = isset($_POST['import_language']) ? sanitize_text_field($_POST['import_language']) : $cob_developer_importer_config['target_language'];
            $current_column_map_key = 'csv_column_map_' . $selected_lang;
            $current_csv_column_map = $cob_developer_importer_config[$current_column_map_key] ?? $cob_developer_importer_config['csv_column_map_en'];

            // Validate essential headers
            $required_cols_for_functionality = ['id', 'name', 'logo_url_csv_col'];
            foreach ($required_cols_for_functionality as $req_key) {
                $mapped_col_name = $current_csv_column_map[$req_key] ?? $req_key; // Fallback to key if not in map
                if (!isset($current_csv_column_map[$req_key]) || !in_array($current_csv_column_map[$req_key], $headers)) {
                    if (file_exists($file_path)) wp_delete_file($file_path);
                    wp_send_json_error(['message' => sprintf(esc_html__( "Error: Required CSV column '%s' (for %s) not found in CSV header. Cannot proceed.", 'cob_theme' ), esc_html($current_csv_column_map[$req_key] ?? $req_key), esc_html($req_key)), 'log' => $log_messages]);
                }
            }
            foreach ($current_csv_column_map as $internal_key => $csv_col_name_mapped) {
                if (empty($csv_col_name_mapped) || in_array($internal_key, $required_cols_for_functionality)) continue;
                if (!in_array($csv_col_name_mapped, $headers)) {
                    $log_messages[] = "<span style='color:orange;'>" . sprintf(esc_html__( "Warning: Expected CSV column '%s' (for %s) not found in header. This data may not be imported or errors might occur.", 'cob_theme' ), esc_html($csv_col_name_mapped), esc_html($internal_key)) . "</span>";
                }
            }


            $status = [
                'temp_file_path'    => $file_path,
                'original_filename' => $original_filename,
                'total_rows'        => $total_rows,
                'processed_rows'    => 0,
                'imported_count'    => 0,
                'updated_count'     => 0,
                'failed_count'      => 0,
                'progress'          => 0,
                'language'          => $selected_lang,
                'csv_headers'       => $headers,
            ];
            update_option($cob_developer_importer_config['status_option_name'], $status, 'no');

            $log_messages[] = sprintf(esc_html__( "File ready for processing. Total data rows (excluding header): %d", 'cob_theme' ), $total_rows);
            wp_send_json_success(['message' => __( 'Preparation successful.', 'cob_theme' ), 'status' => $status, 'log' => $log_messages]);
            break;

        case 'run':
            $status = get_option($cob_developer_importer_config['status_option_name']);
            if (!$status || empty($status['temp_file_path']) || !file_exists($status['temp_file_path'])) {
                wp_send_json_error(['message' => __( 'No valid import process found or temporary file is missing. Please start over.', 'cob_theme' )]);
            }

            if ($status['processed_rows'] >= $status['total_rows']) {
                if (isset($status['temp_file_path']) && file_exists($status['temp_file_path'])) {
                    wp_delete_file($status['temp_file_path']);
                    $status['temp_file_path'] = null;
                    update_option($cob_developer_importer_config['status_option_name'], $status, 'no');
                }
                wp_send_json_success(['status' => $status, 'log' => [__( "Import already completed.", 'cob_theme' )], 'done' => true]);
            }

            $file_path = $status['temp_file_path'];
            $csv_headers = $status['csv_headers'];
            $processed_in_this_batch = 0;

            $current_config_for_import_func = [
                'taxonomy_slug'           => $cob_developer_importer_config['taxonomy_slug'],
                'target_language'         => $status['language'],
                'logo_image_meta_key'     => $cob_developer_importer_config['logo_image_meta_key'],
                'csv_column_map'          => $cob_developer_importer_config['csv_column_map_' . $status['language']] ?? $cob_developer_importer_config['csv_column_map_en'],
            ];

            if (($handle = fopen($file_path, "r")) !== FALSE) {
                fgetcsv($handle, 0, $cob_developer_importer_config['csv_delimiter']);
                for ($i = 0; $i < $status['processed_rows']; $i++) {
                    if (fgetcsv($handle, 0, $cob_developer_importer_config['csv_delimiter']) === FALSE) {
                        $log_messages[] = sprintf(esc_html__( "(Row %d) Warning: Reached end of file unexpectedly while skipping processed rows.", 'cob_theme' ), $status['processed_rows']);
                        $status['processed_rows'] = $status['total_rows'];
                        break;
                    }
                }

                while ($processed_in_this_batch < $cob_developer_importer_config['batch_size'] && $status['processed_rows'] < $status['total_rows']) {
                    $raw_row_data = fgetcsv($handle, 0, $cob_developer_importer_config['csv_delimiter']);
                    if ($raw_row_data === FALSE) {
                        $log_messages[] = __( "Reached end of file.", 'cob_theme' );
                        $status['processed_rows'] = $status['total_rows'];
                        break;
                    }
                    $status['processed_rows']++;
                    $current_row_number_for_log = $status['processed_rows'];

                    if (count($csv_headers) !== count($raw_row_data)) {
                        $log_messages[] = sprintf(esc_html__( "(Row %d) Error: Column count (%d) does not match header count (%d). Skipping row.", 'cob_theme' ), $current_row_number_for_log, count($raw_row_data), count($csv_headers));
                        $status['failed_count']++;
                        $processed_in_this_batch++;
                        continue;
                    }
                    $row_data_assoc = @array_combine($csv_headers, $raw_row_data);
                    if ($row_data_assoc === false) {
                        $log_messages[] = sprintf(esc_html__( "(Row %d) Error: Failed to combine headers with data. Skipping row.", 'cob_theme' ), $current_row_number_for_log);
                        $status['failed_count']++;
                        $processed_in_this_batch++;
                        continue;
                    }

                    $import_result = cob_import_single_developer_ajax($row_data_assoc, $current_config_for_import_func, $current_row_number_for_log);

                    if (isset($import_result['log'])) $log_messages = array_merge($log_messages, $import_result['log']);

                    if ($import_result['status'] === 'imported') $status['imported_count']++;
                    elseif ($import_result['status'] === 'updated') $status['updated_count']++;
                    elseif ($import_result['status'] === 'failed') $status['failed_count']++;

                    $processed_in_this_batch++;
                }
                fclose($handle);
            } else {
                wp_send_json_error(['message' => __( 'Failed to reopen temporary file for processing.', 'cob_theme' )]);
            }

            $status['progress'] = ($status['total_rows'] > 0) ? round(($status['processed_rows'] / $status['total_rows']) * 100) : 100;

            $done = ($status['processed_rows'] >= $status['total_rows']);
            if ($done) {
                if (isset($status['temp_file_path']) && file_exists($status['temp_file_path'])) {
                    wp_delete_file($status['temp_file_path']);
                    $status['temp_file_path'] = null;
                }
                $log_messages[] = __( "Import completed. Temporary file deleted.", 'cob_theme' );
            }
            update_option($cob_developer_importer_config['status_option_name'], $status, 'no');
            wp_send_json_success(['status' => $status, 'log' => $log_messages, 'done' => $done]);
            break;

        case 'cancel':
            $status = get_option($cob_developer_importer_config['status_option_name']);
            if ($status && isset($status['temp_file_path']) && file_exists($status['temp_file_path'])) {
                wp_delete_file($status['temp_file_path']);
            }
            delete_option($cob_developer_importer_config['status_option_name']);
            wp_send_json_success(['message' => __( 'Developer import process cancelled and status cleared.', 'cob_theme' )]);
            break;

        case 'get_status':
            $status = get_option($cob_developer_importer_config['status_option_name']);
            if ($status && isset($status['progress']) && $status['progress'] < 100 && !empty($status['original_filename']) && isset($status['total_rows']) && $status['total_rows'] > 0) {
                wp_send_json_success(['status' => $status, 'log' => [sprintf(esc_html__( "Retrieved previous status for file: %s", 'cob_theme' ), $status['original_filename'])]]);
            } else {
                delete_option($cob_developer_importer_config['status_option_name']); // Clear any invalid old status
                wp_send_json_error(['message' => __( 'No resumable developer import process found.', 'cob_theme' )]);
            }
            break;

        default:
            wp_send_json_error(['message' => __( 'Unknown importer action.', 'cob_theme' )]);
    }
}

/**
 * Import or Update a Single Developer Term (AJAX context).
 */
function cob_import_single_developer_ajax($csv_row_data_assoc, $config, $current_row_number_for_log) {
    $taxonomy_slug = $config['taxonomy_slug']; // Should be 'developer'
    $logo_image_meta_key = $config['logo_image_meta_key'];
    $current_import_language = $config['target_language'];
    $csv_column_map = $config['csv_column_map'];

    $log = [];
    $return_status_details = ['status' => 'failed', 'term_id' => null, 'log' => []];

    $source_id_col_name = $csv_column_map['id'] ?? 'id';
    $source_id = $csv_row_data_assoc[$source_id_col_name] ?? null;

    if (empty($source_id)) {
        $log[] = "({$current_row_number_for_log}) <span style='color:red;'>" . sprintf(esc_html__( "Error: Source ID ('%s') is empty. Skipping row.", 'cob_theme' ), esc_html($source_id_col_name)) . "</span>";
        $return_status_details['log'] = $log;
        return $return_status_details;
    }

    $name_col = $csv_column_map['name'] ?? 'name';
    $slug_col = $csv_column_map['slug'] ?? 'slug'; // e.g., 'all_slugs_en' or 'all_slugs_ar'
    $desc_col = $csv_column_map['description'] ?? 'description';
    $logo_url_col = $csv_column_map['logo_url_csv_col'] ?? 'logo_path';

    $term_name        = sanitize_text_field(trim($csv_row_data_assoc[$name_col] ?? 'Unnamed Developer'));
    $term_slug_source = trim($csv_row_data_assoc[$slug_col] ?? '');
    $term_slug        = !empty($term_slug_source) ? sanitize_title($term_slug_source) : sanitize_title($term_name);
    $term_description = wp_kses_post($csv_row_data_assoc[$desc_col] ?? '');
    $logo_image_url   = !empty($csv_row_data_assoc[$logo_url_col] ?? '') ? esc_url_raw(trim($csv_row_data_assoc[$logo_url_col])) : null;

    $wp_term_id = null;
    // For non-hierarchical taxonomies like 'developer' (usually), parent is 0.
    $term_args = ['name' => $term_name, 'slug' => $term_slug, 'description' => $term_description, 'parent' => 0];

    $existing_term = term_exists($term_slug, $taxonomy_slug);
    if (!$existing_term && !empty($term_name)) { // If slug not found, try by name (slug might have been auto-generated differently)
        $existing_term = term_exists($term_name, $taxonomy_slug);
    }


    if ($existing_term && is_array($existing_term) && isset($existing_term['term_id'])) {
        $wp_term_id = $existing_term['term_id'];
        $current_term_obj = get_term($wp_term_id, $taxonomy_slug);
        $needs_update = false;
        if ($current_term_obj && !is_wp_error($current_term_obj)) {
            if ($current_term_obj->name !== $term_name) $needs_update = true;
            if ($current_term_obj->description !== $term_description) $needs_update = true;
            if ($current_term_obj->slug !== $term_slug && !empty($term_slug)) $needs_update = true;
        } else { $needs_update = true; }

        if ($needs_update) {
            $update_result = wp_update_term($wp_term_id, $taxonomy_slug, $term_args);
            if (is_wp_error($update_result)) {
                $log[] = "({$current_row_number_for_log}) <span style='color:orange;'>" . sprintf(esc_html__( "Notice: Developer '%s' (ID: %d) exists, but update failed: %s", 'cob_theme' ), esc_html($term_name), $wp_term_id, esc_html($update_result->get_error_message())) . "</span>";
            } else {
                $log[] = "({$current_row_number_for_log}) <span style='color:#00A86B;'>" . sprintf(esc_html__( "Updated existing developer '%s' (ID: %d).", 'cob_theme' ), esc_html($term_name), $wp_term_id) . "</span>";
                $return_status_details['status'] = 'updated';
            }
        } else {
            $log[] = "({$current_row_number_for_log}) <span style='color:lightblue;'>" . sprintf(esc_html__( "Developer '%s' (ID: %d) exists and data is current. Checking logo.", 'cob_theme' ), esc_html($term_name), $wp_term_id) . "</span>";
            $return_status_details['status'] = 'updated'; // Still counts as processed for meta/image check
        }
    } else {
        $insert_result = wp_insert_term($term_name, $taxonomy_slug, $term_args);
        if (is_wp_error($insert_result)) {
            $log[] = "({$current_row_number_for_log}) <span style='color:red;'>" . sprintf(esc_html__( "Error importing developer '%s': %s", 'cob_theme' ), esc_html($term_name), esc_html($insert_result->get_error_message())) . "</span>";
            $return_status_details['log'] = $log;
            return $return_status_details;
        } else {
            $wp_term_id = $insert_result['term_id'];
            $log[] = "({$current_row_number_for_log}) <span style='color:lightgreen;'>" . sprintf(esc_html__( "Imported new developer '%s' (Slug: %s) as ID: %d.", 'cob_theme' ), esc_html($term_name), esc_html($term_slug), $wp_term_id) . "</span>";
            $return_status_details['status'] = 'imported';
        }
    }
    $return_status_details['term_id'] = $wp_term_id;

    if ($wp_term_id) {
        // Set language for the term if Polylang is active
        if (function_exists('pll_set_term_language') && $current_import_language && $current_import_language !== 'default') {
            pll_set_term_language($wp_term_id, $current_import_language);
        }

        // Handle Logo Image
        if ($logo_image_url && filter_var($logo_image_url, FILTER_VALIDATE_URL)) {
            $existing_logo_id = get_term_meta($wp_term_id, $logo_image_meta_key, true);
            $attachment_id = null;

            if ($existing_logo_id) {
                $existing_url = wp_get_attachment_url($existing_logo_id);
                // A very basic check. If URL is different, try to re-download.
                // For more robustness, one might compare file sizes or hashes if original source files are accessible.
                if ($existing_url !== $logo_image_url) {
                    $attachment_id = media_sideload_image($logo_image_url, 0, $term_name . ' Logo', 'id');
                } else {
                    $attachment_id = $existing_logo_id; // Assume existing is fine
                }
            } else {
                $attachment_id = media_sideload_image($logo_image_url, 0, $term_name . ' Logo', 'id');
            }

            if ($attachment_id && !is_wp_error($attachment_id)) {
                if ($attachment_id != $existing_logo_id) { // Update meta only if ID is new or changed
                    update_term_meta($wp_term_id, $logo_image_meta_key, $attachment_id);
                    $log[] = "({$current_row_number_for_log}) &nbsp;&nbsp;&hookrightarrow; " . sprintf(esc_html__( "Downloaded/Updated and linked logo for '%s' (Attachment ID: %d).", 'cob_theme' ), esc_html($term_name), $attachment_id);
                    if (function_exists('pll_set_post_language') && $current_import_language && $current_import_language !== 'default') {
                        pll_set_post_language($attachment_id, $current_import_language);
                    }
                }
            } elseif (is_wp_error($attachment_id)) {
                $log[] = "({$current_row_number_for_log}) <span style='color:orange;'>&nbsp;&nbsp;&hookrightarrow; " . sprintf(esc_html__( "Failed to download logo for '%s' from %s. Error: %s", 'cob_theme' ), esc_html($term_name), esc_html($logo_image_url), esc_html($attachment_id->get_error_message())) . "</span>";
            }
        } elseif ($logo_image_url) { // URL provided but not valid
            $log[] = "({$current_row_number_for_log}) <span style='color:orange;'>&nbsp;&nbsp;&hookrightarrow; " . sprintf(esc_html__( "Invalid logo URL provided for '%s': %s", 'cob_theme' ), esc_html($term_name), esc_html($logo_image_url)) . "</span>";
        }
    }

    $return_status_details['log'] = $log;
    return $return_status_details;
}
?>
