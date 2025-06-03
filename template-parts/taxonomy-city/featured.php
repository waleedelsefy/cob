<?php
/**
 * Most Searched Compounds Template
 *
 * @package Capital_of_Business
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$theme_dir = get_template_directory_uri();

$compounds = get_terms( [
    'taxonomy'   => 'compound',
    'hide_empty' => false,
] );

$compound_modified = array();

if ( ! empty( $compounds ) && ! is_wp_error( $compounds ) ) {
    foreach ( $compounds as $compound ) {
        $args = [
            'post_type'      => 'properties',
            'posts_per_page' => 1,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'tax_query'      => [
                [
                    'taxonomy' => 'compound',
                    'field'    => 'term_id',
                    'terms'    => $compound->term_id,
                ],
            ],
        ];
        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            $query->the_post();
            $last_modified = get_the_modified_date( 'U' );
            $compound_modified[ $compound->term_id ] = $last_modified;
        } else {
            $compound_modified[ $compound->term_id ] = 0;
        }
        wp_reset_postdata();
    }

    usort( $compounds, function( $a, $b ) use ( $compound_modified ) {
        return $compound_modified[ $b->term_id ] - $compound_modified[ $a->term_id ];
    } );

    $compounds = array_slice( $compounds, 0, 9 );
}
?>

<div class="compounds">
    <div class="container">
        <div class="top-compounds">
            <div class="right-compounds">
                <h3 class="head"><?php esc_html_e( 'Most Searched Compounds', 'cob_theme' ); ?></h3>
            </div>
        </div>

        <div class="swiper swiper1">
            <div class="swiper-wrapper">
                <?php if ( ! empty( $compounds ) && ! is_wp_error( $compounds ) ) : ?>
                    <?php foreach ( $compounds as $compound ) : ?>
                        <div class="swiper-slide">
                            <a href="<?php echo esc_url( get_term_link( $compound ) ); ?>" class="compounds-card">
                                <div class="top-card-comp">
                                    <h6><?php echo esc_html( $compound->name ); ?></h6>
                                    <span>
                                        <?php
                                        $prop_count = get_term_meta( $compound->term_id, 'propertie_count', true );
                                        if ( ! $prop_count ) {
                                            $prop_count = $compound->count;
                                        }
                                        echo esc_html( $prop_count ) . ' ' . esc_html__( 'Properties', 'cob_theme' );
                                        ?>
                                    </span>
                                </div>

                                <?php
                                $thumbnail_id = get_term_meta( $compound->term_id, 'compound_image', true );
                                $image_url = $thumbnail_id ? $thumbnail_id : $theme_dir . '/assets/imgs/default.jpg';
                                ?>
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $compound->name ); ?>" class="lazyload">
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p><?php esc_html_e( 'No compounds available at the moment.', 'cob_theme' ); ?></p>
                <?php endif; ?>
            </div>

            <!-- أزرار التنقل -->
            <div class="swiper-button-prev">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.66602 6.00033H18.3327M1.66602 6.00033C1.66602 4.54158 5.82081 1.81601 6.87435 0.791992M1.66602 6.00033C1.66602 7.45908 5.82081 10.1847 6.87435 11.2087" stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="swiper-button-next">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.334 5.99967L1.66732 5.99967M18.334 5.99967C18.334 7.45842 14.1792 10.184 13.1257 11.208M18.334 5.99967C18.334 4.54092 14.1792 1.8153 13.1257 0.791341" stroke="#fff" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
