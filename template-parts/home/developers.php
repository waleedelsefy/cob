<?php
$developers = get_terms([
    'taxonomy'   => 'developer',
    'hide_empty' => false,
    'number' => 9,

]);

?>

<div class="motaoron">
    <div class="container">
        <div class="top-motaoron">
            <div class="right-motaoron">
                <h3 class="head"><?php esc_html_e( 'Developers', 'cob_theme' ); ?></h3>
            </div>
            <a href="<?php
            $all_developers_link = get_term_link('developers');
            echo (!is_wp_error($all_developers_link)) ? esc_url($all_developers_link) : '#';
            ?>" class="motaoron-button"><?php esc_html__('View all', 'cob_theme'); ?>
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281171 7.68509C-0.0843415 7.31958 -0.0923715 6.73316 0.253053 6.3556L6.06104 0.535563C6.24982 0.346785 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535563C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z"
                        fill="black" />
                </svg>
            </a>
        </div>

        <div class="swiper swiper4">
            <div class="swiper-wrapper">
                <?php if (!empty($developers) && !is_wp_error($developers)) : ?>
                    <?php foreach ($developers as $developer) : ?>
                        <?php
                        if (!empty($developer) && is_object($developer)) {
                            $developer_link = get_term_link($developer);

                            // ✅ Validate `get_term_link()` output before using it
                            if (is_wp_error($developer_link)) {
                                $developer_link = '#';
                            }

                            $thumbnail_id   = absint(get_term_meta($developer->term_id, 'thumbnail_id', true));
                            $image_url      = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : get_template_directory_uri() . '/assets/imgs/developer-default.png';
                        } else {
                            $developer_link = '#'; // Fallback URL
                            $image_url      = get_template_directory_uri() . '/assets/imgs/developer-default.png';
                        }
                        ?>

                        <div class="swiper-slide">
                            <a href="<?php echo esc_url($developer_link); ?>" class="motaoron-img">
                                <img data-src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($developer->name); ?>" class="lazyload">
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p><?php esc_html_e( 'There are currently no Developers.', 'cob_theme' ); ?></p>

                <?php endif; ?>
            </div>

            <div class="swiper-button-prev">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M1.66602 6.00033H18.3327M1.66602 6.00033C1.66602 4.54158 5.82081 1.81601 6.87435 0.791992M1.66602 6.00033C1.66602 7.45908 5.82081 10.1847 6.87435 11.2087"
                        stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="swiper-button-next">
                <svg width="20" height="12" viewBox="0 0 20 12" fill="white" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M18.334 5.99967L1.66732 5.99967M18.334 5.99967C18.334 7.45842 14.1792 10.184 13.1257 11.208M18.334 5.99967C18.334 4.54092 14.1792 1.8153 13.1257 0.791341"
                        stroke="white" stroke-width="1.5625" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>