<?php namespace Common\Basemods;
/*
* Plugin Name: Clear Unwanted Dashboard Items
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

if (is_admin() || is_network_admin()) {
   //Never load the system if not necessarily need to.  

// remove dashboard widgets
add_action('admin_init', __NAMESPACE__ . '\\remove_admin_dashboard_widgets');
function remove_admin_dashboard_widgets() {
	//remove_meta_box('dashboard_right_now','dashboard','normal'); // right now overview box
	remove_meta_box('dashboard_incoming_links','dashboard','normal'); // incoming links box
	//remove_meta_box('dashboard_quick_press','dashboard','normal'); // quick press box
	remove_meta_box('dashboard_plugins','dashboard','normal'); // new plugins box
	//remove_meta_box('dashboard_recent_drafts','dashboard','normal'); // recent drafts box
	remove_meta_box('dashboard_recent_comments','dashboard','normal'); // recent comments box
	remove_meta_box('dashboard_primary','dashboard','normal'); // wordpress development blog box
	remove_meta_box('dashboard_secondary','dashboard','normal'); // other wordpress news box
	
	// new dashboard plugin widgets
	remove_meta_box('dashboard_rss','dashboard','normal'); // new dashboard plugin news box

	// start removing plugin dashboard boxes. yup i'm goin there
	remove_meta_box('yoast_db_widget','dashboard','normal'); // yoasts dash news
	remove_meta_box('aw_dashboard','dashboard','normal'); // wp socializer box
	remove_meta_box('w3tc_latest','dashboard','normal'); // w3 total cache news box
}

add_action('wp_user_dashboard_setup', __NAMESPACE__ . '\\remove_user_dashboard_widgets' );
function remove_user_dashboard_widgets() {
  remove_meta_box ( 'dashboard_primary', 'dashboard-user', 'normal' ); //Blog Feeds
  remove_meta_box ( 'dashboard_secondary', 'dashboard-user', 'normal' ); //Other News
}

// force remove meta boxes from default posts screen
add_action('do_meta_boxes', __NAMESPACE__ . '\\remove_post_metaboxes');
function remove_post_metaboxes() {
	//remove_meta_box('postcustom','post','normal'); // custom fields metabox
	//remove_meta_box('postexcerpt','post','normal'); // excerpt metabox
	remove_meta_box('commentstatusdiv','post','normal'); // comments metabox
	remove_meta_box('commentsdiv','post','normal'); // comments metabox	
	//remove_meta_box('trackbacksdiv','post','normal'); // trackbacks metabox
	//remove_meta_box('slugdiv','post','normal'); // slug metabox (breaks edit permalink update)
	//remove_meta_box('authordiv','post','normal'); // author metabox
	//remove_meta_box('revisionsdiv','post','normal'); // revisions metabox
	//remove_meta_box('tagsdiv-post_tag','post','normal'); // tags metabox
	//remove_meta_box('categorydiv','post','normal'); // comments metabox
	//remove_meta_box('postimagediv','post','side'); // featured image metabox
}

add_action('after_setup_theme', __NAMESPACE__ . '\\remove_post_format', 15); 
function remove_post_format() { 
	remove_theme_support('post-formats'); 
}

// force remove meta boxes from default pages screen
add_action('do_meta_boxes', __NAMESPACE__ . '\\remove_page_metaboxes');
function remove_page_metaboxes() {
	//remove_meta_box('postcustom','page','normal'); // custom fields metabox
	//remove_meta_box( 'postexcerpt','page','normal' ); // page excerpts	
	remove_meta_box('commentstatusdiv','page','normal'); // discussion metabox
	remove_meta_box('commentsdiv','page','normal'); // comments metabox
	remove_meta_box( 'trackbacksdiv','page','normal' ); // page trackbacks
	//remove_meta_box( 'tagsdiv-post_tag','page','side' ); // page tags
	//remove_meta_box( 'categorydiv','page','side' ); // page category
	//remove_meta_box('slugdiv','page','normal'); // slug metabox (breaks edit permalink update)
	//remove_meta_box('authordiv','page','normal'); // author metabox
	//remove_meta_box('revisionsdiv','page','normal'); // revisions metabox
	
}

function remove_pages_columns($columns) { 
	// Remove comments column 
	unset($columns['comments']); 
	return $columns; 
} 
add_filter('manage_pages_columns', __NAMESPACE__ . '\\remove_pages_columns');

function adjust_profile_fields( $user_contact ) {
/* Add user contact methods */
	//$user_contact['twitter'] = __('Twitter');
	//$user_contact['facebook'] = __('Facebook');
	//$user_contact['linkedin'] = __('Linkedin');
	//$user_contact['skype'] = __('Skype');
	//$user_contact['phone'] = __('Phone Number');
	//$user_contact['mobile'] = __('Mobile Number');
	
