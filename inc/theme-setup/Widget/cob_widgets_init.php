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

/**
 * Register Widget Areas
 */
function cob_widgets_init() {
    register_sidebar([
        'name'          => __('Main Sidebar', 'cob_theme'),
        'id'            => 'main-sidebar',
        'description'   => __('Widgets added here will appear in the sidebar.', 'cob_theme'),
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'cob_widgets_init');
