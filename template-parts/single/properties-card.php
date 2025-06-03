<?php
$post_id       = get_the_ID();
$project_id    = (int) get_post_field( 'post_parent', $post_id );
$location       = get_post_meta( $post_id, 'propertie_location', true );
$area = !empty( get_post_meta( $post_id, 'area', true ) )
    ? intval( get_post_meta( $post_id, 'area', true ) )
    : ' - ';
$price_meta     = get_post_meta( $post_id, 'price', true );
$max_price_meta = get_post_meta( $post_id, 'max_price', true );

$price_value = !empty( $price_meta )
    ? floatval( $price_meta )
    : ( !empty( $max_price_meta ) ? floatval( $max_price_meta ) : ' - ' );

if ( is_numeric( $price_value ) ) {
    $price = number_format( $price_value, 0, '.', ',' );
} else {
    $price = $price_value;
}

$max_price = !empty( $max_price_meta ) ? floatval( $max_price_meta ) : ' - ';
$min_price = !empty( get_post_meta( $post_id, 'min_price', true ) )
    ? floatval( get_post_meta( $post_id, 'min_price', true ) )
    : ' - ';

$rooms     = !empty( get_post_meta( $post_id, 'bedrooms', true ) )
    ? intval( get_post_meta( $post_id, 'bedrooms', true ) )
    : ' - ';
$bathrooms = !empty( get_post_meta( $post_id, 'bathrooms', true ) )
    ? intval( get_post_meta( $post_id, 'bathrooms', true ) )
    : ' - ';

$delivery_year = !empty( get_post_meta( $post_id, 'delivery', true ) )
    ? get_post_meta( $post_id, 'delivery', true )
    : ' - ';

$installments      = get_post_meta( $post_id, 'unit_installments', true );
$unit_down_payment = get_post_meta( $post_id, 'unit_down_payment', true );

$gallery_images = [];
$gallery_ids    = get_post_meta( $post_id, '_gallery_image_ids', true );

if ( ! empty( $gallery_ids ) ) {
    if ( ! is_array( $gallery_ids ) ) {
        $gallery_ids = explode( ',', $gallery_ids );
    }
    foreach ( $gallery_ids as $gallery_id ) {
        $image_url = wp_get_attachment_image_url( absint( $gallery_id ), 'large' );
        if ( $image_url ) {
            $gallery_images[] = $image_url;
        }
    }
}
if ( empty( $gallery_images ) ) {
    $attachments = get_attached_media( 'image', $post_id );
    if ( ! empty( $attachments ) ) {
        foreach ( $attachments as $attachment ) {
            $image_url = wp_get_attachment_image_url( $attachment->ID, 'large' );
            if ( $image_url ) {
                $gallery_images[] = $image_url;
            }
        }
    }
}
if ( empty( $gallery_images ) ) {
    $thumbnail = get_the_post_thumbnail_url( $post_id, 'large' );
    if ( $thumbnail ) {
        $gallery_images[] = $thumbnail;
    }
}

// Get developer image.
$developer_image_url = get_template_directory_uri() . '/assets/imgs/card-devloper-default.png';
$developers = get_the_terms( $post_id, 'developer' );
if ( $developers && ! is_wp_error( $developers ) ) {
    $developer = reset( $developers );
    $developer_thumbnail_id = absint( get_term_meta( $developer->term_id, 'thumbnail_id', true ) );
    if ( $developer_thumbnail_id ) {
        $developer_image_url = wp_get_attachment_url( $developer_thumbnail_id );
    }
}
$compound_terms = get_the_terms( $post_id, 'compound' );
$compound_name  = '';
if ( $compound_terms && ! is_wp_error( $compound_terms ) ) {
    $compound_term = reset( $compound_terms );
    $compound_name = $compound_term->name;
}
$propertie_type_terms = get_the_terms( $post_id, 'type' );
$propertie_type  = '';
if ( $propertie_type_terms && ! is_wp_error( $propertie_type_terms ) ) {
    $propertie_type_term = reset( $propertie_type_terms );
    $propertie_type = $propertie_type_term->name;
}
$project_title_terms = get_the_terms( $post_id, 'compound' );
$project_title  = '';
$project_link = '';
if ( $project_title_terms && ! is_wp_error( $project_title_terms ) ) {
    $project_title_term = reset( $project_title_terms );
    $project_title = $project_title_term->name;
    $project_link =  get_term_link( $project_title_term );
}
$city_terms = get_the_terms( $post_id, 'city' );
$city_name_output = '';
$city_name_link   = '';
if ( $city_terms && ! is_wp_error( $city_terms ) ) {
    $city_term = reset( $city_terms );
    $city_name_output = $city_term->name;
    $city_name_link = get_term_link( $city_term );
}

