<?php
/**
 * Template Name: Contact Us Page
 *
 * Template for the Contact Us Page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 * @package Didos
 */
get_header();
$theme_dir = get_template_directory_uri();

// متغير لتخزين رسالة النتيجة
$message_result = '';

// معالجة بيانات النموذج عند الإرسال
if ( isset( $_POST['contact_form_submitted'] ) && wp_verify_nonce( $_POST['contact_form_nonce'], 'submit_contact_form' ) ) {

    // تنظيف البيانات المدخلة
    $name    = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
    $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
    $email   = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

    // التحقق من تعبئة جميع الحقول
    if ( ! empty( $name ) && ! empty( $phone ) && ! empty( $email ) && ! empty( $message ) ) {

        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_entries';

        // إدخال البيانات في الجدول
        $inserted = $wpdb->insert(
            $table_name,
            array(
                'name'    => $name,
                'phone'   => $phone,
                'email'   => $email,
                'message' => $message,
            )
        );

        // تعيين رسالة النتيجة بناءً على نجاح الإدخال
        if ( $inserted ) {
            $message_result = '<div class="success-message">' . esc_html__( 'Your message has been sent successfully!', 'cob_theme' ) . '</div>';
        } else {
            $message_result = '<div class="error-message">' . esc_html__( 'An error occurred. Please try again later.', 'cob_theme' ) . '</div>';
        }
    } else {
        $message_result = '<div class="error-message">' . esc_html__( 'Please fill in all required fields.', 'cob_theme' ) . '</div>';
    }
}
?>

<div class="head-city">
    <div class="container">
        <div class="breadcrumb">
            <?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) : rank_math_the_breadcrumbs(); endif; ?>
        </div>
        <h2><?php echo esc_html( get_the_title() ); ?></h2>
    </div>
