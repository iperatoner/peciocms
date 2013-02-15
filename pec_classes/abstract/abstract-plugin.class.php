<?php

/**
 * pec_classes/abstract/abstract-plugin.class.php - Abstract Plugin Class
 * 
 * Defines the class from which plugin will inherit.
 * 
 * LICENSE: This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU General Public License as published by the 
 * Free Software Foundation, either version 3 of the License, or (at your option) 
 * any later version. This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License 
 * for more details. You should have received a copy of the 
 * GNU General Public License along with this program. 
 * If not, see <http://www.gnu.org/licenses/>.
 * 
 * @package		peciocms
 * @subpackage	pec_classes.abstract
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */

/**
 * The PecAbstractPlugin provides basic attributes for plugins and must be inherited by your plugin class.
 */
abstract class PecAbstractPlugin {
    
	/**
	 * @var PecSetting $settings Pecio's settings object.
	 */
    protected $settings;
    
    
	/**
	 * @var PecDatabase $database Pecio's database object.
	 */
    protected $database;
    
    
	/**
	 * @var PecSession	$session Pecio's session object.
	 */
    protected $session;
    
    
	/**
	 * @var PecLocale	$localization Pecio's localization object.
	 */
    protected $localization;
    
    
	/**
	 * @var array	$current_page Holds all the relevant data belonging to the currently being viewed page
	 */
	protected $current_page;
	
    
	/**
	 * @var PecPlugin	$plugin_meta This plugin's meta data object
	 */
    protected  $plugin_meta;
    
    
    /**
     * Creates a PecAbstractPlugin instance.
     */
    function __construct() {
        global $pec_database, $pec_settings, $pec_session, $pec_localization;
        
        $this->database =& $pec_database;
        $this->settings =& $pec_settings;
        $this->session =& $pec_session;
        $this->localization =& $pec_localization;
    }

    
    /**
     * Sets the meta data object for this plugin.
     * 
     * @param	PecPlugin	$plugin_meta Meta data object of this plugin
     */
    final public function set_plugin_meta($plugin_meta) {
    	$this->plugin_meta = $plugin_meta;
    }

    
    /**
     * Returns the meta data object of this plugin.
     * 
     * @return	PecPlugin	Meta data object of this plugin
     */
    final public function get_plugin_meta() {
    	return $this->plugin_meta;
    }

    
    /**
     * Sets the current page data array for this plugin.
     * 
     * @param	array	$current_page Holds all the relevant data belonging to the currently being viewed page
     */
    final public function set_current_page($current_page) {
    	if (is_array($current_page)) {
    		$this->current_page = $current_page;
    	}
    }
    
    
    /**
     * May return data to replace the plugin's variable
     * 
     * @param	string	$var_data If the plugin accepts input, it will be put here
     * @return	string Return data to replace the plugin's variable
     */
    abstract public function run($var_data='');
    
    
    /**
     * May return data to place into the template's <head> section
     * 
     * @return	string Return data to place into the <head> section
     */
    abstract public function head_data();
        
}

?>
