<?php
/**
 * Capital of Business Theme Functions - Improved Version
 *
 * This file contains theme functions for the "Capital of Business" theme.
 * It has been improved for better maintainability and supports child themes.
 *
 * @package Capital_of_Business
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Retrieve asset version using the file's modification time (cache busting).
 *
 * @param string $file_path The path to the asset file.
 * @return string|false The file modification time or false if file does not exist.
 */
if ( ! function_exists( 'cob_get_asset_version' ) ) {
    function cob_get_asset_version( $file_path ) {
        return file_exists( $file_path ) ? filemtime( $file_path ) : false;
    }
}

/**
 * Enqueue theme styles and scripts.
 */
if ( ! function_exists( 'cob_enqueue_assets' ) ) {
    function cob_enqueue_assets() {
        // Support for child theme: use get_stylesheet_directory_uri() instead of get_template_directory_uri()
        $theme_uri = get_stylesheet_directory_uri();
        $theme_dir = get_stylesheet_directory();

        // Enqueue base CSS files.
        wp_enqueue_style( 'normalize', $theme_uri . '/assets/css/normalize.css', array(), '8.0.1' );
        wp_enqueue_style( 'footer', $theme_uri . '/assets/css/footer.css', array(), '8.0.1' );
        wp_enqueue_style( 'header', $theme_uri . '/assets/css/header.css', array(), '8.0.1' );
        if ( is_rtl() ) {
            wp_enqueue_style( 'rtl', $theme_uri . '/assets/css/rtl.css', array(), '8.0.1' );
        }

        // Enqueue Bootstrap CSS from theme files.
        // wp_enqueue_style( 'bootstrap', $theme_uri . '/assets/bootstrap4/css/bootstrap.min.css', array(), '4.6.0' );

        // Enqueue Swiper Slider CSS from CDN.
        wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0' );

        // Enqueue FontAwesome CSS from theme files.
        wp_enqueue_style( 'font-awesome', $theme_uri . '/assets/css/fonts/fontawesome/css/all.css', array(), '6.4.2' );

        // Enqueue custom theme CSS files.
	    $css_files = array(
            'animate'      => 'assets/css/animate.css',
            'contact'      => 'assets/css/contact.css',
            'areas'        => 'assets/css/areas.css',
            'developers'   => 'assets/css/motawren-det.css',
            'article-name' => 'assets/css/article-name.css',
            'articles'     => 'assets/css/articels.css',
            'factorys'     => 'assets/css/factorys.css',
            'developer'    => 'assets/css/motawren.css',
            'projects'     => 'assets/css/projects.css',
            'services'     => 'assets/css/services.css',
            'factory-det'  => 'assets/css/factory-det.css',
            'flat-det'     => 'assets/css/flat-det.css',
            'hiring'       => 'assets/css/hiring.css',
            'we'           => 'assets/css/we.css',
            'city'         => 'assets/css/city.css',
            'home'         => 'assets/css/home.css',
        );
	    if ( ! is_robots() ) {
		    foreach ( $css_files as $handle => $relative_path ) {
			    $file_path = $theme_dir . '/' . $relative_path;
			    if ( file_exists( $file_path ) ) {
				    $version = cob_get_asset_version( $file_path );
				    wp_enqueue_style( "cob-{$handle}", $theme_uri . '/' . $relative_path, array(), $version );
			    }
		    }
	    }


        // Enqueue the main theme stylesheet.
        $style_path = $theme_dir . '/style.css';
        if ( file_exists( $style_path ) ) {
            $version = cob_get_asset_version( $style_path );
            wp_enqueue_style( 'cob-style', get_stylesheet_uri(), array(), $version );
        }

        // Enqueue jQuery (bundled with WordPress).
        wp_enqueue_script( 'jquery' );

        // Enqueue Bootstrap Bundle JS (includes Popper.js) from CDN.
        // wp_enqueue_script( 'bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array( 'jquery' ), '5.3.2', true );

        // Enqueue Swiper JS from CDN.
        wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );

        // Enqueue LazyLoad JS from CDN.
        wp_enqueue_script( 'lazyload', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js', array(), '5.3.2', true );

        // Enqueue Floating UI JS if the file exists.
        $floating_ui_path = $theme_dir . '/assets/js/floating-ui.dom.umd.js';
        if ( file_exists( $floating_ui_path ) ) {
            $version = cob_get_asset_version( $floating_ui_path );
            wp_enqueue_script( 'floating-ui', $theme_uri . '/assets/js/floating-ui.dom.umd.js', array( 'jquery' ), $version ? $version : '1.0.0', true );
        }

        // Enqueue the main theme JS if the file exists.
        $main_js_path = $theme_dir . '/assets/js/main.js';
        if ( file_exists( $main_js_path ) ) {
            $version = cob_get_asset_version( $main_js_path );
            wp_enqueue_script( 'main-js', $theme_uri . '/assets/js/main.js', array( 'jquery', 'bootstrap-js' ), $version, true );
        }

        // Enqueue FontAwesome JS from theme files.
        wp_enqueue_script( 'fontawesome', $theme_uri . '/assets/css/fonts/fontawesome/js/all.js', array(), null, true );
    }
}
add_action( 'wp_enqueue_scripts', 'cob_enqueue_assets' );

/**
 * Remove unwanted default styles (Redux Framework).
 */
if ( ! function_exists( 'cob_remove_redux_styles' ) ) {
    function cob_remove_redux_styles() {
        wp_dequeue_style( 'redux-admin-css' );      // Main Redux styles.
        wp_dequeue_style( 'redux-extension-css' );   // Redux extension styles.
    }
}
add_action( 'wp_enqueue_scripts', 'cob_remove_redux_styles', 100 );

/**
 * Enqueue Google Fonts.
 */
if ( ! function_exists( 'cob_enqueue_google_fonts' ) ) {
    function cob_enqueue_google_fonts() {
        wp_enqueue_style(
            'google-fonts',
            'https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Cairo:wght@200..1000&display=swap',
            array(),
            null
        );
    }
}
add_action( 'wp_enqueue_scripts', 'cob_enqueue_google_fonts' );

/**
 * Enqueue job application scripts for designated pages.
 */
if ( ! function_exists( 'enqueue_job_application_script' ) ) {
    function enqueue_job_application_script() {
        if ( is_page_template( 'page-templates/hiring-page.php' ) ) {
            // Enqueue job application script.
            wp_enqueue_script( 'job-application', get_stylesheet_directory_uri() . '/assets/js/job-application.js', array( 'jquery' ), '1.0', true );
            wp_localize_script( 'job-application', 'myAjax', array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
            ) );
            // Enqueue projects script.
            wp_enqueue_script( 'projects', get_stylesheet_directory_uri() . '/assets/js/projects.js', array( 'jquery' ), '1.0', true );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_job_application_script' );

add_action('wp_enqueue_scripts', 'cob_enqueue_leaflet_map_script');
function cob_enqueue_leaflet_map_script() {
    $should_enqueue = false;

    if ( is_singular('properties') ) {
        $should_enqueue = true;
    }

    if ( is_tax('compound') || is_tax('city') ) {
        $should_enqueue = true;
    }

    if ( $should_enqueue ) {
        wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
        wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), null, true);
    }
}
