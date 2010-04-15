<?php

/**
 * pec_classes/blog-comment.class.php - Blog Comment Class
 * 
 * Defines the main Blog Comment class which manages Blog Comments.
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

class PecBlogComment {
    private $comment_id, $comment_post_id, $comment_title, $comment_author, $comment_email, $comment_timestamp, 
            $comment_content;
            
    static $by_array = array(
               'id' => 'comment_id',
               'post_id' => 'comment_post_id',
               'title' => 'comment_title',
               'author' => 'comment_author',
               'email' => 'comment_email',
               'timestamp' => 'comment_timestamp',
               'content' => 'comment_content'
           );
            
    static $by_obj_array = array(
               'post' => ''
           );
    
    function __construct($id=0, $post_id, $title, $author, $email, $timestamp, $content, $from_database=true) {
        global $pec_database;
        $this->database = $pec_database;
        
        /* escaping input data */
        $escaped_data = $this->database->db_string_protection(
            false, 
            array(
                'id' => $id, 'post_id' => $post_id, 'title' => $title, 'author' => $author, 'email' => $email, 
                'timestamp' => $timestamp
            )
        );
        
        $this->comment_id = $escaped_data['id'];
        $this->comment_post_id = $escaped_data['post_id'];
        $this->comment_title = $escaped_data['title'];
        $this->comment_author = $escaped_data['author'];
        $this->comment_email = $escaped_data['email'];
        $this->comment_timestamp = $escaped_data['timestamp'];
        
        if ($from_database) {
            $this->comment_content = $content;
        }
        else {
            $this->comment_content = $this->database->db_escape_string(nl2br(htmlspecialchars($content)));
        }        
    }
    
    public function get_id() {
        return $this->comment_id;
    }
    
    public function get_post_id() {
        return $this->comment_post_id;
    }
    
        public function get_post() {
            return PecBlogPost::load('id', $this->comment_post_id);
        }
    
    public function get_title($strip_protection=true) {
        if ($strip_protection) {
            return stripslashes($this->comment_title);
        }
        else {
            return $this->comment_title;
        }
    }
    
    public function get_author($strip_protection=true) {
        if ($strip_protection) {
            return stripslashes($this->comment_author);
        }
        else {
            return $this->comment_author;
        }
    }
    
    public function get_email($strip_protection=true) {
        if ($strip_protection) {
            return stripslashes($this->comment_email);
        }
        else {
            return $this->comment_email;
        }
    }
    
    public function get_timestamp($format=false) {
        if ($format) {
            return date($format, $this->comment_timestamp);
        }
        else {
            return $this->comment_timestamp;
        }
    }
    
    public function get_content() {
        return stripslashes($this->comment_content);
    }
  
    
    public function set_post_id($post_id) {
        $this->comment_post_id = $this->database->db_string_protection($post_id);
    }
    
        public function set_post($post) {
            $this->comment_post_id = $this->database->db_string_protection($post->get_id());
        }
    
    public function set_title($title) {
        $this->comment_title = $this->database->db_string_protection($title);
    }
    
    public function set_author($author) {
        $this->comment_author = $this->database->db_string_protection($author);
    }
    
    public function set_email($email) {
        $this->comment_email = $this->database->db_string_protection($email);
    }
    
    public function set_timestamp($timestamp) {
        $this->comment_timestamp = $this->database->db_string_protection($timestamp);
    }
    
    public function set_content($content) {
        $this->comment_content = $this->database->db_escape_string(nl2br(htmlspecialchars($content)));
    }
    
    public function belongs_to_post($post) {
        if ($post->get_id() == $this->comment_post_id) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function from_author($author) {
        if ($author == $this->comment_author) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function notify_author_and_admin() {
    	global $pec_localization, $pec_settings;
    	
    	$post = $this->get_post();
    	
    	$email_title = $pec_localization->get('LABEL_COMMENTS_NOTIFY_EMAIL_TITLE');
    	$email_text = $pec_localization->get('LABEL_COMMENTS_NOTIFY_EMAIL_TEXT');
    	
    	$email_title = str_replace('{%POST_TITLE%}', $post->get_title(), $email_title);
    	$email_text = str_replace('{%POST_TITLE%}', $post->get_title(), $email_text);
    	$email_text = str_replace('{%POST_URL%}', create_blogpost_url($post), $email_text);
    	$email_text = str_replace('{%COMMENT_AUTHOR%}', $this->get_author(), $email_text);
    	
    	$author = $post->get_author();
    	
    	# WTF? Otherwise: "Can't use method return value in write context"
    	$author_forename = $author->get_forename();
    	$author_name = !empty($author_forename) ? $author->get_forename() : $author->get_name();
    	$email_text_author = str_replace('{%USERNAME%}', $author_name, $email_text);
    	
    	$admin_email = $pec_settings->get_admin_email();
    	$author_email = $post->get_author()->get_email();
        mail($author_email, $email_title, $email_text_author);
        
        if ($admin_email != $author_email) {
    		$email_text_admin = str_replace('{%USERNAME%}', 'Admin', $email_text);
        	mail($admin_email, $email_title, $email_text_admin);
        }
    }
    
    public function save() {
    	global $pec_settings;
    	
        $new = false;
        if (self::exists('id', $this->comment_id) && $this->get_post()->get_comments_allowed()) {
            $query = "UPDATE " . DB_PREFIX . "blogcomments SET
                        comment_post_id='"    . $this->comment_post_id . "',
                        comment_title='"      . $this->comment_title . "',
                        comment_author='"     . $this->comment_author . "',
                        comment_email='"      . $this->comment_email . "',
                        comment_timestamp='"  . $this->comment_timestamp . "',
                        comment_content='"    . $this->comment_content . "'
                      WHERE comment_id='"    . $this->comment_id . "'";
        }
        else {
            $new = true;
            $query = "INSERT INTO " . DB_PREFIX . "blogcomments (
                        comment_post_id,
                        comment_title,
                        comment_author,
                        comment_email,
                        comment_timestamp,
                        comment_content
                      ) VALUES
                      (
                        '" . $this->comment_post_id . "',
                        '" . $this->comment_title . "',
                        '" . $this->comment_author . "',
                        '" . $this->comment_email . "',
                        '" . $this->comment_timestamp . "',
                        '" . $this->comment_content . "'
                      )";
        }
        
        $this->database->db_connect();
        $this->database->db_query($query);
        if ($new) {
            $this->comment_id = $this->database->db_last_insert_id();
        }
        $this->database->db_close_handle();
        if ($new && $pec_settings->get_comment_notify()) {
            $this->notify_author_and_admin();
        }
    }
    
    public function remove() {
        $query = "DELETE FROM " . DB_PREFIX . "blogcomments WHERE comment_id='" . $this->comment_id . "'";
        
        $this->database->db_connect();
        $this->database->db_query($query);
        $this->database->db_close_handle();
        
        unset($this);        
    }
    
        
    public static function load($by='id', $data=false, $query_add='', $force_array=false) {
        global $pec_database;
        
        /* loading a specific comment, or a specific range of comments */ 
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $query = "SELECT * FROM " . DB_PREFIX . "blogcomments WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $return_data = null;
            
            if ($pec_database->db_num_rows($resource) > 1 || $force_array) {
                $return_data = array();
                
                while ($c = $pec_database->db_fetch_array($resource)) {
                    $return_data[] = new PecBlogComment($c['comment_id'], $c['comment_post_id'], $c['comment_title'], $c['comment_author'], 
                                                        $c['comment_email'], $c['comment_timestamp'], $c['comment_content']);
                }
            }
            elseif ($pec_database->db_num_rows($resource) == 1) {
                $c = $pec_database->db_fetch_array($resource);
                $return_data = new PecBlogComment($c['comment_id'], $c['comment_post_id'], $c['comment_title'], $c['comment_author'], 
                                                  $c['comment_email'], $c['comment_timestamp'], $c['comment_content']);
            }
            
            return $return_data;            
        }
        
        /* if loading all comments belonging to a given post object */
        elseif ($by && $data && array_key_exists($by, self::$by_obj_array)) {
            $all_comments = self::load();
            $comments_on_given_data = array();
            
            foreach ($all_comments as $comment) {
                if ($by == 'post') {
                    if ($comment->belongs_to_post($data)) {
                        $comments_on_given_data[] = $comment;
                    }
                }
            }
            
            return $comments_on_given_data;
        }
        
        /* loading all comments */
        else {
            $query = "SELECT * FROM " . DB_PREFIX . "blogcomments " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $comments = array();
            
            while ($c = $pec_database->db_fetch_array($resource)) {
                $comments[] = new PecBlogComment($c['comment_id'], $c['comment_post_id'], $c['comment_title'], $c['comment_author'], 
                                              $c['comment_email'], $c['comment_timestamp'], $c['comment_content']);
            }
            
            return $comments;
        }
    }
        
    public static function exists($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $query = "SELECT * FROM " . DB_PREFIX . "blogcomments WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            /* if there are more than 0 rows, the comment exists, else not */
            $return_data = $pec_database->db_num_rows($resource) > 0 ? true : false;
            
            return $return_data;            
        }        
        else {
            return false;
        }
    }
}

?>