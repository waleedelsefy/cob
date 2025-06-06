<?php
/**
 * AJAX Handler for Algolia Search & Script Localization.
 *
 * @package cob_theme
 * @since 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue scripts and localize data for the Algolia AJAX search.
 * This function should be hooked into `wp_enqueue_scripts`.
 */
function cob_enqueue_algolia_search_scripts() {
    // This example assumes your main JS file (home.js) is enqueued with the handle 'cob-main-js'.
    // If you've given it a different handle when enqueuing, use that handle here.
    // Make sure your home.js is properly enqueued in your theme's main functions.php or setup file.
    // Example of enqueuing home.js (should be in your theme setup):
    // wp_enqueue_script('cob-main-js', get_template_directory_uri() . '/assets/js/home.js', ['jquery', 'swiper'], '1.0.4', true);

    // This passes PHP variables to JavaScript, making the 'cobAlgoliaAjax' object available.
    wp_localize_script( 'cob-main-js', 'cobAlgoliaAjax', [ // Use the handle of your main theme JS file
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('cob_algolia_ajax_nonce'),
    ]);
}
// This hook ensures that the localization happens correctly when scripts are loaded.
add_action( 'wp_enqueue_scripts', 'cob_enqueue_algolia_search_scripts' );


/**
 * The main AJAX handler for searching with Algolia.
 * Handles the 'cob_algolia_ajax_search' action from the frontend.
 */
function cob_algolia_ajax_search_callback() {
    // 1. Security Check
    check_ajax_referer('cob_algolia_ajax_nonce', 'nonce');

    // 2. Algolia Credentials (Store securely, e.g., in wp-config.php)
    $algolia_app_id = defined('ALGOLIA_APP_ID') ? ALGOLIA_APP_ID : '';
    $algolia_search_api_key = defined('ALGOLIA_SEARCH_KEY') ? ALGOLIA_SEARCH_KEY : '';
    $algolia_index_name = defined('ALGOLIA_INDEX_NAME') ? ALGOLIA_INDEX_NAME : 'properties';

    if ( empty( $algolia_app_id ) || empty( $algolia_search_api_key ) ) {
        wp_send_json_error(['message' => 'Algolia credentials are not configured on the server.']);
        return;
    }

    // 3. Initialize Algolia Client
    try {
        if (!class_exists('\Algolia\AlgoliaSearch\SearchClient')) {
            wp_send_json_error(['message' => 'Algolia PHP client library not found. Please install via Composer.']);
            return;
        }
        $client = \Algolia\AlgoliaSearch\SearchClient::create( $algolia_app_id, $algolia_search_api_key );
        $index = $client->initIndex( $algolia_index_name );
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Failed to initialize Algolia client: ' . $e->getMessage()]);
        return;
    }

    // 4. Get Search Parameters from POST request
    $query = isset( $_POST['cob_algolia_query'] ) ? sanitize_text_field( $_POST['cob_algolia_query'] ) : '';
    $page = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) - 1 : 0; // Algolia pages are 0-indexed
    $hits_per_page = 10;

    // 5. Build Algolia Filters based on form input
    $filters = [];
    if (!empty($_POST['filter_property_type'])) {
        $filters[] = 'taxonomies.type:"' . sanitize_text_field($_POST['filter_property_type']) . '"';
    }
    if (!empty($_POST['filter_resale']) && $_POST['filter_resale'] === '1') {
        $filters[] = 'resale = 1';
    }
    // Handle numeric filters
    if (!empty($_POST['filter_bedrooms'])) {
        $bedrooms = sanitize_text_field($_POST['filter_bedrooms']);
        if ($bedrooms === '6+') { $filters[] = 'bedrooms >= 6'; }
        elseif (is_numeric($bedrooms)) { $filters[] = 'bedrooms = ' . (int)$bedrooms; }
    }
    if (!empty($_POST['filter_bathrooms'])) {
        $bathrooms = sanitize_text_field($_POST['filter_bathrooms']);
        if ($bathrooms === '5+') { $filters[] = 'bathrooms >= 5'; }
        elseif (is_numeric($bathrooms)) { $filters[] = 'bathrooms = ' . (int)$bathrooms; }
    }
    // NOTE: Area and Price filters need the attributes in Algolia to be configured as numeric for range filtering.
    // if (!empty($_POST['filter_area'])) { ... handle area ranges ... }
    // if (!empty($_POST['filter_price'])) { ... handle price ranges ... }

    $search_options = [ 'hitsPerPage' => $hits_per_page, 'page' => $page ];
    if (!empty($filters)) {
        $search_options['filters'] = implode(' AND ', $filters);
    }

    // 6. Perform the Search
    try {
        $results = $index->search( $query, $search_options );
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Error during Algolia search: ' . $e->getMessage()]);
        return;
    }

    // 7. Format Results into HTML
    $hits_html = '';
    $default_image = get_template_directory_uri() . '/assets/imgs/default.jpg';

    if ( isset( $results['hits'] ) && count( $results['hits'] ) > 0 ) {
        ob_start();
        foreach ( $results['hits'] as $hit ) {
            $link = !empty($hit['permalink']) ? esc_url($hit['permalink']) : '#';
            $title = !empty($hit['post_title']) ? esc_html($hit['post_title']) : __('Untitled', 'cob_theme');
            $image_url = !empty($hit['image']) ? esc_url($hit['image']) : $default_image;
            $compound_name = $hit['taxonomies']['compound'][0] ?? '';
            $city_name = $hit['taxonomies']['city'][0] ?? '';
            ?>
            <div class="algolia-hit-card">
                <a href="<?php echo $link; ?>" class="hit-link">
                    <div class="hit-image-container"><img src="<?php echo $image_url; ?>" alt="<?php echo $title; ?>" loading="lazy"></div>
                    <div class="hit-content">
                        <h4 class="hit-title"><?php echo $title; ?></h4>
                        <?php if ($compound_name): ?><p class="hit-compound"><?php echo esc_html($compound_name); ?></p><?php endif; ?>
                        <?php if ($city_name): ?><p class="hit-city"><?php echo esc_html($city_name); ?></p><?php endif; ?>
                    </div>
                </a>
            </div>
            <?php
        }
        $hits_html = ob_get_clean();
    }

    // 8. Generate Pagination
    $pagination_html = '';
    if ( isset( $results['nbPages'] ) && $results['nbPages'] > 1 ) {
        $pagination_html = paginate_links( [
            'base'      => '#', // Base is irrelevant as JS handles the click
            'format'    => '?paged=%#%',
            'total'     => $results['nbPages'],
            'current'   => $page + 1,
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
        ] );
    }

    // 9. Send JSON Response
    wp_send_json_success([
        'hits_html'       => $hits_html,
        'pagination_html' => $pagination_html,
        'query_details'   => $results,
    ]);
}
add_action('wp_ajax_cob_algolia_ajax_search', 'cob_algolia_ajax_search_callback');
add_action('wp_ajax_nopriv_cob_algolia_ajax_search', 'cob_algolia_ajax_search_callback');
