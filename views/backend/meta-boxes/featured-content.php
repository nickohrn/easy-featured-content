<input type="hidden"
		name="featured-content[is-featured-content]"
		id="featured-content-is-featured-content-no"
		value="no" />
<label>
	<input type="checkbox"
			<?php checked(true, $is_featured_content); ?>
			name="featured-content[is-featured-content]"
			id="featured-content-is-featured-content-yes"
			value="yes" />

	<?php _e('This content should be featured'); ?>
</label>

<?php wp_nonce_field('featured-content-save-meta', 'featured-content-save-meta-nonce');
