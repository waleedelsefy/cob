<?php
$paged  = ( get_query_var('paged') ) ? absint( get_query_var('paged') ) : 1;
$number = 10; // عدد العناصر في كل صفحة
$offset = ( $paged - 1 ) * $number;

$all_developer_ids = get_terms( array(
    'taxonomy'   => 'developer',
    'hide_empty' => false,
    'fields'     => 'ids',
) );
$total_terms = is_array( $all_developer_ids ) ? count( $all_developer_ids ) : 0;
$total_pages = ceil( $total_terms / $number );
$args = array(
    'taxonomy'   => 'developer',
    'hide_empty' => false,
    'number'     => $number,
    'offset'     => $offset,
);

$term_query = new WP_Term_Query( $args );
$developers = $term_query->terms;
?>

<section class="pagination-section">
    <div class="container">
        <div class="cards">
            <?php if ( ! empty( $developers ) && ! is_wp_error( $developers ) ) : ?>
                <?php foreach ( $developers as $developer ) : ?>
                    <?php
                    if ( ! empty( $developer ) && is_object( $developer ) ) {
                        $developer_link = get_term_link( $developer );
                        if ( is_wp_error( $developer_link ) ) {
                            $developer_link = '#';
                        }
                        $thumbnail_id = absint( get_term_meta( $developer->term_id, 'thumbnail_id', true ) );
                        $image_url    = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : get_template_directory_uri() . '/assets/imgs/developer-default.png';
                    } else {
                        $developer_link = '#'; // Fallback URL
                        $image_url    = get_template_directory_uri() . '/assets/imgs/developer-default.png';
                    }
                    ?>
                    <div class="motaoron-card">
                        <a href="<?php echo esc_url( $developer_link ); ?>" class="motaoron-img">
                            <img data-src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $developer->name ); ?>" class="lazyload">
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p><?php esc_html_e( 'There are currently no Developers.', 'cob_theme' ); ?></p>
            <?php endif; ?>
        </div>

        <div class="pagination">
            <?php
            if ( $paged > 1 ) : ?>
                <button class="page" data-page="prev">
                    <a href="<?php echo esc_url( get_pagenum_link( $paged - 1 ) ); ?>">
                        <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.10212 6.99773L0.997038 1.89666C0.619477 1.5191 0.619477 0.908582 0.997038 0.53504C1.3746 0.161498 1.98512 0.161497 2.36268 0.53504L8.14656 6.31491C8.51208 6.68042 8.52011 7.26684 8.17468 7.6444L2.36669 13.4644C2.17791 13.6532 1.92888 13.7456 1.68387 13.7456C1.43886 13.7456 1.18983 13.6532 1.00105 13.4644C0.623496 13.0869 0.623496 12.4764 1.00105 12.1028L6.10212 6.99773Z" fill="black"></path>
                        </svg>
                        السابق
                    </a>
                </button>
            <?php endif;


            $range = 5;
            $start = max( 1, $paged - floor( $range / 2 ) );
            $end   = min( $total_pages, $start + $range - 1 );
            if ( ( $end - $start + 1 ) < $range ) {
                $start = max( 1, $end - $range + 1 );
            }
            for ( $i = $start; $i <= $end; $i++ ) :
                $current_class = ( $i == $paged ) ? ' current' : '';
                ?>
                <button class="page<?php echo esc_attr( $current_class ); ?>" data-page="<?php echo esc_attr( $i ); ?>">
                    <a href="<?php echo esc_url( get_pagenum_link( $i ) ); ?>"><?php echo esc_html( $i ); ?></a>
                </button>
            <?php endfor;

            if ( $paged < $total_pages ) : ?>
                <button class="page" data-page="next">
                    <a href="<?php echo esc_url( get_pagenum_link( $paged + 1 ) ); ?>">
                        التالي
                        <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.32561 7.00227L7.4307 12.1033C7.80826 12.4809 7.80826 13.0914 7.4307 13.465C7.05314 13.8385 6.44262 13.8385 6.06506 13.465L0.281172 7.68509C-0.084341 7.31958 -0.0923709 6.73316 0.253054 6.3556L6.06104 0.535562C6.24982 0.346784 6.49885 0.254402 6.74386 0.254402C6.98887 0.254402 7.2379 0.346785 7.42668 0.535562C7.80424 0.913122 7.80424 1.52364 7.42668 1.89719L2.32561 7.00227Z" fill="black" />
                        </svg>
                    </a>
                </button>
            <?php endif; ?>
        </div>

    </div>
</section>
