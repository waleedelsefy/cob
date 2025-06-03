<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*--------------------------------------------------
  CUSTOM POST TYPE: SERVICES
--------------------------------------------------*/
/**
 * Register 'services' custom post type.
 */
function cob_register_services_post_type() {
	$labels = [
		'name'          => __('Services', 'cob_theme'),
		'singular_name' => __('Service', 'cob_theme'),
		'add_new'       => __('Add New Service', 'cob_theme'),
		'add_new_item'  => __('Add New Service', 'cob_theme'),
		'edit_item'     => __('Edit Service', 'cob_theme'),
		'new_item'      => __('New Service', 'cob_theme'),
		'view_item'     => __('View Service', 'cob_theme'),
		'all_items'     => __('All Services', 'cob_theme'),
		'search_items'  => __('Search Services', 'cob_theme'),
		'not_found'     => __('No Services found.', 'cob_theme'),
	];

	$args = [
		'labels'              => $labels,
		'public'            => true,
		'hierarchical'      => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'services' ),
		'show_in_rest'      => true,
	];

	register_post_type('services', $args);
}
add_action('init', 'cob_register_services_post_type');

/**
 * Block direct access to single 'services' posts.
 */
function cob_block_services_redirect() {
	if (is_singular('services')) {
		wp_redirect(home_url(), 301);
		exit;
	}
}
add_action('template_redirect', 'cob_block_services_redirect');

/*--------------------------------------------------
  CUSTOM META BOX: SERVICE SVG ICON
--------------------------------------------------*/
/**
 * Add Service SVG Icon meta box.
 */
function cob_add_service_svg_metabox() {
	if (post_type_exists('services')) {
		add_meta_box(
			'cob_service_svg',
			__('Service SVG Icon', 'cob_theme'),
			'cob_render_service_svg_metabox',
			'services',
			'normal',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'cob_add_service_svg_metabox', 20);

/**
 * Render Service SVG Icon meta box.
 *
 * @param WP_Post $post The current post object.
 */
function cob_render_service_svg_metabox($post) {
	$svg_code = get_post_meta($post->ID, '_cob_service_svg', true);

	wp_nonce_field('cob_save_service_svg', 'cob_service_svg_nonce');
	?>
    <div>
        <label for="cob_service_svg"><?php _e('Insert SVG Code:', 'cob_theme'); ?></label>
        <textarea id="cob_service_svg" name="cob_service_svg" class="widefat" rows="12"><?php echo esc_textarea($svg_code); ?></textarea>
        <p class="description">
			<?php _e('Paste valid SVG code including <svg> tags. Example:', 'cob_theme'); ?>
            <br>
            <code>&lt;svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"&gt;...&lt;/svg&gt;</code>
        </p>
    </div>
	<?php
}

/**
 * Save Service SVG Icon meta box content.
 *
 * @param int $post_id The current post ID.
 */
function cob_save_service_svg($post_id) {
	if (!isset($_POST['post_type']) || $_POST['post_type'] !== 'services') {
		return;
	}

	if (!isset($_POST['cob_service_svg_nonce']) || !wp_verify_nonce($_POST['cob_service_svg_nonce'], 'cob_save_service_svg')) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (!current_user_can('edit_post', $post_id)) {
		return;
	}

	$allowed_svg = array(
		'svg' => array(
			'xmlns'           => true,
			'viewbox'         => true,
			'width'           => true,
			'height'          => true,
			'fill'            => true,
			'class'           => true,
			'id'              => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
		),
		'path' => array(
			'd'             => true,
			'fill'          => true,
			'stroke'        => true,
			'transform'     => true
		),
		'circle' => array(
			'cx'            => true,
			'cy'            => true,
			'r'             => true
		)
	);

	if (isset($_POST['cob_service_svg'])) {
		$svg_code = wp_kses($_POST['cob_service_svg'], $allowed_svg);
		update_post_meta($post_id, '_cob_service_svg', $svg_code);
	}
}
add_action('save_post', 'cob_save_service_svg', 10, 1);

/**
 * Retrieve Service SVG Icon.
 *
 * @param int $post_id The service post ID.
 * @return string The SVG code or an empty string.
 */
function cob_get_service_svg($post_id) {
	$svg_code = get_post_meta($post_id, '_cob_service_svg', true);

	if (!empty($svg_code) && strpos($svg_code, '<svg') !== false) {
		return $svg_code;
	}

	return '';
}

/*--------------------------------------------------
  POLYLANG SUPPORT FOR SERVICES CPT
--------------------------------------------------*/
/**
 * Make 'services' post type translatable with Polylang.
 *
 * This filter adds the 'services' CPT to the list of post types that Polylang considers translatable.
 *
 * @param array $post_types Existing post types registered for translation.
 * @return array Modified post types array including 'services'.
 */
function cob_polylang_services( $post_types ) {
	$post_types['services'] = true;
	return $post_types;
}
add_filter( 'pll_get_post_types', 'cob_polylang_services' );