$taxonomy_output = [];
if ( ! empty( $compound_name ) ) {
    $taxonomy_output[] = esc_html( $compound_name );
}
if ( ! empty( $city_name_output ) ) {
    $taxonomy_output[] = esc_html( $city_name_output );
}
$taxonomy_string = ! empty( $taxonomy_output ) ? implode( ', ', $taxonomy_output ) : '';
$placeholder = get_template_directory_uri() . '/assets/imgs/default.jpg';
?>
<div  class="properties-card">
    <ul class="big-ul">
        <div  class="top-card-properties">
            <img src="<?php echo esc_url( $placeholder ); ?>" data-src="<?php echo esc_url( $developer_image_url ); ?>" alt="<?php echo isset( $developer ) ? esc_attr( $developer->name ) : esc_attr__( 'Default Image', 'cob_theme' ); ?>" class="lazyload">
        </div>
        <li>
           <?php
global $swiper_count;
$swiper_count = isset($swiper_count) ? $swiper_count + 1 : 1; // Unique counter for each Swiper
$swiper_class = "swiper-instance-" . $swiper_count;
?>
<div class="swiper <?php echo esc_attr($swiper_class); ?> swiper-in" data-swiper-id="<?php echo esc_attr($swiper_class); ?>">
    <div class="swiper-wrapper">
        <?php if ( ! empty( $gallery_images ) ) : ?>
            <?php foreach ( $gallery_images as $image_url ) : ?>
                <a href="<?php the_permalink(); ?>" class="swiper-slide">
                    <img data-src="<?php echo esc_url( $image_url ); ?>" class="swiper-in-img lazyload" alt="<?php echo esc_attr( get_the_title() ); ?>">
                </a>
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
    <div class="swiper-pagination <?php echo esc_attr($swiper_class); ?>-pagination"></div> <!-- Unique Pagination -->
