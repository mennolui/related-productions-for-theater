<?php
/*
Plugin Name: Related Productions for Theater
Version: 0.1.1
Description: Add related productions to the Theater for WordPress plugin.
Author: Menno Luitjes
Author URI: http://mennoluitjes.nl
Plugin URI: http://mennoluitjes.nl
Text Domain: wpt_related
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$wpt_related_version = '0.1.1';

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

	/**
	 * Add an instance of your class to the global Theater object.
	 * 
	 * Requires Theater 0.9.4.
	 */
	$wp_theatre->related = new WPT_Related();

}

add_action('wpt_loaded', 'wpt_related_loader');