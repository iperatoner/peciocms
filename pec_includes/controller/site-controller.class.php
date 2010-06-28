<?php

/**
 * pec_includes/controller/site-controller.class.php - Handling of the current view
 * 
 * Handles the current view, its data and the resource generator.
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
 * @subpackage	pec_includes.controller
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */

/**
 * PecSiteController handles the data of the query string and 
 * generates the correct content for the current view using the PecResourceGenerator.
 */
class PecSiteController {
    
	/**
	 * @var PecSetting $settings Pecio settings object.
	 */
    private $settings;
    
    
	/**
	 * @var PecDatabase	$database Pecio's database object.
	 */
    private $database;
    
    
	/**
	 * @var PecLocale	$localization Pecio's localization object.
	 */
    private $localization;
	
    
	/**
	 * @var array			$plugins All available plugins (meta data of them).
	 */
    private $plugins;
    
    
	/**
	 * @var	string			$template_file, Current template file, depends on the current site view.
	 */
    private $template_file;
    
    
	/**
	 * @var PecTemplateResource		$template_resource, Template resource object which holds easy to access data/objects related to the current view for templates.
	 */
    private $template_resource;
    
    
    // TODO: dont forget to think about how to document array keys. perhaps: @var array [articles_on_start=>array|false, article=>PecArticle|false] ...    
	/**
	 * @var array	$current_page Holds all the relevant low level data, e.g. target-/view-type, belonging to the currently being viewed page.
	 */    
    private $current_page = array(
		'target' => array(
			'type' => MENUPOINT_TARGET_HOME,
			'data' => '-'
		),
		'view' => array(
			'main' => SITE_VIEW_HOME,
			'sub' => '',
			'data' => ''
		)
	);
    
	
    /**
     * @static
     * @var array The available views and their proper template files.
     */
    static $view_templates = array(
        SITE_VIEW_HOME => 'home.php',
        
        SITE_VIEW_ARTICLE => 'article.php',        
        SITE_VIEW_SEARCH => 'article.php',    
        SITE_VIEW_404 => 'article.php',
        
        SITE_VIEW_BLOG => 'blog.php',
        SITE_VIEW_BLOGCATEGORY => 'blog.php',
        SITE_VIEW_BLOGTAG => 'blog.php',
        SITE_VIEW_BLOGARCHIVE => 'blog.php',
        
        SITE_VIEW_BLOGPOST => 'post.php'
    );
    
    
    /**
     * Creates a PecSiteController instance.
     * 
     * @param	string|boolean $query_target The target name that is provided by the query string. Or false if not provided.
     */
    function __construct($query_target) {
    	global $pec_database, $pec_settings, $pec_localization;
        
    	// basic pecio objects
    	$this->database = $pec_database;
        $this->settings = $pec_settings;
        $this->localization = $pec_localization;

        // fill the `$current_page`-array with great data about our current view :)
        $this->current_page = $this->grab_view_data($query_target);
        
        $this->template = $this->settings->get_template();
        $this->template_file = self::$view_templates[ $this->current_page['view']['main'] ];
        
        $this->template_resource = new PecTemplateResource($this->settings, $this->template, $this->current_page);
    }

    	
	/**
	 * Fills the prepopulated `$this->current_page`-array with data that belongs to the current view.
	 * 
	 * @param	string|boolean  $query_target Target that was given by the query string. If not, that's false.
	 */
    private static function grab_view_data($query_target) {
    	// Just doing that, because `$this->current_page` is a quite long var-name :D
    	$cp = $this->current_page;
    	
		switch ($query_target) {
			
			case QUERY_TARGET_ARTICLE:
		        $cp['target']['type'] = MENUPOINT_TARGET_ARTICLE;
		        $cp['target']['data'] = $_GET['id'];
		           
		        $cp['view']['main'] = $cp['view']['sub'] = SITE_VIEW_ARTICLE;
		        $cp['view']['data'] = $_GET['id'];
				break;
		    
			case QUERY_TARGET_SEARCH:
		        $cp['target']['type'] = MENUPOINT_TARGET_ARTICLE;
		          
		        $cp['view']['main'] = $cp['view']['sub'] = SITE_VIEW_SEARCH;
		        $cp['view']['data'] = $_GET['term'];
		    	break;
		    
			case QUERY_TARGET_BLOG:
		        $cp['target']['type'] = MENUPOINT_TARGET_BLOG;
		        
		        if (isset($_GET['post_id'])) {
		            $cp['view']['main'] = $cp['view']['sub'] = SITE_VIEW_BLOGPOST;
		            $cp['view']['data'] = $_GET['post_id'];
		        }
		        elseif (isset($_GET['category'])) {
		            $cp['view']['main'] = $cp['view']['sub'] = SITE_VIEW_BLOGCATEGORY;          
		            $cp['view']['data'] = $_GET['category'];
		        }
		        elseif (isset($_GET['tag'])) {
		            $cp['view']['main'] = $cp['view']['sub'] = SITE_VIEW_BLOGTAG;            
		            $cp['view']['data'] = $_GET['tag'];
		        }
		        elseif (isset($_GET['day']) || isset($_GET['month']) || isset($_GET['year'])) {
		            $cp['view']['main'] = $cp['view']['sub'] = SITE_VIEW_BLOGARCHIVE;
		            # HINT: We dont put the archive data into view-data because that is better done in the blog controller later
		        }
		        else {
		            $cp['view']['main'] = $cp['view']['sub'] = SITE_VIEW_BLOG;
		            # HINT: We dont put the blog page into view-data because ['view']['data'] is reserved for category/tag/post-IDs etc.
		        }
		    	break;
		    
		    // HOME
			default:
		        $cp['target']['type'] = MENUPOINT_TARGET_HOME;
		        
		        $cp['view']['main'] = SITE_VIEW_HOME;
		        $cp['view']['sub'] = SITE_VIEW_HOME;
		        
		        if ($this->settings->get_blog_onstart()) {
		            if (isset($_GET['category'])) {
		                $cp['view']['sub'] = SITE_VIEW_BLOGCATEGORY;
		                $cp['view']['data'] = $_GET['category'];
		            }
		            elseif (isset($_GET['tag'])) {
		                $cp['view']['sub'] = SITE_VIEW_BLOGTAG;
		                $cp['view']['data'] = $_GET['tag'];
		            }
		            elseif (isset($_GET['day']) || isset($_GET['month']) || isset($_GET['year'])) {
		                $cp['view']['sub'] = SITE_VIEW_BLOGARCHIVE;
		            }
		            else {
		                $cp['view']['sub'] = SITE_VIEW_BLOG;
		            }
		        }
		    	break;
		}
		
		return $cp;
    }
    
    
	/**
	 * Adds a handler that shall be applied to the template resource.
	 * 
	 * @param	mixed  $handler The handler object that should be applied
	 */
    public function add_handler($handler) {
    	$handler->set_current_page($this->current_page);
    	$this->handlers[] = $handler;
    }
    
    
	/**
	 * Apply all available handlers to the template resource.
	 */
    public function apply_handlers() {
    	foreach ($this->handlers as $handler) {
    		$this->template_resource = $handler->update_template_resource($this->template_resource);
    	}
    }
    
    
    /**
	 * Loads the proper objects (depending on the current site view) into the correct class vars.
	 */
    private function load_object() {
        if ($this->site_view == SITE_VIEW_HOME) {
            $this->articles_on_start = PecArticle::load('onstart', 1, '', true);
                
            $this->template = $this->articles_on_start[0]->get_template();
            $this->template_resource->set('template', $this->template);
        }
        
        // loading the currently active object, depending on the active view
        if ($this->site_view == SITE_VIEW_ARTICLE) {
            $by = $this->settings->get_load_by();
            
            // check if it exists
            if (PecArticle::exists($by, $this->current_target_data)) {
                $this->current_article = PecArticle::load($by, $this->current_target_data);
                $this->current_target_data = $this->current_article->get_id();
                
            	$this->template = $this->current_article->get_template();
            	$this->template_resource->set('template', $this->template);
            }
            else {
                $this->site_view = SITE_VIEW_404;
                $this->sub_site_view = SITE_VIEW_404;
                $this->template_resource->set('site_view', SITE_VIEW_404);
                $this->template_resource->set('sub_site_view', SITE_VIEW_404);
            }
        }
        elseif ($this->site_view == SITE_VIEW_BLOGPOST) {
            $by = $this->settings->get_load_by();
            
            // check if it exists
            if (PecBlogPost::exists($by, $this->current_view_data)) {
                $this->current_blogpost = PecBlogPost::load($by, $this->current_view_data);
                $this->current_view_data = $this->current_blogpost->get_id();
            }
            else {
                $this->site_view = SITE_VIEW_404;
                $this->sub_site_view = SITE_VIEW_404;
                $this->template_resource->set('site_view', SITE_VIEW_404);
                $this->template_resource->set('sub_site_view', SITE_VIEW_404);
            }
        }
        elseif ($this->site_view == SITE_VIEW_BLOGCATEGORY || $this->sub_site_view == SITE_VIEW_BLOGCATEGORY) {
            $by = $this->settings->get_load_by();
            
            // check if it exists
            if (PecBlogCategory::exists($by, $this->current_view_data)) {
                $this->current_blogcategory = PecBlogCategory::load($by, $this->current_view_data);
                $this->current_view_data = $this->current_blogcategory->get_id();
            }
            else {
                $this->site_view = SITE_VIEW_404;
                $this->sub_site_view = SITE_VIEW_404;
                $this->template_resource->set('site_view', SITE_VIEW_404);
                $this->template_resource->set('sub_site_view', SITE_VIEW_404);
            }
        }
        elseif ($this->site_view == SITE_VIEW_BLOGTAG || $this->sub_site_view == SITE_VIEW_BLOGTAG) {
            $by = $this->settings->get_load_by();
            
            // check if it exists
            if (PecBlogTag::exists($by, $this->current_view_data)) {
                $this->current_blogtag = PecBlogTag::load($by, $this->current_view_data);
                $this->current_view_data = $this->current_blogtag->get_id();
            }
            else {
                $this->site_view = SITE_VIEW_404;
                $this->sub_site_view = SITE_VIEW_404;
                $this->template_resource->set('site_view', SITE_VIEW_404);
                $this->template_resource->set('sub_site_view', SITE_VIEW_404);
            }
        }
    }
    
