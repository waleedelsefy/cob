<?php
/**
 * Main Template File
 *
 * @package Capital_of_Business
 */

get_header(); ?>

<main id="primary" class="site-main">
    <section class="container">
        <h1><?php esc_html_e('Welcome to Capital of Business', 'cob_theme'); ?></h1>
        <p><?php esc_html_e('This is the main template file.', 'cob_theme'); ?></p>

        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                get_template_part('template-parts/content', get_post_format());
            endwhile;
            the_posts_navigation();
        else :
            get_template_part('template-parts/content', 'none');
        endif;
        ?>
    </section>
</main>

<?php get_footer(); ?>
