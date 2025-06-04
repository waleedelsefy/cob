<?php
/**
 * Custom Post Types and Taxonomies Registration - Custom Permalink with Language/City/Compound/PostID structure.
 *
 * @package Capital_of_Business
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Rewrite Tags for Compound and City.
 *
 * (Optional – if needed for other custom rules.)
 */


/**
 * Register Compound Taxonomy.
 */
function cob_register_compound_taxonomy() {
	$labels = array(
		'name'              => __( 'Compounds', 'cob_theme' ),
		'singular_name'     => __( 'Compound', 'cob_theme' ),
		'search_items'      => __( 'Search Compounds', 'cob_theme' ),
		'all_items'         => __( 'All Compounds', 'cob_theme' ),
		'parent_item'       => __( 'Parent Compound', 'cob_theme' ),
		'parent_item_colon' => __( 'Parent Compound:', 'cob_theme' ),
		'edit_item'         => __( 'Edit Compound', 'cob_theme' ),
		'update_item'       => __( 'Update Compound', 'cob_theme' ),
		'add_new_item'      => __( 'Add New Compound', 'cob_theme' ),
		'new_item_name'     => __( 'New Compound Name', 'cob_theme' ),
		'menu_name'         => __( 'Compounds', 'cob_theme' ),
	);

	$args = array(
		'labels'            => $labels,
		'public'            => true,
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'compound' ),
		'show_in_rest'      => true,
	);

	register_taxonomy( 'compound', array( 'properties' ), $args );
}
add_action( 'init', 'cob_register_compound_taxonomy' );

/**
 * Register Properties Custom Post Type.
 *
 * We disable the automatic rewrite rules by setting 'rewrite' => false.
 * The final URL structure will be:
 * /{city}/{compound}/{post-ID}
 * Polylang will add the language prefix automatically.
 */
function cob_register_properties_cpt() {
	$labels = array(
		'name'          => __( 'Properties', 'cob_theme' ),
		'singular_name' => __( 'Property', 'cob_theme' ),
		'add_new'       => __( 'Add New Property', 'cob_theme' ),
		'add_new_item'  => __( 'Add New Property', 'cob_theme' ),
		'edit_item'     => __( 'Edit Property', 'cob_theme' ),
		'new_item'      => __( 'New Property', 'cob_theme' ),
		'view_item'     => __( 'View Property', 'cob_theme' ),
		'all_items'     => __( 'All Properties', 'cob_theme' ),
		'search_items'  => __( 'Search Properties', 'cob_theme' ),
		'not_found'     => __( 'No properties found.', 'cob_theme' ),
	);

	$args = array(
		'labels'        => $labels,
		'public'        => true,
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-building',
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		// Disable default rewrite rules
		'rewrite'       => false,
		'show_in_rest'  => true,
		'hierarchical'  => false,
	);

	register_post_type( 'properties', $args );
}
add_action( 'init', 'cob_register_properties_cpt' );

/**
 * Register Factory Custom Post Type.
 */
function cob_register_factory_cpt() {
	$labels = array(
		'name'          => __( 'Factories', 'cob_theme' ),
		'singular_name' => __( 'Factory', 'cob_theme' ),
		'add_new'       => __( 'Add New Factory', 'cob_theme' ),
		'add_new_item'  => __( 'Add New Factory', 'cob_theme' ),
		'edit_item'     => __( 'Edit Factory', 'cob_theme' ),
		'new_item'      => __( 'New Factory', 'cob_theme' ),
		'view_item'     => __( 'View Factory', 'cob_theme' ),
		'all_items'     => __( 'All Factories', 'cob_theme' ),
		'search_items'  => __( 'Search Factories', 'cob_theme' ),
		'not_found'     => __( 'No factories found.', 'cob_theme' ),
	);

	$args = array(
		'labels'        => $labels,
		'public'        => true,
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-store',
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'rewrite'       => array( 'slug' => 'factories' ),
		'show_in_rest'  => true,
	);

	register_post_type( 'factory', $args );
}
add_action( 'init', 'cob_register_factory_cpt' );

/**
 * Register Lands Custom Post Type.
 */
function cob_register_land_cpt() {
	$labels = array(
		'name'          => __( 'Lands', 'cob_theme' ),
		'singular_name' => __( 'Land', 'cob_theme' ),
		'add_new'       => __( 'Add New Land', 'cob_theme' ),
		'add_new_item'  => __( 'Add New Land', 'cob_theme' ),
		'edit_item'     => __( 'Edit Land', 'cob_theme' ),
		'new_item'      => __( 'New Land', 'cob_theme' ),
		'view_item'     => __( 'View Land', 'cob_theme' ),
		'all_items'     => __( 'All Lands', 'cob_theme' ),
		'search_items'  => __( 'Search Lands', 'cob_theme' ),
		'not_found'     => __( 'No lands found.', 'cob_theme' ),
	);

	$args = array(
		'labels'        => $labels,
		'public'        => true,
		'has_archive'   => true,
		'menu_icon'     => 'dashicons-palmtree',
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'rewrite'       => array( 'slug' => 'lands' ),
		'show_in_rest'  => true,
	);

	register_post_type( 'lands', $args );
}
add_action( 'init', 'cob_register_land_cpt' );

