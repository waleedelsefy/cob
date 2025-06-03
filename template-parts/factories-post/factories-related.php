<?php
/**
 * Factory Listings Template for 'Capital of Business' Theme
 *
 * @package Capital_of_Business
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$theme_dir = get_template_directory_uri();
$transient_key = 'latest_factory';
$factories_query = get_transient( $transient_key );

if ( false === $factories_query ) {
    $args = [
        'post_type'      => 'factory',
        'posts_per_page' => 4,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    ];

    $factories_query = new WP_Query( $args );
    set_transient( $transient_key, $factories_query, HOUR_IN_SECONDS );
}
?>

<div class="properties flat-properties">
    <div class="container">
        <div class="top-properties">
            <div class="right-properties">
                <h3 class="head"><?php esc_html_e( 'Featured Factories', 'cob_theme' ); ?></h3>
                <h5><?php esc_html_e( 'Explore the latest real estate projects available', 'cob_theme' ); ?></h5>
            </div>
        </div>
        <div class="swiper swiper3">

                        <div class="swiper-wrapper">
                            <?php if ( $factories_query->have_posts() ) : ?>
                                <?php while ( $factories_query->have_posts() ) : $factories_query->the_post(); ?>



                                        <?php get_template_part('template-parts/single/factorys-card'); ?>


                                <?php endwhile; ?>
                                <?php wp_reset_postdata(); ?>
                            <?php else : ?>
                                <p><?php esc_html_e( 'There are no factories currently available.', 'cob_theme' ); ?></p>
                                <p></p>
                            <?php endif; ?>
                        </div>


            <!-- Custom navigation buttons -->
            <div class="swiper-button-prev">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.66602 6.00033H18.3327M1.66602 6.00033C1.66602 4.54158 5.82081 1.81601 6.87435 0.791992M1.66602 6.00033C1.66602 7.45908 5.82081 10.1847 6.87435 11.2087" stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </div>
            <div class="swiper-button-next">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.334 5.99967L1.66732 5.99967M18.334 5.99967C18.334 7.45842 14.1792 10.184 13.1257 11.208M18.334 5.99967C18.334 4.54092 14.1792 1.8153 13.1257 0.791341" stroke="#fff" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
