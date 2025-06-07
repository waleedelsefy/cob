<?php
/**
 * Template Name: Latest Projects
 * This template has been modified to only display compounds that have a cover image
 * and sorts them by the most recently updated associated property.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$theme_dir = get_template_directory_uri();
// This should match the meta key used by your importer script for the compound's cover image
$compound_cover_image_meta_key = '_compound_cover_image_id';
?>

<div class="projects">
    <div class="container">
        <div class="top-projects">
            <div class="right-projects">
                <!-- Display header and description -->
                <h3 class="head"><?php esc_html_e( 'Latest Projects', 'cob_theme' ); ?></h3>
                <h5><?php esc_html_e( 'Explore a selection of our latest available real estate projects', 'cob_theme' ); ?></h5>
            </div>
            <!-- Link to the project archive page -->
            <?php
            $archive_link = get_post_type_archive_link( 'project' );
            if (!$archive_link) {
                // Fallback link if 'project' CPT is not used or has no archive
                $archive_link = get_permalink( get_page_by_path( 'projects' ) );
            }
            ?>
            <a href="<?php echo esc_url( $archive_link ); ?>" class="projects-button">
                <?php esc_html_e( 'View all', 'cob_theme' ); ?>
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281171 7.68509C-0.0843415 7.31958 -0.0923715 6.73316 0.253053 6.3556L6.06104 0.535563C6.24982 0.346785 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535563C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="white"/>
                </svg>
            </a>
        </div>

        <?php
        // MODIFICATION: Using an advanced query to get compounds sorted by the last modified property within them.
        $all_compounds = get_terms([
            'taxonomy'   => 'compound',
            'hide_empty' => false,
        ]);

        $compound_modified = [];
        $compounds = []; // Initialize as empty array

        if ( ! empty( $all_compounds ) && ! is_wp_error( $all_compounds ) ) {
            foreach ( $all_compounds as $compound ) {
                $property_query = new WP_Query([
                    'post_type'      => 'properties', // Ensure this is your correct property post type
                    'posts_per_page' => 1,
                    'orderby'        => 'modified',
                    'order'          => 'DESC',
                    'tax_query'      => [
                        [
                            'taxonomy' => 'compound',
                            'field'    => 'term_id',
                            'terms'    => $compound->term_id,
                        ],
                    ],
                    'fields'         => 'ids',
                ]);

                if ( $property_query->have_posts() ) {
                    $compound_modified[$compound->term_id] = get_post_modified_time('U', false, $property_query->posts[0]);
                } else {
                    $compound_modified[$compound->term_id] = 0; // Push compounds with no properties to the end
                }
            }

            // Sort all compounds by the collected last modified date
            usort($all_compounds, function($a, $b) use ($compound_modified) {
                $modified_a = $compound_modified[$a->term_id] ?? 0;
                $modified_b = $compound_modified[$b->term_id] ?? 0;
                return $modified_b - $modified_a; // Sort descending (most recent first)
            });

            // After sorting, assign the top 9 to the $compounds variable for the loop.
            $compounds = array_slice($all_compounds, 0, 9);
        }
        ?>

        <?php if ( ! empty( $compounds ) && ! is_wp_error( $compounds ) ) : ?>
            <div class="swiper swiper2"> <?php // Ensure Swiper JS is initialized for this class ?>
                <div class="swiper-wrapper">
                    <?php foreach ( $compounds as $compound ) : ?>
                        <?php
                        // Main filter: First, check if the main cover image exists.
                        $main_image_attachment_id = get_term_meta( $compound->term_id, $compound_cover_image_meta_key, true );

                        // Only render the slide if the attachment ID is found.
                        if ( $main_image_attachment_id ) :
                            // Since the ID exists, get the URL.
                            $image_data = wp_get_attachment_image_src( $main_image_attachment_id, 'medium_large' );
                            // If the image data is invalid (e.g., image deleted), skip this compound.
                            if ( !$image_data || !isset($image_data[0]) ) {
                                continue;
                            }
                            $main_image_display_url = $image_data[0];
                            ?>
                            <div class="swiper-slide">
                                <a href="<?php echo esc_url( get_term_link( $compound ) ); ?>" class="projects-card">
                                    <div class="top-card-proj">
                                        <?php
                                        // Developer Logo logic remains the same.
                                        $dev_logo_url = get_term_meta( $compound->term_id, 'dev_logo', true );
                                        $dev_image_display_url = $dev_logo_url ? $dev_logo_url : $theme_dir . '/assets/imgs/developer-default.png';

                                        $developer_term_id = get_term_meta( $compound->term_id, '_compound_developer', true );
                                        $developer_name_alt = $compound->name; // Fallback
                                        if ($developer_term_id) {
                                            $developer_term = get_term($developer_term_id, 'developer');
                                            if ($developer_term && !is_wp_error($developer_term)) {
                                                $developer_name_alt = $developer_term->name;
                                            }
                                        }
                                        ?>
                                        <img data-src="<?php echo esc_url( $dev_image_display_url ); ?>" alt="<?php echo esc_attr( $developer_name_alt ); ?> Logo" class="lazyload">
                                    </div>

                                    <?php // Image is fetched above, just display it here. ?>
                                    <img class="main-img lazyload" data-src="<?php echo esc_url( $main_image_display_url ); ?>" alt="<?php echo esc_attr( $compound->name ); ?>" >

                                    <div class="bottom-card-proj">
                                        <button>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14.5 9C14.5 10.3807 13.3807 11.5 12 11.5C10.6193 11.5 9.5 10.3807 9.5 9C9.5 7.61929 10.6193 6.5 12 6.5C13.3807 6.5 14.5 7.61929 14.5 9Z" stroke="#E92028" stroke-width="1.5"/>
                                                <path d="M13.2574 17.4936C12.9201 17.8184 12.4693 18 12.0002 18C11.531 18 11.0802 17.8184 10.7429 17.4936C7.6543 14.5008 3.51519 11.1575 5.53371 6.30373C6.6251 3.67932 9.24494 2 12.0002 2C14.7554 2 17.3752 3.67933 18.4666 6.30373C20.4826 11.1514 16.3536 14.5111 13.2574 17.4936Z" stroke="#E92028" stroke-width="1.5"/>
                                                <path d="M18 20C18 21.1046 15.3137 22 12 22C8.68629 22 6 21.1046 6 20" stroke="#E92028" stroke-width="1.5" stroke-linecap="round"/>
                                            </svg>
                                            <?php echo esc_html( $compound->name ); ?>
                                        </button>
                                    </div>
                                </a>
                            </div>
                        <?php
                        endif; // End the check for the main image attachment ID.
                        ?>
                    <?php endforeach; ?>
                </div>
                <!-- Navigation Buttons -->
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
            </div><!-- .swiper -->
        <?php else : ?>
            <div class="no-projects-found">
                <p><?php esc_html_e( 'No projects available at the moment that match the criteria.', 'cob_theme' ); ?></p>
            </div>
        <?php endif; ?>
    </div><!-- .container -->
</div>
