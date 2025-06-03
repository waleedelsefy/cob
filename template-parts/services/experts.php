<div class="experts">
    <div class="container">
        <div class="top-experts">
            <h3><?php esc_html_e( 'Our Experts', 'cob_theme' ); ?></h3>
        </div>
        <div class="swiper swiper7">
            <div class="swiper-wrapper">
                <?php
                $experts = get_users( array(
                    'role'    => 'real_estate_expert',
                    'orderby' => 'display_name',
                    'order'   => 'ASC'
                ) );

                $placeholder = get_template_directory_uri() . '/assets/images/placeholder/our-experts.png';

                if ( ! empty( $experts ) ) {
                    foreach ( $experts as $expert ) {
                        $profile_image = get_user_meta( $expert->ID, 'profile_image', true );
                        $job_title     = get_user_meta( $expert->ID, 'job_title', true );
                        ?>
                        <div class="swiper-slide">
                            <div class="experts-img">
                                <img
                                        src="<?php echo esc_url( $placeholder ); ?>"
                                        data-src="<?php echo esc_url( $profile_image ); ?>"
                                        alt="<?php echo esc_attr( $expert->display_name ); ?>"
                                        class="lazyload"
                                        width="150" height="150" loading="lazy"
                                >
                            </div>
                            <div class="experts-info">
                                <h6><?php echo esc_html( $expert->display_name ); ?></h6>
                                <span><?php echo esc_html( $job_title ); ?></span>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <div class="swiper-button-prev">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M1.66602 6.00033H18.3327M1.66602 6.00033C1.66602 4.54158 5.82081 1.81601 6.87435 0.791992M1.66602 6.00033C1.66602 7.45908 5.82081 10.1847 6.87435 11.2087"
                            stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="swiper-button-next">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                    <path
                            d="M18.334 5.99967L1.66732 5.99967M18.334 5.99967C18.334 7.45842 14.1792 10.184 13.1257 11.208M18.334 5.99967C18.334 4.54092 14.1792 1.8153 13.1257 0.791341"
                            stroke="#fff" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
