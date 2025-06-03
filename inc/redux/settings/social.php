<?php
if (!class_exists('Redux')) {
    return;
}

// Define global Redux variable
$opt_name = "cob_options";

// Register Social Media Settings Section
Redux::setSection($opt_name, [
    'title'      => __('Social Media', 'cob_theme'),
    'id'         => 'social_settings',
    'icon'       => 'el el-share-alt',
    'fields'     => [
        // Enable Social Menu
        [
            'id'       => 'enable_social_menu',
            'type'     => 'switch',
            'title'    => __('Enable Social Menu', 'cob_theme'),
            'default'  => true,
        ],

        // Serialized Social Media Links
        [
            'id'       => 'social_links',
            'type'     => 'multi_text',
            'title'    => __('Social Media Links', 'cob_theme'),
            'desc'     => __('Add social media links. Use the format: platform|URL', 'cob_theme'),
            'default'  => [
                'facebook|https://facebook.com',
                'twitter|https://twitter.com',
                'instagram|https://instagram.com',
                'linkedin|https://linkedin.com',
                'youtube|https://youtube.com'
            ],
        ],
    ]
]);
