<?php
	
class WPT_Related_Admin {

	function __construct() {
		
		add_filter('admin_init',array($this,'admin_init'));
		add_filter('wpt_admin_page_tabs',array($this,'wpt_admin_page_tabs'));

		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
		add_action('save_post_'.WPT_Production::post_type_name, array($this,'save_production'),10);

	}
		
		
	/**
	 * Adds settings fields to your new settings tab.
	 *
	 * The settings tabs are based on the Settings API.
	 * @see http://codex.wordpress.org/Settings_API
	 * 
	 * @return void
	 */
	function admin_init() {
		
		global $wp_theatre;	

        register_setting(
            $wp_theatre->related->slug, // Option group
            $wp_theatre->related->slug // Option name
        );        

        add_settings_section(
            'wpt_related_general', // ID
            '', // Title
            '', // Callback
            $wp_theatre->related->slug // Page
        );  

        add_settings_field(
            'wpt_related_limit', // ID
            __('Show this amount of related productions','wpt_related'), // Title 
            array( $this, 'settings_field_limit' ), // Callback
            $wp_theatre->related->slug, // Page
            'wpt_related_general' // Section           
        );      
	}

	function add_meta_boxes() {
		add_meta_box(
			'wpt_related_prods_manual',
			__('Related productions','wpt_related'),
			array($this,'meta_box_related_prods_manual'),
			WPT_Production::post_type()->name,
			'side'
		); 		
	}
	
	/**
	 * Show a meta box with a related productions multiple select for a production.
	 * http://codex.wordpress.org/Function_Reference/add_meta_box
	 * 
	 * @access public
	 * @since 0.1
	 * @param WP_Post $production
	 * @param mixed $metabox
	 * @see WPT_Related_Admin::add_meta_boxes()
	 * @return void
	 */
	function meta_box_related_prods_manual($production) {
		wp_nonce_field(WPT_Related::manual_name, WPT_Related::manual_name.'_nonce' );

		$args = array(
			'post_type'=>WPT_Production::post_type_name,
			'posts_per_page' => -1
		);

		$related_prods_manual = get_post_meta($production->ID,WPT_Related::manual_name,true);

		$prods = get_posts($args);
		echo '<select multiple="multiple" name="'.WPT_Related::manual_name.'[]">';
		echo '<option></option>';
		if (count($prods)>0) {
			foreach ($prods as $prod) {
				if ($prod->ID != $production->ID) {
					echo '<option value="'.$prod->ID.'"';
					if (in_array($prod->ID,$related_prods_manual)) {
						echo ' selected="selected"';
					}
					echo '>';
					echo $prod->post_title;
					echo '</option>';
				}
			}

		}
		echo '</select>';
	
	}

	function save_production( $post_id ) {
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST[WPT_Related::manual_name.'_nonce'] ) )
			return $post_id;

		$nonce = $_POST[WPT_Related::manual_name.'_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, WPT_Related::manual_name ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		$related_prods_manual = array_map('sanitize_text_field', $_POST[WPT_Related::manual_name]);
		
		// Update the meta field.
		update_post_meta( $post_id, WPT_Related::manual_name, $related_prods_manual );

		/**
		 * Fires after a production is saved through the admin screen.
		 */
		do_action('wpt_related_admin_after_save_'.WPT_Production::post_type_name, $post_id);
	}


	/**
	 * Renders the 'limit' select field of related productions settings.
	 * 
	 * @return void
	 */	 	
	function settings_field_limit() {
		global $wp_theatre;
		
		echo '<select id="wpt_related_limit" name="wpt_related[wpt_related_limit]">';
		echo '<option></option>';
		for($i=0; $i<=10; $i++) {
			echo '<option value="'.$i.'"';
			if ($i==$wp_theatre->related->options['wpt_related_limit']) {
				echo ' selected="selected"';
			}
			echo '>'.$i.'</option>';
		}
		echo '</select>';
	}

	/**
	 * Adds a new settings tab to the Theater settings screen.
	 * 
	 * @param array $tabs An array of all tabs on the Theater settings screen.
	 * @return array $tabs
	 */
	function wpt_admin_page_tabs($tabs) {
		global $wp_theatre;
		$tabs[$wp_theatre->related->slug] = __('Related Productions','wpt_related');		
		return $tabs;
	}
	
	
}

//new WPT_Related_Admin();