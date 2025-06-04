<?php
/**
 * WordPress Admin Tool: Merge Duplicate Developer and City Taxonomy Terms by Name.
 *
 * WARNING: This script makes direct changes to your database by reassigning posts
 * and deleting terms. ALWAYS BACKUP YOUR DATABASE AND WEBSITE COMPLETELY
 * BEFORE RUNNING THIS SCRIPT. Test on a staging environment first.
 *
 * This script will:
 * 1. Find 'developer' taxonomy terms with the same name and merge them.
 * 2. Find 'city' taxonomy terms with the same name and merge them.
 * 3. Attempt to transfer logo/image meta if the primary term doesn't have one.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// --- Configuration ---
define( 'COB_MERGE_DEVELOPER_TAX', 'developer' );
define( 'COB_MERGE_DEVELOPER_LOGO_META_KEY', '_developer_logo_id' ); // Meta key for developer logo attachment ID

define( 'COB_MERGE_CITY_TAX', 'city' );
// **IMPORTANT**: Define the meta key for city images if you use them.
// If cities don't have a dedicated image meta, a logo/image won't be transferred for them.
// define( 'COB_MERGE_CITY_IMAGE_META_KEY', '_city_image_id' ); // Example meta key for city image
// --- End Configuration ---

/**
 * Register the admin page for the merge tool.
 */
function cob_register_merge_dev_city_tool_page() {
    add_management_page(
        __( 'Merge Duplicate Developers/Cities', 'cob_theme' ), // Page title
        __( 'Merge Developers/Cities', 'cob_theme' ),          // Menu title
        'manage_categories',                                     // Capability
        'cob-merge-duplicate-dev-city',                          // Menu slug
        'cob_render_merge_dev_city_tool_page'                  // Function to display the page
    );
}
add_action( 'admin_menu', 'cob_register_merge_dev_city_tool_page' );

/**
 * Render the admin page for the merge tool.
 */
function cob_render_merge_dev_city_tool_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <div class="notice notice-error">
            <p><strong><?php _e( 'WARNING:', 'cob_theme' ); ?></strong> <?php _e( 'This tool will make permanent changes to your database by reassigning posts/objects and deleting taxonomy terms. It is crucial to have a complete backup of your website (files and database) before proceeding. Test on a staging environment first if possible.', 'cob_theme' ); ?></p>
        </div>

        <p><?php esc_html_e( 'This tool will scan for terms with the exact same name within the "Developer" and "City" taxonomies and attempt to merge them.', 'cob_theme' ); ?></p>
        <p><?php esc_html_e( 'For each group of duplicate-named terms, one term will be chosen as the "primary" term. Associated objects will be reassigned to this primary term. If the primary term is missing a logo/image and a duplicate has one, it will be transferred. Other duplicate terms will then be deleted.', 'cob_theme' ); ?></p>

        <form method="post" action="">
            <?php wp_nonce_field( 'cob_merge_dev_city_nonce_action', '_cob_merge_dev_city_nonce' ); ?>
            <p>
                <label for="cob_merge_dev_city_confirmation">
                    <input type="checkbox" name="cob_merge_dev_city_confirmation" id="cob_merge_dev_city_confirmation" value="yes">
                    <?php _e( 'I have backed up my database and understand the risks involved in merging terms.', 'cob_theme' ); ?>
                </label>
            </p>
            <?php submit_button( __( 'Find and Merge All Duplicates (Developers & Cities)', 'cob_theme' ), 'primary', 'cob_do_merge_dev_city', true, array('disabled' => 'disabled') ); ?>
        </form>

        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('#cob_merge_dev_city_confirmation').on('change', function(){
                    if ($(this).is(':checked')) {
                        $('#cob_do_merge_dev_city').prop('disabled', false);
                    } else {
                        $('#cob_do_merge_dev_city').prop('disabled', true);
                    }
                });
            });
        </script>

        <?php
        if ( isset( $_POST['cob_do_merge_dev_city'] ) && isset( $_POST['_cob_merge_dev_city_nonce'] ) ) {
            if ( ! wp_verify_nonce( $_POST['_cob_merge_dev_city_nonce'], 'cob_merge_dev_city_nonce_action' ) ) {
                echo '<div class="notice notice-error"><p>' . esc_html__( 'Nonce verification failed. Action aborted.', 'cob_theme' ) . '</p></div>';
                return;
            }
            if ( ! isset( $_POST['cob_merge_dev_city_confirmation'] ) || $_POST['cob_merge_dev_city_confirmation'] !== 'yes' ) {
                echo '<div class="notice notice-warning"><p>' . esc_html__( 'You must confirm that you have backed up your database and understand the risks.', 'cob_theme' ) . '</p></div>';
                return;
            }
            if ( ! current_user_can( 'manage_categories' ) ) {
                echo '<div class="notice notice-error"><p>' . esc_html__( 'You do not have sufficient permissions to perform this action.', 'cob_theme' ) . '</p></div>';
                return;
            }

            echo '<h2>' . esc_html__( 'Merge Process Log:', 'cob_theme' ) . '</h2>';
            echo '<div id="merge-log-dev-city" style="background: #f7f7f7; border: 1px solid #e5e5e5; padding: 10px; margin-top: 15px; max-height: 800px; overflow-y: auto; font-family: monospace; white-space: pre-wrap;">';

            // Merge Developers
            echo "<h3>" . esc_html__( 'Processing Developers...', 'cob_theme' ) . "</h3>";
            cob_execute_merge_terms_by_name( COB_MERGE_DEVELOPER_TAX, COB_MERGE_DEVELOPER_LOGO_META_KEY );

            echo "<hr style='margin: 20px 0;'>";

            // Merge Cities
            echo "<h3>" . esc_html__( 'Processing Cities...', 'cob_theme' ) . "</h3>";
            $city_image_meta_key = defined('COB_MERGE_CITY_IMAGE_META_KEY') ? COB_MERGE_CITY_IMAGE_META_KEY : null;
            cob_execute_merge_terms_by_name( COB_MERGE_CITY_TAX, $city_image_meta_key );

            echo '</div>';
        }
        ?>
    </div>
    <?php
}

