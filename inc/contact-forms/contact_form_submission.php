<?php
/**
 * Handle AJAX contact form submission.
 */
function cob_handle_ajax_contact_form() {
    // تحقق من nonce
    if ( ! isset( $_POST['contact_form_nonce'] ) || ! wp_verify_nonce( $_POST['contact_form_nonce'], 'submit_contact_form' ) ) {
        wp_send_json_error( __( 'Security check failed.', 'cob_theme' ) );
    }

    // الحصول على البيانات وتنظيفها
    $name    = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
    $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
    $email   = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

    // التحقق من أن جميع الحقول غير فارغة
    if ( empty( $name ) || empty( $phone ) || empty( $email ) || empty( $message ) ) {
        wp_send_json_error( __( 'Please fill in all required fields.', 'cob_theme' ) );
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_entries';

    // إدخال البيانات في الجدول
    $result = $wpdb->insert(
        $table_name,
        array(
            'name'    => $name,
            'phone'   => $phone,
            'email'   => $email,
            'message' => $message,
        )
    );

    if ( false === $result ) {
        wp_send_json_error( __( 'An error occurred. Please try again later.', 'cob_theme' ) );
    } else {
        wp_send_json_success( __( 'Your message has been sent successfully!', 'cob_theme' ) );
    }
}
add_action( 'wp_ajax_submit_contact_form', 'cob_handle_ajax_contact_form' );
add_action( 'wp_ajax_nopriv_submit_contact_form', 'cob_handle_ajax_contact_form' );



/**
 * Contact form shortcode.
 */
function cob_contact_form_shortcode() {
    ob_start();
    ?>
    <style>
        /* نفس التنسيق القديم مع استخدام الكلاسات القديمة */

        .form-contain h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-contain label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-contain input[type="text"],
        .form-contain input[type="tel"],
        .form-contain input[type="email"],
        .form-contain textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-contain button {
            /* background: #2A2C43; */
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            display: block;
            margin: 0 auto;
        }
        .success-message,
        .error-message {
            text-align: center;
            font-size: 20px;
            padding: 20px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .success-message {
            color: #212529;
            background: var(--mainColor);
            border: 1px solid var(--mainColor);
        }
        .error-message {
            color: red;
            background: #ffe0e0;
            border: 1px solid red;
        }
    </style>

        <div class="form-contain">
            <form id="contact-form" method="post">
                <?php wp_nonce_field( 'submit_contact_form', 'contact_form_nonce' ); ?>
                <input type="hidden" name="action" value="submit_contact_form">

                <label for="cob-name"><?php esc_html_e( 'Full Name', 'cob_theme' ); ?></label>
                <input type="text" id="cob-name" name="name" required>

                <label for="cob-phone"><?php esc_html_e( 'Mobile Number', 'cob_theme' ); ?></label>
                <input type="tel" id="cob-phone" name="phone" required>

                <label for="cob-email"><?php esc_html_e( 'Email Address', 'cob_theme' ); ?></label>
                <input type="email" id="cob-email" name="email" required>

                <label for="cob-message"><?php esc_html_e( 'Message', 'cob_theme' ); ?></label>
                <textarea id="cob-message" name="message" rows="5" placeholder="<?php esc_attr_e( 'Example: Enter your message here', 'cob_theme' ); ?>" required></textarea>

                <button type="submit"><?php esc_html_e( 'Send', 'cob_theme' ); ?></button>
            </form>
        </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('contact-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // منع إعادة تحميل الصفحة
                var formData = new FormData(form);
                fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                    method: "POST",
                    credentials: "same-origin",
                    body: formData
                })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        // استخدام الكلاس القديم "form-contain" لاستبدال محتوى النموذج
                        var container = document.querySelector('.form-contain');
                        if ( data.success ) {
                            container.innerHTML = '<div class="success-message">' + data.data + '</div>';
                        } else {
                            container.innerHTML = '<div class="error-message">' + data.data + '</div>';
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        var container = document.querySelector('.form-contain');
                        container.innerHTML = '<div class="error-message"><?php echo esc_js( __( "An unexpected error occurred.", "cob_theme" ) ); ?></div>';
                    });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'contact_form', 'cob_contact_form_shortcode' );
