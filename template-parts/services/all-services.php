<?php

$args = [
    'post_type'      => 'services',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
];

$services_query = new WP_Query($args);
?>

<div class="all-services">
    <div class="container">
        <h2><?php esc_html_e( 'Services provided', 'cob_theme' ); ?></h2>
        <div class="services-cards">
            <?php if ($services_query->have_posts()) : ?>
                <?php while ($services_query->have_posts()) : $services_query->the_post(); ?>
                    <?php
                    $svg_icon = get_post_meta(get_the_ID(), '_cob_service_svg', true);
                    ?>
                    <div class="services-card">
                        <div class="svg-hold">
                            <?php if (!empty($svg_icon)) : ?>
                                <?php echo $svg_icon;?>
                            <?php else : ?>
                                <svg width="29" height="28" fill="white"><rect width="29" height="28" fill="gray"/></svg>
                            <?php endif; ?>
                        </div>
                        <h5><?php the_title(); ?></h5>
                        <p><?php echo get_the_excerpt(); ?></p>
                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>

                <p><?php esc_html_e( 'There are no services currently available.', 'cob_theme' ); ?></p>

            <?php endif; ?>
        </div>


    </div>
</div>