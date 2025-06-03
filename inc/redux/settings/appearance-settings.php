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
Redux::setSection($opt_name, [
    'title'  => __('Appearance Settings', 'cob_theme'),
    'id'     => 'appearance_settings',
    'desc'   => __('Customize the site appearance.', 'cob_theme'),
    'icon'   => 'el el-adjust',
]);

// Include Child Settings
require_once get_template_directory() . '/inc/redux/settings/child/header-settings.php';
require_once get_template_directory() . '/inc/redux/settings/child/footer-settings.php';
require_once get_template_directory() . '/inc/redux/settings/child/typography.php';
