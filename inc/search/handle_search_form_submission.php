<?php
// Handle the search form submissions.
function handle_search_form_submission() {
    // Check if a POST request was made and the nonce is valid.
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['search_nonce'] ) && wp_verify_nonce( $_POST['search_nonce'], 'save_search' ) ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'search'; // Ensure this table exists.

        // Determine which form was submitted.
        $search_type = sanitize_text_field( $_POST['search_type'] );

        if ( 'basic' === $search_type ) {
            // Retrieve and sanitize basic search fields.
            $keyword   = isset( $_POST['basic_search_keyword'] ) ? sanitize_text_field( $_POST['basic_search_keyword'] ) : '';
            $propertie_type = isset( $_POST['basic_propertie_type'] ) ? sanitize_text_field( $_POST['basic_propertie_type'] ) : '';
            $bedrooms  = isset( $_POST['basic_bedrooms'] ) ? intval( $_POST['basic_bedrooms'] ) : 0;
            $price     = isset( $_POST['basic_price'] ) ? sanitize_text_field( $_POST['basic_price'] ) : '';

            $data = array(
                'search_type' => $search_type,
                'keyword'     => $keyword,
                'propertie_type'   => $propertie_type,
                'bedrooms'    => $bedrooms,
                'price'       => $price,
                'date'        => current_time( 'mysql' ),
            );

            $wpdb->insert( $table_name, $data );

        } elseif ( 'detailed' === $search_type ) {
            // Retrieve and sanitize detailed search fields.
            $keyword    = isset( $_POST['detailed_keyword'] ) ? sanitize_text_field( $_POST['detailed_keyword'] ) : '';
            $propertie_type  = isset( $_POST['detailed_propertie_type'] ) ? sanitize_text_field( $_POST['detailed_propertie_type'] ) : '';
            $bedrooms   = isset( $_POST['detailed_bedrooms'] ) ? intval( $_POST['detailed_bedrooms'] ) : 0;
            $price_from = isset( $_POST['price_from'] ) ? floatval( $_POST['price_from'] ) : 0;
            $price_to   = isset( $_POST['price_to'] ) ? floatval( $_POST['price_to'] ) : 0;

            $data = array(
                'search_type' => $search_type,
                'keyword'     => $keyword,
                'propertie_type'   => $propertie_type,
                'bedrooms'    => $bedrooms,
                'price_from'  => $price_from,
                'price_to'    => $price_to,
                'date'        => current_time( 'mysql' ),
            );

            $wpdb->insert( $table_name, $data );
        }
    }
}
add_action( 'init', 'handle_search_form_submission' );
