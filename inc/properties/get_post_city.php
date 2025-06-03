<?php
/**
 * Register Custom Post Types and Taxonomies
 *
 * @package Capital_of_Business
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Property Details from Custom Table
 */
/**
 * Get City Taxonomy
 */
function get_post_city($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $terms = get_the_terms($post_id, 'city');

    return (!empty($terms) && !is_wp_error($terms)) ? $terms[0]->name : null;
}

