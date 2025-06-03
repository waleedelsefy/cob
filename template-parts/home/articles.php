<?php
$theme_dir = get_template_directory_uri();
?>

<div class="articles">
    <div class="container">
        <div class="top-articles">
            <div class="right-articles">
                <h3 class="head"><?php echo esc_html__('Articles.', 'cob_theme'); ?></h3>
                <h5><?php echo esc_html__('Explore a selection of the latest real estate projects available with us.', 'cob_theme'); ?></h5>
            </div>
            <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="articles-button">
                <?php esc_html__('View all', 'cob_theme'); ?>
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281171 7.68509C-0.0843415 7.31958 -0.0923715 6.73316 0.253053 6.3556L6.06104 0.535563C6.24982 0.346785 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535563C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="black" />
                </svg>
            </a>
        </div>

        <div class="swiper swiper6">
            <div class="swiper-wrapper">
                <?php
                $args = [
                    'post_type'      => 'post',
                    'posts_per_page' => 8,
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                ];
                $query = new WP_Query($args);

                if ($query->have_posts()) :
                    while ($query->have_posts()) : $query->the_post();
                        ?>
                        <div class="swiper-slide">
                            <a href="<?php the_permalink(); ?>" class="articles-card">
                                <div class="img-container">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <img data-src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>" class="lazyload">
                                    <?php else : ?>
                                        <img data-src="<?php echo esc_url($theme_dir . '/assets/imgs/default-article.png'); ?>" alt="article Image" class="lazyload">
                                    <?php endif; ?>
                                </div>
                                <div class="articles-info">
                                    <div class="date">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M9.16699 10.833H13.3337M6.66699 10.833H6.67448M10.8337 14.1663H6.66699M13.3337 14.1663H13.3262" stroke="#707070" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M15 1.66699V3.33366M5 1.66699V3.33366" stroke="#707070" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M2.08301 10.2027C2.08301 6.57162 2.08301 4.75607 3.12644 3.62803C4.16987 2.5 5.84925 2.5 9.20801 2.5H10.7913C14.1501 2.5 15.8295 2.5 16.8729 3.62803C17.9163 4.75607 17.9163 6.57162 17.9163 10.2027V10.6307C17.9163 14.2617 17.9163 16.0773 16.8729 17.2053C15.8295 18.3333 14.1501 18.3333 10.7913 18.3333H9.20801C5.84925 18.3333 4.16987 18.3333 3.12644 17.2053C2.08301 16.0773 2.08301 14.2617 2.08301 10.6307V10.2027Z" stroke="#707070" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M2.5 6.66699H17.5" stroke="#707070" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

                                        <?php echo get_the_date('F j, Y'); ?>
                                    </div>
                                    <div class="des">
                                        <p><?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    echo '<p>' . esc_html__('There are no new articles currently.', 'cob_theme') . '</p>';
                endif;
                ?>
            </div>

            <!-- Custom navigation buttons -->
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
