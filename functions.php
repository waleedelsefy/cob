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
        'inc/*.php',
        'inc/*/*.php',
        'inc/*/*/*.php',

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

// In functions.php (or a core theme include file)
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}
