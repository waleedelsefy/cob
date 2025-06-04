<?php
/**
 * Capital of Business Theme Functions - Compound Image Field (Attachment ID Version)
 *
 * Manages a custom image field for the 'compound' taxonomy, storing the attachment ID.
 * This version is designed to be compatible with the AJAX importer script.
 *
 * @package Capital_of_Business
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define the meta key consistently. This should match your importer's config.
define( 'COB_COMPOUND_COVER_IMAGE_META_KEY', '_compound_cover_image_id' );

/**
 * Add custom field to the Compound taxonomy term add screen.
 * Stores Attachment ID.
 */
function cob_add_compound_cover_image_field() {
    ?>
    <div class="form-field term-group">
        <label for="cob-compound-cover-image-id"><?php _e( 'Compound Cover Image', 'cob_theme' ); ?></label>
        <input type="hidden" name="<?php echo esc_attr( COB_COMPOUND_COVER_IMAGE_META_KEY ); ?>" id="cob-compound-cover-image-id" class="cob-image-attachment-id" value="">
        <div id="cob-compound-cover-image-wrapper" style="margin-bottom: 10px;">
            <?php // Image preview will be populated by JavaScript ?>
        </div>
        <button type="button" class="button cob-upload-image-button"><?php _e( 'Upload/Select Image', 'cob_theme' ); ?></button>
        <button type="button" class="button cob-remove-image-button" style="display:none;"><?php _e( 'Remove Image', 'cob_theme' ); ?></button>
        <p class="description"><?php _e( 'Select the main cover image for this compound.', 'cob_theme' ); ?></p>
    </div>
    <?php
}
add_action( 'compound_add_form_fields', 'cob_add_compound_cover_image_field', 10, 2 );

/**
 * Add custom field to the Compound taxonomy term edit screen.
 * Stores Attachment ID.
 *
 * @param WP_Term $term The term object.
 */
function cob_edit_compound_cover_image_field( $term ) {
    $attachment_id = get_term_meta( $term->term_id, COB_COMPOUND_COVER_IMAGE_META_KEY, true );
    $image_url = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'medium' ) : '';
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="cob-compound-cover-image-id"><?php _e( 'Compound Cover Image', 'cob_theme' ); ?></label>
        </th>
        <td>
            <input type="hidden" name="<?php echo esc_attr( COB_COMPOUND_COVER_IMAGE_META_KEY ); ?>" id="cob-compound-cover-image-id" class="cob-image-attachment-id" value="<?php echo esc_attr( $attachment_id ); ?>">
            <div id="cob-compound-cover-image-wrapper" style="margin-bottom: 10px;">
                <?php if ( $image_url ) : ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" style="max-width: 200px; height: auto;">
                <?php endif; ?>
            </div>
            <button type="button" class="button cob-upload-image-button"><?php _e( 'Upload/Select Image', 'cob_theme' ); ?></button>
            <button type="button" class="button cob-remove-image-button" style="<?php echo $attachment_id ? '' : 'display:none;'; ?>"><?php _e( 'Remove Image', 'cob_theme' ); ?></button>
            <p class="description"><?php _e( 'Select the main cover image for this compound.', 'cob_theme' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'compound_edit_form_fields', 'cob_edit_compound_cover_image_field', 10, 2 );


/**
 * JavaScript for Media Uploader for Compound Cover Image.
 * This should be enqueued properly, but for simplicity in this example,
 * it's added via admin_footer. For production, enqueue it.
 */
