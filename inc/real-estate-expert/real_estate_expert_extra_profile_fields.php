<?php
function real_estate_expert_extra_profile_fields( $user ) {
    if ( in_array( 'real_estate_expert', (array) $user->roles ) ) {
        ?>
        <h3><?php esc_html_e( 'Real Estate Expert Information', 'cob_theme' ); ?></h3>
        <table class="form-table">
            <tr>
                <th>
                    <label for="phone_number"><?php esc_html_e( 'Phone Number', 'cob_theme' ); ?></label>
                </th>
                <td>
                    <input type="text" name="phone_number" id="phone_number"
                           value="<?php echo esc_attr( get_user_meta( $user->ID, 'phone_number', true ) ); ?>"
                           class="regular-text" /><br />
                    <span class="description">
                        <?php esc_html_e( 'Enter your phone number.', 'cob_theme' ); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="job_title"><?php esc_html_e( 'Job Title', 'cob_theme' ); ?></label>
                </th>
                <td>
                    <input type="text" name="job_title" id="job_title"
                           value="<?php echo esc_attr( get_user_meta( $user->ID, 'job_title', true ) ); ?>"
                           class="regular-text" /><br />
                    <span class="description">
                        <?php esc_html_e( 'Enter your Job Title.', 'cob_theme' ); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="profile_image"><?php esc_html_e( 'Profile Image', 'cob_theme' ); ?></label>
                </th>
                <td>
                    <input type="text" name="profile_image" id="profile_image"
                           value="<?php echo esc_url( get_user_meta( $user->ID, 'profile_image', true ) ); ?>"
                           class="regular-text" /><br />
                    <span class="description">
                        <?php esc_html_e( 'Enter the URL of your profile image.', 'cob_theme' ); ?>
                    </span>
                </td>
            </tr>
        </table>
        <?php
    }
}
add_action( 'show_user_profile', 'real_estate_expert_extra_profile_fields' );
add_action( 'edit_user_profile', 'real_estate_expert_extra_profile_fields' );

function save_real_estate_expert_extra_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    if ( isset( $_POST['phone_number'] ) ) {
        update_user_meta( $user_id, 'phone_number', sanitize_text_field( $_POST['phone_number'] ) );
    }
    if ( isset( $_POST['job_title'] ) ) {
        update_user_meta( $user_id, 'job_title', sanitize_text_field( $_POST['job_title'] ) );
    }

    if ( isset( $_POST['profile_image'] ) ) {
        update_user_meta( $user_id, 'profile_image', esc_url_raw( $_POST['profile_image'] ) );
    }
}
add_action( 'personal_options_update', 'save_real_estate_expert_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'save_real_estate_expert_extra_profile_fields' );
