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
	 * @var	string			$template_file Current template file, depends on the current site view.
	 */
    private $template_file;
    
    
	/**
	 * @var PecTemplateResource		$template_resource Template resource object which holds easy to access data/objects related to the current view for templates.
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
    	$this->database =& $pec_database;
        $this->settings =& $pec_settings;
        $this->localization =& $pec_localization;

        // fill the `$current_page`-array with great data about our current view :)
        $this->grab_view_data($query_target);
        
        $this->template_file = self::$view_templates[ $this->current_page['view']['main'] ];
        
        $this->template_resource = new PecTemplateResource($this->settings, &$this->current_page);
    }

    	
	/**
	 * Fills the prepopulated `$this->current_page`-array with data that belongs to the current view.
	 * 
	 * @param	string|boolean  $query_target Target that was given by the query string. If not, that's false.
	 */
    private function grab_view_data($query_target) {
    	// Just doing that, because `$this->current_page` is a quite long var-name :D
    	$cp =& $this->current_page;
    	
		switch ($query_target) {
			
			case QUERY_TARGET_ARTICLE:
		        $cp['target']['type'] = MENUPOINT_TARGET_ARTICLE;
		        $cp['target']['data'] = $_GET['id'];
		           
		        $cp['view']['main'] = $cp['view']['sub'] = SITE_VIEW_ARTICLE;
		        $cp['view']['data'] = $_GET['id'];
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
		    
			case QUERY_TARGET_SEARCH:
		        $cp['target']['type'] = MENUPOINT_TARGET_ARTICLE;
		          
		        $cp['view']['main'] = $cp['view']['sub'] = SITE_VIEW_SEARCH;
		        $cp['view']['data'] = $_GET['term'];
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
    }
    
    
	/**
	 * Adds a handler that will be applied to the template resource.
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
    		$handler->apply(&$this->template_resource);
    		if ($handler->check_404()) {
    			$this->set_404();
    		}
    	}
    }
    
    
	/**
	 * Sets current low level view data to 404 and applies a 404 article to the template resource
	 */
    public function set_404() {
    	// change site views
    	$this->current_page['view']['main'] = SITE_VIEW_404;
    	$this->current_page['view']['sub'] = SITE_VIEW_404;
    	
    	// change view template
        $this->template_file = self::$view_templates[ $this->current_page['view']['main'] ];
        
        // create 404 article
        $article = new PecArticle(NULL_ID, $this->localization->get('LABEL_404_PAGE_TITLE'), $this->localization->get('LABEL_404_PAGE_CONTENT'), 0);
        
        // update the template resource
    	$this->template_resource->set('current_page', $this->current_page);
    	$this->template_resource->set('article', $article);
    	
    	// ah, and don't forget to mass update the all handler's current_page var
    	$this->mass_update_current_page();
    }
    
    
	/**
	 * Update all current_page arrays that are in the handlers
	 */
    public function mass_update_current_page() {
    	foreach ($this->handlers as $handler) {
    		$handler->set_current_page($this->current_page);
    	}
    }
    
    
    /**
	 * Includes the proper template file.
	 */
    public function display() {
        
        function pec_display($pecio, $template_path_c) {
        	include($template_path_c);
        }
        
    	// here we need to get the canonicalized template path, so that we can include it with `include()`
        $template_path_c = $this->template_resource->get('template')->get_directory_path();
        
        // that's the "normal" path to the template directory
        $template_path = $this->template_resource->get('template')->get_directory_path(false);
        
        $this->template_resource->set('template_path', $template_path);
        $this->template_resource->set('template_path_c', $template_path_c);
        
        $pecio = $this->template_resource;
        pec_display($pecio, $template_path_c . $this->template_file);
    }
}

?>
