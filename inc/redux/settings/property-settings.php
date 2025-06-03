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
    'title'  => __('Property Settings', 'cob_theme'),
    'id'     => 'property_settings',
    'desc'   => __('Manage real estate property options.', 'cob_theme'),
    'icon'   => 'el el-home',
    'fields' => [
        [
            'id'       => 'default_property_image',
            'type'     => 'media',
            'title'    => __('Default Property Image', 'cob_theme'),
            'desc'     => __('Upload a fallback image for properties.', 'cob_theme'),
        ],
        [
            'id'       => 'property_listing_layout',
            'type'     => 'select',
            'title'    => __('Listing Layout', 'cob_theme'),
            'desc'     => __('Choose the layout for property listings.', 'cob_theme'),
            'options'  => [
                'grid'  => 'Grid View',
                'list'  => 'List View',
            ],
            'default'  => 'grid',
        ],
    ]
]);