</div>
        </li>
        <li>
            <div class="bottom-properties-swiper">
                <ul>
                    <li>
                        <div class="prices">
                            <p>
                                <span style="font-weight:bold"><?php echo esc_html( $price ); ?> <?php esc_html_e( 'EGP', 'cob_theme' ); ?></span>
                            </p>
                            <span>
                                <?php esc_html_e( 'Down Payment:', 'cob_theme' ); ?>
                                <?php echo esc_html( get_post_meta( $post_id, 'propertie_down_payment', true ) ); ?> /
                                <?php echo esc_html( $installments ); ?> <?php esc_html_e( 'Years', 'cob_theme' ); ?>
                            </span>
                        </div>
                    </li>
                    <li>
                        <h6><?php echo esc_html( $propertie_type ); ?></h6>
                        <div class="info">
                            <span>
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_601_5318" style="mask-type:alpha" maskProperties="userSpaceOnUse" x="0" y="0" width="21" height="20">
                                        <rect x="0.5" width="20" height="20" fill="#D9D9D9"></rect>
                                    </mask>
                                    <g mask="url(#mask0_601_5318)">
                                        <path d="M1.5 17H3.3M3.3 17H10.5M3.3 17V5.00019C3.3 3.95008 3.3 3.42464 3.49619 3.02356C3.66876 2.67075 3.94392 2.38412 4.28262 2.20437C4.66766 2 5.17208 2 6.18018 2H7.62018C8.62827 2 9.13209 2 9.51711 2.20437C9.85578 2.38412 10.1314 2.67075 10.304 3.02356C10.5 3.42425 10.5 3.94905 10.5 4.9971V7.85572M10.5 17H17.7M10.5 17V7.85572M10.5 7.85572L10.8253 7.5513C11.5056 6.9147 11.8458 6.59631 12.2303 6.47556C12.5691 6.36917 12.9306 6.36917 13.2694 6.47556C13.654 6.59633 13.9944 6.91456 14.6748 7.5513L16.7448 9.48847C17.0965 9.81763 17.272 9.98244 17.3982 10.1799C17.51 10.3548 17.5931 10.5479 17.6433 10.7516C17.7 10.9812 17.7 11.2278 17.7 11.7202V17M17.7 17H19.5" stroke="#707070" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </g>
                                </svg>
                                <a style="color: #707070" href="<?php echo esc_url( $project_link ); ?>" target="_blank" rel="noopener noreferrer">
        <?php echo esc_html( $project_title ); ?>
    </a>
                            </span>
                            <div class="left-icons">
                                <a target="_blank" href="https://wa.me/2<?php echo esc_attr( get_option( 'company_whatsapp', '0123456789' ) ); ?>?text=اريد الاستفسار عن <?php the_title(); ?> قادم من <?php the_permalink(); ?>" aria-label="Inquire about <?php the_title(); ?> via WhatsApp" class="cta-wts" rel="nofollow noopener">

                                    <svg width="25" height="26" viewBox="0 0 25 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.5 24.3095C18.746 24.3095 23.8095 19.246 23.8095 13C23.8095 6.75387 18.746 1.69043 12.5 1.69043C6.25387 1.69043 1.19043 6.75387 1.19043 13C1.19043 14.5594 1.50604 16.0452 2.07689 17.3968C2.39238 18.1436 2.55013 18.5172 2.56966 18.7995C2.58919 19.0818 2.50611 19.3922 2.33995 20.0132L1.19043 24.3095L5.48666 23.16C6.10767 22.9938 6.41819 22.9107 6.70045 22.9303C6.98273 22.9497 7.35621 23.1075 8.10321 23.423C9.45481 23.9938 10.9405 24.3095 12.5 24.3095Z" fill="#00DE3E" stroke="white" stroke-width="1.69643" stroke-linejoin="round"></path>
                                        <path d="M8.64182 13.4262L9.62682 12.2028C10.042 11.6872 10.5551 11.2073 10.5954 10.5207C10.6054 10.3473 10.4835 9.56882 10.2395 8.01183C10.1436 7.39993 9.57227 7.34473 9.07743 7.34473C8.43259 7.34473 8.11016 7.34473 7.78999 7.49097C7.38533 7.67581 6.96987 8.19555 6.87869 8.63099C6.80656 8.97551 6.86017 9.21291 6.96738 9.68771C7.42275 11.7043 8.49102 13.6959 10.1475 15.3524C11.804 17.0089 13.7956 18.0772 15.8122 18.5326C16.287 18.6398 16.5244 18.6934 16.869 18.6212C17.3044 18.5301 17.8241 18.1147 18.009 17.7099C18.1552 17.3897 18.1552 17.0674 18.1552 16.4225C18.1552 15.9276 18.1 15.3564 17.4881 15.2605C15.9311 15.0164 15.1527 14.8945 14.9792 14.9046C14.2927 14.9448 13.8127 15.458 13.2971 15.8731L12.0738 16.8581" stroke="white" stroke-width="1.69643"></path>
                                    </svg>
                                </a>
                                        <a href="tel:<?php echo esc_attr( get_option( 'company_phone', '0123456789' ) ); ?>" target="_blank" rel="noopener noreferrer" >

                                <svg width="23" height="24" viewBox="0 0 23 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.28696 4.88869L7.83157 3.86407C7.53382 3.19413 7.38494 2.85914 7.16229 2.60279C6.88325 2.28152 6.51952 2.04515 6.11261 1.92064C5.78793 1.82129 5.42135 1.82129 4.68821 1.82129C3.61573 1.82129 3.07949 1.82129 2.62934 2.02745C2.09908 2.2703 1.6202 2.79762 1.4294 3.34875C1.26742 3.81662 1.31383 4.29743 1.40661 5.25904C2.3943 15.4947 8.00599 21.1064 18.2416 22.0941C19.2033 22.1869 19.6841 22.2333 20.1519 22.0713C20.7031 21.8805 21.2304 21.4016 21.4733 20.8714C21.6794 20.4212 21.6794 19.885 21.6794 18.8125C21.6794 18.0793 21.6794 17.7128 21.5801 17.3881C21.4555 16.9811 21.2192 16.6174 20.8979 16.3384C20.6416 16.1157 20.3066 15.9669 19.6366 15.6691L18.612 15.2137C17.8865 14.8913 17.5237 14.7301 17.1551 14.695C16.8022 14.6614 16.4466 14.711 16.1163 14.8396C15.7713 14.9739 15.4664 15.2281 14.8563 15.7363C14.2492 16.2423 13.9457 16.4953 13.5747 16.6308C13.2458 16.7509 12.8111 16.7954 12.4648 16.7443C12.0741 16.6868 11.7749 16.5269 11.1765 16.2071C9.315 15.2123 8.2884 14.1858 7.29354 12.3241C6.97379 11.7258 6.81392 11.4266 6.75634 11.0359C6.7053 10.6895 6.74979 10.2548 6.86992 9.92603C7.00543 9.55506 7.25842 9.25149 7.76438 8.64432C8.27267 8.03438 8.52681 7.72941 8.66118 7.38434C8.78977 7.05414 8.83927 6.69841 8.80572 6.34564C8.77065 5.977 8.60942 5.61423 8.28696 4.88869Z" stroke="#EC3C43" stroke-width="1.69643" stroke-linecap="round"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <span>
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12.0837 7.50033C12.0837 8.65091 11.1509 9.58366 10.0003 9.58366C8.84974 9.58366 7.91699 8.65091 7.91699 7.50033C7.91699 6.34973 8.84974 5.41699 10.0003 5.41699C11.1509 5.41699 12.0837 6.34973 12.0837 7.50033Z" stroke="#707070" stroke-width="1.25"/>
        <path d="M11.0482 14.5783C10.7671 14.849 10.3914 15.0003 10.0005 15.0003C9.60949 15.0003 9.23383 14.849 8.95274 14.5783C6.37891 12.0843 2.92965 9.29824 4.61175 5.25343C5.52124 3.06643 7.70444 1.66699 10.0005 1.66699C12.2965 1.66699 14.4797 3.06643 15.3892 5.25343C17.0692 9.29316 13.6283 12.0929 11.0482 14.5783Z" stroke="#707070" stroke-width="1.25"/>
        <path d="M15 16.667C15 17.5875 12.7614 18.3337 10 18.3337C7.23857 18.3337 5 17.5875 5 16.667" stroke="#707070" stroke-width="1.25" stroke-linecap="round"/>
    </svg>
    <a style="color: #707070" href="<?php echo esc_url( $city_name_link ); ?>" target="_blank" rel="noopener noreferrer">
        <?php echo esc_html( $city_name_output ); ?>
    </a>
