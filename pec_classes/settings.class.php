<?php

/**
 * pec_classes/settings.class.php - Setting Class
 * 
 * Defines the main Setting class which manages the Settings.
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

class PecSetting {
    private $setting_id, $setting_sitename_main, $setting_sitename_sub, $setting_description, 
            $setting_tags, $setting_admin_email, $setting_comment_notify, $setting_locale, $setting_url_type, 
            $setting_posts_per_page, $setting_blog_onstart, $setting_template_id, $setting_nospam_key_1, $setting_nospam_key_2, 
            $setting_nospam_key_3, $readonly;
            
    static $by_array = array();
    
    function __construct($id=0, $sitename_main, $sitename_sub, $description, $tags, $admin_email, $comment_notify, 
                         $locale, $url_type, $posts_per_page, $blog_onstart, $template_id, $nospam_1=false, 
                         $nospam_2=false, $nospam_3=false) {
        global $pec_database;
        $this->database =& $pec_database;
        
        /* escaping input data */
        $escaped_data = $this->database->db_string_protection(
            false, 
            array(
                'id' => $id, 'sitename_main' => $sitename_main, 'sitename_sub' => $sitename_sub, 
                'tags' => $tags, 'admin_email' => $admin_email, 'comment_notify' => $comment_notify, 
                'locale' => $locale, 'url_type' => $url_type, 'posts_per_page' => $posts_per_page, 
                'blog_onstart' => $blog_onstart, 'template_id' => $template_id
            )
        );
        
        $this->setting_id = $escaped_data['id'];
        $this->setting_sitename_main = $escaped_data['sitename_main'];        
        $this->setting_sitename_sub = $escaped_data['sitename_sub'];
        $this->setting_description = $description;
        $this->setting_tags = $escaped_data['tags'];
        $this->setting_admin_email = $escaped_data['admin_email'];
        $this->setting_comment_notify = $escaped_data['comment_notify'];
        
        $this->setting_locale = $escaped_data['locale'];
        $this->setting_url_type = $escaped_data['url_type'];
        $this->setting_posts_per_page = $escaped_data['posts_per_page'];
        $this->setting_blog_onstart = $escaped_data['blog_onstart'];
        $this->setting_template_id = $escaped_data['template_id'];
        
        if ($nospam_1 && $nospam_2 && $nospam_3) {
            $this->setting_nospam_key_1 = $nospam_1;
            $this->setting_nospam_key_2 = $nospam_2;
            $this->setting_nospam_key_3 = $nospam_3;
        }
        else {
            $time_1 = time();
            $time_2 = 2 * $time_1;
            $time_3 = 3 * $time_1;
            
            $this->setting_nospam_key_1 = $this->create_nospam_key($time_1 . $_SERVER["REMOTE_ADDR"]);
            $this->setting_nospam_key_2 = $this->create_nospam_key(realpath(__FILE__) . $time_2);
            $this->setting_nospam_key_3 = $this->create_nospam_key($time_3 . $this->setting_admin_email);
        }
        
        $this->readonly = false;
    }
    
    public function get_id() {
        return $this->setting_id;
    }
    
    public function get_sitename_main($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->setting_sitename_main);
        }
        else {
            return $this->setting_sitename_main;
        }
    }
    
    public function get_sitename_sub($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->setting_sitename_sub);
        }
        else {
            return $this->setting_sitename_sub;
        }
    }
    
    public function get_description() {
        return $this->setting_description;
    }
    
    public function get_tags($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->setting_tags);
        }
        else {
            return $this->setting_tags;
        }
    }
    
    public function get_admin_email($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->setting_admin_email);
        }
        else {
            return $this->setting_admin_email;
        }
    }
    
    public function get_comment_notify() {
        return $this->setting_comment_notify;
    }
    
    public function get_locale() {
        return $this->setting_locale;
    }
    
    public function get_url_type() {
        return $this->setting_url_type;
    }
    
    public function get_posts_per_page() {
        return $this->setting_posts_per_page;
    }
    
    public function get_blog_onstart() {
        return $this->setting_blog_onstart;
    }
    
    public function get_template_id() {
        return $this->setting_template_id;
    }
    
	    public function get_template() {
	        return PecTemplate::exists('id', $this->setting_template_id) 
	        	? PecTemplate::load('id', $this->setting_template_id)
	        	: PecTemplate::load('title', DEFAULT_TEMPLATE_NAME);
	    }
    
    public function get_nospam_key($number) {
        switch ($number) {
            case 1: return $this->setting_nospam_key_1; break;
            case 2: return $this->setting_nospam_key_2; break;
            case 3: return $this->setting_nospam_key_3; break;            
            default: return ''; break;
        }
    }
    
    public function get_load_by() {
    	if ($this->setting_url_type === URL_TYPE_HUMAN || $this->setting_url_type === URL_TYPE_REWRITE) {
    		return 'slug';
    	}
    	else {
    		return 'id';
    	}
    }

        
    public function set_sitename_main($sitename_main) {
        $this->setting_sitename_main = $sitename_main;
    }
    
    public function set_sitename_sub($sitename_sub) {
        $this->setting_sitename_sub = $sitename_sub;
    }
    
    public function set_description($description) {
        $this->setting_description = $description;
    }
    
    public function set_tags($tags) {
        $this->setting_tags = $tags;
    }
    
    public function set_admin_email($admin_email) {
        $this->setting_admin_email = $admin_email;
    }
    
    public function set_comment_notify($comment_notify) {
        $this->setting_comment_notify = $comment_notify;
    }
    
    public function set_locale($locale) {
        $this->setting_locale = $locale;
    }
    
    public function set_url_type($url_type) {
        $this->setting_url_type = $url_type;
    }
    
    public function set_posts_per_page($posts_per_page) {
        $this->setting_posts_per_page = $posts_per_page;
    }
    
    public function set_blog_onstart($blog_onstart) {
        $this->setting_blog_onstart = $blog_onstart;
    }
    
    public function set_template_id($template_id) {
        $this->setting_template_id = $template_id;
    }
    
    public function set_nospam_key($number, $key) {
        switch ($number) {
            case 1: $this->setting_nospam_key_1 = $key; break;
            case 2: $this->setting_nospam_key_2 = $key; break;
            case 3: $this->setting_nospam_key_3 = $key; break;
        }
    }
    
    public function generate_new_nospam_keys() {
        $time_1 = time();
        $time_2 = 3 * $time_1;
        $time_3 = 4 * $time_1;
        
        $this->setting_nospam_key_1 = $this->create_nospam_key($_SERVER["REMOTE_ADDR"] . $time_1);
        $this->setting_nospam_key_2 = $this->create_nospam_key($time_2 . realpath(__FILE__));
        $this->setting_nospam_key_3 = $this->create_nospam_key(md5($this->setting_admin_email)  . $time_3);
    }
    
    private function create_nospam_key($extra_string) {
        return random_string(rand(5, 15)) . base64_encode($extra_string) . random_string(rand(5, 15));
    }
    
    public function make_readonly() {
        if ($this->readonly == false) {
            $this->readonly = true;
        }
    }
    
    public function save($update_feeds=true) {
        $new = false;
        if (self::exists()) {
            $query = "UPDATE " . DB_PREFIX . "settings SET
                        setting_sitename_main='"  . $this->setting_sitename_main . "',
                        setting_sitename_sub='"   . $this->setting_sitename_sub . "',
                        setting_description='"    . $this->setting_description . "',
                        setting_tags='"           . $this->setting_tags . "',
                        setting_admin_email='"    . $this->setting_admin_email . "',
                        setting_comment_notify='" . $this->setting_comment_notify . "',
                        setting_locale='"         . $this->setting_locale . "',
                        setting_url_type='"       . $this->setting_url_type . "',
                        setting_posts_per_page='" . $this->setting_posts_per_page . "',
                        setting_blog_onstart='"   . $this->setting_blog_onstart . "',
                        setting_template_id='"    . $this->setting_template_id . "',
                        setting_nospam_key_1='"   . $this->setting_nospam_key_1 . "',
                        setting_nospam_key_2='"   . $this->setting_nospam_key_2 . "',
                        setting_nospam_key_3='"   . $this->setting_nospam_key_3 . "'
                      WHERE setting_id='"    . $this->setting_id . "'";
        }
        else {
            $new = true;
            $query = "INSERT INTO " . DB_PREFIX . "settings (
                        setting_sitename_main,
                        setting_sitename_sub,
                        setting_description,
                        setting_tags,
                        setting_admin_email,
                        setting_comment_notify,
                        setting_locale,
                        setting_url_type,
                        setting_posts_per_page,
                        setting_blog_onstart,
                        setting_template_id,
                        setting_nospam_key_1,
                        setting_nospam_key_2,
                        setting_nospam_key_3
                      ) VALUES
                      (
                        '" . $this->setting_sitename_main . "',
                        '" . $this->setting_sitename_sub . "',
                        '" . $this->setting_description . "',
                        '" . $this->setting_tags . "',
                        '" . $this->setting_admin_email . "',
                        '" . $this->setting_comment_notify . "',
                        '" . $this->setting_locale . "',
                        '" . $this->setting_url_type . "',
                        '" . $this->setting_posts_per_page . "',
                        '" . $this->setting_blog_onstart . "',
                        '" . $this->setting_template_id . "',
                        '" . $this->setting_nospam_key_1 . "',
                        '" . $this->setting_nospam_key_2 . "',
                        '" . $this->setting_nospam_key_3 . "'
                      )";
        }
        
        $this->database->db_connect();
        $this->database->db_query($query);
        if ($new) {
            $this->setting_id = $this->database->db_last_insert_id();
        }
        $this->database->db_close_handle();
        
        if ($update_feeds) {
        	$blog_tags = PecBlogTag::load();
        	$blog_categories = PecBlogCategory::load();
        	
	        // saving all feeds
        	PecBlogPost::save_main_feed();
	        foreach ($blog_tags as $t) {
	        	$t->save_feed();
	        }
	        foreach ($blog_categories as $c) {
	        	$c->save_feed();
	        }
        }
    }
    
    public function remove() {
        $query = "DELETE FROM " . DB_PREFIX . "settings WHERE setting_id='" . $this->setting_id . "'";
        
        $this->database->db_connect();
        $this->database->db_query($query);
        $this->database->db_close_handle();
        
        unset($this);        
    }
    
        
    public static function load($query_add='') {
        global $pec_database;
        
        $query = "SELECT * FROM " . DB_PREFIX . "settings WHERE setting_id='1' " . $query_add;
            
        $pec_database->db_connect();
        $resource = $pec_database->db_query($query);
            
        $return_data = null;

        $s = $pec_database->db_fetch_array($resource);
        $setting = new PecSetting($s['setting_id'], $s['setting_sitename_main'], $s['setting_sitename_sub'],
                                  $s['setting_description'], $s['setting_tags'], $s['setting_admin_email'], 
                                  $s['setting_comment_notify'], $s['setting_locale'], $s['setting_url_type'], 
                                  $s['setting_posts_per_page'], $s['setting_blog_onstart'], $s['setting_template_id'], 
                                  $s['setting_nospam_key_1'], $s['setting_nospam_key_2'], $s['setting_nospam_key_3']);
        $pec_database->db_close_handle();
        
        return $setting;      
    }
        
    public static function exists($query_add='') {
        global $pec_database;
        
        $query = "SELECT * FROM " . DB_PREFIX . "settings WHERE setting_id='1' " . $query_add;
            
        $pec_database->db_connect();
        $resource = $pec_database->db_query($query);
                
        /* if there are more than 0 rows, the settings exists, else not */
        $return_data = $pec_database->db_num_rows($resource) > 0 ? true : false;
        $pec_database->db_close_handle();
            
        return $return_data; 
    }
}

?>