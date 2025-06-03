<div class="areas">
    <div class="container">
        <div class="top-areas">
            <div class="right-areas">
                <h3 class="head"><?php esc_html_e('Top Areas', 'cob_theme'); ?></h3>
                <h5><?php esc_html_e('Search by area', 'cob_theme'); ?></h5>
            </div>
            <a class="areas-button" href="<?php echo esc_url('/areas/'); ?>">
                <?php esc_html_e('View all', 'cob_theme'); ?>
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281171 7.68509C-0.0843415 7.31958 -0.0923715 6.73316 0.253053 6.3556L6.06104 0.535563C6.24982 0.346785 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535563C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="black" />
                </svg>
            </a>
        </div>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php
                $cities = get_terms([
                    'taxonomy'   => 'city',
                    'hide_empty' => false,
                    'number'     => 6,
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                ]);

                if (!empty($cities) && !is_wp_error($cities)) :
                    foreach ($cities as $city) :
                        $city_link = get_term_link($city);
                        $city_name = $city->name;
                        if (is_wp_error($city_link)) {
                            $city_link = '#';
                        }

                        $thumbnail_id = absint(get_term_meta($city->term_id, 'thumbnail_id', true));
                        $city_image_url = $thumbnail_id
                            ? wp_get_attachment_url($thumbnail_id)
                            : get_template_directory_uri() . '/assets/imgs/default.jpg';

                        $compounds = get_terms([
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
                        $compound_count = count($compounds);

                        $property_query = new WP_Query([
                            'post_type'      => 'properties',
                            'tax_query'      => [
                                [
                                    'taxonomy' => 'city',
                                    'field'    => 'term_id',
                                    'terms'    => $city->term_id,
                                ],
                            ],
                            'posts_per_page' => -1,
                            'fields'         => 'ids',
                        ]);
                        $property_count = $property_query->found_posts;
                        ?>

                        <div class="swiper-slide">
                            <a href="<?php echo esc_url($city_link); ?>" class="card">
                                <svg width="27" height="27" viewBox="0 0 27 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M16.0555 18.1727V10.9445M16.0555 10.9445H8.82735M16.0555 10.9445L5.36827 21.6318M11.2552 24.78C14.8933 25.5005 18.8123 24.4511 21.6318 21.6318C26.1228 17.1407 26.1228 9.85932 21.6318 5.36828C17.1406 0.87724 9.85929 0.87724 5.36827 5.36828C2.54889 8.18766 1.49947 12.1067 2.21998 15.7448" stroke="#EC3C43" stroke-width="2.42105" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p><?php echo esc_html($compound_count) . ' ' . __('Compounds', 'cob_theme'); ?></p>
                                <span><?php echo esc_html($property_count) . ' ' . __('Properties', 'cob_theme'); ?></span>
                                <button><?php echo esc_html($city_name); ?></button>
                            </a>
                        </div>

                        <?php
                        wp_reset_postdata();
                    endforeach;
                else :
                    echo '<p>' . esc_html_e('There are currently no services.', 'cob_theme') . '</p>';
                endif;
                ?>
            </div>

            <div class="swiper-button-prev">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.66602 6.00033H18.3327M1.66602 6.00033C1.66602 4.54158 5.82081 1.81601 6.87435 0.791992M1.66602 6.00033C1.66602 7.45908 5.82081 10.1847 6.87435 11.2087" stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="swiper-button-next">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="#fff" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18.334 5.99967L1.66732 5.99967M18.334 5.99967C18.334 7.45842 14.1792 10.184 13.1257 11.208M18.334 5.99967C18.334 4.54092 14.1792 1.8153 13.1257 0.791341" stroke="#fff" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>