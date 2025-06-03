<?php
function cob_register_company_settings() {
    register_setting( 'cob_options_group', 'company_phone' );
    register_setting( 'cob_options_group', 'company_whatsapp' );
    register_setting( 'cob_options_group', 'email' );
    register_setting( 'cob_options_group', 'zoom_id' );
    register_setting( 'cob_options_group', 'zoom_key' );

    add_settings_section(
        'cob_company_section',
        __( 'Company & Zoom Contact Settings', 'cob_theme' ),
        'cob_company_section_text',
        'cob_company'
    );

    add_settings_field(
        'company_phone',
        __( 'Company Phone', 'cob_theme' ),
        'cob_company_phone_field',
        'cob_company',
        'cob_company_section'
    );
    add_settings_field(
        'company_whatsapp',
        __( 'Company WhatsApp', 'cob_theme' ),
        'cob_company_whatsapp_field',
        'cob_company',
        'cob_company_section'
    );

    add_settings_field(
        'email',
        __( 'Email', 'cob_theme' ),
        'cob_email_field',
        'cob_company',
        'cob_company_section'
    );
    add_settings_field(
        'zoom_id',
        __( 'Zoom ID', 'cob_theme' ),
        'cob_zoom_id_field',
        'cob_company',
        'cob_company_section'
    );
    add_settings_field(
        'zoom_key',
        __( 'Zoom Key', 'cob_theme' ),
        'cob_zoom_key_field',
        'cob_company',
        'cob_company_section'
    );
}
add_action( 'admin_init', 'cob_register_company_settings' );

// نص القسم
function cob_company_section_text() {
    echo '<p>' . __( 'Please enter your company and Zoom contact details below:', 'cob_theme' ) . '</p>';
}

// حقل رقم الهاتف
function cob_company_phone_field() {
    $value = get_option( 'company_phone', '0123456789' );
    echo '<input type="text" name="company_phone" value="' . esc_attr( $value ) . '" />';
}

// حقل الواتساب
function cob_company_whatsapp_field() {
    $value = get_option( 'company_whatsapp', '0123456789' );
    echo '<input type="text" name="company_whatsapp" value="' . esc_attr( $value ) . '" />';
}

function cob_email_field() {
    $value = get_option( 'email', '' );
    echo '<input type="email" name="email" value="' . esc_attr( $value ) . '" />';
}
function cob_zoom_id_field() {
    $value = get_option( 'zoom_id', '' );
    echo '<input type="text" name="zoom_id" value="' . esc_attr( $value ) . '" />';
}

function cob_zoom_key_field() {
    $value = get_option( 'zoom_key', '' );
    echo '<input type="text" name="zoom_key" value="' . esc_attr( $value ) . '" />';
}

function cob_add_company_options_page() {
    add_options_page(
        __( 'Company Options', 'cob' ),
        __( 'Company Options', 'cob' ),
        'manage_options',
        'cob_company',
        'cob_company_options_page'
    );
}
add_action( 'admin_menu', 'cob_add_company_options_page' );

function cob_company_options_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Company Options', 'cob' ); ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields( 'cob_options_group' ); ?>
            <?php do_settings_sections( 'cob_company' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
