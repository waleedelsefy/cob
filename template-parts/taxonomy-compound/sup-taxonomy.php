<?php
$current_term = get_queried_object();

function cob_trim_description( $text, $max_chars = 90 ) {
    if ( mb_strlen( $text ) > $max_chars ) {
        return mb_substr( $text, 0, $max_chars ) . '...';
    }
    return $text;
}

$developer_ids = get_term_meta( $current_term->term_id, 'compound_developer', true );
$city_ids      = get_term_meta( $current_term->term_id, 'compound_city', true );

$developer_names = array();
if ( ! empty( $developer_ids ) && is_array( $developer_ids ) ) {
    foreach ( $developer_ids as $dev_id ) {
        $dev_term = get_term( $dev_id, 'developer' );
        if ( ! is_wp_error( $dev_term ) && $dev_term ) {
            $developer_names[] = $dev_term->name;
        }
    }
}

$city_names = array();
if ( ! empty( $city_ids ) && is_array( $city_ids ) ) {
    foreach ( $city_ids as $city_id ) {
        $city_term = get_term( $city_id, 'city' );
        if ( ! is_wp_error( $city_term ) && $city_term ) {
            $city_names[] = $city_term->name;
        }
    }
}

$child_terms = get_terms( array(
    'taxonomy'   => $current_term->taxonomy,
    'parent'     => $current_term->term_id,
    'hide_empty' => false,
) );
?>

<?php if ( ! empty( $child_terms ) && ! is_wp_error( $child_terms ) ) : ?>
    <section class="city-layout-section">
        <div class="container">
            <div class="section-layout">
                <div class="text-container">
                    <div class="text-content">
                        <div class="outer-circle">
                            <div class="inner-dot"></div>
                        </div>
                        <h3 class="article-title"><?php echo esc_html( $current_term->name ); ?></h3>

                        <?php if ( ! empty( $current_term->description ) ) : ?>
                            <p><?php echo esc_html( cob_trim_description( $current_term->description, 90 ) ); ?></p>
                        <?php endif; ?>

                        <?php if ( ! empty( $developer_names ) ) : ?>
                            <p><strong>المطور:</strong> <?php echo esc_html( implode( ', ', $developer_names ) ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $city_names ) ) : ?>
                            <p><strong>المدينة:</strong> <?php echo esc_html( implode( ', ', $city_names ) ); ?></p>
                        <?php endif; ?>

                        <ul class="content-list">
                            <?php foreach ( $child_terms as $child ) : ?>
                                <li>
                                    <div class="outer-circle3">
                                        <div class="inner-dot"></div>
                                    </div>
                                    <a href="<?php echo esc_url( get_term_link( $child ) ); ?>">
                                        <h3 class="subtitle"><?php echo esc_html( $child->name ); ?></h3>
                                    </a>
                                    <?php if ( ! empty( $child->description ) ) : ?>
                                        <p><?php echo esc_html( cob_trim_description( $child->description, 90 ) ); ?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="images-container">
                    <img data-src="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/articles-det.jpg' ); ?>" alt="<?php _e( 'Image 1', 'cob_theme' ); ?>" class="image1 lazyload">
                    <div class="inner-img">
                        <img data-src="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/articles-det2.jpg' ); ?>" alt="<?php _e( 'Image 2', 'cob_theme' ); ?>" class="image2 lazyload">
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
