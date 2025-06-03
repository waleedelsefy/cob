<?php
/**
 * Template Name: Latest Projects
 * Description: A custom page template to display parent cities (with attached child cities) in tabs with pagination.
 */

// Get theme directory URI.
$theme_dir = get_template_directory_uri();

// Setup pagination.
$paged  = ( get_query_var('paged') ) ? absint( get_query_var('paged') ) : 1;
$number = 9; // Number of parent cities per page.
$offset = ( $paged - 1 ) * $number;

// Get total number of parent cities.
$all_city_ids = get_terms( array(
	'taxonomy'   => 'city',
	'hide_empty' => false,
	'fields'     => 'ids',
	'parent'     => 0, // Only parent cities.
) );
$total_cities = ( is_array( $all_city_ids ) ) ? count( $all_city_ids ) : 0;
$total_pages  = ceil( $total_cities / $number );

// Get parent cities for current page.
$cities = get_terms( array(
	'taxonomy'   => 'city',
	'hide_empty' => false,
	'number'     => $number,
	'offset'     => $offset,
	'parent'     => 0, // Only parent cities.
) );
?>

<div class="tabs-sec">
    <div class="container">
        <!-- Tabs -->
        <div class="tabs">
            <div class="select-tabs">
            </div>
        </div>

        <!-- Tab Panels -->
        <div class="tab-content">
            <!-- Total Tab Panel (dynamic content) -->
            <div id="total" class="tab-panel active">
                <div class="grid">
					<?php if ( ! empty( $cities ) && ! is_wp_error( $cities ) ) : ?>
						<?php foreach ( $cities as $city ) :
							$city_link = get_term_link( $city );
							if ( is_wp_error( $city_link ) ) {
								$city_link = '#';
							}
							$city_name = $city->name;

							// Get city image.
							$thumbnail_id   = absint( get_term_meta( $city->term_id, 'thumbnail_id', true ) );
							$city_image_url = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : $theme_dir . '/assets/imgs/default-city.png';


							$compound_query = get_terms([
								'taxonomy'   => 'compound',
								'hide_empty' => false,
								'meta_query' => [
									[
										'key'     => 'compound_city',
										'value'   => '"' . $city->term_id . '"',
										'compare' => 'LIKE'
									]
								]
							]);
							$compound_count = count($compound_query);


							// Query for properties in this city, including child cities.
							$property_query = new WP_Query( array(
								'post_type'      => 'properties',
								'tax_query'      => array(
									array(
										'taxonomy'         => 'city',
										'field'            => 'term_id',
										'terms'            => $city->term_id,
										'include_children' => true, // Include child cities.
									),
								),
								'posts_per_page' => -1,
								'fields'         => 'ids',
							) );
							$property_count = $property_query->found_posts;
							?>
                            <a href="<?php echo esc_url( $city_link ); ?>" class="card">
                                <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.0555 18.1727V10.9445M16.0555 10.9445H8.82735M16.0555 10.9445L5.36827 21.6318M11.2552 24.78C14.8933 25.5005 18.8123 24.4511 21.6318 21.6318C26.1228 17.1407 26.1228 9.85932 21.6318 5.36828C17.1406 0.87724 9.85929 0.87724 5.36827 5.36828C2.54889 8.18766 1.49947 12.1067 2.21998 15.7448" stroke="#EC3C43" stroke-width="2.42105" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p><?php echo esc_html( $compound_count ) . ' ' . __( 'Compounds', 'cob_theme' ); ?></p>
                                <span><?php echo esc_html( $property_count ) . ' ' . __( 'Properties', 'cob_theme' ); ?></span>
                                <button><?php echo esc_html( $city_name ); ?></button>
                            </a>
						<?php endforeach; ?>
					<?php else : ?>
                        <p><?php esc_html_e( 'No cities found.', 'cob_theme' ); ?></p>
					<?php endif; ?>
                </div>
            </div>

            <!-- Additional tab panels (static or dynamic content) -->
            <div id="october" class="tab-panel">
                <div class="grid">
                    <p><?php esc_html_e('Content for 6 October', 'cob_theme'); ?></p>
                </div>
            </div>
            <div id="zayed" class="tab-panel">
                <div class="grid">
                    <p><?php esc_html_e('Content for Sheikh Zayed', 'cob_theme'); ?></p>
                </div>
            </div>
            <div id="mokttam" class="tab-panel">
                <div class="grid">
                    <p><?php esc_html_e('Content for Mokattam', 'cob_theme'); ?></p>
                </div>
            </div>
            <div id="nasr" class="tab-panel">
                <div class="grid">
                    <p><?php esc_html_e('Content for Nasr City', 'cob_theme'); ?></p>
                </div>
            </div>
            <div id="helepolis" class="tab-panel">
                <div class="grid">
                    <p><?php esc_html_e('Content for New Heliopolis', 'cob_theme'); ?></p>
                </div>
            </div>
            <div id="sahel" class="tab-panel">
                <div class="grid">
                    <p><?php esc_html_e('Content for North Coast', 'cob_theme'); ?></p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination">
			<?php
			$big = 999999999;
			$pagination_links = paginate_links( array(
				'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'    => '?paged=%#%',
				'current'   => $paged,
				'total'     => $total_pages,
				'prev_text' => '<svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.10212 6.99773L0.997038 1.89666C0.619477 1.5191 0.619477 0.908582 0.997038 0.53504C1.3746 0.161498 1.98512 0.161497 2.36268 0.53504L8.14656 6.31491C8.51208 6.68042 8.52011 7.26684 8.17468 7.6444L2.36669 13.4644C2.17791 13.6532 1.92888 13.7456 1.68387 13.7456C1.43886 13.7456 1.18983 13.6532 1.00105 13.4644C0.623496 13.0869 0.623496 12.4764 1.00105 12.1028L6.10212 6.99773Z" fill="black" />
                                </svg> ' . esc_html__( 'Previous', 'cob_theme' ),
				'next_text' => esc_html__( 'Next', 'cob_theme' ) . ' <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281172 7.68509C-0.084341 7.31958 -0.0923709 6.73316 0.253054 6.3556L6.06104 0.535562C6.24982 0.346784 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535562C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="black" />
                                </svg>',
				'type'      => 'list',
				'end_size'  => 1,
				'mid_size'  => 2,
			) );
			if ( $pagination_links ) {
				echo $pagination_links;
			}
			?>
        </div>
    </div>
</div>
