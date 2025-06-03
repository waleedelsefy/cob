<?php
add_action( 'wp_enqueue_scripts', function() {
    if ( is_page_template( 'page-latest-projects.php' ) ) {
        wp_enqueue_script(
            'cob-latest-projects-ajax',
            get_template_directory_uri() . '/assets/js/latest-projects-ajax.js',
            [ 'jquery' ],
            '1.0',
            true
        );
        wp_localize_script( 'cob-latest-projects-ajax', 'cobProjects', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'cob_projects_nonce' ),
        ] );
    }
} );

// AJAX handler
add_action( 'wp_ajax_cob_load_projects', 'cob_ajax_load_projects' );
add_action( 'wp_ajax_nopriv_cob_load_projects', 'cob_ajax_load_projects' );
function cob_ajax_load_projects() {
    check_ajax_referer( 'cob_projects_nonce', 'nonce' );
    $paged = absint( $_POST['paged'] ?? 1 );

    $query = new WP_Query([
        'post_type'      => 'factory',
        'posts_per_page' => 3,
        'paged'          => $paged,
    ]);

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            ?>
            <a href="<?php the_permalink(); ?>" class="factorys-card">
                <ul class="big-ul">
                </ul>
            </a>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo '<p>' . esc_html__( 'No projects found.', 'cob_theme' ) . '</p>';
    }
    $cards = ob_get_clean();

    $big  = 999999999;
    $pages = paginate_links([
        'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'    => '?paged=%#%',
        'current'   => $paged,
        'total'     => $query->max_num_pages,
        'prev_text' => '<svg…></svg> ' . esc_html__( 'Previous', 'cob_theme' ),
        'next_text' => esc_html__( 'Next', 'cob_theme' ) . ' <svg…></svg>',
        'type'      => 'list',
        'end_size'  => 1,
        'mid_size'  => 2,
    ]);

    wp_send_json_success([
        'cards'      => $cards,
        'pagination' => $pages,
    ]);
}
