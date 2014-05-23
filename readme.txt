=== Featured Content ===
Contributors: nickohrn
Tags: featured-content, admin
Requires at least: 3.5.1
Tested up to: 3.5.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to mark content as featured and provides supporting functionality.

== Description ==

This plugin is very simple and grew out of a client project of mine. It allows you to mark any content that has an editing UI
in the WordPress administrative section as featured. It also provides a template tag that you can use to display certain markup
or styles for feature content and a query variable that allows you to query for featured content.

== Installation ==

1. Upload `featured-content` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Indicate featured content through the row actions on the content management screen or the meta box in the content editing screen
1. Use the query variable `is_featured` in your queries or the template tag `featured_content_is_featured_content` in your templates

== Frequently Asked Questions ==

= Why would I want to use this plugin? =

If you have content on your site that you want to feature (maybe on the home page or in a sidebar) then you can use this plugin
to enable marking that content as featured. It works for the built-in WordPress types (Page, Post) as well as any custom types
that have an editing UI.

Use this plugin so that you don't have to write the same thing yourself.

= How do I use the query variable? =

In your custom queries (`new WP_Query`, `get_posts` or `query_posts`) simply pass in the query variable `is_featured` as follows:

`$featured_posts = new WP_Query(array('is_featured' => 'yes', 'post_type' => 'post'));`

If you need to get only non featured content, you would do something like the following:

`$non_featured_posts = new WP_Query(array('is_featured' => 'no', 'post_type' => 'post'));`

== Screenshots ==

1. This is the metabox that will appear on all editing screens after plugin activation
2. This screenshot shows the links you can click to feature or unfeature any content on your site

== Changelog ==

= 1.0 =
* Initial release of plugin

== Upgrade Notice ==

= 1.0 =
This is the initial release version.