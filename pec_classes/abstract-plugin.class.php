<?php

/**
 * pec_classes/abstract-plugin.class.php - Abstract Plugin Class
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
 * @subpackage	pec_classes
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
    
    protected $database, $settings, $session, $localization, $plugin_meta, $site_view, $sub_site_view;
    
    
    /**
     * Creates a PecAbstractPlugin instance.
     * 
     * @param	PecPlugin	$plugin_meta Meta data of this plugin
     * @param	string		$site_view The current site view
     * @param	string		$sub_site_view The current sub site view
     */
    function __construct($plugin_meta, $site_view=false, $sub_site_view=false) {
    					 	
        global $pec_database, $pec_settings, $pec_session, $pec_localization;
        
        $this->database = $pec_database;
        $this->settings = $pec_settings;
        $this->session = $pec_session;
        $this->localization = $pec_localization;
        
        $this->plugin_meta = $plugin_meta;
        
        $this->site_view = $site_view;
        $this->sub_site_view = $sub_site_view;
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
