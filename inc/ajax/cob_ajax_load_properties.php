<?php
// 1b) AJAX callback: renders the properties loop + pagination

function cob_ajax_load_properties() {
    check_ajax_referer( 'cob_factory_nonce', 'nonce' );

    $paged = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
    $city  = sanitize_text_field( $_POST['city'] );

    $args = [
        'post_type'      => 'properties',
        'posts_per_page' => 6,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => [
            [
                'taxonomy' => 'city',
                'field'    => 'slug',
                'terms'    => $city,
            ],
        ],
    ];
    $query = new WP_Query( $args );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/single/properties-card' );
        }
        wp_reset_postdata();
    } else {
        echo '<p>' . esc_html__( 'There are no posts currently available', 'cob_theme' ) . '</p>';
    }
    $html = ob_get_clean();

    // regenerate pagination
    $big  = 999999999;
    $pages = paginate_links( [
        'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'    => '?paged=%#%',
        'current'   => $paged,
        'total'     => $query->max_num_pages,
        'prev_text' => esc_html__( 'Previous', 'cob_theme' ),
        'next_text' => esc_html__( 'Next', 'cob_theme' ),
        'type'      => 'list',
        'end_size'  => 1,
        'mid_size'  => 2,
    ] );

    wp_send_json_success( [
        'cards'      => $html,
        'pagination' => $pages,
    ] );
}
add_action( 'wp_ajax_cob_load_properties', 'cob_ajax_load_properties' );
add_action( 'wp_ajax_nopriv_cob_load_properties', 'cob_ajax_load_properties' );
