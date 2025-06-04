<?php
/**
 * WordPress Admin Tool: Find and Edit Compounds, Developers, (and optionally Cities) Missing Images.
 *
 * This script adds an admin page under "Tools" to list:
 * - 'compound' taxonomy terms missing a cover image (meta: _compound_cover_image_id).
 * - 'developer' taxonomy terms missing a logo (meta: _developer_logo_id).
 * - Optionally, 'city' taxonomy terms missing an image (meta: _city_image_id - needs to be defined).
 * It provides direct links to edit each term.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// --- Configuration ---
define( 'COB_MISSING_IMG_COMPOUND_TAX', 'compound' );
define( 'COB_MISSING_IMG_COMPOUND_META_KEY', '_compound_cover_image_id' );

define( 'COB_MISSING_IMG_DEVELOPER_TAX', 'developer' );
define( 'COB_MISSING_IMG_DEVELOPER_META_KEY', '_developer_logo_id' );

// Optional: City Image Configuration - uncomment and adjust if you have city images
// define( 'COB_MISSING_IMG_CITY_TAX', 'city' );
// define( 'COB_MISSING_IMG_CITY_META_KEY', '_city_image_id' ); // Example meta key for city image
// --- End Configuration ---

/**
 * Register the admin page for the Missing Images tool.
 */
function cob_register_missing_images_tool_page() {
    add_management_page(
        __( 'Manage Missing Images', 'cob_theme' ),       // Page title
        __( 'Missing Images', 'cob_theme' ),              // Menu title
        'manage_categories',                                 // Capability (use 'manage_terms' for more fine-grained control if needed)
        'cob-missing-images-tool',                         // Menu slug
        'cob_render_missing_images_tool_page'              // Function to display the page
    );
}
add_action( 'admin_menu', 'cob_register_missing_images_tool_page' );

/**
 * Render the admin page for the Missing Images tool.
 */
function cob_render_missing_images_tool_page() {
    if ( ! current_user_can( 'manage_categories' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'cob_theme' ) );
    }
    ?>
    <div class="wrap cob-missing-images-tool">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p><?php esc_html_e( 'This tool helps you find and manage Compounds, Developers, and Cities that are missing their designated images.', 'cob_theme' ); ?></p>

        <style>
            .cob-missing-images-tool .section { margin-bottom: 30px; padding: 15px; background-color: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); }
            .cob-missing-images-tool .section h2 { margin-top: 0; }
            .cob-missing-images-tool ul { list-style: disc; margin-left: 20px; }
            .cob-missing-images-tool li { margin-bottom: 8px; }
            .cob-missing-images-tool .edit-link { margin-left: 10px; font-size: 0.9em; }
            .cob-missing-images-tool .no-items { color: green; }
        </style>

        <?php
        // Section for Compounds missing cover images
        cob_display_terms_missing_image_section(
            COB_MISSING_IMG_COMPOUND_TAX,
            COB_MISSING_IMG_COMPOUND_META_KEY,
            __( 'Compounds Missing Cover Image', 'cob_theme' ),
            __( 'All compounds have a cover image assigned.', 'cob_theme' )
        );

        // Section for Developers missing logos
        cob_display_terms_missing_image_section(
            COB_MISSING_IMG_DEVELOPER_TAX,
            COB_MISSING_IMG_DEVELOPER_META_KEY,
            __( 'Developers Missing Logo', 'cob_theme' ),
            __( 'All developers have a logo assigned.', 'cob_theme' )
        );

        // Optional Section for Cities missing images
        if ( defined( 'COB_MISSING_IMG_CITY_TAX' ) && defined( 'COB_MISSING_IMG_CITY_META_KEY' ) ) {
            cob_display_terms_missing_image_section(
                COB_MISSING_IMG_CITY_TAX,
                COB_MISSING_IMG_CITY_META_KEY,
                __( 'Cities Missing Image', 'cob_theme' ),
                __( 'All cities have an image assigned (or this feature is not configured).', 'cob_theme' )
            );
        }
        ?>
    </div>
    <?php
}

/**
 * Helper function to display a section for terms missing a specific image meta.
 *
 * @param string $taxonomy_slug The slug of the taxonomy to check.
 * @param string $meta_key The meta key that stores the attachment ID for the image.
 * @param string $section_title The title for this section.
 * @param string $no_items_message Message to display if no items are found.
 */
function cob_display_terms_missing_image_section( $taxonomy_slug, $meta_key, $section_title, $no_items_message ) {
    echo '<div class="section">';
    echo '<h2>' . esc_html( $section_title ) . '</h2>';

    if ( ! taxonomy_exists( $taxonomy_slug ) ) {
        echo '<p style="color:red;">' . sprintf( esc_html__( 'Error: Taxonomy "%s" does not exist.', 'cob_theme' ), esc_html( $taxonomy_slug ) ) . '</p>';
        echo '</div>';
        return;
    }

    $terms_missing_image = get_terms( array(
        'taxonomy'   => $taxonomy_slug,
        'hide_empty' => false,
        'fields'     => 'all',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key'     => $meta_key,
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     => $meta_key,
                'value'   => '', // Also check for empty string value
                'compare' => '=',
            ),
            array(
                'key'     => $meta_key,
                'value'   => '0', // Also check for '0' as some plugins might store this
                'compare' => '=',
            ),
        ),
    ) );

    if ( is_wp_error( $terms_missing_image ) ) {
        echo '<p style="color:red;">' . sprintf( esc_html__( 'Error fetching terms for %s: %s', 'cob_theme' ), esc_html( $taxonomy_slug ), esc_html( $terms_missing_image->get_error_message() ) ) . '</p>';
    } elseif ( empty( $terms_missing_image ) ) {
        echo '<p class="no-items">' . esc_html( $no_items_message ) . '</p>';
    } else {
        echo '<ul>';
        foreach ( $terms_missing_image as $term ) {
            if ( ! $term instanceof WP_Term ) continue;

            $edit_link = get_edit_term_link( $term->term_id, $taxonomy_slug );
            echo '<li>';
            echo esc_html( $term->name ) . ' (ID: ' . esc_html( $term->term_id ) . ')';
            if ( $edit_link ) {
                echo ' <a href="' . esc_url( $edit_link ) . '" class="edit-link button button-small">' . esc_html__( 'Edit', 'cob_theme' ) . '</a>';
            }
            echo '</li>';
        }
        echo '</ul>';
        echo '<p>' . sprintf( esc_html( _n( '%d item found.', '%d items found.', count( $terms_missing_image ), 'cob_theme' ) ), count( $terms_missing_image ) ) . '</p>';
    }
    echo '</div>'; // End .section
}
?>
