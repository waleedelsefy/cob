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
    'title'  => __('General Settings', 'cob_theme'),
    'id'     => 'general_settings',
    'desc'   => __('Manage general theme options.', 'cob_theme'),
    'icon'   => 'el el-cogs',
    'fields' => [
        [
            'id'       => 'site_logo',
            'type'     => 'media',
            'title'    => __('Site Logo', 'cob_theme'),
            'desc'     => __('Upload your site logo.', 'cob_theme'),
        ],
        [
            'id'       => 'favicon',
            'type'     => 'media',
            'title'    => __('Favicon', 'cob_theme'),
            'desc'     => __('Upload a 32x32px favicon.', 'cob_theme'),
        ],
    ]
]);
