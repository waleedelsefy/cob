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
    'title'  => __('Header Settings', 'cob_theme'),
    'id'     => 'header_settings',
    'subsection' => true,
    'fields' => [
        [
            'id'       => 'header_background',
            'type'     => 'color',
            'title'    => __('Header Background', 'cob_theme'),
            'default'  => '#ffffff',
        ],
        [
            'id'       => 'header_sticky',
            'type'     => 'switch',
            'title'    => __('Sticky Header', 'cob_theme'),
            'default'  => true,
        ],
    ]
]);
