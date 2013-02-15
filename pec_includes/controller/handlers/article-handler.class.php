<?php

/**
 * pec_includes/controller/handlers/article-handler.class.php - Contain the article handler class
 * 
 * Contains the PecArticleHandler which is a class for handling and applying articles to the TemplateResource
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
 * PecArticleHandler is the class for handling and applying articles to the TemplateResource.
 */
class PecArticleHandler extends PecAbstractHandler {
	
    /**
     * Creates a PecArticleHandler instance.
     */
    function __construct() {
    	parent::__construct();
    }
    
    
    /**
     * May update the PecTemplateResource with new data (e.g. the current article etc.)
     * 
     * @param	PecTemplateResource $template_resource Holds a lot of data (e.g. objects, articles, etc.) related to the current view
     * @return	array The updated PecTemplateResource
     */
    public function apply($template_resource) {
    	
    	switch ($this->current_page['view']['main']) {
    		
    		case SITE_VIEW_HOME:
	            $articles_on_start = PecArticle::load('onstart', 1, '', true);
	            
	            if (count($articles_on_start) > 0) {
	            	$template = $articles_on_start[0]->get_template();
	            	
		            $template_resource->set('template', $template);
	            	$template_resource->set('articles', $articles_on_start);
	            }
	            else {
	            	$template_resource->set('articles', array());
	            }
	            
	            break;
    		
	        
    		case SITE_VIEW_ARTICLE:
	            $by = $this->settings->get_load_by();
	            
	            // check if it exists
	            if (PecArticle::exists($by, $this->current_page['target']['data'])) {
	                $article = PecArticle::load($by, $this->current_page['target']['data']);
	                
	            	$template = $article->get_template();
	            	
	            	$template_resource->set('template', $template);
	            	$template_resource->set('article', $article);
	            }
	            else {
	                $this->is_404 = true;
	            }
	            break;
	            
	            
    		case SITE_VIEW_SEARCH:
	            $search = new PecSearch($this->current_page['view']['data']);
	            $search->do_search();
	            $article = $search->get();    
	            
	            $template_resource->set('article', $article);
	            break;
	            
    	}
    	
    	#return $template_resource;
    }
}

?>
