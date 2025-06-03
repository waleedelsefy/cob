<?php
/**
 * Template Name: Latest Projects
 */

$theme_dir = get_template_directory_uri();
?>

<div class="projects">
    <div class="container">
        <div class="top-projects">
            <div class="right-projects">
                <!-- Display header and description -->
                <h3 class="head"><?php esc_html_e( 'Latest Projects', 'cob_theme' ); ?></h3>
                <h5><?php esc_html_e( 'Explore a selection of our latest available real estate projects', 'cob_theme' ); ?></h5>
            </div>
            <!-- Link to the project archive page -->
            <a href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>" class="projects-button">
				<?php esc_html_e( 'View all', 'cob_theme' ); ?>
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281171 7.68509C-0.0843415 7.31958 -0.0923715 6.73316 0.253053 6.3556L6.06104 0.535563C6.24982 0.346785 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535563C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="white"/>
                </svg>
            </a>
        </div>

		<?php
		// Retrieve up to 9 compound terms.
		$compounds = get_terms( [
			'taxonomy'   => 'compound',
			'hide_empty' => false,
			'number'     => 9,
		] );
		?>

        <div class="swiper swiper2">
            <div class="swiper-wrapper">
				<?php if ( ! empty( $compounds ) && ! is_wp_error( $compounds ) ) : ?>
					<?php foreach ( $compounds as $compound ) : ?>
                        <div class="swiper-slide">
                            <!-- Link to the compound term archive page -->
                            <a href="<?php echo esc_url( get_term_link( $compound ) ); ?>" class="projects-card">
                                <div class="top-card-proj">
									<?php
									// Retrieve the compound image URL from term meta.
									// The meta field 'compound_image' stores the image URL directly.
									$thumbnail = get_term_meta( $compound->term_id, 'compound_image', true );
									$dev_logo = get_term_meta( $compound->term_id, 'dev_logo', true );
									$image_url = $thumbnail ? $thumbnail : $theme_dir . '/assets/imgs/default.jpg';
									$dev_image_ = $dev_logo ? $dev_logo : $theme_dir . '/assets/imgs/developer-default.png';
									?>
                                    <img data-src="<?php echo esc_url( $dev_image_ ); ?>" alt="<?php echo esc_attr( $compound->name ); ?>" class="lazyload">
                                </div>
                                <img class="main-img lazyload" data-src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $compound->name ); ?>" >
                                <div class="bottom-card-proj">
                                    <button>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.5 9C14.5 10.3807 13.3807 11.5 12 11.5C10.6193 11.5 9.5 10.3807 9.5 9C9.5 7.61929 10.6193 6.5 12 6.5C13.3807 6.5 14.5 7.61929 14.5 9Z" stroke="#E92028" stroke-width="1.5"/>
                                            <path d="M13.2574 17.4936C12.9201 17.8184 12.4693 18 12.0002 18C11.531 18 11.0802 17.8184 10.7429 17.4936C7.6543 14.5008 3.51519 11.1575 5.53371 6.30373C6.6251 3.67932 9.24494 2 12.0002 2C14.7554 2 17.3752 3.67933 18.4666 6.30373C20.4826 11.1514 16.3536 14.5111 13.2574 17.4936Z" stroke="#E92028" stroke-width="1.5"/>
                                            <path d="M18 20C18 21.1046 15.3137 22 12 22C8.68629 22 6 21.1046 6 20" stroke="#E92028" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
										<?php echo esc_html( $compound->name ); ?>
                                    </button>
                                </div>
                            </a>
                        </div>
					<?php endforeach; ?>
				<?php else : ?>
                    <div class="swiper-slide">
                        <p><?php esc_html_e( 'No projects available.', 'cob_theme' ); ?></p>
                    </div>
				<?php endif; ?>
            </div>
            <!-- Navigation Buttons -->
            <div class="swiper-button-prev">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.66602 6.00033H18.3327M1.66602 6.00033C1.66602 4.54158 5.82081 1.81601 6.87435 0.791992M1.66602 6.00033C1.66602 7.45908 5.82081 10.1847 6.87435 11.2087" stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="swiper-button-next">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.334 5.99967L1.66732 5.99967M18.334 5.99967C18.334 7.45842 14.1792 10.184 13.1257 11.208M18.334 5.99967C18.334 4.54092 14.1792 1.8153 13.1257 0.791341" stroke="#fff" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="swiper-pagination"></div>
        </div><!-- .swiper -->
    </div><!-- .container -->
</div>
