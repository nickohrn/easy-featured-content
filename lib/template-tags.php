<?php

function featured_content_is_featured_content($post_id = null) {
	return apply_filters('featured_content_is_featured_content', Featured_Content::is_featured_content($post_id));
}