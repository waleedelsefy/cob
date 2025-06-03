<?php
/**
 * Capital of Business Theme Functions
 *
 * @package Capital_of_Business
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add custom field to the Compound taxonomy term add screen.
 */
function add_compound_image_field() {
    $compound_image = '';
    ?>
    <div class="form-field">
        <label for="compound-image"><?php _e( 'Compound Image', 'cob_theme' ); ?></label>
        <img src="<?php echo esc_url( $compound_image ); ?>" height="150px" id="selected-compound-image" style="display: <?php echo ! empty( $compound_image ) ? 'block' : 'none'; ?>;">
        <input type="text" name="compound_image" id="compound-image" class="regular-text hidden" value="">
        <br>
        <button class="button" id="upload-compound-image"><?php _e( 'Upload Image', 'cob_theme' ); ?></button>
        <button class="button" id="remove-compound-image"><?php _e( 'Remove Image', 'cob_theme' ); ?></button>
        <p class="description"><?php _e( 'Enter the URL of the compound image or use the "Upload Image" button.', 'cob_theme' ); ?></p>
    </div>
    <script>
        jQuery(document).ready(function ($) {
            var file_frame;
            $('#upload-compound-image').click(function (e) {
                e.preventDefault();
                // If the media frame already exists, reopen it.
                if ( file_frame ) {
                    file_frame.open();
                    return;
                }
                // Create the media frame.
                file_frame = wp.media({
                    title: '<?php _e( 'Select or Upload Image', 'cob_theme' ); ?>',
                    button: {
                        text: '<?php _e( 'Use this image', 'cob_theme' ); ?>',
                    },
                    multiple: false
                });
                // When an image is selected, run a callback.
                file_frame.on('select', function () {
                    var attachment = file_frame.state().get('selection').first().toJSON();
                    $('#compound-image').val( attachment.url );
                    $('#selected-compound-image').attr('src', attachment.url).show();
                });
                // Finally, open the modal.
                file_frame.open();
            });
            // Remove image button
            $('#remove-compound-image').click(function (e) {
                e.preventDefault();
                $('#compound-image').val('');
                $('#selected-compound-image').attr('src', '').hide();
            });
        });
    </script>
    <?php
}
add_action( 'compound_add_form_fields', 'add_compound_image_field' );

/**
 * Save custom field value when creating or editing compound taxonomy term.
 *
 * @param int $term_id The term ID.
 */
function save_compound_image_field( $term_id ) {
    if ( isset( $_POST['compound_image'] ) ) {
        // Save the URL of the compound image.
        update_term_meta( $term_id, 'compound_image', esc_url( $_POST['compound_image'] ) );
    }
}
add_action( 'created_compound', 'save_compound_image_field' );
add_action( 'edited_compound', 'save_compound_image_field' );

/**
 * Add custom field to the Compound taxonomy term edit screen.
 *
 * @param object $term The term object.
 */
function edit_compound_image_field( $term ) {
    $compound_image = get_term_meta( $term->term_id, 'compound_image', true );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="compound-image"><?php _e( 'Compound Image', 'cob_theme' ); ?></label>
        </th>
        <td>
            <div>
                <img src="<?php echo esc_url( $compound_image ); ?>" height="150px" style="margin-bottom: 10px; max-width: 100%; <?php echo empty( $compound_image ) ? 'display:none;' : ''; ?>" id="preview-compound-image">
            </div>
            <div>
                <input type="text" name="compound_image" id="compound-image" class="regular-text hidden" value="<?php echo esc_url( $compound_image ); ?>">
                <button class="button" id="update-compound-image"><?php _e( 'Upload Image', 'cob_theme' ); ?></button>
                <button class="button" id="remove-compound-image"><?php _e( 'Remove Image', 'cob_theme' ); ?></button>
            </div>
            <p class="description"><?php _e( 'Enter the URL of the compound image or use the "Upload Image" button.', 'cob_theme' ); ?></p>
        </td>
    </tr>
    <script>
        jQuery(document).ready(function ($) {
            $('#preview-compound-image').attr('src', $('#compound-image').val());
            var file_frame;
            $('#update-compound-image').click(function (e) {
                e.preventDefault();
                // Create the media frame if it doesn't exist.
                if ( typeof file_frame === 'undefined' ) {
                    file_frame = wp.media({
                        title: '<?php _e( 'Select or Upload Image', 'cob_theme' ); ?>',
                        button: {
                            text: '<?php _e( 'Use this image', 'cob_theme' ); ?>'
                        },
                        multiple: false
                    });
                    // When an image is selected, run a callback.
                    file_frame.on('select', function () {
                        var attachment = file_frame.state().get('selection').first().toJSON();
                        $('#compound-image').val( attachment.url );
                        $('#preview-compound-image').attr('src', attachment.url).show();
                    });
                }
                // Finally, open the modal.
                file_frame.open();
            });
            // Remove image button functionality.
            $('#remove-compound-image').click(function (e) {
                e.preventDefault();
                $('#compound-image').val('');
                $('#preview-compound-image').attr('src', '').hide();
            });
        });
    </script>
    <?php
}
add_action( 'compound_edit_form_fields', 'edit_compound_image_field' );

/**
 * Add Compound Image column to the taxonomy list table.
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function add_compound_image_column( $columns ) {
    $columns['compound_image'] = __( 'Image', 'cob_theme' );
    return $columns;
}
add_filter( 'manage_edit-compound_columns', 'add_compound_image_column' );

/**
 * Display the Compound Image column in the taxonomy list table.
 *
 * @param string $content The column content.
 * @param string $column_name The column name.
 * @param int    $term_id The term ID.
 * @return string Modified column content.
 */
function display_compound_image_column( $content, $column_name, $term_id ) {
    if ( 'compound_image' === $column_name ) {
        $compound_image = get_term_meta( $term_id, 'compound_image', true );
        if ( $compound_image ) {
            $content .= '<img src="' . esc_url( $compound_image ) . '" style="max-width: 50px; height: auto;" />';
        }
    }
    return $content;
}
add_filter( 'manage_compound_custom_column', 'display_compound_image_column', 10, 3 );
