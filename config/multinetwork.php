<?php
/**
Initial configs: 28/06/2012 by Howard Mei
Modified: 25/10/2013 by Howard Mei howardmei@mubiic.com
**/
!defined('ROOT_DOMAIN') && exit('Error: config MUL#1');
!defined('_WEBROOT_DIR_') && exit('Error: config MUL#2');
!defined('WP_USERFILES_DIR') && exit('Error: config MUL#3');

define('WPMU_SENDFILE', getenv('X-SENDFILE') ?: false);
define('WPMU_ACCEL_REDIRECT', getenv('X-ACCEL-REDIRECT') ?: false);
if ( WP_ALLOW_MULTISITE == true ) {
	define('UPLOADBLOGSDIR', WP_USERFILES_DIR . '/sites.dir');  // Comment this out in single site mode
} else {
	define('_WP_UPLOAD_DIR_', WP_USERFILES_DIR . '/uploads');	
}


// UPLOADBLOGSDIR should be a symlink to web/usrfs/sites.dir
if (WP_ALLOW_MULTISITE == true && is_link('UPLOADBLOGSDIR')) {
// Automatically Activate the network
// Follow the instructions in the dashboard before activate!
// Which means sunrise.php and blog.dir are created, .htaccess is updated
	define('MULTISITE', true);
	define('SUBDOMAIN_INSTALL', true);
	define('DOMAIN_CURRENT_SITE', ROOT_DOMAIN);
	define('PATH_CURRENT_SITE', '/');
	define('SITE_ID_CURRENT_SITE', 1);
	define('BLOG_ID_CURRENT_SITE', 1);
	if ( file_exists(WP_CONTENT_DIR . '/sunrise.php') ) {
		define( 'SUNRISE', 'on');	
	}
	define('NOBLOGREDIRECT', WP_HOME);
}

