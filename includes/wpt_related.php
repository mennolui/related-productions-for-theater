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

		/**
		 * Get the HTML for the related productions.
		 * 
		 * @access public
		 * @return string
		 */
		public function get_related_prods_html() {
			$html = '';
			$related_prods = $this->get_related_prods();
			if (!empty($related_prods)) {
				$html .= '<h3>'.__('Related productions','wpt_related').'</h3>';
				$html .= '<ul>';
				foreach ($related_prods as $related_prod) {
					$html .= '<li>'.$related_prod->html().'</li>';
				}
				$html .= '</ul>';
			}
			return $html;
		}

		/**
		 * Get the related productions.
		 * 
		 * @access public
		 * @return array of WP_Production objects
		 */
		public function get_related_prods() {
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
					$related_prods = array_merge($related_prods,$this->get_related_prods_category($related_prods));
				}
	
				// make sure we're not over the limit
				$related_prods = array_slice($related_prods,0,$limit);
			}
			return $related_prods;
		}

		/**
		 * Get the productions that are related by manual selection (selected in the Production admin as 'related productions').
		 * 
		 * @access private
		 * @return array of WP_Production objects
		 */
		private function get_related_prods_manual() {
			global $post;

			$related_prods_manual = array();

			$related_prods_manual_ids = get_post_meta($post->ID,WPT_Related::manual_name,true);
			if (is_array($related_prods_manual_ids)) {
				foreach ($related_prods_manual_ids as $prod_id) {
					$production = new WPT_Production($prod_id);
					$related_prods_manual[] = $production;
				}
			}
			return $related_prods_manual;
		}

		private function get_related_prods_manual_after() {
			$related_prods_manual_after = array();

			/**
			 * Filter the related prods after the manual related prods
			 *
			 * @param array  $related_prods_manual_after	The current related prods after the manual related prods.
			 */	
			$related_prods_manual_after = apply_filters('wpt_related_prods_manual_after', $related_prods_manual_after);
			return $related_prods_manual_after;
		}

		/**
		 * Get the (upcoming) productions that are related by category.
		 * 
		 * @since 0.1.3
		 *
		 * @access public
		 * @param array $related_prods
		 * @return array of WP_Production objects
		 */
		private function get_related_prods_category($related_prods) {
			global $post;
			global $wp_theatre;

			$related_prods_category = array();

			$categories = get_the_terms($post->ID, 'category');
			if (is_array($categories)) {

				// Get the categories of current post
				$cat_ids = array();
				foreach ($categories as $cat) {
					$cat_ids[] = $cat->term_id;
				}

				// Exclude current post and previously found related productions
				$exclude_ids = array();
				$exclude_ids[] = $post->ID;
				foreach ($related_prods as $prod) {
					$exclude_ids[] = $prod->ID;
				}

				$args = array(
					'cat' => implode(',', $cat_ids),
					'post__not_in' => $exclude_ids,
					'upcoming' => true,
				);

				$related_prods_category = $wp_theatre->productions->get($args);
			}
			
			return $related_prods_category;
		}
	}