<?php
/**
 * Capital of Business Theme Functions
 *
 * @package Capital_of_Business
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'after_setup_theme', function() {
    load_theme_textdomain( 'cob_theme', get_template_directory() . '/languages' );
} );

$autoload_paths = [
    'inc/post-types/*.php',
    'inc/ajax/*.php',
    'inc/metaboxes/*.php',
    'inc/search/*.php',
    'inc/theme-setup/*.php',
    'inc/contact-forms/*.php',
    'inc/city/*.php',
    'inc/developer/*.php',
    'inc/jobs/*.php',
    'inc/properties/*.php',
    'inc/real-estate-expert/*.php',
    'inc/transients/*.php',
    'inc/importer/cob-importer.php'
];

foreach ( $autoload_paths as $path ) {
    foreach ( glob( get_template_directory() . '/' . $path ) as $file ) {
        require_once $file;
    }
}

add_action( 'init', function() {
    foreach ( glob( get_template_directory() . '/inc/redux/settings/*.php' ) as $file ) {
        require_once $file;
    }
    foreach ( glob( get_template_directory() . '/inc/redux/settings/child/*.php' ) as $file ) {
        require_once $file;
    }
} );

// 1a) Enqueue our AJAX script and localize data
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'cob-factory-ajax',
        get_template_directory_uri() . '/assets/js/factory-ajax.js',
        [ 'jquery' ],
        '1.0',
        true
    );

    // Pass ajax_url, nonce and current city slug to JS
    $city = ( get_queried_object() && ! is_wp_error( get_queried_object() ) )
        ? get_queried_object()->slug
        : '';

    wp_localize_script( 'cob-factory-ajax', 'cobFactory',
        [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'cob_factory_nonce' ),
            'city'     => $city,
        ]
    );
} );

// 1b) AJAX callback: renders the properties loop + pagination
function cob_ajax_load_properties() {
    check_ajax_referer( 'cob_factory_nonce', 'nonce' );

    $paged = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
    $city  = sanitize_text_field( $_POST['city'] );

    $args = [
        'post_type'      => 'properties',
        'posts_per_page' => 6,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => [
            [
                'taxonomy' => 'city',
                'field'    => 'slug',
                'terms'    => $city,
            ],
        ],
    ];
    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/single/properties-card' );
        }
        wp_reset_postdata();
    } else {
        echo '<p>' . esc_html__( 'There are no posts currently available', 'cob_theme' ) . '</p>';
    }
    $html = ob_get_clean();

    // regenerate pagination
    $big  = 999999999;
    $pages = paginate_links( [
        'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'    => '?paged=%#%',
        'current'   => $paged,
        'total'     => $query->max_num_pages,
        'prev_text' => esc_html__( 'Previous', 'cob_theme' ),
        'next_text' => esc_html__( 'Next', 'cob_theme' ),
        'type'      => 'list',
        'end_size'  => 1,
        'mid_size'  => 2,
    ] );

    wp_send_json_success( [
        'cards'      => $html,
        'pagination' => $pages,
    ] );
}
add_action( 'wp_ajax_cob_load_properties', 'cob_ajax_load_properties' );
add_action( 'wp_ajax_nopriv_cob_load_properties', 'cob_ajax_load_properties' );