/**
 * Executes the process of finding and merging duplicate terms for a given taxonomy.
 *
 * @param string $taxonomy_slug The slug of the taxonomy to process.
 * @param string|null $image_meta_key The meta key for the term's image (attachment ID). Null if no image handling.
 */
function cob_execute_merge_terms_by_name( $taxonomy_slug, $image_meta_key = null ) {
    $all_terms_merged_count = 0;

    echo "<p>" . sprintf( esc_html__( 'Starting duplicate term check for taxonomy: %s', 'cob_theme' ), "<strong>" . esc_html( $taxonomy_slug ) . "</strong>" ) . "</p>";

    if ( ! taxonomy_exists( $taxonomy_slug ) ) {
        echo "<p style='color:red;'>" . sprintf( esc_html__( 'Error: Taxonomy "%s" does not exist. Skipping.', 'cob_theme' ), esc_html( $taxonomy_slug ) ) . "</p>";
        return;
    }

    $terms = get_terms( array(
        'taxonomy'   => $taxonomy_slug,
        'hide_empty' => false,
        'fields'     => 'all',
    ) );

    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        echo "<p>" . sprintf( esc_html__( 'No terms found in "%s" or an error occurred.', 'cob_theme' ), esc_html( $taxonomy_slug ) ) . "</p>";
        return;
    }

    $terms_by_name = array();
    foreach ( $terms as $term ) {
        if ( ! $term instanceof WP_Term ) continue;
        $terms_by_name[ $term->name ][] = $term;
    }

    $found_duplicates_for_taxonomy = false;

    foreach ( $terms_by_name as $name => $duplicate_terms_group ) {
        if ( count( $duplicate_terms_group ) > 1 ) {
            $found_duplicates_for_taxonomy = true;
            echo "<hr style='border-top-color: #ccc;'><p>" . sprintf( esc_html__( 'Found %d terms in "%s" with the name: "%s"', 'cob_theme' ), count( $duplicate_terms_group ), esc_html($taxonomy_slug), "<strong>" . esc_html( $name ) . "</strong>" ) . "</p>";

            usort( $duplicate_terms_group, function ( $a, $b ) {
                return $a->term_id - $b->term_id; // Prefer older term (smaller ID) as primary
            } );

            $primary_term = $duplicate_terms_group[0]; // Default to oldest
            $primary_term_has_image = false;
            if ($image_meta_key) {
                $primary_term_has_image = (bool) get_term_meta( $primary_term->term_id, $image_meta_key, true );
            }

            // If default primary doesn't have an image, try to find one that does among duplicates
            if ($image_meta_key && !$primary_term_has_image) {
                foreach($duplicate_terms_group as $potential_primary_candidate) {
                    if (get_term_meta( $potential_primary_candidate->term_id, $image_meta_key, true )) {
                        $primary_term = $potential_primary_candidate;
                        $primary_term_has_image = true;
                        echo "<p>" . sprintf( esc_html__( 'Selected term ID %d as primary for "%s" because it has an image.', 'cob_theme' ), $primary_term->term_id, esc_html($name) ) . "</p>";
                        break;
                    }
                }
            }

            echo "<p>" . sprintf( esc_html__( 'Designated Term ID %d (Slug: %s) as the primary for "%s".', 'cob_theme' ), $primary_term->term_id, "<code>" . esc_html( $primary_term->slug ) . "</code>", esc_html($name) ) . "</p>";

            $terms_to_delete_ids_in_group = array();

            foreach ( $duplicate_terms_group as $duplicate_term ) {
                if ( $duplicate_term->term_id === $primary_term->term_id ) {
                    continue;
                }

                echo "<p>" . sprintf( esc_html__( 'Processing duplicate Term ID %d (Slug: %s) for "%s"...', 'cob_theme' ), $duplicate_term->term_id, "<code>" . esc_html( $duplicate_term->slug ) . "</code>", esc_html($name) ) . "</p>";

                // 1. Reassign objects (posts, custom post types)
                // Determine which object types are associated with this taxonomy
                $taxonomy_object_types = get_taxonomy( $taxonomy_slug )->object_type;
                if (empty($taxonomy_object_types)) {
                    $taxonomy_object_types = ['post', 'page']; // Fallback, adjust if needed
                    echo "<p style='color:orange;'>" . sprintf( esc_html__( 'Warning: Could not determine object types for taxonomy %s. Falling back to "post", "page". Review if other post types need updating.', 'cob_theme' ), esc_html($taxonomy_slug) ) . "</p>";
                }

                $posts_args = array(
                    'post_type'      => $taxonomy_object_types,
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'tax_query'      => array(
                        array(
                            'taxonomy' => $taxonomy_slug,
                            'field'    => 'term_id',
                            'terms'    => $duplicate_term->term_id,
                        ),
                    ),
                );
                $objects_with_duplicate_term = get_posts( $posts_args );

                if ( ! empty( $objects_with_duplicate_term ) ) {
                    echo "<ul>";
                    foreach ( $objects_with_duplicate_term as $object_id ) {
                        // Replace the duplicate term with the primary term for this object
                        $set_terms_result = wp_set_object_terms( $object_id, $primary_term->term_id, $taxonomy_slug, false ); // false to replace
                        if (is_wp_error($set_terms_result)) {
                            echo "<li style='color:orange;'>" . sprintf( esc_html__( 'Failed to set primary term %d for Object ID %d. Error: %s', 'cob_theme' ), $primary_term->term_id, $object_id, esc_html($set_terms_result->get_error_message()) ) . "</li>";
                        } else {
                            echo "<li>" . sprintf( esc_html__( 'Reassigned Object ID %d from term %d to primary term %d.', 'cob_theme' ), $object_id, $duplicate_term->term_id, $primary_term->term_id ) . "</li>";
                        }
                    }
                    echo "</ul>";
                } else {
                    echo "<p>" . esc_html__( 'No objects found associated with this duplicate term.', 'cob_theme' ) . "</p>";
                }

                // 2. Transfer image meta if primary doesn't have one and this duplicate does
                if ( $image_meta_key && ! $primary_term_has_image ) {
                    $duplicate_cover_id = get_term_meta( $duplicate_term->term_id, $image_meta_key, true );
                    if ( $duplicate_cover_id ) {
                        update_term_meta( $primary_term->term_id, $image_meta_key, $duplicate_cover_id );
                        $primary_term_has_image = true;
                        echo "<p style='color:green;'>" . sprintf( esc_html__( 'Transferred image (Attachment ID: %s) from duplicate term %d to primary term %d.', 'cob_theme' ), esc_html( $duplicate_cover_id ), $duplicate_term->term_id, $primary_term->term_id ) . "</p>";
                    }
                }

                $terms_to_delete_ids_in_group[] = $duplicate_term->term_id;
            }

            if (!empty($terms_to_delete_ids_in_group)) {
                echo "<p>" . esc_html__( 'Attempting to delete the merged (duplicate) terms...', 'cob_theme' ) . "</p><ul>";
                foreach($terms_to_delete_ids_in_group as $term_to_delete_id) {
                    $delete_result = wp_delete_term( $term_to_delete_id, $taxonomy_slug );
                    if ( is_wp_error( $delete_result ) ) {
                        echo "<li style='color:red;'>" . sprintf( esc_html__( 'Failed to delete term ID %d. Error: %s', 'cob_theme' ), $term_to_delete_id, esc_html( $delete_result->get_error_message() ) ) . "</li>";
                    } else if ($delete_result === false || $delete_result === 0) {
                        echo "<li style='color:red;'>" . sprintf( esc_html__( 'Failed to delete term ID %d (term might not exist, be default, or have children).', 'cob_theme' ), $term_to_delete_id ) . "</li>";
                    } else {
                        echo "<li style='color:green;'>" . sprintf( esc_html__( 'Successfully deleted term ID %d.', 'cob_theme' ), $term_to_delete_id ) . "</li>";
                        $all_terms_merged_count++;
                    }
                }
                echo "</ul>";
            }
        }
    }

    if ( ! $found_duplicates_for_taxonomy ) {
        echo "<p>" . sprintf( esc_html__( 'No duplicate terms (by name) found in taxonomy "%s".', 'cob_theme' ), esc_html($taxonomy_slug) ) . "</p>";
    } else {
        echo "<hr style='border-top-color: #ccc;'><p style='font-weight:bold; color:blue;'>" . sprintf( esc_html__( 'Merge process for "%s" completed. Total duplicate terms (excluding primaries) processed for deletion: %d', 'cob_theme' ), esc_html($taxonomy_slug), $all_terms_merged_count ) . "</p>";
    }
    echo "<p>" . esc_html__( 'If any terms were merged, please flush your permalinks by going to Settings > Permalinks and clicking "Save Changes".', 'cob_theme' ) . "</p>";
}

?>
