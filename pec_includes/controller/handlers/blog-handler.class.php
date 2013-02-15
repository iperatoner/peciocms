<?php

/**
 * pec_includes/controller/handlers/blog-handler.class.php - Contain the blog handler class
 * 
 * Contains the PecBlogHandler which is a class for handling and applying posts, categories, tags, etc. to the TemplateResource
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
 * @subpackage	pec_includes.controller.handlers
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */


/**
 * PecBlogHandler is the class for handling and applying blog posts, categories, tags etc. to the TemplateResource.
 */
class PecBlogHandler extends PecAbstractHandler {
	
	/**
	 * @var array $archive_data Data that is related to the current archive view
	 */
	private $archive_data = array(
		'where_condition' => '',
		'day' => '',
		'month' => '',
		'year'
	);
	
	
	/**
	 * @var array $blogcategory Blog category of the current view, else false
	 */
	private $blogcategory = false;
	
	
	/**
	 * @var array $blogtag Blog tag of the current view, else false
	 */
	private $blogtag = false;
	
	
    /**
     * Creates a PecBlogHandler instance.
     */
    function __construct() {
    	parent::__construct();
    }
    
    
    /**
     * May update the PecTemplateResource with new data (e.g. the current post etc.)
     * 
     * @param	PecTemplateResource $template_resource Holds a lot of data (e.g. objects, articles, etc.) related to the current view
     * @return	array The updated PecTemplateResource
     */
    public function apply($template_resource) {
        $current_blog_page = isset($_GET['p']) && !empty($_GET['p']) ? (int)$_GET['p'] : 1;
        
        $by = $this->settings->get_load_by();
    	
        $view_main =& $this->current_page['view']['main'];
        $view_sub =& $this->current_page['view']['sub'];
        
        if ($view_main === SITE_VIEW_BLOGPOST) {
            
            if (PecBlogPost::exists($by, $this->current_page['view']['data'])) {
                $blogpost = PecBlogPost::load($by, $this->current_page['view']['data']);
                
            	$this->process_new_comment($blogpost);
            	
            	$template_resource->set('blogpost', $blogpost);
            }
            else {
                $this->is_404 = true;
            }
        }
        elseif ($view_main === SITE_VIEW_BLOGCATEGORY || 
        		$view_sub === SITE_VIEW_BLOGCATEGORY) {
            
            if (PecBlogCategory::exists($by, $this->current_page['view']['data'])) {
                $this->blogcategory = PecBlogCategory::load($by, $this->current_page['view']['data']);
                
            	$blogposts = PecBlogPost::load(
            		'category',
            		$this->blogcategory,
            		"WHERE post_status='1' ORDER BY post_timestamp DESC",
            		$current_blog_page,
            		true
            	);
            	
	            // all available posts are needed for older / newer posts link, not only those from the current page
	            $all_blogposts_count = count(PecBlogPost::load('category', $this->blogcategory, "WHERE post_status='1'", false, true));
	            
            	$template_resource->set('blogcategory', $this->blogcategory);
            }
            else {
                $this->is_404 = true;
            }
        }
        elseif ($view_main === SITE_VIEW_BLOGTAG || 
        		$view_sub === SITE_VIEW_BLOGTAG) {
            
            if (PecBlogTag::exists($by, $this->current_page['view']['data'])) {
                $this->blogtag = PecBlogTag::load($by, $this->current_page['view']['data']);
            	
                $blogposts = PecBlogPost::load(
                	'tag',
                	$this->blogtag,
                	"WHERE post_status='1' ORDER BY post_timestamp DESC",
                	$current_blog_page,
                	true
                );
            	
	            // all available posts are needed for older / newer posts link, not only those from the current page
	            $all_blogposts_count = count(PecBlogPost::load('tag', $this->blogtag, "WHERE post_status='1'", false, true));
            
                $template_resource->set('blogtag', $this->blogtag);
            }
            else {
                $this->is_404 = true;
            }
        }
        elseif ($view_main === SITE_VIEW_BLOGARCHIVE || 
        		$view_sub === SITE_VIEW_BLOGARCHIVE) {
            
            $this->archive_data = $this->generate_archive_data();
            
            $blogposts = PecBlogPost::load(
            	0,
            	false, 
            	$this->archive_data['where_condition'] . " AND post_status='1' ORDER BY post_timestamp DESC", 
            	$current_blog_page,
            	true
            );
            
            // all available posts are needed for older / newer posts link, not only those from the current page
            // since we don't need to check for objects like categories/tags here, we can use a direct query for this
            $this->database->db_connect();
            $all_blogposts_count = $this->database->db_query(
            	"SELECT COUNT(*) as total FROM " . DB_PREFIX . 
            	"blogposts " . $this->archive_data['where_condition'] . " AND post_status='1'"
            );
            $all_blogposts_count = $this->database->db_fetch_array($all_blogposts_count);
            $this->database->db_close_handle();
            $all_blogposts_count = $all_blogposts_count['total'];
            
            $template_resource->set('blogarchive_day', $this->archive_data['day']);
            $template_resource->set('blogarchive_month', $this->archive_data['month']);
            $template_resource->set('blogarchive_year', $this->archive_data['year']);
        }
        elseif ($view_main === SITE_VIEW_BLOG || 
        		$view_sub === SITE_VIEW_BLOG) {
            $blogposts = PecBlogPost::load(
            	'status', 
            	1,
            	"ORDER BY post_timestamp DESC",
            	$current_blog_page,
            	true
            );
            
            // all available posts are needed for older / newer posts link, not only those from the current page
            // since we don't need to check for objects like categories/tags here, we can use a direct query for this
            $this->database->db_connect();
            $all_blogposts_count = $this->database->db_query(
            	"SELECT COUNT(*) as total FROM " . DB_PREFIX . "blogposts WHERE post_status='1'"
            );
            $all_blogposts_count = $this->database->db_fetch_array($all_blogposts_count);
            $this->database->db_close_handle();
            $all_blogposts_count = $all_blogposts_count['total'];
        }
        
        
        // Generic data that is generated all the same for category/tag/archive/default view of the blog
        if ($view_sub === SITE_VIEW_BLOG ||
        	$view_sub === SITE_VIEW_BLOGCATEGORY || 
        	$view_sub === SITE_VIEW_BLOGTAG || 
        	$view_sub === SITE_VIEW_BLOGARCHIVE) {
        		
        	$available_blog_pages = $this->get_available_blog_pages($all_blogposts_count);
        	
        	$older_entries_page = $this->get_older_entries_page($current_blog_page, $available_blog_pages);
        	$newer_entries_page = $this->get_newer_entries_page($current_blog_page, $available_blog_pages);
        	
        	$older_entries_url = $this->get_blog_page_url($older_entries_page);
        	$newer_entries_url = $this->get_blog_page_url($newer_entries_page);
        	
        	
        	$template_resource->set('available_blog_pages', $available_blog_pages);
        	
        	$template_resource->set('blog_older_entries_page', $older_entries_page);
        	$template_resource->set('blog_newer_entries_page', $newer_entries_page);
        	
        	$template_resource->set('blog_older_entries_url', $older_entries_url);
        	$template_resource->set('blog_newer_entries_url', $newer_entries_url);
        	
        	// Finally we have to set the blog posts
        	$template_resource->set('blogposts', $blogposts);
        	$template_resource->set('all_available_blogposts', $all_blogposts);
        }
        
    	#return $template_resource;
    }
    
    
    /**
     * Generates data related to the blog archive being viewed right now.
     * 
     * @return	array Contains day/month/year of the archive and an SQL `WHERE`-condition for loading the correct blog posts
     */
    public function generate_archive_data() {
        // HINT: default, human and rewrite all have the same query string structure for archives
        
        $where_condition = "WHERE ";
            
        // needed to check if there is any condition yet after the WHERE keyword
        $some_condition = false;
            
        $day = $month = $year = false;
            
        // generate the where condition wether day, month or year is given
        if (isset($_GET['day']) && !empty($_GET['day'])) {
        	$day = $this->database->db_string_protection($_GET['day']);
        	
            $where_condition .= "post_day='" . $day . "'";
            $some_condition = true;
        }
        
        if (isset($_GET['month']) && !empty($_GET['month'])) {
            if ($some_condition) {
                $where_condition .= " AND ";
            }
            else {                        
                $some_condition = true;    
            }
            
         	$month = $this->database->db_string_protection($_GET['month']);
            $where_condition .= "post_month='" . $month . "'";                
        }
        
        if (isset($_GET['year']) && !empty($_GET['year'])) {
            if ($some_condition) {
                $where_condition .= " AND ";
            }
            else {                        
                $some_condition = true;    
            }
            
        	$year = $this->database->db_string_protection($_GET['year']);
            $where_condition .= "post_year='" . $year . "'";                
        }
        
        return array(
        	'where_condition' => $where_condition, 
        	'day' => $day,
        	'month' => $month,
        	'year' => $year
        );
    }

    
    /**
	 * Calculates the available blog pages
	 * 
	 * @param	integer	$all_blogposts_count Number of available blog posts
	 * @return	integer	Number of blog pages
	 */
    public function get_available_blog_pages($all_blogposts_count) {
    	if ($all_blogposts_count) {
    		$available_pages = ceil($all_blogposts_count / $this->settings->get_posts_per_page());
    		return $available_pages;
    	}
    	else {
    		return 1;
    	}
    }
    
    
    /**
	 * Calculates the page number of the older entries page
	 * 
	 * @param	integer	$current_blog_page The current blog page, e.g. 2
	 * @param	integer	$available_pages All available blog pages, e.g. 5
	 * @return	integer	Number of the older entries blog page
	 */
    public function get_older_entries_page($current_blog_page=false, $available_pages=false) {
    	if ($current_blog_page !== false && $available_pages !== false) {
    		if ($current_blog_page < $available_pages && $current_blog_page != $available_pages) {
    			$older_entries_page = $current_blog_page + 1;
    		}
    		else {
    			$older_entries_page = false;
    		}
    		
    		return $older_entries_page;
    	}
    	else {
    		return 1;
    	}
    }
    
    
    /**
	 * Calculates the page number of the newer entries page
	 * 
	 * @param	integer	$current_blog_page The current blog page, e.g. 2
	 * @param	integer	$available_pages All available blog pages, e.g. 5
	 * @return	integer	Number of the newer entries blog page
	 */    
    public function get_newer_entries_page($current_blog_page=false, $available_pages=false) {
    	if ($current_blog_page !== false && $available_pages !== false) {
    		if ($current_blog_page <= $available_pages && $current_blog_page != 1) {
    			$newer_entries_page = $current_blog_page - 1;
    		}
    		else {
    			$newer_entries_page = false;
    		}
    		
    		return $newer_entries_page;
    	}
    	else {
    		return 1;
    	}
    }    
    
    
    /**
	 * Generates the blog URL for a blog page
	 * 
	 * @param	integer	$page The blog page for which we want to create the URl, e.g. 2
	 * @return	string|boolean	Blog page URL or false
	 */
    public function get_blog_page_url($page=false) {
    	if ($page !== false) {
    		if ($this->current_page['view']['main'] == SITE_VIEW_BLOG) {
    			$home =  false;
    		}
    		elseif ($this->current_page['view']['main'] == SITE_VIEW_HOME) {
    			$home =  true;
    		}
    		
    		switch ($this->current_page['view']['sub']) {
    			case SITE_VIEW_BLOG: $url = create_blog_url($page, $home); break;
    			case SITE_VIEW_BLOGCATEGORY: $url = create_blogcategory_url($this->blogcategory, $page, $home); break;
    			case SITE_VIEW_BLOGTAG: $url = create_blogtag_url($this->blogtag, $page, $home); break;
    			case SITE_VIEW_BLOGARCHIVE: 
    				$url = create_blogarchive_url(
    					$this->archive_data['day'],
    					$this->archive_data['month'],
    					$this->archive_data['year'],
    					$page,
    					$home
    				); 
    				break;
    		}
    		
    		return $url;
    	}
    	else {
    		return false;
    	}
    }
    
    
    /**
	 * Processes a new comment.
	 * 
	 * @param	PecBlogPost	$post The blog post object for that the comment has to be created
	 */
    public function process_new_comment($post=false) {
        	
        if ($post && isset($_GET['action']) && $_GET['action'] == 'new-comment') {
        	
            // EVERYTHING SET?
            if (isset($_POST['comment_author']) && isset($_POST['comment_email']) &&
                isset($_POST['comment_title']) && isset($_POST['comment_content'])) {
                    
                $unique_key_1 = $this->settings->get_nospam_key(1);
                $unique_key_2_md5 = md5($this->settings->get_nospam_key(2));
                $unique_key_3 = $this->settings->get_nospam_key(3);
                
                // SPAM CHECK
                if (isset($_POST[$unique_key_1]) && isset($_POST[$unique_key_2_md5]) && empty($_POST[$unique_key_1]) &&
                    $_POST[$unique_key_2_md5] == crypt($unique_key_3, $_POST[$unique_key_2_md5])) {
                    
                    // SOMETHING EMPTY
                    if (empty($_POST['comment_author']) || empty($_POST['comment_email']) ||
                        empty($_POST['comment_title']) || empty($_POST['comment_content'])) {
        				
                        pec_redirect(create_blogpost_url($post, false, 'message=empty_fields#messages'), 0, false, false);
                    }
                    
                    // EMAIL INCORRECT
                    elseif (!email_syntax_correct($_POST['comment_email']) || 
                            !email_host_exists($_POST['comment_email'])) {
        				
                        pec_redirect(create_blogpost_url($post, false, 'message=email_incorrect#messages'), 0, false, false);
                    }
                    
                    // ALRIGHT
                    else {
                        $comment = new PecBlogComment(
                        	NULL_ID, $post->get_id(), $_POST['comment_title'], $_POST['comment_author'], 
                            $_POST['comment_email'], time(), $_POST['comment_content'], false
                        );
                        $comment->save();
                        pec_redirect(create_blogpost_url($post, false, 'message=comment_created#messages'), 0, false, false);
                    }
                }
            }
        }
    }
}

?>
