<?php
/**
 * WordPress Admin Tool: Merge Duplicate Compound Taxonomy Terms by Name.
 *
 * WARNING: This script makes direct changes to your database by reassigning posts
 * and deleting terms. ALWAYS BACKUP YOUR DATABASE AND WEBSITE COMPLETELY
 * BEFORE RUNNING THIS SCRIPT. Test on a staging environment first.
 *
 * This script will:
 * 1. Find 'compound' taxonomy terms with the same name.
 * 2. For each set of duplicates, designate a primary term.
 * 3. Reassign posts from duplicate terms to the primary term.
 * 4. Attempt to transfer the cover image meta if the primary term doesn't have one.
 * 5. Delete the duplicate terms.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// --- Configuration ---
define( 'COB_MERGE_TARGET_TAXONOMY', 'compound' ); // The taxonomy to process
define( 'COB_MERGE_COVER_IMAGE_META_KEY', '_compound_cover_image_id' ); // Meta key for cover image attachment ID
// --- End Configuration ---

/**
 * Register the admin page for the merge tool.
 */
function cob_register_merge_compounds_tool_page() {
    add_management_page(
        __( 'Merge Duplicate Compounds', 'cob_theme' ), // Page title
        __( 'Merge Compounds', 'cob_theme' ),          // Menu title
        'manage_categories',                             // Capability (manage_terms for specific taxonomy might be better)
        'cob-merge-duplicate-compounds',                 // Menu slug
        'cob_render_merge_compounds_tool_page'         // Function to display the page
    );
}
add_action( 'admin_menu', 'cob_register_merge_compounds_tool_page' );

/**
 * Render the admin page for the merge tool.
 */
function cob_render_merge_compounds_tool_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <div class="notice notice-error">
            <p><strong><?php _e( 'WARNING:', 'cob_theme' ); ?></strong> <?php _e( 'This tool will make permanent changes to your database by reassigning posts and deleting taxonomy terms. It is crucial to have a complete backup of your website (files and database) before proceeding. Test on a staging environment first if possible.', 'cob_theme' ); ?></p>
        </div>

        <p><?php printf( esc_html__( 'This tool will scan the "%s" taxonomy for terms that have the exact same name and attempt to merge them.', 'cob_theme' ), esc_html(COB_MERGE_TARGET_TAXONOMY) ); ?></p>
        <p><?php printf( esc_html__( 'For each group of duplicate-named terms, one term will be chosen as the "primary" term (usually the oldest or one with a cover image). Posts will be reassigned to this primary term, and the cover image (meta key: %s) will be transferred if the primary term doesn_t have one. Other duplicate terms will then be deleted.', 'cob_theme' ), '<code>' . esc_html(COB_MERGE_COVER_IMAGE_META_KEY) . '</code>' ); ?></p>

        <form method="post" action="">
            <?php wp_nonce_field( 'cob_merge_compounds_nonce_action', '_cob_merge_compounds_nonce' ); ?>
            <p>
                <label for="cob_merge_confirmation">
                    <input type="checkbox" name="cob_merge_confirmation" id="cob_merge_confirmation" value="yes">
                    <?php _e( 'I have backed up my database and understand the risks.', 'cob_theme' ); ?>
                </label>
            </p>
            <?php submit_button( __( 'Find and Merge Duplicate Compounds', 'cob_theme' ), 'primary', 'cob_do_merge_compounds', true, array('disabled' => 'disabled') ); // Button disabled initially ?>
        </form>

        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('#cob_merge_confirmation').on('change', function(){
                    if ($(this).is(':checked')) {
                        $('#cob_do_merge_compounds').prop('disabled', false);
                    } else {
                        $('#cob_do_merge_compounds').prop('disabled', true);
                    }
                });
            });
        </script>

        <?php
        if ( isset( $_POST['cob_do_merge_compounds'] ) && isset( $_POST['_cob_merge_compounds_nonce'] ) ) {
            if ( ! wp_verify_nonce( $_POST['_cob_merge_compounds_nonce'], 'cob_merge_compounds_nonce_action' ) ) {
                echo '<div class="notice notice-error"><p>' . esc_html__( 'Nonce verification failed. Action aborted.', 'cob_theme' ) . '</p></div>';
                return;
            }
            if ( ! isset( $_POST['cob_merge_confirmation'] ) || $_POST['cob_merge_confirmation'] !== 'yes' ) {
                echo '<div class="notice notice-warning"><p>' . esc_html__( 'You must confirm that you have backed up your database and understand the risks.', 'cob_theme' ) . '</p></div>';
                return;
            }
            if ( ! current_user_can( 'manage_categories' ) ) { // Or 'manage_terms' with taxonomy
                echo '<div class="notice notice-error"><p>' . esc_html__( 'You do not have sufficient permissions to perform this action.', 'cob_theme' ) . '</p></div>';
                return;
            }

            echo '<h2>' . esc_html__( 'Merge Process Log:', 'cob_theme' ) . '</h2>';
            echo '<div id="merge-log" style="background: #f7f7f7; border: 1px solid #e5e5e5; padding: 10px; margin-top: 15px; max-height: 600px; overflow-y: auto; font-family: monospace; white-space: pre-wrap;">';
            cob_execute_merge_duplicate_compounds();
            echo '</div>';
        }
        ?>
    </div>
    <?php
}


