
<div class="head-services">
    <div class="container">
        <div class="breadcrumb">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>
        <div class="main-section">
            <div class="text-container">
                <h2><?php echo esc_html( get_the_title() );?></h2>
                <?php
                the_content();
                ?>
                <?php get_template_part('template-parts/button-container'); ?>

            </div>
            <div class="images-container">
                <img data-src="<?php echo get_template_directory_uri();?>/assets/imgs/services1.png" alt="<?php esc_attr_e( 'services1', 'cob_theme' ); ?>" class="image1 lazyload">
                <div class="inner-img">
                    <img data-src="<?php echo get_template_directory_uri();?>/assets/imgs/services2.jpg" alt="<?php esc_attr_e( 'Image 2', 'cob_theme' ); ?>" class="image2 lazyload">
                </div>
            </div>
        </div>
    </div>
</div>
