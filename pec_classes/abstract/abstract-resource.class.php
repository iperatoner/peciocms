<?php

/**
 * pec_classes/abstract/abstract-resource.class.php - Contains an abstract resource class
 * 
 * Contains the PecAbstractResource which is the base class for all kinds of resource objects
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
 * PecAbstractResource is the base class for all resources 
 * that hold data about anything
 */
class PecAbstractResource {
	
    /**
	 * @var array An array of all the data that is in the resource
	 */
    protected $data = array();
    
    
	/**
	 * @var array Contains properties that can not be modified.
	 */
    protected static $locked_properties = array();
	
    
    /**
     * Creates a PecAbstractResource instance.
     */
    function __construct() {
    }

    
    /**
     * Returns the data of an array key.
     * 
     * @param	string	$key The array key which's data should be returned
     * @return	mixed The proper data
     */
    final public function get($key) {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        else {
            return '';
        }
    }

    
	    /**
	     * Prints the data of an array key.
	     * 
	     * @param	string	$key The array key which's data should be printed out
	     */
	    final public function out($key) {
            echo $this->get($key);
	    }

    
    /**
     * Sets the data of an array key.
     * 
     * @param	string	$key The array key which's data should be set
     * @param	mixed	$value The data that should be set to the given array key
     */
    final public function set($key, $value) {
        if (array_key_exists($key, $this->data) && 
        	!in_array($key, self::$locked_properties)) {
            $this->data[$key] = $value;
        }
    }

    
    /**
     * Updates another resource with the data of this resource
     * 
     * @param	PecAbstractResource	$resource Another resource object
     * @return  PecAbstractResource the updated resource object
     */
    final public function inject($resource) {
        foreach ($this->data as $key => $value) {
        	$resource->set($key, $value);
        }
        return $resource;
    }

    
    /**
     * Updates this resource with the data of another resource
     * 
     * @param	string	$resource Another resource object
     * @return  PecAbstractResource This resource object with the updated data
     */
    final public function grab($resource) {
        return $resource->inject($this);
    }
    
}

?>
