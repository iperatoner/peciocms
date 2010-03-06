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
 * @version		2.0.1
 * @link		http://pecio-cms.com
 */

/**
 * PecSiteController handles the data of the query string and 
 * generates the correct content for the current view using the PecResourceGenerator.
 */
class PecSiteController {
    
	/**
	 * @var integer 		$current_target_type, Current target that is given in the query string and used for menupoints.
	 * @var string			$current_target_data, Target data of the current target that is given in the query string and used for menupoints.
	 * @var string			$site_view, Current site view.
	 * @var string			$sub_site_view, On start page may be a sub site view, e.g. blogcategory, if the blog is assigned to it.
	 * @var string			$current_view_data, The data that belongs to the current view. It's usually given somewhere in the query string. If site_view is blogcategory, this would be the category name/id.
	 * @var array			$articles_on_start, All the articles that are assigned to the start page. Only set if current view is the start page.
	 * @var PecArticle		$current_article, Current article. Only set if current site view is article.
	 * @var PecBlogPost		$current_blogpost, Current blogpost. Only set if current site view is blogpost.
	 * @var PecBlogCategory	$current_blogcategory, Current blogcategory. Only set if current site view is blogcategory.
	 * @var PecBlogTag		$current_blogtag, Current blogtag. Only set if current site view is blogtag.
	 * @var array			$plugins, All available plugins (meta data of them).
	 * @var	string			$template_file, Current template file, depends on the current site view.
	 * 
	 * @var PecResourceGenerator	$resource_generator, The resource generator that creates all the resources for the templates.
	 * @var PecTemplateResource		$template_resource, Template resource object which holds the generated resources.
	 * @var PecDatabase				$database, Pecio's database object.
	 * @var PecSetting				$settings, Pecio's settings.
	 */
    private $current_target_type, $current_target_data, $site_view, $sub_site_view, $current_view_data, $articles_on_start, 
            $current_article, $current_blogpost, $current_blogcategory, $current_blogtag, $plugins, 
            $template_file, $resource_generator, $template_resource, $database, $settings;
    
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
    
    function __construct($current_target_type, $current_target_data, $site_view, $sub_site_view, $current_view_data) {
        global $pec_database, $pec_settings, $pec_localization;
        $this->database = $pec_database;
        $this->settings = $pec_settings;        
        $this->template = PecTemplate::exists('id', $this->settings->get_template_id()) ? PecTemplate::load('id', $this->settings->get_template_id()) : PecTemplate::load('title', DEFAULT_TEMPLATE_NAME);
        
        $this->current_target_type = $current_target_type;
        $this->current_target_data = $current_target_data;
        $this->site_view = $site_view;
        $this->sub_site_view = $sub_site_view;
        $this->current_view_data = $current_view_data;
        
        $this->articles_on_start = false;
        $this->current_article = false;
        $this->current_blogpost = false;
        $this->current_blogcategory = false;
        $this->current_blogtag = false;
        
        $this->template_resource = new PecTemplateResource($this->settings, $this->template, $this->site_view, $this->sub_site_view);        

        $this->load_object();
        $this->plugins = PecPlugin::load();
        
        $this->template_file = self::$view_templates[$this->site_view];
        
        $this->resource_generator = new PecResourceGenerator(
        	$this->current_target_type, $this->current_target_data, $this->site_view, $this->sub_site_view, 
            $this->current_view_data, $this->articles_on_start, $this->current_article, $this->current_blogpost,
            $this->current_blogcategory, $this->current_blogtag, $this->plugins
        );
        
        $this->pec_localization = $pec_localization;
    }
    
    /**
	 * Loads the proper objects depending on the current site view into the class vars.
	 */
    private function load_object() {
        if ($this->site_view == SITE_VIEW_HOME) {
            $this->articles_on_start = PecArticle::load('onstart', 1, '', true);
        }
        
        // loading the currently active object, depending on the active view
        if ($this->site_view == SITE_VIEW_ARTICLE) {
            $by = $this->settings->get_load_by();
            
            // check if it exists
            if (PecArticle::exists($by, $this->current_target_data)) {
                $this->current_article = PecArticle::load($by, $this->current_target_data);
                $this->current_target_data = $this->current_article->get_id();
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
            
            // all available posts are needed for older / newer posts link, not only those from the current page
            $available_blogposts = PecBlogPost::load('status', 1, false, false, true);
        	
            $this->template_resource->set('blogposts', $blogposts);
        }
        
        // POST
        elseif ($this->site_view == SITE_VIEW_BLOGPOST) {        	
            $this->resource_generator->process_new_comment($this->current_blogpost);
            $this->current_blogpost = $this->resource_generator->parse_plugins_of('blogpost', $this->current_blogpost);
           	$blogcomments = PecBlogComment::load('post', $this->current_blogpost);
           	
            $this->template_resource->set('blogpost', $this->current_blogpost);
            $this->template_resource->set('blogcomments', $blogcomments);
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
        $template_path = TEMPLATE_PATH . $this->template->get_directory_name() . '/' . $this->template_file;
        $pecio = $this->template_resource;
        
        include($template_path);
    }
}

?>
