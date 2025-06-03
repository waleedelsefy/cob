<?php
$term = get_queried_object();

$args = array(
    'post_type'      => 'projects',
    'posts_per_page' => 6,
    'meta_key'       => 'post_views_count',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
    'tax_query'      => array(
        array(
            'taxonomy' => $term->taxonomy,
            'field'    => 'slug',
            'terms'    => $term->slug,
        ),
    ),
);

$most_visited_query = new WP_Query( $args );


if ( ! $most_visited_query->have_posts() ) {
    $args = array(
        'post_type'      => 'projects',
        'posts_per_page' => 6,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => array(
            array(
                'taxonomy' => $term->taxonomy,
                'field'    => 'slug',
                'terms'    => $term->slug,
            ),
        ),
    );
    $most_visited_query = new WP_Query( $args );
}
$top_two_posts = array();
if ( ! empty( $most_visited_query->posts ) ) {
    $top_two_posts = array_slice( $most_visited_query->posts, 0, 2 );
}
?>

<section class="city-layout-section">
    <div class="container">
        <div class="section-layout city-layout">
            <div class="text-container">
                <div class="text-content">
                    <h3 class="article-title"><?php
                        printf(
                            esc_html__( 'The most important compounds %s', 'cob_theme' ),
                            single_term_title( '', false )
                        );
                        ?></h3>
                    <ul class="content-list">
                        <?php if ( $most_visited_query->have_posts() ) : ?>
                            <?php while ( $most_visited_query->have_posts() ) : $most_visited_query->the_post(); ?>
                                <li>
                                    <p>
                                        <?php the_title(); ?>
                                        <?php
                                        $views = get_post_meta( get_the_ID(), 'post_views_count', true );
                                        if ( $views ) {
                                            echo ' - ' . esc_html( $views ) . ' ' . __( 'Visits', 'cob_theme' );
                                        }
                                        ?>
                                    </p>
                                </li>
                            <?php endwhile; ?>
                            <?php wp_reset_postdata(); ?>
                        <?php else : ?>
                            <li>
                                <p><?php _e( 'No projects available', 'cob_theme' ); ?></p>
                            </li>
                        <?php endif; ?>

                        <?php
                        if ( $most_visited_query->max_num_pages > 1 ) :
                            ?>
                           <!-- <li>
                                <button onclick="window.location.href='<?php /*echo esc_url( get_next_posts_page_link( $most_visited_query->max_num_pages ) ); */?>'">
                                    <?php /*_e( 'View More', 'cob_theme' ); */?>
                                    <svg width="14" height="8" viewBox="0 0 14 8" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.74788 5.53181L11.8489 0.426726C12.2265 0.0491642 12.837 0.0491643 13.2106 0.426726C13.5841 0.804284 13.5841 1.4148 13.2106 1.79236L7.4307 7.57625C7.06519 7.94176 6.47876 7.94979 6.10121 7.60437L0.281168 1.79638C0.09239 1.6076 0 1.35857 0 1.11356C0 0.868548 0.09239 0.61952 0.281168 0.43074C0.658728 0.0531825 1.26925 0.0531825 1.64279 0.43074L6.74788 5.53181Z" fill="#E92028"/>
                                    </svg>
                                </button>
                            </li>-->
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="images-container">
                <?php if ( ! empty( $top_two_posts ) ) : ?>
                    <div class="project-image image1">
                        <?php
                        $first_post = $top_two_posts[0];
                        if ( has_post_thumbnail( $first_post->ID ) ) {
                            echo get_the_post_thumbnail( $first_post->ID, 'full', array( 'class' => 'image1' ) );
                        } else {
                            ?>
                            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/articles-det.jpg' ); ?>"
                                 alt="<?php echo esc_attr( get_the_title( $first_post->ID ) ); ?>"
                                 class="image1">
                            <?php
                        }
                        ?>
                    </div>
                    <div class="project-image inner-img image2">
                        <?php
                        if ( count( $top_two_posts ) > 1 ) {
                            $second_post = $top_two_posts[1];
                            if ( has_post_thumbnail( $second_post->ID ) ) {
                                echo get_the_post_thumbnail( $second_post->ID, 'full', array( 'class' => 'image2' ) );
                            } else {
                                ?>
                                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/articles-det2.jpg' ); ?>"
                                     alt="<?php echo esc_attr( get_the_title( $second_post->ID ) ); ?>"
                                     class="image2">
                                <?php
                            }
                        }
                        ?>
                    </div>
                <?php else : ?>
                    <div class="project-image image1">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/articles-det.jpg' ); ?>"
                             alt="<?php _e( 'Default Image 1', 'cob_theme' ); ?>"
                             class="image1 lazyload">
                    </div>
                    <div class="project-image inner-img image2">
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/articles-det2.jpg' ); ?>"
                             alt="<?php _e( 'Default Image 2', 'cob_theme' ); ?>"
                             class="image2 lazyload">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
