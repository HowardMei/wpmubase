<?php namespace Common\Basemods;
/*
* Plugin Name: Make all urls relative to root
* Description: This plugin is designed to load base modules and deactivate conflicted plugins.
* Plugin URI: https://github.com/HowardMei/wpmubase
* Version: 1.0
* Author: Howard Mei
* Author URI: https://github.com/HowardMei
* Original Code: http://www.deluxeblogtips.com/2012/06/relative-urls.html
*/

/*
 Don't call this file directly.
*/
if ( ! class_exists('WP') ) {
	header( 'Status: 404 Not Found' );
	header( 'HTTP/1.1 404 Not Found' );
	exit( '404 Error: File Not Found.' );
}

add_action( 'template_redirect', __NAMESPACE__ . '\\make_relative_urls' );
function make_relative_urls() {
// Don't do anything if:
// - In feed
// - In sitemap by WordPress SEO plugin
	if ( is_feed() || get_query_var( 'sitemap' ) || in_array($GLOBALS['pagenow'], ['wp-login.php', 'wp-register.php']) ) {
		return; 
	}
	$filters = array(
	'attachment_link',
	'author_link',
	'author_feed_link',
    'bloginfo_url',
	'category_link',
	'category_feed_link',
	'comment_reply_link',
	'feed_link',
	'post_link',
	'post_type_link',
	'post_comments_feed_link',
	'page_link',
	'get_shortlink',
	'post_type_archive_link',
    'get_comment_link',
	'get_comments_pagenum_link',
    'get_pagenum_link',
    'script_loader_src',
    'style_loader_src',
	'search_link',
	'term_link',
    'the_permalink',
	'day_link',
	'month_link',
	'year_link',
    'wp_list_pages',
    'wp_list_categories',
    'wp_get_attachment_url',
    'the_content_more_link',
    'the_tags',
    'the_author_posts_link',
	);
	foreach ( $filters as $filter ) {
		add_filter( $filter, __NAMESPACE__ . '\\root_relative_url' );
	}
}


/**
 * Make a URL relative to root
 */
function root_relative_url($input) {
  $url = parse_url($input);
  if (!isset($url['host']) || !isset($url['path'])) {
    return $input;
  }
  $site_url = parse_url(network_site_url());  // falls back to site_url

  if (!isset($url['scheme'])) {
    $url['scheme'] = $site_url['scheme'];
  }
  $hosts_match = $site_url['host'] === $url['host'];
  $schemes_match = $site_url['scheme'] === $url['scheme'];
  $ports_exist = isset($site_url['port']) && isset($url['port']);
  $ports_match = ($ports_exist) ? $site_url['port'] === $url['port'] : true;

  if ($hosts_match && $schemes_match && $ports_match) {
		return wp_make_link_relative($input);
  }
  return $input;
}



