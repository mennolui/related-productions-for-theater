<?php
/*
Plugin Name: Related Productions for Theater
Version: 0.1
Description: Add related productions to the Theater for WordPress plugin.
Author: Menno Luitjes
Author URI: http://mennoluitjes.nl
Plugin URI: http://mennoluitjes.nl
Text Domain: wpt_related
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Loads the WPT_Related class.
 *
 * Triggered by the `wpt_loaded` action, which is fired after the Theater for WordPress plugin is loaded.
 * 
 * @access public
 * @return void
 */
function wpt_related_loader() {
	require_once(dirname(__FILE__) . '/includes/wpt_related.php');		

	if (is_admin()) {
		require_once(dirname(__FILE__) . '/includes/wpt_related_admin.php');			
	}
}

add_action('wpt_loaded', 'wpt_related_loader');