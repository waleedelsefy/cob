<?php
$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
$term = get_queried_object();

$args = [
    'post_type'      => 'properties',
    'posts_per_page' => 6,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'tax_query'      => array(
        array(
            'taxonomy' => $term->taxonomy,
            'field'    => 'slug',
            'terms'    => $term->slug,
        ),
    ),
];
$propertie_id = get_the_ID();
$project_id = (int) get_post_field( 'post_parent', $propertie_id );
$project_title = get_the_title( $project_id ) ?: '--';

$properties_query = new WP_Query( $args );
$total_results = $properties_query->found_posts;
?>

<section class="pagination-section pagination-city ">
    <div class="container">
        <div class="top-compounds">
            <div class="right-compounds">
                <h3 class="head">
                    <?php
                    printf(
                        esc_html__( 'The most searched compounds in %s', 'cob_theme' ),
                        single_term_title( '', false )
                    );
                    ?>
                </h3>
                <h5>
                    <?php
                    if ( $term && ! is_wp_error( $term ) ) {
                        echo '<p>' . sprintf( esc_html__( '%d Results', 'cob_theme' ), $properties_query->found_posts ) . '</p>';
                    }
                    ?>
                </h5>
            </div>
        </div>
        <div class="properties-cards">
            <?php if ( $properties_query->have_posts() ) : ?>
                <?php while ( $properties_query->have_posts() ) : $properties_query->the_post(); ?>
                    <?php
                    get_template_part('template-parts/single/properties-card'); ?>


                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p><?php esc_html_e( 'There are no posts currently available', 'cob_theme' ); ?></p>
            <?php endif; ?>
        </div>

        <!-- Cards Section -->

        <div class="pagination">
            <?php
            $big = 999999999;
            echo paginate_links( array(
                'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'    => '?paged=%#%',
                'current'   => $paged,
                'total'     => $properties_query->max_num_pages,
                'prev_text' => '<svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.10212 6.99773L0.997038 1.89666C0.619477 1.5191 0.619477 0.908582 0.997038 0.53504C1.3746 0.161498 1.98512 0.161497 2.36268 0.53504L8.14656 6.31491C8.51208 6.68042 8.52011 7.26684 8.17468 7.6444L2.36669 13.4644C2.17791 13.6532 1.92888 13.7456 1.68387 13.7456C1.43886 13.7456 1.18983 13.6532 1.00105 13.4644C0.623496 13.0869 0.623496 12.4764 1.00105 12.1028L6.10212 6.99773Z" fill="black" />
                                </svg> ' . esc_html__( 'Previous', 'cob_theme' ),
                'next_text' => esc_html__( 'Next', 'cob_theme' ) . ' <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281172 7.68509C-0.084341 7.31958 -0.0923709 6.73316 0.253054 6.3556L6.06104 0.535562C6.24982 0.346784 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346784 7.42668 0.535562C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="black" />
                                </svg>',
                'type'      => 'list',
                'end_size'  => 1,
                'mid_size'  => 2,
            ) );
            ?>
        </div>
    </div>
</section>
