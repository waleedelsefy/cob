<?php

function add_job_qualifications_meta_box() {
    add_meta_box(
        'job_qualifications_meta_box',
        __( 'Job Qualifications', 'cob_theme' ),
        'job_qualifications_meta_box_callback',
        'jobs',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'add_job_qualifications_meta_box' );


/**
 *
 * @param WP_Post $post
 */
function job_qualifications_meta_box_callback( $post ) {
    wp_nonce_field( 'save_job_qualifications', 'job_qualifications_nonce' );

    $qualifications = get_post_meta( $post->ID, 'job_qualifications', true );
    if ( ! is_array( $qualifications ) ) {
        $qualifications = array();
    }
    ?>
    <div id="job_qualifications_container">
        <?php if ( ! empty( $qualifications ) ) : ?>
            <?php foreach ( $qualifications as $qualification ) : ?>
                <div class="job-qualification">
                    <input type="text" name="job_qualifications[]" value="<?php echo esc_attr( $qualification ); ?>" />
                    <button type="button" class="remove_qualification button"><?php esc_html_e( 'Remove', 'cob_theme' ); ?></button>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="job-qualification">
                <input type="text" name="job_qualifications[]" value="" />
                <button type="button" class="remove_qualification button"><?php esc_html_e( 'Remove', 'cob_theme' ); ?></button>
            </div>
        <?php endif; ?>
    </div>
    <button type="button" id="add_qualification" class="button"><?php esc_html_e( 'Add Qualification', 'cob_theme' ); ?></button>

    <script>
        jQuery(document).ready(function($) {
            $('#add_qualification').on('click', function(e) {
                e.preventDefault();
                $('#job_qualifications_container').append(
                    '<div class="job-qualification"><input type="text" name="job_qualifications[]" value="" /> <button type="button" class="remove_qualification button"><?php echo esc_html__( "Remove", "cob_theme" ); ?></button></div>'
                );
            });

            $(document).on('click', '.remove_qualification', function(e) {
                e.preventDefault();
                $(this).closest('.job-qualification').remove();
            });
        });
    </script>
    <?php
}


/**
 *
 * @param int $post_id
 *
 */
function save_job_qualifications_meta_box( $post_id ) {
    if ( ! isset( $_POST['job_qualifications_nonce'] ) ||
        ! wp_verify_nonce( $_POST['job_qualifications_nonce'], 'save_job_qualifications' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['job_qualifications'] ) ) {
        $qualifications = array_map( 'sanitize_text_field', $_POST['job_qualifications'] );
        $qualifications = array_filter( $qualifications, function( $value ) {
            return ! empty( $value );
        } );
        update_post_meta( $post_id, 'job_qualifications', $qualifications );
    } else {
        delete_post_meta( $post_id, 'job_qualifications' );
    }
}
add_action( 'save_post', 'save_job_qualifications_meta_box' );
