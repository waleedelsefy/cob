<?php
// Global WordPress database variable and get the current post ID
global $wpdb;
$post_id = get_the_ID();

// Get the first 'city' term (if available) and its link; otherwise, use default text
$city_name = esc_html__( 'Not known', 'cob_theme' );
$city_link = '';
$city_terms = get_the_terms( $post_id, 'city' );
if ( $city_terms && ! is_wp_error( $city_terms ) ) {
	$city_name = esc_html( $city_terms[0]->name );
	$city_link = get_term_link( $city_terms[0] );
}

// Since all values are now stored in post meta, the custom table query is removed

function format_number($value, $decimal = 0) {
    return number_format((float) $value, $decimal, '.', ',');
}

$area             = get_post_meta( $post_id, 'area', true );
$price            = get_post_meta( $post_id, 'price', true );
$max_price        = get_post_meta( $post_id, 'max_price', true );
$min_price        = get_post_meta( $post_id, 'min_price', true );
$rooms            = get_post_meta( $post_id, 'bedrooms', true );
$bathrooms        = get_post_meta( $post_id, 'bathrooms', true );
$delivery_year    = get_post_meta( $post_id, 'delivery', true );

// Retrieve various meta values using get_post_meta()
// Convert values to int or float when needed; if empty, display a dash (' - ')
$installments     = get_post_meta( $post_id, 'unit_installments', true );
$unit_down_payment = get_post_meta( $post_id, 'unit_down_payment', true );

// Get the 'type' taxonomy term for unit type and set its name; if not found, set a default value
$unit_type = get_the_terms( $post_id, 'type' );
if ( $unit_type && ! is_wp_error( $unit_type ) ) {
	$unit_type = reset( $unit_type );
	$unit_type_name = $unit_type->name;
} else {
	$unit_type_name = ' - ';
}

// Get the 'finishing' taxonomy term for finishing type and set its name; if not found, set a default value
$finishing_type = get_the_terms( $post_id, 'finishing' );
if ( $finishing_type && ! is_wp_error( $finishing_type ) ) {
	$finishing_type = reset( $finishing_type );
	$finishing_type_name = $finishing_type->name;
} else {
	$finishing_type_name = ' - ';
}
?>

