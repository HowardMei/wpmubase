<?php namespace Common\Basemods;
/*
Plugin Name: Admin Show All Items ID
Plugin URI: https://github.com/HowardMei/wpmubase
Description: This plugin shows post/page/category/tag/comment/media/user IDs for admin users.
Version: 2.0
Adapted From: http://wordpress.org/plugins/showid-for-postpagecategorytagcomment/
*/

/*
 Don't call this file directly.
*/
if ( ! class_exists('WP') ) {
	header('Status: 404 Not Found');
	header('HTTP/1.1 404 Not Found');
	exit('404 Error: File Not Found.');
}

if ( is_admin() || is_network_admin() && current_user_can('manage_options') ) {
   //Never load the system if not necessarily need to.
   
	function always_showid() {
	?>
	<style type="text/css">div.row-actions{visibility:visible !important;} #object_id { width:2%;}</style>
	<?php
	}
	add_action( 'admin_head', __NAMESPACE__ . '\\always_showid' );

	function showid_userid_add($actions,$user_object) {
		if( isset( $actions['edit'] ) ) {
			$actions['edit'] = '<span class="wp-ui-text-primary">' . "ID:" . intval( $user_object->ID ) . '</span>' . " | " . $actions['edit'];
		}
		return $actions;
	}
	add_filter( 'user_row_actions', __NAMESPACE__ . '\\showid_userid_add', '10', '2' );

	function showid_mediaid_add($actions,$post) {
		if( isset( $actions['edit'] ) ) {
			$actions['edit'] = '<span class="wp-ui-text-primary">' . "ID:" . intval( $post->ID ) . '</span>' . " | " . $actions['edit'];
		}
		return $actions;
	}
	add_filter( 'media_row_actions', __NAMESPACE__ . '\\showid_mediaid_add', '10', '2' );

	function showid_link_catid_add($actions,$category) {
		if( isset( $actions['edit'] ) ) {
			$actions['edit'] = '<span class="wp-ui-text-primary">' . "ID:" . intval( $category->term_id ) . '</span>' . " | " . $actions['edit'];
		}
		return $actions;
	}
	add_filter( 'link_cat_row_actions', __NAMESPACE__ . '\\showid_link_catid_add', '10', '2' );

	function showid_postid_show($actions,$post) {
		if ( current_user_can( 'edit_posts' ) ) {
			if( isset( $actions['edit'] ) ) {
				$actions['edit'] = '<span class="wp-ui-text-primary">' . "ID:" . intval( $post->ID ) . '</span>' . " | " . $actions['edit'];
			}
		}
		return $actions;
	}
	add_filter( 'post_row_actions', __NAMESPACE__ . '\\showid_postid_show', '10', '2' );

	function showid_pageid_show($actions,$page) {
		if ( current_user_can( 'edit_pages' ) ) {
			if( isset( $actions['edit'] ) ) {
				$actions['edit'] = '<span class="wp-ui-text-primary">' . "ID:" . intval( $page->ID ) . '</span>' . " | " . $actions['edit'];
			}
		}
		return $actions;
	}
	add_filter( 'page_row_actions', __NAMESPACE__ . '\\showid_pageid_show', '10', '2' );

	function showid_catid_show($actions,$category) {
		if ( current_user_can( 'manage_categories' ) ) {
			if( isset( $actions['edit'] ) ) {
				$actions['edit'] = '<span class="wp-ui-text-primary">' . "ID:" . intval ( $category->term_id ) . '</span>' . " | " . $actions['edit'];
			}
		}
		return $actions;
	}
	add_filter( 'cat_row_actions', __NAMESPACE__ . '\\showid_catid_show', '10', '2' );

	function showid_tagid_show($actions,$tag) {
		if ( current_user_can( 'edit_posts' ) ) {
			if( isset( $actions['edit'] ) ) {
				$actions['edit'] = '<span class="wp-ui-text-primary">' . "ID:" . intval( $tag->term_id ) . '</span>' . " | " . $actions['edit'];
			}
		}
		return $actions;
	}
	add_filter( 'tag_row_actions', __NAMESPACE__ . '\\showid_tagid_show', '10', '2' );

	function showid_commentid_show($actions,$comment) {
		if ( current_user_can( 'moderate_comments' ) ) {
			if( isset( $actions['edit'] ) ) {
				$actions['edit'] = '<span class="wp-ui-text-primary">' . "ID:" . intval( $comment->comment_ID ) . '</span>' . " | " . $actions['edit'];
			}
		}
		return $actions;
	}
	add_filter( 'comment_row_actions', __NAMESPACE__ . '\\showid_commentid_show', '10', '2' );

	if (is_multisite()) {
		//add_action( 'admin_menu', 'blog_id_in_site_admin_menu' );
		if ( function_exists( 'wp_editor' ) ) //3.3
			add_filter( 'admin_bar_menu', __NAMESPACE__ . '\\blog_id_in_site_menu', 200 );
		else
			add_filter( 'admin_user_info_links', __NAMESPACE__ . '\\blog_id_in_howdy_greeting' );

		function blog_id_in_site_admin_menu(){
			global $blog_id;

			if ( function_exists( 'is_network_admin' ) ) { //3.1
				add_submenu_page('index.php', "Site ID:  $blog_id", "Site ID: $blog_id", 'administrator', network_admin_url( "site-info.php?id=$blog_id" ) );
			}
			elseif ( function_exists( 'is_super_admin' ) ) { //2.9-3.0
				add_submenu_page('ms-admin.php', "Site ID: $blog_id", "Site ID: $blog_id", 'administrator',' /ms-sites.php?action=editblog&id=' . intval( $blog_id ));
			}
			elseif ( function_exists( 'is_site_admin' ) ) { //2.9mu
				add_submenu_page('wpmu-admin.php', "Blog ID: $blog_id", "Blog ID: $blog_id", 'administrator', '/wpmu-blogs.php?action=editblog&id=' . intval( $blog_id ));
			}
		}
			
		function blog_id_in_howdy_greeting($links) {
			global $blog_id;
			
			if ( function_exists( 'is_network_admin' ) ) { //3.1
				if ( is_super_admin() )
					$links[] = ' | <a href="'. network_admin_url( "site-info.php?id=$blog_id" ) . "\">Site ID: $blog_id</a>";
				else
					$links[] = ' | Site ID: ' . intval( $blog_id );
			}
			elseif ( function_exists( 'is_super_admin' ) ) { //2.9-3.0
				if ( is_super_admin() )
					$links[] = " | <a href=admin_url('/ms-sites.php?action=editblog&id=$blog_id')>Site ID: $blog_id</a>";
				else
					$links[] = ' | Site ID: ' . intval( $blog_id );
			}

			return $links;
			}
		
		function blog_id_in_site_menu( $wp_admin_bar ) {
			global $blog_id;
			$edit_url = network_admin_url( "site-info.php?id=$blog_id");
			$node = array(
				'id'     => 'site-id',
				'parent' => 'site-name',
				'title'  => "Site ID: $blog_id",
				'href'   => $edit_url,
			);
			if ( ! is_super_admin() ) unset($node['href']);
			$wp_admin_bar->add_node( $node );
			}

		add_action( 'admin_init', array( __NAMESPACE__ . '\\Show_Super_Admin_Id', 'init' ) );
		
		class Show_Super_Admin_Id {
			
			public static function init() {
				
				$class = __CLASS__ ;
				if ( empty( $GLOBALS[ $class ] ) )
					$GLOBALS[ $class ] = new $class;
			}
			
			/**
			 * Init function to register all used hooks
			 * 
			 * @since   0.0.1
			 * @return  void
			 */
			public function __construct() {
				
				if ( ! is_network_admin() )
					return NULL;
				
				// add blog id
				add_filter( 'wpmu_blogs_columns',           array( $this, 'get_id' ) );
				add_action( 'manage_sites_custom_column',   array( $this, 'get_blog_id' ), 10, 2 );
				
				// add user id
				add_filter( 'manage_users-network_columns', array( $this, 'get_id' ) );
				add_action( 'manage_users_custom_column',   array( $this, 'get_user_id' ), 10, 3 );
			}
			
			public function get_blog_id( $column_name, $blog_id ) {
				
				if ( 'object_id' === $column_name )
					echo intval( $blog_id );
				
				return $column_name;
			}
			
			public function get_user_id( $value, $column_name, $user_id ) {
				
				if ( 'object_id' === $column_name )
					echo intval( $user_id );
			}
			
			// Add in a column header
			public function get_id( $columns ) {
				
				$columns['object_id'] = __('ID');
				
				return $columns;
			}
		} // end class
	}  //end if (is_multisite())
}
