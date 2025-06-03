<?php
/**
 * Factory Listings Template for 'Capital of Business' Theme
 *
 * @package Capital_of_Business
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the current language code using Polylang (if available)
$language = function_exists( 'pll_current_language' ) ? pll_current_language() : 'default';

// Use a language-specific transient key to cache the properties query
$transient_key = 'latest_properties_' . $language;
$properties_query = get_transient( $transient_key );

if ( false === $properties_query ) {
	$args = [
		'post_type'      => 'properties',
		'posts_per_page' => 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	];

	$properties_query = new WP_Query( $args );
	set_transient( $transient_key, $properties_query, HOUR_IN_SECONDS );
}

// Get the correct "flats" page URL for the current language.
// If Polylang is active, get_page_by_path will return the page for the current language.
$flats_page = get_page_by_path( 'flats' );
if ( function_exists( 'pll_get_post' ) && $flats_page ) {
	$flats_page = pll_get_post( $flats_page->ID );
}
$flats_url = $flats_page ? get_permalink( $flats_page ) : '#';
?>

<div class="properties">
    <div class="container">
        <div class="top-properties">
            <div class="right-properties">
                <h3 class="head"><?php echo esc_html__( 'Featured Properties', 'cob_theme' ); ?></h3>
                <h5><?php echo esc_html__( 'Explore the latest real estate projects available', 'cob_theme' ); ?></h5>
            </div>
            <a href="<?php echo esc_url( $flats_url ); ?>" class="properties-button">
				<?php echo esc_html__( 'View All', 'cob_theme' ); ?>
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281171 7.68509C-0.0843415 7.31958 -0.0923715 6.73316 0.253053 6.3556L6.06104 0.535563C6.24982 0.346785 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535563C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="black"/>
                </svg>
            </a>
        </div>
        <div class="swiper swiper3">
            <div class="swiper-wrapper">
				<?php if ( $properties_query->have_posts() ) : ?>
					<?php while ( $properties_query->have_posts() ) : $properties_query->the_post(); ?>
						<?php
						$post_id = get_the_ID();
						$city_name = esc_html__( 'Not known', 'cob_theme' );
						$city_link = '';
						$city_terms = get_the_terms( $post_id, 'city' );

						if ( $city_terms && ! is_wp_error( $city_terms ) ) {
							$city_name = esc_html( $city_terms[0]->name );
							$city_link = get_term_link( $city_terms[0] );
						}
						?>
                        <div class="swiper-slide">
							<?php get_template_part( 'template-parts/single/properties-card' ); ?>
                        </div>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				<?php else : ?>
                    <p><?php echo esc_html__( 'No properties available at the moment.', 'cob_theme' ); ?></p>
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