function cob_compound_cover_image_uploader_script() {
    // Check if we are on the relevant taxonomy screen
    $screen = get_current_screen();
    if ( ! $screen || $screen->taxonomy !== 'compound' ) { // Replace 'compound' with your actual taxonomy slug if different
        return;
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            var mediaUploader;

            $('body').on('click', '.cob-upload-image-button', function(e) {
                e.preventDefault();
                var $button = $(this);
                var $formField = $button.closest('.form-field, .term-group-wrap'); // Works for both add and edit forms
                var $attachmentIdField = $formField.find('.cob-image-attachment-id');
                var $imageWrapper = $formField.find('#cob-compound-cover-image-wrapper');
                var $removeButton = $formField.find('.cob-remove-image-button');

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: '<?php _e( "Choose Compound Cover Image", "cob_theme" ); ?>',
                    button: {
                        text: '<?php _e( "Use this image", "cob_theme" ); ?>'
                    },
                    multiple: false
                });

                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $attachmentIdField.val(attachment.id);
                    var imageUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                    $imageWrapper.html('<img src="' + imageUrl + '" style="max-width:200px; height:auto;" />');
                    $removeButton.show();
                });

                mediaUploader.open();
            });

            $('body').on('click', '.cob-remove-image-button', function(e) {
                e.preventDefault();
                var $button = $(this);
                var $formField = $button.closest('.form-field, .term-group-wrap');
                var $attachmentIdField = $formField.find('.cob-image-attachment-id');
                var $imageWrapper = $formField.find('#cob-compound-cover-image-wrapper');

                $attachmentIdField.val('');
                $imageWrapper.html('');
                $button.hide();
            });
        });
    </script>
    <?php
}
add_action( 'admin_footer-edit-tags.php', 'cob_compound_cover_image_uploader_script' ); // For edit screen
add_action( 'admin_footer-term.php', 'cob_compound_cover_image_uploader_script' );     // For add screen (WP 4.5+)


/**
 * Save custom field value (Attachment ID) when creating or editing compound taxonomy term.
 *
 * @param int $term_id The term ID.
 */
function cob_save_compound_cover_image_field( $term_id ) {
    if ( isset( $_POST[COB_COMPOUND_COVER_IMAGE_META_KEY] ) ) {
        $attachment_id = sanitize_text_field( $_POST[COB_COMPOUND_COVER_IMAGE_META_KEY] );
        if ( ! empty( $attachment_id ) && is_numeric( $attachment_id ) ) {
            update_term_meta( $term_id, COB_COMPOUND_COVER_IMAGE_META_KEY, $attachment_id );
        } else {
            delete_term_meta( $term_id, COB_COMPOUND_COVER_IMAGE_META_KEY );
        }
    }
}
add_action( 'created_compound', 'cob_save_compound_cover_image_field', 10, 2 );
add_action( 'edited_compound', 'cob_save_compound_cover_image_field', 10, 2 );


/**
 * Add Compound Cover Image column to the taxonomy list table.
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function cob_add_compound_cover_image_column( $columns ) {
    // Insert before 'slug' or 'posts' column for better placement
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key == 'slug' || $key == 'posts') { // Adjust if your target column is different
            $new_columns[COB_COMPOUND_COVER_IMAGE_META_KEY] = __( 'Cover Image', 'cob_theme' );
        }
        $new_columns[$key] = $value;
    }
    if (!isset($new_columns[COB_COMPOUND_COVER_IMAGE_META_KEY])) { // Fallback if target not found
        $new_columns[COB_COMPOUND_COVER_IMAGE_META_KEY] = __( 'Cover Image', 'cob_theme' );
    }
    return $new_columns;
}
add_filter( 'manage_edit-compound_columns', 'cob_add_compound_cover_image_column' );

/**
 * Display the Compound Cover Image column in the taxonomy list table.
 *
 * @param string $content The column content (empty for custom columns).
 * @param string $column_name The column name.
 * @param int    $term_id The term ID.
 * @return string Modified column content.
 */
function cob_display_compound_cover_image_column( $content, $column_name, $term_id ) {
    if ( COB_COMPOUND_COVER_IMAGE_META_KEY === $column_name ) {
        $attachment_id = get_term_meta( $term_id, COB_COMPOUND_COVER_IMAGE_META_KEY, true );
        if ( $attachment_id ) {
            // Display a small thumbnail
            $image_html = wp_get_attachment_image( $attachment_id, array(50, 50), true );
            if ($image_html) {
                return $image_html;
            }
        }
        return '&#8212;'; // Em dash for no image
    }
    return $content; // Important for other columns
}
add_filter( 'manage_compound_custom_column', 'cob_display_compound_cover_image_column', 10, 3 );

?>
