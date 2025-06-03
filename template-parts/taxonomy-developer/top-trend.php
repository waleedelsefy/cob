<?php
/**
 * Taxonomy Compound Featured Template Part with JavaScript Pagination
 *
 * @package Capital_of_Business
 */

// Get the current term
$term = get_queried_object();

// Retrieve the sort option from the URL (default is date_desc).
$sort_option = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'date_desc';

// Set query parameters based on the selected sort option.
switch ($sort_option) {
    case 'price_asc':
        $orderby  = 'meta_value_num';
        $order    = 'ASC';
        $meta_key = 'price';
        break;
    case 'price_desc':
        $orderby  = 'meta_value_num';
        $order    = 'DESC';
        $meta_key = 'price';
        break;
    case 'date_asc':
        $orderby  = 'date';
        $order    = 'ASC';
        $meta_key = '';
        break;
    default: // date_desc
        $orderby  = 'date';
        $order    = 'DESC';
        $meta_key = '';
        break;
}

// Get the current page number (default to 1)
$current_page = 1;

// Build the WP_Query arguments for the first page
$args = [
    'post_type'      => 'properties',
    'posts_per_page' => 6,
    'paged'          => $current_page,
    'orderby'        => $orderby,
    'order'          => $order,
    'post_status'    => 'publish',
    'tax_query'      => [
        [
            'taxonomy' => $term->taxonomy,
            'field'    => 'slug',
            'terms'    => $term->slug,
        ],
    ],
];

if (!empty($meta_key)) {
    $args['meta_key'] = $meta_key;
}

// Execute the query
$properties_query = new WP_Query($args);
$total_results = $properties_query->found_posts;
$max_pages = $properties_query->max_num_pages;

$placeholder = get_template_directory_uri() . '/assets/imgs/placeholder.jpg';

