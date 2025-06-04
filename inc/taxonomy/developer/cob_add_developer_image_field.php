<?php
/**
 * Custom Fields for 'developer' Taxonomy - Logo (Attachment ID Version)
 *
 * Manages a custom logo field for the 'developer' taxonomy, storing the attachment ID.
 * This version is designed to be compatible with the AJAX developer importer script.
 *
 * @package Capital_of_Business
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define the meta key consistently. This should match your developer importer's config.
define( 'COB_DEVELOPER_LOGO_META_KEY', '_developer_logo_id' );

/**
 * Add custom field to the Developer taxonomy term add screen.
 * Stores Attachment ID.
 */
function cob_add_developer_logo_field_form() {
    ?>
    <div class="form-field term-group">
        <label for="cob-developer-logo-id"><?php _e( 'Developer Logo', 'cob_theme' ); ?></label>
        <input type="hidden" name="<?php echo esc_attr( COB_DEVELOPER_LOGO_META_KEY ); ?>" id="cob-developer-logo-id" class="cob-logo-attachment-id" value="">
        <div id="cob-developer-logo-wrapper" style="margin-bottom: 10px; width: 150px; height: 150px; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; text-align: center;">
            <span class="description"><?php _e('No logo selected', 'cob_theme'); ?></span>
            <?php // Image preview will be populated by JavaScript ?>
        </div>
        <button type="button" class="button cob-upload-logo-button"><?php _e( 'Upload/Select Logo', 'cob_theme' ); ?></button>
        <button type="button" class="button cob-remove-logo-button" style="display:none;"><?php _e( 'Remove Logo', 'cob_theme' ); ?></button>
        <p class="description"><?php _e( 'Select the logo for this developer.', 'cob_theme' ); ?></p>
    </div>
    <?php
}
add_action( 'developer_add_form_fields', 'cob_add_developer_logo_field_form', 10, 2 );

/**
 * Add custom field to the Developer taxonomy term edit screen.
 * Stores Attachment ID.
 *
 * @param WP_Term $term The term object.
 */
function cob_edit_developer_logo_field_form( $term ) {
    $attachment_id = get_term_meta( $term->term_id, COB_DEVELOPER_LOGO_META_KEY, true );
    $image_url = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'medium' ) : ''; // 'medium' or 'thumbnail'
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="cob-developer-logo-id"><?php _e( 'Developer Logo', 'cob_theme' ); ?></label>
        </th>
        <td>
            <input type="hidden" name="<?php echo esc_attr( COB_DEVELOPER_LOGO_META_KEY ); ?>" id="cob-developer-logo-id" class="cob-logo-attachment-id" value="<?php echo esc_attr( $attachment_id ); ?>">
            <div id="cob-developer-logo-wrapper" style="margin-bottom: 10px; width: 150px; height: 150px; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; text-align: center;">
                <?php if ( $image_url ) : ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" style="max-width: 100%; max-height: 100%; height: auto; width:auto;">
                <?php else: ?>
                    <span class="description"><?php _e('No logo selected', 'cob_theme'); ?></span>
                <?php endif; ?>
            </div>
            <button type="button" class="button cob-upload-logo-button"><?php _e( 'Upload/Select Logo', 'cob_theme' ); ?></button>
            <button type="button" class="button cob-remove-logo-button" style="<?php echo $attachment_id ? '' : 'display:none;'; ?>"><?php _e( 'Remove Logo', 'cob_theme' ); ?></button>
            <p class="description"><?php _e( 'Select the logo for this developer.', 'cob_theme' ); ?></p>
        </td>
    </tr>
    <?php
}
add_action( 'developer_edit_form_fields', 'cob_edit_developer_logo_field_form', 10, 2 );


/**
 * JavaScript for Media Uploader for Developer Logo.
 */
