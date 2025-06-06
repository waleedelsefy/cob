<?php
/**
 * Template Name: Landing & Search Template
 *
 * @package cob_theme
 */


$theme_dir = get_template_directory_uri();
?>

<div class="landing">
    <div class="container">
        <div class="img-holder">
            <div class="swiper landing-swiper">
                <div class="swiper-wrapper">
                    <?php
                    $args         = [
                        'post_type'      => 'slider',
                        'posts_per_page' => -1,
                        'orderby'        => 'menu_order',
                        'order'          => 'ASC',
                    ];
                    $slider_query = new WP_Query( $args );
                    ?>
                    <?php if ( $slider_query->have_posts() ) : ?>
                        <?php while ( $slider_query->have_posts() ) : $slider_query->the_post(); ?>
                            <?php $slider_img = get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>
                            <div class="swiper-slide">
                                <img
                                        data-src="<?php echo esc_url( $slider_img ? $slider_img : $theme_dir . '/assets/imgs/landing.jpg' ); ?>"
                                        alt="<?php the_title_attribute(); ?>"
                                        class="lazyload"
                                >
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    <?php else : ?>
                        <div class="swiper-slide">
                            <img
                                    data-src="<?php echo esc_url( $theme_dir . '/assets/imgs/landing.jpg' ); ?>"
                                    alt="<?php esc_attr_e( 'Default Slide', 'cob_theme' ); ?>"
                                    class="lazyload"
                            >
                        </div>
                    <?php endif; ?>
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

            <div class="search-bar">
                <h3 id="SearchTitle2" class="head"><?php esc_html_e( 'Find your property', 'cob_theme' ); ?></h3>

                <!-- Basic Search Form -->
                <form id="basicSearchForm" class="basic-search-form" onsubmit="return false;">
                    <div class="search-bar-content">
                        <div class="search-form">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M7.66634 2.08301C4.58275 2.08301 2.08301 4.58275 2.08301 7.66634C2.08301 10.7499 4.58275 13.2497 7.66634 13.2497C10.7499 13.2497 13.2497 10.7499 13.2497 7.66634C13.2497 4.58275 10.7499 2.08301 7.66634 2.08301ZM0.583008 7.66634C0.583008 3.75432 3.75432 0.583008 7.66634 0.583008C11.5784 0.583008 14.7497 3.75432 14.7497 7.66634C14.7497 11.5784 11.5784 14.7497 7.66634 14.7497C3.75432 14.7497 0.583008 11.5784 0.583008 7.66634Z"
                                      fill="#081945" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                      d="M12.8027 12.8027C13.0956 12.5098 13.5704 12.5098 13.8633 12.8027L15.1967 14.136C15.4896 14.4289 15.4896 14.9038 15.1967 15.1967C14.9038 15.4896 14.4289 15.4896 14.136 15.1967L12.8027 13.8633C12.5098 13.5704 12.5098 13.0956 12.8027 12.8027Z"
                                      fill="#081945" />
                            </svg>
                            <input
                                    type="text"
                                    id="basicSearchInput"
                                    name="basic_search"
                                    placeholder="<?php esc_attr_e( 'Search by compound, location, real estate', 'cob_theme' ); ?>"
                                    value=""
                            />
                        </div>

                        <ul class="nav-menu2">
                            <li>
                                <?php
                                $propertie_types = get_terms( [
                                    'taxonomy'   => 'type',
                                    'hide_empty' => false,
                                ] );
                                ?>
                                <select id="basicPropertyType" name="basic_propertie_type">
                                    <option value=""><?php esc_html_e( 'Select Property Type', 'cob_theme' ); ?></option>
                                    <?php if ( ! empty( $propertie_types ) && ! is_wp_error( $propertie_types ) ) : ?>
                                        <?php foreach ( $propertie_types as $term ) : ?>
                                            <option value="<?php echo esc_attr( $term->slug ); ?>">
                                                <?php echo esc_html( $term->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </li>
                            <li>
                                <select id="basicBedrooms" name="bedrooms">
                                    <option value=""><?php esc_html_e( 'Bedrooms', 'cob_theme' ); ?></option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </li>
                            <li>
                                <select id="basicPrice" name="filter_price">
                                    <option value=""><?php esc_html_e( 'Price', 'cob_theme' ); ?></option>
                                    <option value="0-1000000">0 - 1,000,000</option>
                                    <option value="1000000-2500000">1,000,000 - 2,500,000</option>
                                    <option value="2500000-5000000">2,500,000 - 5,000,000</option>
                                    <option value="5000000+">5,000,000+</option>
                                </select>
                            </li>
                            <li>
                                <button type="button" id="basicSearchButton">
                                    <?php esc_html_e( 'Search', 'cob_theme' ); ?>
                                </button>
                            </li>
                        </ul>
                    </div>
                </form>

                <!-- Detailed Search Form Toggle -->
                <div class="search-container">
                    <h2 style="display: none;" id="searchTitle"><?php esc_html_e( 'Detailed search', 'cob_theme' ); ?></h2>
                    <svg class="search-icon" id="searchIcon" width="16" height="16" viewBox="0 0 16 16" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M7.66634 2.08301C4.58275 2.08301 2.08301 4.58275 2.08301 7.66634C2.08301 10.7499 4.58275 13.2497 7.66634 13.2497C10.7499 13.2497 13.2497 10.7499 13.2497 7.66634C13.2497 4.58275 10.7499 2.08301 7.66634 2.08301ZM0.583008 7.66634C0.583008 3.75432 3.75432 0.583008 7.66634 0.583008C11.5784 0.583008 14.7497 3.75432 14.7497 7.66634C14.7497 11.5784 11.5784 14.7497 7.66634 14.7497C3.75432 14.7497 0.583008 11.5784 0.583008 7.66634Z"
                              fill="#081945" />
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M12.8027 12.8027C13.0956 12.5098 13.5704 12.5098 13.8633 12.8027L15.1967 14.136C15.4896 14.4289 15.4896 14.9038 15.1967 15.1967C14.9038 15.4896 14.4289 15.4896 14.136 15.1967L12.8027 13.8633C12.5098 13.5704 12.5098 13.0956 12.8027 12.8027Z"
                              fill="#081945" />
                    </svg>
                    <input
                            type="text"
                            id="detailedSearchInput"
                            name="s"
                            placeholder="<?php esc_attr_e( 'Search by keywords, location, real estate photos', 'cob_theme' ); ?>"
                            value=""
                    >
                    <button id="toggleButton" type="button">
                        <svg id="sliderIcon" width="16" height="16" viewBox="0 0 16 16" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 4.66699H4" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M2 11.333H6" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 11.333H14" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M10 4.66699H14" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                    d="M4 4.66699C4 4.04574 4 3.73511 4.10149 3.49008C4.23682 3.16338 4.49639 2.90381 4.82309 2.76849C5.06812 2.66699 5.37875 2.66699 6 2.66699C6.62125 2.66699 6.93187 2.66699 7.17693 2.76849C7.5036 2.90381 7.7632 3.16338 7.89853 3.49008C8 3.73511 8 4.04574 8 4.66699C8 5.28825 8 5.59887 7.89853 5.84391C7.7632 6.17061 7.5036 6.43017 7.17693 6.5655C6.93187 6.66699 6.62125 6.66699 6 6.66699C5.37875 6.66699 5.06812 6.66699 4.82309 6.5655C4.49639 6.43017 4.23682 6.17061 4.10149 5.84391C4 5.59887 4 5.28825 4 4.66699Z"
                                    stroke="white" />
                            <path
                                    d="M8 11.333C8 10.7117 8 10.4011 8.10147 10.1561C8.2368 9.82941 8.4964 9.56981 8.82307 9.43447C9.06813 9.33301 9.37873 9.33301 10 9.33301C10.6213 9.33301 10.9319 9.33301 11.1769 9.43447C11.5036 9.56981 11.7632 9.82941 11.8985 10.1561C12 10.4011 12 10.7117 12 11.333C12 11.9543 12 12.2649 11.8985 12.5099C11.7632 12.8366 11.5036 13.0962 11.1769 13.2315C10.9319 13.333 10.6213 13.333 10 13.333C9.37873 13.333 9.06813 13.333 8.82307 13.2315C8.4964 13.0962 8.2368 12.8366 8.10147 12.5099C8 12.2649 8 11.9543 8 11.333Z"
                                    stroke="white" />
                        </svg>
                        <svg id="closeIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M18.3 5.7L12 12l6.3 6.3-1.4 1.4L12 13.4 5.7 19.7 4.3 18.3 10.6 12 4.3 5.7 5.7 4.3 12 10.6l6.3-6.3 1.4 1.4z" />
                        </svg>
                    </button>
                </div>

                <div id="search-results" class="search-results"></div>
            </div>
        </div>
    </div>
</div>


<!-- تضمين مكتبات Algolia InstantSearch -->
<script src="https://cdn.jsdelivr.net/npm/algoliasearch@4/dist/algoliasearch-lite.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/instantsearch.js@4"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تكوين Algolia
        const searchClient = algoliasearch(
            'VPPO9FLNT3',                  // Application ID
            '858a51f64a8d791e686106550c9983ad'  // Search-Only API Key
        );

        // انشاء مثيل InstantSearch
        const search = instantsearch({
            indexName: 'properties', // استبدل باسم فهرس الـproperties في Algolia إذا كان مختلفًا
            searchClient: searchClient,
            routing: true
        });

        // Widget للـ “Basic Search” (term فقط)
        search.addWidgets([
            instantsearch.widgets.searchBox({
                container: '#basicSearchInput',
                placeholder: '<?php esc_attr_e( "Search by compound, location, real estate", "cob_theme" ); ?>',
                showReset: false,
                showSubmit: false,
                showLoadingIndicator: false,
                cssClasses: {
                    input: 'algolia-input',
                }
            })
        ]);

        // Widget لفلترة نوع العقار
        search.addWidgets([
            instantsearch.widgets.menuSelect({
                container: '#basicPropertyType',
                attribute: 'type', // تأكد أن لديك attribute في Algolia باسم “type”
                templates: {
                    defaultOption: '<?php esc_html_e( "Select Property Type", "cob_theme" ); ?>'
                },
                cssClasses: {
                    select: 'algolia-select',
                }
            })
        ]);

        // Widget لفلترة عدد الغرف
        search.addWidgets([
            instantsearch.widgets.menuSelect({
                container: '#basicBedrooms',
                attribute: 'bedrooms', // تأكد من هذا الحقل في فهرسك
                templates: {
                    defaultOption: '<?php esc_html_e( "Bedrooms", "cob_theme" ); ?>'
                },
                cssClasses: {
                    select: 'algolia-select',
                }
            })
        ]);

        // Widget لفلترة الأسعار (range slider يظهر قيم الحد الأقصى والأدنى مع شريط سحب)
        search.addWidgets([
            instantsearch.widgets.rangeInput({
                container: '#basicPrice',
                attribute: 'price', // تأكد أن لديك attribute “price” في فهرسك (رقمي)
                cssClasses: {
                    form: 'algolia-price-range',
                    input: 'algolia-price-input',
                },
                // يمكنك تحديد step أو تنسيق العرض هنا إذا لزم الأمر
            })
        ]);

        search.addWidgets([
            instantsearch.widgets.hits({
                container: '#search-results',
                templates: {
                    item(hit) {
                        return `
                            <div class="hit">
                                <a href="${hit.permalink}">
                                    <h4>${instantsearch.highlight({ attribute: 'title', hit })}</h4>
                                </a>
                                <p>${hit.location || ''}</p>
                                <p><?php esc_html_e( 'Price', 'cob_theme' ); ?>: ${hit.price || ''} EGP</p>
                                <p><?php esc_html_e( 'Bedrooms', 'cob_theme' ); ?>: ${hit.bedrooms || ''}</p>
                            </div>
                        `;
                    },
                    empty: '<?php esc_html_e( "No results found", "cob_theme" ); ?>',
                },
                cssClasses: {
                    list: 'algolia-hits-list',
                    item: 'algolia-hit-item',
                }
            })
        ]);

        search.start();

        document.getElementById('basicSearchButton').addEventListener('click', function() {
            const inputElement = document.querySelector('#basicSearchInput input');
            const queryValue = inputElement ? inputElement.value : '';
            search.helper.setQuery(queryValue).search();
        });

        // إعداد Detailed Search Toggle
        const toggleButton  = document.getElementById('toggleButton');
        const hiddenContent = document.getElementById('hiddenContent');
        toggleButton.addEventListener('click', function() {
            if ( hiddenContent.style.display === 'none' ) {
                hiddenContent.style.display = 'block';
            } else {
                hiddenContent.style.display = 'none';
            }
        });

    });
</script>