/**
 * Executes the process of finding and merging duplicate terms.
 */
function cob_execute_merge_duplicate_compounds() {
    $taxonomy = COB_MERGE_TARGET_TAXONOMY;
    $cover_image_meta_key = COB_MERGE_COVER_IMAGE_META_KEY;
    $all_terms_merged_count = 0;

    echo "<p>" . sprintf( esc_html__( 'Starting duplicate term check for taxonomy: %s', 'cob_theme' ), "<strong>" . esc_html( $taxonomy ) . "</strong>" ) . "</p>";

    $terms = get_terms( array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'fields'     => 'all', // Get full term objects
    ) );

    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        echo "<p>" . esc_html__( 'No terms found or an error occurred while fetching terms.', 'cob_theme' ) . "</p>";
        return;
    }

    $terms_by_name = array();
    foreach ( $terms as $term ) {
        $terms_by_name[ $term->name ][] = $term;
    }

    $found_duplicates = false;

    foreach ( $terms_by_name as $name => $duplicate_terms_group ) {
        if ( count( $duplicate_terms_group ) > 1 ) {
            $found_duplicates = true;
            echo "<hr><p>" . sprintf( esc_html__( 'Found %d terms with the name: "%s"', 'cob_theme' ), count( $duplicate_terms_group ), "<strong>" . esc_html( $name ) . "</strong>" ) . "</p>";

            // Sort by term_id to prefer keeping the oldest as primary
            // Or, you could sort by which one has an image, or most posts, etc.
            usort( $duplicate_terms_group, function ( $a, $b ) {
                return $a->term_id - $b->term_id;
            } );

            // Try to find a primary term that has a cover image, otherwise take the first (oldest)
            $primary_term = $duplicate_terms_group[0]; // Default to oldest
            $primary_term_has_image = (bool) get_term_meta( $primary_term->term_id, $cover_image_meta_key, true );

            if (!$primary_term_has_image) {
                foreach($duplicate_terms_group as $potential_primary) {
                    if (get_term_meta( $potential_primary->term_id, $cover_image_meta_key, true )) {
                        $primary_term = $potential_primary;
                        $primary_term_has_image = true;
                        echo "<p>" . sprintf( esc_html__( 'Selected term ID %d as primary because it has a cover image.', 'cob_theme' ), $primary_term->term_id ) . "</p>";
                        break;
                    }
                }
            }

            echo "<p>" . sprintf( esc_html__( 'Designated Term ID %d (Slug: %s) as the primary term to keep.', 'cob_theme' ), $primary_term->term_id, "<code>" . esc_html( $primary_term->slug ) . "</code>" ) . "</p>";

            $terms_to_delete_ids = array();

            foreach ( $duplicate_terms_group as $duplicate_term ) {
                if ( $duplicate_term->term_id === $primary_term->term_id ) {
                    continue; // Skip the primary term itself
                }

                echo "<p>" . sprintf( esc_html__( 'Processing duplicate Term ID %d (Slug: %s)...', 'cob_theme' ), $duplicate_term->term_id, "<code>" . esc_html( $duplicate_term->slug ) . "</code>" ) . "</p>";

                // 1. Reassign posts
                $posts_args = array(
                    'post_type'      => 'any', // Consider specifying post types if 'compound' is used by many
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'tax_query'      => array(
                        array(
                            'taxonomy' => $taxonomy,
                            'field'    => 'term_id',
                            'terms'    => $duplicate_term->term_id,
                        ),
                    ),
                );
                $posts_with_duplicate_term = get_posts( $posts_args );

                if ( ! empty( $posts_with_duplicate_term ) ) {
                    echo "<ul>";
                    foreach ( $posts_with_duplicate_term as $post_id ) {
                        // Add the primary term to the post
                        $append_result = wp_set_object_terms( $post_id, $primary_term->term_id, $taxonomy, true ); // true to append
                        if (is_wp_error($append_result)) {
                            echo "<li style='color:orange;'>" . sprintf( esc_html__( 'Failed to add primary term %d to Post ID %d. Error: %s', 'cob_theme' ), $primary_term->term_id, $post_id, esc_html($append_result->get_error_message()) ) . "</li>";
                        } else {
                            // Remove the duplicate term from the post (wp_set_object_terms with append=true doesn't remove old ones)
                            // So we need to explicitly remove it if we want only the primary one.
                            // Or, if you want to ensure it's only the primary, set the terms without appending for the first one.
                            // For safety, let's re-set terms ensuring only primary is there for this taxonomy.
                            $set_terms_result = wp_set_object_terms( $post_id, $primary_term->term_id, $taxonomy, false ); // false to replace
                            if (is_wp_error($set_terms_result)) {
                                echo "<li style='color:orange;'>" . sprintf( esc_html__( 'Failed to set primary term %d for Post ID %d after reassigning. Error: %s', 'cob_theme' ), $primary_term->term_id, $post_id, esc_html($set_terms_result->get_error_message()) ) . "</li>";
                            } else {
                                echo "<li>" . sprintf( esc_html__( 'Reassigned Post ID %d from term %d to primary term %d.', 'cob_theme' ), $post_id, $duplicate_term->term_id, $primary_term->term_id ) . "</li>";
                            }
                        }
                    }
                    echo "</ul>";
                } else {
                    echo "<p>" . esc_html__( 'No posts found associated with this duplicate term.', 'cob_theme' ) . "</p>";
                }

                // 2. Transfer cover image meta if primary doesn't have one
                if ( ! $primary_term_has_image ) {
                    $duplicate_cover_id = get_term_meta( $duplicate_term->term_id, $cover_image_meta_key, true );
                    if ( $duplicate_cover_id ) {
                        update_term_meta( $primary_term->term_id, $cover_image_meta_key, $duplicate_cover_id );
                        $primary_term_has_image = true; // Mark that primary now has an image
                        echo "<p style='color:green;'>" . sprintf( esc_html__( 'Transferred cover image (Attachment ID: %s) from duplicate term %d to primary term %d.', 'cob_theme' ), esc_html( $duplicate_cover_id ), $duplicate_term->term_id, $primary_term->term_id ) . "</p>";
                    }
                }

                // Add to list for deletion after processing all in group
                $terms_to_delete_ids[] = $duplicate_term->term_id;
            }

            // 3. Delete the processed duplicate terms
            if (!empty($terms_to_delete_ids)) {
                echo "<p>" . esc_html__( 'Attempting to delete the merged (duplicate) terms...', 'cob_theme' ) . "</p><ul>";
                foreach($terms_to_delete_ids as $term_to_delete_id) {
                    $delete_result = wp_delete_term( $term_to_delete_id, $taxonomy );
                    if ( is_wp_error( $delete_result ) ) {
                        echo "<li style='color:red;'>" . sprintf( esc_html__( 'Failed to delete term ID %d. Error: %s', 'cob_theme' ), $term_to_delete_id, esc_html( $delete_result->get_error_message() ) ) . "</li>";
                    } else if ($delete_result === false || $delete_result === 0) { // Deletion failed but not WP_Error
                        echo "<li style='color:red;'>" . sprintf( esc_html__( 'Failed to delete term ID %d (term might not exist or is default).', 'cob_theme' ), $term_to_delete_id ) . "</li>";
                    } else {
                        echo "<li style='color:green;'>" . sprintf( esc_html__( 'Successfully deleted term ID %d.', 'cob_theme' ), $term_to_delete_id ) . "</li>";
                        $all_terms_merged_count++;
                    }
                }
                echo "</ul>";
            }
        }
    }

    if ( ! $found_duplicates ) {
        echo "<p>" . esc_html__( 'No duplicate terms (by name) found in this taxonomy.', 'cob_theme' ) . "</p>";
    } else {
        echo "<hr><p style='font-weight:bold; color:blue;'>" . sprintf( esc_html__( 'Merge process completed. Total duplicate terms (excluding primaries) processed for deletion: %d', 'cob_theme' ), $all_terms_merged_count ) . "</p>";
    }
    echo "<p>" . esc_html__( 'Please flush your permalinks by going to Settings > Permalinks and clicking "Save Changes".', 'cob_theme' ) . "</p>";
}

?>
