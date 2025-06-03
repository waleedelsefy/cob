<?php
global $wpdb;
$post_id = get_the_ID();

$city_name = esc_html__( 'Not known', 'cob_theme' );
$city_link = '';
$city_terms = get_the_terms( $post_id, 'city' );
if ( $city_terms && ! is_wp_error( $city_terms ) ) {
    $city_name = esc_html( $city_terms[0]->name );
    $city_link = get_term_link( $city_terms[0] );
}
$area             = !empty( get_post_meta( $post_id, 'area', true ) ) ? intval( get_post_meta( $post_id, 'area', true ) ) : ' - ';
$price            = !empty( get_post_meta( $post_id, 'price', true ) ) ? floatval( get_post_meta( $post_id, 'price', true ) ) : ' - ';
$max_price        = !empty( get_post_meta( $post_id, 'max_price', true ) ) ? floatval( get_post_meta( $post_id, 'max_price', true ) ) : ' - ';
$min_price        = !empty( get_post_meta( $post_id, 'min_price', true ) ) ? floatval( get_post_meta( $post_id, 'min_price', true ) ) : ' - ';
$rooms            = !empty( get_post_meta( $post_id, 'bedrooms', true ) ) ? intval( get_post_meta( $post_id, 'bedrooms', true ) ) : ' - ';
$bathrooms        = !empty( get_post_meta( $post_id, 'bathrooms', true ) ) ? intval( get_post_meta( $post_id, 'bathrooms', true ) ) : ' - ';
$delivery_year    = !empty( get_post_meta( $post_id, 'delivery', true ) ) ? get_post_meta( $post_id, 'delivery', true ) : ' - ';
$installments     = get_post_meta( $post_id, 'unit_installments', true );
$unit_down_payment = get_post_meta( $post_id, 'unit_down_payment', true );

$propertie_type = get_the_terms( $post_id, 'type' );

if ( $propertie_type && ! is_wp_error( $propertie_type ) ) {
    $propertie_type = reset( $propertie_type );
    $propertie_type_name = $propertie_type->name;
} else {
}
$finishing_type = get_the_terms( $post_id, 'finishing' );

if ( $finishing_type && ! is_wp_error( $finishing_type ) ) {
    $finishing_type = reset( $finishing_type );
    $finishing_type_name = $finishing_type->name;
} else {
}

