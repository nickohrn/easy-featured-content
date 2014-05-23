=== Easy Featured Content ===
Contributors: nickohrn
Tags: featured-content, admin
Requires at least: 3.5.1
Tested up to: 3.9.1
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to mark content as featured and use the designation in your queries and via a template tag.

== Description ==

Quickly and easily mark any content that has an editing UI in the WordPress administrative section as featured. This plugin
also provides a template tag that you can use to display certain markup or styles for featured content and a query
variable that allows you to query for featured (or non-featured) content.

== Installation ==

1. Upload `featured-content` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Indicate featured content through the row actions on the content management screen or the meta box in the content editing screen
1. Use the query variable `is_featured` in your queries or the template tag `featured_content_is_featured_content` in your templates

== Frequently Asked Questions ==

= How do I make a post type featurable? =

By default, the plugin only allows posts and pages to be featured. However, if you have a custom post type, you can easily add featured content support by modifying the following snippet:

`function register_featured_content_support() {
	// Adds featured content support to the custom post type 'my-post-type-slug'
	add_post_type_support('my-post-type-slug', 'featured-content');

	// Removes featured content support from pages
	remove_post_type_support('page', 'featured-content');
}
add_filter('after_setup_theme', 'register_featured_content_support');`

= Why would I want to use this plugin? =

If you have content on your site that you want to feature (maybe on the home page or in a sidebar) then you can use this plugin
to enable marking that content as featured. It works for any post type that provides an editing UI.

= How do I use the query variable? =

In your custom queries (`new WP_Query`, `get_posts` or `query_posts`) simply pass in the query variable `is_featured` as follows:

`$featured_posts = new WP_Query(array('is_featured' => 'yes', 'post_type' => 'post'));`

If you need to get only non featured content, you would do something like the following:

`$non_featured_posts = new WP_Query(array('is_featured' => 'no', 'post_type' => 'post'));`

== Screenshots ==

1. This is the metabox that will appear on all editing screens after plugin activation
2. This screenshot shows the links you can click to feature or unfeature any content on your site

== Changelog ==

= 1.1.0 =
* Better security
* Code cleanup
* Use `has_post_type_support` and `add_post_type_support`
* Only posts and pages support featured content by default now

= 1.0.0 =
* Initial release of plugin

== Upgrade Notice ==

= 1.1.0 =
Only posts and pages can be featured by default now - if you wish to have other post types featurable, use `add_post_type_support` as detailed in the FAQ section

= 1.0.0 =
Initial release of plugin