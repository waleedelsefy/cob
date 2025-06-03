<div class="hiring-landing">
    <div class="container">
        <div class="right-hiring">
            <h2><?php esc_html_e( 'Join the fastest-growing real estate companies', 'cob_theme' ); ?></h2>
            <div class="underline"></div>
            <p>
                <?php esc_html_e( 'We are looking for passionate individuals to join us in our mission. We value flat hierarchies, clear communication, ownership, and complete accountability.', 'cob_theme' ); ?>
            </p>
        </div>
        <div class="left-hiring">

            <div class="swiper hiring-swiper">
                <div class="swiper-wrapper">
                    <?php
                    $post_id      = get_the_ID();
                    $gallery_ids  = get_post_meta( $post_id, '_gallery_image_ids', true );
                    $gallery_images = [];
                    if ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) {
                        foreach ( $gallery_ids as $attachment_id ) {
                            $image_url = wp_get_attachment_image_url( $attachment_id, 'large' );
                            if ( $image_url ) {
                                $gallery_images[] = $image_url;
                            }
                        }
                    }
                    ?>

                    <?php
                    if ( ! empty( $gallery_images ) ) : ?>
                        <?php foreach ( $gallery_images as $image_url ) : ?>
                            <div class="swiper-slide">
                                <img data-src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="lazyload">
                                <div class="underline"></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <!-- Custom navigation buttons -->
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
</div>
