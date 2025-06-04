<?php
/**
 * Most Searched Compounds Template
 *
 * @package Capital_of_Business
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$theme_dir = get_template_directory_uri();

// It's good practice to use the config from your importer if possible,
// or ensure these keys match.
// For this example, we'll hardcode the key used by the latest importer script.
$cover_image_meta_key = '_compound_cover_image_id'; // As per your importer script config

$compounds = get_terms( [
    'taxonomy'   => 'compound',
    'hide_empty' => false,
    // Consider adding 'number' => some_limit if you only need a certain amount before sorting,
    // though sorting all then slicing is also fine for moderate numbers of terms.
] );

$compound_modified = array();

if ( ! empty( $compounds ) && ! is_wp_error( $compounds ) ) {
    foreach ( $compounds as $compound ) {
        $args = [
            'post_type'      => 'properties', // Make sure this is your correct property post type
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
            'fields'         => 'ids', // More efficient as we only need the date from one post
        ];
        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            // $query->the_post(); // Not needed if using 'fields' => 'ids'
            // $last_modified = get_the_modified_date( 'U' );
            $last_modified = get_post_modified_time( 'U', false, $query->posts[0] );
            $compound_modified[ $compound->term_id ] = $last_modified;
        } else {
            // If a compound has no properties, its last modified date for sorting purposes could be its term creation/modification date,
            // or simply a very old date to push it to the bottom. Using 0 is fine for sorting.
            // Alternatively, you could use $compound->term_id creation time if available or relevant.
            // For now, 0 means it will be sorted as less recent.
            $compound_modified[ $compound->term_id ] = 0;
        }
        // wp_reset_postdata(); // Not strictly necessary if 'fields' => 'ids' and not calling the_post()
    }

    // Sort compounds by the last modified date of their properties
    usort( $compounds, function( $a, $b ) use ( $compound_modified ) {
        $modified_a = $compound_modified[ $a->term_id ] ?? 0;
        $modified_b = $compound_modified[ $b->term_id ] ?? 0;
        return $modified_b - $modified_a; // Sorts descending (most recent first)
    } );

    // Get the top 9 compounds
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
            <div class="swiper swiper1"> <?php // Ensure Swiper JS is initialized for this class ?>
                <div class="swiper-wrapper">
                    <?php foreach ( $compounds as $compound ) : ?>
                        <div class="swiper-slide">
                            <a href="<?php echo esc_url( get_term_link( $compound ) ); ?>" class="compounds-card">
                                <div class="top-card-comp">
                                    <h6><?php echo esc_html( $compound->name ); ?></h6>
                                    <span>
                                        <?php
                                        // Attempt to get a specific property count meta, otherwise use default term count
                                        // Note: 'propertie_count' has a typo, usually it's 'properties_count'
                                        $prop_count = get_term_meta( $compound->term_id, 'properties_count', true ); // Corrected typo
                                        if ( ! $prop_count || !is_numeric($prop_count) ) { // Check if it's not set or not a number
                                            $prop_count = $compound->count; // Fallback to the number of posts associated with the term
                                        }
                                        echo esc_html( number_format_i18n( (int) $prop_count ) ) . ' ' . esc_html__( 'Properties', 'cob_theme' );
                                        ?>
                                    </span>
                                </div>

                                <?php
                                // Get the cover image using the attachment ID stored in term meta
                                $attachment_id = get_term_meta( $compound->term_id, $cover_image_meta_key, true );
                                $image_url = $theme_dir . '/assets/imgs/default.jpg'; // Default image

                                if ( $attachment_id ) {
                                    // Get image URL by desired size. 'medium', 'large', 'thumbnail', or a custom registered size.
                                    $image_data = wp_get_attachment_image_src( $attachment_id, 'medium' ); // Or 'large', 'medium_large' etc.
                                    if ( $image_data && isset($image_data[0]) ) {
                                        $image_url = $image_data[0];
                                    }
                                }
                                ?>
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $compound->name ); ?>" class="lazyload"> <?php // Ensure lazyload is initialized ?>
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
            <p><?php esc_html_e( 'No compounds available at the moment.', 'cob_theme' ); ?></p>
        <?php endif; ?>
    </div>
</div>
