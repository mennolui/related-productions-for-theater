<?php

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	/**
	 * WPT_Related class.
	 *
	 * Adds related productions to the Theater for WordPress plugin.
	 */
	 
	class WPT_Related {
		
		function __construct() {

			// A unique identifier for your plugin.
			$this->slug = 'wpt_related';
			
			/*
			 * Load the options for your plugin.
			 * @see WPT_Related_Admin
			 */
			$this->options = get_option('wpt_related');
			
			$this->load_sub_classes();

			// Go Menno!

		}
		
		/**
		 * Loads and initializes the sub classes of your plugin.
		 * 
		 * @access private
		 * @return void
		 */
		private function load_sub_classes() {
			if (is_admin()) {
				require_once(dirname(__FILE__) . '/wpt_related_admin.php');
				$this->admin = new WPT_Related_Admin();
			}			
		}
		
	}