<?php
/**
 * Compound Developer & City with Select2 (Quick & Bulk Edit Support)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function cdc_enqueue_select2_admin() {
    wp_enqueue_script(
        'select2',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        array('jquery'),
        '4.1.0',
        true
    );
    wp_enqueue_style(
        'select2-css',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        array(),
        '4.1.0'
    );
    $inline_js = '
        jQuery(document).ready(function($) {
            $(".select2").select2({
                placeholder: "Select an option",
                allowClear: true,
                width: "100%"
            });
        });
    ';
    wp_add_inline_script('select2', $inline_js);
}
add_action('admin_enqueue_scripts', 'cdc_enqueue_select2_admin');

function cdc_compound_add_fields() {
    $developers = get_terms( array(
        'taxonomy'   => 'developer',
        'hide_empty' => false,
    ) );
    $cities = get_terms( array(
        'taxonomy'   => 'city',
        'hide_empty' => false,
    ) );
    ?>
    <div class="form-field term-group">
        <label for="compound_developer"><?php _e( 'Select Developer', 'cob_theme' ); ?></label>
        <select id="compound_developer" name="custom_fields[compound_developer][]" class="select2" multiple>
            <?php foreach ( $developers as $developer ) : ?>
                <option value="<?php echo esc_attr( $developer->term_id ); ?>">
                    <?php echo esc_html( $developer->name ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-field term-group">
        <label for="compound_city"><?php _e( 'Select City', 'cob_theme' ); ?></label>
        <select id="compound_city" name="custom_fields[compound_city][]" class="select2" multiple>
            <?php foreach ( $cities as $city ) : ?>
                <option value="<?php echo esc_attr( $city->term_id ); ?>">
                    <?php echo esc_html( $city->name ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
}
add_action( 'compound_add_form_fields', 'cdc_compound_add_fields' );

function cdc_compound_edit_fields( $term ) {
    $selected_developers = get_term_meta( $term->term_id, 'compound_developer', true );
    $selected_cities     = get_term_meta( $term->term_id, 'compound_city', true );
    if ( ! is_array( $selected_developers ) ) {
        $selected_developers = array();
    }
    if ( ! is_array( $selected_cities ) ) {
        $selected_cities = array();
    }
    $developers = get_terms( array(
        'taxonomy'   => 'developer',
        'hide_empty' => false,
    ) );
    $cities = get_terms( array(
        'taxonomy'   => 'city',
        'hide_empty' => false,
    ) );
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="compound_developer"><?php _e( 'Select Developer', 'cob_theme' ); ?></label>
        </th>
        <td>
            <select id="compound_developer" name="custom_fields[compound_developer][]" class="select2" multiple>
                <?php foreach ( $developers as $developer ) : ?>
                    <option value="<?php echo esc_attr( $developer->term_id ); ?>" <?php echo in_array( $developer->term_id, $selected_developers ) ? 'selected="selected"' : ''; ?>>
                        <?php echo esc_html( $developer->name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="compound_city"><?php _e( 'Select City', 'cob_theme' ); ?></label>
        </th>
        <td>
            <select id="compound_city" name="custom_fields[compound_city][]" class="select2" multiple>
                <?php foreach ( $cities as $city ) : ?>
                    <option value="<?php echo esc_attr( $city->term_id ); ?>" <?php echo in_array( $city->term_id, $selected_cities ) ? 'selected="selected"' : ''; ?>>
                        <?php echo esc_html( $city->name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <?php
}
add_action( 'compound_edit_form_fields', 'cdc_compound_edit_fields' );


function cdc_save_compound_meta( $term_id ) {
    if ( isset( $_POST['custom_fields']['compound_developer'] ) ) {
        $developers = array_map( 'sanitize_text_field', (array) $_POST['custom_fields']['compound_developer'] );
        update_term_meta( $term_id, 'compound_developer', $developers );
    }
    if ( isset( $_POST['custom_fields']['compound_city'] ) ) {
        $cities = array_map( 'sanitize_text_field', (array) $_POST['custom_fields']['compound_city'] );
        update_term_meta( $term_id, 'compound_city', $cities );
    }
}
add_action( 'created_compound', 'cdc_save_compound_meta' );
add_action( 'edited_compound', 'cdc_save_compound_meta' );

function cdc_compound_columns( $columns ) {
    $columns['cdc_custom_fields'] = __( 'Custom Fields', 'cob_theme' );
    return $columns;
}
add_filter( 'manage_edit-compound_columns', 'cdc_compound_columns' );

function cdc_compound_column_content( $content, $column_name, $term_id ) {
    if ( 'cdc_custom_fields' === $column_name ) {
        $developers = get_term_meta( $term_id, 'compound_developer', true );
        $cities     = get_term_meta( $term_id, 'compound_city', true );
        if ( ! is_array( $developers ) ) {
            $developers = array();
        }
        if ( ! is_array( $cities ) ) {
            $cities = array();
        }
        $dev = implode( ',', $developers );
        $cit = implode( ',', $cities );
        $content = '<span class="cdc-custom-fields" data-developer="' . esc_attr( $dev ) . '" data-city="' . esc_attr( $cit ) . '"></span>';
    }
    return $content;
}
add_filter( 'manage_compound_custom_column', 'cdc_compound_column_content', 10, 3 );

function cdc_hide_compound_columns() {
    echo '<style>
        .column-cdc_custom_fields { display: none; }
    </style>';
}
add_action( 'admin_head', 'cdc_hide_compound_columns' );

function cdc_quick_edit_custom_box() {
    $screen = get_current_screen();
    if ( 'edit-compound' !== $screen->id ) {
        return;
    }
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            var $inlineEditForm = $('#inline-edit');
            if ( $inlineEditForm.length ) {
                if ( ! $inlineEditForm.find('.cdc-quick-edit-fields').length ) {
                    var html = '<fieldset class="cdc-quick-edit-fields">';
                    html += '<div class="inline-edit-group">';
                    html += '<label><span class="title"><?php _e("Developer", "cob_theme"); ?></span>';
                    html += '<select name="custom_fields[compound_developer][]" class="select2" multiple>';
                    <?php
                    $developers = get_terms( array( 'taxonomy' => 'developer', 'hide_empty' => false ) );
                    foreach ( $developers as $developer ) {
                        echo 'html += \'<option value="'.esc_attr( $developer->term_id ).'">'.esc_html( $developer->name ).'</option>\';';
                    }
                    ?>
                    html += '</select>';
                    html += '</label>';
                    html += '</div>';
                    html += '<div class="inline-edit-group">';
                    html += '<label><span class="title"><?php _e("City", "cob_theme"); ?></span>';
                    html += '<select name="custom_fields[compound_city][]" class="select2" multiple>';
                    <?php
                    $cities = get_terms( array( 'taxonomy' => 'city', 'hide_empty' => false ) );
                    foreach ( $cities as $city ) {
                        echo 'html += \'<option value="'.esc_attr( $city->term_id ).'">'.esc_html( $city->name ).'</option>\';';
                    }
                    ?>
                    html += '</select>';
                    html += '</label>';
                    html += '</div>';
                    html += '</fieldset>';
                    $inlineEditForm.find('fieldset.inline-edit-col-left').append( html );

                    $('#inline-edit select.select2').select2({
                        placeholder: "Select an option",
                        allowClear: true,
                        width: "100%"
                    });
                }
                $('a.editinline').on('click', function(){
                    var termId = $(this).closest('tr').attr('id').replace('tag-', '');
                    var $row = $('#tag-' + termId);
                    var devData = $row.find('.cdc-custom-fields').data('developer');
                    var citData = $row.find('.cdc-custom-fields').data('city');
                    var devArray = (devData) ? devData.toString().split(',') : [];
                    var citArray = (citData) ? citData.toString().split(',') : [];
                    $('#inline-edit select[name="custom_fields[compound_developer][]"]').val( devArray ).trigger('change');
                    $('#inline-edit select[name="custom_fields[compound_city][]"]').val( citArray ).trigger('change');
                });
            }
        });
    </script>
    <?php
}
add_action( 'admin_footer-edit-tags.php', 'cdc_quick_edit_custom_box' );

/**
 * دعم التحرير المتعدد (Bulk Edit) للتصنيفات
 */
