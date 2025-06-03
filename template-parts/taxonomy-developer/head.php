<div class="head-city">
    <div class="container">
        <div class="breadcrumb">
            <?php
            if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
                rank_math_the_breadcrumbs();
            }
            ?>
        </div>
        <div class="main-section">
            <div class="text-container">
                <h2><?php single_term_title(); ?></h2>
                <div class="term-description">
                    <?php echo term_description(); ?>
                </div>
                <?php get_template_part('template-parts/button-container'); ?>

            </div>
            <div class="images-container">
                <img data-src="<?php echo get_template_directory_uri(); ?>/assets/imgs/services2.jpg" alt="<?php esc_attr_e( 'Image 2', 'cob_theme' ); ?>" class="image1 lazyload">

                <div class="inner-img">
                    <?php
                    $term    = get_queried_object();
                    $term_id = isset( $term->term_id ) ? $term->term_id : 0;
                    $city_image = get_term_meta( $term_id, 'developer_image', true );
                    $image_src  = ! empty( $city_image ) ? esc_url( $city_image ) : get_template_directory_uri() . '/assets/imgs/services1.png';
                    ?>
                    <img data-src="<?php echo $image_src; ?>" alt="<?php esc_attr_e( 'developer Image', 'cob_theme' ); ?>" class="image2 lazyload">

                </div>
            </div>
        </div>
    </div>
</div>