?>
<div class="flat-details">
    <ul class="all-det">
        <li>
            <ul class="head-det">
                <?php
                $post_id = get_the_ID();
                $developer_image_url = get_template_directory_uri() . '/assets/imgs/card-devloper-default.png';
                $developers = get_the_terms( $post_id, 'developer' );
                if ( $developers && ! is_wp_error( $developers ) ) {
                    $developer = reset( $developers );
                    $developer_thumbnail_id = absint( get_term_meta( $developer->term_id, 'thumbnail_id', true ) );
                    if ( $developer_thumbnail_id ) {
                        $developer_image_url = wp_get_attachment_url( $developer_thumbnail_id );
                    }
                }
                ?>
                <li><img data-src="<?php echo $developer_image_url; ?>" alt="<?php the_title_attribute(); ?>"   class="lazyload">
                    <div class="falt-title">
                        <h6><?php esc_html_e(get_the_title());?></h6>
                        <?php
                        $project_parent = get_post_meta( get_the_ID(), 'project_parent', true );

                        $city_terms = get_the_terms( get_the_ID(), 'city' );

                        $child_term  = false;
                        $parent_term = false;

                        if ( $city_terms && ! is_wp_error( $city_terms ) ) {
                            foreach ( $city_terms as $term ) {
                                if ( $term->parent ) {
                                    $child_term  = $term;
                                    $parent_term = get_term( $term->parent, 'city' );
                                    break;
                                }
                            }
                            if ( ! $child_term ) {
                                $child_term = reset( $city_terms );
                            }
                        }

                        $output = [];

                        if ( ! empty( $project_parent ) ) {
                            $output[] = esc_html( $project_parent );
                        }

                        if ( $child_term && ! empty( $child_term->name ) ) {
                            $output[] = esc_html( $child_term->name );
                        }

                        if ( $parent_term && ! empty( $parent_term->name ) ) {
                            $output[] = esc_html( $parent_term->name );
                        }

                        if ( ! empty( $output ) ) {
                            echo '<span>' . implode( ' , ', $output ) . '</span>';
                        }
                        ?>
                    </div>
                </li>
                <li>
                    <?php get_template_part('template-parts/button-container'); ?>
                </li>
            </ul>
        </li>
        <li>
            <div class="land-price">
                <h6><?php esc_html_e( ' Factory price ', 'cob_theme' ); ?></h6>
                <p><?php echo $price; ?><span> <?php echo esc_html_e( 'L.E.', 'cob_theme' ); ?></span></p>

            </div>
        </li>
        <li>
            <div class="table-container">
                <table>
                    <tr>
                        <th><span><svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M16.8333 5.00065V15.0007M15.1667 3.33398H5.16667M15.1667 16.6673H5.16667M3.5 15.0007V5.00065"
                                                stroke="#707070" stroke-width="1.25" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M18.4993 3.33366C18.4993 4.25413 17.7532 5.00033 16.8327 5.00033C15.9122 5.00033 15.166 4.25413 15.166 3.33366C15.166 2.41318 15.9122 1.66699 16.8327 1.66699C17.7532 1.66699 18.4993 2.41318 18.4993 3.33366Z"
                                                stroke="#707070" stroke-width="1.25" />
                                            <path
                                                d="M5.16536 3.33268C5.16536 4.25316 4.41917 4.99935 3.4987 4.99935C2.57822 4.99935 1.83203 4.25316 1.83203 3.33268C1.83203 2.41221 2.57822 1.66602 3.4987 1.66602C4.41917 1.66602 5.16536 2.41221 5.16536 3.33268Z"
                                                stroke="#707070" stroke-width="1.25" />
                                            <path
                                                d="M18.4993 16.6667C18.4993 17.5872 17.7532 18.3333 16.8327 18.3333C15.9122 18.3333 15.166 17.5872 15.166 16.6667C15.166 15.7462 15.9122 15 16.8327 15C17.7532 15 18.4993 15.7462 18.4993 16.6667Z"
                                                stroke="#707070" stroke-width="1.25" />
                                            <path
                                                d="M5.16536 16.6667C5.16536 17.5872 4.41917 18.3333 3.4987 18.3333C2.57822 18.3333 1.83203 17.5872 1.83203 16.6667C1.83203 15.7462 2.57822 15 3.4987 15C4.41917 15 5.16536 15.7462 5.16536 16.6667Z"
                                                stroke="#707070" stroke-width="1.25" />
                                        </svg><?php echo $area . ' ' . esc_html_e( ' Meter ', 'cob_theme' ); ?></span>
                        </th>

                        <th><span><svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.66634 16.667L4.83301 17.5003M15.6663 16.667L16.4997 17.5003"
                                                  stroke="#707070" stroke-width="1.25" stroke-linecap="round" />
                                            <path
                                                d="M3.16699 10V10.8333C3.16699 13.5832 3.16699 14.9581 4.02127 15.8124C4.87553 16.6667 6.25047 16.6667 9.00033 16.6667H12.3337C15.0835 16.6667 16.4584 16.6667 17.3127 15.8124C18.167 14.9581 18.167 13.5832 18.167 10.8333V10"
                                                stroke="#707070" stroke-width="1.25" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M2.33301 10H18.9997" stroke="#707070" stroke-width="1.25"
                                                  stroke-linecap="round" />
                                            <path
                                                d="M3.99902 10V4.60283C3.99902 3.44147 4.9405 2.5 6.10186 2.5C7.03374 2.5 7.85447 3.11332 8.11851 4.00701L8.16569 4.16667"
                                                stroke="#707070" stroke-width="1.25" stroke-linecap="round" />
                                            <path d="M7.33301 5.00065L9.41634 3.33398" stroke="#707070"
                                                  stroke-width="1.25" stroke-linecap="round" />
                                        </svg><?php echo $bathrooms . ' ' . esc_html_e( ' Bathrooms ', 'cob_theme' ); ?>
                                    </span> </th>

                    </tr>
                    <tr>

                        <td><span><?php echo $post_id .  esc_html_e( 'Reference number ', 'cob_theme' ); ?></span></td>
                        <td><span><svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                       xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M13.5843 12.084C16.3457 12.084 18.5843 9.8454 18.5843 7.08398C18.5843 4.32256 16.3457 2.08398 13.5843 2.08398C10.8229 2.08398 8.58431 4.32256 8.58431 7.08398C8.58431 7.81766 8.74233 8.5144 9.02623 9.14207L2.75098 15.4173V17.9173H5.25098V16.2507H6.91764V14.584H8.58431L11.5262 11.6421C12.1539 11.926 12.8506 12.084 13.5843 12.084Z"
                                                stroke="#707070" stroke-width="1.25" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path d="M15.2503 5.41699L14.417 6.25033" stroke="#707070"
                                                  stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg><?php echo $delivery_year .  esc_html_e( 'Delivery in ', 'cob_theme' ); ?></span>
                                    </span></td>

                    </tr>
                </table>
            </div>

        </li>
    </ul>
</div>