<?php

get_header(); 
if ( have_posts() ) :
    while ( have_posts() ) : the_post(); 
        ?>

        <div class="head-services head-flat">
            <div class="container">
                <div class="breadcrumb">
                    <?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) : rank_math_the_breadcrumbs(); endif; ?>
                </div>
            </div>
        </div>

        <?php 
        get_template_part( 'template-parts/properties-post/main-flat' );
        get_template_part( 'template-parts/properties-post/landing-flat' );
        get_template_part( 'template-parts/properties-post/map' );
        get_template_part( 'template-parts/properties-post/similar-units' );
        get_template_part( 'template-parts/contact-section' );
        ?>

        <script src="<?php echo get_template_directory_uri() ?>/assets/js/single-properties.js"></script>

    <?php 
    endwhile;
else :
    echo '<p>عذراً، الصفحة غير موجودة.</p>';
endif;

get_footer();
