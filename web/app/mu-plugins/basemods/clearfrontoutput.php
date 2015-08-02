<?php namespace Common\Basemods;
/*
* Plugin Name: Clear Front End Output
* Description: This plugin is designed to load base modules and deactivate conflicted plugins.
* Plugin URI: https://github.com/HowardMei/wpmubase
* Version: 1.0
* Author: Howard Mei
* Author URI: https://github.com/HowardMei
*/

/*
 Don't call this file directly.
*/
if ( ! class_exists('WP') ) {
	header('Status: 404 Not Found');
	header('HTTP/1.1 404 Not Found');
	exit('404 Error: File Not Found.');
}

// Disallow indexing of your site on non-production environments.
if (WP_ENV !== 'production' && !is_admin()) {
  add_action('pre_option_blog_public', '__return_zero');
}

function wp_head_cleanup() {
  // Originally from http://wpengineer.com/1438/wordpress-header/
  add_action('wp_head', 'ob_start', 1, 0);
  add_action('wp_head', function () {
    $pattern = '/.*' . preg_quote(esc_url(get_feed_link('comments_' . get_default_feed())), '/') . '.*[\r\n]+/';
    echo preg_replace($pattern, '', ob_get_clean());
  }, 3, 0);
  remove_action('wp_head', 'rsd_link'); // Remove the link to the Really Simple Discovery service endpoint link
  remove_action('wp_head', 'wlwmanifest_link'); // Remove the link to the Windows Live Writer manifest file
  remove_action('wp_head', 'wp_generator'); // Remove the XHTML generator that is generated on the wp_head hook, WP version
  remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('admin_print_scripts', 'print_emoji_detection_script');
  remove_action('wp_print_styles', 'print_emoji_styles');
  remove_action('admin_print_styles', 'print_emoji_styles');
  remove_filter('the_content_feed', 'wp_staticize_emoji');
  remove_filter('comment_text_rss', 'wp_staticize_emoji');
  remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
  add_filter('use_default_gallery_style', '__return_false');
  // Remove php powered by info [or add expose_php = Off in php.ini]
  header_remove("X-Powered-By");
  defined('_ENGINE_NAME_') && defined('_ENGINE_VER_') && header( 'X-Powered-By:' . _ENGINE_NAME_ . '-' . _ENGINE_VER_ );
}

add_action('init', __NAMESPACE__ . '\\wp_head_cleanup');

//Support shortcode in text widget
add_filter('widget_text', 'do_shortcode');

/**
 * Remove the WordPress version from RSS feeds
 */
add_filter('the_generator', '__return_false');

// Remove the X-Pingback header
function remove_x_pingback($headers) {
	unset($headers['X-Pingback']);
	return $headers;
}
add_filter('wp_headers', __NAMESPACE__ . '\\remove_x_pingback');


// Remove comments are closed
function remove_comments_are_closed($translated_text, $untranslated_text, $domain) {
    if ( $untranslated_text == 'Comments are closed.' ) {
        return '';
    }
    return $translated_text;
}
add_filter('gettext', __NAMESPACE__ . '\\remove_comments_are_closed', 20, 3);


/**
 * Wrap embedded media as suggested by Readability
 *
 * @link https://gist.github.com/965956
 * @link http://www.readability.com/publishers/guidelines#publisher
 */
function readability_embed_wrap($cache) {
  return '<div class="entry-content-asset">' . $cache . '</div>';
}
add_filter('embed_oembed_html', __NAMESPACE__ . '\\readability_embed_wrap');

// Remove WP version param from any enqueued scripts (using wp_enqueue_script()) or styles (using wp_enqueue_style()).

function remove_appended_version_script_style( $target_url ) {
	// Check if "ver=" argument exists in the url or not
    if(strpos( $target_url, 'ver=' )) {
        $target_url = remove_query_arg( 'ver', $target_url );
	}
    return $target_url;
}
add_filter('style_loader_src', __NAMESPACE__ . '\\remove_appended_version_script_style', 999);
add_filter('script_loader_src', __NAMESPACE__ . '\\remove_appended_version_script_style', 999);


