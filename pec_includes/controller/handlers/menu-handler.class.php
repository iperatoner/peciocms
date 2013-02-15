<?php

/**
 * pec_includes/controller/handlers/menu-handler.class.php - Contains the menu handler class
 * 
 * Contains the PecMenuHandler which is a class for handling and applying menupoints and other menu data to the TemplateResource 
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
 * PecMenuHandler is the class for handling and applying texts and links to the TemplateResource .
 */
class PecMenuHandler extends PecAbstractHandler {
	
	/**
	 * @var array	$active_menupoints The menupoint objects which are targeting to the current target.
	 */
	private $active_menupoints = array();
	
	private $menu_item_html_cache = array();
	
	private $blog_on_start = false;
	private $current_target_type, $current_target_data, $view_main;

	
    /**
     * Creates a PecMenuHandler instance.
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
    	
    	$this->active_menupoints = PecMenuPoint::load(
            '',
            false,
            "WHERE point_target_type='" . $this->current_page['target']['type'] . "'
             AND   point_target_data='" . $this->current_page['target']['data'] . "'",
            true
        );
        
        // doing some references due to performance issues
        $this->blog_on_start = $this->settings->get_blog_onstart();
        $this->current_target_type =& $this->current_page['target']['type'];
        $this->current_target_data =& $this->current_page['target']['data'];
        $this->view_main =& $this->current_page['view']['main'];
        
    	$complete_menu = $this->generate_menu();
    	$folding_menu = $this->generate_menu(0, 0, false, true);
        $root_menu = $this->generate_menu(0, 0, false);
        $sub_menu = $this->generate_current_submenus();
    	
        $template_resource->set('complete_menu', $complete_menu);
        $template_resource->set('folding_menu', $folding_menu);
        $template_resource->set('root_menu', $root_menu);
        $template_resource->set('sub_menu', $sub_menu);
        
        $template_resource->set('active_menupoints', $this->active_menupoints);
        
    	#return $template_resource;
    }
    
    
    /**
	 * Generates an HTML menu with the available menupoints.
	 * 
	 * @param	integer		$root_id ID of the root menupoint for which the HTML menu has to be created, e.g. 0 or 1 or ...
	 * @param	integer		$count Current level of menupoint, e.g. 1 or 2 or ...
	 * @param	recursive	$recursive Wether the menu should be created recursiveley or not, true|false
	 * @return	string		The HTML menu
	 */
    public function generate_menu($root_id=0, $count=0, $recursive=true, $folding=false) {
        if (PecMenuPoint::exists('root_id', $root_id)) {
            $menupoints = PecMenuPoint::load('root_id', $root_id, "ORDER BY point_sort ASC", true);
            
            // the full layouted menu including the items
            $menu_html = '';
            
            // the html layout of the menu items only
            $menu_items_html = '';
            
            // loading template file of menu wrapper
            $wrapper_template = get_intern_template(MENUWRAPPER_TPL_FILE);
            
            // loading template file of menu item
            $item_template = get_intern_template(MENUITEM_TPL_FILE);
            
            $mp_count = count($menupoints);
            for ($i=0; $i<$mp_count; ++$i) {
            	$mp =& $menupoints[$i];
            	$is_active = false;
            	
                // if the target type of the menupoint is the same as the current target
                // and if the target data of the menupoint is the same as the current active content 
                // (e.g. an article id)
                // OR if the menupoint is targeting to home and 
                // we are viewing a blog post and the blog is assigned to the start page
                if (
                	($mp->get_target_type() == $this->current_target_type && 
                     $mp->get_target_data() == $this->current_target_data) || 
                     
                    ($mp->get_target_type() == MENUPOINT_TARGET_HOME &&
                     $this->view_main == SITE_VIEW_BLOGPOST &&
                     $this->blog_on_start)
                   ) {
                    $li_class = 'active' . $count;
            		$is_active = true;
                }
                else {
                    // check if one of the active menupoints has the menupoint that's currently in the loop
                    // as root/superroot
                    $sub_menupoint_active = false;
                    foreach ($this->active_menupoints as $active_mp) {
                        if ($active_mp->get_superroot_id() == $mp->get_id() || 
                        	$active_mp->get_root_id() == $mp->get_id()) {
                            $sub_menupoint_active = true; break;
                        }
                    }
                    
                    // Set the proper li-class and `$is_active`-variable
                    if ($sub_menupoint_active) {
                    	$li_class = 'active' . $count;
            			$is_active = true;
                    }
                    else {
                    	$li_class = 'inactive' . $count;
                    }
                }
                
                $url = $this->get_menupoint_url(&$mp);
                
                $item = str_replace('{%CLASS%}', $li_class, $item_template);
                $item = str_replace('{%URL%}', $url, $item);
                $item = str_replace('{%NAME%}', $mp->get_name(), $item);
                
                // append the submenu 
                // EITHER if we want a complete recursive menu 
                // OR if we want a folding menu and the current menupoint is active
                if ($recursive || ($folding && $is_active)) {
                    $submenu = $this->generate_menu($mp->get_id(), $count+1, &$recursive, &$folding);
                    $item = str_replace('{%SUBMENU%}', $submenu, $item);
                }
                else {
                    $item = str_replace('{%SUBMENU%}', '', $item);
                }
                
                $menu_items_html .= $item;
            }
            
            // replace the vars of the menu wrapper template with the now generated items and the count number
            $menu_html = str_replace('{%COUNT%}', $count, $wrapper_template);
            $menu_html = str_replace('{%ITEMS%}', $menu_items_html, $menu_html);
            
            return $menu_html;
        }
        else {
            return '';
        }
    }
    
    
	    /**
		 * Generates the HTML submenus that belong to the current target.
		 * 
		 * @return	string The HTML submenus
		 */
        public function generate_current_submenus() {
            $sub_menus_html = '';
            
            // generating the html of the sub menus that are belonging to the menupoints 
            // which target to the currently active content
            foreach ($this->active_menupoints as $active_mp) {
                // if the active menupoint is not in the top level, we have to use the superroot id of this menupoint 
                // to get the submenupoints of it
                if ($active_mp->get_superroot_id() != 0) {
                    $sub_menus_html .= $this->generate_menu($active_mp->get_superroot_id());
                }
                else {
                    $sub_menus_html .= $this->generate_menu($active_mp->get_id());
                }
            }
            
            return $sub_menus_html;
        }
    
        
	    /**
		 * Generates an URL for a menupoint.
		 * 
		 * @param	PecMenuPoint $menupoint Menupoint object for that the URL has to be generated
		 * @return	string The proper URL
		 */
        private function get_menupoint_url($menupoint) {
            switch ($menupoint->get_target_type()) {
                case MENUPOINT_TARGET_HOME:
                    $url = create_home_url();
                    break;
                    
                case MENUPOINT_TARGET_ARTICLE:
                    if (PecArticle::exists('id', $menupoint->get_target_data())) {
                        $article = PecArticle::load('id', $menupoint->get_target_data());
                        $url = create_article_url($article);
                    }
                    else {
                        $url = create_404_url($menupoint->get_target_data());
                    }
                    break;
                    
                case MENUPOINT_TARGET_BLOG:
                    $url = create_blog_url();
                    break;
                    
                case MENUPOINT_TARGET_URL:
                    $url = $menupoint->get_target_data();
                    break;
            }
            
            return $url;
        }
}

?>
