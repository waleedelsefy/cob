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

<div class="flat-slider">
    <!-- Slider الرئيسي (مع الصور الكبيرة) -->
    <div class="swiper flat-swiper2">
        <div class="swiper-wrapper">
            <?php foreach ( $gallery_images as $image ) : ?>
                <div class="swiper-slide">
                    <img data-src="<?php echo esc_url( $image ); ?>" class="lazyload" alt="" />
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>

    <!-- Slider الخاص بالصور المصغرة (Thumbs) -->
    <div thumbsSlider="" class="swiper flat-swiper">
        <div class="swiper-wrapper">
            <?php foreach ( $gallery_images as $image ) : ?>
                <div class="swiper-slide">
                    <img data-src="<?php echo esc_url( $image ); ?>" class="lazyload" alt="" />
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- زر عرض جميع الصور -->
<button class="btn" id="togglePopup">
    <span>عرض الكل</span>
    <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M11.8839 26.1388C16.8742 29.0199 23.2553 27.3101 26.1364 22.3199C29.0175 17.3296 27.3077 10.9485 22.3175 8.06736C17.3272 5.18621 10.9461 6.89601 8.06496 11.8863C5.18382 16.8766 6.89362 23.2576 11.8839 26.1388Z" fill="white" />
        <path d="M13.4873 15.0158L20.7159 19.1892M13.4873 15.0158C13.8526 14.3831 16.3371 14.2413 17.0504 14.061M13.4873 15.0158C13.122 15.6484 14.2415 17.871 14.442 18.5789" stroke="#E92028" stroke-width="0.782516" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
</button>

<div class="overlay" id="overlay"></div>

<!-- النافذة المنبثقة (Popup) لعرض الصور -->
<div class="popup" id="popup">
    <button id="closePopup">
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M31.375 8.625L8.625 31.375M8.625 8.625L31.375 31.375" stroke="white" stroke-width="2.4375" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>

    <!-- Slider الرئيسي داخل النافذة المنبثقة -->
    <div class="swiper pop-swiper2">
        <div class="swiper-wrapper">
            <?php foreach ( $gallery_images as $image ) : ?>
                <div class="swiper-slide">
                    <img data-src="<?php echo esc_url( $image ); ?>" class="lazyload" alt="" />
                </div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-button-next">
            <svg width="35" height="23" viewBox="0 0 35 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M33.002 11.502L2.00195 11.502M33.002 11.502C33.002 14.2152 25.274 19.2848 23.3145 21.1895M33.002 11.502C33.002 8.78868 25.274 3.71902 23.3145 1.81445" stroke="white" stroke-width="2.90625" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <div class="swiper-button-prev">
            <svg width="35" height="23" viewBox="0 0 35 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.99805 11.498H32.998M1.99805 11.498C1.99805 8.78477 9.72596 3.71523 11.6855 1.81055M1.99805 11.498C1.99805 14.2113 9.72596 19.281 11.6855 21.1855" stroke="white" stroke-width="2.90625" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <div thumbsSlider="" class="swiper pop-swiper">
        <div class="swiper-wrapper">
            <?php foreach ( $gallery_images as $image ) : ?>
                <div class="swiper-slide">
                    <img data-src="<?php echo esc_url( $image ); ?>" class="lazyload" alt="" />
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
