<?php
/**
 * Capital of Business Theme Functions
 *
 * @package Capital_of_Business
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
require_once get_template_directory() . '/inc/theme-setup/class-cob-walker-nav-menu.php';

/**
 * cob theme Menus
 */
// Register Header Menus
function cob_register_menus() {
    register_nav_menus( array(
        'primary_menu' => __( 'Primary Menu', 'cob_theme' ),
        'top-articles' => __( 'Top Articles', 'cob_theme' ),
        'footer_menu'            => __('Footer Menu', 'cob_theme'),
        'footer_cities_menu'     => __('Footer Cities Menu', 'cob_theme'),
        'footer_projects_menu'   => __('Footer Projects Menu', 'cob_theme'),
        'footer_developers_menu' => __('Footer Developers Menu', 'cob_theme'),
    ) );
}
add_action( 'after_setup_theme', 'cob_register_menus' );

require_once get_template_directory() . '/inc/theme-setup/class-cob-walker-nav-menu.php';
