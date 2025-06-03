<?php
/**
 * Register Custom Post Types and Taxonomies
 *
 * @package Capital_of_Business
 */

if (!defined('ABSPATH')) {
    exit;
}
add_filter('pll_get_post_types', function($post_types) {
    unset($post_types['project']);
    return $post_types;
}, 10, 1);
