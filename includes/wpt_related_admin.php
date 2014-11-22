<?php
	
class WPT_Related_Admin {

	function __construct() {
		
		add_filter('admin_init',array($this,'admin_init'));
		add_filter('wpt_admin_page_tabs',array($this,'wpt_admin_page_tabs'));
		
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
            'my_wpt_related_section', // ID
            '', // Title
            '', // Callback
            $wp_theatre->related->slug // Page
        );  

        add_settings_field(
            'my_wpt_related_setting', // ID
            __('A related production setting','wpt_related'), // Title 
            array( $this, 'my_wpt_related_setting_callback' ), // Callback
            $wp_theatre->related->slug, // Page
            'my_wpt_related_section' // Section           
        );      
	}

	
	/**
	 * Renders the input fields for your new setting.
	 *
	 * This is just an example. You should write your own.
	 * 
	 * @return void
	 */
	function my_wpt_related_setting_callback() {
		global $wp_theatre;
		
		echo '<input type="text" id="my_wpt_related_setting" name="wpt_related[my_wpt_related_setting]"';
		if (!empty($wp_theatre->related->options['my_wpt_related_setting'])) {
			echo ' value="'.$wp_theatre->related->options['my_wpt_related_setting'].'"';
			
		}
		echo ' />';
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