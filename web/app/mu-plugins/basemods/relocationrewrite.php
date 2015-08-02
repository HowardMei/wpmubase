<?php namespace Common\Basemods;
/*
* Plugin Name: Custom Admin/Register/Login url with rewrite
* Description: This plugin is designed to rewrite admin/register/login urls.
* Plugin URI: https://github.com/HowardMei/wpmubase
* Version: 1.0
* Author: Howard Mei
* Author URI: https://github.com/HowardMei

Make sure .htaccess contains following rules:
# Rewrites for relocation
RewriteRule ^inc/(.*) /wp-includes/$1 [QSA,L]
RewriteRule ^app/skin/(.*) /wp-content/themes/$1 [QSA,L]
RewriteRule ^app/addon/(.*) /wp-content/plugins/$1 [QSA,L]
RewriteRule ^app/base/(.*) /wp-content/mu-plugins/$1 [QSA,L]
RewriteRule ^app/files/(.*) /wp-content/uploads/$1 [QSA,L]
RewriteRule ^media/(.*) /uploads/$1 [QSA,L]
RewriteRule ^app/(.*) /wp-content/$1 [QSA,L]

# Custom login url
RewriteRule ^userlogin/?$ /wp-login.php [QSA,L]
RewriteRule ^userlogout(.+) /wp-login.php?action=logout$1 [QSA,L]
RewriteRule ^netsignup/?$ /wp-login.php?action=register [QSA,L]
RewriteRule ^userforgot/?$ /wp-login.php?action=lostpassword [QSA,L]

# Custom admin url
RewriteRule ^console$ %{REQUEST_URI}/ [R=301,L]
RewriteRule ^console/(.*?)$ /wp-admin/$1?%{QUERY_STRING} [QSA,L]

*/
/*
 Don't call this file directly.
*/
if ( ! class_exists('WP') ) {
	header('Status: 404 Not Found');
	header('HTTP/1.1 404 Not Found');
	exit('404 Error: File Not Found.');
}

!defined('ABSPATH') && header("HTTP/1.1 404 Not Found") && exit('404 Error: File Not Found.');
!defined('WP_PLUGIN_DIR') && header("HTTP/1.1 404 Not Found") && exit('404 Error: File Not Found.');
!defined('WPMU_PLUGIN_DIR') && header("HTTP/1.1 404 Not Found") && exit('404 Error: File Not Found.');

