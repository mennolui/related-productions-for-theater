<?php

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	/*
	 * 'Bezoek ons' widget.
	 * Show three links to pages with information about tickets, season tickets and opening hours.
	 * Links can be set in the widget settings.
	 */


	class WPT_Related_Widget extends WP_Widget {
	
		function __construct() {
			parent::__construct(
				'wpt_related', 
				__('Theater Related Productions', 'wpt_related'),
				array( 
					'description' => __('Display related productions for the current production.', 'wpt_related')
				)
			);
		}


		/*
		 * Widget frontend HTML.
		 */
		
		public function widget( $args, $instance ) {
			global $wp_theatre;

			$related_prods_html = $wp_theatre->related->get_related_prods_html();
			
			if (empty($related_prods_html)) {
				return;
			}
	
			echo $args['before_widget'];

			$html = '';
			if (!empty( $instance['title'])) {
				$html.= $args['before_title'] . apply_filters('widget_title', $instance['title']). $args['after_title'];
			}
			$html.= $related_prods_html;

			echo $html;
		
			echo $args['after_widget'];
		}
		
		/*
		 * Widget settings form.
		 * Sets widget title and the three links.
		 */
		
		public function form( $instance ) {
			if ( isset( $instance[ 'title' ] ) ) {
				$title = $instance[ 'title' ];
			}
			else {
				$title = __('Related productions', 'wpt_related');
			}
			
			echo '<p>';
			echo '<label for="'.$this->get_field_id( 'title' ).'">'.__( 'Title:', 'wpt_related').'</label> ';
			echo '<input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="text" value="'.esc_attr( $title ).'">';
			echo '</p>';
					
		}		
		
	}
