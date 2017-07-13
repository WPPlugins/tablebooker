<?php
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// ** tablebooker Plugin
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

/**
	@package tablebooker 
	@version 1.1.2

	Plugin Name: tablebooker
	Plugin URI: http://tablebooker.be
	Description: This plugin integrates the tablebooker reservation form into your Wordpress website.
	Author: tablebooker, CVBA
	Version: 1.1.2
	Author URI: http://tablebooker.be
	License: GPL2

	Copyright 2013 tablebooker, CVBA  (email : dev@tablebooker.be)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//followed tutorial http://net.tutsplus.com/tutorials/wordpress/create-wordpress-plugins-with-oop-techniques/

class WPTablebooker {
	var $pluginPath;
	var $pluginUrl;
	  
    public function __construct()  
    {
    	// Set Plugin Path
		$this->pluginPath = dirname(__FILE__);
		// Set Plugin URL
		$this->pluginUrl = WP_PLUGIN_URL . '/tablebooker';
		$this->register_hooks();
    }
    
    /**
	 * Centralized place for adding all actions and filters for the plugin into wordpress
	 */
	function register_hooks(){
		if(is_admin()){
			//register_activation_hook(__FILE__, array(&$this,'install'));
			add_action('admin_menu', array(&$this,'admin_menu_link'));
			add_action('admin_init', array(&$this,'admin_init'));
		}
		else{
			add_shortcode('tablebooker_reservation', array($this, 'reservation_shortcode'));
			// Add shortcode support for widgets
			add_filter('widget_text', 'do_shortcode');
		}
	}
    
    /**
	 * Adds the Administration link under Settings in the Wordpress Menu for Administrators
	 */
	function admin_menu_link(){
		add_options_page('tablebooker options', 'tablebooker', 'administrator', basename(__FILE__), array(&$this,'admin_options_page'));
	}
    
    public function getReservationWidget(){
    	if (!class_exists('TablebookerAPI')){ 
    		include 'tablebookerAPI.php';
    	}
    	
    	$options = get_option('tablebooker_options');
    	if(!$options || !isset($options['restaurant_id'])){
    		return "configure tablebooker plugin";
    	}
    	$tablebookerAPI = new TablebookerAPI($options['restaurant_id']);
    	//echo $options["reservation_form_background"];
		$widget = $tablebookerAPI->getReservationFrom($options["reservation_form_background"], $options['embed_language']);
		
		return $widget;
    }
    
    public function reservation_shortcode($atts)  
    {  
    	return $this->getReservationWidget();
    }
    
    /**
	 * Displays the administration page
	 */
	public function admin_options_page(){
		require_once($this->pluginPath .'/adminPage.php');
	}
	
	public function admin_init(){
		register_setting( 'tablebooker_options', 'tablebooker_options');
		add_settings_section('tablebooker_main', 'tablemanager credentials', array(&$this,'tablebooker_section_text'), 'tablebooker');
		add_settings_field('restaurant_id', __('Restaurant id'), array(&$this,'restaurant_id_field'), 'tablebooker', 'tablebooker_main');
		add_settings_field('reservation_form_background', __('Background color'), array(&$this,'reservation_form_background_field'), 'tablebooker', 'tablebooker_main');
		add_settings_field('embed_language', __('Language'), array(&$this,'embed_language_field'), 'tablebooker', 'tablebooker_main');
	}
	
	public function tablebooker_section_text() {
		echo '<p>Set up the connection with tablemanager.</p>';
	}
	
	function restaurant_id_field() {
		$options = get_option('tablebooker_options');
		echo "<input id='restaurant_id' name='tablebooker_options[restaurant_id]' size='40' type='text' value='{$options['restaurant_id']}' />";
	}
	
	function reservation_form_background_field() {
		$options = get_option('tablebooker_options');
		$select = '<select name="tablebooker_options[reservation_form_background]" id="reservation_form_background"><option value="light" ';
		if($options['reservation_form_background'] == 'light'){
			$select .= 'selected="selected"';
		}
		$select .= '>' . __('Light') . '</option><option value="dark"';
		if($options['reservation_form_background'] == 'dark'){
			$select .= 'selected="selected"';
		}
		$select .= '>' . __('Dark') . '</option></select>';
		echo $select;
	}

	function embed_language_field() {
		$options = get_option('tablebooker_options');
		$select = '<select name="tablebooker_options[embed_language]" id="embed_language">';

		$select .= '<option value="nl"';
		if($options['embed_language'] == 'nl'){
			$select .= ' selected="selected"';
		}
		$select .= '>' . __('Dutch') . '</option>';

		$select .= '<option value="fr"';
		if($options['embed_language'] == 'fr'){
			$select .= ' selected="selected"';
		}
		$select .= '>' . __('French') . '</option>';

		$select .= '<option value="en"';
		if($options['embed_language'] == 'en'){
			$select .= ' selected="selected"';
		}
		$select .= '>' . __('English') . '</option>';

		$select .= '<option value="current"';
		if($options['embed_language'] == 'current'){
			$select .= ' selected="selected"';
		}
		$select .= '>' . __('Site language') . '</option>';

		$select .= '</select>';
		echo $select;
	}
}

/**
 * function that can be used in templates to render the reservation widget
 */
function tablebooker_reservation()
{
	$wpTablebooker = new WPTablebooker;
	echo $wpTablebooker->getReservationWidget();
}
  
$wpTablebooker = new WPTablebooker();
?>