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
    'title'  => __('Footer Settings', 'cob_theme'),
    'id'     => 'footer_settings',
    'subsection' => true,
    'fields' => [
        [
            'id'       => 'footer_text',
            'type'     => 'textarea',
            'title'    => __('Footer Text', 'cob_theme'),
            'default'  => __('© 2024 Capital of Business. All rights reserved.', 'cob_theme'),
        ],
    ]
]);
