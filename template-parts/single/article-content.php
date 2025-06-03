<div class="article-content">
    <div class="container">
        <div class="article-text">
            <?php the_content() ?>
        </div>
        <div class="sidebar">
        <div class="side-section">
            <div class="side-container">
                <div class="title"><?php esc_html_e( 'Latest Posts', 'cob_theme' ); ?></div>
                <div class="underline"></div>
                <ul class="items">
                    <?php
                    $latest_posts = new WP_Query( array(
                        'post_type'      => 'post',
                        'posts_per_page' => 5,
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    ) );

                    if ( $latest_posts->have_posts() ) :
                        while ( $latest_posts->have_posts() ) : $latest_posts->the_post();
                            ?>
                            <li class="item">
                                <a href="<?php the_permalink(); ?>">
                                    <div class="dot">
                                        <div class="dot-inner"></div>
                                    </div>
                                    <?php
                                    if ( has_post_thumbnail() ) {
                                        $thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
                                        ?>
                                        <img data-src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" class="lazyload">
                                        <?php
                                    } else {
                                        echo '<img data-src="' . esc_url( get_template_directory_uri() . '/assets/imgs/default.png' ) . '" alt="' . esc_attr( get_the_title() ) . '" class="lazyload">';
                                    }
                                    ?>
                                    <span><?php the_title(); ?></span>
                                </a>
                            </li>
                        <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<li class="item">' . esc_html__( 'No posts found.', 'cob_theme' ) . '</li>';
                    endif;
                    ?>
                </ul>
            </div>

            <!-- Social Icons -->
            <h3>تابعونا على</h3>
            <div class="underline"></div>
            <div class="social-links">

                <div class="icon-holder"><a href="#"><svg width="16" height="22" viewBox="0 0 16 22" fill="none"
                                                          xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M2.18121 9.48174C1.20345 9.48174 0.99939 9.67364 0.99939 10.5928V12.2595C0.99939 13.1788 1.20345 13.3706 2.18121 13.3706H4.54484V20.0373C4.54484 20.9565 4.7489 21.1484 5.72666 21.1484H8.09029C9.06809 21.1484 9.27209 20.9565 9.27209 20.0373V13.3706H11.9261C12.6677 13.3706 12.8588 13.2351 13.0625 12.5648L13.569 10.8981C13.9179 9.74984 13.7029 9.48174 12.4326 9.48174H9.27209V6.704C9.27209 6.09035 9.80119 5.59288 10.4539 5.59288H13.8176C14.7953 5.59288 14.9994 5.40103 14.9994 4.48177V2.25955C14.9994 1.34029 14.7953 1.14844 13.8176 1.14844H10.4539C7.19039 1.14844 4.54484 3.63575 4.54484 6.704V9.48174H2.18121Z"
                                  stroke="white" stroke-width="1.5" stroke-linejoin="round" />
                        </svg>


                    </a></div>
                <div class="icon-holder"><a href="#"><svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                                          xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M3.13416 13.0002C3.13416 8.34957 3.13416 6.02427 4.5789 4.57951C6.02366 3.13477 8.34896 3.13477 12.9995 3.13477C17.6501 3.13477 19.9754 3.13477 21.4202 4.57951C22.8649 6.02427 22.8649 8.34957 22.8649 13.0002C22.8649 17.6507 22.8649 19.976 21.4202 21.4208C19.9754 22.8655 17.6501 22.8655 12.9995 22.8655C8.34896 22.8655 6.02366 22.8655 4.5789 21.4208C3.13416 19.976 3.13416 17.6507 3.13416 13.0002Z"
                                stroke="white" stroke-width="1.38462" stroke-linejoin="round" />
                            <path
                                d="M17.6737 12.9992C17.6737 15.5801 15.5815 17.6723 13.0006 17.6723C10.4197 17.6723 8.32751 15.5801 8.32751 12.9992C8.32751 10.4184 10.4197 8.32617 13.0006 8.32617C15.5815 8.32617 17.6737 10.4184 17.6737 12.9992Z"
                                stroke="white" stroke-width="1.55769" />
                            <path d="M18.7196 7.28809H18.7103" stroke="white" stroke-width="2.07692"
                                  stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                    </a></div>
                <div class="icon-holder"><a href="#"><svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                                          xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5.99939 20L11.8704 14.1291M11.8704 14.1291L5.99939 6H9.88829L14.1285 11.8709M11.8704 14.1291L16.1105 20H19.9994L14.1285 11.8709M19.9994 6L14.1285 11.8709"
                                stroke="white" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                    </a></div>
                <div class="icon-holder"><a href="#"><svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                                          xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.4601 21.1122C14.189 21.1122 16.4013 18.8999 16.4013 16.171V9.78485C17.2253 10.4454 18.2115 10.9121 19.2906 11.1155C19.5792 11.17 19.7235 11.1972 19.9014 11.1512C20.1129 11.0965 20.3479 10.9017 20.4408 10.7041C20.5189 10.5377 20.5189 10.3566 20.5189 9.99454C20.5189 9.61685 20.5189 9.428 20.4777 9.30264C20.4095 9.09517 20.3503 9.01495 20.1722 8.88849C20.0646 8.81209 19.8103 8.73352 19.3017 8.57639C18.0065 8.17618 16.9843 7.15402 16.5841 5.85876C16.427 5.35021 16.3484 5.09593 16.2721 4.98834C16.1456 4.81025 16.0654 4.75108 15.8579 4.68284C15.7325 4.6416 15.5437 4.6416 15.166 4.6416C14.7823 4.6416 14.5904 4.6416 14.4391 4.70429C14.2373 4.78787 14.0769 4.94819 13.9934 5.14998C13.9307 5.30132 13.9307 5.49318 13.9307 5.8769V16.171C13.9307 17.5355 12.8246 18.6416 11.4601 18.6416C10.0956 18.6416 8.98951 17.5355 8.98951 16.171C8.98951 15.2358 9.50916 14.4219 10.2755 14.0024C10.8446 13.6909 11.1292 13.5351 11.2089 13.4581C11.3659 13.3063 11.3731 13.2943 11.4308 13.0836C11.4601 12.9767 11.4601 12.8062 11.4601 12.4651C11.4601 12.1149 11.4601 11.9398 11.3763 11.7685C11.2803 11.5723 11.0186 11.3689 10.8048 11.3243C10.6182 11.2853 10.4912 11.3177 10.2373 11.3823C8.0998 11.9265 6.51892 13.8641 6.51892 16.171C6.51892 18.8999 8.73116 21.1122 11.4601 21.1122Z"
                                stroke="white" stroke-width="1.23529" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                    </a></div>
                <div class="icon-holder"><a href="#"><svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                                          xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.99939 10.2002V19.9993" stroke="white" stroke-width="2.0998"
                                  stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M11.601 14.3998V19.9993M11.601 14.3998C11.601 12.0804 13.4811 10.2002 15.8005 10.2002C18.12 10.2002 20.0001 12.0804 20.0001 14.3998V19.9993M11.601 14.3998V10.2002"
                                stroke="white" stroke-width="2.0998" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M6.0121 6.00098H5.99951" stroke="white" stroke-width="2.79973"
                                  stroke-linecap="round" stroke-linejoin="round" />
                        </svg>

                    </a></div>
                <div class="icon-holder"><a href="#"><svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                                          xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12.9992 21.8267C14.8785 21.8267 16.6807 21.641 18.3508 21.3006C20.437 20.8754 21.4801 20.6627 22.432 19.4389C23.3839 18.215 23.3839 16.8102 23.3839 14.0003V11.9992C23.3839 9.18938 23.3839 7.78449 22.432 6.56066C21.4801 5.33684 20.437 5.12422 18.3508 4.69896C16.6807 4.3585 14.8785 4.17285 12.9992 4.17285C11.1199 4.17285 9.31778 4.3585 7.64759 4.69896C5.56142 5.12422 4.51834 5.33684 3.56648 6.56066C2.61462 7.78449 2.61462 9.18938 2.61462 11.9992V14.0003C2.61462 16.8102 2.61462 18.215 3.56648 19.4389C4.51834 20.6627 5.56142 20.8754 7.64759 21.3006C9.31778 21.641 11.1199 21.8267 12.9992 21.8267Z"
                                stroke="white" stroke-width="1.03846" />
                            <path
                                d="M17.114 13.3248C16.9599 13.9539 16.14 14.4057 14.5 15.3095C12.7162 16.2923 11.8244 16.7838 11.1021 16.5945C10.8574 16.5303 10.6321 16.4175 10.4428 16.2646C9.88415 15.8129 9.88416 14.8752 9.88416 12.9998C9.88416 11.1245 9.88415 10.1868 10.4428 9.73511C10.6321 9.58213 10.8574 9.46938 11.1021 9.40525C11.8244 9.21591 12.7162 9.70735 14.5 10.6902C16.14 11.594 16.9599 12.0458 17.114 12.6749C17.1665 12.889 17.1665 13.1107 17.114 13.3248Z"
                                stroke="white" stroke-width="1.55769" stroke-linejoin="round" />
                        </svg>

                    </a></div>
            </div>

        </div>
        </div>
    </div>
</div>