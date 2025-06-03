<?php
/**
 * Capital of Business Theme Functions
 *
 * @package Capital_of_Business
 */

if (!class_exists('Redux')) {
    return;
}

// Define global Redux variable
$opt_name = "cob_options";

Redux::setArgs($opt_name, [
    'opt_name'     => $opt_name,
    'display_name' => 'Capital of Business Settings',
    'menu_title'   => 'COB Settings',
    'menu_type'    => 'menu',
    'admin_bar'    => true,
    'allow_sub_menu' => true,
    'dev_mode'     => false,
    'page_priority' => 3,
    'page_parent'  => 'themes.php',
    'page_permissions' => 'manage_options',
    'menu_icon'    => 'dashicons-admin-generic',
    'save_defaults' => true,
    'default_show' => false,
    'default_mark' => '',
]);

// Include Parent Tabs
require_once get_template_directory() . '/inc/redux/settings/general-settings.php';
require_once get_template_directory() . '/inc/redux/settings/property-settings.php';
require_once get_template_directory() . '/inc/redux/settings/appearance-settings.php';
require_once get_template_directory() . '/inc/redux/settings/social.php';

function enqueue_developer_admin_scripts($hook) {
    // Ensure we are on the taxonomy edit or add new term pages
    if (isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'developer') {
        wp_enqueue_media();
        wp_enqueue_script('admin-js', get_template_directory_uri() . '/assets/js/admin.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_developer_admin_scripts');
