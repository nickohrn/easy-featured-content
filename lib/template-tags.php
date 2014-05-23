<?php

function featured_content_is_featured_content($post_id = null) {
	return apply_filters(__FUNCTION__, Featured_Content::is_featured_content($post_id), $post_id);
}