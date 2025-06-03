<?php
function ase_create_search_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'search_queries';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        query text NOT NULL,
        count mediumint(9) DEFAULT 1 NOT NULL,
        last_searched datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id),
        KEY query (query(191))
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'ase_create_search_table' );

function ase_store_search_query( $search_query ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'search_queries';

    $existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table_name WHERE query = %s", $search_query ) );
    if ( $existing ) {
        $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET count = count + 1, last_searched = NOW() WHERE id = %d", $existing ) );
    } else {
        $wpdb->insert(
            $table_name,
            array(
                'query'         => $search_query,
                'count'         => 1,
                'last_searched' => current_time( 'mysql' )
            )
        );
    }
}

function ase_handle_search_logging() {
    if ( is_search() && isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
        ase_store_search_query( sanitize_text_field( $_GET['s'] ) );
    }
}
add_action( 'wp', 'ase_handle_search_logging' );

function ase_search_suggestions_from_db() {
    global $wpdb;
    $term = isset( $_GET['term'] ) ? sanitize_text_field( $_GET['term'] ) : '';
    $suggestions = array();

    if ( ! empty( $term ) ) {
        $table_name = $wpdb->prefix . 'search_queries';
        $results = $wpdb->get_results( $wpdb->prepare( "SELECT query FROM $table_name WHERE query LIKE %s ORDER BY count DESC LIMIT 5", '%' . $wpdb->esc_like( $term ) . '%' ) );
        if ( $results ) {
            foreach ( $results as $row ) {
                $suggestions[] = $row->query;
            }
        }
    }
    wp_send_json( $suggestions );
}
add_action( 'wp_ajax_nopriv_ase_search_suggestions_from_db', 'ase_search_suggestions_from_db' );
add_action( 'wp_ajax_ase_search_suggestions_from_db', 'ase_search_suggestions_from_db' );

function cob_modify_search_query( $query ) {
    if ( $query->is_search() && $query->is_main_query() && ! is_admin() ) {
        $query->set( 'posts_per_page', 6 );
        $query->set( 'post_type', 'properties' );
    }
}
add_action( 'pre_get_posts', 'cob_modify_search_query' );

function ase_record_page_view() {
    if ( is_single() ) {
        global $post, $wpdb;
        $table_name = $wpdb->prefix . 'page_views';
        $post_id = $post->ID;
        $current_time = current_time( 'mysql' );

        $existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table_name WHERE post_id = %d", $post_id ) );
        if ( $existing ) {
            $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET views = views + 1, last_viewed = %s WHERE post_id = %d", $current_time, $post_id ) );
        } else {
            $wpdb->insert(
                $table_name,
                array(
                    'post_id'     => $post_id,
                    'views'       => 1,
                    'last_viewed' => $current_time
                )
            );
        }
    }
}
add_action( 'wp', 'ase_record_page_view' );

function ase_search_suggestions_from_views() {
    global $wpdb;
    $term = isset( $_GET['term'] ) ? sanitize_text_field( $_GET['term'] ) : '';
    $suggestions = array();

    if ( ! empty( $term ) ) {
        $results = $wpdb->get_results( $wpdb->prepare( "
            SELECT p.ID, p.post_title, pv.views
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->prefix}page_views pv ON p.ID = pv.post_id
            WHERE p.post_type = 'properties'
            AND p.post_status = 'publish'
            AND p.post_title LIKE %s
            ORDER BY IFNULL(pv.views, 0) DESC
            LIMIT 5
        ", '%' . $wpdb->esc_like( $term ) . '%' ) );
        if ( $results ) {
            foreach ( $results as $row ) {
                $suggestions[] = $row->post_title;
            }
        }
    }
    wp_send_json( $suggestions );
}
add_action( 'wp_ajax_nopriv_ase_search_suggestions_from_views', 'ase_search_suggestions_from_views' );
add_action( 'wp_ajax_ase_search_suggestions_from_views', 'ase_search_suggestions_from_views' );