</div>
<section class="contact-page">
    <div class="container">

        <!-- Map Container -->
        <div class="map-container">
            <h6><?php esc_html_e( 'Company Name', 'cob_theme' ); ?></h6>
            <p>
                <span>
                    <svg width="17" height="21" viewBox="0 0 17 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.1276 9.71809L7.18065 11.7711L11.2868 7.66503M15.3929 8.89686C15.3929 12.9788 11.8 16.2879 8.20718 19.9834C4.61433 16.2879 1.02148 12.9788 1.02148 8.89686C1.02148 4.81492 4.23863 1.50586 8.20718 1.50586C12.1757 1.50586 15.3929 4.81492 15.3929 8.89686Z" stroke="black" stroke-width="1.69691" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <?php esc_html_e( 'It is simply dummy text (i.e., the focus is on design rather than content) and is used in layout design.', 'cob_theme' ); ?>
            </p>
            <span><?php esc_html_e( 'Egypt, Cairo', 'cob_theme' ); ?></span>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3624.912550413685!2d46.67529611500742!3d24.71355128411824!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2f02dd1a827e2b%3A0x676cb4b9793d1fd4!2sRiyadh%2C%20Saudi%20Arabia!5e0!3m2!1sen!2sus!4v1636533416407!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe>
            <div class="company-details">
                <div class="right-comp">
                    <ul>
                        <li>
                            <div class="svg-hold">
                                <svg width="21" height="15" viewBox="0 0 21 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="1.64611" y="0.948844" width="18.1192" height="13.0086" rx="3.01986" stroke="#263238" stroke-width="1.39378" />
                                    <path d="M2.34375 1.64551L9.69303 8.25079C10.2186 8.72311 11.0144 8.72746 11.545 8.2609L19.0691 1.64551" stroke="#263238" stroke-width="1.39378" />
                                </svg>
                            </div>
                            <div class="comp-text">
                                <p><?php esc_html_e( 'Contact Email', 'cob_theme' ); ?></p>
                                <p>support@organic.com</p>
                            </div>
                        </li>
                        <li>
                            <div class="svg-hold">
                                <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.22 16.06C18.4927 15.3273 16.7314 14.258 15.8768 13.8271C14.764 13.2666 14.6724 13.2208 13.7976 13.8706C13.2142 14.3043 12.8263 14.6917 12.1435 14.546C11.4606 14.4004 9.97683 13.5793 8.67758 12.2843C7.37834 10.9893 6.50957 9.46251 6.36348 8.78202C6.21739 8.10153 6.61124 7.71824 7.04081 7.13345C7.64625 6.30917 7.60045 6.17179 7.08295 5.05901C6.67948 4.19351 5.57899 2.44878 4.84349 1.72524C4.05671 0.948126 4.05671 1.08551 3.54974 1.29616C3.13701 1.4698 2.74105 1.68087 2.36681 1.92673C1.63407 2.41352 1.2274 2.81787 0.942999 3.42555C0.658602 4.03323 0.53083 5.45787 1.99953 8.1258C3.46822 10.7937 4.49865 12.1579 6.63139 14.2846C8.76414 16.4112 10.4041 17.5547 12.8016 18.8992C15.7674 20.5601 16.9049 20.2364 17.5145 19.9524C18.124 19.6685 18.5303 19.2655 19.018 18.5328C19.2645 18.1593 19.4761 17.7638 19.65 17.3514C19.8611 16.8463 19.9985 16.8463 19.22 16.06Z" stroke="#263238" stroke-width="1.39397" stroke-miterlimit="10" />
                                </svg>
                            </div>
                            <div class="comp-text">
                                <p><?php esc_html_e( 'Contact Number', 'cob_theme' ); ?></p>
                                <p><?php esc_html_e( '1222222123', 'cob_theme' ); ?></p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="left-comp">
                    <ul>
                        <li>
                            <div class="svg-hold">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.4693 1.98194C13.4164 1.98466 14.3238 2.34815 14.9935 2.99301C15.6631 3.63787 16.0406 4.51171 16.0434 5.42367V12.3064C16.0406 13.2184 15.6631 14.0922 14.9935 14.7371C14.3238 15.3819 13.4164 15.7454 12.4693 15.7481H5.32188C4.37484 15.7454 3.46739 15.3819 2.79773 14.7371C2.12807 14.0922 1.7506 13.2184 1.74777 12.3064V5.42367C1.7506 4.51171 2.12807 3.63787 2.79773 2.99301C3.46739 2.34815 4.37484 1.98466 5.32188 1.98194H12.4693ZM12.4693 0.605469H5.32188C2.56989 0.605469 0.318359 2.77361 0.318359 5.42367V12.3064C0.318359 14.9565 2.56989 17.1246 5.32188 17.1246H12.4693C15.2213 17.1246 17.4728 14.9565 17.4728 12.3064V5.42367C17.4728 2.77361 15.2213 0.605469 12.4693 0.605469Z" fill="#2A2C43" />
                                    <path d="M13.5419 5.42427C13.3299 5.42427 13.1226 5.36372 12.9463 5.25027C12.77 5.13682 12.6325 4.97558 12.5514 4.78692C12.4702 4.59827 12.449 4.39068 12.4904 4.1904C12.5317 3.99013 12.6339 3.80616 12.7838 3.66177C12.9337 3.51738 13.1248 3.41905 13.3328 3.37921C13.5407 3.33938 13.7563 3.35982 13.9522 3.43797C14.1481 3.51611 14.3156 3.64844 14.4334 3.81823C14.5512 3.98801 14.6141 4.18762 14.6141 4.39182C14.6144 4.52749 14.5869 4.66187 14.5331 4.78727C14.4793 4.91266 14.4003 5.02659 14.3007 5.12252C14.2011 5.21845 14.0828 5.29449 13.9526 5.34627C13.8224 5.39805 13.6828 5.42456 13.5419 5.42427ZM8.89604 6.11232C9.46154 6.11232 10.0143 6.2738 10.4845 6.57634C10.9547 6.87887 11.3212 7.30888 11.5376 7.81199C11.754 8.31509 11.8106 8.86869 11.7003 9.40278C11.59 9.93687 11.3177 10.4275 10.9178 10.8125C10.5179 11.1976 10.0085 11.4598 9.45385 11.566C8.89922 11.6723 8.32433 11.6178 7.80187 11.4094C7.27942 11.201 6.83287 10.8481 6.5187 10.3953C6.20453 9.94251 6.03684 9.41019 6.03684 8.86563C6.03765 8.13565 6.33914 7.43579 6.87517 6.91961C7.4112 6.40343 8.13798 6.1131 8.89604 6.11232ZM8.89604 4.73585C8.04784 4.73585 7.21867 4.97805 6.51341 5.43184C5.80815 5.88563 5.25847 6.53061 4.93388 7.28523C4.60928 8.03985 4.52435 8.87021 4.68983 9.67131C4.85531 10.4724 5.26376 11.2083 5.86353 11.7858C6.46331 12.3634 7.22747 12.7567 8.05938 12.9161C8.89129 13.0754 9.75359 12.9936 10.5372 12.6811C11.3209 12.3685 11.9907 11.8392 12.4619 11.16C12.9331 10.4809 13.1847 9.68243 13.1847 8.86563C13.1847 7.77035 12.7328 6.71992 11.9286 5.94543C11.1243 5.17095 10.0335 4.73585 8.89604 4.73585Z" fill="#2A2C43" />
                                </svg>
                            </div>
                        </li>
                        <li>
                            <div class="svg-hold">
                                <svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.5528 0.485352L1.48269 0.485352C0.768517 0.485352 0.144531 0.999222 0.144531 1.70501L0.144531 14.8042C0.144531 15.5138 0.768517 16.1462 1.48269 16.1462L14.549 16.1462C15.267 16.1462 15.8054 15.5096 15.8054 14.8042L15.8054 1.70501C15.8095 0.999222 15.267 0.485352 14.5528 0.485352ZM4.99904 13.5394L2.75548 13.5394L2.75548 6.56371L4.99904 6.56371L4.99904 13.5394ZM3.95487 5.50311H3.93878C3.22076 5.50311 2.75583 4.96861 2.75583 4.29953C2.75583 3.61822 3.233 3.0963 3.9671 3.0963C4.7012 3.0963 5.1504 3.61437 5.16648 4.29953C5.16613 4.96861 4.7012 5.50311 3.95487 5.50311ZM13.1986 13.5394L10.955 13.5394L10.955 9.72524C10.955 8.81146 10.6285 8.18712 9.81684 8.18712C9.1967 8.18712 8.82965 8.60661 8.6664 9.01526C8.60522 9.16208 8.58879 9.36203 8.58879 9.56618L8.58879 13.5394L6.34524 13.5394L6.34524 6.56371L8.58879 6.56371L8.58879 7.53447C8.91529 7.06954 9.42532 6.40046 10.6121 6.40046C12.0849 6.40046 13.1989 7.37122 13.1989 9.46411L13.1986 13.5394Z" fill="#2A2C43" />
                                </svg>
                            </div>
                        </li>
                        <li>
                            <div class="svg-hold">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.3654 8.86504C17.3654 4.30384 13.5248 0.605469 8.78818 0.605469C4.05155 0.605469 0.210938 4.30384 0.210938 8.86504C0.210938 12.9874 3.34699 16.4045 7.44799 17.0247V11.2533H5.2696V8.86504H7.44799V7.04535C7.44799 4.97567 8.72883 3.83149 10.6878 3.83149C11.6263 3.83149 12.6081 3.993 12.6081 3.993V6.02581H11.526C10.4611 6.02581 10.128 6.66224 10.128 7.31637V8.86504H12.5066L12.1268 11.2533H10.1284V17.0254C14.2294 16.4056 17.3654 12.9886 17.3654 8.86504Z" fill="#2A2C43" />
                                </svg>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

<?php     echo   do_shortcode( '[contact_form]'  ) ?>

    </div>
</section>

<script src="<?php echo $theme_dir ?>/assets/js/contact.js"></script>
<?php get_footer(); ?>
