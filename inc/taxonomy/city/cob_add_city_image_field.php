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
 * Add custom field to the City taxonomy term add screen.
 */
function add_city_image_field() {
    $city_image = '';
    ?>
    <div class="form-field">
        <label for="city-image"><?php _e( 'City Image', 'cob_theme' ); ?></label>
        <img src="<?php echo esc_url( $city_image ); ?>" height="150px" id="selected-city-image" style="display: <?php echo ! empty( $city_image ) ? 'block' : 'none'; ?>;">
        <input type="text" name="city_image" id="city-image" class="regular-text hidden" value="">
        <br>
        <button class="button" id="upload-city-image"><?php _e( 'Upload Image', 'cob_theme' ); ?></button>
        <button class="button" id="remove-city-image"><?php _e( 'Remove Image', 'cob_theme' ); ?></button>
        <p class="description"><?php _e( 'Enter the URL of the city image or use the "Upload Image" button.', 'cob_theme' ); ?></p>
    </div>
    <script>
        jQuery(document).ready(function ($) {
            var file_frame;
            $('#upload-city-image').click(function (e) {
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
                    $('#city-image').val( attachment.url );
                    $('#selected-city-image').attr('src', attachment.url).show();
                });
                // Finally, open the modal.
                file_frame.open();
            });
            // Remove image button
            $('#remove-city-image').click(function (e) {
                e.preventDefault();
                $('#city-image').val('');
                $('#selected-city-image').attr('src', '').hide();
            });
        });
    </script>
    <?php
}
add_action( 'city_add_form_fields', 'add_city_image_field' );

/**
 * Save custom field value when creating or editing city taxonomy term.
 *
 * @param int $term_id The term ID.
 */
function save_city_image_field( $term_id ) {
    if ( isset( $_POST['city_image'] ) ) {
        // Save the URL of the city image.
        update_term_meta( $term_id, 'city_image', esc_url( $_POST['city_image'] ) );
    }
}
add_action( 'created_city', 'save_city_image_field' );
add_action( 'edited_city', 'save_city_image_field' );

/**
 * Add custom field to the City taxonomy term edit screen.
 *
 * @param object $term The term object.
 */
function edit_city_image_field( $term ) {
    $city_image = get_term_meta( $term->term_id, 'city_image', true );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="city-image"><?php _e( 'City Image', 'cob_theme' ); ?></label>
        </th>
        <td>
            <div>
                <img src="<?php echo esc_url( $city_image ); ?>" height="150px" style="margin-bottom: 10px; max-width: 100%; <?php echo empty( $city_image ) ? 'display:none;' : ''; ?>" id="preview-city-image">
            </div>
            <div>
                <input type="text" name="city_image" id="city-image" class="regular-text hidden" value="<?php echo esc_url( $city_image ); ?>">
                <button class="button" id="update-city-image"><?php _e( 'Upload Image', 'cob_theme' ); ?></button>
                <button class="button" id="remove-city-image"><?php _e( 'Remove Image', 'cob_theme' ); ?></button>
            </div>
            <p class="description"><?php _e( 'Enter the URL of the city image or use the "Upload Image" button.', 'cob_theme' ); ?></p>
        </td>
    </tr>
    <script>
        jQuery(document).ready(function ($) {
            $('#preview-city-image').attr('src', $('#city-image').val());
            var file_frame;
            $('#update-city-image').click(function (e) {
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
                        $('#city-image').val( attachment.url );
                        $('#preview-city-image').attr('src', attachment.url).show();
                    });
                }
                // Finally, open the modal.
                file_frame.open();
            });
            // Remove image button functionality.
            $('#remove-city-image').click(function (e) {
                e.preventDefault();
                $('#city-image').val('');
                $('#preview-city-image').attr('src', '').hide();
            });
        });
    </script>
    <?php
}
add_action( 'city_edit_form_fields', 'edit_city_image_field' );

/**
 * Add City Image column to the taxonomy list table.
 *
 * @param array $columns The existing columns.
 * @return array Modified columns.
 */
function add_city_image_column( $columns ) {
    $columns['city_image'] = __( 'Image', 'cob_theme' );
    return $columns;
}
add_filter( 'manage_edit-city_columns', 'add_city_image_column' );

/**
 * Display the City Image column in the taxonomy list table.
 *
 * @param string $content The column content.
 * @param string $column_name The column name.
 * @param int    $term_id The term ID.
 * @return string Modified column content.
 */
function display_city_image_column( $content, $column_name, $term_id ) {
    if ( 'city_image' === $column_name ) {
        $city_image = get_term_meta( $term_id, 'city_image', true );
        if ( $city_image ) {
            $content .= '<img src="' . esc_url( $city_image ) . '" style="max-width: 50px; height: auto;" />';
        }
    }
    return $content;
}
add_filter( 'manage_city_custom_column', 'display_city_image_column', 10, 3 );
