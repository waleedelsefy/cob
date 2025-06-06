<?php
/**
 * PHP Functions for Algolia AJAX Search Functionality.
 * Place this code in your theme's functions.php or a relevant include file.
 */

// Make sure to define $default_slider_img_path globally or pass it to the AJAX handler if needed in hit generation there.
// For simplicity, if your hit generation in PHP needs a default image, you might redefine it or fetch it from an option.
// $theme_dir_for_ajax = get_template_directory_uri();
// $default_slider_img_path_for_ajax = $theme_dir_for_ajax . '/assets/imgs/landing.jpg';


if ( ! function_exists( 'cob_enqueue_algolia_ajax_search_scripts' ) ) {
    /**
     * Enqueues scripts and localizes data for the Algolia AJAX search.
     * This function passes necessary PHP variables (like AJAX URL and nonce) to the frontend JavaScript.
     */
    function cob_enqueue_algolia_ajax_search_scripts() {
        // Only enqueue these scripts on pages where the Algolia AJAX search form is present.
        // Replace 'your_page_template_slug.php' with the actual template file name or use other conditions.
        // The original template name was 'Landing & Search Template'. Let's assume its slug is 'page-templates/landing-search-template.php'.
        if ( is_page_template( 'page-templates/landing-search-template.php' ) ) {

            // If your main Algolia AJAX JavaScript is in a separate file (e.g., assets/js/cob-algolia-ajax.js),
            // you would enqueue it here. The JavaScript provided in the template is inline.
            // If it remains inline, you might not need to enqueue a specific JS file here for the AJAX logic itself,
            // but you still need wp_localize_script to pass PHP variables to ANY script that's already enqueued
            // and will be present on the page (like jQuery or a main theme script).
            // For this example, let's assume 'jquery' is enqueued and available for localization.

            wp_localize_script( 'jquery', 'cobAlgoliaAjax', [ // Localizing to 'jquery' as it's a common dependency.
                // Replace 'jquery' with your actual script handle if you have a dedicated JS file for this.
                'ajax_url' => admin_url( 'admin-ajax.php' ), // WordPress AJAX URL
                'nonce'    => wp_create_nonce( 'cob_algolia_ajax_search_nonce' ), // Nonce for security
                // You can also pass other translated strings or settings here if needed by your JS
                'loading_text' => esc_js(__('Loading results...', 'cob_theme')),
                'no_results_text' => esc_js(__('No properties found matching your criteria.', 'cob_theme')),
                'error_text' => esc_js(__('An error occurred while searching. Please try again.', 'cob_theme')),
                'price_text' => esc_js(__('Price', 'cob_theme')),
                'egp_text' => esc_js(__('EGP', 'cob_theme')),
                'bedrooms_text' => esc_js(__('Bedrooms', 'cob_theme')),
                'price_on_request_text' => esc_js(__('Price on request', 'cob_theme')),
                'na_text' => esc_js(__('N/A', 'cob_theme')),
                'untitled_property_text' => esc_js(__('Untitled Property', 'cob_theme')),
            ]);
        }
    }
    add_action( 'wp_enqueue_scripts', 'cob_enqueue_algolia_ajax_search_scripts' );
}


