<?php
/**
 * Footer Template
 *
 * @package Capital_of_Business
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get Redux Social Settings
$enable_social_menu = get_option('cob_options')['enable_social_menu'] ?? true;
$social_links = get_option('cob_options')['social_links'] ?? [];
$social_icons = [
    'facebook'  => 'fab fa-facebook-f',
    'twitter'   => 'fab fa-twitter',
    'instagram' => 'fab fa-instagram',
    'linkedin'  => 'fab fa-linkedin',
    'youtube'   => 'fab fa-youtube'
];
get_template_part('template-parts/fixed-icons');

?>

<footer class="footer">
    <div class="container top-footer">
        <div class="main-footer">
            <div class="footer-links">
                <div class="footer-section">
                    <h4><?php esc_html_e('Quick links', 'cob_theme'); ?></h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer_menu',
                        'container'      => false,
                        'menu_class'     => '',
                        'fallback_cb'    => false,
                    ]);
                    ?>
                </div>

                <div class="footer-section">
                    <h4><?php esc_html_e('Top Areas', 'cob_theme'); ?></h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer_cities_menu',
                        'container'      => false,
                        'menu_class'     => '',
                        'fallback_cb'    => false,
                    ]);
                    ?>
                </div>

                <div class="footer-section">
                    <h4><?php esc_html_e('Latest Projects', 'cob_theme'); ?></h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer_projects_menu',
                        'container'      => false,
                        'menu_class'     => '',
                        'fallback_cb'    => false,
                    ]);
                    ?>
                </div>

                <div class="footer-section">
                    <h4><?php esc_html_e('Developers', 'cob_theme'); ?></h4>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer_developers_menu',
                        'container'      => false,
                        'menu_class'     => '',
                        'fallback_cb'    => false,
                    ]);
                    ?>
                </div>
            </div>

            <div class="newsletter">
                <h3><?php esc_html_e('Subscribe to the newsletter', 'cob_theme'); ?></h3>
                <p><?php esc_html_e('Subscribe now to receive the latest news and exclusive offers', 'cob_theme'); ?></p>
                <form method="post" class="newsletter-form">
                    <input type="email" name="newsletter_email" placeholder="E-mail" required />
                    <button type="submit"><?php esc_html_e('Subscribe', 'cob_theme'); ?></button>
                </form>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="top-bottom-footer">
                <div class="footer-logo">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/imgs/logo-white.png'); ?>" alt="Logo">
                </div>

                <!-- Social Icons -->
                <?php if ($enable_social_menu && !empty($social_links)) : ?>
                    <div class="social-links">
                        <?php foreach ($social_links as $link) :
                            list($platform, $url) = explode('|', $link);
                            if (!empty($platform) && !empty($url)) :
                                $icon_class = $social_icons[$platform] ?? 'fas fa-link';
                                ?>
                                <a href="<?php echo esc_url($url); ?>" target="_blank">
                                    <i class="<?php echo esc_attr($icon_class); ?>"></i>
                                </a>
                            <?php endif;
                        endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

            <div class="end-footer">
                <div class="end-right">
                    <p> <?php esc_html_e('All rights reserved ©', 'cob_theme'); ?><?php echo date('Y'); ?> <?php esc_html_e('Capital of Business', 'cob_theme'); ?></p>
                </div>
                <div class="end-left">
                    <span><a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" class="end-link"><?php esc_html_e('privacy policy', 'cob_theme'); ?></a></span>
                    <span><a href="<?php echo esc_url(home_url('/terms-of-use')); ?>"><?php esc_html_e('terms of use', 'cob_theme'); ?></a></span>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
<script>
    window.addEventListener("load", function () {
        const preloader = document.getElementById("preloader");
        if (preloader) {
            preloader.style.transition = "opacity 0.5s ease";
            preloader.style.opacity = "0";
            console.log("Page loaded. Hiding preloader...");
            setTimeout(() => {
                preloader.style.display = "none";
            }, 500);
        }
    });
</script>
</body>
</html>