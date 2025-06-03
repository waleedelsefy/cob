<?php
/**
 * The template for displaying search results pages.
 *
 * @package cob_theme
 */

get_header();
?>
<section class="pagination-section pagination-city">

<div class="search-results container">


    <?php if ( have_posts() ) : ?>
    <div class="properties-cards">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/single/properties-card' ); ?>

            <?php endwhile; ?>

        <?php
        the_posts_pagination( array(
            'prev_text' => esc_html__( 'Previous', 'cob_theme' ),
            'next_text' => esc_html__( 'Next', 'cob_theme' ),
        ) );
        ?>

    <?php else : ?>
        <p><?php esc_html_e( 'No results found.', 'cob_theme' ); ?></p>
        <?php get_search_form(); ?>
    <?php endif; ?>
</div>
</section>

<?php get_footer(); ?>
