<div class="landing-art-name">
    <div class="container">
        <div class="title">
            <h3> <?php echo esc_html( get_the_title() );?></h3>
            <div class="underline"></div>
        </div>
        <?php
        $post_id = get_the_ID();
        $gallery_ids    = get_post_meta( $post_id, '_gallery_image_ids', true );
        $gallery_images = [];
        if ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) {
            foreach ( $gallery_ids as $attachment_id ) {
                $image_url = wp_get_attachment_image_url( $attachment_id, 'large' );
                if ( $image_url ) {
                    $gallery_images[] = $image_url;
                }
            }
        }

        // في حالة عدم وجود صور في الجاليري نستخدم صور افتراضية
        if ( empty( $gallery_images ) ) {
            $theme_dir = get_template_directory_uri();
            $gallery_images = [
                $theme_dir . '/assets/imgs/flat1.png',
                $theme_dir . '/assets/imgs/flat2.png',
                $theme_dir . '/assets/imgs/flat3.png',
                $theme_dir . '/assets/imgs/articles1.png',
                $theme_dir . '/assets/imgs/articles2.png',
                $theme_dir . '/assets/imgs/articles3.png',
                $theme_dir . '/assets/imgs/articles4.jpg',
            ];
        }
        ?>

        <div class="swiper-container">
            <!-- Thumbnails Swiper -->
            <div class="swiper thumbnails-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ( $gallery_images as $index => $img_url ) : ?>
                        <div class="swiper-slide">
                            <img data-src="<?php echo esc_url( $img_url ); ?>" alt="Thumbnail <?php echo esc_attr( $index + 1 ); ?>" class="lazyload">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Main Swiper -->
            <div class="swiper main-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ( $gallery_images as $index => $img_url ) : ?>
                        <div class="swiper-slide">
                            <img data-src="<?php echo esc_url( $img_url ); ?>" alt="Slide <?php echo esc_attr( $index + 1 ); ?>" class="lazyload">
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>

    </div>
</div>