<?php
	
class WPT_Related_Admin {

	function __construct() {
		
		add_filter('admin_init',array($this,'admin_init'));
		add_filter('wpt_admin_page_tabs',array($this,'wpt_admin_page_tabs'));
		
		// The slug used to identify your settings.
		$this->slug = 'wpt_related';

		$this->options = get_option($this->slug);
		
	}
		
		
	/**
	 * Adds settings fields to your new settings tab.
	 *
	 * The settings tabs are based on the Settings API.
	 * @see http://codex.wordpress.org/Settings_API
	 * 
	 * @access public
	 * @return void
	 */
	function admin_init() {		

        register_setting(
            $this->slug, // Option group
            $this->slug // Option name
        );        

        add_settings_section(
            'my_wpt_related_section', // ID
            '', // Title
            '', // Callback
            $this->slug // Page
        );  

        add_settings_field(
            'my_wpt_related_setting', // ID
            __('A related production setting','wpt_related'), // Title 
            array( $this, 'my_wpt_related_setting_callback' ), // Callback
            $this->slug, // Page
            'my_wpt_related_section' // Section           
        );      
	}

	
	/**
	 * Renders the input fields for your new setting.
	 *
	 * This is just an example. You should write your own.
	 * 
	 * @access public
	 * @return void
	 */
	function my_wpt_related_setting_callback() {
		global $wpt_ticketscript;
		
		echo '<input type="text" id="my_wpt_related_setting" name="wpt_related[my_wpt_related_setting]"';
		if (!empty($this->options['my_wpt_related_setting'])) {
			echo ' value="'.$this->options['my_wpt_related_setting'].'"';
			
		}
		echo ' />';
	}

	/**
	 * Adds a new settings tab to the Theater settings screen.
	 * 
	 * @access public
	 * @param array $tabs An array of all tabs on the Theater settings screen.
	 * @return array $tabs
	 */
	function wpt_admin_page_tabs($tabs) {
		$tabs[$this->slug] = __('Related Productions','wpt_related');		
		return $tabs;
	}
	
	
}

new WPT_Related_Admin();