<?php
/*
Plugin Name: Ultra Menu
Plugin URI: http://catapultthemes.com/create-your-own-mega-menu-in-wordpress/
Description: Add image upload and other custom fields to your WordPress menu items. Based on work by Dzikri Aziz at {@link https://github.com/kucrut/wp-menu-item-custom-fields} and Weston Ruter at {@link https://gist.github.com/3802459 gist}
Version: 1.0.0
Author: Catapult Themes
Author URI: http://catapultthemes.com/
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin class
 **/
 
if ( ! class_exists( 'Ultra_Menu' ) ) :
	/**
	* Menu Item Custom Fields Loader
	*/
	class Ultra_Menu {
		/**
		* Add filter
		*
		* @wp_hook action wp_loaded
		*/
		public static function load() {
			add_filter( 'wp_edit_nav_menu_walker', array( __CLASS__, '_filter_walker' ), 99 );
			add_action( 'admin_enqueue_scripts', array ( __CLASS__, 'enqueue_admin_scripts' ), 10, 1 );
			add_action( 'wp_enqueue_scripts', array ( __CLASS__, 'enqueue_scripts' ) );
		}
		/**
		* Replace default menu editor walker with ours
		*
		* We don't actually replace the default walker. We're still using it and
		* only injecting some HTMLs.
		*
		* @since   0.1.0
		* @access  private
		* @wp_hook filter wp_edit_nav_menu_walker
		* @param   string $walker Walker class name
		* @return  string Walker class name
		*/
		public static function _filter_walker( $walker ) {
			$walker = 'Ultra_Menu_Walker';
			if ( ! class_exists( $walker ) ) {
				require_once dirname( __FILE__ ) . '/walker-nav-menu-edit.php';
			}
			return $walker;
		}
		/**
		* Enqueue the script for the media uploader
		* Only on the nav-menu.php page
		*/
		public static function enqueue_admin_scripts( $hook ) {
			if ( 'nav-menus.php' == $hook ) {
				wp_enqueue_script ( 'ultra-menu-script', plugin_dir_url( __FILE__ ) . 'js/ultra-menu-script.js' );
			}
		}
		/**
		* Enqueue the CSS for the front end
		*/
		public static function enqueue_scripts() {
			wp_enqueue_style ( 'ultra-menu-styles', plugin_dir_url( __FILE__ ) . 'css/ultra-menu-style.css' );
		}
	}
	add_action( 'wp_loaded', array( 'Ultra_Menu', 'load' ), 9 );
endif; // class_exists( 'Ultra_Menu' )

require_once dirname( __FILE__ ) . '/class-ultra-menu-fields.php';
require_once dirname( __FILE__ ) . '/ultra-menu-filters.php';

