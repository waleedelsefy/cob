<?php
/**
 * Capital of Business Theme Functions
 *
 * @package Capital_of_Business
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Init Minify HTML
 */
function add_developer_image_field() {
    $developer_image = '';     ?>
    <div class="form-field">
        <label for="developer-image"><?php _e('Developer Image', 'cob_theme'); ?></label>
        <img src="<?php echo esc_url($developer_image); ?>" height="150px" id="selected-developer-image">
        <input type="text" name="developer_image" id="developer-image" class="regular-text hidden" value="">
        <button class="button" id="upload-developer-image"><?php _e('Upload Image', 'cob_theme'); ?></button>
        <p class="description"><?php _e('Enter the URL of the developer image or use the "Upload Image" button.', 'cob_theme'); ?></p>
    </div>
    <script>
        jQuery(document).ready(function ($) {
            var file_frame;
            $('#upload-developer-image').click(function (e) {
                e.preventDefault();
                if (file_frame) {
                    file_frame.open();
                    return;
                }
                // Create the media frame.
                file_frame = wp.media({
                    title: '<?php _e('Select or Upload Image', 'cob_theme'); ?>',
                    button: {
                        text: '<?php _e('Use this image', 'cob_theme'); ?>',
                    },
                    multiple: false,
                });
                file_frame.on('select', function () {
                    var attachment = file_frame.state().get('selection').first().toJSON();
                    $('#developer-image').val(attachment.url);
                    $('#selected-developer-image').attr('src', attachment.url); // Set the image source
                });
                file_frame.open();
            });
        });
    </script>
    <?php
}
// Add custom field to the developer taxonomy term add screen
add_action('developer_add_form_fields', 'add_developer_image_field');
// Save custom field value when creating or editing developer taxonomy term
function save_developer_image_field($term_id) {
    if (isset($_POST['developer_image'])) {
        update_term_meta($term_id, 'developer_image', esc_url($_POST['developer_image']));
    }
}
add_action('created_developer', 'save_developer_image_field');
add_action('edited_developer', 'save_developer_image_field');
function edit_developer_image_field($term) {
    $developer_image = get_term_meta($term->term_id, 'developer_image', true);    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="developer-image"><?php _e('developer Image', 'cob_theme'); ?></label></th>
        <td>
            <div>
                <img src="<?php echo esc_url($developer_image); ?>" height="150px" style="margin-bottom: 10px; max-width: 100%;" id="preview-developer-image">
            </div>
            <div>
                <input type="text" name="developer_image" id="developer-image" class="regular-text hidden" value="<?php echo esc_url($developer_image); ?>">
                <button class="button" id="update-developer-image"><?php _e('Upload Image', 'cob_theme'); ?></button>
            </div>
            <p class="description"><?php _e('Enter the URL of the developer image or use the "Upload Image" button.', 'cob_theme'); ?></p>
        </td>
    </tr>
    <script>
        jQuery(document).ready(function ($) {
            $('#preview-developer-image').attr('src', $('#developer-image').val());
            $('#update-developer-image').click(function (e) {
                e.preventDefault();
                // Create the media frame if it doesn't exist
                if (typeof file_frame === 'undefined') {
                    file_frame = wp.media({
                        title: '<?php _e('Select or Upload Image', 'cob_theme'); ?>',
                        button: {
                            text: '<?php _e('Use this image', 'cob_theme'); ?>'
                        },
                        multiple: false
                    });
                    // When an image is selected, run a callback.
                    file_frame.on('select', function () {
                        var attachment = file_frame.state().get('selection').first().toJSON();
                        $('#developer-image').val(attachment.url);
                        $('#preview-developer-image').attr('src', attachment.url).show(); // Set the image source and display it
                    });
                }
                // Finally, open the modal.
                file_frame.open();
            });
        });
    </script>
    <?php
}
add_action('developer_edit_form_fields', 'edit_developer_image_field');
function add_developer_image_column($columns) {
    $columns['developer_image'] = __('Image', 'cob_theme');
    return $columns;
}
add_filter('manage_edit-developer_columns', 'add_developer_image_column');
function display_developer_image_column($content, $column_name, $term_id) {
    if ($column_name === 'developer_image') {
        $developer_image = get_term_meta($term_id, 'developer_image', true);
        if ($developer_image) {
            $content .= '<img src="' . esc_url($developer_image) . '" style="max-width: 50px; height: auto;" />';
        }
    }
    return $content;
}