    /**
	 * Prepares the template data for the current view by creating the menus, 
	 * sidebar texts and additional objects depending on the current site view and 
	 * puts them into the template resource object.
	 */
    public function prepare_view() {
        $plugin_head_data = $this->resource_generator->get_plugin_head_data();
        
        $complete_menu = $this->resource_generator->generate_menu();
        $root_menu = $this->resource_generator->generate_menu(0, 0, false);
        $sub_menu = $this->resource_generator->generate_current_submenus();
        
        $sidebar_texts = $this->resource_generator->parse_plugins($this->resource_generator->generate_texts());
        $sidebar_links = $this->resource_generator->generate_links();
        
        $search_form = PecSearch::get_search_form();
        
        $this->template_resource->set('plugin_head_data', $plugin_head_data);
        $this->template_resource->set('complete_menu', $complete_menu);
        $this->template_resource->set('root_menu', $root_menu);
        $this->template_resource->set('sub_menu', $sub_menu);
        $this->template_resource->set('sidebar_texts', $sidebar_texts);
        $this->template_resource->set('sidebar_links', $sidebar_links);
        $this->template_resource->set('search_form', $search_form);
        
        // used to load the posts for the current page of the blog
        $current_blog_page = isset($_GET['p']) && !empty($_GET['p']) ? (int)$_GET['p'] : 1;
        
        // HOME
        if ($this->site_view == SITE_VIEW_HOME) {
            $this->articles_on_start = $this->resource_generator->parse_plugins_of('articles', $this->articles_on_start);
            $this->template_resource->set('articles', $this->articles_on_start);
        }        
        
        // ARTICLE
        if ($this->site_view == SITE_VIEW_ARTICLE) {
            $this->current_article = $this->resource_generator->parse_plugins_of('article', $this->current_article);
            $this->template_resource->set('article', $this->current_article);
        }
        
        // SEARCH
        elseif ($this->site_view == SITE_VIEW_SEARCH) {
            $search = new PecSearch($this->current_view_data);
            $search->do_search();
            $article = $search->get();    
            
            $this->template_resource->set('article', $article);
        }
        
        // BLOG
        elseif ($this->site_view == SITE_VIEW_BLOG || $this->sub_site_view == SITE_VIEW_BLOG) {
            $blogposts = PecBlogPost::load('status', 1, "ORDER BY post_timestamp DESC", $current_blog_page, true);
            $blogposts = $this->resource_generator->parse_plugins_of('blogposts', $blogposts);
            
            // Not sure if we need that
           	#$blogcomments = PecBlogComment::load('post', $this->current_blogpost, 'ORDER BY comment_timestamp ASC');
            
            // all available posts are needed for older / newer posts link, not only those from the current page
            $available_blogposts = PecBlogPost::load('status', 1, false, false, true);
        	
            $this->template_resource->set('blogposts', $blogposts);
            
            // Not sure if we need that
            #$this->template_resource->set('blogcomments', $blogcomments);
        }
        
        // POST
        elseif ($this->site_view == SITE_VIEW_BLOGPOST) {        	
            $this->resource_generator->process_new_comment($this->current_blogpost);
            $this->current_blogpost = $this->resource_generator->parse_plugins_of('blogpost', $this->current_blogpost);
            
            // Not sure if we need that
           	#$blogcomments = PecBlogComment::load('post', $this->current_blogpost, 'ORDER BY comment_timestamp ASC');
           	
            $this->template_resource->set('blogpost', $this->current_blogpost);
            
            // Not sure if we need that
            #$this->template_resource->set('blogcomments', $blogcomments);
        }
        
        // CATEGORY
        elseif ($this->site_view == SITE_VIEW_BLOGCATEGORY || $this->sub_site_view == SITE_VIEW_BLOGCATEGORY) {
            $blogposts = PecBlogPost::load('category', $this->current_blogcategory, "WHERE post_status='1' ORDER BY post_timestamp DESC", $current_blog_page, true);
            $blogposts = $this->resource_generator->parse_plugins_of('blogposts', $blogposts);
            
            // all available posts are needed for older / newer posts link, not only those from the current page
            $available_blogposts = PecBlogPost::load('category', $this->current_blogcategory, "WHERE post_status='1'", false, true);
        	
            $this->template_resource->set('blogposts', $blogposts);
            $this->template_resource->set('blogcategory', $this->current_blogcategory);
        }
        
        // TAG
        elseif ($this->site_view == SITE_VIEW_BLOGTAG || $this->sub_site_view == SITE_VIEW_BLOGTAG) {
            $blogposts = PecBlogPost::load('tag', $this->current_blogtag, "WHERE post_status='1' ORDER BY post_timestamp DESC", $current_blog_page, true);
            $blogposts = $this->resource_generator->parse_plugins_of('blogposts', $blogposts);
            
            // all available posts are needed for older / newer posts link, not only those from the current page
            $available_blogposts = PecBlogPost::load('tag', $this->current_blogtag, "WHERE post_status='1'", false, true);
            
            $this->template_resource->set('blogposts', $blogposts);
            $this->template_resource->set('blogtag', $this->current_blogtag);
        }
        
        // ARCHIVE
        elseif ($this->site_view == SITE_VIEW_BLOGARCHIVE || $this->sub_site_view == SITE_VIEW_BLOGARCHIVE) {
            // default, human and rewrite all have the same query structure for archives
            $where_condition = "WHERE ";
            
            // needed to check if there is the first condition after this WHERE keyword
            $some_condition = false;
            
            $day = false; $month = false; $year = false;
            
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
            
            $blogposts = PecBlogPost::load(0, false, $where_condition . " AND post_status='1' ORDER BY post_timestamp DESC", $current_blog_page, true);
            $blogposts = $this->resource_generator->parse_plugins_of('blogposts', $blogposts);
            
            // all available posts are needed for older / newer posts link, not only those from the current page
            $available_blogposts = PecBlogPost::load(0, false, $where_condition . " AND post_status='1'", false, true);
        	
            $this->template_resource->set('blogarchive_day', $day);
            $this->template_resource->set('blogarchive_month', $month);
            $this->template_resource->set('blogarchive_year', $year);
            
            $this->template_resource->set('blogposts', $blogposts);
        }
        
        // 404
        elseif ($this->site_view == SITE_VIEW_404) {
            $article = new PecArticle(NULL_ID, $this->pec_localization->get('LABEL_404_PAGE_TITLE'), $this->pec_localization->get('LABEL_404_PAGE_CONTENT'), 0);
            $this->template_resource->set('article', $article);
        }
        
        // OLDER / NEWER POSTS LINK
        if ($this->sub_site_view == SITE_VIEW_BLOG || $this->sub_site_view == SITE_VIEW_BLOGCATEGORY || 
        	$this->sub_site_view == SITE_VIEW_BLOGTAG || $this->sub_site_view == SITE_VIEW_BLOGARCHIVE) {
        		
        	$available_blog_pages = $this->resource_generator->get_available_blog_pages($available_blogposts);
        	
        	$older_entries_page = $this->resource_generator->get_older_entries_page($current_blog_page, $available_blog_pages);
        	$newer_entries_page = $this->resource_generator->get_newer_entries_page($current_blog_page, $available_blog_pages);
        	
        	$older_entries_url = $this->resource_generator->get_blog_page_url($older_entries_page);
        	$newer_entries_url = $this->resource_generator->get_blog_page_url($newer_entries_page);
        	
        	$this->template_resource->set('blog_older_entries_page', $older_entries_page);
        	$this->template_resource->set('blog_newer_entries_page', $newer_entries_page);
        	$this->template_resource->set('blog_older_entries_url', $older_entries_url);
        	$this->template_resource->set('blog_newer_entries_url', $newer_entries_url);
        }
    }
    
    /**
	 * Includes the proper template file.
	 */
    public function display() {
    	// here we need to get the canonicalized template path, so that we can include it with `include()`
        $template_path_c = $this->template->get_directory_path() . $this->template_file;
        
        // that's the "normal" path to the template directory
        $template_path = $this->template->get_directory_path(false);
        $this->template_resource->set('template_path', $template_path);
        
        $pecio = $this->template_resource;
        
        include($template_path_c);
    }
}

?>