function cob_developer_logo_uploader_script() {
    $screen = get_current_screen();
    // Only run on 'developer' taxonomy add/edit screens
    if ( ! $screen || $screen->taxonomy !== 'developer' ) {
        return;
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            var mediaUploader;

            $('body').on('click', '.cob-upload-logo-button', function(e) {
                e.preventDefault();
                var $button = $(this);
                // Find the closest form-field container, works for both add and edit term screens
                var $formField = $button.closest('.form-field, .term-group-wrap');
                var $attachmentIdField = $formField.find('.cob-logo-attachment-id');
                var $imageWrapper = $formField.find('#cob-developer-logo-wrapper');
                var $removeButton = $formField.find('.cob-remove-logo-button');

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: '<?php _e( "Choose Developer Logo", "cob_theme" ); ?>',
                    button: {
                        text: '<?php _e( "Use this logo", "cob_theme" ); ?>'
                    },
                    multiple: false,
                    library: { // Only show images
                        type: 'image'
                    }
                });

                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $attachmentIdField.val(attachment.id);
                    // Use 'medium' or 'thumbnail' for preview, or 'url' if sizes are not available
                    var imageUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                    $imageWrapper.html('<img src="' + imageUrl + '" style="max-width:100%; max-height:100%; height:auto; width:auto;" />');
                    $removeButton.show();
                });

                mediaUploader.open();
            });

            $('body').on('click', '.cob-remove-logo-button', function(e) {
                e.preventDefault();
                var $button = $(this);
                var $formField = $button.closest('.form-field, .term-group-wrap');
                var $attachmentIdField = $formField.find('.cob-logo-attachment-id');
                var $imageWrapper = $formField.find('#cob-developer-logo-wrapper');

                $attachmentIdField.val('');
                $imageWrapper.html('<span class="description"><?php _e('No logo selected', 'cob_theme'); ?></span>');
                $button.hide();
            });
        });
    </script>
    <?php
}
// Hook the script into the footer of taxonomy add/edit pages.
add_action( 'admin_footer-edit-tags.php', 'cob_developer_logo_uploader_script' );
add_action( 'admin_footer-term.php', 'cob_developer_logo_uploader_script' );


/**
 * Save custom field value (Attachment ID) when creating or editing developer taxonomy term.
 *
 * @param int $term_id The term ID.
 */
function cob_save_developer_logo_field( $term_id ) {
    if ( isset( $_POST[COB_DEVELOPER_LOGO_META_KEY] ) ) {
        $attachment_id = sanitize_text_field( $_POST[COB_DEVELOPER_LOGO_META_KEY] );
        if ( ! empty( $attachment_id ) && is_numeric( $attachment_id ) ) {
            update_term_meta( $term_id, COB_DEVELOPER_LOGO_META_KEY, $attachment_id );
        } else {
            // If the field is submitted empty, delete the meta.
            delete_term_meta( $term_id, COB_DEVELOPER_LOGO_META_KEY );
        }
    }
}
// Use the correct hook for the 'developer' taxonomy
add_action( 'created_developer', 'cob_save_developer_logo_field', 10, 2 );
add_action( 'edited_developer', 'cob_save_developer_logo_field', 10, 2 );


/**
 * Add Developer Logo column to the 'developer' taxonomy list table.
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function cob_add_developer_logo_column( $columns ) {
    $new_columns = array();
    $inserted = false;
    foreach ($columns as $key => $value) {
        if (($key == 'slug' || $key == 'posts') && !$inserted) {
            $new_columns[COB_DEVELOPER_LOGO_META_KEY] = __( 'Logo', 'cob_theme' );
            $inserted = true;
        }
        $new_columns[$key] = $value;
    }
    if (!$inserted) { // Fallback if target columns not found
        $new_columns[COB_DEVELOPER_LOGO_META_KEY] = __( 'Logo', 'cob_theme' );
    }
    return $new_columns;
}
add_filter( 'manage_edit-developer_columns', 'cob_add_developer_logo_column' );

/**
 * Display the Developer Logo column in the 'developer' taxonomy list table.
 *
 * @param string $content The column content.
 * @param string $column_name The column name.
 * @param int    $term_id The term ID.
 * @return string Modified column content.
 */
function cob_display_developer_logo_column( $content, $column_name, $term_id ) {
    if ( COB_DEVELOPER_LOGO_META_KEY === $column_name ) {
        $attachment_id = get_term_meta( $term_id, COB_DEVELOPER_LOGO_META_KEY, true );
        if ( $attachment_id ) {
            // Display a small thumbnail (e.g., 50x50)
            $image_html = wp_get_attachment_image( $attachment_id, array(50, 50), true, array('style' => 'max-height:50px; width:auto;') );
            if ($image_html) {
                return $image_html;
            }
        }
        return '&#8212;'; // Em dash for no image
    }
    return $content;
}
// Use the correct hook for the 'developer' taxonomy
add_filter( 'manage_developer_custom_column', 'cob_display_developer_logo_column', 10, 3 );

?>
