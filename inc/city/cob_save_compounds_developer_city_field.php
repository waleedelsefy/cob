<?php
function cob_save_compound_developer_city_field($term_id) {
	if (isset($_POST['compound_developer'])) {
		update_term_meta($term_id, 'compound_developer', esc_attr($_POST['compound_developer']));
	}

	if (isset($_POST['compound_city'])) {
		update_term_meta($term_id, 'compound_city', esc_attr($_POST['compound_city']));
	}
}
add_action('created_compound', 'cob_save_compound_developer_city_field');
add_action('edited_compound', 'cob_save_compound_developer_city_field');
