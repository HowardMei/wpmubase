<?php 
/**
* Plugin Name: Base Module Loader
* Description: This plugin is designed to load base modules and deactivate conflicted plugins.
* Plugin URI: https://github.com/HowardMei/wpmubase
* Version: 1.0
* Author: Howard Mei
* Author URI: https://github.com/HowardMei
 **/
 
/*
 Don't call this file directly.
*/
!defined('ABSPATH') && header("HTTP/1.1 404 Not Found") && exit('404 Error: File Not Found.');
!defined('WP_PLUGIN_DIR') && header("HTTP/1.1 404 Not Found") && exit('404 Error: File Not Found.');
!defined('WPMU_PLUGIN_DIR') && header("HTTP/1.1 404 Not Found") && exit('404 Error: File Not Found.');
if (!is_blog_installed()) { return; }

// Load base modules and deactivate 3rd party plugins conflicting with these modules.
class BaseModuleLoader {
	private static $instance = false;
	private static $basemodules = array();
	private static $conflictplugins = array();
	private static $noload_modules = array('index.php','relocationrewrite.php',);

	private static $module_plugins_conflictmap = array(
			'adminshowid.php' => array(
				'ShowID for Post-etc' => 'showid-for-postpagecategorytagcomment/showid.php',
			),
			'clearadminbar.php' => array(
			
			),
			'clearadminmenu.php' => array(
			
			),
			'cleardashboard.php' => array(
			
			),
			'clearfrontoutput.php' => array(
			
			),
			'relocationrewrite.php' => array(
				'HTML5 Boilerplate .htaccess' => 'wp-h5bp-htaccess/wp-h5bp-htaccess.php',
			),
			'fastuserswitching.php' => array(
				'User Switching' => 'user-switching/user-switching.php',
			),
			'makeurlsrelative.php' => array(
				'Root Relative URLs' => 'roots/soil',
				'Root Relative URLs' => 'root-relative-urls/sb_root_relative_urls.php',
			),
			'no3rdpartyads.php' => array(
			
			),
			'nobrokenshortcodes.php' => array(
			
			),
			'nowpmudevad.php' => array(
			
			),
			'optionsadminbar.php' => array(
			
			),
		);


	public static function get_instance() {
		if ( !self::$instance ) {
			self::$instance = new static();
			self::get_basemodules();
			if ( did_action( 'plugins_loaded' ) ) {
				self::plugin_textdomain();
			} else {
				add_action( 'plugins_loaded', array( __CLASS__, 'plugins_textdomain' ) );
				add_filter( 'all_plugins', array( __CLASS__, 'mute_conflictplugins' ) );
				//add_action( 'admin_init', array( __CLASS__, 'add_basemods2muplugin' ) );
			}
			self::load_basemodules();
			self::add_basemods2muplugin();
		}

		return self::$instance;
	}

	private static function get_basemodules() {
		// Open base module dir and list base modules
		if ( $plugins_dir = @opendir( WPMU_PLUGIN_DIR . '/basemods' ) ) {
			while ( ( $file = readdir( $plugins_dir ) ) !== false ) {
								if ( substr( $file, -4 ) == '.php' )
										self::$basemodules[] = $file;
						}
		}
		@closedir( $plugins_dir );
		if ( empty(self::$noload_modules) ) {
			return self::$basemodules;
		} else {
			self::$basemodules = array_diff(self::$basemodules, self::$noload_modules);
			return self::$basemodules;
		}
	}
	
	private static function get_conflictplugins() {
		$basemodules = array_filter(self::get_basemodules());
	    $module_plugins_conflict = array_filter(self::module_plugins_conflictmap);// remove empty keys
		
		foreach ( $module_plugins_conflict as $module=>$plugins ) {
			if( in_array($module, $basemodules) && is_array($plugins) ) {
				foreach ( $plugins as $plugintitle=>$pluginid ) {
					self::$conflict_plugins[] = $pluginid; }
			}
		}
		return array_filter(self::$conflict_plugins);
	}

	public static function plugins_textdomain() {
		load_plugin_textdomain( 'basemodules', false, WPMU_PLUGIN_DIR . '/lang/' );
	}

	public static function mute_conflictplugins( $plugins ) {
	// Prevent specific plugins from being activated (or, in some cases, deactivated).
	// Allow the super admin to see all plugins, by adding the URL param, show_all_plugins=1
		if ( is_super_admin() ) {
			return $plugins;
		}
		if ( ! empty(self::$conflict_plugins) ) {
			foreach ( $conflicting_plugins as $plugin_todisable ) {
				// Deactivate conflicting plugins networkwide
				if  ( is_plugin_active( $plugin_todisable ) ) {
					deactivate_plugins($plugin_todisable, true); }
				// Hide all conflicting plugins from list after deactivation
				if ( array_key_exists( $plugin_todisable, $plugins ) && empty( $_GET['show_all_plugins'] ) ) {
					unset( $plugins[ $plugin_todisable ] );	}
				}
		}
		return $plugins;
	}
	
	public static function load_basemodules() {
		if ( ! empty(self::$basemodules) ) {
			foreach ( self::$basemodules as $plugin_file ) {
				if ( !is_readable( WPMU_PLUGIN_DIR . '/basemods/' . "$plugin_file" ) )
								continue;
				require_once( WPMU_PLUGIN_DIR . '/basemods/' . "$plugin_file" );
			}
		}
	}
	

	private static function add_basemods2muplugin() {
        if ( ! empty(self::$basemodules) ) {
			add_action('admin_init', function() {
				add_action('after_plugin_row_mu-require.php', function() {
					$table = new WP_Plugins_List_Table;
					foreach ( self::$basemodules as $plugin_file ) {
						$plugin_data = get_plugin_data( WPMU_PLUGIN_DIR . '/basemods/' . "$plugin_file", false);
						if (empty($plugin_data['Name'])) {
							$plugin_data['Name'] = $plugin_file;
						}
						$plugin_data['Name'] = "&nbsp;&nbsp;&nbsp;&nbsp;+&nbsp;&nbsp;" . $plugin_data['Name'];
						$table->single_row(array($plugin_file, $plugin_data));
					}
				});
			});
		}
	}
}

$BaseModuleLoader = BaseModuleLoader::get_instance();
