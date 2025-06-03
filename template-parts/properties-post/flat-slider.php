<?php
$post_id = get_the_ID();
$property_title = get_the_title($post_id); 

$image_ids_raw = get_post_meta($post_id, '_cob_gallery_images', true);
$image_ids = is_array($image_ids_raw) ? $image_ids_raw : array_filter(explode(',', (string) $image_ids_raw));

$gallery_items = [];

if (!empty($image_ids)) {
    foreach ($image_ids as $id) {
        $id = absint($id); 
        if (!$id) {
            continue;
        }

      
        $main_image_data = wp_get_attachment_image_src($id, 'large');
        $main_url = $main_image_data ? $main_image_data[0] : wp_get_attachment_url($id); // fallback للرابط الكامل

        $thumb_image_data = wp_get_attachment_image_src($id, 'thumbnail');
        $thumb_url = $thumb_image_data ? $thumb_image_data[0] : wp_get_attachment_url($id); // fallback للرابط الكامل

        $alt_text = get_post_meta($id, '_wp_attachment_image_alt', true);
        if (empty($alt_text)) {
            $alt_text = get_the_title($id); 
        }
        if (empty($alt_text)) {
            $alt_text = $property_title; 
        }

        // التأكد من وجود الروابط قبل إضافتها
        if ($main_url && $thumb_url) {
            $gallery_items[] = [
                'main_url'  => $main_url,
                'thumb_url' => $thumb_url,
                'alt'       => $alt_text,
            ];
        }
    }
}

// الصور الثابتة الاحتياطية في حال عدم وجود صور ديناميكية
if (empty($gallery_items)) {
    $theme_dir = get_template_directory_uri();
    // تعريف معلومات الصور الثابتة مع نصوص بديلة مقترحة
    $static_images_info = [
        ['file' => 'flat1.png', 'alt' => sprintf(esc_html__('Image of %s - 1', 'cob_theme'), $property_title)],
        ['file' => 'flat2.png', 'alt' => sprintf(esc_html__('Image of %s - 2', 'cob_theme'), $property_title)],
        ['file' => 'flat3.png', 'alt' => sprintf(esc_html__('Image of %s - 3', 'cob_theme'), $property_title)],
        ['file' => 'articles1.png', 'alt' => sprintf(esc_html__('Image of %s - 4', 'cob_theme'), $property_title)],
        ['file' => 'articles2.png', 'alt' => sprintf(esc_html__('Image of %s - 5', 'cob_theme'), $property_title)],
        ['file' => 'articles3.png', 'alt' => sprintf(esc_html__('Image of %s - 6', 'cob_theme'), $property_title)],
        ['file' => 'articles4.jpg', 'alt' => sprintf(esc_html__('Image of %s - 7', 'cob_theme'), $property_title)],
    ];

    foreach ($static_images_info as $static_image) {
        $url = $theme_dir . '/assets/imgs/' . $static_image['file'];
        $gallery_items[] = [
            'main_url'  => $url,
            'thumb_url' => $url, 
            'alt'       => $static_image['alt'],
        ];
    }
}
?>

<?php if (!empty($gallery_items)) : // فقط اعرض السلايدر إذا كان هناك صور ?>
    <div class="flat-slider">
        <div class="swiper flat-swiper2">
            <div class="swiper-wrapper">
                <?php foreach ($gallery_items as $image_data): ?>
                    <div class="swiper-slide">
                        <img data-src="<?php echo esc_url($image_data['main_url']); ?>" class="lazyload" alt="<?php echo esc_attr($image_data['alt']); ?>" />
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

        <div thumbsSlider="" class="swiper flat-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($gallery_items as $image_data): ?>
                    <div class="swiper-slide">
                        <img data-src="<?php echo esc_url($image_data['thumb_url']); ?>" class="lazyload" alt="<?php echo esc_attr(sprintf(esc_html__('Thumbnail for %s', 'cob_theme'), $image_data['alt'])); ?>" />
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <button class="btn" id="togglePopup">
        <span><?php esc_html_e('View All', 'cob_theme'); ?></span>
        <svg width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11.8839 26.1388C16.8742 29.0199 23.2553 27.3101 26.1364 22.3199C29.0175 17.3296 27.3077 10.9485 22.3175 8.06736C17.3272 5.18621 10.9461 6.89601 8.06496 11.8863C5.18382 16.8766 6.89362 23.2576 11.8839 26.1388Z" fill="white" />
            <path d="M13.4873 15.0158L20.7159 19.1892M13.4873 15.0158C13.8526 14.3831 16.3371 14.2413 17.0504 14.061M13.4873 15.0158C13.122 15.6484 14.2415 17.871 14.442 18.5789" stroke="#E92028" stroke-width="0.782516" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>

    <div class="overlay" id="overlay"></div>

    <div class="popup" id="popup">
        <button id="closePopup">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M31.375 8.625L8.625 31.375M8.625 8.625L31.375 31.375" stroke="white" stroke-width="2.4375" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>

        <div class="swiper pop-swiper2">
            <div class="swiper-wrapper">
                <?php foreach ($gallery_items as $image_data): ?>
                    <div class="swiper-slide">
                        <img data-src="<?php echo esc_url($image_data['main_url']); ?>" class="lazyload" alt="<?php echo esc_attr($image_data['alt']); ?>" />
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
                <?php foreach ($gallery_items as $image_data): ?>
                    <div class="swiper-slide">
                        <img data-src="<?php echo esc_url($image_data['thumb_url']); ?>" class="lazyload" alt="<?php echo esc_attr(sprintf(esc_html__('Thumbnail for %s', 'cob_theme'), $image_data['alt'])); ?>" />
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif;?>