<?php
$post_id      = get_the_ID();
$price        = get_post_meta( 'price', $post_id );
$down_payment = get_post_meta( 'down_payment', $post_id );
$location     = get_post_city( $post_id );
$area         = get_post_meta( 'area', $post_id );

// Get gallery image IDs from cache or meta (if not already cached)

$gallery_ids = get_post_meta( $post_id, '_gallery_image_ids', true );

$gallery_images = [];

if ( ! empty( $gallery_ids ) && is_array( $gallery_ids ) ) {
    foreach ( $gallery_ids as $attachment_id ) {
        // Get the image URL (using 'large' size, you can change the size if needed)
        $image_url = wp_get_attachment_image_url( $attachment_id, 'large' );
        if ( $image_url ) {
            $gallery_images[] = $image_url;
        }
    }
}

?>
<div class="swiper-slide">
<a href="<?php the_permalink(); ?>" class="factorys-card">
    <ul class="big-ul">
        <li>
            <div class="swiper swiper7-in swiper-in">
                <div class="swiper-wrapper">
                    <?php if ( ! empty( $gallery_images ) ) : ?>
                        <?php foreach ( $gallery_images as $image_url ) : ?>
                            <div class="swiper-slide">
                                <img data-src="<?php echo esc_url( $image_url ); ?>" class="swiper-in-img lazyload" alt="<?php echo esc_attr( get_the_title() ); ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="swiper-slide">
                            <?php $thumbnail = get_the_post_thumbnail_url( $post_id, 'large' ); ?>
                            <?php if ( $thumbnail ) : ?>
                                <img data-src="<?php echo esc_url( $thumbnail ); ?>" class="swiper-in-img lazyload" alt="<?php echo esc_attr( get_the_title() ); ?>">
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>

        </li>
        <li>
            <div class="bottom-factorys-swiper">
                <ul>
                    <li>
                        <div class="prices">
                            <p>
                                <span style="font-weight:bold"><?php echo esc_html( $price ); ?> ج.م</span> السعر
                            </p>
                            <span> مقدم <?php echo esc_html( $down_payment ); ?></span>
                        </div>
                    </li>
                    <li>
                        <h6><?php the_title(); ?></h6>
                        <span>
														<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M12.5837 7.50033C12.5837 8.65091 11.6509 9.58366 10.5003 9.58366C9.34974 9.58366 8.41699 8.65091 8.41699 7.50033C8.41699 6.34973 9.34974 5.41699 10.5003 5.41699C11.6509 5.41699 12.5837 6.34973 12.5837 7.50033Z" stroke="#707070" stroke-width="1.25"/>
															<path d="M11.5482 14.5783C6.87891 12.0843 3.42965 9.29824 5.11175 5.25343C6.02124 3.06643 8.20444 1.66699 10.5005 1.66699C12.7965 1.66699 14.9797 3.06643 15.8892 5.25343C17.5692 9.29316 14.1283 12.0929 11.5482 14.5783Z" stroke="#707070" stroke-width="1.25"/>
														</svg>
														<?php echo esc_html( $location ); ?>
													</span>
                    </li>
                    <li>
                        <?php get_template_part('template-parts/single/bottom-icons'); ?>
                    </li>

                </ul>
            </div>
        </li>
    </ul>
</a>
</div>