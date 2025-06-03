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
 * cob theme setup
 */

function cob_theme_setup() {
    // Enable support for various WordPress features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo');
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('widgets');

    load_theme_textdomain('cob_theme', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'cob_theme_setup');
