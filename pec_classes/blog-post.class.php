<?php

/**
 * pec_classes/blog-post.class.php - Blog Post Class
 * 
 * Defines the main Blog Post class which manages Blog Posts.
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

class PecBlogPost {
    private $post_id, $post_timestamp, $post_year, $post_month, $post_day, $post_author_id, 
            $post_title, $post_slug, $post_content_cut, $post_content, $post_tags, $post_categories, 
            $post_comments_allowed, $post_status, $readonly;
            
    static $by_array = array(
               'id' => 'post_id',
               'timestamp' => 'post_timestamp',
               'year' => 'post_year',
               'month' => 'post_month',
               'day' => 'post_day',
               'author_id' => 'post_author_id',
               'title' => 'post_title',
               'slug' => 'post_slug',
               'content_cut' => 'post_content_cut',
               'content' => 'post_content',
               'tags' => 'post_tags',
               'categories' => 'post_categories',
               'comments_allowed' => 'post_comments_allowed',
               'status' => 'post_status'
           );
            
    static $by_obj_array = array(
               'tag' => '',
               'category' => '',
               'author' => ''
           );
    
    function __construct($id=0, $timestamp, $year, $month, $day, $author_id, $title, $content_cut, 
                         $content, $tags, $categories, $comments_allowed, $status, $slug=false) {
        global $pec_database;
        $this->database = $pec_database;
        
        /* escaping input data */
        $escaped_data = $this->database->db_string_protection(
            false, 
            array(
                'id' => $id, 'timestamp' => $timestamp, 'year' => $year, 'month' => $month, 'day' => $day, 
                'author_id' => $author_id, 'title' => $title, 'tags' => $tags, 'categories' => $categories, 
                'comments_allowed' => $comments_allowed, 'status' => $status
            )
        );
        
        $this->post_id = $escaped_data['id'];
        $this->post_timestamp = $escaped_data['timestamp'];
        $this->post_year = $escaped_data['year'];
        $this->post_month = $escaped_data['month'];
        $this->post_day = $escaped_data['day'];
        $this->post_author_id = $escaped_data['author_id'];
        $this->post_title = $escaped_data['title'];
        
        // content doesn't need to be protected, because that is done by the CKEditor :)
        $this->post_content_cut = $content_cut;
        $this->post_content = $content;
        
        $this->post_comments_allowed = $escaped_data['comments_allowed'];
        $this->post_status = $escaped_data['status'];
        
        $this->post_tags = $escaped_data['tags'];
        $this->post_tags_array = flat_to_array($tags);
        
        $this->post_categories = $escaped_data['categories'];
        $this->post_categories_array = flat_to_array($categories);
        
        /* if this post hasn't got a slug yet */
        if (!$slug) {
            $this->post_slug = self::slugify($title);
        }
        else {
            $this->post_slug = $slug;
        }
        
        $this->readonly = false;
    }
    
    public function get_id() {
        return $this->post_id;
    }
    
    public function get_timestamp($format=false) {
        if ($format) {
            return date($format, $this->post_timestamp);
        }
        else {
            return $this->post_timestamp;
        }
    }
    
    public function get_year() {
        return $this->post_year;
    }
    
    public function get_month() {
        return $this->post_month;
    }
    
    public function get_day() {
        return $this->post_day;
    }
    
    public function get_author_id() {
        return $this->post_author_id;
    }
    
        public function get_author() {
            return PecUser::load('id', $this->post_author_id);
        }
    
        
    public function get_title($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->post_title);
        }
        else {
            return $this->post_title;
        }
    }
    
    public function get_slug() {
        return $this->post_slug;
    }
    
    public function get_content_cut() {
        return $this->database->db_string_protection_decode($this->post_content_cut);
    }
    
    public function get_content() {
        return $this->database->db_string_protection_decode($this->post_content);
    }
    
    public function get_tags($type=TYPE_ARRAY) {
        if ($type === TYPE_ARRAY) {
            return $this->post_tags_array;
        }
        elseif ($type === TYPE_FLAT) {
            return $this->post_tags;
        }
        elseif ($type === TYPE_OBJ_ARRAY) {
            $query_addition = "WHERE ";
            $start = true;
            foreach ($this->post_tags_array as $tag_id) {
                if ($start) {
                    $query_addition .= "tag_id='" . $tag_id . "'";
                    $start = false;
                }
                else {
                    $query_addition .= " OR tag_id='" . $tag_id . "'";
                }
            }
            
            if (!$start) {
            	return PecBlogTag::load(false, false, $query_addition);
            }
            else {
            	return array();
            }
        }
    }
    
    public function get_categories($type=TYPE_ARRAY) {
        if ($type === TYPE_ARRAY) {
            return $this->post_categories_array;
        }
        elseif ($type === TYPE_FLAT) {
            return $this->post_categories;
        }
        elseif ($type === TYPE_OBJ_ARRAY) {
            $query_addition = "WHERE ";
            $start = true;
            foreach ($this->post_categories_array as $cat_id) {
                if ($start) {
                    $query_addition .= "cat_id='" . $cat_id . "'";
                    $start = false;
                }
                else {
                    $query_addition .= " OR cat_id='" . $cat_id . "'";
                }
            }
            
            if (!$start) {
            	return PecBlogCategory::load(false, false, $query_addition);
            }
            else {
            	return array();
            }
        }
    }
    
    public function get_comments_allowed($human_readable=false) {
        if ($human_readable) {
            return $this->post_comments_allowed == true ? '&#x2713;' : '&#x2717;';
        }
        else {
            return $this->post_comments_allowed;            
        }
    }
    
    public function get_status($human_readable=false) {
        if ($human_readable) {
            return $this->post_status == true ? '&#x2713;' : '&#x2717;';
        }
        else {
            return $this->post_status;            
        }
    }
    
    
    public function set_timestamp($timestamp, $adjust_dmy=false) {
        $this->post_timestamp = $this->database->db_string_protection($timestamp);
        if ($adjust_dmy) {
            $this->set_year(date('Y', $timestamp));
            $this->set_month(date('n', $timestamp));
            $this->set_day(date('j', $timestamp));
        }
    }
    
    public function set_year($year) {
        $this->post_year = $this->database->db_string_protection($year);
    }
    
    public function set_month($month) {
        $this->post_month = $this->database->db_string_protection($month);
    }
    
    public function set_day($day) {
        $this->post_day = $this->database->db_string_protection($day);
    }    
    
    public function set_author_id($author_id) {
        $this->post_author_id = $this->database->db_string_protection($author_id);
    }
    
        public function set_author($author) {
            $this->post_author_id = $this->database->db_string_protection($author->get_id());
        }
    
    public function set_title($title) {
        if ($title != $this->post_title) {
            $this->post_title = $this->database->db_string_protection($title);
            if (slugify($title) != $this->post_slug) {
            	$this->post_slug = self::slugify($title);
            }
        }
    }
    
    public function set_content_cut($content_cut) {
        $this->post_content_cut = htmlentities($content_cut);
    }
    
    public function set_content($content) {
        $this->post_content = htmlentities($content);
    }
    
    public function set_categories($categories, $type=TYPE_ARRAY) {
        if ($type == TYPE_FLAT) {
            $this->post_categories = $categories;
            $this->post_categories_array = flat_to_array($categories);
        }
        elseif ($type == TYPE_ARRAY) {
            if (!empty($categories)) {
                $this->post_categories_array = $categories;
                $this->post_categories = array_to_flat($categories);
            }
            else {
                $this->post_categories_array = array();
                $this->post_categories = array_to_flat($this->post_categories_array);
            }
        }
        elseif ($type == TYPE_OBJ_ARRAY) {
            $this->post_categories_array = array();
            foreach ($categories as $c) {
                $this->post_categories_array[] = $c->get_id();
            }
            $this->post_categories = array_to_flat($thi->post_categories_array);
        }        
    }
    
    public function set_tags($tags, $type=TYPE_ARRAY) {
        if ($type == TYPE_FLAT) {
            $this->post_tags = $tags;
            $this->post_tags_array = flat_to_array($tags);
        }
        elseif ($type == TYPE_ARRAY) {
            $this->post_tags_array = $tags;
            $this->post_tags = array_to_flat($tags);
        }
        elseif ($type == TYPE_OBJ_ARRAY) {
            $this->post_tags_array = array();
            foreach ($tags as $t) {
                $this->post_tags_array[] = $t->get_id();
            }
            $this->post_tags = array_to_flat($thi->post_tags_array);
        }        
    }
    
    public function add_tag($tag) {
        if (!empty($this->post_tags)) {
            $this->post_tags .= MULTIPLE_ID_DILIMITER . $tag->get_id();
        }
        else {
            $this->post_tags = $tag->get_id();            
        }
        $this->post_tags_array = flat_to_array($this->post_tags);
    }
    
    public function remove_tag($tag) {
        if (in_array($tag->get_id(), $this->post_tags_array)) {
            $key = array_search($tag->get_id(), $this->post_tags_array);
            unset($this->post_tags_array[$key]);
            $this->post_tags = array_to_flat($this->post_tags_array);
        }
    }
    
    public function add_category($cat) {
        if (!empty($this->post_categories)) {
            $this->post_categories .= MULTIPLE_ID_DILIMITER . $cat->get_id();
        }
        else {
            $this->post_categories = $cat->get_id();            
        }
        $this->post_categories_array = flat_to_array($this->post_categories);
    }
    
    public function remove_category($cat) {
        if (in_array($cat->get_id(), $this->post_categories_array)) {
            $key = array_search($cat->get_id(), $this->post_categories_array);
            unset($this->post_categories_array[$key]);
            $this->post_categories = array_to_flat($this->post_categories_array);
        }
    }
    
    public function set_comments_allowed($comments_allowed=true) {
        $this->post_comments_allowed = $this->database->db_string_protection($comments_allowed, false);
    }
    
    public function set_status($status=true) {
        $this->post_status = $this->database->db_string_protection($status, false);
    }
    
    public function from_author($author) {
        if ($author->get_id() == $this->post_author_id) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function has_tag($tag) {
        if (in_array($tag->get_id(), $this->post_tags_array)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function in_category($cat) {
        if (in_array($cat->get_id(), $this->post_categories_array)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function make_readonly() {
        if ($this->readonly == false) {
            $this->readonly = true;
        }
    }
    
    public function save($update_feeds=true, $insert_id=false) {
        $new = false;
        if (self::exists('id', $this->post_id)) {
            $query = "UPDATE " . DB_PREFIX . "blogposts SET
                        post_timestamp='"   	 . $this->post_timestamp . "',
                        post_year='"         	 . $this->post_year . "',
                        post_month='"        	 . $this->post_month . "',
                        post_day='"          	 . $this->post_day . "',
                        post_author_id='"    	 . $this->post_author_id . "',
                        post_title='"        	 . $this->post_title . "',
                        post_slug='"         	 . $this->post_slug . "',
                        post_content_cut='"  	 . $this->post_content_cut . "',
                        post_content='"      	 . $this->post_content . "',
                        post_tags='"         	 . $this->post_tags . "',
                        post_categories='"   	 . $this->post_categories . "',
                        post_comments_allowed='" . $this->post_comments_allowed . "',
                        post_status='"       	 . $this->post_status . "'
                      WHERE post_id='"    . $this->post_id . "'";
        }
        else {
            $new = true;
            if ($insert_id) {
                $id_field = 'post_id,';
                $id_data = "'" . $this->post_id . "',";
            }
            else {
                $id_field = '';
                $id_data = '';
            }
            
            $query = "INSERT INTO " . DB_PREFIX . "blogposts (
                        " . $id_field . "
                        post_timestamp,
                        post_year,
                        post_month,
                        post_day,
                        post_author_id,
                        post_title,
                        post_slug,
                        post_content_cut,
                        post_content,
                        post_tags,
                        post_categories,
                        post_comments_allowed,
                        post_status
                      ) VALUES
                      (
                        " . $id_data . "
                        '" . $this->post_timestamp . "',
                        '" . $this->post_year . "',
                        '" . $this->post_month . "',
                        '" . $this->post_day . "',
                        '" . $this->post_author_id . "',
                        '" . $this->post_title . "',
                        '" . $this->post_slug . "',
                        '" . $this->post_content_cut . "',
                        '" . $this->post_content . "',
                        '" . $this->post_tags . "',
                        '" . $this->post_categories . "',
                        '" . $this->post_comments_allowed . "',
                        '" . $this->post_status . "'
                      )";
        }
        
        $this->database->db_connect();
        $this->database->db_query($query);
        if ($new) {
            $this->post_id = $this->database->db_last_insert_id();            
        }
        $this->database->db_close_handle();
        
        if ($update_feeds) {
	        // saving all feeds
	        self::save_main_feed();
	        foreach ($this->get_tags(TYPE_OBJ_ARRAY) as $t) {
	        	$t->save_feed();
	        }
	        foreach ($this->get_categories(TYPE_OBJ_ARRAY) as $c) {
	        	$c->save_feed();
	        }
        }
    }
    
    public function remove($update_feeds=true) {
        $query = "DELETE FROM " . DB_PREFIX . "blogposts WHERE post_id='" . $this->post_id . "'";
        
        $this->database->db_connect();
        $this->database->db_query($query);
        $this->database->db_close_handle();
        
        if ($update_feeds) {
	        // re-generating all feeds
	        self::save_main_feed();
	        foreach ($this->get_tags(TYPE_OBJ_ARRAY) as $t) {
	        	$t->save_feed();
	        }
	        foreach ($this->get_categories(TYPE_OBJ_ARRAY) as $c) {
	        	$c->save_feed();
	        }
        }
        
        unset($this);        
    }
    
        
    public static function load($by='id', $data=false, $query_add='', $post_page=false, $force_array=false) {
        global $pec_database, $pec_settings;
        
        if ($post_page) {
            $start_post_row_number = ($pec_settings->get_posts_per_page() * $post_page) - $pec_settings->get_posts_per_page();
            $limit = " LIMIT " . $start_post_row_number . "," . $pec_settings->get_posts_per_page();
        }
        else {
            $limit = "";
        }
        
        /* loading a specific post, or a specific range of posts */ 
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "blogposts WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add . $limit;
                        
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $return_data = null;
            
            if ($pec_database->db_num_rows($resource) > 1 || $force_array) {
                $return_data = array();
                
                while ($p = $pec_database->db_fetch_array($resource)) {
                    $return_data[] = new PecBlogPost($p['post_id'], $p['post_timestamp'], $p['post_year'], $p['post_month'], 
                                                     $p['post_day'], $p['post_author_id'], $p['post_title'], $p['post_content_cut'], 
                                                     $p['post_content'], $p['post_tags'], $p['post_categories'], $p['post_comments_allowed'],
                                                     $p['post_status'], $p['post_slug']);
                }
            }
            elseif ($pec_database->db_num_rows($resource) == 1) {
                $p = $pec_database->db_fetch_array($resource);
                $return_data = new PecBlogPost($p['post_id'], $p['post_timestamp'], $p['post_year'], $p['post_month'], 
                                               $p['post_day'], $p['post_author_id'], $p['post_title'], $p['post_content_cut'], 
                                               $p['post_content'], $p['post_tags'], $p['post_categories'], $p['post_comments_allowed'],
                                               $p['post_status'], $p['post_slug']);
            }
            
            return $return_data;            
        }
        
        /* if loading all posts belonging to a given tag or category or author object */
        elseif ($by && $data && array_key_exists($by, self::$by_obj_array)) {
            $all_posts = self::load('', false, $query_add);
            $posts_on_given_data = array();
            
            if ($post_page) {
                $current_post_number = 1;
                $count_added_posts = 0;
            }
            
            foreach ($all_posts as $post) {
                if ($by == 'tag' && $post->has_tag($data) || 
                    $by == 'category' && $post->in_category($data) ||
                    $by == 'author' && $post->from_author($data)) {
                        
                    if ($post_page) {
                        if ($current_post_number > $start_post_row_number && 
                            $count_added_posts < $pec_settings->get_posts_per_page()) {
                            $posts_on_given_data[] = $post;
                            $count_added_posts++;
                        }
                    }
                    else {
                        $posts_on_given_data[] = $post;
                    }
                    $current_post_number++;
                }
            }
            
            return $posts_on_given_data;
        }
        
        /* loading all posts */
        else {
            $query = "SELECT * FROM " . DB_PREFIX . "blogposts " . $query_add . $limit;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $posts = array();
            
            while ($p = $pec_database->db_fetch_array($resource)) {
                $posts[] = new PecBlogPost($p['post_id'], $p['post_timestamp'], $p['post_year'], $p['post_month'], 
                                              $p['post_day'], $p['post_author_id'], $p['post_title'], $p['post_content_cut'], 
                                              $p['post_content'], $p['post_tags'], $p['post_categories'], $p['post_comments_allowed'],
                                              $p['post_status'], $p['post_slug']);
            }
            
            return $posts;
        }
    }
        
    public static function exists($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "blogposts WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            /* if there are more than 0 rows, the post exists, else not */
            $return_data = $pec_database->db_num_rows($resource) > 0 ? true : false;
            
            return $return_data;            
        }        
        else {
            return false;
        }
    }
    
    public static function slugify($title) {        
        $slug = slugify($title);
        
        $counter = 1;
        while (self::exists('slug', $slug)) {
            $slug = slugify($title) . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    public static function generate_feed_items($posts=false) {
    	if ($posts !== false) {
    		global $pec_settings;
    		
    		$home = $pec_settings->get_blog_onstart();
    		$items = array();
    		
    		foreach ($posts as $p) {
    			$item = new FeedItem();
				$item->title = $p->get_title();
				$item->link = create_blogpost_url($p);
				$item->description = $p->get_content_cut() . $p->get_content();
				$item->source = convert_ampersands_to_entities(create_blog_url(false, $home));
				$item->author = $p->get_author()->get_email();
				$item->date = $p->get_timestamp();
				$items[] = $item;
    		}
    		
    		return $items;
    	}
    	else {
    		return false;
    	}
    }
    
    public static function save_main_feed($settings=false) {
    	if (!$settings) {
    		global $pec_settings;
			$settings = $pec_settings;
    	}
    	global $pec_localization;

    	$home = $settings->get_blog_onstart();
    	$posts = self::load('status', 1, "ORDER BY post_timestamp DESC", false, true);
    	
    	$feed = new UniversalFeedCreator();
		$feed->title = $settings->get_sitename_main() . ' - ' . $pec_localization->get('LABEL_GENERAL_BLOG');
		$feed->description = $settings->get_description();
		$feed->link = convert_ampersands_to_entities(create_blog_url(false, $home));
	
    	$feed_items = self::generate_feed_items($posts);
    	
    	foreach ($feed_items as $item) {
    		$feed->addItem($item);
    	}
    	
    	$feed->saveFeed(FEED_TYPE, MAIN_FEED_PATH . MAIN_FEED_FILE);
    }
}

?>