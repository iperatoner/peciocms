<?php

/**
 * pec_classes/sidebarlink-category.class.php - Sidebar Link Category Class
 * 
 * Defines the main Sidebar Link Category class which manages Sidebar Link Categories.
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
 * @version		2.0.1
 * @link		http://pecio-cms.com
 */

class PecSidebarLinkCat {
    private $cat_id, $cat_title, $cat_visibility, $cat_onarticles, $cat_sort;
            
    static $by_array = array(
               'id' => 'cat_id',
               'title' => 'cat_title',
               'visibility' => 'cat_visibility',
               'onarticles' => 'cat_onarticles',
               'sort' => 'cat_sort'
           );
            
    static $by_obj_array = array(
               'article' => ''
           );
    
    function __construct($id=0, $title, $visibility, $onarticles, $sort=1) {
        global $pec_database;
        $this->database = $pec_database;
        
        /* escaping input data */
        $escaped_data = $this->database->db_string_protection(
            false, 
            array(
                'id' => $id, 'title' => $title, 'visibility' => $visibility, 'onarticles' => $onarticles, 'sort' => $sort
            )
        );
        
        $this->cat_id = $escaped_data['id'];
        $this->cat_title = $escaped_data['title'];
        $this->cat_visibility = $escaped_data['visibility'];
        
        $this->cat_onarticles = $escaped_data['onarticles'];
        $this->cat_onarticles_array = flat_to_array($onarticles);
        
        $this->cat_sort = $escaped_data['sort'];
        
    }
    
    public function get_id() {
        return $this->cat_id;
    }
    
