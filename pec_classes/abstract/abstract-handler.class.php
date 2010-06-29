<?php

/**
 * pec_classes/abstract/abstract-handler.class.php - Containing an abstract handler class
 * 
 * Contains the PecAbstractHandler which is the base class for creating/modifying the current view's available objects
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
 * PecAbstractHandler is the base class for all handlers 
 * that create and/or modify data for/of the current view.
 */
class PecAbstractHandler {
    
	/**
	 * @var PecSetting $settings Pecio settings object.
	 */
    protected $settings;
    
    
	/**
	 * @var PecDatabase	$database Pecio's database object.
	 */
    protected $database;
    
    
	/**
	 * @var PecLocale	$localization Pecio's localization object.
	 */
    protected $localization;
    
    
	/**
	 * @var array	$current_page Holds all the relevant data belonging to the currently being viewed page
	 */
	protected $current_page;
    
    
	/**
	 * @var boolean	$is_404 Wether something does not exist and is worth to raise a 404 or not
	 */
	protected $is_404 = false;

	
    /**
     * Creates a PecAbstractHandler instance.
     */
    function __construct() {
    	global $pec_settings, $pec_database, $pec_localization;
    	
    	$this->settings =& $pec_settings;
    	$this->database =& $pec_database;
    	$this->localization =& $pec_localization;
    }

    
    /**
     * Sets the current page data array for this handler. This can't be done in the `__construct` method, because handlers are built before the `$current_page`-array is available.
     * 
     * @param	array	$current_page Holds all the relevant data belonging to the currently being viewed page
     */
    final public function set_current_page($current_page) {
    	if (is_array($current_page)) {
    		$this->current_page = $current_page;
    	}
    }
    
    
    /**
     * May update the PecTemplateResource with new data (e.g. the current article etc.)
     * 
     * @param	PecTemplateResource $template_resource Holds a lot of data (e.g. objects, articles, etc.) related to the current view
     * @return	array The updated PecTemplateResource
     */
    public function apply(&$template_resource) {
    	#return $template_resource;
    }
    
    
    /**
     * Checks if the handler raised a 404 error, e.g. if a blogpost does not exist
     */
    public function check_404() {
    	if ($this->is_404) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }
}

?>
