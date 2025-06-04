<?php
/**
 * WordPress Permalink and Rewrite Rule Customizations for Cob Theme.
 * This file includes functions to add rewrite tags, custom query variables,
 * and custom rewrite rules for properties and compound taxonomy archives.
 */

/**
 * Add rewrite tags for language, city, and compound.
 * These tags can be used in permalink structures and rewrite rules.
 * %lang%: Matches 'ar' or 'en'.
 * %city%: Matches any character except '/'.
 * %compound%: Matches any character except '/'.
 */
if ( ! function_exists( 'cob_add_rewrite_tags' ) ) {
    function cob_add_rewrite_tags() {
        // Language tag (e.g., 'en', 'ar')
        add_rewrite_tag( '%lang%', '(ar|en)' );
        // City tag (expects a slug)
        add_rewrite_tag( '%city%', '([^/]+)' );
        // Compound tag (expects a slug)
        add_rewrite_tag( '%compound%', '([^/]+)' );
    }
    add_action( 'init', 'cob_add_rewrite_tags' );
}

/**
 * Add custom query variables to WordPress.
 * This makes 'lang', 'city', and 'compound' recognizable as public query variables.
 *
 * @param array $vars Existing public query variables.
 * @return array Modified query variables.
 */
if ( ! function_exists( 'cob_add_query_vars' ) ) {
    function cob_add_query_vars( $vars ) {
        $vars[] = 'lang';     // For language code
        $vars[] = 'city';     // For city slug
        $vars[] = 'compound'; // For compound slug (can also be used by WP for taxonomy term)
        return $vars;
    }
    add_filter( 'query_vars', 'cob_add_query_vars' );
}

/**
 * Add rewrite rule for single 'properties' posts including language.
 * User renamed this from cob_add_properties_rewrite_rule_with_lang.
 * Matches URLs like: /{lang_code}/{city_slug}/{compound_slug}/{post_id}/
 * Example: /en/el-sheikh-zayed/arkan-palm/123/
 */
if ( ! function_exists( 'cob_add_properties_rewrite_rule' ) ) {
    function cob_add_properties_rewrite_rule() {
        add_rewrite_rule(
        // Regex: Matches two characters for lang, then city, then compound, then numeric post ID.
            '^([^/]{2})/([^/]+)/([^/]+)/([0-9]+)/?$',
            // Query: Maps to properties post type, sets post ID (p), lang, city, and compound query vars.
            'index.php?post_type=properties&p=$matches[4]&lang=$matches[1]&city=$matches[2]&compound=$matches[3]',
            'top' // Add this rule with high priority.
        );
    }
    add_action( 'init', 'cob_add_properties_rewrite_rule' );
}

/**
 * Add Custom Rewrite Rule for Properties (without language in the slug directly).
 * User renamed this from cob_add_properties_rewrite_rule_no_lang.
 * This rule matches URLs of the form: /{city}/{compound}/{post-ID}
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


/**
 * Add rewrite rule for 'compound' taxonomy archives with city and language in the URL.
 * This is the key rule to help WordPress recognize the custom compound archive URL structure.
 * Matches URLs like: /{lang_code}/{city_slug}/{compound_slug}/
 * Example: /en/el-sheikh-zayed/arkan-palm/
 */
if ( ! function_exists( 'cob_compound_taxonomy_archive_rewrite_rule' ) ) {
    function cob_compound_taxonomy_archive_rewrite_rule() {
        add_rewrite_rule(
        // Regex: Matches two characters for lang, then city slug, then compound slug.
            '^([^/]{2})/([^/]+)/([^/]+)/?$',
            // Query:
            // - taxonomy=compound: Tells WP this is for the 'compound' taxonomy.
            // - term=$matches[3]: Sets the term slug for the 'compound' taxonomy (WordPress uses 'term' for the slug here).
            // - lang=$matches[1]: Sets the language query variable.
            // - city=$matches[2]: Sets the city query variable.
            'index.php?taxonomy=compound&term=$matches[3]&lang=$matches[1]&city=$matches[2]',
            'top' // Add this rule with high priority.
        );
    }
    add_action( 'init', 'cob_compound_taxonomy_archive_rewrite_rule' );
}


