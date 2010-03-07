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
 * @version		2.0.2
 * @link		http://pecio-cms.com
 */

abstract class PecAbstractPlugin {
    
    protected $database, $settings, $session, $localization, $plugin_meta;
    
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
    
    abstract public function run($var_data='');
    abstract public function head_data();
        
}

?>
