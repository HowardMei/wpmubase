<?php

// Web root entry point and view bootstrapper
define('WP_USE_THEMES', true);
require(__DIR__ . '/wp-blog-header.php');

// Load the wordpress core
if ( !isset($wp_did_loadcore) ) {

	$wp_did_loadcore = true;

	require_once( __DIR__ . '/core/wp-load.php' );

	wp();
	
	!function_exists('wp') && exit('Error: index #1');
	
	require_once( ABSPATH . WPINC . '/template-loader.php' );
}


// Load custom global header and footer (w/ custom 404 page etc.)
require(__DIR__ . '/wp-blog-footer.php');