<div class="flat-details">
    <ul class="all-det">
        <li>
            <ul class="head-det">
				<?php
				// Get developer information and image URL (fallback to a default image if none found)
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
                <li>
                    <img data-src="<?php echo $developer_image_url; ?>" alt="<?php the_title_attribute(); ?>" class="lazyload">
                    <div class="falt-title">
                        <h6><?php esc_html_e( get_the_title() ); ?></h6>
						<?php
						// Get the 'compound' taxonomy term and its name
						$compound_terms = get_the_terms( get_the_ID(), 'compound' );
						$compound_name = '';
						if ( $compound_terms && ! is_wp_error( $compound_terms ) ) {
							$compound_term = reset( $compound_terms );
							$compound_name = $compound_term->name;
						}

						// Get the 'city' taxonomy term and its name
						$city_terms = get_the_terms( get_the_ID(), 'city' );
						$city_name_output = '';
						if ( $city_terms && ! is_wp_error( $city_terms ) ) {
							$city_term = reset( $city_terms );
							$city_name_output = $city_term->name;
						}

						// Build output string: compound name followed by city name separated by a comma
						$output = array();
						if ( ! empty( $compound_name ) ) {
							$output[] = esc_html( $compound_name );
						}
						if ( ! empty( $city_name_output ) ) {
							$output[] = esc_html( $city_name_output );
						}
						if ( ! empty( $output ) ) {
							echo '<span>' . implode( ' , ', $output ) . '</span>';
						}
						?>
                    </div>
                </li>
                <li>
					<?php get_template_part( 'template-parts/button-container' ); ?>
                </li>
            </ul>
        </li>
        <li>
            <ul class="flat-price">
                <li>
                    <h6><?php esc_html_e( ' Prices start ', 'cob_theme' ); ?></h6>
                </li>
                <li>
                    <ul>
                        <li>
                            <p><?php esc_html_e( ' From ', 'cob_theme' ); ?></p>
                            <span><?php echo format_number($max_price, 0) . esc_html_e( 'L.E.', 'cob_theme' ); ?></span>
                        </li>
                        <li>
                            <svg width="40" height="36" viewBox="0 0 40 36" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <rect width="40" height="36" rx="18" fill="white" />
                                <rect x="0.5" y="0.5" width="39" height="35" rx="17.5" stroke="#EC3C43" stroke-opacity="0.2" />
                                <path d="M27 15H14.6586C13.6528 15 13.1499 15 13.0247 14.6913C12.8996 14.3827 13.2552 14.0194 13.9664 13.2929L16.2109 11"
                                      stroke="#EC3C43" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M13 21H25.3414C26.3472 21 26.8501 21 26.9753 21.3087C27.1004 21.6173 26.7448 21.9806 26.0336 22.7071L23.7891 25"
                                      stroke="#EC3C43" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </li>
                        <li>
                            <p><?php esc_html_e( ' To ', 'cob_theme' ); ?></p>
                            <span><?php echo format_number($min_price, 0) . esc_html_e( 'L.E.', 'cob_theme' ); ?></span>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        <li>
            <!-- Display the unit type name -->
            <h6><?php echo esc_html( $unit_type_name ); ?></h6>
            <div class="table-container">
                <table>
                    <tr>
                        <th>
                            <span>
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.8333 5.00065V15.0007M15.1667 3.33398H5.16667M15.1667 16.6673H5.16667M3.5 15.0007V5.00065"
                                          stroke="#707070" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M18.4993 3.33366C18.4993 4.25413 17.7532 5.00033 16.8327 5.00033C15.9122 5.00033 15.166 4.25413 15.166 3.33366C15.166 2.41318 15.9122 1.66699 16.8327 1.66699C17.7532 1.66699 18.4993 2.41318 18.4993 3.33366Z"
                                          stroke="#707070" stroke-width="1.25" />
                                </svg>
                                <?php echo $area . ' ' . esc_html_e( ' Meter ', 'cob_theme' ); ?>
                            </span>
                        </th>
                        <th>
                            <span>
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.8327 14.584H2.16602" stroke="#707070" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M18.8327 17.5V13.3333C18.8327 11.762 18.8327 10.9763 18.3445 10.4882C17.8563 10 17.0707 10 15.4993 10H5.49935C3.928 10 3.14232 10 2.65417 10.4882C2.16602 10.9763 2.16602 11.762 2.16602 13.3333V17.5"
                                          stroke="#707070" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9.66667 10V8.51117C9.66667 8.19393 9.619 8.08784 9.37475 7.96282C8.86625 7.70243 8.24888 7.5 7.58333 7.5C6.91779 7.5 6.30046 7.70243 5.79187 7.96282C5.54767 8.08784 5.5 8.19393 5.5 8.51117V10"
                                          stroke="#707070" stroke-width="1.25" stroke-linecap="round" />
                                    <path d="M15.4987 10V8.51117C15.4987 8.19393 15.451 8.08784 15.2068 7.96282C14.6983 7.70243 14.0809 7.5 13.4154 7.5C12.7498 7.5 12.1324 7.70243 11.6239 7.96282C11.3797 8.08784 11.332 8.19393 11.332 8.51117V10"
                                          stroke="#707070" stroke-width="1.25" stroke-linecap="round" />
                                </svg>
                                <?php echo $rooms . ' ' . esc_html_e( ' Rooms ', 'cob_theme' ); ?>
                            </span>
                        </th>
                        <th>
                            <span>
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.66634 16.667L4.83301 17.5003M15.6663 16.667L16.4997 17.5003"
                                          stroke="#707070" stroke-width="1.25" stroke-linecap="round" />
                                    <path d="M3.16699 10V10.8333C3.16699 13.5832 3.16699 14.9581 4.02127 15.8124C4.87553 16.6667 6.25047 16.6667 9.00033 16.6667H12.3337C15.0835 16.6667 16.4584 16.6667 17.3127 15.8124C18.167 14.9581 18.167 13.5832 18.167 10.8333V10"
                                          stroke="#707070" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M2.33301 10H18.9997" stroke="#707070" stroke-width="1.25" stroke-linecap="round" />
                                    <path d="M3.99902 10V4.60283C3.99902 3.44147 4.9405 2.5 6.10186 2.5C7.03374 2.5 7.85447 3.11332 8.11851 4.00701L8.16569 4.16667"
                                          stroke="#707070" stroke-width="1.25" stroke-linecap="round" />
                                    <path d="M7.33301 5.00065L9.41634 3.33398" stroke="#707070" stroke-width="1.25" stroke-linecap="round" />
                                </svg>
                                <?php echo $bathrooms . ' ' . esc_html_e( ' Bathrooms ', 'cob_theme' ); ?>
                            </span>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <span>
                                <svg width="17" height="18" viewBox="0 0 17 18" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.24007 2.96902L3.40673 3.61948C2.14285 4.606 1.51091 5.09927 1.17196 5.79477C0.833008 6.49026 0.833008 7.2937 0.833008 8.90057V10.6432C0.833008 13.7972 0.833008 15.3741 1.80932 16.3539C2.78563 17.3337 4.35697 17.3337 7.49967 17.3337H9.16634C12.309 17.3337 13.8804 17.3337 14.8567 16.3539C15.833 15.3741 15.833 13.7972 15.833 10.6432V8.90057C15.833 7.2937 15.833 6.49026 15.4941 5.79477C15.1551 5.09927 14.5232 4.606 13.2593 3.61948L12.4259 2.96902C10.4598 1.43433 9.47667 0.666992 8.33301 0.666992C7.18934 0.666992 6.20623 1.43433 4.24007 2.96902Z"
                                          stroke="#707070" stroke-width="1.25" stroke-linejoin="round" />
                                </svg>
                                <?php echo esc_html( $finishing_type_name ); ?>
                            </span>
                        </td>
                        <td>
                            <span><?php echo $post_id . esc_html_e( 'Reference number ', 'cob_theme' ); ?></span>
                        </td>
                        <td>
                            <span>
                                <svg width="21" height="20" viewBox="0 0 21 20" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.5843 12.084C16.3457 12.084 18.5843 9.8454 18.5843 7.08398C18.5843 4.32256 16.3457 2.08398 13.5843 2.08398C10.8229 2.08398 8.58431 4.32256 8.58431 7.08398C8.58431 7.81766 8.74233 8.5144 9.02623 9.14207L2.75098 15.4173V17.9173H5.25098V16.2507H6.91764V14.584H8.58431L11.5262 11.6421C12.1539 11.926 12.8506 12.084 13.5843 12.084Z"
                                          stroke="#707070" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M15.2503 5.41699L14.417 6.25033" stroke="#707070" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <?php echo $delivery_year . esc_html_e( 'Delivery in ', 'cob_theme' ); ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </li>
    </ul>
</div>
