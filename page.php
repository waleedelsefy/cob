<?php
/**
 * Default page template for the COB theme.
 *
 * This file displays the content of static pages.
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <?php
        // Start the Loop to display the page content
        while ( have_posts() ) :
            the_post();

            // Load the template part for page content
            get_template_part( 'template-parts/content', 'page' );

            // If comments are open or there is at least one comment, load the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
        endwhile; // End of the loop.
        ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php

// Load the footer.
get_footer();
?>
