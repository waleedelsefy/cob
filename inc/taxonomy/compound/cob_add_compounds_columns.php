<?php
/**
 * Standalone Script for Adding Custom Admin Columns to 'compound' Taxonomy.
 *
 * This script adds 'Developer' and 'City' columns to the 'compound' taxonomy
 * list table in the WordPress admin area. It assumes that the 'compound_developer'
 * and 'compound_city' meta fields store the TERM_IDs of the respective developer
 * and city terms.
 *
 * @package Capital_of_Business_Customizations
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Adds custom columns for Developer and City to the "compound" taxonomy list table.
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
if ( ! function_exists( 'cob_add_compound_admin_columns_standalone' ) ) {
    function cob_add_compound_admin_columns_standalone($columns) {
        $new_columns = [];
        $inserted_custom_cols = false;

        // Example: Insert custom columns before 'posts' (count) column if it exists
        foreach ($columns as $key => $value) {
            if ($key === 'posts' && !$inserted_custom_cols) {
                // The keys 'compound_developer' and 'compound_city' are used to identify the columns
                // in the manage_compound_custom_column filter.
                $new_columns['compound_developer_col'] = __('Developer', 'cob_theme'); // Changed key slightly for clarity
                $new_columns['compound_city_col'] = __('City', 'cob_theme');       // Changed key slightly for clarity
                $inserted_custom_cols = true;
            }
            $new_columns[$key] = $value;
        }

        if (!$inserted_custom_cols) {
            $new_columns['compound_developer_col'] = __('Developer', 'cob_theme');
            $new_columns['compound_city_col'] = __('City', 'cob_theme');
        }

        return $new_columns;
    }
}
add_filter('manage_edit-compound_columns', 'cob_add_compound_admin_columns_standalone');

/**
 * Displays the content for the custom Developer and City columns.
 *
 * @param string $content Default column content (empty for custom columns).
 * @param string $column_name Name of the current column.
 * @param int    $term_id ID of the current term.
 * @return string Content for the custom column.
 */
if ( ! function_exists( 'cob_show_compound_admin_column_content_standalone' ) ) {
    function cob_show_compound_admin_column_content_standalone($content, $column_name, $term_id) {
        // These should match the slugs of your registered taxonomies for developers and cities
        // And also match what's in your importer config ($cob_compound_importer_config)
        // Or how you generally store this relationship.
        $developer_taxonomy_slug = 'developer'; // IMPORTANT: Adjust if your developer taxonomy slug is different
        $city_taxonomy_slug = 'city';           // IMPORTANT: Adjust if your city taxonomy slug is different

        // Meta keys where the TERM_ID of the developer/city is stored for the compound term
        $developer_meta_key = 'compound_developer'; // This is the meta key on the 'compound' term
        $city_meta_key = 'compound_city';       // This is the meta key on the 'compound' term

        if ($column_name === 'compound_developer_col') { // Match the key from cob_add_compound_admin_columns_standalone
            $developer_term_id = get_term_meta($term_id, $developer_meta_key, true);
            if (!empty($developer_term_id) && is_numeric($developer_term_id)) {
                $developer_term = get_term($developer_term_id, $developer_taxonomy_slug);

                if ($developer_term && !is_wp_error($developer_term)) {
                    $edit_link = get_edit_term_link($developer_term->term_id, $developer_taxonomy_slug);
                    if ($edit_link) {
                        return '<a href="' . esc_url($edit_link) . '">' . esc_html($developer_term->name) . '</a>';
                    }
                    return esc_html($developer_term->name);
                } else {
                    return '<em>' . __('Error loading developer', 'cob_theme') . ' (ID: ' . esc_html($developer_term_id) . ')</em>';
                }
            } else {
                return '&#8212;'; // Em dash for not assigned
            }
        }

        if ($column_name === 'compound_city_col') { // Match the key from cob_add_compound_admin_columns_standalone
            $city_term_id = get_term_meta($term_id, $city_meta_key, true);
            if (!empty($city_term_id) && is_numeric($city_term_id)) {
                $city_term = get_term($city_term_id, $city_taxonomy_slug);

                if ($city_term && !is_wp_error($city_term)) {
                    $edit_link = get_edit_term_link($city_term->term_id, $city_taxonomy_slug);
                    if ($edit_link) {
                        return '<a href="' . esc_url($edit_link) . '">' . esc_html($city_term->name) . '</a>';
                    }
                    return esc_html($city_term->name);
                } else {
                    return '<em>' . __('Error loading city', 'cob_theme') . ' (ID: ' . esc_html($city_term_id) . ')</em>';
                }
            } else {
                return '&#8212;'; // Em dash for not assigned
            }
        }

        return $content; // Return content for other columns
    }
}
add_filter('manage_compound_custom_column', 'cob_show_compound_admin_column_content_standalone', 10, 3);

?>
