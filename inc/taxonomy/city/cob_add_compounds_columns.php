<?php

function cob_add_compound_columns($columns) {
	$columns['compound_developer'] = __('Developer', 'cob_theme');
	$columns['compound_city'] = __('City', 'cob_theme');
	return $columns;
}
add_filter('manage_edit-compound_columns', 'cob_add_compound_columns');

function cob_show_compound_column_content($content, $column_name, $term_id) {
	if ($column_name === 'compound_developer') {
		$developer_id = get_term_meta($term_id, 'compound_developer', true);
		if (!empty($developer_id)) {
			$developer = get_term($developer_id);

			if ($developer && !is_wp_error($developer) && is_object($developer) && property_exists($developer, 'name')) {
				$content = esc_html($developer->name);
			} else {
				$content = __('Not Assigned', 'cob_theme');
			}
		} else {
			$content = __('Not Assigned', 'cob_theme');
		}
	}

	if ($column_name === 'compound_city') {
		$city_id = get_term_meta($term_id, 'compound_city', true);
		if (!empty($city_id)) {
			$city = get_term($city_id);

			if ($city && !is_wp_error($city) && is_object($city) && property_exists($city, 'name')) {
				$content = esc_html($city->name);
			} else {
				$content = __('Not Assigned', 'cob_theme');
			}
		} else {
			$content = __('Not Assigned', 'cob_theme');
		}
	}

	return $content;
}
add_filter('manage_compound_custom_column', 'cob_show_compound_column_content', 10, 3);
