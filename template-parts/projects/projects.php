<?php
/**
 * Template Name: Latest Projects
 */

$theme_dir = get_template_directory_uri();

$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;

$projects_query = new WP_Query( array(
    'post_type'      => 'projects',
    'posts_per_page' => 16,
    'paged'          => $paged,
) );
?>

<section class="pagination-section">
    <div class="container">
        <!-- Cards Section -->
        <div class="cards">
            <?php if ( $projects_query->have_posts() ) : ?>
                <?php while ( $projects_query->have_posts() ) : $projects_query->the_post(); ?>
                    <a href="<?php the_permalink(); ?>" class="projects-card">
                        <div class="top-card-proj">
                            <?php
                            $post_id = get_the_ID();
                            $developer_image_url = $theme_dir . '/assets/imgs/card-devloper-default.png';
                            $developers = get_the_terms( $post_id, 'developer' );
                            if ( $developers && ! is_wp_error( $developers ) ) {
                                $developer = reset( $developers );
                                $developer_thumbnail_id = absint( get_term_meta( $developer->term_id, 'thumbnail_id', true ) );
                                if ( $developer_thumbnail_id ) {
                                    $developer_image_url = wp_get_attachment_url( $developer_thumbnail_id );
                                }
                            }
                            ?>
                            <img data-src="<?php echo esc_url( $developer_image_url ); ?>" alt="<?php the_title_attribute(); ?>" class="lazyload">
                        </div>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <img class="main-img lazyload" data-src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>" alt="<?php the_title_attribute(); ?>">
                        <?php else : ?>
                            <img class="main-img lazyload" data-src="<?php echo esc_url( $theme_dir . '/assets/imgs/projects1.png' ); ?>" alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>
                        <div class="bottom-card-proj">
                            <button>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.5 9C14.5 10.3807 13.3807 11.5 12 11.5C10.6193 11.5 9.5 10.3807 9.5 9C9.5 7.61929 10.6193 6.5 12 6.5C13.3807 6.5 14.5 7.61929 14.5 9Z" stroke="#E92028" stroke-width="1.5"/>
                                    <path d="M13.2574 17.4936C12.9201 17.8184 12.4693 18 12.0002 18C11.531 18 11.0802 17.8184 10.7429 17.4936C7.6543 14.5008 3.51519 11.1575 5.53371 6.30373C6.6251 3.67932 9.24494 2 12.0002 2C14.7554 2 17.3752 3.67933 18.4666 6.30373C20.4826 11.1514 16.3536 14.5111 13.2574 17.4936Z" stroke="#E92028" stroke-width="1.5"/>
                                    <path d="M18 20C18 21.1046 15.3137 22 12 22C8.68629 22 6 21.1046 6 20" stroke="#E92028" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <?php the_title(); ?>
                            </button>
                        </div>
                    </a>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <p><?php esc_html_e( 'No projects found.', 'cob_theme' ); ?></p>
            <?php endif; ?>
        </div>

        <!-- Pagination Buttons -->
        <div class="pagination">
            <?php
            $big = 999999999; // an unlikely integer
            $pagination_links = paginate_links( array(
                'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'    => '?paged=%#%',
                'current'   => $paged,
                'total'     => $projects_query->max_num_pages,
                'prev_text' => '<svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.10212 6.99773L0.997038 1.89666C0.619477 1.5191 0.619477 0.908582 0.997038 0.53504C1.3746 0.161498 1.98512 0.161497 2.36268 0.53504L8.14656 6.31491C8.51208 6.68042 8.52011 7.26684 8.17468 7.6444L2.36669 13.4644C2.17791 13.6532 1.92888 13.7456 1.68387 13.7456C1.43886 13.7456 1.18983 13.6532 1.00105 13.4644C0.623496 13.0869 0.623496 12.4764 1.00105 12.1028L6.10212 6.99773Z" fill="black" />
                                </svg> ' . esc_html__( 'Previous', 'cob_theme' ),
                'next_text' => esc_html__( 'Next', 'cob_theme' ) . ' <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281172 7.68509C-0.084341 7.31958 -0.0923709 6.73316 0.253054 6.3556L6.06104 0.535562C6.24982 0.346784 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535562C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="black" />
                                </svg>',
                'type'      => 'list',
                'end_size'  => 1,
                'mid_size'  => 2,
            ) );
            if ( $pagination_links ) {
                echo $pagination_links;
            }
            ?>
        </div>
    </div>
</section>
