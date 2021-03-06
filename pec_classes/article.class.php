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
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */

/**
 * The PecArticle is used for creating and loading articles. An instance of it is one article (one row in the database) that can be saved or created using this class.
 */
class PecArticle {

    private $article_id, $article_title, $article_slug, 
            $article_content, $article_onstart, $article_template_id, 
            $readonly;
    
	/**
	 * @static
	 * @var array All database columns that an article has. You have to use the key of one of them to load an article by a specific property.
	 */
    static $by_array = array(
               'id' => 'article_id',
               'title' => 'article_title',
               'slug' => 'article_slug',
               'content' => 'article_content',
               'onstart' => 'article_onstart',
               'template_id' => 'article_template_id'
           );
    
    /**
     * Creates a PecArticle instance.
     * 
     * @param	integer		$id ID of the article
     * @param	string		$title Title of the article
     * @param	string		$content Content of the article
     * @param	boolean		$onstart Wether the article should be placed on the start page or not
     * @param	string		$template_id ID of the template to use (default: GLOBAL_TEMPLATE_ID)
     * @param	string		$slug The slug of this article (default: false, may be generated by the class itself)
     */
    function __construct($id=0, $title, $content, $onstart, $template_id=GLOBAL_TEMPLATE_ID, $slug=false) {
        global $pec_database;
        $this->database =& $pec_database;
        
        /* escaping input data */
        $escaped_data = $this->database->db_string_protection(
            false, 
            array(
                'id' => $id, 'title' => $title, 'template_id' => $template_id
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
        
        $this->article_template_id = $escaped_data['template_id'];
        
        $this->readonly = false;
    }
    
    /**
     * Returns ID of this article.
     * 
     * @return	integer	ID of the article
     */
    public function get_id() {
        return $this->article_id;
    }
    
    /**
     * Returns the title of this article.
     * 
     * @param	boolean	$strip_protextion Wether to remove the database string protection (e.g. mysql_escape_string) or not, default: true
     * @return	string	Title of the article
     */
    public function get_title($strip_protection=true) {
        if ($strip_protection) {
            return $this->database->db_string_protection_decode($this->article_title);
        }
        else {
            return $this->article_title;
        }
    }
    
    
    /**
     * Returns slug of this article.
     * 
     * @return	string	Slug of the article
     */
    public function get_slug() {
        return $this->article_slug;
    }
    
    
    /**
     * Returns the content of this article.
     * 
     * @return	string	Content of this article
     */
    public function get_content() {
        return $this->database->db_string_protection_decode($this->article_content);
    }
    
    
    /**
     * Returns wether the article is assigned to the start page or not.
     * 
     * @param	boolean	$human_readable Wether the return value shall be human readable (symbols as HTML entities) or not, default: false
     * @return	string or boolean	Wether the article is assigned to the start page or not, this will return true or false. If $human_readable is set to `true`, it will return HMTL entities.
     */
    public function get_onstart($human_readable=false) {
        if ($human_readable) {
            return $this->article_onstart == true ? '&#x2713;' : '&#x2717;';
        }
        else {
            return $this->article_onstart;
        }
    }
    
    
    /**
     * Returns the ID of the template to use for this article
     * 
     * @return	string	ID of the template or the GLOBAL_TEMPLATE_ID
     */
    public function get_template_id() {
        return $this->article_template_id;
    }
    
    
	    /**
	     * Returns the template to use for this article
	     * 
	     * @return	PecTemplate 	template to use for this article
	     */
	    public function get_template() {
    		global $pec_settings;
    		
	    	if ($this->article_template_id == GLOBAL_TEMPLATE_ID) {
    			return $pec_settings->get_template();
	    	}
	    	else {
		        return PecTemplate::exists('id', $this->article_template_id) 
		        	? PecTemplate::load('id', $this->article_template_id)
		        	: $pec_settings->get_template();
	    	}
	    }
    
    
    /**
     * Sets the title for this article.
     * 
     * @param	string	$title The new title for this article
     */
    public function set_title($title) {
        if ($title != $this->article_title) {
            $this->article_title = $this->database->db_string_protection($title);
            if (slugify($title) != $this->article_slug) {
            	$this->article_slug = $this->database->db_string_protection(self::slugify($title));
            }
        }
    }
    
    
    /**
     * Sets content for this article.
     * 
     * @param	string	$content The new content for this article
     */
    public function set_content($content) {
        $this->article_content = htmlentities($content);
    }
    
    
    /**
     * Sets wether the article is assigned to the start page or not
     * 
     * @param	boolean	$onstart Wether the article shall be assigned to the start page or not
     */
    public function set_onstart($onstart) {
        $this->article_onstart = $this->database->db_string_protection($onstart, false);
    }
    
    
    /**
     * Set a new template id
     * 
     * @param	string	$template_id ID of the template to set for this article
     */
    public function set_template_id($template_id) {
        $this->article_template_id = $this->database->db_string_protection($template_id, false);
    }
    
    
    /**
     * [DEPRECATED] Make article readonly
     */
    public function make_readonly() {
        if ($this->readonly == false) {
            $this->readonly = true;
        }
    }
    
    
    /**
     * Saves or creates this article
     */
    public function save($insert_id=false) {
        $new = false;
        if (self::exists('id', $this->article_id)) {
            $query = "UPDATE " . DB_PREFIX . "articles SET
                        article_title='"     . $this->article_title . "',
                        article_slug='"      . $this->article_slug . "',
                        article_content='"   . $this->article_content . "',
                        article_onstart='"   . $this->article_onstart . "',
                        article_template_id='" . $this->article_template_id . "'
                      WHERE article_id='"    . $this->article_id . "'";
        }
        else {
            $new = true;
            if ($insert_id) {
                $id_field = 'article_id,';
                $id_data = "'" . $this->article_id . "',";
            }
            else {
                $id_field = '';
                $id_data = '';
            }
            
            $query = "INSERT INTO " . DB_PREFIX . "articles (
                        " . $id_field . "
                        article_title,
                        article_slug,
                        article_content,
                        article_onstart,
                        article_template_id
                      ) VALUES
                      (
                        " . $id_data . "
                        '" . $this->article_title . "',
                        '" . $this->article_slug . "',
                        '" . $this->article_content . "',
                        '" . $this->article_onstart . "',
                        '" . $this->article_template_id . "'
                      )";
        }
        
        $this->database->db_connect();
        $this->database->db_query($query);
        if ($new) {
            $this->article_id = $this->database->db_last_insert_id();
        }
        $this->database->db_close_handle();
    }
    
    
    /**
     * Remove this article
     */
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
    
    
    /**
     * Load a specific article or a specific range/set of articles or all articles
     * 
     * @param	string	$by The database column by that you want to load the article/set of articles. Must be a string of self::$by_array
     * @param	mixed	$data The data that may match the articles you want to load (e.g. if $by is 'id', $data is the ID of the article you want to load)
     * @param	string	$query_add Additional data for the SQL query
     * @param	boolean	$force_array Force the return value to be an array, even if there is only one article
     * @return	array/PecArticle An array of matching articles or one PecArticle instance that matched the query
     */
    public static function load($by='id', $data=false, $query_add='', $force_array=false) {
        global $pec_database;
        
        /* loading a specific article, or a specific range of articles */ 
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "articles WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            
            $return_data = null;
            
            if ($pec_database->db_num_rows($resource) > 1 || $force_array) {
                $return_data = array();
                
                while ($a = $pec_database->db_fetch_array($resource)) {
                    $return_data[] = new PecArticle($a['article_id'], $a['article_title'], $a['article_content'],
                                                 $a['article_onstart'], $a['article_template_id'], $a['article_slug']);
                }
            }
            elseif ($pec_database->db_num_rows($resource) == 1) {
                $a = $pec_database->db_fetch_array($resource);
                $return_data = new PecArticle($a['article_id'], $a['article_title'], $a['article_content'], 
                                              $a['article_onstart'], $a['article_template_id'], $a['article_slug']);
            }
            
            $pec_database->db_close_handle();
            
            return $return_data;            
        }
        
        /* loading all articles */
        else {
            $query = "SELECT * FROM " . DB_PREFIX . "articles " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            
            $articles = array();
            
            while ($a = $pec_database->db_fetch_array($resource)) {
                $articles[] = new PecArticle($a['article_id'], $a['article_title'], $a['article_content'],
                                             $a['article_onstart'], $a['article_template_id'], $a['article_slug']);
            }
            
            $pec_database->db_close_handle();
            
            return $articles;
        }
    }
    
    
    /**
     * Check wether a specific article exists
     * 
     * @param	string	$by The database column by that you want to check. Must be a string of self::$by_array
     * @param	mixed	$data The data that may match the articles you want to check (e.g. if $by is 'id', $data is the ID of the article you want to check)
     * @param	string	$query_add Additional data for the SQL query
     * @return	boolean Wether the article exists or not, this may be true or false
     */
    public static function exists($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "articles WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
                
            /* if there are more than 0 rows, the article exists, else not */
            $return_data = $pec_database->db_num_rows($resource) > 0 ? true : false;
            $pec_database->db_close_handle();
            
            return $return_data;            
        }        
        else {
            return false;
        }
    }
    
    
    /**
     * Slugifies the title of an article
     * 
     * @param	string	$title Title that shall be slugified
     * @return	string The slugified title
     */
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
