<?php



if ( ! function_exists( 'cob_add_rewrite_tags' ) ) {
    function cob_add_rewrite_tags() {
        add_rewrite_tag( '%lang%', '(ar|en)' );
        add_rewrite_tag( '%city%', '([^/]+)' );
        add_rewrite_tag( '%compound%', '([^/]+)' );
    }
    add_action( 'init', 'cob_add_rewrite_tags' );
}


if ( ! function_exists( 'cob_add_properties_rewrite_rule' ) ) {
    function cob_add_properties_rewrite_rule() {
        add_rewrite_rule(
            '^([^/]{2})/([^/]+)/([^/]+)/([0-9]+)/?$',
            'index.php?post_type=properties&p=$matches[4]&lang=$matches[1]&city=$matches[2]&compound=$matches[3]',
            'top'
        );

    }
    add_action( 'init', 'cob_add_properties_rewrite_rule' );
}


// ------------------------------------------------------
// ------------------------------------------------------
if ( ! function_exists( 'cob_add_query_vars' ) ) {
    function cob_add_query_vars( $vars ) {
        $vars[] = 'lang';
        $vars[] = 'city';
        $vars[] = 'compound';
        return $vars;
    }
    add_filter( 'query_vars', 'cob_add_query_vars' );
}



if ( ! function_exists( 'cob_properties_permalink' ) ) {
    function cob_properties_permalink( $post_link, $post, $leavename, $sample ) {
        if ( 'properties' !== $post->post_type ) {
            return $post_link;
        }

        if ( function_exists( 'pll_current_language' ) ) {
            $lang = pll_current_language();
        } else {
            $lang = 'en';
        }

        $city_slug = 'city';
        $city_terms = get_the_terms( $post->ID, 'city' );
        if ( ! empty( $city_terms ) && ! is_wp_error( $city_terms ) ) {
            $city_slug = current( $city_terms )->slug;
        }

        $compound_slug = 'compound';
        $compound_terms = get_the_terms( $post->ID, 'compound' );
        if ( ! empty( $compound_terms ) && ! is_wp_error( $compound_terms ) ) {
            $compound_slug = current( $compound_terms )->slug;
        }

        $post_link = home_url( user_trailingslashit( "$lang/$city_slug/$compound_slug/" . $post->ID ) );

        return $post_link;
    }
    add_filter( 'post_type_link', 'cob_properties_permalink', 10, 4 );
}


if ( ! function_exists( 'cob_compound_term_link' ) ) {
    function cob_compound_term_link( $termlink, $term, $taxonomy ) {
        if ( 'compound' !== $taxonomy ) {
            return $termlink;
        }

        if ( function_exists( 'pll_current_language' ) ) {
            $lang = pll_current_language();
        } else {
            $lang = 'en';
        }

        $city_term_id = get_term_meta( $term->term_id, 'compound_city', true );
        if ( $city_term_id ) {
            $city_term = get_term( $city_term_id, 'city' );
            if ( ! is_wp_error( $city_term ) && $city_term ) {
                $city_slug     = $city_term->slug;
                $compound_slug = $term->slug;
                // /{lang}/{city_slug}/{compound_slug}/
                $termlink = home_url( user_trailingslashit( "$lang/$city_slug/$compound_slug" ) );
            }
        }

        return $termlink;
    }
    add_filter( 'term_link', 'cob_compound_term_link', 10, 3 );
}
