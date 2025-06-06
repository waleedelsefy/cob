<?php
/**
 * WordPress Permalink and Rewrite Rule Customizations for Cob Theme.
 *
 * This version uses unique URL bases for each content type to prevent rewrite rule conflicts.
 * - Cities:      /lang/city/city-slug/
 * - Compounds:   /lang/compound/city-slug/compound-slug/
 * - Properties:  /lang/property/city-slug/compound-slug/post-id/
 *
 * This structure is more robust and less prone to errors.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Add custom query variables to WordPress.
 * The rewrite tags (%lang%, %city%, etc.) are no longer strictly needed for matching
 * since we are using explicit URL bases, but query vars are essential.
 */
if ( ! function_exists( 'cob_add_query_vars' ) ) {
    function cob_add_query_vars( $vars ) {
        $vars[] = 'lang';
        $vars[] = 'city';     // WordPress can map this to the 'city' taxonomy if the query var matches the taxonomy name
        $vars[] = 'compound'; // WordPress can map this to the 'compound' taxonomy
        return $vars;
    }
    add_filter( 'query_vars', 'cob_add_query_vars' );
}

/**
 * Add all custom rewrite rules with unambiguous URL structures.
 */
if ( ! function_exists( 'cob_add_all_custom_rewrite_rules_unambiguous' ) ) {
    function cob_add_all_custom_rewrite_rules_unambiguous() {

        // --- Single Property Post Rule ---
        // Matches /{lang_code}/property/{city_slug}/{compound_slug}/{post_id}/
        add_rewrite_rule(
            '^([^/]{2})/property/([^/]+)/([^/]+)/([0-9]+)/?$',
            'index.php?post_type=properties&p=$matches[4]&lang=$matches[1]&city=$matches[2]&compound=$matches[3]',
            'top'
        );
        // Matches /property/{city_slug}/{compound_slug}/{post_id}/ (for default language)
        add_rewrite_rule(
            '^property/([^/]+)/([^/]+)/([0-9]+)/?$',
            'index.php?post_type=properties&p=$matches[3]&city=$matches[1]&compound=$matches[2]',
            'top'
        );

        // --- Compound Taxonomy Archive Rule ---
        // Matches /{lang_code}/compound/{city_slug}/{compound_slug}/
        add_rewrite_rule(
            '^([^/]{2})/compound/([^/]+)/([^/]+)/?$',
            'index.php?taxonomy=compound&term=$matches[3]&lang=$matches[1]&city=$matches[2]',
            'top'
        );
        // Matches /compound/{city_slug}/{compound_slug}/ (for default language)
        add_rewrite_rule(
            '^compound/([^/]+)/([^/]+)/?$',
            'index.php?taxonomy=compound&term=$matches[2]&city=$matches[1]',
            'top'
        );

        // --- City Taxonomy Archive Rule ---
        // Matches /{lang_code}/city/{city_term_slug}/
        add_rewrite_rule(
            '^([^/]{2})/city/([^/]+)/?$',
            'index.php?taxonomy=city&term=$matches[2]&lang=$matches[1]',
            'top'
        );
        // Matches /city/{city_term_slug}/ (for default language)
        add_rewrite_rule(
            '^city/([^/]+)/?$',
            'index.php?taxonomy=city&term=$matches[1]',
            'top'
        );
    }
    add_action( 'init', 'cob_add_all_custom_rewrite_rules_unambiguous' );
}

/**
 * Customize the permalink for 'properties' post type to match the new structure.
 */
