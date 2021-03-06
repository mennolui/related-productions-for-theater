<?php
/*
Plugin Name: Related Productions for Theater
Version: 0.1.3
Description: Add related productions to the Theater for WordPress plugin.
Author: Menno Luitjes
Author URI: http://mennoluitjes.nl
Plugin URI: http://mennoluitjes.nl
Text Domain: wpt_related
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$wpt_related_version = '0.1.3';

/**
 * Loads the WPT_Related class.
 *
 * Triggered by the `wpt_loaded` action, which is fired after the Theater for WordPress plugin is loaded.
 * 
 * @access public
 * @return void
 */
function wpt_related_loader() {
	global $wp_theatre;
	
	require_once(dirname(__FILE__) . '/includes/wpt_related.php');
	require_once(dirname(__FILE__) . '/includes/widget.php');

	add_action('widgets_init', 'widgets_init');

	/**
	 * Add an instance of your class to the global Theater object.
	 * 
	 * Requires Theater 0.9.4.
	 */
	$wp_theatre->related = new WPT_Related();

}

function widgets_init() {
	register_widget('WPT_Related_Widget');
}

add_action('wpt_loaded', 'wpt_related_loader');