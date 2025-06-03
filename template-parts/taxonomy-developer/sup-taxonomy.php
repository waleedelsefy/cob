<?php
// الحصول على كائن التصنيف الحالي.
$current_term = get_queried_object();

// دالة مساعدة لاختصار النص إلى 90 حرف.
function cob_trim_description( $text, $max_chars = 90 ) {
    if ( mb_strlen( $text ) > $max_chars ) {
        return mb_substr( $text, 0, $max_chars ) . '...';
    }
    return $text;
}

// جلب التصنيفات الفرعية للتصنيف الحالي.
$child_terms = get_terms( array(
    'taxonomy'   => $current_term->taxonomy, // مثلاً: city
    'parent'     => $current_term->term_id,
    'hide_empty' => false,
) );

// التأكد من وجود تصنيفات فرعية.
if ( ! empty( $child_terms ) && ! is_wp_error( $child_terms ) ) :
    ?>
    <section class="city-layout-section">
        <div class="container">
            <div class="section-layout">
                <!-- عمود النص / القائمة -->
                <div class="text-container">
                    <div class="text-content">
                        <div class="outer-circle">
                            <div class="inner-dot"></div>
                        </div>
                        <h3 class="article-title"><?php echo esc_html( $current_term->name ); ?></h3>
                        <?php if ( ! empty( $current_term->description ) ) : ?>
                            <p><?php echo esc_html( cob_trim_description( $current_term->description, 90 ) ); ?></p>
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
                                    <?php else : ?>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <!-- عمود الصور -->
                <div class="images-container">
                    <img data-src="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/articles-det.jpg' ); ?>" alt="<?php _e( 'Image 1', 'cob_theme' ); ?>" class="image1 lazyload">
                    <div class="inner-img">
                        <img data-src="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/articles-det2.jpg' ); ?>" alt="<?php _e( 'Image 2', 'cob_theme' ); ?>" class="image2 lazyload">
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
endif;
?>
