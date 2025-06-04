<?php
function cob_edit_compound_taxonomy_description($term) {
	$taxonomy = 'compound';
	if (isset($_GET['taxonomy']) && $_GET['taxonomy'] === $taxonomy) {
		$description = term_description($term->term_id, $taxonomy);
		?>
		<tr class="form-field term-description-wrap">
			<th scope="row"><label for="description"><?php _e('Description', 'cob_theme'); ?></label></th>
			<td>
				<?php
				wp_editor(
					$description,
					'compound_description',
					[
						'textarea_name' => 'description',
						'quicktags'     => true,
						'media_buttons' => true,
						'tinymce'       => true,
					]
				);
				?>
				<p class="description"><?php _e('The description is not prominent by default, but some themes may show it.', 'cob_theme'); ?></p>
			</td>
		</tr>
		<?php
	}
}
add_action('compound_edit_form_fields', 'cob_edit_compound_taxonomy_description');

function cob_add_compound_taxonomy_description() {
	if (isset($_GET['taxonomy']) && $_GET['taxonomy'] === 'compound') {
		?>
		<div class="form-field term-description-wrap">
			<label for="description"><?php _e('Description', 'cob_theme'); ?></label>
			<?php
			wp_editor(
				'',
				'compound_description',
				[
					'textarea_name' => 'description',
					'quicktags'     => true,
					'media_buttons' => true,
					'tinymce'       => true,
				]
			);
			?>
			<p class="description"><?php _e('The description is not prominent by default, but some themes may show it.', 'cob_theme'); ?></p>
		</div>
		<?php
	}
}
add_action('compound_add_form_fields', 'cob_add_compound_taxonomy_description');

remove_filter('pre_term_description', 'wp_filter_kses');
remove_filter('term_description', 'wp_kses_data');
