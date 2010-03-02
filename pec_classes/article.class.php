<?php

/**
 * pec_classes/article.class.php - Article Class
 * 
 * Defines the main Article class which manages articles.
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

class PecArticle {
    private $article_id, $article_title, $article_slug, 
            $article_content, $article_onstart, $readonly;
            
    static $by_array = array(
               'id' => 'article_id',
               'title' => 'article_title',
               'slug' => 'article_slug',
               'content' => 'article_content',
               'onstart' => 'article_onstart'
           );
    
    function __construct($id=0, $title, $content, $onstart, $slug=false) {
        global $pec_database;
        $this->database = $pec_database;
        
        /* escaping input data */
        $escaped_data = $this->database->db_string_protection(
            false, 
            array(
                'id' => $id, 'title' => $title
            )
        );
        
        $this->article_id = $escaped_data['id'];
        $this->article_title = $escaped_data['title'];
        
        /* if this article hasn't got a slug yet */
        if (!$slug) {
            $this->article_slug = self::slugify($title);
        }
        else {
            $this->article_slug = $slug;
        }
        
        // content doesn't need to be protected, because that is done by the CKEditor :)
        $this->article_content = $content;
        $this->article_onstart = $onstart;
        
        $this->readonly = false;
    }
    
    public function get_id() {
        return $this->article_id;
    }
    
    public function get_title($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->article_title);
        }
        else {
            return $this->article_title;
        }
    }
    
    public function get_slug() {
        return $this->article_slug;
    }
    
    public function get_content() {
        return $this->article_content;
    }
    
    public function get_onstart($human_readable=false) {
        if ($human_readable) {
            return $this->article_onstart == true ? '&#x2713;' : '&#x2717;';
        }
        else {
            return $this->article_onstart;            
        }
    }
    
    
    public function set_title($title) {
        if ($title != $this->article_title) {
            $this->article_title = $this->database->db_string_protection($title);
            $this->article_slug = $this->database->db_string_protection(self::slugify($title));
        }
    }
    
    public function set_content($content) {
        $this->article_content = $content;
    }
    
    public function set_onstart($onstart) {
        $this->article_onstart = $this->database->db_string_protection($onstart);
    }
    
    public function make_readonly() {
        if ($this->readonly == false) {
            $this->readonly = true;
        }
    }
    
    public function save() {
        $new = false;
        if (self::exists('id', $this->article_id)) {
            $query = "UPDATE " . DB_PREFIX . "articles SET
                        article_title='"     . $this->article_title . "',
                        article_slug='"      . $this->article_slug . "',
                        article_content='"   . $this->article_content . "',
                        article_onstart='"   . $this->article_onstart . "'
                      WHERE article_id='"    . $this->article_id . "'";
        }
        else {
            $new = true;
            $query = "INSERT INTO " . DB_PREFIX . "articles (
                        article_title,
                        article_slug,
                        article_content,
                        article_onstart
                      ) VALUES
                      (
                        '" . $this->article_title . "',
                        '" . $this->article_slug . "',
                        '" . $this->article_content . "',
                        '" . $this->article_onstart . "'
                      )";
        }
        
        $this->database->db_connect();
        $this->database->db_query($query);
        if ($new) {
            $this->article_id = $this->database->db_last_insert_id();
        }
        $this->database->db_close_handle();
    }
    
    public function remove() {
        // remove texts assigned to this article
        $assigned_texts = PecSidebarText::load('article', $this);
        foreach ($assigned_texts as $t) {
            $t->remove_article($this);
            $t->save();
        }
        
        // remove linkcategories assigned to this article
        $assigned_linkcats = PecSidebarLinkCat::load('article', $this);
        foreach ($assigned_linkcats as $lc) {
            $lc->remove_article($this);
            $lc->save();
        }
        
        $query = "DELETE FROM " . DB_PREFIX . "articles WHERE article_id='" . $this->article_id . "'";
        
        $this->database->db_connect();
        $this->database->db_query($query);
        $this->database->db_close_handle();
        
        unset($this);        
    }
    
        
    public static function load($by='id', $data=false, $query_add='', $force_array=false) {
        global $pec_database;
        
        /* loading a specific article, or a specific range of articles */ 
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "articles WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $return_data = null;
            
            if ($pec_database->db_num_rows($resource) > 1 || $force_array) {
                $return_data = array();
                
                while ($a = $pec_database->db_fetch_array($resource)) {
                    $return_data[] = new PecArticle($a['article_id'], $a['article_title'], $a['article_content'],
                                                 $a['article_onstart'], $a['article_slug']);
                }
            }
            elseif ($pec_database->db_num_rows($resource) == 1) {
                $a = $pec_database->db_fetch_array($resource);
                $return_data = new PecArticle($a['article_id'], $a['article_title'], $a['article_content'], 
                                              $a['article_onstart'], $a['article_slug']);
            }
            
            return $return_data;            
        }
        
        /* loading all articles */
        else {
            $query = "SELECT * FROM " . DB_PREFIX . "articles " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
            
            $articles = array();
            
            while ($a = $pec_database->db_fetch_array($resource)) {
                $articles[] = new PecArticle($a['article_id'], $a['article_title'], $a['article_content'],
                                             $a['article_onstart'], $a['article_slug']);
            }
            
            return $articles;
        }
    }
        
    public static function exists($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "articles WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            $pec_database->db_close_handle();
                
            /* if there are more than 0 rows, the article exists, else not */
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
}

?>