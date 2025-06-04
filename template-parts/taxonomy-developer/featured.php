<?php
/**
 * Template Name: Latest Projects
 */

$theme_dir = get_template_directory_uri();

$term = get_queried_object();

$featured_args = array(
    'post_type'      => 'properties',
    'posts_per_page' => 9,
    'meta_query'     => array(
        array(
            'key'     => '_is_featured',
            'value'   => 'yes',
            'compare' => '='
        ),
    ),
    'tax_query'      => array(
        array(
            'taxonomy' => $term->taxonomy,
            'field'    => 'slug',
            'terms'    => $term->slug,
        ),
    ),
);

$projects_query = new WP_Query( $featured_args );
if ( ! $projects_query->have_posts() ) {
    $latest_args = array(
        'post_type'      => 'properties',
        'posts_per_page' => 9,
        'tax_query'      => array(
            array(
                'taxonomy' => $term->taxonomy,
                'field'    => 'slug',
                'terms'    => $term->slug,
            ),
        ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    $projects_query = new WP_Query( $latest_args );
}
?>
<div class="compounds">
    <div class="container">
        <div class="top-compounds">
            <div class="right-compounds">
                <h3 class="head"><?php esc_html_e( 'Featured projects', 'cob_theme' ); ?></h3>

                    <?php
                    if ( $term && ! is_wp_error( $term ) ) {
                        echo '<p>' . sprintf( esc_html__( '%d Results', 'cob_theme' ), $projects_query->found_posts ) . '</p>';
                    }
                    ?>

            </div>
        </div>

        <div class="swiper swiper1">
            <div class="swiper-wrapper">
                <?php if ( $projects_query->have_posts() ) : ?>
                    <?php while ( $projects_query->have_posts() ) : $projects_query->the_post(); ?>
                        <div class="swiper-slide">
                            <a href="<?php the_permalink(); ?>" class="compounds-card">
                                <div class="top-card-comp">
                                    <h6><?php the_title(); ?></h6>
                                </div>
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <img class="main-img lazyload" data-src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>" alt="<?php the_title_attribute(); ?>">
                                <?php else : ?>
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else : ?>
                    <div class="swiper-slide">
                        <p><?php esc_html_e( 'No projects available.', 'cob_theme' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="swiper-button-prev">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.66602 6.00033H18.3327M1.66602 6.00033C1.66602 4.54158 5.82081 1.81601 6.87435 0.791992M1.66602 6.00033C1.66602 7.45908 5.82081 10.1847 6.87435 11.2087" stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="swiper-button-next">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.334 5.99967L1.66732 5.99967M18.334 5.99967C18.334 7.45842 14.1792 10.184 13.1257 11.208M18.334 5.99967C18.334 4.54092 14.1792 1.8153 13.1257 0.791341" stroke="#fff" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
