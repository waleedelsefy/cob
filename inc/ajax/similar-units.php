<?php

// Enqueue السكريبت وتوفير البيانات للـ JS
add_action( 'wp_enqueue_scripts', function() {
    if ( is_singular( 'properties' ) ) {
        wp_enqueue_script(
            'cob-similar-units-ajax',
            get_template_directory_uri() . '/assets/js/similar-units-ajax.js',
            [ 'jquery' ],
            '1.0',
            true
        );
        global $post;
        $terms = get_the_terms( $post->ID, 'city' );
        $city  = ( ! is_wp_error( $terms ) && $terms ) ? $terms[0]->slug : '';
        wp_localize_script( 'cob-similar-units-ajax', 'cobSimilar', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'cob_similar_nonce' ),
            'unit_id'  => $post->ID,
            'city'     => $city,
        ] );
    }
} );

// تسجيل handlers لـ AJAX (مسجّلين للزوار وللمستخدمين المسجّلين)
add_action( 'wp_ajax_cob_load_similar_units', 'cob_ajax_load_similar_units' );
add_action( 'wp_ajax_nopriv_cob_load_similar_units', 'cob_ajax_load_similar_units' );

function cob_ajax_load_similar_units() {
    check_ajax_referer( 'cob_similar_nonce', 'nonce' );
    $paged   = absint( $_POST['paged']   ?? 1 );
    $unit_id = absint( $_POST['unit_id'] ?? 0 );
    $city    = sanitize_text_field( $_POST['city'] ?? '' );

    $query = new WP_Query([
        'post_type'      => 'properties',
        'posts_per_page' => 6,
        'paged'          => $paged,
        'post__not_in'   => [ $unit_id ],
        'tax_query'      => [[
            'taxonomy' => 'city',
            'field'    => 'slug',
            'terms'    => $city,
        ]],
    ]);

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/single/properties-card' );
        }
        wp_reset_postdata();
    } else {
        echo '<p>' . esc_html__( 'There are no posts currently available.', 'cob_theme' ) . '</p>';
    }
    $cards = ob_get_clean();

    $big   = 999999999;
    $pages = paginate_links([
        'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'    => '?paged=%#%',
        'current'   => $paged,
        'total'     => $query->max_num_pages,
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'type'      => 'list',
    ]);

    wp_send_json_success([
        'cards'      => $cards,
        'pagination' => $pages,
    ]);
}
