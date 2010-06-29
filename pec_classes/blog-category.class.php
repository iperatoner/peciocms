<?php

/**
 * pec_classes/blog-category.class.php - Blog Category Class
 * 
 * Defines the main Blog Category class which manages Blog Categories.
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
 * The PecBlogCategory is used for creating and loading blog categories. An instance of it is one category (one row in the database) that can be saved or created using this class.
 */
class PecBlogCategory {
	
    private $cat_id, $cat_name, $cat_slug;
    
	/**
	 * @static
	 * @var array All database columns that a blog category has. You have to use the key of one of them to load a category by a specific property.
	 */
    static $by_array = array(
               'id' => 'cat_id',
               'name' => 'cat_name',
               'slug' => 'cat_slug',
               'post' => ''
           );
    
    /**
     * Creates a PecBlogCategory instance.
     * 
     * @param	integer		$id ID of the blog category
     * @param	string		$name Name of the blog category
     * @param	string		$slug Slug of the blog category
     */
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
        
        $this->cat_id = $escaped_data['id'];
        $this->cat_name = $escaped_data['name'];
        if (!$slug) {
            $this->cat_slug = self::slugify($name);
        }
        else {
            $this->cat_slug = $slug;
        }
        
    }
    
    
    /**
     * Returns ID of this blog category
     * 
     * @return	integer	ID of the blog category
     */
    public function get_id() {
        return $this->cat_id;
    }
    
    
    /**
     * Returns the name of this blog category
     * 
     * @param	boolean	$strip_protextion Wether to remove the database string protection (e.g. mysql_escape_string) or not, default: true
     * @return	string	Name of the blog category
     */
    public function get_name($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->cat_name);
        }
        else {
            return $this->cat_name;
        }
    }
    
    
    /**
     * Returns slug of this blog category
     * 
     * @return	string Slug of the blog category
     */
    public function get_slug() {
        return $this->cat_slug;
    }
    
    
    /**
     * Sets the name for this blog category
     * 
     * @param	string	$name The new name for this blog category
     */
    public function set_name($name) {
        if ($name != $this->cat_name) {
            $this->cat_name = $this->database->db_string_protection($name);
            $this->cat_slug = self::slugify($name);
        }
    }
    
    
    /**
     * Saves the RSS feed of all blog posts that are in this category
     */
    public function save_feed() {
    	global $pec_settings, $pec_localization;

    	$home = $pec_settings->get_blog_onstart();
    	$posts = PecBlogPost::load('category', $this, "WHERE post_status='1' ORDER BY post_timestamp DESC");
    	
    	$feed = new UniversalFeedCreator();
		$feed->title = $pec_settings->get_sitename_main() . ' - ' . 
					   $pec_localization->get('LABEL_GENERAL_BLOG') . ' - ' . 
					   $pec_localization->get('LABEL_GENERAL_CATEGORY') . ': ' . $this->cat_name;
		$feed->description = $pec_settings->get_description();
		$feed->link = convert_ampersands_to_entities(create_blogcategory_url($this, false, $home));
	
    	$feed_items = PecBlogPost::generate_feed_items($posts);
    	
    	foreach ($feed_items as $item) {
    		$feed->addItem($item);
    	}
    	
    	$feed->saveFeed(FEED_TYPE, CATEGORY_FEED_PATH . $this->cat_id . '.xml');
    }
    
    
    /**
     * Saves or creates this blog category
     */
    public function save() {
        $new = false;
        if (!self::exists('id', $this->cat_id)) {
            $new = true;
            $query = "INSERT INTO " . DB_PREFIX . "blogcategories (
                        cat_name,
                        cat_slug
                      ) VALUES
                      (
                        '" . $this->cat_name . "',
                        '" . $this->cat_slug . "'
                      )";
        }
        else {
            $query = "UPDATE " . DB_PREFIX . "blogcategories SET 
                        cat_name='" . $this->cat_name . "',
                        cat_slug='" . $this->cat_slug . "'
                      WHERE cat_id='" . $this->cat_id . "'";            
        }
            
        $this->database->db_connect();
        $this->database->db_query($query);
        if ($new) {
            $this->cat_id = $this->database->db_last_insert_id();
        }
        $this->database->db_close_handle();
    }    
    
    /**
     * Removes this blog category
     */
    public function remove() {
        $belonging_posts = PecBlogPost::load('category', $this);
        foreach ($belonging_posts as $p) {
            $p->remove_category($this);
            $p->save();
        }
        
        $query = "DELETE FROM " . DB_PREFIX . "blogcategories WHERE cat_id='" . $this->cat_id . "'";
        
        $this->database->db_connect();
        $this->database->db_query($query);
        $this->database->db_close_handle();
        
        $feed_path = CATEGORY_FEED_PATH . $this->cat_id . '.xml';
        if (file_exists($feed_path)) {
        	unlink($feed_path);
        }
        
        unset($this);  
    }
    
    
    /**
     * Returns the ID's of the given names of blog categories
     * 
     * @param	array	$cat_names The category names to "convert" into ID's
     * @return array An array of category ID's
     */
    public static function get_ids_of_catnames($cat_names) {
        $cat_ids = array();
        
        foreach ($cat_names as $cat_name) {
            $cat = new PecBlogCategory(NULL_ID, $cat_name);
            $cat->save();
            $cat_ids[] = $cat->get_id();
        }
        
        return $cat_ids;
    }          
        
    /**
     * Load a specific blog category or a specific range/set of categories or all categories
     * 
     * @param	string	$by The database column by that you want to load the category/set of categories. Must be a string of self::$by_array
     * @param	mixed	$data The data that may match the categories you want to load (e.g. if $by is 'id', $data is the ID of the category you want to load)
     * @param	string	$query_add Additional data for the SQL query
     * @return	array/PecBlogCategory An array of matching categories or one PecBlogCategory instance that matched the query
     */
    public static function load($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        /* loading a specific cat, or a specific range of cats */ 
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "blogcategories  WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $return_data = null;
            
            if ($pec_database->db_num_rows($resource) > 1) {
                $return_data = array();
                
                while ($cat = $pec_database->db_fetch_array($resource)) {
                    $return_data[] = new PecBlogCategory($cat['cat_id'], $cat['cat_name'], $cat['cat_slug']);
                }
            }
            elseif ($pec_database->db_num_rows($resource) == 1) {
                $cat = $pec_database->db_fetch_array($resource);
                $return_data = new PecBlogCategory($cat['cat_id'], $cat['cat_name'], $cat['cat_slug']);
            }
            
            return $return_data;            
        }
        
        /* loading all cats */
        else {
            $query = "SELECT * FROM " . DB_PREFIX . "blogcategories " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $cats = array();
            
            while ($cat = $pec_database->db_fetch_array($resource)) {
                $cats[] = new PecBlogCategory($cat['cat_id'], $cat['cat_name'], $cat['cat_slug']);
            }
            
            return $cats;
        }
    }
    
    
    /**
     * Check wether a specific blog category exists
     * 
     * @param	string	$by The database column by that you want to check. Must be a string of self::$by_array
     * @param	mixed	$data The data that may match the categories you want to check (e.g. if $by is 'id', $data is the ID of the category you want to check)
     * @param	string	$query_add Additional data for the SQL query
     * @return	boolean Wether the category exists or not, this may be true or false
     */
    public static function exists($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "blogcategories WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            /* if there are more than 0 rows, the cat exists, else not */
            $exists = $pec_database->db_num_rows($resource) > 0 ? true : false;
            
            return $exists;            
        }        
        else {
            return false;
        }
    }
    
    
    /**
     * Slugifies the name of a category
     * 
     * @param	string	$name Name that shall be slugified
     * @return	string The slugified name
     */
    public static function slugify($name) {
    	// We don't need to check if this slug already exists because two categories with the same name are useless!
        return slugify($name);
    }
}

?>