function cdc_bulk_edit_custom_box() {
    $screen = get_current_screen();
    if ( 'edit-compound' !== $screen->id ) {
        return;
    }
    $developers = get_terms( array( 'taxonomy' => 'developer', 'hide_empty' => false ) );
    $cities     = get_terms( array( 'taxonomy' => 'city', 'hide_empty' => false ) );
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            var $bulkEditForm = $('#bulk-edit');
            if ( $bulkEditForm.length ) {
                if ( ! $bulkEditForm.find('.cdc-bulk-edit-fields').length ) {
                    var html = '<div class="cdc-bulk-edit-fields">';
                    html += '<div class="inline-edit-group">';
                    html += '<label><span class="title"><?php _e("Developer", "cob_theme"); ?></span>';
                    html += '<select name="custom_fields[compound_developer][]" class="select2" multiple>';
                    <?php
                    foreach ( $developers as $developer ) {
                        echo 'html += \'<option value="'.esc_attr( $developer->term_id ).'">'.esc_html( $developer->name ).'</option>\';';
                    }
                    ?>
                    html += '</select>';
                    html += '</label>';
                    html += '</div>';
                    html += '<div class="inline-edit-group">';
                    html += '<label><span class="title"><?php _e("City", "cob_theme"); ?></span>';
                    html += '<select name="custom_fields[compound_city][]" class="select2" multiple>';
                    <?php
                    foreach ( $cities as $city ) {
                        echo 'html += \'<option value="'.esc_attr( $city->term_id ).'">'.esc_html( $city->name ).'</option>\';';
                    }
                    ?>
                    html += '</select>';
                    html += '</label>';
                    html += '</div>';
                    html += '</div>';
                    $('#bulk-edit .inline-edit-col-left').append( html );
                    $('#bulk-edit select.select2').select2({
                        placeholder: "Select an option",
                        allowClear: true,
                        width: "100%"
                    });
                }
            }
        });
    </script>
    <?php
}
add_action( 'admin_footer-edit-tags.php', 'cdc_bulk_edit_custom_box' );
