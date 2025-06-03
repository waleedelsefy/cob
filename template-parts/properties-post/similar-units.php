<?php
/**
 * Similar Units Section
 *
 * @package cob_theme
 */
$paged      = 1;
$city_terms = get_the_terms( get_the_ID(), 'city' );
$city_slug  = ( ! is_wp_error( $city_terms ) && $city_terms ) ? $city_terms[0]->slug : '';

$query = new WP_Query([
    'post_type'      => 'properties',
    'posts_per_page' => 6,
    'paged'          => $paged,
    'post__not_in'   => [ get_the_ID() ],
    'tax_query'      => [[
        'taxonomy' => 'city',
        'field'    => 'slug',
        'terms'    => $city_slug,
    ]],
]);
?>

<section class="pagination-section pagination-city">
    <div class="container">
        <div class="top-compounds">
            <div class="right-compounds">
                <h3 class="head">
                    <?php
                    $city_obj = get_queried_object();
                    if ( $city_obj && ! is_wp_error( $city_obj ) ) {
                        echo '<p>' . sprintf(
                                esc_html__( 'The most searched compounds in %s', 'cob_theme' ),
                                esc_html( $city_obj->name )
                            ) . '</p>';
                    }
                    ?>
                </h3>
            </div>
        </div>

        <div class="properties-cards">
            <?php
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    get_template_part( 'template-parts/single/properties-card' );
                }
                wp_reset_postdata();
            } else {
                echo '<p>'. esc_html__( 'There are no posts currently available.', 'cob_theme' ) .'</p>';
            }
            ?>
        </div>
    </div>
<div class="container similar-units-pagination-container">
    <div class="similar-units-pagination">
        <?php
        echo paginate_links([
            'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'format'    => '?paged=%#%',
            'current'   => $paged,
            'total'     => $query->max_num_pages,
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'type'      => 'list',
        ]);
        ?>
    </div>
</div>
</section>
