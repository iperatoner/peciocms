<?php

/**
 * pec_includes/controller/handlers/plugin-handler.class.php - Contains the plugin handler class
 * 
 * Contains the PecPluginHandler which is a class for handling and applying plugins to the TemplateResource and the data in it
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
 * PecPluginHandler is the class for handling and applying plugins to the TemplateResource and its data.
 */
class PecPluginHandler extends PecAbstractHandler {
	
	/**
	 * @var array	$plugin_meta_instances The meta plugin instances that hold metadata about them
	 */
	private $plugin_meta_instances = array();
	
	
	/**
	 * @var array	$plugin_instances The actual plugin instances
	 */
	private $plugin_instances = array();
	
	
	/**
	 * @var array	$head_data Head data of all plugin instances in one string
	 */
	private $head_data = '';
	
	
	/**
	 * @var array	$first_parse Wether the parse-method was called yet or not. Needed because we want to generate all the plugin's head data in the first parse.
	 */
	private $first_parse = true;

	
    /**
     * Creates a PecPluginHandler instance.
     */
    function __construct() {
    	parent::__construct();
    	
    	$this->plugin_meta_instances = PecPlugin::load();
    }
    
    
    /**
     * May update the PecTemplateResource with new data (e.g. the current post etc.)
     * 
     * @param	PecTemplateResource $template_resource Holds a lot of data (e.g. objects, articles, etc.) related to the current view
     * @return	array The updated PecTemplateResource
     */
    public function apply($template_resource) {
		$this->create_plugin_instances();
		
		$article = $template_resource->get('article');
		$articles = $template_resource->get('articles');
		
		$blogpost = $template_resource->get('blogpost');
		$blogposts = $template_resource->get('blogposts');
		$all_available_blogposts = $template_resource->get('all_available_blogposts');
		
		$sidebar_text_objects = $template_resource->get('sidebar_text_objects');
		$sidebar_linkcategory_objects = $template_resource->get('sidebar_linkcategory_objects');
		
		$sidebar_texts = $template_resource->get('sidebar_texts');
		$sidebar_links = $template_resource->get('sidebar_links');
		
		// SINGLE ARTICLE
		if ($article) {
			$content = $article->get_content();
			$content = $this->parse_plugins($content);
			$article->set_content($content);
			
			$template_resource->set('article', $article);
		}
		
		// ARTICLES
		if ($articles && is_array($articles)) {
			$article_num = count($articles);
			for ($i=0; $i<$article_num; ++$i) {
				$content = $articles[$i]->get_content();
				$content = $this->parse_plugins($content);
				$articles[$i]->set_content($content);
			}
			$template_resource->set('article', $articles);
		}
		
		// SINGLE BLOGPOST
		if ($blogpost) {
			$content = $blogpost->get_content();
			$content = $this->parse_plugins($content);
			$blogpost->set_content($content);
			
			$content_cut = $blogpost->get_content_cut();
			$content_cut = $this->parse_plugins($content_cut);
			$blogpost->set_content_cut($content_cut);
			
			$template_resource->set('blogpost', $blogpost);
		}
		
		// BLOGPOSTS
		if ($blogposts && is_array($blogposts)) {
			$post_num = count($blogposts);
			for ($i=0; $i<$post_num; ++$i) {
				$content = $blogposts[$i]->get_content();
				$content = $this->parse_plugins($content);
				$blogposts[$i]->set_content($content);
				
				$content_cut = $blogposts[$i]->get_content_cut();
				$content_cut = $this->parse_plugins($content_cut);
				$blogposts[$i]->set_content_cut($content_cut);
			}
			
			$template_resource->set('blogposts', $blogposts);
		}
		
		// ALL AVAILABLE BLOGPOSTS 
		if ($all_available_blogposts && is_array($all_available_blogposts)) {
			$post_num = count($all_available_blogposts);
			for ($i=0; $i<$post_num; ++$i) {
				$content = $all_available_blogposts[$i]->get_content();
				$content = $this->parse_plugins($content);
				$all_available_blogposts[$i]->set_content($content);
				
				$content_cut = $all_available_blogposts[$i]->get_content_cut();
				$content_cut = $this->parse_plugins($content_cut);
				$all_available_blogposts[$i]->set_content_cut($content_cut);
			}
			
			$template_resource->set('all_available_blogposts', $all_available_blogposts);
		}
		
		// TEXT OBJECTS
		if ($sidebar_text_objects && is_array($sidebar_text_objects)) {
			$text_num = count($sidebar_text_objects);
			for ($i=0; $i<$text_num; ++$i) {
				$content = $sidebar_text_objects[$i]->get_content();
				$content = $this->parse_plugins($content);
				$sidebar_text_objects[$i]->set_content($content);
			}
			$template_resource->set('sidebar_text_objects', $sidebar_text_objects);
		}
		
		// LINKCAT OBJECTS
		if ($sidebar_linkcategory_objects && is_array($sidebar_linkcategory_objects)) {
			$linkcat_num = count($sidebar_linkcategory_objects);
			for ($i=0; $i<$linkcat_num; ++$i) {
				$title = $sidebar_linkcategory_objects[$i]->get_title();
				$title = $this->parse_plugins($title);
				$sidebar_linkcategory_objects[$i]->set_title($title);
			}
			$template_resource->set('sidebar_linkcategory_objects', $sidebar_linkcategory_objects);
		}
		
		// TEXTS
		if ($sidebar_texts) {
			$sidebar_texts = $this->parse_plugins($sidebar_texts);
			$template_resource->set('sidebar_texts', $sidebar_texts);
		}
		
		// LINKS
		if ($sidebar_links) {
			$sidebar_links = $this->parse_plugins($sidebar_links);
			$template_resource->set('sidebar_links', $sidebar_links);
		}
		
		$template_resource->set('plugin_meta_instances', $this->plugin_meta_instances);
		$template_resource->set('plugin_instances', $this->plugin_instances);
		$template_resource->set('plugin_head_data', $this->head_data);
		

		/*
		 * TODO:
		 * 
		 * Plugins should be able to hook their own managers into the controller. 
		 * so they might be executed here.
		 * 
		 */
		
    	#return $template_resource;
    }
    
    
    /**
	 * Creates main plugin instances one time.
	 */
    private function create_plugin_instances() {
        foreach ($this->plugin_meta_instances as $p) {
        	
        	if ($p->is_installed() || !$p->installation_required()) {
	            require_once(PLUGIN_PATH . $p->get_directory_name() . '/' . $p->get_property('main_file'));
	            
	            $class_name = $p->get_property('class_name');
                
	            eval('$plugin_instance = new ' . $class_name . '();');
                
                $plugin_instance->set_plugin_meta($p);
                $plugin_instance->set_current_page($this->current_page);
                
                // append it!
                $this->plugin_instances[$class_name] = $plugin_instance;
        	}
        	
        }
    }
    
    
    /**
	 * Parses the plugin vars in a given string.
	 * 
	 * @param	string	$string The string (usually content) to parse
	 * @return	string	Parsed string
	 */
    public function parse_plugins($string) {
    
        // Only continue if actually _any_ var exists in this string
        $varpos = strpos($string, '{%');
        
        if ($varpos || is_integer($varpos)) {
        	foreach ($this->plugin_instances as $plugin_instance) {
                if ($this->first_parse) {
        			$this->head_data .= $plugin_instance->head_data();
                }
                
        		$p = $plugin_instance->get_plugin_meta();
        		
        		// Just doing a pre-check if this variable actually occurs in the string. 
        		// If not, we're skipping this plugin right here (potentially better performance)
        		$varpos = strpos($string, $p->get_property('variable'));
        		
        		if ($varpos || is_integer($varpos)) {
        			
		            // INPUT ENABLED
		            if ($p->get_property('input_enabled')) {
		                $var_datas = grep_data_between('{%' . $p->get_property('variable') . '-(', ')%}', $string);
		                
		                foreach ($var_datas as $var_data) {
		                    $complete_var = '{%' . $p->get_property('variable') . '-(' . $var_data . ')%}';
		                    $pos = strpos($string, $complete_var);
		                    
		                    if ($pos || is_integer($pos)) {
		                        $replace_data = $plugin_instance->run($var_data);
		                        $string = str_replace($complete_var, $replace_data, $string);
		                    }
		                }
		            }
		                
		            // NO INPUT
		            else {
		                $complete_var = '{%' . $p->get_property('variable') . '%}';
		                $pos = strpos($string, $complete_var);
		                
		                if ($pos || is_integer($pos)) {
		                    $replace_data = $plugin_instance->run();
		                    $string = str_replace($complete_var, $replace_data, $string);
		                }
		            }
		            
        		}
        	}
        
        	if ($this->first_parse) {
        		$this->first_parse = false;
        	}
    	}
    	
        return $string;
    }
}

?>
