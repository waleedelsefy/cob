<?php
/**
 * Most Searched Compounds Template
 * Displays a slider of compounds, sorted by the last modification date of their associated properties.
 * Includes a fix for get_taxonomy_archive_link() on older WordPress versions.
 *
 * @package Capital_of_Business
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$theme_dir = get_template_directory_uri();

// Meta key for the compound's cover image attachment ID.
// This should match the configuration in your importer and manual field management scripts.
$cover_image_meta_key = '_compound_cover_image_id';

// Meta key for the custom property count on a compound term.
$property_count_meta_key = 'properties_count'; // Ensure this is the correct meta key you are using.

$all_compounds_for_sorting = get_terms( [
    'taxonomy'   => 'compound',
    'hide_empty' => false,
] );

$compound_last_activity = array();

if ( ! empty( $all_compounds_for_sorting ) && ! is_wp_error( $all_compounds_for_sorting ) ) {
    foreach ( $all_compounds_for_sorting as $compound_term ) {
        $args = [
            'post_type'      => 'properties',
            'posts_per_page' => 1,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'tax_query'      => [
                [
                    'taxonomy' => 'compound',
                    'field'    => 'term_id',
                    'terms'    => $compound_term->term_id,
                ],
            ],
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ];
        $property_query = new WP_Query( $args );

        if ( $property_query->have_posts() ) {
            $last_modified_timestamp = get_post_modified_time( 'U', false, $property_query->posts[0] );
            $compound_last_activity[ $compound_term->term_id ] = $last_modified_timestamp;
        } else {
            $compound_last_activity[ $compound_term->term_id ] = 0;
        }
    }

    usort( $all_compounds_for_sorting, function( $a, $b ) use ( $compound_last_activity ) {
        $activity_a = $compound_last_activity[ $a->term_id ] ?? 0;
        $activity_b = $compound_last_activity[ $b->term_id ] ?? 0;
        return $activity_b - $activity_a;
    } );

    $compounds_to_display = array_slice( $all_compounds_for_sorting, 0, 9 );
} else {
    $compounds_to_display = [];
}
?>

<div class="compounds">
    <div class="container">
        <div class="top-compounds">
            <div class="right-compounds">
                <h3 class="head"><?php esc_html_e( 'Most Searched Compounds', 'cob_theme' ); ?></h3>
            </div>
            <?php
            $compound_taxonomy_slug = 'compound'; // Your compound taxonomy slug
            $compounds_archive_page_slug = 'compounds-list'; // Example slug for a page listing all compounds
            $all_compounds_archive_link = '#'; // Default fallback

            // Attempt 1: Use get_taxonomy_archive_link() if available (WP 4.5+)
            if ( function_exists( 'get_taxonomy_archive_link' ) ) {
                $archive_link_candidate = get_taxonomy_archive_link( $compound_taxonomy_slug );
                if ( ! is_wp_error( $archive_link_candidate ) && $archive_link_candidate ) {
                    $all_compounds_archive_link = $archive_link_candidate;
                }
            }

            // Attempt 2: Fallback to a specific page by slug if the archive link wasn't found or function doesn't exist
            if ( $all_compounds_archive_link === '#' || is_wp_error( $all_compounds_archive_link ) ) {
                $compounds_archive_page = get_page_by_path( $compounds_archive_page_slug );
                if ( $compounds_archive_page ) {
                    $all_compounds_archive_link = get_permalink( $compounds_archive_page->ID );
                } else {
                    // Attempt 3: Construct a basic link (assumes pretty permalinks and taxonomy base is its slug)
                    $all_compounds_archive_link = home_url( user_trailingslashit( $compound_taxonomy_slug ) );
                }
            }
            // Final check if any link was successfully generated
            if ( is_wp_error( $all_compounds_archive_link ) || !$all_compounds_archive_link ) {
                $all_compounds_archive_link = '#'; // Ultimate fallback
            }

            if ($all_compounds_archive_link && $all_compounds_archive_link !== '#') :
                ?>
                <a href="<?php echo esc_url($all_compounds_archive_link); ?>" class="compounds-view-all-button">
                    <?php esc_html_e('View all', 'cob_theme'); ?>
                    <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281171 7.68509C-0.0843415 7.31958 -0.0923715 6.73316 0.253053 6.3556L6.06104 0.535563C6.24982 0.346785 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535563C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="black" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>

        <?php if ( ! empty( $compounds_to_display ) ) : ?>
            <div class="swiper swiper1">
                <div class="swiper-wrapper">
                    <?php foreach ( $compounds_to_display as $compound ) : ?>
                        <div class="swiper-slide">
                            <a href="<?php echo esc_url( get_term_link( $compound ) ); ?>" class="compounds-card">
                                <div class="top-card-comp">
                                    <h6><?php echo esc_html( $compound->name ); ?></h6>
                                    <span>
                                        <?php
                                        $prop_count = get_term_meta( $compound->term_id, $property_count_meta_key, true );
                                        if ( empty($prop_count) || !is_numeric($prop_count) ) {
                                            $prop_count = $compound->count;
                                        }
                                        echo esc_html( number_format_i18n( (int) $prop_count ) ) . ' ' . esc_html__( 'Properties', 'cob_theme' );
                                        ?>
                                    </span>
                                </div>

                                <?php
                                $attachment_id = get_term_meta( $compound->term_id, $cover_image_meta_key, true );
                                $image_url = $theme_dir . '/assets/imgs/default.jpg';

                                if ( $attachment_id && is_numeric($attachment_id) ) {
                                    $image_data = wp_get_attachment_image_src( (int) $attachment_id, 'medium' );
                                    if ( $image_data && isset($image_data[0]) ) {
                                        $image_url = $image_data[0];
                                    }
                                }
                                ?>
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $compound->name ); ?>" class="lazyload">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="swiper-button-prev">
                    <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.66602 6.00033H18.3327M1.66602 6.00033C1.66602 4.54158 5.82081 1.81601 6.87435 0.791992M1.66602 6.00033C1.66602 7.45908 5.82081 10.1847 6.87435 11.2087"
                              stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div class="swiper-button-next">
                    <svg width="20" height="12" viewBox="0 0 20 12" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.334 5.99967L1.66732 5.99967M18.334 5.99967C18.334 7.45842 14.1792 10.184 13.1257 11.208M18.334 5.99967C18.334 4.54092 14.1792 1.8153 13.1257 0.791341"
                              stroke="#fff" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        <?php else : ?>
            <div class="no-compounds-found">
                <p><?php esc_html_e( 'No compounds available at the moment.', 'cob_theme' ); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
