<?php
/*
Plugin Name: Class Filters Function Lib
Description: This plugin is essential for removing annoying actions added by anonymous classes in 3rd party plugins or themes.
Original Code: https://github.com/herewithme/wp-filters-extras @Feb 25, 2013
*/
/*
 Don't call this file directly.
*/
!defined('ABSPATH') && header("HTTP/1.1 404 Not Found") && exit('404 Error: File Not Found.');
!defined('WP_PLUGIN_DIR') && header("HTTP/1.1 404 Not Found") && exit('404 Error: File Not Found.');
!defined('WPMU_PLUGIN_DIR') && header("HTTP/1.1 404 Not Found") && exit('404 Error: File Not Found.');

/**
 * Allow to remove method for an hook when, it's a class method used and class don't have global for instanciation !
 */
if ( ! function_exists( 'remove_filters_with_method_name' ) ) {
	function remove_filters_with_method_name( $hook_name = '', $method_name = '', $priority = 0 ) {
		global $wp_filter;
		
		// Take only filters on right hook name and priority
		if ( !isset($wp_filter[$hook_name][$priority]) || !is_array($wp_filter[$hook_name][$priority]) )
			return false;
		
		// Loop on filters registered
		foreach( (array) $wp_filter[$hook_name][$priority] as $unique_id => $filter_array ) {
			// Test if filter is an array ! (always for class/method)
			if ( isset($filter_array['function']) && is_array($filter_array['function']) ) {
				// Test if object is a class and method is equal to param !
				if ( is_object($filter_array['function'][0]) && get_class($filter_array['function'][0]) && $filter_array['function'][1] == $method_name ) {
					unset($wp_filter[$hook_name][$priority][$unique_id]);
				}
			}
			
		}
		
		return false;
	}
}

/**
 * Allow to remove method for an hook when, it's a class method used and class don't have variable, but you know the class name :)
*/
if ( ! function_exists( 'remove_filters_for_anonymous_class' ) )
{
	function remove_filters_for_anonymous_class( $hook_name = '', $class_name ='', $method_name = '', $priority = 0 ) {
		global $wp_filter;
		
		// Take only filters on right hook name and priority
		if ( !isset($wp_filter[$hook_name][$priority]) || !is_array($wp_filter[$hook_name][$priority]) )
			return false;
		
		// Loop on filters registered
		foreach( (array) $wp_filter[$hook_name][$priority] as $unique_id => $filter_array ) {
			// Test if filter is an array ! (always for class/method)
			if ( isset($filter_array['function']) && is_array($filter_array['function']) ) {
				// Test if object is a class, class and method is equal to param !
				if ( is_object($filter_array['function'][0]) && get_class($filter_array['function'][0]) && get_class($filter_array['function'][0]) == $class_name && $filter_array['function'][1] == $method_name ) {
					unset($wp_filter[$hook_name][$priority][$unique_id]);
				}
			}
			
		}
		
		return false;
	}
}