    public function get_title($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->cat_title);
        }
        else {
            return $this->cat_title;
        }
    }
    
    public function get_visibility() {
        return $this->cat_visibility;
    }
    
    public function get_onarticles($type=TYPE_ARRAY) {
        if ($type === TYPE_ARRAY) {
            return $this->cat_onarticles_array;
        }
        elseif ($type === TYPE_FLAT) {
            return $this->cat_onarticles;
        }
        elseif ($type === TYPE_OBJ_ARRAY) {
            $query_addition = "WHERE ";
            $start = true;
            foreach ($this->cat_onarticles_array as $article_id) {
                if ($start) {
                    $query_addition .= "article_id='" . $article_id . "'";
                    $start = false;
                }
                else {
                    $query_addition .= " OR article_id='" . $article_id . "'";
                }
            }
            
            if (!$start) {
            	return PecArticle::load(false, false, $query_addition);
            }
            else {
            	return array();
            }
        }
    }
    
    public function get_sort() {
        return $this->cat_sort;
    }
    
    
    public function set_title($title) {
        $this->cat_title = $this->database->db_string_protection($title);
    }
    
    public function set_visibility($visibility) {
        $this->cat_visibility = $this->database->db_string_protection($visibility);
    }
    
    public function set_onarticles($articles, $type=TYPE_ARRAY) {
        if ($type == TYPE_ARRAY) {
            $this->cat_onarticles_array = $articles;
            $this->cat_onarticles = array_to_flat($articles);
        }
        elseif ($type == TYPE_FLAT) {
            $this->cat_onarticles = $articles;
            $this->cat_onarticles_array = flat_to_array($articles);
        }
    }
    
    public function set_sort($sort) {
        $this->cat_sort = $this->database->db_string_protection($sort);
    }
    
    public function add_article($article) {
        if (!empty($this->cat_onarticles)) {
            $this->cat_onarticles .= MULTIPLE_ID_DILIMITER . $article->get_id();
        }
        else {
            $this->cat_onarticles = $article->get_id();            
        }
        $this->cat_onarticles_array = self::onarticle_to_array($this->cat_onarticles);
    }
    
    public function remove_article($article) {
        if (in_array($article->get_id(), $this->cat_onarticles_array)) {
            $key = array_search($article->get_id, $this->cat_onarticles_array);
            unset($this->cat_onarticles_array[$key]);
            $this->cat_onarticles = array_to_flat($this->cat_onarticles_array);
        }
    }
    
    public function is_on_article($article) {
        if (in_array($article->get_id(), $this->cat_onarticles_array)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function save() {
        $new = false;
        if (self::exists('id', $this->cat_id)) {
            $query = "UPDATE " . DB_PREFIX . "sidebarlinkcategories SET
                        cat_title='"      . $this->cat_title . "',
                        cat_visibility='" . $this->cat_visibility . "',
                        cat_onarticles='" . $this->cat_onarticles . "',
                        cat_sort='"       . $this->cat_sort . "'
                      WHERE cat_id='" . $this->cat_id . "'";
        }
        else {
            $new = true;
            $query = "INSERT INTO " . DB_PREFIX . "sidebarlinkcategories (
                        cat_title,
                        cat_visibility,
                        cat_onarticles,
                        cat_sort
                      ) VALUES
                      (
                        '" . $this->cat_title . "',
                        '" . $this->cat_visibility . "',
                        '" . $this->cat_onarticles . "',
                        '" . $this->cat_sort . "'
                      )";
        }
        
        $this->database->db_connect();
        $this->database->db_query($query);
        if ($new) {
            $this->cat_id = $this->database->db_last_insert_id();
        }
        $this->database->db_close_handle();
    }    
    
    public function remove() {
        $query = "DELETE FROM " . DB_PREFIX . "sidebarlinkcategories WHERE cat_id='" . $this->cat_id . "'";
        
        $this->database->db_connect();
        $this->database->db_query($query);
        $this->database->db_close_handle();
        
        unset($this);  
    }
    
        
    public static function load($by='id', $data=false, $query_add='', $force_array=false) {
        global $pec_database;
        
        /* loading a specific category, or a specific range of categories */ 
        if ($by && $data && array_key_exists($by, self::$by_array) && $by != 'article') {
            $query = "SELECT * FROM " . DB_PREFIX . "sidebarlinkcategories WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $return_data = null;
            
            if ($pec_database->db_num_rows($resource) > 1 || $force_array) {
                $return_data = array();
                
                while ($c = $pec_database->db_fetch_array($resource)) {
                    $return_data[] = new PecSidebarLinkCat($c['cat_id'], $c['cat_title'], $c['cat_visibility'], $c['cat_onarticles'], $c['cat_sort']);
                }
            }
            elseif ($pec_database->db_num_rows($resource) == 1) {
                $c = $pec_database->db_fetch_array($resource);
                $return_data = new PecSidebarLinkCat($c['cat_id'], $c['cat_title'], $c['cat_visibility'], $c['cat_onarticles'], $c['cat_sort']);
            }
            
            return $return_data;            
        }
        
        /* if loading all categories visible on a given article object */
        elseif ($by && $data && array_key_exists($by, self::$by_obj_array)) {
            $all_cats = self::load();
            $cats_on_given_data = array();
            
            foreach ($all_cats as $cat) {
                if ($by == 'article') {
                    if ($cat->is_on_article($data) && $cat->get_visibility() == TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES) {
                        $cats_on_given_data[] = $cat;
                    }
                }
            }
            
            return $cats_on_given_data;
        }
        
        /* loading all categories */
        else {
            $query = "SELECT * FROM " . DB_PREFIX . "sidebarlinkcategories " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $cats = array();
            
            while ($c = $pec_database->db_fetch_array($resource)) {
                $cats[] = new PecSidebarLinkCat($c['cat_id'], $c['cat_title'], $c['cat_visibility'], $c['cat_onarticles'], $c['cat_sort']);
            }
            
            return $cats;
        }
    }
        
    public static function exists($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $query = "SELECT * FROM " . DB_PREFIX . "sidebarlinkcategories WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            /* if there are more than 0 rows, the category exists, else not */
            $return_data = $pec_database->db_num_rows($resource) > 0 ? true : false;
            
            return $return_data;            
        }        
        else {
            return false;
        }
    }
}

?>