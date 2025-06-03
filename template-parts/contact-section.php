<?php
$theme_dir               = get_template_directory_uri();
$contact_img_placeholder = $theme_dir . '/assets/imgs/contact.jpg';
$logo_img_placeholder    = $theme_dir . '/assets/imgs/logo.png';
?>
<div class="contact-section">
    <div class="container">
        <div class="contact-content">
            <div class="form-container">
                <h2 class="head"><?php echo esc_html__( 'Need Help?', 'cob_theme' ); ?></h2>
                <p><?php echo esc_html__( 'Fill in your information and a real estate expert will contact you shortly.', 'cob_theme' ); ?></p>
                <form>
                    <input type="text" id="name" name="name" placeholder="<?php esc_attr_e( 'Your Name', 'cob_theme' ); ?>" required>
                    <input type="tel" id="mobile" name="mobile" pattern="[0-9]*" placeholder="<?php esc_attr_e( 'Phone Number', 'cob_theme' ); ?>" required>
                    <input type="email" id="email" name="email" placeholder="<?php esc_attr_e( 'Email Address', 'cob_theme' ); ?>" required>
                    <textarea id="message" name="message" rows="4" placeholder="<?php esc_attr_e( 'Your Message', 'cob_theme' ); ?>" required></textarea>
                    <button type="submit"><?php echo esc_html__( 'Send', 'cob_theme' ); ?></button>
                </form>
            </div>
            <div class="image-container">
                <img
                        src="<?php echo esc_url( $contact_img_placeholder ); ?>"
                        data-src="<?php echo $theme_dir ?>/assets/imgs/contact.jpg"
                        alt="<?php esc_attr_e( 'Office Image', 'cob_theme' ); ?>"
                        class="offical-img lazyload"
                        width="600" height="400" loading="lazy"
                >
                <img
                        src="<?php echo esc_url( $logo_img_placeholder ); ?>"
                        data-src="<?php echo $theme_dir ?>/assets/imgs/logo.png"
                        alt="<?php esc_attr_e( 'Company Logo', 'cob_theme' ); ?>"
                        class="logo-contact lazyload"
                        width="150" height="80" loading="lazy"
                >
            </div>
        </div>
    </div>
</div>
