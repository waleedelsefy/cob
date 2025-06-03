<?php


// Add AJAX handler for loading properties
if (!function_exists('load_developer_properties_ajax_handler')) {
    function load_developer_properties_ajax_handler()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'load_developer_properties_nonce')) {
            wp_send_json_error('Invalid nonce');
        }

        // Get parameters
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $sort_option = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'date_desc';
        $term_id = isset($_POST['term_id']) ? intval($_POST['term_id']) : 0;
        $taxonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : 'developer';

        // Set query parameters based on the selected sort option
        switch ($sort_option) {
            case 'price_asc':
                $orderby = 'meta_value_num';
                $order = 'ASC';
                $meta_key = 'price';
                break;
            case 'price_desc':
                $orderby = 'meta_value_num';
                $order = 'DESC';
                $meta_key = 'price';
                break;
            case 'date_asc':
                $orderby = 'date';
                $order = 'ASC';
                $meta_key = '';
                break;
            default: // date_desc
                $orderby = 'date';
                $order = 'DESC';
                $meta_key = '';
                break;
        }

        // Build the WP_Query arguments
        $args = [
            'post_type' => 'properties',
            'posts_per_page' => 6,
            'paged' => $page,
            'orderby' => $orderby,
            'order' => $order,
            'post_status' => 'publish',
            'tax_query' => [
                [
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => $term_id,
                ],
            ],
        ];

        if (!empty($meta_key)) {
            $args['meta_key'] = $meta_key;
        }

        // Execute the query
        $properties_query = new WP_Query($args);

        // Start output buffering to capture the HTML
        ob_start();

        if ($properties_query->have_posts()) {
            while ($properties_query->have_posts()) {
                $properties_query->the_post();
                get_template_part('template-parts/single/properties-card');
            }
            wp_reset_postdata();
        } else {
            echo '<p class="no-results">' . esc_html__('There are no posts currently available', 'cob_theme') . '</p>';
        }

        // Get the buffered content
        $html = ob_get_clean();

        // Send the response
        wp_send_json_success([
            'html' => $html,
            'max_pages' => $properties_query->max_num_pages,
            'total_results' => $properties_query->found_posts
        ]);
    }

    add_action('wp_ajax_load_developer_properties', 'load_developer_properties_ajax_handler');
    add_action('wp_ajax_nopriv_load_developer_properties', 'load_developer_properties_ajax_handler');
}

