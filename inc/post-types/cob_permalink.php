<?php

/**
 * Add rewrite tags for language, city, and compound.
 * This is the primary and complete definition.
 */
if ( ! function_exists( 'cob_add_rewrite_tags' ) ) {
    function cob_add_rewrite_tags() {
        add_rewrite_tag( '%lang%', '(ar|en)' );
        add_rewrite_tag( '%city%', '([^/]+)' );
        add_rewrite_tag( '%compound%', '([^/]+)' );
    }
    add_action( 'init', 'cob_add_rewrite_tags' );
}

// The problematic second definition of cob_add_rewrite_tags and its add_action have been removed.

/**
 * Add rewrite rule for properties including language.
 * Matches: /{lang}/{city}/{compound}/{post-id}
 */
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

/**
 * Add custom query variables.
 */
if ( ! function_exists( 'cob_add_query_vars' ) ) {
    function cob_add_query_vars( $vars ) {
        $vars[] = 'lang';
        $vars[] = 'city';
        $vars[] = 'compound';
        return $vars;
    }
    add_filter( 'query_vars', 'cob_add_query_vars' );
}

/**
 * Customize the permalink for 'properties' post type.
 */
if ( ! function_exists( 'cob_properties_permalink' ) ) {
    function cob_properties_permalink( $post_link, $post, $leavename, $sample ) {
        if ( 'properties' !== $post->post_type ) {
            return $post_link;
        }

        // Determine language
        if ( function_exists( 'pll_current_language' ) ) {
            $lang = pll_current_language();
            // Fallback if pll_current_language returns empty (e.g., for default language not shown in slug)
            if (empty($lang)) {
                // You might want to get the default language slug from Polylang settings or set a hardcoded default
                $default_lang_slug = function_exists('pll_default_language') ? pll_default_language('slug') : 'en';
                $lang = $default_lang_slug;
            }
        } else {
            $lang = 'en'; // Default language if Polylang is not active
        }

        // Get city slug
        $city_slug = 'city'; // Default/fallback city slug
        $city_terms = get_the_terms( $post->ID, 'city' );
        if ( ! empty( $city_terms ) && ! is_wp_error( $city_terms ) ) {
            $city_slug = current( $city_terms )->slug;
        }

        // Get compound slug
        $compound_slug = 'compound'; // Default/fallback compound slug
        $compound_terms = get_the_terms( $post->ID, 'compound' );
        if ( ! empty( $compound_terms ) && ! is_wp_error( $compound_terms ) ) {
            $compound_slug = current( $compound_terms )->slug;
        }

        $post_link = home_url( user_trailingslashit( "$lang/$city_slug/$compound_slug/" . $post->ID ) );

        return $post_link;
    }
    add_filter( 'post_type_link', 'cob_properties_permalink', 10, 4 );
}

/**
 * Customize the term link for 'compound' taxonomy.
 */
if ( ! function_exists( 'cob_compound_term_link' ) ) {
    function cob_compound_term_link( $termlink, $term, $taxonomy ) {
        if ( 'compound' !== $taxonomy ) {
            return $termlink;
        }

        // Determine language
        if ( function_exists( 'pll_current_language' ) ) {
            $lang = pll_current_language();
            if (empty($lang)) {
                $default_lang_slug = function_exists('pll_default_language') ? pll_default_language('slug') : 'en';
                $lang = $default_lang_slug;
            }
        } else {
            $lang = 'en';
        }

        $city_term_id = get_term_meta( $term->term_id, 'compound_city', true );
        if ( $city_term_id ) {
            $city_term = get_term( $city_term_id, 'city' );
            if ( $city_term && ! is_wp_error( $city_term ) ) {
                $city_slug     = $city_term->slug;
                $compound_slug = $term->slug;
                $termlink = home_url( user_trailingslashit( "$lang/$city_slug/$compound_slug" ) );
            }
        }
        // Consider returning a more generic link if city is not found, or ensure compound_city meta is always set.
        return $termlink;
    }
    add_filter( 'term_link', 'cob_compound_term_link', 10, 3 );
}

/**
 * Add Custom Rewrite Rule for Properties (without language in the slug directly).
 *
 * This rule matches URLs of the form:
 * /{city}/{compound}/{post-ID}
 * and rewrites them to the appropriate query for the "properties" post type.
 * Note: Language might be handled by Polylang or another mechanism for such URLs.
 */
if ( ! function_exists( 'cob_custom_properties_rewrite_rule' ) ) {
    function cob_custom_properties_rewrite_rule() {
        add_rewrite_rule(
            '^([^/]+)/([^/]+)/([0-9]+)/?$', // Matches: {city}/{compound}/{post-id}
            // Correctly maps captured city and compound to query variables
            'index.php?post_type=properties&city=$matches[1]&compound=$matches[2]&p=$matches[3]',
            'top'
        );
    }
    add_action( 'init', 'cob_custom_properties_rewrite_rule' );
}

?>