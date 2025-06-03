<?php
$theme_dir = get_template_directory_uri();

?>
<div class="head-areas">
    <div class="container">
        <div class="breadcrumb">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>


    </div>
    <div class="areas-landing">
        <img
            data-src="<?php
            $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
            if ( ! empty( $large_image_url ) && isset( $large_image_url[0] ) ) {
                echo esc_url( $large_image_url[0] );
            } else {
                echo esc_url( $theme_dir . '/assets/imgs/landing-areas.png' );
            }
            ?>"
            class="img1 lazyload"
            alt="<?php esc_attr_e( 'landing areas', 'cob_theme' ); ?>">
        <img data-src="<?php   echo esc_url( $theme_dir . '/assets/imgs/logo-white.png' )?>" class="img2 ls-is-cached lazyloaded" alt="img2" src="<?php   echo esc_url( $theme_dir . '/assets/imgs/logo-white.png' )?>">
    </div>
</div>
