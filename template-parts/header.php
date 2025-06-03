<?php

// Get Redux Framework Options
$header_background = get_option('header_background', '#ffffff');
$header_text_color = get_option('header_text_color', '#000000');
$header_sticky     = get_option('header_sticky', true);
$header_layout     = get_option('header_layout', 'default');
$header_logo       = get_option('header_logo')['url'] ?? get_template_directory_uri() . '/assets/imgs/logo.png';
$header_logo_dark  = get_option('header_logo_dark')['url'] ?? get_template_directory_uri() . '/assets/imgs/logo-dark.png';
$header_search     = get_option('header_search', true);
$header_social     = get_option('header_social_icons', true);
$facebook_url      = get_option('header_facebook', 'https://facebook.com');
$twitter_url       = get_option('header_twitter', 'https://twitter.com');
$instagram_url     = get_option('header_instagram', 'https://instagram.com');
?>

<header class="site-header <?php echo $header_sticky ? 'sticky-header' : ''; ?>" style="background-color: <?php echo esc_attr($header_background); ?>; color: <?php echo esc_attr($header_text_color); ?>;">
    <div class="container">
        <div class="navbar">
            <div class="logo">
                <a href="<?php echo esc_url(home_url()); ?>">
                    <img src="<?php echo esc_url($header_logo); ?>" alt="<?php bloginfo('name'); ?>" class="light-logo" />
                    <img src="<?php echo esc_url($header_logo_dark); ?>" alt="<?php bloginfo('name'); ?>" class="dark-logo" />
                </a>
            </div>

            <nav class="nav-bar">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary_menu',
                    'container'      => false,
                    'menu_class'     => 'nav-links nav-menu',
                    'fallback_cb'    => false,
                ]);
                ?>
            </nav>

            <?php if ($header_search) : ?>
                <div class="search-bar">
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <input type="search" name="s" placeholder="<?php esc_attr_e('Search...', 'cob_theme'); ?>" />
                        <button type="submit"><?php esc_html_e('Search', 'cob_theme'); ?></button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($header_social) : ?>
                <div class="social-icons">
                    <a href="<?php echo esc_url($facebook_url); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo esc_url($twitter_url); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="<?php echo esc_url($instagram_url); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
