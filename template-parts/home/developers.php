<?php
/**
 * Template for displaying a slider of Developers.
 * Assumes 'developer' taxonomy and a meta field for logo attachment ID.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$theme_dir = get_template_directory_uri();
// This should match the meta key used by your manual field and importer for the developer's logo.
// It's defined as COB_DEVELOPER_LOGO_META_KEY in the manual_developer_logo_field_updated artifact.
$developer_logo_meta_key = '_developer_logo_id';

$developers_args = [
    'taxonomy'   => 'developer', // Ensure this is your correct developer taxonomy slug
    'hide_empty' => false,     // Set to true if you only want developers with associated posts/compounds
    'number'     => 9,         // Limit to 9 developers for the slider
    // Consider adding 'orderby' and 'order' if you need specific sorting
    // e.g., 'orderby' => 'name', 'order' => 'ASC'
    // or 'orderby' => 'count', 'order' => 'DESC' (by number of associated posts)
];
$developers = get_terms( $developers_args );

?>

<div class="motaoron"> <?php // Consider renaming class to something like "developers-slider-section" for clarity ?>
    <div class="container">
        <div class="top-motaoron"> <?php // Consider "top-developers-header" ?>
            <div class="right-motaoron"> <?php // Consider "developers-header-title" ?>
                <h3 class="head"><?php esc_html_e( 'Developers', 'cob_theme' ); ?></h3>
            </div>
            <?php
            $all_developers_page_slug = 'developers'; // Slug of your "All Developers" page
            $developer_taxonomy_name = 'developer';   // The slug of your 'developer' taxonomy

            // Attempt 1: Link to a Custom Post Type archive if 'developer' is also a CPT
            $all_developers_link = get_post_type_archive_link( $developer_taxonomy_name );

            // Attempt 2: Link to Taxonomy archive if function exists (WP 4.5+)
            if ( ! $all_developers_link || is_wp_error( $all_developers_link ) ) {
                if ( function_exists( 'get_taxonomy_archive_link' ) ) {
                    $all_developers_link = get_taxonomy_archive_link( $developer_taxonomy_name );
                }
                // If function doesn't exist, $all_developers_link remains as is (false or error from CPT check)
                // The next block will handle further fallbacks.
            }

            // Attempt 3: Fallback to a specific page by slug if previous attempts failed
            if ( ! $all_developers_link || is_wp_error( $all_developers_link ) ) {
                $developers_page = get_page_by_path( $all_developers_page_slug );
                if ( $developers_page ) {
                    $all_developers_link = get_permalink( $developers_page->ID );
                } else {
                    // Attempt 4: Construct a basic link if page not found (assumes pretty permalinks)
                    // This is a more direct guess if get_taxonomy_archive_link was the one that failed or was missing.
                    $all_developers_link = home_url( user_trailingslashit( $developer_taxonomy_name ) );

                    // Final Fallback: If all else fails or constructed URL is still an error/empty
                    if ( is_wp_error( $all_developers_link ) || ! $all_developers_link ) {
                        $all_developers_link = '#'; // Ultimate fallback
                    }
                }
            }
            ?>
            <a href="<?php echo esc_url( $all_developers_link ); ?>" class="motaoron-button">
                <?php esc_html_e( 'View all', 'cob_theme' ); ?>
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281171 7.68509C-0.0843415 7.31958 -0.0923715 6.73316 0.253053 6.3556L6.06104 0.535563C6.24982 0.346785 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535563C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z"
                            fill="black" />
                </svg>
            </a>
        </div>

        <?php if ( ! empty( $developers ) && ! is_wp_error( $developers ) ) : ?>
            <div class="swiper swiper4"> <?php // Ensure Swiper JS is initialized for this class ?>
                <div class="swiper-wrapper">
                    <?php foreach ( $developers as $developer ) : ?>
                        <?php
                        // Initialize variables for each iteration
                        $developer_link = '#';
                        $image_url      = $theme_dir . '/assets/imgs/developer-default.png';
                        $developer_name = __('Unnamed Developer', 'cob_theme');

                        if ( ! empty( $developer ) && is_object( $developer ) && isset($developer->term_id) ) {
                            $developer_name = $developer->name;
                            $developer_link_candidate = get_term_link( $developer );

                            if ( !is_wp_error( $developer_link_candidate ) ) {
                                $developer_link = $developer_link_candidate;
                            }

                            $attachment_id = get_term_meta( $developer->term_id, $developer_logo_meta_key, true );

                            if ( $attachment_id ) {
                                $image_data = wp_get_attachment_image_src( $attachment_id, 'medium' );
                                if ( $image_data && isset($image_data[0]) ) {
                                    $image_url = $image_data[0];
                                }
                            }
                        }
                        ?>

                        <div class="swiper-slide">
                            <a href="<?php echo esc_url( $developer_link ); ?>" class="motaoron-img">
                                <img data-src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $developer_name ); ?>" class="lazyload">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="swiper-button-prev">
                    <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M1.66602 6.00033H18.3327M1.66602 6.00033C1.66602 4.54158 5.82081 1.81601 6.87435 0.791992M1.66602 6.00033C1.66602 7.45908 5.82081 10.1847 6.87435 11.2087"
                                stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div class="swiper-button-next">
                    <svg width="20" height="12" viewBox="0 0 20 12" fill="white" xmlns="http://www.w3.org/2000/svg">
                        <path
                                d="M18.334 5.99967L1.66732 5.99967M18.334 5.99967C18.334 7.45842 14.1792 10.184 13.1257 11.208M18.334 5.99967C18.334 4.54092 14.1792 1.8153 13.1257 0.791341"
                                stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        <?php else : ?>
            <div class="no-developers-found">
                <p><?php esc_html_e( 'There are currently no Developers.', 'cob_theme' ); ?></p>
            </div>
        <?php endif; ?>
    </div><!-- .container -->
</div>
