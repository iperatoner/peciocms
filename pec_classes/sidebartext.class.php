<?php

/**
 * pec_classes/sidebartext.class.php - Sidebar Text Class
 * 
 * Defines the main Sidebar Text class which manages Sidebar Texts.
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

class PecSidebarText {
    private $text_id, $text_title, $text_content, $text_visibility, $text_onarticles, $text_sort;
            
    static $by_array = array(
               'id' => 'text_id',
               'title' => 'text_title',
               'content' => 'text_content',
               'visibility' => 'text_visibility',
               'onarticles' => 'text_onarticles',
               'sort' => 'text_sort'
           );
            
    static $by_obj_array = array(
               'article' => ''
           );
    
    function __construct($id=0, $title, $content, $visibility, $onarticles, $sort=1) {
        global $pec_database;
        $this->database =& $pec_database;
        
        /* escaping input data */
        $escaped_data = $this->database->db_string_protection(
            false, 
            array(
                'id' => $id, 'title' => $title, 'visibility' => $visibility, 'onarticles' => $onarticles, 'sort' => $sort
            )
        );
        
        $this->text_id = $escaped_data['id'];
        $this->text_title = $escaped_data['title'];
        $this->text_content = $content;
        $this->text_visibility = $escaped_data['visibility'];
        
        $this->text_onarticles = $escaped_data['onarticles'];
        $this->text_onarticles_array = flat_to_array($onarticles);
        
        $this->text_sort = $escaped_data['sort'];
        
    }
    
    public function get_id() {
        return $this->text_id;
    }
    
    public function get_title($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->text_title);
        }
        else {
            return $this->text_title;
        }
    }
    
    public function get_content() {
        return $this->database->db_string_protection_decode($this->text_content);
    }
    
    public function get_visibility() {
        return $this->text_visibility;
    }
    
    public function get_onarticles($type=TYPE_ARRAY) {
        if ($type === TYPE_ARRAY) {
            return $this->text_onarticles_array;
        }
        elseif ($type === TYPE_FLAT) {
            return $this->text_onarticles;
        }
        elseif ($type === TYPE_OBJ_ARRAY) {
            $query_addition = "WHERE ";
            $start = true;
            foreach ($this->text_onarticles_array as $article_id) {
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
        return $this->text_sort;
    }
    
    
    public function set_title($title) {
        $this->text_title = $this->database->db_string_protection($title);
    }
    
    public function set_content($content) {
        $this->text_content = htmlentities($content);
    }
    
    public function set_visibility($visibility) {
        $this->text_visibility = $this->database->db_string_protection($visibility);
    }
    
    public function set_sort($sort) {
        $this->text_sort = $this->database->db_string_protection($sort);
    }
    
    public function set_onarticles($articles, $type=TYPE_ARRAY) {
        if ($type == TYPE_ARRAY) {
            $this->text_onarticles_array = $articles;
            $this->text_onarticles = array_to_flat($articles);
        }
        elseif ($type == TYPE_FLAT) {
            $this->text_onarticles = $articles;
            $this->text_onarticles_array = flat_to_array($articles);
        }
    }
    
    public function add_article($article) {
        if (!empty($this->text_onarticles)) {
            $this->text_onarticles .= MULTIPLE_ID_DILIMITER . $article->get_id();
        }
        else {
            $this->text_onarticles = $article->get_id();            
        }
        $this->text_onarticles_array = flat_to_array($this->text_onarticles);
    }
    
    public function remove_article($article) {
        if (in_array($article->get_id(), $this->text_onarticles_array)) {
            $key = array_search($article->get_id(), $this->text_onarticles_array);
            unset($this->text_onarticles_array[$key]);
            $this->text_onarticles = array_to_flat($this->text_onarticles_array);
        }
    }
    
    public function is_on_article($article) {
        if (in_array($article->get_id(), $this->text_onarticles_array)) {
            return true;
        }
    	
    	return false;
    }
    
    public function save() {
        $new = false;
        if (self::exists('id', $this->text_id)) {
            $query = "UPDATE " . DB_PREFIX . "sidebartexts SET
                        text_title='"      . $this->text_title . "',
                        text_content='"    . $this->text_content . "',
                        text_visibility='" . $this->text_visibility . "',
                        text_onarticles='" . $this->text_onarticles . "',
                        text_sort='"       . $this->text_sort . "'
                      WHERE text_id='" . $this->text_id . "'";
        }
        else {
            $new = true;
            $query = "INSERT INTO " . DB_PREFIX . "sidebartexts (
                        text_title,
                        text_content,
                        text_visibility,
                        text_onarticles,
                        text_sort
                      ) VALUES
                      (
                        '" . $this->text_title . "',
                        '" . $this->text_content . "',
                        '" . $this->text_visibility . "',
                        '" . $this->text_onarticles . "',
                        '" . $this->text_sort . "'
                      )";
        }
        
        $this->database->db_connect();
        $this->database->db_query($query);
        if ($new) {
            $this->text_id = $this->database->db_last_insert_id();
        }
        $this->database->db_close_handle();
    }    
    
    public function remove() {
        $query = "DELETE FROM " . DB_PREFIX . "sidebartexts WHERE text_id='" . $this->text_id . "'";
        
        $this->database->db_connect();
        $this->database->db_query($query);
        $this->database->db_close_handle();
        
        unset($this);  
    }
    
        
    public static function load($by='id', $data=false, $query_add='', $force_array=false) {
        global $pec_database;
        
        /* loading a specific text, or a specific range of texts */ 
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $query = "SELECT * FROM " . DB_PREFIX . "sidebartexts WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            
            $return_data = null;
            
            if ($pec_database->db_num_rows($resource) > 1 || $force_array) {
                $return_data = array();
                
                while ($t = $pec_database->db_fetch_array($resource)) {
                    $return_data[] = new PecSidebarText($t['text_id'], $t['text_title'], $t['text_content'],
                                                        $t['text_visibility'], $t['text_onarticles'], $t['text_sort']);
                }
            }
            elseif ($pec_database->db_num_rows($resource) == 1) {
                $t = $pec_database->db_fetch_array($resource);
                $return_data = new PecSidebarText($t['text_id'], $t['text_title'], $t['text_content'], 
                                                  $t['text_visibility'], $t['text_onarticles'], $t['text_sort']);
            }
            $pec_database->db_close_handle();
            
            return $return_data;            
        }
        
        /* if loading all texts visible on a given article object */
        elseif ($by && $data && array_key_exists($by, self::$by_obj_array)) {
            $all_texts = self::load();
            $texts_on_given_data = array();
            
            foreach ($all_texts as $text) {
                if ($by == 'article') {
                    if ($text->is_on_article($data) && $text->get_visibility() == TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES) {
                        $texts_on_given_data[] = $text;
                    }
                }
            }
            
            return $texts_on_given_data;
        }
        
        /* loading all texts */
        else {
            $query = "SELECT * FROM " . DB_PREFIX . "sidebartexts " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            
            $texts = array();
            
            while ($t = $pec_database->db_fetch_array($resource)) {
                $texts[] = new PecSidebarText($t['text_id'], $t['text_title'], $t['text_content'],
                                              $t['text_visibility'], $t['text_onarticles'], $t['text_sort']);
            }
            $pec_database->db_close_handle();
            
            return $texts;
        }
    }
        
    public static function exists($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $query = "SELECT * FROM " . DB_PREFIX . "sidebartexts WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            
            /* if there are more than 0 rows, the text exists, else not */
            $return_data = $pec_database->db_num_rows($resource) > 0 ? true : false;
            $pec_database->db_close_handle();
            
            return $return_data;            
        }        
        else {
            return false;
        }
    }
}

?>