<?php
/**
 * Factory Listings Template for 'Capital of Business' Theme
 *
 * @package Capital_of_Business
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$theme_dir = get_template_directory_uri();

// Use transient caching to improve performance
$transient_key = 'latest_factories';
$factories_query = get_transient( $transient_key );

if ( false === $factories_query ) {
    $args = [
        'post_type'      => 'factory',
        'posts_per_page' => 6,           // Adjust as needed
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,          // Optimize if pagination is not required
    ];

    $factories_query = new WP_Query( $args );
    // Cache the query results for 1 hour (adjust as needed)
    set_transient( $transient_key, $factories_query, HOUR_IN_SECONDS );
}
?>

<div class="factorys">
    <div class="container">
        <div class="top-factorys">
            <div class="right-factorys">
                <h3 class="head"><?php esc_html_e( 'Latest Factories', 'cob_theme' ); ?></h3>
                <h5><?php esc_html_e( 'Discover the latest factories and advanced technologies that meet the needs of the modern industrial market', 'cob_theme' ); ?></h5>

            </div>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'factory' ) ); ?>" class="factorys-button">
                <?php esc_html__('View all', 'cob_theme'); ?>
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281171 7.68509C-0.0843415 7.31958 -0.0923715 6.73316 0.253053 6.3556L6.06104 0.535563C6.24982 0.346785 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535563C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="white"/>
                </svg>
            </a>
        </div>

        <div class="swiper swiper5">
            <div class="swiper-wrapper">
                <?php if ( $factories_query->have_posts() ) : ?>
                    <?php while ( $factories_query->have_posts() ) : $factories_query->the_post(); ?>
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
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p><?php esc_html_e( 'There are no factories currently available.', 'cob_theme' ); ?></p>
                    <p></p>
                <?php endif; ?>
            </div>
            <div class="swiper-button-prev">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.66602 6.00033H18.3327M1.66602 6.00033C1.66602 4.54158 5.82081 1.81601 6.87435 0.791992M1.66602 6.00033C1.66602 7.45908 5.82081 10.1847 6.87435 11.2087" stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="swiper-button-next">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.334 5.99967L1.66732 5.99967M18.334 5.99967C18.334 7.45842 14.1792 10.184 13.1257 11.208M18.334 5.99967C18.334 4.54092 14.1792 1.8153 13.1257 0.791341" stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
