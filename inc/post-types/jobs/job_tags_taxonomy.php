<?php
function create_job_tags_taxonomy() {
    $labels = array(
        'name'                       => _x( 'Job Tags', 'taxonomy general name', 'cob_theme' ),
        'singular_name'              => _x( 'Job Tag', 'taxonomy singular name', 'cob_theme' ),
        'search_items'               => __( 'Search Job Tags', 'cob_theme' ),
        'popular_items'              => __( 'Popular Job Tags', 'cob_theme' ),
        'all_items'                  => __( 'All Job Tags', 'cob_theme' ),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __( 'Edit Job Tag', 'cob_theme' ),
        'update_item'                => __( 'Update Job Tag', 'cob_theme' ),
        'add_new_item'               => __( 'Add New Job Tag', 'cob_theme' ),
        'new_item_name'              => __( 'New Job Tag Name', 'cob_theme' ),
        'separate_items_with_commas' => __( 'Separate job tags with commas', 'cob_theme' ),
        'add_or_remove_items'        => __( 'Add or remove job tags', 'cob_theme' ),
        'choose_from_most_used'      => __( 'Choose from the most used job tags', 'cob_theme' ),
        'not_found'                  => __( 'No job tags found.', 'cob_theme' ),
        'menu_name'                  => __( 'Job Tags', 'cob_theme' ),
    );

    $args = array(
        'hierarchical'          => false, // Set to false to behave like tags.
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'job-tag' ),
    );

    register_taxonomy( 'job_tag', array( 'jobs' ), $args );
}
add_action( 'init', 'create_job_tags_taxonomy', 0 );
