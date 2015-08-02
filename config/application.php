<?php
/**
 * Basic parameters including env loading and ROOT_DOMAIN checking.
 */

!defined('_WEBROOT_DIR_') && exit('Error: config APP#1');
!defined('_ROOT_DIR_') && exit('Error: config APP#2');
!defined('_CONFIG_DIR_') && exit('Error: config APP#3');

/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = new Dotenv\Dotenv(_ROOT_DIR_);
if (file_exists(_ROOT_DIR_ . '/.env')) {
	  $dotenv->load();
	  $multi_db=getenv('MULTI_DB') ?: false;
	  if ( $multi_db != true ) {
		$dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD', 'ROOT_DOMAIN']);
	  } else {
		$dotenv->required('ROOT_DOMAIN')->notEmpty();
	  }
} else {
	$rootdomain=getenv('ROOT_DOMAIN');
	if ( empty($rootdomain) )
		exit('Error: config APP#4');
}

/**
 * Set up our global environment constant and load its config first
 * Default: development
 */
define('WORK_ENV', getenv('WORK_ENV') ?: 'development');
define('WP_ENV', WORK_ENV);

$env_config = _CONFIG_DIR_ . '/environments/' . WORK_ENV . '.php';

if ( file_exists($env_config) ) {
	require_once $env_config;
} else {
	exit('Error: config APP#5');
}


/**
 * DB settings
 */

if ( $multi_db == false ) {
	define('DB_NAME', getenv('DB_NAME') ?: 'demodb');
	define('DB_USER', getenv('DB_USER') ?: 'demouser');
	define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'demopass');
	define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
	define('DB_CHARSET', 'utf8');
	define('DB_COLLATE', 'utf8_general_ci');
	$table_prefix = getenv('DB_PREFIX') ?: 'wpdb1_';
	##exit('Error: config debug ' . DB_HOST);
} else {
	if ( file_exists( _CONFIG_DIR_ . '/multidb-config.php' ) ) {
		require_once( _CONFIG_DIR_ . '/multidb-config.php' );	
	} else {
		exit('Error: config APP#6');
	}
}

/**
 * Authentication Unique Keys and Salts
 */
define('AUTH_KEY', getenv('AUTH_KEY'));
define('SECURE_AUTH_KEY', getenv('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY', getenv('LOGGED_IN_KEY'));
define('NONCE_KEY', getenv('NONCE_KEY'));
define('AUTH_SALT', getenv('AUTH_SALT'));
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT', getenv('LOGGED_IN_SALT'));
define('NONCE_SALT', getenv('NONCE_SALT'));

/**
 * Application Settings
 */
// The root domain of a multisite network whose ID=1.
define('ROOT_DOMAIN', getenv('ROOT_DOMAIN'));
define('WP_MEMORY_LIMIT', getenv('MEMORY_LIMIT') ?: '128M');
define('WP_MAX_MEMORY_LIMIT', getenv('MAX_MEMORY_LIMIT') ?: '512M');
define('WP_DEFAULT_THEME', getenv('DEFAULT_THEME') ?: 'flat-bootstrap');
define('WP_POST_REVISIONS', true);
// Turn on this and go to dashboard menu tools network setup
define('WP_ALLOW_MULTISITE', getenv('ALLOW_MULTISITE') ?: false); 
define('FORCE_SSL_ADMIN', getenv('FORCE_SSL_ADMIN') ?: false);
define('FORCE_SSL_LOGIN', getenv('FORCE_SSL_LOGIN') ?: false);
// File system access mode: 'direct', 'ssh2', 'ftpext' or 'ftpsockets'
// Take care the permissions of WP_USERFILES_DIR and WP_CONTENT_DIR/{plugins, mu-plugins, themes}
define('FS_METHOD', getenv('FS_METHOD') ?: 'direct');