</span>

                    </li>
                    <li>
                        <div class="bottom-icons">
                            <div>
                                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.5 6V18M18.5 4H6.5M18.5 20H6.5M4.5 18V6" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M22.5 4C22.5 5.10457 21.6046 6 20.5 6C19.3954 6 18.5 5.10457 18.5 4C18.5 2.89543 19.3954 2 20.5 2C21.6046 2 22.5 2.89543 22.5 4Z" stroke="#707070" stroke-width="1.5"></path>
                                    <path d="M6.5 4C6.5 5.10457 5.60457 6 4.5 6C3.39543 6 2.5 5.10457 2.5 4C2.5 2.89543 3.39543 2 4.5 2C5.60457 2 6.5 2.89543 6.5 4Z" stroke="#707070" stroke-width="1.5"></path>
                                    <path d="M22.5 20C22.5 21.1046 21.6046 22 20.5 22C19.3954 22 18.5 21.1046 18.5 20C18.5 18.8954 19.3954 18 20.5 18C21.6046 18 22.5 18.8954 22.5 20Z" stroke="#707070" stroke-width="1.5"></path>
                                    <path d="M6.5 20C6.5 21.1046 5.60457 22 4.5 22C3.39543 22 2.5 21.1046 2.5 20C2.5 18.8954 3.39543 18 4.5 18C5.60457 18 6.5 18.8954 6.5 20Z" stroke="#707070" stroke-width="1.5"></path>
                                </svg>
                                <span><?php echo esc_html( $area ); ?></span>
                            </div>
                            <div>
                                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.5 17.5H2.5" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M22.5 21V16C22.5 14.1144 22.5 13.1716 21.9142 12.5858C21.3284 12 20.3856 12 18.5 12H6.5C4.61438 12 3.67157 12 3.08579 12.5858C2.5 13.1716 2.5 14.1144 2.5 16V21" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M11.5 12V10.2134C11.5 9.83272 11.4428 9.70541 11.1497 9.55538C10.5395 9.24292 9.79865 9 9 9C8.20135 9 7.46055 9.24292 6.85025 9.55538C6.55721 9.70541 6.5 9.83272 6.5 10.2134V12" stroke="#707070" stroke-width="1.5" stroke-linecap="round"></path>
                                    <path d="M18.5 12V10.2134C18.5 9.83272 18.4428 9.70541 18.1497 9.55538C17.5395 9.24292 16.7987 9 16 9C15.2013 9 14.4605 9.24292 13.8503 9.55538C13.5572 9.70541 13.5 9.83272 13.5 10.2134V12" stroke="#707070" stroke-width="1.5" stroke-linecap="round"></path>
                                </svg>
                                <span><?php echo esc_html( $rooms ); ?></span>
                            </div>
                            <div>
                                <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.5 20L5.5 21M18.5 20L19.5 21" stroke="#707070" stroke-width="1.5" stroke-linecap="round"></path>
                                    <path d="M3.5 12V13C3.5 16.2998 3.5 17.9497 4.52513 18.9749C5.55025 20 7.20017 20 10.5 20H14.5C17.7998 20 19.4497 20 20.4749 18.9749C21.5 17.9497 21.5 16.2998 21.5 13V12" stroke="#707070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M2.5 12H22.5" stroke="#707070" stroke-width="1.5" stroke-linecap="round"></path>
                                    <path d="M4.5 12V5.5234C4.5 4.12977 5.62977 3 7.0234 3C8.14166 3 9.12654 3.73598 9.44339 4.80841L9.5 5" stroke="#707070" stroke-width="1.5" stroke-linecap="round"></path>
                                    <path d="M8.5 6L11 4" stroke="#707070" stroke-width="1.5" stroke-linecap="round"></path>
                                </svg>
                                <span><?php echo esc_html( $bathrooms ); ?></span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>
<?php
wp_reset_postdata();
?>
