<?php
$theme_dir = get_template_directory_uri();

$main_img_placeholder      = $theme_dir . '/assets/images/placeholder/hiring.png';
$logo_white_placeholder    = $theme_dir . '/assets/images/placeholder/logo_white_placeholder.png';
$default_main_image        = $theme_dir . '/assets/images/placeholder/hiring.png';

$large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
$main_img        = !empty($large_image_url) ? esc_url($large_image_url[0]) : $default_main_image;
?>

<div class="head-we">
    <div class="container">
        <div class="breadcrumb">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>
    </div>
    <div class="areas-landing w-full">
        <img
                src="<?php echo esc_url( $main_img_placeholder ); ?>"
                data-src="<?php echo esc_url( $main_img ); ?>"
                class="img1 lazyload"
                alt="img1"
                width="1000" height="600" loading="lazy"
        >
        <img
                src="<?php echo esc_url( $logo_white_placeholder ); ?>"
                data-src="<?php echo $theme_dir ?>/assets/imgs/logo-white.png"
                class="img2 lazyload"
                alt="img2"
                width="300" height="100" loading="lazy"
        >
    </div>
</div>
