<?php

/**
 * pec_classes/article.class.php - Blog Tag Class
 * 
 * Defines the main Blog Tag class which manages Blog Tags.
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

class PecBlogTag {
    private $tag_id, $tag_name, $tag_slug;
            
    static $by_array = array(
               'id' => 'tag_id',
               'name' => 'tag_name',
               'slug' => 'tag_slug',
               'post' => ''
           );
    
    function __construct($id=0, $name, $slug=false) {
        global $pec_database;
        $this->database =& $pec_database;
        
        /* escaping input data */
        $escaped_data = $this->database->db_string_protection(
            false, 
            array(
                'id' => $id, 'name' => $name
            )
        );
        
        $this->tag_id = $escaped_data['id'];
        $this->tag_name = $escaped_data['name'];
        if (!$slug) {
            $this->tag_slug = self::slugify($name);
        }
        else {
            $this->tag_slug = $slug;
        }
        
    }
    
    public function get_id() {
        return $this->tag_id;
    }
    
    public function get_name($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->tag_name);
        }
        else {
            return $this->tag_name;
        }
    }
    
    public function get_slug() {
        return $this->tag_slug;
    }
    
    
    public function set_name($name) {
        $this->tag_name = $this->database->db_string_protection($name);
        $this->tag_slug = self::slugify($name);
    }
    
    public function save_feed() {
    	global $pec_settings, $pec_localization;

    	$home = $pec_settings->get_blog_onstart();
    	$posts = PecBlogPost::load('tag', $this, "WHERE post_status='1' ORDER BY post_timestamp DESC");
    	
    	$feed = new UniversalFeedCreator();
		$feed->title = $pec_settings->get_sitename_main() . ' - ' . 
					   $pec_localization->get('LABEL_GENERAL_BLOG') . ' - ' . 
					   $pec_localization->get('LABEL_GENERAL_TAG') . ': ' . $this->tag_name;
		$feed->description = $pec_settings->get_description();
		$feed->link = convert_ampersands_to_entities(create_blogtag_url($this, false, $home));
	
    	$feed_items = PecBlogPost::generate_feed_items($posts);
    	
    	foreach ($feed_items as $item) {
    		$feed->addItem($item);
    	}
    	
    	$feed->saveFeed(FEED_TYPE, TAG_FEED_PATH . $this->tag_id . '.xml');
    }
    
    public function save() {
        if (!self::exists('name', $this->tag_name)) {
            $query = "INSERT INTO " . DB_PREFIX . "blogtags (
                        tag_name,
                        tag_slug
                      ) VALUES
                      (
                        '" . $this->tag_name . "',
                        '" . $this->tag_slug . "'
                      )";
        
            $this->database->db_connect();
            $this->database->db_query($query);
            $this->tag_id = $this->database->db_last_insert_id();
            $this->database->db_close_handle();
        }
        /* load the tag into this obj if it exists */
        else {
            $tag = self::load('name', $this->tag_name);
            $this->tag_id = $tag->get_id();
            $this->tag_slug = $tag->get_slug();
        }
    }    
    
    public function remove() {
        $query = "DELETE FROM " . DB_PREFIX . "blogtags WHERE tag_id='" . $this->tag_id . "'";
        
        $this->database->db_connect();
        $this->database->db_query($query);
        $this->database->db_close_handle();
        
        $feed_path = TAG_FEED_PATH . $this->tag_id . '.xml';
        if (file_exists($feed_path)) {
        	unlink($feed_path);
        }
        
        unset($this);  
    }
    
    
    public static function get_ids_of_tagnames($tag_names, $explode=false, $dilimiter=',') {
        $tag_ids = array();
        
        if ($explode) {
            $tag_names = str_replace($dilimiter . ' ', $dilimiter, $tag_names);
            $tag_names = str_replace(' ' . $dilimiter, $dilimiter, $tag_names);
            $tag_names = explode($dilimiter, $tag_names);
        }
        
        foreach ($tag_names as $tag_name) {
            $tag = new PecBlogTag(NULL_ID, $tag_name);
            $tag->save();
            $tag_ids[] = $tag->get_id();
        }
        
        return $tag_ids;
    }   

    public static function remove_deprecated_tags() {
    	$tags = self::load();
    	
    	$tag_num = count($tags);
    	for ($i=0; $i<$tag_num; ++$i) {
    		$t = $tags[$i];
    		if (count(PecBlogPost::load('tag', $t)) < 1) {
    			$t->remove();
    		}
    	}
    }
        
    public static function load($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        /* loading a specific tag, or a specific range of tags */ 
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "blogtags WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            
            $return_data = null;
            
            if ($pec_database->db_num_rows($resource) > 1) {
                $return_data = array();
                
                while ($tag = $pec_database->db_fetch_array($resource)) {
                    $return_data[] = new PecBlogTag($tag['tag_id'], $tag['tag_name'], $tag['tag_slug']);
                }
            }
            elseif ($pec_database->db_num_rows($resource) == 1) {
                $tag = $pec_database->db_fetch_array($resource);
                $return_data = new PecBlogTag($tag['tag_id'], $tag['tag_name'], $tag['tag_slug']);
            }
            $pec_database->db_close_handle();
            
            return $return_data;            
        }
        
        /* loading all tags */
        else {
            $query = "SELECT * FROM " . DB_PREFIX . "blogtags " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            
            $tags = array();
            
            while ($tag = $pec_database->db_fetch_array($resource)) {
                $tags[] = new PecBlogTag($tag['tag_id'], $tag['tag_name'], $tag['tag_slug']);
            }
            $pec_database->db_close_handle();
            
            return $tags;
        }
    }
        
    public static function exists($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "blogtags WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            
            /* if there are more than 0 rows, the tag exists, else not */
            $exists = $pec_database->db_num_rows($resource) > 0 ? true : false;
            $pec_database->db_close_handle();
            
            return $exists;            
        }        
        else {
            return false;
        }
    }
    
    public static function slugify($name) {
        return slugify($name);
    }
}

?>