if ( ! function_exists( 'cob_handle_algolia_ajax_search_request' ) ) {
    /**
     * WordPress AJAX Handler for Algolia Search.
     *
     * This function receives search parameters via AJAX, queries Algolia using the PHP client,
     * and returns the search results (hits and pagination) as JSON.
     */
    add_action( 'wp_ajax_cob_algolia_ajax_search', 'cob_handle_algolia_ajax_search_request' );
    add_action( 'wp_ajax_nopriv_cob_algolia_ajax_search', 'cob_handle_algolia_ajax_search_request' ); // For non-logged-in users

    function cob_handle_algolia_ajax_search_request() {
        // 1. Verify the nonce for security.
        // The first parameter is the nonce action, the second is the key in $_POST/$_REQUEST for the nonce value.
        check_ajax_referer( 'cob_algolia_ajax_search_nonce', 'nonce' );

        // --- IMPORTANT: Algolia PHP Client Setup ---
        // Ensure you have the Algolia PHP client library installed (e.g., via Composer)
        // and autoloaded. For example:
        // if ( ! class_exists('\Algolia\AlgoliaSearch\SearchClient') && file_exists( get_template_directory() . '/vendor/autoload.php') ) {
        //    require_once get_template_directory() . '/vendor/autoload.php';
        // }

        // Replace with your Algolia credentials.
        // SECURITY BEST PRACTICE: Store these in wp-config.php as constants, or in WordPress options,
        // NOT directly in the theme code if it's public.
        $algolia_app_id = defined('ALGOLIA_APP_ID') ? ALGOLIA_APP_ID : 'VPPO9FLNT3'; // Fallback for example
        $algolia_admin_key = defined('ALGOLIA_ADMIN_KEY') ? ALGOLIA_ADMIN_KEY : 'YOUR_ALGOLIA_ADMIN_API_KEY_HERE'; // KEEP THIS SECRET
        $algolia_index_name = defined('ALGOLIA_INDEX_NAME') ? ALGOLIA_INDEX_NAME : 'properties'; // Fallback for example

        if ( 'YOUR_ALGOLIA_ADMIN_API_KEY_HERE' === $algolia_admin_key || empty($algolia_app_id) ) {
            wp_send_json_error(['message' => __('Algolia credentials are not configured correctly on the server.', 'cob_theme')]);
            return;
        }

        // Initialize Algolia client
        try {
            if (!class_exists('\Algolia\AlgoliaSearch\SearchClient')) {
                throw new Exception(esc_html__('Algolia PHP client class not found. Please ensure it is installed and autoloaded.', 'cob_theme'));
            }
            $client = \Algolia\AlgoliaSearch\SearchClient::create( $algolia_app_id, $algolia_admin_key );
            $index = $client->initIndex( $algolia_index_name );
        } catch (Exception $e) {
            wp_send_json_error(['message' => esc_html__('Algolia client initialization error: ', 'cob_theme') . esc_html($e->getMessage())]);
            return;
        }

        // Sanitize and retrieve search parameters from POST request
        $query_string = isset( $_POST['cob_algolia_query'] ) ? sanitize_text_field( wp_unslash( $_POST['cob_algolia_query'] ) ) : '';
        $paged        = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
        $hits_per_page = 6; // Number of results per page, should match your frontend intentions

        // Build Algolia search options (filters, pagination, etc.)
        $search_options = [
            'hitsPerPage' => $hits_per_page,
            'page'        => $paged - 1, // Algolia pages are 0-indexed
            // Examples of other useful search parameters:
            'attributesToSnippet' => ['description:20'], // Snippet the 'description' attribute to 20 words
            'attributesToHighlight' => ['title', 'compound_name', 'location_name'], // Attributes to highlight matching query terms
            // 'typoTolerance' => 'min', // Be more tolerant to typos
        ];

        $filters_array = []; // Array to hold all filter strings for Algolia

        // Property Type Filter (taxonomy 'type')
        if ( ! empty( $_POST['filter_property_type'] ) ) {
            $filters_array[] = 'type:"' . sanitize_text_field( wp_unslash( $_POST['filter_property_type'] ) ) . '"'; // Assuming 'type' is a string attribute
        }

        // Bedrooms Filter (meta_key '_cob_bedrooms' or Algolia attribute 'bedrooms')
        if ( ! empty( $_POST['filter_bedrooms'] ) ) {
            $bedrooms_value = sanitize_text_field( wp_unslash( $_POST['filter_bedrooms'] ) );
            if ( $bedrooms_value === '6+' ) {
                $filters_array[] = 'bedrooms >= 6'; // Numeric comparison
            } else {
                $filters_array[] = 'bedrooms = ' . absint( $bedrooms_value ); // Numeric comparison
            }
        }

        // Bathrooms Filter (meta_key '_cob_bathrooms' or Algolia attribute 'bathrooms')
        if ( ! empty( $_POST['filter_bathrooms'] ) ) {
            $bathrooms_value = sanitize_text_field( wp_unslash( $_POST['filter_bathrooms'] ) );
            if ( $bathrooms_value === '5+' ) {
                $filters_array[] = 'bathrooms >= 5'; // Numeric comparison
            } else {
                $filters_array[] = 'bathrooms = ' . absint( $bathrooms_value ); // Numeric comparison
            }
        }

        // Area Filter (meta_key '_cob_min_unit_area' or Algolia attribute 'min_unit_area')
        if ( ! empty( $_POST['filter_area'] ) ) {
            $area_range = sanitize_text_field( wp_unslash( $_POST['filter_area'] ) );
            if ( strpos( $area_range, '-' ) !== false ) {
                list( $min_area, $max_area ) = explode( '-', $area_range );
                $filters_array[] = 'min_unit_area >= ' . absint( $min_area );
                $filters_array[] = 'min_unit_area <= ' . absint( $max_area );
            } elseif ( strpos( $area_range, '+' ) !== false ) {
                $min_area = str_replace( '+', '', $area_range );
                $filters_array[] = 'min_unit_area >= ' . absint( $min_area );
            }
        }

        // Price Filter (meta_key '_cob_price' or Algolia attribute 'price')
        if ( ! empty( $_POST['filter_price'] ) ) {
            $price_range = sanitize_text_field( wp_unslash( $_POST['filter_price'] ) );
            if ( strpos( $price_range, '-' ) !== false ) {
                list( $min_price, $max_price ) = explode( '-', $price_range );
                $filters_array[] = 'price >= ' . absint( $min_price );
                $filters_array[] = 'price <= ' . absint( $max_price );
            } elseif ( strpos( $price_range, '+' ) !== false ) {
                $min_price = str_replace( '+', '', $price_range );
                $filters_array[] = 'price >= ' . absint( $min_price );
            }
        }

        // Resale Filter (Algolia attribute 'resale' - boolean or specific string/number)
        if ( isset( $_POST['filter_resale'] ) && $_POST['filter_resale'] === '1' ) {
            $filters_array[] = 'resale:true'; // Assumes 'resale' is a boolean attribute in Algolia. Adjust if it's numeric (e.g., 'resale = 1').
        }

        // Combine all filters with AND logic if multiple filters are applied
        if ( ! empty( $filters_array ) ) {
            $search_options['filters'] = implode( ' AND ', $filters_array );
        }

        // Perform the search using the Algolia PHP client
        try {
            $results = $index->search( $query_string, $search_options );
        } catch (Exception $e) {
            wp_send_json_error(['message' => esc_html__('Algolia search operation error: ', 'cob_theme') . esc_html($e->getMessage())]);
            return;
        }

        // Prepare HTML for search results (hits)
        $hits_html = '';
        // Define default image path for AJAX context if not available globally
        $default_img_for_ajax = get_template_directory_uri() . '/assets/imgs/landing.jpg';

        if ( ! empty( $results['hits'] ) ) {
            foreach ( $results['hits'] as $hit ) {
                // Sanitize and prepare data for display
                $title = isset($hit['_highlightResult']['title']['value']) ? $hit['_highlightResult']['title']['value'] : (isset($hit['title']) ? esc_html($hit['title']) : __('Untitled Property', 'cob_theme'));
                $location = isset($hit['location']) ? (isset($hit['_snippetResult']['location']['value']) ? $hit['_snippetResult']['location']['value'] : esc_html($hit['location'])) : '';
                $price_display = isset($hit['price']) ? number_format_i18n(floatval($hit['price'])) . ' ' . __('EGP', 'cob_theme') : __('Price on request', 'cob_theme');
                $bedrooms_display = isset($hit['bedrooms']) ? esc_html($hit['bedrooms']) : __('N/A', 'cob_theme');
                $thumbnail = isset($hit['thumbnail_url']) ? esc_url($hit['thumbnail_url']) : esc_url($default_img_for_ajax);
                $permalink = isset($hit['permalink']) ? esc_url($hit['permalink']) : '#';

                // Build HTML for each hit item - ensure classes match your theme's styling
                $hits_html .= '<div class="property-hit-card">'; // Adjust class as needed
                $hits_html .= '  <a href="' . $permalink . '" class="property-hit-link">';
                $hits_html .= '    <div class="property-hit-image-container"><img src="' . $thumbnail . '" alt="' . esc_attr(strip_tags(isset($hit['title']) ? $hit['title'] : 'Property')) . '" width="300" height="200" loading="lazy"/></div>';
                $hits_html .= '    <div class="property-hit-content">';
                $hits_html .= '      <h4 class="property-hit-title">' . $title . '</h4>'; // $title is already highlighted/escaped
                if ($location) {
                    $hits_html .= '  <p class="property-hit-location">' . $location . '</p>'; // $location is already snippeted/escaped
                }
                $hits_html .= '      <p class="property-hit-price">' . esc_html__('Price', 'cob_theme') . ': ' . $price_display . '</p>';
                $hits_html .= '      <p class="property-hit-bedrooms">' . esc_html__('Bedrooms', 'cob_theme') . ': ' . $bedrooms_display . '</p>';
                $hits_html .= '    </div>';
                $hits_html .= '  </a>';
                $hits_html .= '</div>';
            }
        } else {
            $hits_html = '<p class="no-results-found">' . esc_html__( 'No properties found matching your criteria.', 'cob_theme' ) . '</p>';
        }

        // Prepare pagination HTML (basic server-side generated example)
        $pagination_html = '';
        if ( isset( $results['nbPages'] ) && $results['nbPages'] > 1 ) {
            $pagination_html .= '<nav class="algolia-ajax-pagination"><ul class="page-numbers">'; // Use WordPress standard class for pagination
            for ( $i = 1; $i <= $results['nbPages']; $i++ ) {
                $current_class = ( $i == $paged ) ? 'current' : '';
                // data-page attribute will be used by JavaScript to trigger new AJAX request for that page
                $pagination_html .= '<li><a class="page-numbers ' . $current_class . '" href="#" data-page="' . esc_attr( $i ) . '">' . esc_html( number_format_i18n( $i ) ) . '</a></li>';
            }
            $pagination_html .= '</ul></nav>';
        }

        // Send the JSON response back to JavaScript
        wp_send_json_success( [
            'hits_html'       => $hits_html,
            'pagination_html' => $pagination_html,
            'total_hits'      => isset( $results['nbHits'] ) ? $results['nbHits'] : 0,
            'total_pages'     => isset( $results['nbPages'] ) ? $results['nbPages'] : 0,
            'current_page'    => $paged, // The page number that was requested
            'query_sent'      => $query_string, // For debugging: what query was sent to Algolia
            'filters_applied' => isset($search_options['filters']) ? $search_options['filters'] : 'None', // For debugging
        ] );
    }
}
?>