/**
 * Register Custom Taxonomies: City, Developer, Finishing, Type.
 */
function cob_register_taxonomies() {
	$default_args = array(
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => true,
		'show_in_rest'      => true,
	);

	// City Taxonomy
	register_taxonomy( 'city', array( 'lands', 'properties', 'factory', 'posts' ), array_merge( $default_args, array(
		'labels' => array(
			'name'                       => __( 'Cities', 'cob_theme' ),
			'singular_name'              => __( 'City', 'cob_theme' ),
			'search_items'               => __( 'Search Cities', 'cob_theme' ),
			'all_items'                  => __( 'All Cities', 'cob_theme' ),
			'parent_item'                => __( 'Parent City', 'cob_theme' ),
			'parent_item_colon'          => __( 'Parent City:', 'cob_theme' ),
			'edit_item'                  => __( 'Edit City', 'cob_theme' ),
			'view_item'                  => __( 'View City', 'cob_theme' ),
			'update_item'                => __( 'Update City', 'cob_theme' ),
			'add_new_item'               => __( 'Add New City', 'cob_theme' ),
			'new_item_name'              => __( 'New City Name', 'cob_theme' ),
			'menu_name'                  => __( 'Cities', 'cob_theme' ),
			'separate_items_with_commas' => __( 'Separate cities with commas', 'cob_theme' ),
		),
		'rewrite' => array( 'slug' => 'city' ),
	) ) );

	// Developer Taxonomy
	register_taxonomy( 'developer', array( 'lands', 'properties', 'factory', 'posts' ), array_merge( $default_args, array(
		'labels' => array(
			'name'                       => __( 'Developers', 'cob_theme' ),
			'singular_name'              => __( 'Developer', 'cob_theme' ),
			'search_items'               => __( 'Search Developers', 'cob_theme' ),
			'all_items'                  => __( 'All Developers', 'cob_theme' ),
			'parent_item'                => __( 'Parent Developer', 'cob_theme' ),
			'parent_item_colon'          => __( 'Parent Developer:', 'cob_theme' ),
			'edit_item'                  => __( 'Edit Developer', 'cob_theme' ),
			'view_item'                  => __( 'View Developer', 'cob_theme' ),
			'update_item'                => __( 'Update Developer', 'cob_theme' ),
			'add_new_item'               => __( 'Add New Developer', 'cob_theme' ),
			'new_item_name'              => __( 'New Developer Name', 'cob_theme' ),
			'menu_name'                  => __( 'Developers', 'cob_theme' ),
			'separate_items_with_commas' => __( 'Separate Developers with commas', 'cob_theme' ),
		),
		'rewrite' => array( 'slug' => 'developer' ),
	) ) );

	// Finishing Taxonomy
	register_taxonomy( 'finishing', array( 'projects', 'properties' ), array_merge( $default_args, array(
		'labels' => array(
			'name'                       => __( 'Finishing Types', 'cob_theme' ),
			'singular_name'              => __( 'Finishing Type', 'cob_theme' ),
			'search_items'               => __( 'Search Finishing Types', 'cob_theme' ),
			'all_items'                  => __( 'All Finishing Types', 'cob_theme' ),
			'edit_item'                  => __( 'Edit Finishing Type', 'cob_theme' ),
			'view_item'                  => __( 'View Finishing Type', 'cob_theme' ),
			'update_item'                => __( 'Update Finishing Type', 'cob_theme' ),
			'add_new_item'               => __( 'Add New Finishing Type', 'cob_theme' ),
			'new_item_name'              => __( 'New Finishing Type Name', 'cob_theme' ),
			'menu_name'                  => __( 'Finishing Types', 'cob_theme' ),
			'separate_items_with_commas' => __( 'Separate Finishing Types with commas', 'cob_theme' ),
		),
		'rewrite' => array( 'slug' => 'finishing' ),
	) ) );

	// Type Taxonomy
	register_taxonomy( 'type', array( 'lands', 'properties', 'posts' ), array_merge( $default_args, array(
		'labels' => array(
			'name'          => __( 'Types', 'cob_theme' ),
			'singular_name' => __( 'Type', 'cob_theme' ),
		),
		'rewrite' => array( 'slug' => 'type' ),
	) ) );
}
add_action( 'init', 'cob_register_taxonomies' );

/**
 * Update Property Views with Rate Limiting.
 */
function cob_update_project_views() {
	if ( is_admin() || ! is_singular( 'properties' ) ) {
		return;
	}

	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return;
	}

	$cookie_name = 'properties_viewed_' . $post_id;
	if ( isset( $_COOKIE[ $cookie_name ] ) ) {
		return;
	}

	$views = (int) get_post_meta( $post_id, 'properties_views', true );
	$views++;
	update_post_meta( $post_id, 'properties_views', $views );

	setcookie( $cookie_name, 1, time() + HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
}
add_action( 'template_redirect', 'cob_update_project_views' );
