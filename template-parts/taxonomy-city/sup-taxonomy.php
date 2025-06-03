<?php
/**
 * Template Name: Top 5 Open Compounds by City
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_term = get_queried_object();
$city_id      = $current_term->term_id;

$transient_key = 'top5_open_compounds_' . $city_id;
$compounds     = get_transient( $transient_key );

if ( false === $compounds ) {
    $compounds = get_terms( array(
        'taxonomy'   => 'compound',
        'hide_empty' => false,
        'number'     => 5,
        'orderby'    => 'meta_value_num',
        'order'      => 'DESC',
        'meta_query' => array(
            array(
                'key'     => 'compound_city',
                'value'   => '"' . $city_id . '"',
                'compare' => 'LIKE',
            ),
            array(
                'key'     => 'compound_status',
                'value'   => 'open',
                'compare' => '=',
            )
        )
    ) );
    set_transient( $transient_key, $compounds, 12 * HOUR_IN_SECONDS );
}
?>

<section class="top-compounds-section">
    <div class="container">
        <h3 class="section-title"><?php esc_html_e( 'Top 5 Open Compounds', 'cob_theme' ); ?></h3>
        <?php if ( ! empty( $compounds ) && ! is_wp_error( $compounds ) ) : ?>
            <ul class="compounds-list">
                <?php foreach ( $compounds as $compound ) : ?>
                    <li>
                        <a href="<?php echo esc_url( get_term_link( $compound ) ); ?>">
                            <h4><?php echo esc_html( $compound->name ); ?></h4>
                        </a>
                        <?php
                        $prop_count = get_term_meta( $compound->term_id, 'propertie_count', true );
                        if ( ! $prop_count ) {
                            $prop_count = $compound->count;
                        }
                        ?>
                        <p><?php echo esc_html( $prop_count ); ?> <?php esc_html_e( 'Properties', 'cob_theme' ); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p><?php esc_html_e( 'No open compounds available.', 'cob_theme' ); ?></p>
        <?php endif; ?>
    </div>
</section>