add_filter( 'login_headerurl', __NAMESPACE__ . '\\custom_loginlogo_url' ); 
function custom_loginlogo_url($url) 
{ 
	if (defined('_NETBRAND_HOME_LINK_')){
		return _NETBRAND_HOME_LINK_; 
	} else {
		return '/logo.png'; 
	}
}

/**
 * Don't return the default description in the RSS feed if it hasn't been changed
 */
function remove_default_description($bloginfo) {
  $default_tagline = 'Just another WordPress site';
  return ($bloginfo === $default_tagline) ? '' : $bloginfo;
}
add_filter('bloginfo', __NAMESPACE__ . '\\remove_default_description');
add_filter('get_bloginfo_rss', __NAMESPACE__ . '\\remove_default_description');

/**
* Clean up language_attributes() used in <html> tag
*
* Change lang="en-US" to lang="en"
* Remove dir="ltr"
*/
function clear_language_attributes() {
  $attributes = array();
  $output = '';

  if (function_exists('is_rtl')) {
    if (is_rtl() == 'rtl') {
      $attributes[] = 'dir="rtl"';
    }
  }

  $lang = get_bloginfo('language');

  if ($lang && $lang !== 'en-US') {
    $attributes[] = "lang=\"$lang\"";
  } else {
    $attributes[] = 'lang="en"';
  }

  $output = implode(' ', $attributes);
  $output = apply_filters(__NAMESPACE__ . '\\clear_language_attributes', $output);

  return $output;
}
add_filter('language_attributes', __NAMESPACE__ . '\\clear_language_attributes');

/**
* Clean up output of stylesheet <link> tags
*/
function clear_clean_style_tag($input) {
  preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches);
  // Only display media if it is meaningful
  $media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
  return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
}
add_filter('style_loader_tag', __NAMESPACE__ . '\\clear_clean_style_tag');

/**
* Wrap embedded media as suggested by Readability
*
* @link https://gist.github.com/965956
* @link http://www.readability.com/publishers/guidelines#publisher
*/
function clear_embed_wrap($cache, $url, $attr = '', $post_ID = '') {
  return '<div class="entry-content-asset">' . $cache . '</div>';
}
add_filter('embed_oembed_html', __NAMESPACE__ . '\\clear_embed_wrap', 10, 4);

/**
* Add Bootstrap thumbnail styling to images with captions
* Use <figure> and <figcaption>
*
* @link http://justintadlock.com/archives/2011/07/01/captions-in-wordpress
*/
function clear_caption($output, $attr, $content) {
  if (is_feed()) {
    return $output;
  }

  $defaults = array(
    'id' => '',
    'align' => 'alignnone',
    'width' => '',
    'caption' => ''
  );

  $attr = shortcode_atts($defaults, $attr);

  // If the width is less than 1 or there is no caption, return the content wrapped between the [caption] tags
  if ($attr['width'] < 1 || empty($attr['caption'])) {
    return $content;
  }

  // Set up the attributes for the caption <figure>
  $attributes = (!empty($attr['id']) ? ' id="' . esc_attr($attr['id']) . '"' : '' );
  $attributes .= ' class="thumbnail wp-caption ' . esc_attr($attr['align']) . '"';
  $attributes .= ' style="width: ' . esc_attr($attr['width']) . 'px"';

  $output = '<figure' . $attributes .'>';
  $output .= do_shortcode($content);
  $output .= '<figcaption class="caption wp-caption-text">' . $attr['caption'] . '</figcaption>';
  $output .= '</figure>';

  return $output;
}
add_filter('img_caption_shortcode', __NAMESPACE__ . '\\clear_caption', 10, 3);


/**
 * Add and remove body_class() classes
 */
function clean_body_class($classes) {
  // Add post/page slug if not present
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }
  // Remove unnecessary classes
  $home_id_class = 'page-id-' . get_option('page_on_front');
  $remove_classes = [
    'page-template-default',
    $home_id_class
  ];
  $classes = array_diff($classes, $remove_classes);
  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\clean_body_class');