/**
 * Customize the permalink for 'properties' post type.
 * Generates permalinks like: /{lang_code}/{city_slug}/{compound_slug}/{post_id}/
 *
 * @param string  $post_link The original post link.
 * @param WP_Post $post      The post object.
 * @param bool    $leavename Whether to keep the post name.
 * @param bool    $sample    Is it a sample permalink.
 * @return string The customized post link.
 */
if ( ! function_exists( 'cob_properties_permalink' ) ) {
    function cob_properties_permalink( $post_link, $post, $leavename, $sample ) {
        // Only modify permalinks for the 'properties' post type.
        if ( 'properties' !== $post->post_type ) {
            return $post_link;
        }

        // Determine the language slug using the post's language.
        $lang_slug = 'en'; // Default language.
        if ( function_exists( 'pll_get_post_language' ) ) {
            $current_lang = pll_get_post_language( $post->ID, 'slug' );
            if ( ! empty( $current_lang ) ) {
                $lang_slug = $current_lang;
            } elseif ( function_exists('pll_default_language') ) {
                // Fallback to default Polylang language slug if post has no language explicitly set.
                $lang_slug = pll_default_language('slug');
            }
        }

        // Get the city slug associated with the property.
        $city_slug_val = 'unknown-city'; // Fallback if no city term is found.
        $city_terms = get_the_terms( $post->ID, 'city' );
        if ( ! empty( $city_terms ) && ! is_wp_error( $city_terms ) ) {
            $city_slug_val = current( $city_terms )->slug;
        }

        // Get the compound slug associated with the property.
        $compound_slug_val = 'unknown-compound'; // Fallback if no compound term is found.
        $compound_terms = get_the_terms( $post->ID, 'compound' );
        if ( ! empty( $compound_terms ) && ! is_wp_error( $compound_terms ) ) {
            $compound_slug_val = current( $compound_terms )->slug;
        }

        // Construct the new permalink using the post ID.
        $post_link = home_url( user_trailingslashit( "{$lang_slug}/{$city_slug_val}/{$compound_slug_val}/" . $post->ID ) );

        return $post_link;
    }
    add_filter( 'post_type_link', 'cob_properties_permalink', 10, 4 );
}

/**
 * Customize the term link for 'compound' taxonomy.
 * Generates term links like: /{lang_code}/{city_slug}/{compound_slug}/
 *
 * @param string  $termlink The original term link.
 * @param WP_Term $term     The term object.
 * @param string  $taxonomy The taxonomy slug.
 * @return string The customized term link.
 */
if ( ! function_exists( 'cob_compound_term_link' ) ) {
    function cob_compound_term_link( $termlink, $term, $taxonomy ) {
        // Only modify links for the 'compound' taxonomy.
        if ( 'compound' !== $taxonomy ) {
            return $termlink;
        }

        // Determine the language slug for the term.
        $lang_slug = 'en'; // Default language.
        if ( function_exists( 'pll_get_term_language' ) ) {
            $current_lang = pll_get_term_language( $term->term_id, 'slug' );
            if ( ! empty( $current_lang ) ) {
                $lang_slug = $current_lang;
            } elseif ( function_exists('pll_default_language') ) {
                // Fallback to default Polylang language slug if term has no language explicitly set.
                $lang_slug = pll_default_language('slug');
            }
        }

        // Get the city associated with this compound term via term meta.
        // Assumes 'compound_city' meta key stores the term_id of the linked city.
        $city_term_id = get_term_meta( $term->term_id, 'compound_city', true );

        if ( $city_term_id ) {
            $city_term_obj = get_term( $city_term_id, 'city' ); // Get the city term object.
            if ( $city_term_obj && ! is_wp_error( $city_term_obj ) ) {
                $city_slug_val     = $city_term_obj->slug;
                $compound_slug_val = $term->slug; // Slug of the current compound term.
                // Construct the new term link.
                $termlink = home_url( user_trailingslashit( "{$lang_slug}/{$city_slug_val}/{$compound_slug_val}" ) );
            } else {
                // City term not found or error. Log or handle as needed.
                // The original $termlink (WordPress default) will be returned in this case.
                // error_log("Could not find city term (ID: {$city_term_id}) for compound {$term->slug} (ID: {$term->term_id})");
            }
        }
        // If 'compound_city' meta is not set or city term is invalid, the original $termlink will be returned.
        return $termlink;
    }
    add_filter( 'term_link', 'cob_compound_term_link', 10, 3 );
}


