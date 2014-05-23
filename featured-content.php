<?php
/*
Plugin Name: Easy Featured Content
Description: Easily mark any content on your site featured with a simple checkbox or link.
Version: 1.1.0
Author: Nick Ohrn of Plugin-Developer.com
Author URI: http://plugin-developer.com/
*/

if(!class_exists('Featured_Content')) {
	class Featured_Content {
		/// CONSTANTS

		//// VERSION
		const VERSION = '1.1.0';

		//// KEYS
		const IS_FEATURED_CONTENT_KEY = '_is_featured_content';

		//// CACHE
		const CACHE_PERIOD = 86400; // 24 HOURS

		/// DATA STORAGE
		private static $admin_page_hooks = array('edit.php');

		public static function init() {
			self::add_actions();
			self::add_filters();
		}

		private static function add_actions() {
			if(is_admin()) {
				add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
				add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_administrative_resources'));
			}

			add_action('after_setup_theme', array(__CLASS__, 'add_post_type_support'), 9);
			add_action('save_post', array(__CLASS__, 'save_post_meta'), 10, 2);
			add_action('wp_ajax_featured-content', array(__CLASS__, 'ajax_featured_content_toggle'));
		}

		private static function add_filters() {
			add_filter('pre_get_posts', array(__CLASS__, 'transform_query_variables'));

			add_filter('page_row_actions', array(__CLASS__, 'add_featured_content_toggle'), 10, 2);
			add_filter('post_row_actions', array(__CLASS__, 'add_featured_content_toggle'), 10, 2);
		}

		/// AJAX CALLBACKS

		public static function ajax_featured_content_toggle() {
			$data = stripslashes_deep($_REQUEST);

			$is_ajax = isset($data['is-ajax']);

			$is_featured_content = isset($data['is-featured-content']) && 'yes' === $data['is-featured-content'] ? 'yes' : 'no';
			$is_featured_content_bool = 'yes' === $is_featured_content;

			$nonce = isset($data['featured-content-toggle-nonce']) ? $data['featured-content-toggle-nonce'] : false;

			$post_id = isset($data['post-id']) ? $data['post-id'] : 0;
			$post = get_post($post_id);
			$post_type_object = get_post_type_object($post->post_type);

			$can_edit_post = current_user_can($post_type_object->cap->edit_post, $post->ID);
			$supports_featured_content = post_type_supports($post->post_type, 'featured-content');

			if($can_edit_post && $supports_featured_content && wp_verify_nonce($nonce, 'featured-content-toggle')) {
				self::_set_is_featured_content($post_id, $is_featured_content);
			}

			$is_featured_content_bool = self::is_featured_content($post_id);

			if($is_ajax) {
				$results = array(
					'link' => self::_get_ajax_toggle_link($post_id, $is_featured_content_bool),
					'text' => $is_featured_content_bool ? __('Unfeature') : __('Feature')
				);

				echo json_encode($results);
			} else {
				wp_redirect(get_edit_post_link($post_id, 'raw')); exit;
			}

			exit;
		}

		private static function _get_ajax_toggle_link($post_id, $is_featured_content_bool) {
			return wp_nonce_url(add_query_arg(array(
				'action' => 'featured-content',
				'is-featured-content' => $is_featured_content_bool ? 'no' : 'yes',
				'post-id' => $post_id
			), admin_url('admin-ajax.php')), 'featured-content-toggle', 'featured-content-toggle-nonce');
		}

		/// CALLBACKS

		public static function add_featured_content_toggle($actions, $post) {
			$post_type_object = get_post_type_object($post->post_type);

			$can_edit_post = current_user_can($post_type_object->cap->edit_post, $post->ID);
			$supports_featured_content = post_type_supports($post->post_type, 'featured-content');

			if($can_edit_post && $supports_featured_content) {
				$is_featured_content = self::is_featured_content($post->ID);
				$link = self::_get_ajax_toggle_link($post->ID, $is_featured_content);
				$text = $is_featured_content ? __('Unfeature') : __('Feature');

				$actions['featured-content'] = sprintf('<a class="is-featured-content-toggle" href="%s">%s</a>', $link, $text);
			}

			return $actions;
		}

		public static function add_meta_boxes($post_type) {
			if(post_type_supports($post_type, 'featured-content')) {
				add_meta_box('featured-content-meta-box', __('Featured'), array(__CLASS__, 'display_meta_box'), $post_type, 'side', 'core');
			}
		}

		public static function add_post_type_support() {
			add_post_type_support('page', 'featured-content');
			add_post_type_support('post', 'featured-content');
		}

		public static function enqueue_administrative_resources($hook) {
			if(!in_array($hook, self::$admin_page_hooks)) { return; }

			wp_enqueue_script('featured-content-backend', plugins_url('resources/backend/featured-content.js', __FILE__), array('jquery'), self::VERSION);
			wp_localize_script('featured-content-backend', 'Featured_Content', array(
				'feature_text' => __('Feature'),
				'unfeature_text' => __('Unfeature'),
			));
		}

		public static function save_post_meta($post_id, $post) {
			$data = stripslashes_deep($_POST);
			if(wp_is_post_autosave($post_id)
				|| wp_is_post_revision($post_id)
				|| !post_type_supports($post->post_type, 'featured-content')
				|| !isset($data['featured-content-save-meta-nonce'])
				|| !wp_verify_nonce($data['featured-content-save-meta-nonce'], 'featured-content-save-meta')) {
				return;
			}

			self::_set_is_featured_content($post_id, $data['featured-content']['is-featured-content']);
		}

		public static function transform_query_variables($wp_query) {
			if(isset($wp_query->query_vars['is_featured']) && $wp_query->query_vars['is_featured']) {
				if(!isset($wp_query->query_vars['meta_query']) || !is_array($wp_query->query_vars['meta_query'])) {
					$wp_query->query_vars['meta_query'] = array();
				}

				if('yes' === $wp_query->query_vars['is_featured']) {
					$wp_query->query_vars['meta_query'][] = array(
						'compare' => '=',
						'key' => self::IS_FEATURED_CONTENT_KEY,
						'value' => 'yes',
					);
				} else {
					$wp_query->query_vars['meta_query'][] = array(
						'compare' => 'NOT EXISTS',
						'key' => self::IS_FEATURED_CONTENT_KEY,
					);
				}
			}
		}

		/// DISPLAY CALLBACKS

		public static function display_meta_box($post) {
			$is_featured_content = self::is_featured_content($post->ID);

			include('views/backend/meta-boxes/featured-content.php');
		}

		/// POST META

		private static function _get_is_featured_content($post_id) {
			$post_id = empty($post_id) && in_the_loop() ? get_the_ID() : $post_id;

			if($post_id) {
				$is_featured_content = wp_cache_get(self::IS_FEATURED_CONTENT_KEY, $post_id);

				if(false === $is_featured_content) {
					$is_featured_content = 'yes' === get_post_meta($post_id, self::IS_FEATURED_CONTENT_KEY, true) ? 'yes' : 'no';
					wp_cache_set(self::IS_FEATURED_CONTENT_KEY, $is_featured_content, $post_id, time() + self::CACHE_PERIOD);
				}
			} else {
				$is_featured_content = null;
			}


			return 'yes' === $is_featured_content;
		}

		private static function _set_is_featured_content($post_id, $is_featured_content) {
			$post_id = empty($post_id) && in_the_loop() ? get_the_ID() : $post_id;

			if($post_id) {
				$is_featured_content = 'yes' === $is_featured_content ? 'yes' : 'no';

				if('yes' === $is_featured_content) {
					update_post_meta($post_id, self::IS_FEATURED_CONTENT_KEY, $is_featured_content);
				} else {
					delete_post_meta($post_id, self::IS_FEATURED_CONTENT_KEY);
				}

				wp_cache_delete(self::IS_FEATURED_CONTENT_KEY, $post_id);
			} else {
				$is_featured_content = false;
			}

			return $is_featured_content;
		}

		/// TEMPLATE TAGS

		public static function is_featured_content($post_id) {
			return self::_get_is_featured_content($post_id);
		}
	}

	require_once('lib/template-tags.php');
	Featured_Content::init();
}