// Get the current URL as the base for AJAX requests
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// Remove any existing query parameters
$base_url = strtok($current_url, '?');
?>
<section class="pagination-section pagination-city pagination-brand">
    <div class="container">
        <div class="top-compounds">
            <div class="right-compounds">
                <!-- Header displays "Discover [Taxonomy Term Name] Units" -->
                <h3 class="head"><?php echo sprintf(esc_html__('Discover %s Units', 'cob_theme'), $term->name); ?></h3>
                <!-- Display the dynamic property count -->
                <h5 class="results-count"><?php echo esc_html($total_results); ?> <?php esc_html_e('results', 'cob_theme'); ?></h5>
            </div>
            <div class="tabs-btn">
                <div class="select-tabs">
                    <select class="sort-select" id="sort-select">
                        <option value="date_desc" <?php selected($sort_option, 'date_desc'); ?>>
                            <?php esc_html_e('Sort By', 'cob_theme'); ?>
                        </option>
                        <option value="price_asc" <?php selected($sort_option, 'price_asc'); ?>>
                            <?php esc_html_e('Price: Low to High', 'cob_theme'); ?>
                        </option>
                        <option value="price_desc" <?php selected($sort_option, 'price_desc'); ?>>
                            <?php esc_html_e('Price: High to Low', 'cob_theme'); ?>
                        </option>
                        <option value="date_asc" <?php selected($sort_option, 'date_asc'); ?>>
                            <?php esc_html_e('Date: Oldest', 'cob_theme'); ?>
                        </option>
                        <option value="date_desc" <?php selected($sort_option, 'date_desc'); ?>>
                            <?php esc_html_e('Date: Newest', 'cob_theme'); ?>
                        </option>
                    </select>
                    <span class="arrow">
                        <!-- Down arrow icon -->
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M15.7146 0C16.1879 0 16.5717 0.383759 16.5717 0.857141V16.5714L18.4574 14.0571C18.7414 13.6784 19.2787 13.6017 19.6574 13.8857C20.0362 14.1697 20.1128 14.7069 19.8288 15.0857L16.4003 19.6571C16.1789 19.9523 15.7935 20.0726 15.4435 19.956C15.0935 19.8393 14.8574 19.5117 14.8574 19.1428V0.857141C14.8574 0.383759 15.2412 0 15.7146 0Z"
                                  fill="black" />
                            <path
                                    d="M0 14.571C0 14.0976 0.383759 13.7139 0.857141 13.7139H11.1428C11.6162 13.7139 12 14.0976 12 14.571C12 15.0444 11.6162 15.4281 11.1428 15.4281H0.857141C0.383759 15.4281 0 15.0444 0 14.571Z"
                                    fill="black" />
                            <path opacity="0.7"
                                  d="M2.28613 8.85714C2.28613 8.38377 2.66989 8 3.14327 8H11.1433C11.6166 8 12.0004 8.38377 12.0004 8.85714C12.0004 9.33051 11.6166 9.71428 11.1433 9.71428H3.14327C2.66989 9.71428 2.28613 9.33051 2.28613 8.85714Z"
                                  fill="black" />
                            <path opacity="0.4"
                                  d="M4.57129 3.14327C4.57129 2.66989 4.95505 2.28613 5.42843 2.28613H11.1427C11.6161 2.28613 11.9998 2.66989 11.9998 3.14327C11.9998 3.61666 11.6161 4.00041 11.1427 4.00041H5.42843C4.95505 4.00041 4.57129 3.61666 4.57129 3.14327Z"
                                  fill="black" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        <!-- Properties Cards Section -->
        <div class="properties-cards" id="properties-container">
            <?php if ($properties_query->have_posts()) : ?>
                <?php while ($properties_query->have_posts()) : $properties_query->the_post(); ?>
                    <?php get_template_part('template-parts/single/properties-card'); ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p class="no-results"><?php esc_html_e('There are no posts currently available', 'cob_theme'); ?></p>
            <?php endif; ?>
        </div>

        <!-- Pagination Buttons -->
        <?php if ($max_pages > 1) : ?>
            <div class="pagination">
                <div class="nav-links" id="pagination-container">
                    <!-- Previous button (disabled on first page) -->
                    <a class="prev page-numbers <?php echo ($current_page == 1) ? 'disabled' : ''; ?>" href="javascript:void(0);" data-page="<?php echo max(1, $current_page - 1); ?>">
                        <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.10212 6.99773L0.997038 1.89666C0.619477 1.5191 0.619477 0.908582 0.997038 0.53504C1.3746 0.161498 1.98512 0.161497 2.36268 0.53504L8.14656 6.31491C8.51208 6.68042 8.52011 7.26684 8.17468 7.6444L2.36669 13.4644C2.17791 13.6532 1.92888 13.7456 1.68387 13.7456C1.43886 13.7456 1.18983 13.6532 1.00105 13.4644C0.623496 13.0869 0.623496 12.4764 1.00105 12.1028L6.10212 6.99773Z" fill="black" />
                        </svg> <?php esc_html_e('Previous', 'cob_theme'); ?>
                    </a>

                    <!-- Page numbers -->
                    <?php for ($i = 1; $i <= min(5, $max_pages); $i++) : ?>
                        <?php if ($i == $current_page) : ?>
                            <span aria-current="page" class="page-numbers current"><?php echo $i; ?></span>
                        <?php else : ?>
                            <a class="page-numbers" href="javascript:void(0);" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <!-- Show ellipsis if there are more pages -->
                    <?php if ($max_pages > 5) : ?>
                        <span class="page-numbers dots">…</span>
                        <a class="page-numbers" href="javascript:void(0);" data-page="<?php echo $max_pages; ?>"><?php echo $max_pages; ?></a>
                    <?php endif; ?>

                    <!-- Next button (disabled on last page) -->
                    <a class="next page-numbers <?php echo ($current_page == $max_pages) ? 'disabled' : ''; ?>" href="javascript:void(0);" data-page="<?php echo min($max_pages, $current_page + 1); ?>">
                        <?php esc_html_e('Next', 'cob_theme'); ?> <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281172 7.68509C-0.084341 7.31958 -0.0923709 6.73316 0.253054 6.3556L6.06104 0.535562C6.24982 0.346784 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346784 7.42668 0.535562C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="black" />
                        </svg>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Add JavaScript for AJAX pagination -->
