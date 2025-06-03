<?php

function cob_register_sliders_post_type() {
    $labels = [
        'name'          => __('Sliders', 'cob_theme'),
        'singular_name' => __('Slider', 'cob_theme'),
        'add_new'       => __('Add New Slider', 'cob_theme'),
        'add_new_item'  => __('Add New Slider', 'cob_theme'),
        'edit_item'     => __('Edit Slider', 'cob_theme'),
        'new_item'      => __('New Slider', 'cob_theme'),
        'view_item'     => __('View Slider', 'cob_theme'),
        'all_items'     => __('All Sliders', 'cob_theme'),
        'search_items'  => __('Search Sliders', 'cob_theme'),
        'not_found'     => __('No Sliders found.', 'cob_theme'),
    ];

    $args = [
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-slides',
        'capability_type'    => 'post',
        'supports'           => ['title', 'editor', 'thumbnail'],
        'has_archive'        => false,
        'rewrite'            => false,
        'query_var'          => false,
        'show_in_rest'       => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    ];

    register_post_type('sliders', $args);
}
add_action('init', 'cob_register_sliders_post_type');

function cob_block_sliders_redirect() {
    if (is_singular('sliders')) {
        wp_redirect(home_url(), 301);
        exit;
    }
}
add_action('template_redirect', 'cob_block_sliders_redirect');

