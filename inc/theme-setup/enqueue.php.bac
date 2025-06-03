<?php
/**
 * Capital of Business Theme Functions - Improved Version with Mix Manifest Support
 *
 * This file contains theme functions for the "Capital of Business" theme,
 * incorporating Laravel Mix output support.
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
 * Retrieve the URL for an asset compiled by Laravel Mix.
 *
 * This function reads the mix-manifest.json file located in the specified manifest directory.
 *
 * @param string $path The asset path relative to theme root (e.g., '/assets/css/app.css').
 * @param string $manifestDirectory The directory where mix-manifest.json is located (default: '/dist').
 * @return string The URL to the asset with cache busting, or fallback to original path if not found.
 */
if ( ! function_exists( 'mix_asset' ) ) {
	function mix_asset( $path, $manifestDirectory = '/dist' ) {
		static $manifest;

		$manifestPath = get_template_directory() . $manifestDirectory . '/mix-manifest.json';

		if ( ! $manifest ) {
			if ( file_exists( $manifestPath ) ) {
				$manifest = json_decode( file_get_contents( $manifestPath ), true );
			} else {
				// If mix-manifest.json is not available, fallback to the original path.
				return get_template_directory_uri() . $path;
			}
		}

		// Ensure the path starts with a forward slash.
		$path = '/' . ltrim( $path, '/' );

		if ( array_key_exists( $path, $manifest ) ) {
			return get_template_directory_uri() . $manifestDirectory . $manifest[ $path ];
		}

		// If not found in the manifest, return the original asset URL.
		return get_template_directory_uri() . $path;
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

		// For local assets, we assume the compiled outputs are in /dist.
		$manifestDir = '/dist';

		// Enqueue base CSS files using mix_asset so that if compiled versions exist in /dist, they are used.
		wp_enqueue_style( 'normalize', mix_asset( '/assets/css/normalize.css', $manifestDir ), array(), null );
		wp_enqueue_style( 'footer', mix_asset( '/assets/css/footer.css', $manifestDir ), array(), null );
		wp_enqueue_style( 'header', mix_asset( '/assets/css/header.css', $manifestDir ), array(), null );
		if ( is_rtl() ) {
			wp_enqueue_style( 'rtl', mix_asset( '/assets/css/rtl.css', $manifestDir ), array(), null );
		}

		// Enqueue Swiper Slider CSS from CDN.
		wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0' );

		// Enqueue FontAwesome CSS from theme files using mix_asset.
		wp_enqueue_style( 'font-awesome', mix_asset( '/assets/css/fonts/fontawesome/css/all.css', $manifestDir ), array(), null );

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
					// For these files, try to load from mix manifest (compiled folder).
					wp_enqueue_style( "cob-{$handle}", mix_asset( '/' . $relative_path, $manifestDir ), array(), null );
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

		// Enqueue Swiper JS from CDN.
		wp_enqueue_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );

		// Enqueue LazyLoad JS from CDN.
		wp_enqueue_script( 'lazyload', 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js', array(), '5.3.2', true );

		// Enqueue Floating UI JS if the file exists.
		$floating_ui_path = $theme_dir . '/assets/js/floating-ui.dom.umd.js';
		if ( file_exists( $floating_ui_path ) ) {
			$version = cob_get_asset_version( $floating_ui_path );
			wp_enqueue_script( 'floating-ui', mix_asset( '/assets/js/floating-ui.dom.umd.js', $manifestDir ), array( 'jquery' ), $version ? $version : '1.0.0', true );
		}

		// Enqueue the main theme JS if the file exists.
		$main_js_path = $theme_dir . '/assets/js/main.js';
		if ( file_exists( $main_js_path ) ) {
			$version = cob_get_asset_version( $main_js_path );
			wp_enqueue_script( 'main-js', mix_asset( '/assets/js/main.js', $manifestDir ), array( 'jquery' ), $version, true );
		}

		// Enqueue FontAwesome JS from theme files.
		wp_enqueue_script( 'fontawesome', mix_asset( '/assets/css/fonts/fontawesome/js/all.js', $manifestDir ), array(), null, true );
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
			wp_enqueue_script( 'job-application', mix_asset( '/assets/js/job-application.js', '/dist' ), array( 'jquery' ), '1.0', true );
			wp_localize_script( 'job-application', 'myAjax', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			) );
			// Enqueue projects script.
			wp_enqueue_script( 'projects', mix_asset( '/assets/js/projects.js', '/dist' ), array( 'jquery' ), '1.0', true );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'enqueue_job_application_script' );