<script>
    jQuery(document).ready(function($) {
        // Store the current page and sort option
        var currentPage = 1;
        var currentSort = '<?php echo esc_js($sort_option); ?>';
        var maxPages = <?php echo esc_js($max_pages); ?>;
        var termId = <?php echo esc_js($term->term_id); ?>;
        var taxonomy = '<?php echo esc_js($term->taxonomy); ?>';
        var isLoading = false;

        // Function to update pagination UI
        function updatePagination(page) {
            // Update current page
            currentPage = parseInt(page);

            // Create pagination HTML
            var paginationHtml = '';

            // Previous button
            paginationHtml += '<a class="prev page-numbers ' + (currentPage == 1 ? 'disabled' : '') + '" href="javascript:void(0);" data-page="' + Math.max(1, currentPage - 1) + '">';
            paginationHtml += '<svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">';
            paginationHtml += '<path d="M6.10212 6.99773L0.997038 1.89666C0.619477 1.5191 0.619477 0.908582 0.997038 0.53504C1.3746 0.161498 1.98512 0.161497 2.36268 0.53504L8.14656 6.31491C8.51208 6.68042 8.52011 7.26684 8.17468 7.6444L2.36669 13.4644C2.17791 13.6532 1.92888 13.7456 1.68387 13.7456C1.43886 13.7456 1.18983 13.6532 1.00105 13.4644C0.623496 13.0869 0.623496 12.4764 1.00105 12.1028L6.10212 6.99773Z" fill="black" />';
            paginationHtml += '</svg> <?php esc_html_e('Previous', 'cob_theme'); ?>';
            paginationHtml += '</a>';

            // Calculate start and end page numbers
            var startPage = Math.max(1, currentPage - 2);
            var endPage = Math.min(maxPages, currentPage + 2);

            // Ensure we show at least 5 page numbers if available
            if (endPage - startPage < 4 && maxPages > 4) {
                if (startPage == 1) {
                    endPage = Math.min(5, maxPages);
                } else if (endPage == maxPages) {
                    startPage = Math.max(1, maxPages - 4);
                }
            }

            // First page and ellipsis
            if (startPage > 1) {
                paginationHtml += '<a class="page-numbers" href="javascript:void(0);" data-page="1">1</a>';
                if (startPage > 2) {
                    paginationHtml += '<span class="page-numbers dots">…</span>';
                }
            }

            // Page numbers
            for (var i = startPage; i <= endPage; i++) {
                if (i == currentPage) {
                    paginationHtml += '<span aria-current="page" class="page-numbers current">' + i + '</span>';
                } else {
                    paginationHtml += '<a class="page-numbers" href="javascript:void(0);" data-page="' + i + '">' + i + '</a>';
                }
            }

            // Last page and ellipsis
            if (endPage < maxPages) {
                if (endPage < maxPages - 1) {
                    paginationHtml += '<span class="page-numbers dots">…</span>';
                }
                paginationHtml += '<a class="page-numbers" href="javascript:void(0);" data-page="' + maxPages + '">' + maxPages + '</a>';
            }

            // Next button
            paginationHtml += '<a class="next page-numbers ' + (currentPage == maxPages ? 'disabled' : '') + '" href="javascript:void(0);" data-page="' + Math.min(maxPages, currentPage + 1) + '">';
            paginationHtml += '<?php esc_html_e('Next', 'cob_theme'); ?> <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">';
            paginationHtml += '<path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281172 7.68509C-0.084341 7.31958 -0.0923709 6.73316 0.253054 6.3556L6.06104 0.535562C6.24982 0.346784 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346784 7.42668 0.535562C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="black" />';
            paginationHtml += '</svg>';
            paginationHtml += '</a>';

            // Update pagination container
            $('#pagination-container').html(paginationHtml);

            // Attach click events to new pagination links
            attachPaginationEvents();
        }

        // Function to load properties via AJAX
        function loadProperties(page, sort) {
            if (isLoading) return;
            isLoading = true;

            // Show loading indicator
            $('#properties-container').append('<div class="loading-indicator">Loading...</div>');

            // Make AJAX request
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'load_developer_properties',
                    page: page,
                    sort: sort,
                    term_id: termId,
                    taxonomy: taxonomy,
                    nonce: '<?php echo wp_create_nonce('load_developer_properties_nonce'); ?>'
                },
                success: function(response) {
                    // Update properties container
                    $('#properties-container').html(response.data.html);

                    // Update pagination
                    maxPages = response.data.max_pages;
                    updatePagination(page);

                    // Update URL without reloading the page (for browser history)
                    if (window.history && window.history.pushState) {
                        var newUrl = updateUrlParameter(window.location.href, 'sort', sort);
                        window.history.pushState({page: page, sort: sort}, '', newUrl);
                    }

                    // Remove loading indicator
                    $('.loading-indicator').remove();
                    isLoading = false;
                },
                error: function() {
                    // Handle error
                    $('#properties-container').html('<p class="no-results">Error loading properties. Please try again.</p>');
                    $('.loading-indicator').remove();
                    isLoading = false;
                }
            });
        }

        // Function to update URL parameter
        function updateUrlParameter(uri, key, value) {
            var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            var separator = uri.indexOf('?') !== -1 ? "&" : "?";
            if (uri.match(re)) {
                return uri.replace(re, '$1' + key + "=" + value + '$2');
            } else {
                return uri + separator + key + "=" + value;
            }
        }

        // Function to attach click events to pagination links
        function attachPaginationEvents() {
            $('.page-numbers').not('.dots, .current, .disabled').on('click', function(e) {
                e.preventDefault();
                var page = $(this).data('page');
                loadProperties(page, currentSort);
            });
        }

        // Handle sort select change
        $('#sort-select').on('change', function() {
            currentSort = $(this).val();
            loadProperties(1, currentSort);
        });

        // Initial attachment of pagination events
        attachPaginationEvents();

        // Handle browser back/forward buttons
        $(window).on('popstate', function(e) {
            if (e.originalEvent.state) {
                loadProperties(e.originalEvent.state.page, e.originalEvent.state.sort);
            }
        });
    });
</script>

