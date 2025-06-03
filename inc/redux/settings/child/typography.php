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
    'title'  => __('Typography', 'cob_theme'),
    'id'     => 'typography_settings',
    'subsection' => true,
    'fields' => [
        [
            'id'       => 'body_font',
            'type'     => 'typography',
            'title'    => __('Body Font', 'cob_theme'),
            'google'   => true,
            'default'  => [
                'font-family' => 'Roboto',
                'font-size'   => '16px',
            ],
        ],
    ]
]);
