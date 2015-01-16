<?php

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	/**
	 * WPT_Related class.
	 *
	 * Adds related productions to the Theater for WordPress plugin.
	 */
	 
	class WPT_Related {

		const manual_name = 'wpt_related_prod_manual';
		
		function __construct() {

			// Set version
			global $wpt_related_version;
			$this->wpt_related_version = $wpt_related_version;

			// A unique identifier for your plugin.
			$this->slug = 'wpt_related';
			
			/*
			 * Load the options for your plugin.
			 * @see WPT_Related_Admin
			 */
			$this->options = get_option('wpt_related');
			
			$this->load_sub_classes();


			add_filter('wpt_production_page_content_after',array($this,'wpt_production_page_content_after'));

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

		function wpt_production_page_content_after($after) {
			$after .= $this->get_related_prods_html();

			return $after;
		}

		function get_related_prods_html() {
			$html = '';
			$related_prods = $this->get_related_prods();
			if (count($related_prods) > 0) {
				$html .= '<h3>'.__('Related productions','wpt_related').'</h3>';
				$html .= '<ul>';
				foreach ($related_prods as $related_prod) {
					$production = new WPT_Production($related_prod);
					$html .= '<li>'.$production->html().'</li>';
				}
				$html .= '</ul>';
			}
			return $html;
		}

		function get_related_prods() {
			global $wp_theatre;
			
			// Set a default limit.
			$limit = 5;

			if (!empty($wp_theatre->related->options['wpt_related_limit'])) {
				$limit = $wp_theatre->related->options['wpt_related_limit'];			
			}

			$related_prods = $this->get_related_prods_manual();
			if (count($related_prods) < $limit) {
				// add some more
				$related_prods = array_merge($related_prods,$this->get_related_prods_manual_after());
				if (count($related_prods) < $limit) {
					// add even more
					$related_prods = array_merge($related_prods,$this->get_related_prods_category());
				}
			}

			// make sure we're not over the limit
			$related_prods = array_slice($related_prods,0,$limit);

			return $related_prods;
		}

		function get_related_prods_manual() {
			global $post;
			$related_prods_manual = get_post_meta($post->ID,WPT_Related::manual_name,true);
			return $related_prods_manual;
		}

		function get_related_prods_manual_after() {
			$related_prods_manual_after = array();

			/**
			 * Filter the related prods after the manual related prods
			 *
			 * @param array  $related_prods_manual_after	The current related prods after the manual related prods.
			 */	
			$related_prods_manual_after = apply_filters('wpt_related_prods_manual_after', $related_prods_manual_after);
			return $related_prods_manual_after;
		}

		function get_related_prods_category() {
			//@todo: return upcoming productions from same category
			return array();
		}
	}