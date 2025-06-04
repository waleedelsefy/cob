<?php

function job_applicants_menu_page() {
    add_menu_page(
        __( 'Job Applicants', 'cob_theme' ),
        __( 'Job Applicants', 'cob_theme' ),
        'manage_options',
        'job-applicants',
        'job_applicants_page_callback',
        'dashicons-id-alt2',
        26
    );
}
add_action( 'admin_menu', 'job_applicants_menu_page' );

function job_applicants_page_callback() {
    global $wpdb;
    $table = $wpdb->prefix . 'job_applications';
    if ( isset( $_POST['update_status'] ) && check_admin_referer( 'update_job_applicant_status', 'job_applicant_nonce' ) ) {
        $applicant_id = intval( $_POST['applicant_id'] );
        $status = sanitize_text_field( $_POST['status'] );
        $wpdb->update(
            $table,
            array( 'status' => $status ),
            array( 'id' => $applicant_id ),
            array( '%s' ),
            array( '%d' )
        );
        echo '<div class="updated"><p>' . esc_html__( 'Status updated successfully.', 'cob_theme' ) . '</p></div>';
    }
    $results = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY submission_date DESC" );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Job Applicants', 'cob_theme' ); ?></h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
                <th><?php esc_html_e( 'ID', 'cob_theme' ); ?></th>
                <th><?php esc_html_e( 'Job ID', 'cob_theme' ); ?></th>
                <th><?php esc_html_e( 'Full Name', 'cob_theme' ); ?></th>
                <th><?php esc_html_e( 'Phone', 'cob_theme' ); ?></th>
                <th><?php esc_html_e( 'Email', 'cob_theme' ); ?></th>
                <th><?php esc_html_e( 'Resume', 'cob_theme' ); ?></th>
                <th><?php esc_html_e( 'Additional Details', 'cob_theme' ); ?></th>
                <th><?php esc_html_e( 'Submission Date', 'cob_theme' ); ?></th>
                <th><?php esc_html_e( 'Status', 'cob_theme' ); ?></th>
                <th><?php esc_html_e( 'Action', 'cob_theme' ); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if ( ! empty( $results ) ) : ?>
                <?php foreach ( $results as $applicant ) : ?>
                    <tr>
                        <td><?php echo esc_html( $applicant->id ); ?></td>
                        <td><?php echo esc_html( $applicant->job_id ); ?></td>
                        <td><?php echo esc_html( $applicant->full_name ); ?></td>
                        <td><?php echo esc_html( $applicant->phone ); ?></td>
                        <td><?php echo esc_html( $applicant->email ); ?></td>
                        <td>
                            <?php if ( ! empty( $applicant->resume ) ) : ?>
                                <a href="<?php echo esc_url( $applicant->resume ); ?>" target="_blank"><?php esc_html_e( 'Download', 'cob_theme' ); ?></a>
                            <?php else : ?>
                                <?php esc_html_e( 'N/A', 'cob_theme' ); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html( $applicant->additional_details ); ?></td>
                        <td><?php echo esc_html( $applicant->submission_date ); ?></td>
                        <td><?php echo esc_html( $applicant->status ); ?></td>
                        <td>
                            <form method="post" style="margin:0;">
                                <?php wp_nonce_field( 'update_job_applicant_status', 'job_applicant_nonce' ); ?>
                                <input type="hidden" name="applicant_id" value="<?php echo intval( $applicant->id ); ?>">
                                <select name="status">
                                    <option value="pending" <?php selected( $applicant->status, 'pending' ); ?>><?php esc_html_e( 'Pending', 'cob_theme' ); ?></option>
                                    <option value="approved" <?php selected( $applicant->status, 'approved' ); ?>><?php esc_html_e( 'Approved', 'cob_theme' ); ?></option>
                                    <option value="rejected" <?php selected( $applicant->status, 'rejected' ); ?>><?php esc_html_e( 'Rejected', 'cob_theme' ); ?></option>
                                </select>
                                <input type="submit" name="update_status" class="button" value="<?php esc_attr_e( 'Update', 'cob_theme' ); ?>">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="10"><?php esc_html_e( 'No applicants found.', 'cob_theme' ); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