/**
 * Optional: pre_get_posts action to further refine the query on compound taxonomy archives.
 * This function can be used if you need to ensure properties listed on a
 * /lang/city-slug/compound-slug/ page are also filtered by the city from the URL,
 * in addition to the compound term itself. The rewrite rule above should handle loading the correct template,
 * but this ensures the main query for posts on that page is correctly filtered.
 */
if ( ! function_exists( 'cob_filter_compound_archive_by_city_if_needed' ) ) {
    function cob_filter_compound_archive_by_city_if_needed( $query ) {
        // Check if it's the main query, on the frontend, and for the 'compound' taxonomy archive.
        if ( ! is_admin() && $query->is_main_query() && $query->is_tax('compound') ) {

            $city_slug_from_url = get_query_var( 'city' ); // Get city slug from URL (set by our rewrite rule and query_vars filter)
            $queried_compound_term_obj = $query->get_queried_object(); // Get the main compound term object being queried

            if ( ! empty( $city_slug_from_url ) && $queried_compound_term_obj instanceof WP_Term ) {
                // Optional: Validate if the city in URL matches the city linked to the compound via meta.
                // This adds an extra layer of consistency.
                $linked_city_id = get_term_meta( $queried_compound_term_obj->term_id, 'compound_city', true );
                if ( $linked_city_id ) {
                    $linked_city_term = get_term( $linked_city_id, 'city' );
                    if ( $linked_city_term && ! is_wp_error( $linked_city_term ) && $linked_city_term->slug === $city_slug_from_url ) {
                        // The city in the URL matches the compound's linked city.
                        // Now, ensure the main query for posts (e.g., 'properties') is filtered by this city as well.

                        $tax_query_array = $query->get( 'tax_query' );
                        if ( ! is_array( $tax_query_array ) ) {
                            $tax_query_array = [];
                        }

                        // Add the city filter to the existing tax query.
                        // WordPress would have already added the 'compound' taxonomy filter.
                        $tax_query_array[] = [
                            'taxonomy' => 'city',
                            'field'    => 'slug',
                            'terms'    => $city_slug_from_url,
                        ];

                        // If there are multiple taxonomy conditions, set the relation to 'AND'.
                        if ( count( $tax_query_array ) > 1 ) {
                            $tax_query_array['relation'] = 'AND';
                        }

                        $query->set( 'tax_query', $tax_query_array );
                    } else {
                        // Mismatch between city in URL and compound's linked city.
                        // This could indicate an invalid URL. You might want to trigger a 404.
                        // $query->set_404();
                        // error_log( "Permalink/Query Mismatch: City in URL ('{$city_slug_from_url}') does not match linked city for compound '{$queried_compound_term_obj->slug}'." );
                    }
                } else {
                    // Compound does not have a 'compound_city' meta value.
                    // Decide how to handle this: maybe it's okay, or maybe it should be an error/404.
                    // error_log( "Missing 'compound_city' meta for compound '{$queried_compound_term_obj->slug}'." );
                }
            }
        }
    }
    // To activate this filter, uncomment the line below.
    // This is useful if you want to list 'properties' on the compound archive page
    // and ensure they are filtered by both the compound AND the city from the URL.
    // add_action( 'pre_get_posts', 'cob_filter_compound_archive_by_city_if_needed' );
}

/**
 * IMPORTANT: After adding or modifying rewrite rules, you MUST flush WordPress's
 * rewrite rules for the changes to take effect.
 * You can do this by visiting the "Settings" > "Permalinks" page in the
 * WordPress admin area and simply clicking the "Save Changes" button.
 * Alternatively, for programmatic flushing (e.g., on theme activation):
 * // register_activation_hook( __FILE__, 'cob_flush_rewrite_rules_on_activation' );
 * // function cob_flush_rewrite_rules_on_activation() {
 * //     cob_add_rewrite_tags();
 * //     cob_add_properties_rewrite_rule();
 * //     cob_custom_properties_rewrite_rule();
 * //     cob_compound_taxonomy_archive_rewrite_rule();
 * //     flush_rewrite_rules();
 * // }
 * // Remember that flush_rewrite_rules() is an expensive operation and should not be run on every page load.
 */

?>