/* Remove user contact methods */
	unset($user_contact['aim']);
	unset($user_contact['jabber']);
	unset($user_contact['yim']);
	//unset($user_contact['url']);
	//unset($user_contact['description']);
return $user_contact;
}
add_filter('user_contactmethods', __NAMESPACE__ . '\\adjust_profile_fields',10,1);

} //end of if(is_admin())

if (is_network_admin()) {
add_action('wp_network_dashboard_setup', __NAMESPACE__ . '\\remove_network_dashboard_widgets' );    
function remove_network_dashboard_widgets() {
  //remove_meta_box ( 'network_dashboard_right_now', 'dashboard-network', 'normal' ); // Right Now
  remove_meta_box ( 'dashboard_plugins', 'dashboard-network', 'normal' ); // Plugins
  remove_meta_box ( 'dashboard_primary', 'dashboard-network', 'side' ); // Blog Feeds
  remove_meta_box ( 'dashboard_secondary', 'dashboard-network', 'side' ); // Other News
}
} //end of if(is_network_admin())

if ( is_admin() || is_network_admin()) {

add_action( 'wp_after_admin_bar_render', __NAMESPACE__ . '\\remove_help_tabs',10,2 );
function remove_help_tabs()
{
  $screen = get_current_screen();
  $screen->remove_help_tabs();
  //add_filter('screen_options_show_screen', '__return_false');
}

add_action( 'wp_after_admin_bar_render', __NAMESPACE__ . '\\remove_help_sidebar',10,2 );
function remove_help_sidebar()
{
  $screen = get_current_screen();
  if (method_exists($screen,'remove_help_sidebar'))
		$screen->remove_help_sidebar();
}

//add_filter('screen_options_show_screen', __NAMESPACE__ . '\\removeall_screen_options', 10, 2); 
function remove_screen_options($display_boolean, $wp_screen_object){
  $blacklist = array('index.php', 'themes.php','profile.php','plugins.php',
					 'options-general.php','plugin-install.php','theme-install.php');
  if (in_array($GLOBALS['pagenow'], $blacklist)) {
    $wp_screen_object->render_screen_layout();
    $wp_screen_object->render_per_page_options();
    return false;
  } else {
    return true;
  }
}

function removeall_screen_options($display_boolean, $wp_screen_object){
    $wp_screen_object->render_screen_layout();
    $wp_screen_object->render_per_page_options();
    return false;
}

// Remove the welcome panel 
add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\remove_welcome_panel' );
function remove_welcome_panel() {
    global $wp_filter;
    unset( $wp_filter['welcome_panel'] );
}

// Remove update notifications for everybody except super admin
add_action('admin_notices', __NAMESPACE__ . '\\remove_update_notification_nonsuper',1);
add_action('network_admin_notices', __NAMESPACE__ . '\\remove_update_notification_nonsuper',1);
function remove_update_notification_nonsuper() {
	//if (!current_user_can('manage_network'))
	remove_action('admin_notices','update_nag',3);
	remove_action('network_admin_notices','update_nag',3);
}

function remove_adminbar_items()
{
	global $wp_admin_bar;
    
	$wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
    $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
    $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
    $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
    $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
    $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
	//$wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
    //$wp_admin_bar->remove_menu('view-site');        // Remove the view site link
    $wp_admin_bar->remove_menu('updates');          // Remove the updates link
    //$wp_admin_bar->remove_menu('comments');         // Remove the comments link
    //$wp_admin_bar->remove_menu('new-content');      // Remove the content link
    $wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
   // $wp_admin_bar->remove_menu('my-account');       // Remove the user details tab

}
add_action('wp_before_admin_bar_render', __NAMESPACE__ . '\\remove_adminbar_items');


// Remove the plugin meta information for normal admins
if ( ! class_exists('ClearPluginMeta') ) {
add_action('plugins_loaded', array ( __NAMESPACE__ . '\\ClearPluginMeta', 'get_instance' ));
class ClearPluginMeta {

 // Plugin instance
	protected static $instance = NULL;

	function __construct() {
					//declare hooks
                add_filter( 'plugin_row_meta' , array( &$this, 'remove_plugin_meta' ), 10, 2 );
                add_action( 'admin_init', array( &$this, 'remove_plugin_update_row' ) );
        }

	// Access this plugin¡¯s working instance
	public static function get_instance() {
			if ( NULL === self::$instance )
							self::$instance = new self;

			return self::$instance;
	}

	//removes the meta information for normal admins
	function remove_plugin_meta($plugin_meta, $plugin_file) {
	 if ( is_super_admin() ) {
					if ( defined('_ENGINE_PLUGINCREDITS_') ) {
						$new_plugin_meta[] = _ENGINE_PLUGINCREDITS_;}
					else {
						$new_plugin_meta[] = '';}
					return $new_plugin_meta;
			} else {
		remove_all_actions("after_plugin_row_$plugin_file");
			 return array();
			}
	}

    function remove_plugin_update_row() {
		 if ( !is_super_admin() ) {
			remove_all_actions('after_plugin_row');
				}
	}
}}

// Remove the theme meta information for normal admins
if ( ! class_exists('ClearThemeMeta') ) {
add_action('wp_after_admin_bar_render', array ( __NAMESPACE__ . '\\ClearThemeMeta', 'get_instance' ));
class ClearThemeMeta {

 // Theme instance
	protected static $instance = NULL;

	function __construct() {
					//declare hooks
                add_filter( 'theme_row_meta' , array( &$this, 'remove_theme_meta' ), 10, 2 );
                add_action( 'admin_init', array( &$this, 'remove_theme_update_row' ) );
        }

	// Access this theme¡¯s working instance
	public static function get_instance() {
			if ( NULL === self::$instance )
							self::$instance = new self;

			return self::$instance;
	}

	//removes the meta information for normal admins
	function remove_theme_meta($theme_meta, $stylesheet) {
	 if ( is_super_admin() ) {
					if ( defined('_ENGINE_THEMECREDITS_') ) {
						$new_theme_meta[] = _ENGINE_THEMECREDITS_;}
					else {
						$new_theme_meta[] = '';}
					return $new_theme_meta;
			} else {
		remove_all_actions("after_theme_row_$stylesheet");
			 return array();
			}
	}

    function remove_theme_update_row() {
		 if ( !is_super_admin() ) {
			remove_all_actions('after_theme_row');
				}
	}
}}

//Hide admin footer infomation
function change_footer_admin () {return ' ';}
add_filter('admin_footer_text', __NAMESPACE__ . '\\change_footer_admin', 49);
function change_footer_version() {return ' ';}
add_filter( 'update_footer', __NAMESPACE__ . '\\change_footer_version', 49);

} //end of if ( is_admin() || is_network_admin())

?>
