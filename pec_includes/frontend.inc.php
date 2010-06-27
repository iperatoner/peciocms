<?php

/**
 * pec_includes/frontend.inc.php - Functions for index.php
 * 
 * Defines frontend helper functions used in the main index.php
 * 
 * LICENSE: This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU General Public License as published by the 
 * Free Software Foundation, either version 3 of the License, or (at your option) 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License 
 * for more details. You should have received a copy of the 
 * GNU General Public License along with this program. 
 * If not, see <http://www.gnu.org/licenses/>.
 * 
 * @package		peciocms
 * @subpackage	pec_includes
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */


/**
 * Fills the `$current_page`-array with proper data
 * 
 * @param	array $current_page Prepopulated array that holds data about the currently being viewed page
 * @return	array The filled `$current_page`-array
 */
function fill_current_page_array($current_page) {
	
	/**
	 * Fills the `$current_page`-array with proper data, when we're on the homepage. We do this, because we don't want to repeat ourself.
	 * 
	 * @param	array $current_page Prepopulated array that holds data about the currently being viewed page
	 * @return	array The `$current_page`-array that now is filled with data about the home page
	 */
	function process_home_data($current_page) {
        // menupoints are using numbers as target type 
        $current_page['target']['type'] = MENUPOINT_TARGET_HOME;
        
        $current_page['view']['main'] = SITE_VIEW_HOME;
        $current_page['view']['sub'] = SITE_VIEW_HOME;
        
        if ($pec_settings->get_blog_onstart()) {
            if (isset($_GET['category'])) {
                $current_page['view']['sub'] = SITE_VIEW_BLOGCATEGORY;
                $current_page['view']['data'] = $_GET['category'];
            }
            elseif (isset($_GET['tag'])) {
                $current_page['view']['sub'] = SITE_VIEW_BLOGTAG;
                $current_page['view']['data'] = $_GET['tag'];
            }
            elseif (isset($_GET['day']) || isset($_GET['month']) || isset($_GET['year'])) {
                $current_page['view']['sub'] = SITE_VIEW_BLOGARCHIVE;
            }
            else {
                $current_page['view']['sub'] = SITE_VIEW_BLOG;
            }
        }
	    
		return $current_page;
	}
	
	
	if (isset($_GET['target']) && !empty($_GET['target'])) {
		
	    // ARTICLE
	    
	    if ($_GET['target'] == QUERY_TARGET_ARTICLE) {
	        $current_page['target']['type'] = MENUPOINT_TARGET_ARTICLE;
	        $current_page['target']['data'] = $_GET['id'];
	           
	        $current_page['view']['main'] = SITE_VIEW_ARTICLE;
	        $current_page['view']['sub'] = SITE_VIEW_ARTICLE;
	        $current_page['view']['data'] = $_GET['id'];
	    }
	    
	    
	    // SEARCH
	    
	    elseif ($_GET['target'] == QUERY_TARGET_SEARCH) {
	        $current_page['target']['type'] = MENUPOINT_TARGET_ARTICLE;
	          
	        $current_page['view']['main'] = SITE_VIEW_SEARCH;
	        $current_page['view']['sub'] = SITE_VIEW_SEARCH;
	        $current_page['view']['data'] = $_GET['term'];
	    }
	    
	    
	    // BLOG
	    
	    elseif ($_GET['target'] == QUERY_TARGET_BLOG) {
	        $current_page['target']['type'] = MENUPOINT_TARGET_BLOG;
	        
	        if (isset($_GET['post_id'])) {
	            $current_page['view']['main'] = SITE_VIEW_BLOGPOST;
	            $current_page['view']['sub'] = SITE_VIEW_BLOGPOST;            
	            $current_page['view']['data'] = $_GET['post_id'];
	        }
	        elseif (isset($_GET['category'])) {
	            $current_page['view']['main'] = SITE_VIEW_BLOGCATEGORY;
	            $current_page['view']['sub'] = SITE_VIEW_BLOGCATEGORY;            
	            $current_page['view']['data'] = $_GET['category'];
	        }
	        elseif (isset($_GET['tag'])) {
	            $current_page['view']['main'] = SITE_VIEW_BLOGTAG;
	            $current_page['view']['sub'] = SITE_VIEW_BLOGTAG;            
	            $current_page['view']['data'] = $_GET['tag'];
	        }
	        elseif (isset($_GET['day']) || isset($_GET['month']) || isset($_GET['year'])) {
	            $current_page['view']['main'] = SITE_VIEW_BLOGARCHIVE;
	            $current_page['view']['sub'] = SITE_VIEW_BLOGARCHIVE;
	            /*
	             * HINT: We dont put the archive data from the query into view-data
	             * because that is better done in the blog controller later
	             * 
	             */
	        }
	        else {
	            $current_page['view']['main'] = SITE_VIEW_BLOG;
	            $current_page['view']['sub'] = SITE_VIEW_BLOG;
	            /*
	             * HINT: We dont put the current blog page from the query into view-data
	             * because ['view']['data'] is already reserved for category/tag/post-IDs etc.
	             * 
	             */
	        }
	    }
	    
	    
	    // HOME
	    
	    else {
	    	// because we don't want to repeat ourselves, we're calling a function
	    	$current_page = process_home_data($current_page);
	    }
	}
	    
	
	// HOME
	
	else {
    	// because we don't want to repeat ourselves, we're calling a function
	    $current_page = process_home_data($current_page);
	}
   
    return $current_page;
}

?>