if ( ! function_exists( 'cob_properties_permalink_unambiguous' ) ) {
    function cob_properties_permalink_unambiguous( $post_link, $post, $leavename, $sample ) {
        if ( 'properties' !== $post->post_type || $sample ) {
            return $post_link;
        }

        $lang_slug = 'en';
        if ( function_exists( 'pll_get_post_language' ) ) {
            $current_lang = pll_get_post_language( $post->ID, 'slug' );
            if ( ! empty( $current_lang ) ) {
                $lang_slug = $current_lang;
            } elseif ( function_exists('pll_default_language') ) {
                $lang_slug = pll_default_language('slug');
            }
        }

        $city_slug_val = 'unknown-city';
        $city_terms = get_the_terms( $post->ID, 'city' );
        if ( ! empty( $city_terms ) && ! is_wp_error( $city_terms ) ) {
            $city_slug_val = current( $city_terms )->slug;
        }

        $compound_slug_val = 'unknown-compound';
        $compound_terms = get_the_terms( $post->ID, 'compound' );
        if ( ! empty( $compound_terms ) && ! is_wp_error( $compound_terms ) ) {
            $compound_slug_val = current( $compound_terms )->slug;
        }

        $hide_default_lang_slug = function_exists('pll_is_language_hidden') && pll_is_language_hidden($lang_slug);

        if ($hide_default_lang_slug) {
            $post_link = home_url( user_trailingslashit( "property/{$city_slug_val}/{$compound_slug_val}/" . $post->ID ) );
        } else {
            $post_link = home_url( user_trailingslashit( "{$lang_slug}/property/{$city_slug_val}/{$compound_slug_val}/" . $post->ID ) );
        }

        return $post_link;
    }
    add_filter( 'post_type_link', 'cob_properties_permalink_unambiguous', 10, 4 );
}

/**
 * Customize term links for 'compound' and 'city' taxonomies to match the new structure.
 */
if ( ! function_exists( 'cob_custom_term_link_unambiguous' ) ) {
    function cob_custom_term_link_unambiguous( $termlink, $term, $taxonomy ) {

        $lang_slug = 'en';
        if ( function_exists( 'pll_get_term_language' ) ) {
            $current_lang = pll_get_term_language( $term->term_id, 'slug' );
            if ( ! empty( $current_lang ) ) {
                $lang_slug = $current_lang;
            } elseif ( function_exists('pll_default_language') ) {
                $lang_slug = pll_default_language('slug');
            }
        }
        $hide_default_lang_slug = function_exists('pll_is_language_hidden') && pll_is_language_hidden($lang_slug);

        // Handle 'compound' taxonomy links: /lang/compound/city-slug/compound-slug/
        if ( 'compound' === $taxonomy ) {
            $city_term_id = get_term_meta( $term->term_id, 'compound_city', true );
            $city_slug_val = 'unknown-city';
            if ( $city_term_id && is_numeric($city_term_id) ) {
                $city_term_obj = get_term( (int) $city_term_id, 'city' );
                if ( $city_term_obj && ! is_wp_error( $city_term_obj ) ) {
                    $city_slug_val = $city_term_obj->slug;
                }
            }
            $compound_slug_val = $term->slug;

            if ($hide_default_lang_slug) {
                return home_url( user_trailingslashit( "compound/{$city_slug_val}/{$compound_slug_val}" ) );
            } else {
                return home_url( user_trailingslashit( "{$lang_slug}/compound/{$city_slug_val}/{$compound_slug_val}" ) );
            }
        }

        // Handle 'city' taxonomy links: /lang/city/city-slug/
        if ( 'city' === $taxonomy ) {
            $city_slug_val = $term->slug;
            if ($hide_default_lang_slug) {
                return home_url( user_trailingslashit( "city/{$city_slug_val}" ) );
            } else {
                return home_url( user_trailingslashit( "{$lang_slug}/city/{$city_slug_val}" ) );
            }
        }

        return $termlink; // Return original link for any other taxonomy
    }
    add_filter( 'term_link', 'cob_custom_term_link_unambiguous', 10, 3 );
}


/**
 * IMPORTANT: After adding or modifying rewrite rules, you MUST flush WordPress's
 * rewrite rules for the changes to take effect.
 * You can do this by visiting the "Settings" > "Permalinks" page in the
 * WordPress admin area and simply clicking the "Save Changes" button.
 * Do this EVERY TIME you change these rules.
 */
?>
