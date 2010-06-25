<?php

/**
 * pec_classes/sidebarlink.class.php - Sidebar Link Class
 * 
 * Defines the main Sidebar Link class which manages Sidebar Links.
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

class PecSidebarLink {
    private $link_id, $link_cat_id, $link_name, $link_url, $link_sort;
            
    static $by_array = array(
               'id' => 'link_id',
               'cat_id' => 'link_cat_id',
               'name' => 'link_name',
               'url' => 'link_url',
               'sort' => 'link_sort'
           );
            
    static $by_obj_array = array(
               'cat' => ''
           );
    
    function __construct($id=0, $cat_id, $name, $url, $sort=1) {
        global $pec_database;
        $this->database = $pec_database;
        
        /* escaping input data */
        $escaped_data = $this->database->db_string_protection(
            false, 
            array(
                'id' => $id, 'cat_id' => $cat_id, 'name' => $name, 'url' => $url, 'sort' => $sort
            )
        );
        
        $this->link_id = $escaped_data['id'];
        $this->link_cat_id = $escaped_data['cat_id'];
        $this->link_name = $escaped_data['name'];
        $this->link_url = $escaped_data['url'];
        $this->link_sort = $escaped_data['sort'];
        
    }
    
    public function get_id() {
        return $this->link_id;
    }
    
    public function get_cat_id() {
        return $this->link_cat_id;
    }
    
    public function get_name($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->link_name);
        }
        else {
            return $this->link_name;
        }
    }
    
    public function get_url() {
        return $this->link_url;
    }
    
    public function get_sort() {
        return $this->link_sort;
    }
    
    
    public function set_cat_id($cat_id) {
        $this->link_cat_id = $this->database->db_string_protection($cat_id);
    }
    
    public function set_name($name) {
        $this->link_name = $this->database->db_string_protection($name);
    }
    
    public function set_url($url) {
        $this->link_url = $this->database->db_string_protection($url);
    }
    
    public function set_sort($sort) {
        $this->link_sort = $this->database->db_string_protection($sort);
    }
    
    public function save() {
        $new = false;
        if (self::exists('id', $this->link_id)) {
            $query = "UPDATE " . DB_PREFIX . "sidebarlinks SET
                        link_cat_id='"   . $this->link_cat_id . "',
                        link_name='"     . $this->link_name . "',
                        link_url='"      . $this->link_url . "',
                        link_sort='"     . $this->link_sort . "'
                      WHERE link_id='" . $this->link_id . "'";
        }
        else {
            $new = true;
            $query = "INSERT INTO " . DB_PREFIX . "sidebarlinks (
                        link_cat_id,
                        link_name,
                        link_url,
                        link_sort
                      ) VALUES
                      (
                        '" . $this->link_cat_id . "',
                        '" . $this->link_name . "',
                        '" . $this->link_url . "',
                        '" . $this->link_sort . "'
                      )";
        }
        
        $this->database->db_connect();
        $this->database->db_query($query);
        if ($new) {
            $this->link_id = $this->database->db_last_insert_id();
        }
        $this->database->db_close_handle();
    }    
    
    public function remove() {
        $query = "DELETE FROM " . DB_PREFIX . "sidebarlinks WHERE link_id='" . $this->link_id . "'";
        
        $this->database->db_connect();
        $this->database->db_query($query);
        $this->database->db_close_handle();
        
        unset($this);  
    }
    
    public function belongs_to_category($cat) {
        if ($cat->get_id() == $this->link_cat_id) {
            return true;
        }
        else {
            return false;
        }
    }
        
        
    public static function load($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        /* loading a specific link, or a specific range of links */ 
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $query = "SELECT * FROM " . DB_PREFIX . "sidebarlinks WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $return_data = null;
            
            if ($pec_database->db_num_rows($resource) > 1) {
                $return_data = array();
                
                while ($link = $pec_database->db_fetch_array($resource)) {
                    $return_data[] = new PecSidebarLink($link['link_id'], $link['link_cat_id'], $link['link_name'], $link['link_url'], $link['link_sort']);
                }
            }
            elseif ($pec_database->db_num_rows($resource) == 1) {
                $link = $pec_database->db_fetch_array($resource);
                $return_data = new PecSidebarLink($link['link_id'], $link['link_cat_id'], $link['link_name'], $link['link_url'], $link['link_sort']);
            }
            
            return $return_data;            
        }
        
        /* if loading all links belonging to a given link-category object */
        elseif ($by && $data && array_key_exists($by, self::$by_obj_array)) {
            $all_links = self::load(0, false, $query_add);
            $links_on_given_data = array();
            
            foreach ($all_links as $link) {
                if ($by == 'cat') {
                    if ($link->belongs_to_category($data)) {
                        $links_on_given_data[] = $link;
                    }
                }
            }
            return $links_on_given_data;
        }
        
        /* loading all links */
        else {
            $query = "SELECT * FROM " . DB_PREFIX . "sidebarlinks " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $links = array();
            
            while ($link = $pec_database->db_fetch_array($resource)) {
                $links[] = new PecSidebarLink($link['link_id'], $link['link_cat_id'], $link['link_name'], $link['link_url'], $link['link_sort']);
            }
            
            return $links;
        }
    }
        
    public static function exists($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $query = "SELECT * FROM " . DB_PREFIX . "sidebarlinks WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            /* if there are more than 0 rows, the link exists, else not */
            $return_data = $pec_database->db_num_rows($resource) > 0 ? true : false;
            
            return $return_data;            
        }        
        else {
            return false;
        }
    }
}

?>