<?php
/**
 * Template for displaying a slider of Top Areas (Cities).
 * This template fetches city terms and displays their associated compound and property counts.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$theme_dir = get_template_directory_uri();
// Meta key for the city's image (if you implement city images, ensure this key is correct)
// This should match the meta key used if you have a system for city images, e.g., from an importer or manual field.
// Based on previous discussions, importer for cities might use '_city_image_id'
$city_image_meta_key = '_city_image_id'; // Defaulting to a potential key, adjust if needed or remove if not used.

$cities_args = [
    'taxonomy'   => 'city',      // Your city taxonomy slug
    'hide_empty' => false,
    'number'     => 6,         // Number of cities to display
    'orderby'    => 'count',     // Order by the number of posts associated with the city term
    'order'      => 'DESC',      // Show cities with most posts first
];
$cities = get_terms( $cities_args );

?>

<div class="areas">
    <div class="container">
        <div class="top-areas">
            <div class="right-areas">
                <h3 class="head"><?php esc_html_e( 'Top Areas', 'cob_theme' ); ?></h3>
                <h5><?php esc_html_e( 'Search by area', 'cob_theme' ); ?></h5>
            </div>
            <?php
            $city_taxonomy_slug = 'city';
            $all_cities_archive_link = '#';

            if ( function_exists( 'get_taxonomy_archive_link' ) ) {
                $archive_link_candidate = get_taxonomy_archive_link( $city_taxonomy_slug );
                if ( ! is_wp_error( $archive_link_candidate ) && $archive_link_candidate ) {
                    $all_cities_archive_link = $archive_link_candidate;
                }
            }

            if ( $all_cities_archive_link === '#' || is_wp_error( $all_cities_archive_link ) ) {
                $cities_archive_page_slug = 'areas'; // Assuming 'areas' is the slug for your all cities/areas page
                $cities_archive_page = get_page_by_path( $cities_archive_page_slug );
                if ( $cities_archive_page ) {
                    $all_cities_archive_link = get_permalink( $cities_archive_page->ID );
                } else {
                    $all_cities_archive_link = home_url( user_trailingslashit( $city_taxonomy_slug ) );
                }
            }
            if ( is_wp_error( $all_cities_archive_link ) || !$all_cities_archive_link ) {
                $all_cities_archive_link = '#';
            }
            ?>
            <a class="areas-button" href="<?php echo esc_url( $all_cities_archive_link ); ?>">
                <?php esc_html_e( 'View all', 'cob_theme' ); ?>
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281171 7.68509C-0.0843415 7.31958 -0.0923715 6.73316 0.253053 6.3556L6.06104 0.535563C6.24982 0.346785 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535563C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="black" />
                </svg>
            </a>
        </div>
        <div class="swiper mySwiper"> <?php // Ensure Swiper JS is initialized for this class ?>
            <div class="swiper-wrapper">
                <?php
                if ( ! empty( $cities ) && ! is_wp_error( $cities ) ) :
                    foreach ( $cities as $city ) :
                        // Ensure $city is a WP_Term object
                        if ( ! $city instanceof WP_Term ) {
                            continue;
                        }

                        $city_link = get_term_link( $city );
                        $city_name = $city->name;
                        if ( is_wp_error( $city_link ) ) {
                            $city_link = '#';
                        }

                        // Get City Image (if you have a system for it)
                        // $city_image_attachment_id = get_term_meta( $city->term_id, $city_image_meta_key, true );
                        // $city_display_image_url = $theme_dir . '/assets/imgs/default-city.jpg'; // Default city image
                        // if ( $city_image_attachment_id && is_numeric($city_image_attachment_id) ) {
                        //     $img_data = wp_get_attachment_image_src( (int) $city_image_attachment_id, 'medium' );
                        //     if ( $img_data && isset($img_data[0]) ) {
                        //         $city_display_image_url = $img_data[0];
                        //     }
                        // }
                        // For now, since there's no image in your card structure, this part is commented out.
                        // If you add an <img> tag for the city, you can use $city_display_image_url.

                        // Count compounds linked to this city
                        // This assumes 'compound_city' meta on 'compound' terms stores the city's term_id
                        $compounds_in_city_args = [
                            'taxonomy'   => 'compound', // The taxonomy of compounds
                            'hide_empty' => false,
                            'meta_query' => [
                                [
                                    'key'     => 'compound_city', // The meta key on compound terms
                                    'value'   => $city->term_id,
                                    'compare' => '=', // Exact match for the city term ID
                                    'type'    => 'NUMERIC'
                                ]
                            ],
                            'fields' => 'ids', // More efficient, we only need the count
                        ];
                        $compounds_in_city = get_terms( $compounds_in_city_args );
                        $compound_count = 0;
                        if ( !is_wp_error($compounds_in_city) && is_array($compounds_in_city) ) {
                            $compound_count = count( $compounds_in_city );
                        }

                        // Count properties directly associated with this city
                        // This uses the built-in WordPress term relationship, not a meta field on properties.
                        $property_count = $city->count; // This is the number of posts directly assigned to this city term.

                        ?>

                        <div class="swiper-slide">
                            <a href="<?php echo esc_url( $city_link ); ?>" class="card">
                                <?php /* If you want to display the city image, you'd add an <img> tag here using $city_display_image_url */ ?>
                                <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.0555 18.1727V10.9445M16.0555 10.9445H8.82735M16.0555 10.9445L5.36827 21.6318M11.2552 24.78C14.8933 25.5005 18.8123 24.4511 21.6318 21.6318C26.1228 17.1407 26.1228 9.85932 21.6318 5.36828C17.1406 0.87724 9.85929 0.87724 5.36827 5.36828C2.54889 8.18766 1.49947 12.1067 2.21998 15.7448" stroke="#EC3C43" stroke-width="2.42105" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p><?php echo esc_html( number_format_i18n( $compound_count ) ) . ' ' . __('Compounds', 'cob_theme'); ?></p>
                                <span><?php echo esc_html( number_format_i18n( $property_count ) ) . ' ' . __('Properties', 'cob_theme'); ?></span>
                                <button><?php echo esc_html( $city_name ); ?></button>
                            </a>
                        </div>

                    <?php
                        // wp_reset_postdata(); // Not needed here as we are not in a WP_Query post loop
                    endforeach;
                else :
                    // No cities found
                    echo '<div class="swiper-slide no-items-found-slide"><p>' . esc_html__( 'No areas available at the moment.', 'cob_theme' ) . '</p></div>';
                endif;
                ?>
            </div>

            <div class="swiper-button-prev">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.66602 6.00033H18.3327M1.66602 6.00033C1.66602 4.54158 5.82081 1.81601 6.87435 0.791992M1.66602 6.00033C1.66602 7.45908 5.82081 10.1847 6.87435 11.2087" stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="swiper-button-next">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.334 5.99967L1.66732 5.99967M18.334 5.99967C18.334 7.45842 14.1792 10.184 13.1257 11.208M18.334 5.99967C18.334 4.54092 14.1792 1.8153 13.1257 0.791341" stroke="#fff" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
