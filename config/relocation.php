<?php
/**
Initial configs: 28/06/2012 by Howard Mei
Modified: 25/10/2013 by Howard Mei
Modified: 20/07/2015 by Howard Mei
*/

!defined('_WEBROOT_DIR_') && exit('Error: config REL#1');
!defined('ABSPATH') && exit('Error: config REL#2');
!defined('WP_CONTENT_DIR') && exit('Error: config REL#3');
!function_exists('_get_wpmuhome') && exit('Error: config REL#4');

/**
 * Custom Dirs and Locations
 */
// Be able to move the original dirs to another place. No trailing slash, full paths only.
define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
define('WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins');
define('WP_LANG_DIR', WP_CONTENT_DIR . '/lang');

/**
 * Custom SLUGS and URLs
 */
define('WP_HOME', _get_wpmuhome());

!defined('ENABLE_RELOCATE') && define('ENABLE_RELOCATE', getenv('ENABLE_RELOCATE') ?: false);
!defined('REWRITE_ENGINE') && define('REWRITE_ENGINE', getenv('REWRITE_ENGINE') ?: 'apache');

if ( ENABLE_RELOCATE == true ) {
	define('WP_CORE_SLUG', getenv('WP_CORE_SLUG') ?: 'core');
	define('WP_ADMIN_SLUG', getenv('WP_ADMIN_SLUG') ?: 'dash');
	define('WP_INCLUDES_SLUG', getenv('WP_INCLUDES_SLUG') ?: 'inc');
	define('WP_CONTENT_SLUG', getenv('WP_CONTENT_SLUG') ?: 'app');
	define('WP_PLUGIN_SLUG', getenv('WP_PLUGIN_SLUG') ?: 'plugins');
	define('WP_MUPLUGIN_SLUG', getenv('WP_MUPLUGIN_SLUG') ?: 'common');
	define('WP_THEMES_SLUG', getenv('WP_THEMES_SLUG') ?: 'themes');
	define('WP_UPLOAD_SLUG', getenv('WP_UPLOAD_SLUG') ?: 'media');
	define('WP_LOGIN_SLUG', getenv('WP_LOGIN_SLUG') ?: 'ulogin');
	define('WP_LOGOUT_SLUG', getenv('WP_LOGOUT_SLUG') ?: 'ulogout');
	define('WP_REGISTER_SLUG', getenv('WP_REGISTER_SLUG') ?: 'usignup');
	define('WP_FORGOT_SLUG', getenv('WP_FORGOT_SLUG') ?: 'uforgot');

	/**
	 * Preset Home and Siteurl [To sync cookie paths and keep options from multisite superadmin touch.]
	 */
	define('WP_SITEURL', WP_HOME . '/' . WP_CORE_SLUG );
	define('WP_CONTENT_URL', WP_HOME . '/' . WP_CONTENT_SLUG);
	define('WP_PLUGIN_URL', WP_CONTENT_URL . '/' . WP_PLUGIN_SLUG );
	define('WPMU_PLUGIN_URL', WP_CONTENT_URL . '/' . WP_MUPLUGIN_SLUG );
			
	/** 
		Custom Cookies and Cookie Paths
	*/
	//define('COOKIE_DOMAIN', false);  # false for sub-folder install, .mydomain.com for sub-domain install, comment out for domain mapping plugin

	define('COOKIEHASH', md5(WP_SITEURL));
	define('COOKIE_PREFIX', 'CMSaaS');
	define('USER_COOKIE', COOKIE_PREFIX . 'user_' . COOKIEHASH);
	define('PASS_COOKIE', COOKIE_PREFIX . 'pass_' . COOKIEHASH);
	define('AUTH_COOKIE', COOKIE_PREFIX . '_' . COOKIEHASH);
	define('SECURE_AUTH_COOKIE', COOKIE_PREFIX . '_sec_' . COOKIEHASH);
	define('LOGGED_IN_COOKIE', COOKIE_PREFIX . '_logged_in_' . COOKIEHASH);
	define('TEST_COOKIE', COOKIE_PREFIX . '_test_cookie');
	define('SITECOOKIEPATH', preg_replace('|https?://[^/]+|i', '', rtrim(WP_SITEURL, '/') . '/'));
	define('ADMIN_COOKIE_PATH', SITECOOKIEPATH . WP_ADMIN_SLUG);
	define('PLUGINS_COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', WP_PLUGIN_URL));
	define('MUPLUGINS_COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', WPMU_PLUGIN_URL));

} else {
	define('WP_SITEURL', WP_HOME . '/core' );
	define('WP_CONTENT_URL', WP_HOME . '/app');
}

