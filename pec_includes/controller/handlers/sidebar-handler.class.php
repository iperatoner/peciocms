<?php

/**
 * pec_includes/controller/handlers/sidebar-handler.class.php - Contains the sidebar handler class
 * 
 * Contains the PecSidebarHandler which is a class for handling and applying texts and links to the TemplateResource 
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
 * PecSidebarHandler is the class for handling and applying texts and links to the TemplateResource .
 */
class PecSidebarHandler extends PecAbstractHandler {
	
    /**
     * Creates a PecSidebarHandler instance.
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
    	$current_article = $template_resource->get('article');
    	$articles_on_start = $template_resource->get('articles');
    	
    	// Generate sidebar objects
    	$texts = $this->generate_texts($current_article, $articles_on_start);
    	$link_categories = $this->generate_link_categories($current_article, $articles_on_start);
    	
    	// Generate HTML strings from those objects
    	$texts_html = $this->generate_texts_html($texts);
    	$links_html = $this->generate_links_html($link_categories);
    	
    	// Don't forget the search form!
        $search_form = PecSearch::get_search_form();
    	
    	// Finally apply all that generated stuff to the template resource
    	$template_resource->set('sidebar_text_objects', $texts);
    	$template_resource->set('sidebar_texts', $texts_html);
    	
    	$template_resource->set('sidebar_linkcategory_objects', $link_categories);
    	$template_resource->set('sidebar_links', $links_html);
    	
    	$template_resource->set('search_form', $search_form);
    	
    	#return $template_resource;
    }
    
        
    /**
	 * Generates the sidebar text objects which are allowed to be displayed on the current site view.
	 * 
	 * @return	array		PecSidebarText objects
	 */
    public function generate_texts($current_article, $articles_on_start) {
        
        $view_main =& $this->current_page['view']['main'];
        
        // all texts that are allowed to be displayed on the currently active page/content/article/blog/...
        $all_visible_texts = array();
        
        // an array that contains arrays of PecSidebarText objects
        $text_arrays = array();
        
        // EVERYWHERE texts
        $text_arrays[] = PecSidebarText::load('visibility', TEXT_VISIBILITY_EVERYWHERE, '', true);
        
        // ARTICLE / SEARCH / 404 texts
        if ($view_main == SITE_VIEW_ARTICLE || 
        	$view_main == SITE_VIEW_SEARCH || 
            $view_main == SITE_VIEW_404) {
            	
            $text_arrays[] = PecSidebarText::load('visibility', TEXT_VISIBILITY_ON_ALL_ARTICLES, '', true);
            
            // only load texts of specific article, if that is one
            if ($view_main == SITE_VIEW_ARTICLE) {
                $text_arrays[] = PecSidebarText::load('article', $current_article, '', true);
            }
        }
        
        // HOME texts
        elseif ($view_main == SITE_VIEW_HOME) {
            $text_arrays[] = PecSidebarText::load('visibility', TEXT_VISIBILITY_ON_ALL_ARTICLES, '', true);
            
            if ($this->settings->get_blog_onstart()) {
                $text_arrays[] = PecSidebarText::load('visibility', TEXT_VISIBILITY_ON_BLOG, '', true);
            }

            // loading texts that are assigned to the start articles
            $text_loader_code = 'PecSidebarText::load("article", {%VAR%}, "", true);';
            $texts_on_start_articles = $this->load_sidebar_objects_for_articles($articles_on_start, $text_loader_code);
            
            $text_arrays[] = $texts_on_start_articles;
        }
        
        // BLOG texts
        elseif ($view_main == SITE_VIEW_BLOG ||
        		$view_main == SITE_VIEW_BLOGPOST || 
                $view_main == SITE_VIEW_BLOGCATEGORY ||
                $view_main == SITE_VIEW_BLOGTAG  ||
                $view_main == SITE_VIEW_BLOGARCHIVE) {
                	
            $text_arrays[] = PecSidebarText::load('visibility', TEXT_VISIBILITY_ON_BLOG, '', true);
        }
        
        // merging all text-arrays together
        foreach ($text_arrays as $texts) {
            $all_visible_texts = array_merge($all_visible_texts, $texts);
        }
        
        // sort the mixed texts properly
		$all_visible_texts = $this->sort_sidebar_objects($all_visible_texts);
        
        return $all_visible_texts;
    }
    
    
    /**
	 * Generates the sidebar links which are allowed to be displayed on the current site view.
	 * 
	 * @return	array		PecSidebarLinkCat objects
	 */
    public function generate_link_categories($current_article, $articles_on_start) {
        
        $view_main =& $this->current_page['view']['main'];
        
        // all link categories that are allowed to be displayed on the currently active page/content/article/blog/...
        $all_visible_categories = array();
        
        // an array that contains arrays of PecSidebarLinkCat objects
        $linkcategory_arrays = array();
        
        // EVERYWHERE link-categories
        $linkcategory_arrays[] = PecSidebarLinkCat::load('visibility', TEXT_VISIBILITY_EVERYWHERE, '', true);
        
        // ARTICLE / SEARCH / 404 link-categories
        if ($view_main == SITE_VIEW_ARTICLE || 
        	$view_main == SITE_VIEW_SEARCH || 
            $view_main == SITE_VIEW_404) {
            	
            $linkcategory_arrays[] = PecSidebarLinkCat::load('visibility', TEXT_VISIBILITY_ON_ALL_ARTICLES, '', true);
            
            // only load link-categories of specific article, if that is one
            if ($view_main == SITE_VIEW_ARTICLE) {
                $linkcategory_arrays[] = PecSidebarLinkCat::load('article', $current_article, '', true);
            }
        }
        
        // HOME link-categories
        elseif ($view_main == SITE_VIEW_HOME) {
            $linkcategory_arrays[] = PecSidebarLinkCat::load('visibility', TEXT_VISIBILITY_ON_ALL_ARTICLES, '', true);
            
            if ($this->settings->get_blog_onstart()) {
                $linkcategory_arrays[] = PecSidebarLinkCat::load('visibility', TEXT_VISIBILITY_ON_BLOG, '', true);
            }
            
            // categories that are on articles which are assgined to the start page
            $linkcategory_loader_code = 'PecSidebarLinkCat::load("article", {%VAR%}, "", true);';
            $linkcategories_on_start_articles = $this->load_sidebar_objects_for_articles($articles_on_start, $linkcategory_loader_code);
            
            $linkcategory_arrays[] = $linkcategories_on_start_articles;
        }
        
        // BLOG link-categories
        elseif ($view_main == SITE_VIEW_BLOG ||
        		$view_main == SITE_VIEW_BLOGPOST || 
                $view_main == SITE_VIEW_BLOGCATEGORY ||
                $view_main == SITE_VIEW_BLOGTAG ||
                $view_main == SITE_VIEW_BLOGARCHIVE) {
                	
            $linkcategory_arrays[] = PecSidebarLinkCat::load('visibility', TEXT_VISIBILITY_ON_BLOG, '', true);
        }
        
        // merging all linkcategory-arrays together
        foreach ($linkcategory_arrays as $linkcategories) {
            $all_visible_categories = array_merge($all_visible_categories, $linkcategories);
        }
        
        $all_visible_categories = $this->sort_sidebar_objects($all_visible_categories);
        
        return $all_visible_categories;
    }

    
    /**
	 * Loads all sidebar objects that are specificly assigned to the given articles
	 * 
	 * @param	array		$articles Article objects to use
	 * @param	string		$object_loader_code The PHP code to execute for loading a new sidebar object 
	 * @return	array		Sidebar objects that are assigned to the given articles
	 */
    public function load_sidebar_objects_for_articles($articles, $object_loader_code) {
    	// objects that are on the given articles
    	$objects = array();

    	if (count($articles) > 0) {
	    	// replacing {%VAR%} with the article-variable name we will use inside the foreach-loop
	    	$object_loader_code = str_replace('{%VAR%}', '$a', $object_loader_code);
	    	
	        // object ids that are already added to the array (because some objects might be assigned to multiple articles)
	    	$objects_already_in_array = array();
	    	
	    	foreach ($articles as $a) {
	            eval('$objects_for_article = ' . $object_loader_code);
	            
	            // removing duplicates from this array (of this article)
	            foreach ($objects_for_article as $key => $o) {
	            	
	            	// if this object was already added by another article, remove it
	                if (in_array($o->get_id(), $objects_already_in_array)) {
	                    unset($objects_for_article[$key]);
	                }
	                else {
	                    $objects_already_in_array[] = $o->get_id();
	                }
	            }
	                
	            $objects = array_merge($objects, $objects_for_article);
	        }
		}
        
        return $objects;
    }
    
    
    /**
	 * Sorts mixed sidebar objects (link categories or texts). Only works for objects that have a method `get_sort`
	 * 
	 * @param	array		$objects Mixed objects that need to get sorted
	 * @return	array		Sorted sidebar objects (most probably PecSidebarLinkCat or PecSidebarText instances)
	 */
    public function sort_sidebar_objects($objects) {
        // sort the (probably mixed) texts or link categories
        $biggest_sort_number = 1;
        $sorted_objects = array();
        
		// find out the biggest sort number
        foreach ($objects as $o) {
        	$sort = $o->get_sort();
            if ($sort > $biggest_sort_number) {
                $biggest_sort_number = $sort;
            }
        }
        
        // now sort them by checking each sort step
        for ($i=1; $i<=$biggest_sort_number; ++$i) {
            foreach ($objects as $o) {
                if ($o->get_sort() == $i) {
                    $sorted_objects[] = $o;
                }
            }
        }
        
        return $sorted_objects;
    }
    
    
    /**
	 * Generates an html string for the given sidebar texts
	 * 
	 * @param	array		$texts PecSidebarText objects
	 * @return	string		HTML layout including all those sidebar texts
	 */
    public function generate_texts_html($texts) {
        $text_template = get_intern_template(SIDEBARTEXT_TPL_FILE);
        
        // the html result of all texts 
        $texts_html = '';
        
        foreach ($texts as $t) {
            $html = str_replace('{%ID%}', $t->get_id(), $text_template);
            $html = str_replace('{%TITLE%}', $t->get_title(), $html);
            $html = str_replace('{%CONTENT%}', $t->get_content(), $html);
            
            $texts_html .= $html;
        }
        
        return $texts_html;
    }
    
    
    /**
	 * Generates an html string for the given sidebar link categories
	 * 
	 * @param	array		$link_categories PecSidebarLinkCat objects
	 * @return	string		HTML layout including all those sidebar links and categories
	 */
    public function generate_links_html($link_categories) {
        $linkcategory_template = get_intern_template(SIDEBARLINKCAT_TPL_FILE);
        $link_template = get_intern_template(SIDEBARLINK_TPL_FILE);        
        
        // the html result of all links 
        $links_html = '';
        
        foreach ($link_categories as $lc) {
            $links = PecSidebarLink::load('cat', $lc, 'ORDER BY link_sort ASC');
            $link_list_items = '';
            
            foreach ($links as $l) {
                $item = str_replace('{%URL%}', $l->get_url(), $link_template);
                $item = str_replace('{%LINK_NAME%}', $l->get_name(), $item);
                $link_list_items .= $item;
            }
            
            $html = str_replace('{%ID%}', $lc->get_id(), $linkcategory_template);
            $html = str_replace('{%TITLE%}', $lc->get_title(), $html);
            $html = str_replace('{%LINKS%}', $link_list_items, $html);
            
            $links_html .= $html;
        }
        
        return $links_html;
    }
}

?>
