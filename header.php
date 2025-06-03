<?php
/**
 * Header Template
 *
 * @package Capital_of_Business
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_post_id = get_the_ID();

// Check if Polylang functions exist before calling them.
if ( function_exists( 'pll_current_language' ) ) {
	$current_lang = pll_current_language();
} else {
	// Default language if Polylang is not available.
	$current_lang = 'default';
}

if ( function_exists( 'pll_get_post' ) ) {
	if ( $current_lang == 'en' ) {
		$translated_id = pll_get_post( $current_post_id, 'ar' );
		$text = esc_html__( 'Arabic', 'cob_theme' );
	} else {
		$translated_id = pll_get_post( $current_post_id, 'en' );
		$text = esc_html__( 'English', 'cob_theme' );
	}
} else {
	// If Polylang is not available, no translation will be done.
	$translated_id = null;
	$text = '';
}

$url = $translated_id ? get_permalink( $translated_id ) : get_permalink( $current_post_id );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/logo.png' ); ?>" sizes="25x25" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header>
    <div class="container">
        <div class="navbar" style="z-index: 1; flex-direction: row;">

            <!-- Hamburger Menu (Accessible Button) -->
            <div class="hamburger" aria-label="<?php esc_attr_e( 'Toggle navigation', 'cob_theme' ); ?>">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>

            <!-- Dynamic Logo -->
            <div class="logo">
                <a href="<?php echo esc_url( home_url() ); ?>">
					<?php if ( has_custom_logo() ) : ?>
						<?php the_custom_logo(); ?>
					<?php else : ?>
                        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/logo.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>" />
					<?php endif; ?>
                </a>
            </div>

            <!-- WordPress Navigation -->
            <nav class="nav-bar" aria-label="<?php esc_attr_e( 'Primary Menu', 'cob_theme' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary_menu',
					'container'      => false,
					'menu_class'     => 'nav-links nav-menu',
					'fallback_cb'    => false,
					'depth'          => 3, // Adjust as needed.
					'walker'         => new Cob_Walker_Nav_Menu(),
				) );
				?>
            </nav>

            <div class="language-selector">
                <a href="<?php echo esc_url( $url ); ?>">
					<?php echo $text; ?>
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.99903 18C7.76036 18 6.5937 17.7633 5.49903 17.29C4.40436 16.816 3.45103 16.1727 2.63903 15.36C1.82703 14.5473 1.1837 13.594 0.709029 12.5C0.234362 11.406 -0.00230429 10.2393 -0.00097096 9C-0.00097096 7.75733 0.235696 6.58967 0.709029 5.497C1.18303 4.40367 1.82636 3.45133 2.63903 2.64C3.4517 1.82867 4.40503 1.18533 5.49903 0.71C6.5937 0.236667 7.76036 0 8.99903 0C10.2417 0 11.4094 0.236667 12.502 0.71C13.5954 1.184 14.548 1.82733 15.36 2.64C16.172 3.45267 16.815 4.405 17.289 5.497C17.7624 6.59033 17.999 7.758 17.999 9C17.999 10.2387 17.7624 11.4053 17.289 12.5C16.815 13.5947 16.1717 14.548 15.359 15.36C14.5464 16.172 13.594 16.8153 12.502 17.29C11.41 17.7647 10.2424 18.0013 8.99903 18ZM8.99903 17.008C9.5857 16.254 10.0697 15.5137 10.451 14.787C10.8317 14.0603 11.1414 13.247 11.38 12.347H6.61803C6.8827 13.2977 7.1987 14.1363 7.56603 14.863C7.93403 15.5897 8.4117 16.3047 8.99903 17.008ZM7.72603 16.858C7.25936 16.308 6.83336 15.628 6.44803 14.818C6.0627 14.0087 5.77603 13.1847 5.58803 12.346H1.75303C2.32636 13.5893 3.1387 14.6093 4.19003 15.406C5.24203 16.202 6.4207 16.686 7.72603 16.858ZM10.272 16.858C11.5774 16.686 12.756 16.202 13.808 15.406C14.8594 14.6093 15.6717 13.5893 16.245 12.346H12.411C12.1577 13.1973 11.8387 14.0277 11.454 14.837C11.0687 15.647 10.6747 16.3213 10.272 16.858ZM1.34503 11.347H5.38003C5.30403 10.9363 5.2507 10.5363 5.22003 10.147C5.18803 9.75833 5.17203 9.376 5.17203 9C5.17203 8.624 5.1877 8.24167 5.21903 7.853C5.25036 7.46433 5.3037 7.06433 5.37903 6.653H1.34603C1.23736 6.99967 1.15236 7.37733 1.09103 7.786C1.0297 8.194 0.999029 8.59867 0.999029 9C0.999029 9.40133 1.02936 9.80633 1.09003 10.215C1.1507 10.6237 1.2357 11.0007 1.34503 11.346M6.38003 11.346H11.618C11.694 10.936 11.7474 10.5427 11.778 10.166C11.81 9.79 11.826 9.40133 11.826 9C11.826 8.59867 11.8104 8.21 11.779 7.834C11.7477 7.458 11.6944 7.06467 11.619 6.654H6.37903C6.3037 7.064 6.25036 7.45733 6.21903 7.834C6.1877 8.21 6.17203 8.59867 6.17203 9C6.17203 9.40133 6.1877 9.79 6.21903 10.166C6.25036 10.542 6.3047 10.9353 6.38003 11.346ZM12.619 11.346H16.653C16.7617 11 16.8467 10.623 16.908 10.215C16.9687 9.80633 16.999 9.40133 16.999 9C16.999 8.59867 16.9687 8.19367 16.908 7.785C16.8474 7.37633 16.7624 6.99933 16.653 6.654H12.618C12.694 7.064 12.7474 7.46367 12.778 7.853C12.81 8.24233 12.826 8.62467 12.826 9C12.826 9.37533 12.8104 9.75767 12.779 10.147C12.7477 10.5363 12.6944 10.9363 12.619 11.347M12.411 5.654H16.245C15.659 4.38467 14.8564 3.36467 13.837 2.594C12.8177 1.82333 11.6294 1.333 10.272 1.123C10.7387 1.737 11.1584 2.43933 11.531 3.23C11.9037 4.02 12.197 4.828 12.411 5.654ZM6.61803 5.654H11.38C11.116 4.71533 10.7904 3.86667 10.403 3.108C10.0157 2.34933 9.5477 1.644 8.99903 0.992C8.45036 1.64333 7.98236 2.34867 7.59503 3.108C7.2077 3.86733 6.88136 4.716 6.61803 5.654ZM1.75403 5.654H5.58803C5.80203 4.82867 6.09536 4.02067 6.46803 3.23C6.8407 2.43933 7.26036 1.737 7.72703 1.123C6.3577 1.33367 5.16636 1.827 4.15303 2.603C3.1397 3.38033 2.3397 4.397 1.75303 5.653" fill="black"></path>
                    </svg>
                </a>
            </div>

            <div class="language-contact">
                <div class="contact">
                    <a href="tel:<?php echo esc_attr( get_option( 'company_phone', '0123456789' ) ); ?>" aria-label="<?php esc_attr_e( 'Call us', 'cob_theme' ); ?>">
                        <span><?php echo esc_html( get_option( 'company_phone', '0123456789' ) ); ?></span>
                        <span>
                            <svg width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.83597 3.26565C6.2168 3.68781 6.09606 4.16606 5.75932 4.61985C5.67949 4.72744 5.58746 4.83345 5.47632 4.95026C5.42297 5.00633 5.38137 5.04849 5.29436 5.13559C5.09676 5.33339 4.93064 5.4996 4.79601 5.63422C4.73073 5.6995 5.17647 6.59021 6.04152 7.45612C6.90611 8.32156 7.79678 8.7676 7.86237 8.70197L8.36038 8.20369C8.63463 7.92917 8.77986 7.79673 8.97944 7.66579C9.39433 7.3936 9.84687 7.31963 10.2285 7.66092C11.4747 8.55232 12.1799 9.09933 12.5254 9.4583C13.1993 10.1585 13.1109 11.2362 12.5292 11.8511C12.3275 12.0643 12.0717 12.3202 11.7697 12.6114C9.94233 14.4397 6.08584 13.3271 3.13026 10.3686C0.174006 7.40943 -0.938051 3.55257 0.885437 1.72811C1.21281 1.39559 1.32074 1.28771 1.63957 0.973557C2.23317 0.388661 3.36066 0.297078 4.04605 0.974136C4.40657 1.33027 4.98134 2.06979 5.83597 3.26565Z" fill="#E92028" />
                            </svg>
                        </span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</header>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const hamburger = document.querySelector(".hamburger");
        const navMenu = document.querySelector(".nav-menu");
        const navbar = document.querySelector(".navbar");
        const languageSelector = document.querySelector(".language-selector");
        const mobileBreakpoint = 768;

        const resetForDesktop = () => {
            if (window.innerWidth >= mobileBreakpoint) {
                hamburger.classList.remove("active");
                navMenu.classList.remove("active");
                hamburger.setAttribute("aria-expanded", "false");
                navbar.style.flexDirection = "row";
                navbar.style.gap = "";
                languageSelector.style.display = "block";
            }
        };

        const toggleMenu = () => {
            if (window.innerWidth < mobileBreakpoint) {
                hamburger.classList.toggle("active");
                navMenu.classList.toggle("active");

                const menuOpen = navMenu.classList.contains("active");
                hamburger.setAttribute("aria-expanded", menuOpen);

                if (menuOpen) {
                    navbar.style.flexDirection = "row-reverse";
                    navbar.style.gap = "50%";
                    languageSelector.style.display = "none";
                } else {
                    navbar.style.flexDirection = "row";
                    navbar.style.gap = "";
                    languageSelector.style.display = "block";
                }
            }
        };

        const closeMenuOnLinkClick = () => {
            if (window.innerWidth < mobileBreakpoint) {
                hamburger.classList.remove("active");
                navMenu.classList.remove("active");
                hamburger.setAttribute("aria-expanded", "false");
                navbar.style.flexDirection = "row";
                navbar.style.gap = "";
                languageSelector.style.display = "block";
            }
        };

        // Event listeners
        hamburger.addEventListener("click", toggleMenu);
        document.querySelectorAll(".nav-links a").forEach((link) => {
            link.addEventListener("click", closeMenuOnLinkClick);
        });
        window.addEventListener("resize", resetForDesktop);

        // Initial check on page load
        resetForDesktop();
    });
</script>
</body>
</html>
