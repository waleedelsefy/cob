<?php
/**
 * Template Name: Latest Projects
 */

$paged = 1;
?>
<section class="pagination-section">
    <div class="container">
        <div class="factorys-cards">
            <?php
            $projects_query = new WP_Query([
                'post_type'      => 'factory',
                'posts_per_page' => 3,
                'paged'          => $paged,
            ]);
            if ($projects_query->have_posts()):
                while ($projects_query->have_posts()): $projects_query->the_post();
                    get_template_part( 'template-parts/single/factorys-card' );
                endwhile;
                wp_reset_postdata();
            else:
                echo '<p>' . esc_html__( 'No projects found.', 'cob_theme' ) . '</p>';
            endif;
            ?>
        </div>

        <div class="pagination">
            <?php
            $big = 999999999;
            echo paginate_links([
                'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format'    => '?paged=%#%',
                'current'   => $paged,
                'total'     => $projects_query->max_num_pages,
                'prev_text' => '<svg…></svg> ' . esc_html__('Previous','cob_theme'),
                'next_text' => esc_html__('Next','cob_theme') . ' <svg…></svg>',
                'type'      => 'list',
                'end_size'  => 1,
                'mid_size'  => 2,
            ]);
            ?>
        </div>
    </div>
</section>