if (defined('ENABLE_RELOCATE') && (ENABLE_RELOCATE == true) && defined('WP_ADMIN_SLUG') && defined('REWRITE_ENGINE')) {

if ( REWRITE_ENGINE === 'apache' ) {
	if ( file_exists( WPMU_PLUGIN_DIR . '/baselib/h5bp-htaccess.php' ) ) {
		require_once( WPMU_PLUGIN_DIR . '/baselib/h5bp-htaccess.php' );
		add_action('generate_rewrite_rules', __NAMESPACE__ . '\\ApacheServerConfig::init', -9999, 0);
	}
} elseif ( REWRITE_ENGINE === 'nginx' ) {
	if ( file_exists( WPMU_PLUGIN_DIR . '/baselib/h5bp-nginxconf.php' ) ) {
		require_once( WPMU_PLUGIN_DIR . '/baselib/h5bp-nginxconf.php' );
		add_action('generate_rewrite_rules', __NAMESPACE__ . '\\NginxServerConfig::init', -9999, 0);
	}
} else {
	if ( file_exists( WPMU_PLUGIN_DIR . '/baselib/h5bp-dummyconf.php' ) ) {
		require_once( WPMU_PLUGIN_DIR . '/baselib/h5bp-dummyconf.php' );
		add_action('generate_rewrite_rules', __NAMESPACE__ . '\\DummyServerConfig::init', -9999, 0);
	}
}


add_action('init', __NAMESPACE__ . '\\secure_login_admin');
function secure_login_admin() {
	if((strpos(strtolower($_SERVER['REQUEST_URI']),'wp-login.php') !== false) && strtolower($_SERVER['REQUEST_METHOD']) != "post")
	{
		status_header( '404' );
		exit;
	}
	else if((strpos(strtolower($_SERVER['REQUEST_URI']), WP_LOGOUT_SLUG) !== false))
	{
		wp_logout();
		wp_redirect(site_url(WP_LOGIN_SLUG, 'login'));
		exit;
	}
	else if((strpos(strtolower($_SERVER['REQUEST_URI']),'wp-admin') !== false) && !is_user_logged_in())
	{
		header("HTTP/1.1 404 Not Found");
		exit('404 Error: File Not Found.');
	}
	else if((strpos(strtolower($_SERVER['REQUEST_URI']), WP_ADMIN_SLUG . '/about.php') !== false) ||
			(strpos(strtolower($_SERVER['REQUEST_URI']), WP_ADMIN_SLUG . '/credits.php') !== false) ||
			(strpos(strtolower($_SERVER['REQUEST_URI']), WP_ADMIN_SLUG . '/freedoms.php') !== false))
	{
		header("HTTP/1.1 404 Not Found");
		exit('404 Error: File Not Found.');
	}
}

/** When working with domain-mapped sites on WordPress.com, home_url() and site_url() will return different values.
    home_url() returns the primary mapped domain (e.g. vippuppies.com)
    site_url() returns the *.wordpress.com URL (e.g. vippuppies.wordpress.com)
	home_url() will only return the mapped domain on or after the init has fired. Calling it before then will return the .wordpress.com domain.
*/


// includes url fix
add_filter('includes_url', __NAMESPACE__ . '\\fix_includes_url', 10, 2);
add_filter('script_loader_src', __NAMESPACE__ . '\\fix_includes_url', 10, 2);
add_filter('style_loader_src', __NAMESPACE__ . '\\fix_includes_url', 10, 2);
function fix_includes_url($link) {
    return str_replace('/wp-includes/', '/' . WP_INCLUDES_SLUG . '/',$link);
}


// themes url fix
add_filter('theme_root_uri',__NAMESPACE__ . '\\fix_themes_url');
function fix_themes_url($link) {
    return str_replace(content_url('themes', ''), content_url(WP_THEMES_SLUG, ''),$link);
}

// upload url fix
add_filter('upload_dir',__NAMESPACE__ . '\\fix_upload_url');
function fix_upload_url($patharray) {
    $newbasedir = str_replace(WP_CONTENT_DIR . '/uploads', WP_UPLOAD_DIR, $patharray['basedir']);
	$newsubdir = '';
	if ( _ENABLE_UPLOAD_MASK_ === true ) {
		if ($patharray['subdir'] == '') {
			$tmpsub = substr(md5(_UPLOAD_MASK_ . rand(1,12)),rand(1,12),12);
			$newsubdir = "/$tmpsub";
		} else {
			$tmpsub = explode('/',$patharray['subdir']);
			$ty = substr(md5(_UPLOAD_MASK_ . $tmpsub[0]),rand(1,3),12);
			$tm = substr(md5(_UPLOAD_MASK_ . $tmpsub[1]),rand(1,4),12);
			$newsubdir = "/$ty/$tm";
		}
	}
	if (realpath(dirname(WP_UPLOAD_DIR)) != realpath(WP_CONTENT_DIR)) {
		$newbaseurl = str_replace(content_url('uploads', ''), home_url(WP_UPLOAD_SLUG, ''),$patharray['baseurl']);
	} else {
		$newbaseurl = str_replace(content_url('uploads', ''), content_url(WP_UPLOAD_SLUG, ''),$patharray['baseurl']);
	}
	$patharray['path']=str_replace($patharray['basedir'] . $patharray['subdir'] , $newbasedir . $newsubdir, $patharray['path']);
	$patharray['url']=str_replace($patharray['baseurl'] . $patharray['subdir'] , $newbaseurl . $newsubdir, $patharray['url']);	
	if ( _ENABLE_UPLOAD_MASK_ === true ) {
		$patharray['subdir'] = $newsubdir;
	}
	$patharray['basedir'] = $newbasedir;
	$patharray['baseurl'] = $newbaseurl;
	return $patharray;
}

//wp-admin url fix
add_filter('admin_url',__NAMESPACE__ . '\\fix_wpadmin_url', 10, 2);
add_filter('home_url',__NAMESPACE__ . '\\fix_wpadmin_url');
//add_filter('wp_admin_css_uri',__NAMESPACE__ . '\\fix_wpadmin_url');
add_filter('script_loader_src', __NAMESPACE__ . '\\fix_wpadmin_url');
add_filter('style_loader_src', __NAMESPACE__ . '\\fix_wpadmin_url');
function fix_wpadmin_url($link){
    return str_replace('/wp-admin/', '/' . WP_ADMIN_SLUG . '/',$link);
}
 
//network wp-admin url fix
add_filter('network_admin_url',__NAMESPACE__ . '\\fix_netwpadmin_url');
add_filter('user_admin_url',__NAMESPACE__ . '\\fix_netwpadmin_url');
function fix_netwpadmin_url($link){
    return str_replace('/wp-admin/', '/' . WP_ADMIN_SLUG . '/',$link);
} 

//login url fix
add_filter('login_url',__NAMESPACE__ . '\\fix_login_url');
function fix_login_url($link){
    return str_replace(home_url('wp-login.php', 'login'),home_url(WP_LOGIN_SLUG, 'login'),$link);
}

//new logout url
add_filter('logout_url',__NAMESPACE__ . '\\new_logout_url',10,2);         // solve the problem of redirect_to para
function new_logout_url($link){
    return home_url(WP_LOGOUT_SLUG,'');
}

//register url fix
add_filter('register',__NAMESPACE__ . '\\fix_register_url');
function fix_register_url($link){
    return str_replace(home_url('wp-login.php?action=register', 'login'),home_url(WP_REGISTER_SLUG, 'login'),$link);
}
 
//forgot password url fix
add_filter('lostpassword_url',__NAMESPACE__ . '\\fix_lostpass_url');
function fix_lostpass_url($link){
    return str_replace('wp-login.php?action=lostpassword','',str_replace(home_url('wp-login.php', 'login'),home_url(WP_FORGOT_SLUG, 'login'),$link));
}

//Site URL hack to overwrite register url
add_filter('site_url',__NAMESPACE__ . '\\fix_site_urls',10,3);
function fix_site_urls($url, $path, $orig_scheme){
    if ($path == 'wp-login.php')
        return home_url(WP_LOGIN_SLUG, 'login');
    if ($path == 'wp-login.php?action=register')
        return home_url(WP_REGISTER_SLUG, 'login');
	if ($path == 'wp-login.php?action=logout')
        return home_url(WP_LOGOUT_SLUG, 'login_post');
 	if ($path == 'wp-login.php?action=lostpassword')
        return home_url(WP_FORGOT_SLUG, 'login');
    return $url;
}

//Network site URL hack to overwrite register url
add_filter('network_site_url',__NAMESPACE__ . '\\fix_netsite_urls',10,3);
function fix_netsite_urls($url, $path, $orig_scheme){
    if ($path == 'wp-login.php')
        return network_home_url(WP_LOGIN_SLUG, 'login');
    if ($path == 'wp-login.php?action=register')
        return network_home_url(WP_REGISTER_SLUG, 'login');
	if ($path == 'wp-login.php?action=logout')
        return network_home_url(WP_LOGOUT_SLUG, 'login_post');
 	if ($path == 'wp-login.php?action=lostpassword')
        return network_home_url(WP_FORGOT_SLUG, 'login');
    return $url;
}

} //if (defined('ENABLE_RELOCATE') && (ENABLE_RELOCATE == true) && defined('WP_ADMIN_SLUG') )
