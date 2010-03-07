<?php

/**
 * pec_classes/menupoint.class.php - Menupoint Class
 * 
 * Defines the main Menupoint class which manages Menupoints.
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

class PecMenuPoint {
    private $point_id, $point_root_id, $point_superroot_id, $point_name, 
            $point_slug, $point_target_type, $point_target_data, $point_sort;
            
    static $by_array = array(
               'id' => 'point_id',
               'superroot_id' => 'point_superroot_id',
               'root_id' => 'point_root_id',
               'name' => 'point_name',
               'slug' => 'point_slug',
               'target_type' => 'point_target_type',
               'target_data' => 'point_targeto_data',
               'sort' => 'point_sort'
           );
            
    static $by_obj_array = array(
               'root_menupoint' => '',
               'superroot_menupoint' => ''
           );
    
    function __construct($id=0, $superroot_id, $root_id, $name, $target_type, $target_data, $sort=1, $slug=false) {
        global $pec_database;
        $this->database = $pec_database;
        
        /* escaping input data */
        $escaped_data = $this->database->db_string_protection(
            false, 
            array(
                'id' => $id, 'superroot_id' => $superroot_id, 'root_id' => $root_id, 'name' => $name, 'target_type' => $target_type,
                'target_data' => $target_data, 'sort' => $sort
            )
        );
        
        $this->point_id = $escaped_data['id'];
        $this->point_superroot_id = $escaped_data['superroot_id'];
        $this->point_root_id = $escaped_data['root_id'];
        $this->point_name = $escaped_data['name'];
        
        /* if this point hasn't got a slug yet */
        if (!$slug) {
            $this->point_slug = self::slugify($name);
        }
        else {
            $this->point_slug = $slug;
        }
        
        $this->point_target_type = $escaped_data['target_type'];
        if ($this->point_target_type == MENUPOINT_TARGET_URL) {
        	$this->point_target_data = $this->database->db_escape_string($target_data);
        }
        else {
        	$this->point_target_data = $escaped_data['target_data'];
        }
        $this->point_sort = $escaped_data['sort'];
        
    }
    
    public function get_id() {
        return $this->point_id;
    }
    
    public function get_superroot_id() {
        return $this->point_superroot_id;
    }
    
        public function get_superroot_menupoint() {
            if ($this->point_superroot_id != 0) {
                return self::load('id', $this->point_superroot_id);
            }
            else {
                return false;
            }
        }
    
    public function get_root_id() {
        return $this->point_root_id;
    }
    
        public function get_root_menupoint() {
            if ($this->point_root_id != 0) {
                return self::load('id', $this->point_root_id);
            }
            else {
                return false;
            }
        }
    
    public function get_name($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->point_name);
        }
        else {
            return $this->point_name;
        }
    }
    
    public function get_slug() {
        return $this->point_slug;
    }
    
    public function get_target_type() {
        return $this->point_target_type;
    }
    
    public function get_target_data() {
        return $this->point_target_data;
    }
    
    public function get_sort() {
        return $this->point_sort;
    }

    
    public function set_superroot_id($superroot_id) {
        $this->point_superroot_id = $this->database->db_string_protection($superroot_id);
    }
        
        public function set_superroot_menupoint($superroot_menupoint) {
            $this->point_superroot_id = $this->database->db_string_protection($superroot_menupoint->get_id());
        }

    
    public function set_root_id($root_id) {
        $this->point_root_id = $this->database->db_string_protection($root_id);
    }
        
        public function set_root_menupoint($root_menupoint) {
            $this->point_root_id = $this->database->db_string_protection($root_menupoint->get_id());
        }
    
    public function set_name($name) {
        if ($name != $this->point_name) {
            $this->point_name = $this->database->db_string_protection($name);
            $this->point_slug = $this->database->db_string_protection(self::slugify($name));
        }
    }
    
    public function set_target_type($target_type=MENUPOINT_TARGET_HOME) {
        $this->point_target_type = $this->database->db_string_protection($target_type);
        $this->point_target_data = '';
    }
    
    public function set_target_data($target_data='') {
        if ($this->point_target_type == MENUPOINT_TARGET_URL) {
        	$this->point_target_data = $this->database->db_escape_string($target_data);
        }
        else {
        	$this->point_target_data = $this->database->db_string_protection($target_data);
        }
    }
    
        public function set_target($target_type=MENUPOINT_TARGET_HOME, $target_data='') {
            $this->point_target_type = $this->database->db_string_protection($target_type);
            
	        if ($this->point_target_type == MENUPOINT_TARGET_URL) {
	        	$this->point_target_data = $this->database->db_escape_string($target_data);
	        }
	        else {
            	$this->point_target_data = $this->database->db_string_protection($target_data);
	        }
        }
    
    public function set_sort($sort) {
        $this->point_sort = $sort;
    }
    
    public function save() {
        $new = false;
        if (self::exists('id', $this->point_id)) {
            $query = "UPDATE " . DB_PREFIX . "menupoints SET
                        point_superroot_id='". $this->point_superroot_id . "',
                        point_root_id='"     . $this->point_root_id . "',
                        point_name='"        . $this->point_name . "',
                        point_slug='"        . $this->point_slug . "',
                        point_target_type='" . $this->point_target_type . "',
                        point_target_data='" . $this->point_target_data . "',
                        point_sort='"        . $this->point_sort . "'
                      WHERE point_id='"    . $this->point_id . "'";
        }
        else {
            $new = true;
            $query = "INSERT INTO " . DB_PREFIX . "menupoints (
                        point_superroot_id,
                        point_root_id,
                        point_name,
                        point_slug,
                        point_target_type,
                        point_target_data,
                        point_sort
                      ) VALUES
                      (
                        '" . $this->point_superroot_id . "',
                        '" . $this->point_root_id . "',
                        '" . $this->point_name . "',
                        '" . $this->point_slug . "',
                        '" . $this->point_target_type . "',
                        '" . $this->point_target_data . "',
                        '" . $this->point_sort . "'
                      )";
        }
        
        $this->database->db_connect();
        $this->database->db_query($query);
        if ($new) {
            $this->point_id = $this->database->db_last_insert_id();
        }
        $this->database->db_close_handle();
    }
    
    public function remove() {
        $query = "DELETE FROM " . DB_PREFIX . "menupoints WHERE point_id='" . $this->point_id . "'";
        
        $this->database->db_connect();
        $this->database->db_query($query);
        $this->database->db_close_handle();
        
        unset($this);        
    }
    
        
    public static function load($by='id', $data=false, $query_add='', $force_array=false) {
        global $pec_database;
        
        /* loading a specific menupoint, or a specific range of menupoints */ 
        if ($by && $data !== false && array_key_exists($by, self::$by_array)) {
            $query = "SELECT * FROM " . DB_PREFIX . "menupoints WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $return_data = null;
            
            if ($pec_database->db_num_rows($resource) > 1 || $force_array) {
                $return_data = array();
                
                while ($m = $pec_database->db_fetch_array($resource)) {
                    $return_data[] = new PecMenuPoint($m['point_id'], $m['point_superroot_id'], $m['point_root_id'], $m['point_name'],
                                                      $m['point_target_type'], $m['point_target_data'], 
                                                      $m['point_sort'], $m['point_slug']);
                }
            }
            elseif ($pec_database->db_num_rows($resource) == 1) {
                $m = $pec_database->db_fetch_array($resource);
                $return_data = new PecMenuPoint($m['point_id'], $m['point_superroot_id'], $m['point_root_id'], $m['point_name'],
                                                $m['point_target_type'], $m['point_target_data'], 
                                                $m['point_sort'], $m['point_slug']);
            }
            
            return $return_data;            
        }
        
        /* if loading all menupoints belonging to a given root menupoint or superroot menupoint */
        elseif ($by && $data && array_key_exists($by, self::$by_obj_array)) {
            $all_menupoints = self::load();
            $menupoints_on_given_data = array();
            
            foreach ($all_menupoints as $menupoint) {
                if ($by == 'root_menupoint' || $by == 'superroot_menupoint') {
                    if ($menupoint->get_root_id() == $data->get_id() || $menupoint->get_superroot_id() == $data->get_id()) {
                        $menupoints_on_given_data[] = $menupoint;
                    }
                }
            }
            
            return $menupoints_on_given_data;
        }
        
        /* loading all menupoints */
        else {
            $query = "SELECT * FROM " . DB_PREFIX . "menupoints " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $menupoints = array();
            
            while ($m = $pec_database->db_fetch_array($resource)) {
                $menupoints[] = new PecMenuPoint($m['point_id'], $m['point_superroot_id'], $m['point_root_id'], $m['point_name'],
                                               $m['point_target_type'], $m['point_target_data'], 
                                               $m['point_sort'], $m['point_slug']);
            }
            
            return $menupoints;
        }
    }
        
    public static function exists($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        if ($by && $data !== false && array_key_exists($by, self::$by_array)) {
            $query = "SELECT * FROM " . DB_PREFIX . "menupoints WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
                
            /* if there are more than 0 rows, the menupoint exists, else not */
            $return_data = $pec_database->db_num_rows($resource) > 0 ? true : false;
            
            return $return_data;            
        }        
        else {
            return false;
        }
    }
    
    public static function slugify($name) {        
        $slug = slugify($name);
        
        $counter = 1;
        while (self::exists('slug', $slug)) {
            $slug = slugify($name) . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}

?>