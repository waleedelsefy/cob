<?php
/**
 * Most Searched Compounds Template
 *
 * This template fetches compounds (a custom taxonomy), sorts them by the most
 * recently updated property within them, and displays them in a Swiper slider.
 * It includes a critical modification to only display compounds that have a cover image assigned.
 *
 * @package Capital_of_Business
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$theme_dir = get_template_directory_uri();

// Define the meta key for the compound's cover image.
// This should match the key used in your backend (e.g., via ACF or your importer script).
$cover_image_meta_key = '_compound_cover_image_id';

// Retrieve all terms from the 'compound' taxonomy.
$compounds = get_terms( [
    'taxonomy'   => 'compound',
    'hide_empty' => false,
    // For better performance on sites with many terms, consider limiting the number here
    // and adjusting the logic if needed, e.g., 'number' => 100.
] );

$compound_modified = array();

// Proceed only if terms were found.
if ( ! empty( $compounds ) && ! is_wp_error( $compounds ) ) {
    // Loop through each compound to find the last modification date of its properties.
    foreach ( $compounds as $compound ) {
        $args = [
            'post_type'      => 'properties', // Ensure this is your correct property post type slug.
            'posts_per_page' => 9,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'tax_query'      => [
                [
                    'taxonomy' => 'compound',
                    'field'    => 'term_id',
                    'terms'    => $compound->term_id,
                ],
            ],
            'fields'         => 'ids', // More efficient query, as we only need the ID to get the modified time.
        ];
        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            // Store the last modified timestamp of the newest property in the compound.
            $last_modified = get_post_modified_time( 'U', false, $query->posts[0] );
            $compound_modified[ $compound->term_id ] = $last_modified;
        } else {
            // If a compound has no properties, assign 0 to sort it as the least recent.
            $compound_modified[ $compound->term_id ] = 0;
        }
    }

    // Sort the $compounds array based on the collected modification dates.
    usort( $compounds, function( $a, $b ) use ( $compound_modified ) {
        $modified_a = $compound_modified[ $a->term_id ] ?? 0;
        $modified_b = $compound_modified[ $b->term_id ] ?? 0;
        return $modified_b - $modified_a; // Sorts in descending order (most recent first).
    } );

    // After sorting, take the top 9 compounds.
    // Note: The final display might be less than 9 if some of the top compounds lack an image.
    $compounds = array_slice( $compounds, 0, 9 );
}
?>

<div class="compounds">
    <div class="container">
        <div class="top-compounds">
            <div class="right-compounds">
                <h3 class="head"><?php esc_html_e( 'Most Searched Compounds', 'cob_theme' ); ?></h3>
            </div>
        </div>

        <?php if ( ! empty( $compounds ) && ! is_wp_error( $compounds ) ) : ?>
            <div class="swiper swiper1"> <?php // Ensure your Swiper JS is initialized on this class. ?>
                <div class="swiper-wrapper">
                    <?php foreach ( $compounds as $compound ) : ?>
                        <?php
                        // MODIFICATION: Check for an image before rendering the card.
                        // First, get the cover image attachment ID from the term's metadata.
                        $attachment_id = get_term_meta( $compound->term_id, $cover_image_meta_key, true );

                        // Only proceed to render the HTML if a valid attachment ID was found.
                        if ( $attachment_id ) :
                            $image_url = '';
                            // Get the image source URL from the attachment ID.
                            // You can change 'medium' to other sizes like 'large', 'thumbnail', or a custom image size.
                            $image_data = wp_get_attachment_image_src( $attachment_id, 'medium' );
                            if ( $image_data && isset($image_data[0]) ) {
                                $image_url = $image_data[0];
                            } else {
                                // Fallback for the rare case an ID exists but the image is gone. Skip this item.
                                continue;
                            }
                            ?>
                            <div class="swiper-slide">
                                <a href="<?php echo esc_url( get_term_link( $compound ) ); ?>" class="compounds-card">
                                    <div class="top-card-comp">
                                        <h6><?php echo esc_html( $compound->name ); ?></h6>
                                        <span>
                                        <?php
                                        // Attempt to get a specific property count from meta, otherwise use the default term count.
                                        $prop_count = get_term_meta( $compound->term_id, 'properties_count', true );
                                        if ( ! $prop_count || !is_numeric($prop_count) ) {
                                            $prop_count = $compound->count; // Fallback to WP's default post count for the term.
                                        }
                                        echo esc_html( number_format_i18n( (int) $prop_count ) ) . ' ' . esc_html__( 'Properties', 'cob_theme' );
                                        ?>
                                    </span>
                                    </div>

                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $compound->name ); ?>" class="lazyload"> <?php // Ensure your lazyload script is initialized. ?>
                                </a>
                            </div>
                        <?php
                        endif; // End the conditional check for $attachment_id.
                        ?>
                    <?php endforeach; ?>
                </div>

                <!-- Swiper Navigation Buttons -->
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
                <!-- Swiper Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        <?php else : ?>
            <p><?php esc_html_e( 'No compounds available at the moment.', 'cob_theme' ); ?></p>
        <?php endif; ?>
    </div>
</div>
