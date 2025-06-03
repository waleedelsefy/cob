<section>
    <div class="container">
        <div class="section-layout">
            <div class="text-container">
                <div class="text-content">
                    <div class="outer-circle">
                        <div class="inner-dot"></div>
                    </div>
                    <?php
                    $locations = get_nav_menu_locations();

                    if ( isset( $locations['top-articles'] ) ) {
                        $menu = wp_get_nav_menu_object( $locations['top-articles'] );
                        if ( $menu ) {
                            $menu_items = wp_get_nav_menu_items( $menu->term_id );
                        }
                    }

                    if ( ! empty( $menu_items ) ) {
                        $first_item = reset( $menu_items );
                        echo '<h3 class="article-title">' . esc_html( get_the_title( $first_item->object_id ) ) . '</h3>';

                        $menu_items = array_slice( $menu_items, 0, 3 );
                        $circle_classes = array( 'outer-circle2', 'outer-circle3', 'outer-circle4' );

                        echo '<ul class="content-list">';
                        $index = 0;
                        foreach ( $menu_items as $item ) {
                            $post_id      = $item->object_id;
                            $post_title   = get_the_title( $post_id );
                            $post_excerpt = get_the_excerpt( $post_id );
                            $post_excerpt = wp_html_excerpt( $post_excerpt, 120, '...' );
                            $circle_class = isset( $circle_classes[ $index ] ) ? $circle_classes[ $index ] : 'outer-circle2';
                            ?>
                            <li>
                                <div class="<?php echo esc_attr( $circle_class ); ?>">
                                    <div class="inner-dot"></div>
                                </div>
                                <h3 class="subtitle"><?php echo esc_html( $post_title ); ?></h3>
                                <p><?php echo esc_html( $post_excerpt ); ?></p>
                            </li>
                            <?php
                            $index++;
                        }
                        echo '</ul>';
                    } else {
                        echo '<h3 class="article-title">' . esc_html__( 'Top Articles', 'cob_theme' ) . '</h3>';
                        echo '<ul class="content-list"><li>' . esc_html__( 'No articles selected.', 'cob_theme' ) . '</li></ul>';
                    }
                    ?>
                </div><!-- .text-content -->
            </div><!-- .text-container -->
            <div class="images-container">
                <img data-src="<?php echo get_template_directory_uri();?>/assets/imgs/articles-det.jpg" alt="<?php esc_attr_e( 'Image 1', 'cob_theme' ); ?>" class="image1 lazyload">
                <div class="inner-img">
                    <img data-src="<?php echo get_template_directory_uri();?>/assets/imgs/articles-det2.jpg" alt="<?php esc_attr_e( 'Image 2', 'cob_theme' ); ?>" class="image2 lazyload">
                </div>
            </div>
        </div>
    </div>
</section>
