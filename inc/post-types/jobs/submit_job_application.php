<?php
function submit_job_application() {
    if ( ! isset( $_POST['job_application_nonce'] ) ||
        ! wp_verify_nonce( $_POST['job_application_nonce'], 'submit_job_application' ) ) {
        wp_send_json_error( __( 'Verification failed, please try again.', 'cob_theme' ) );
    }
    $job_id             = isset( $_POST['job_id'] ) ? intval( $_POST['job_id'] ) : 0;
    $full_name          = isset( $_POST['full_name'] ) ? sanitize_text_field( $_POST['full_name'] ) : '';
    $phone              = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
    $experience_years   = isset( $_POST['experience_years'] ) ? sanitize_text_field( $_POST['experience_years'] ) : '';
    $email              = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $address            = isset( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : '';
    $additional_details = isset( $_POST['additional_details'] ) ? sanitize_textarea_field( $_POST['additional_details'] ) : '';
    $resume_file = '';
    if ( ! empty( $_FILES['resume']['name'] ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        $uploaded = wp_handle_upload( $_FILES['resume'], array( 'test_form' => false ) );
        if ( isset( $uploaded['url'] ) ) {
            $resume_file = esc_url_raw( $uploaded['url'] );
        }
    }

    global $wpdb;
    $table = $wpdb->prefix . 'job_applications';
    $result = $wpdb->insert(
        $table,
        array(
            'job_id'             => $job_id,
            'full_name'          => $full_name,
            'phone'              => $phone,
            'experience_years'   => $experience_years,
            'email'              => $email,
            'address'            => $address,
            'resume'             => $resume_file,
            'additional_details' => $additional_details,
            'submission_date'    => current_time( 'mysql' )
        ),
        array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
    );

    if ( $result ) {
        wp_send_json_success( array( 'message' => __( '!Thank you for applying for the job', 'cob_theme' ) ) );
    } else {
        wp_send_json_error( __( 'An error occurred while saving your request, please try again.', 'cob_theme' ) );
    }
}
add_action( 'wp_ajax_submit_job_application', 'submit_job_application' );
add_action( 'wp_ajax_nopriv_submit_job_application', 'submit_job_application' );
