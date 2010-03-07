<?php

/**
 * pec_includes/controller/resource-generator.class.php - Generates content depending on the current view
 * 
 * Generates the content that can be used by templates, always depending on the current view.
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
 * @version		2.0.2
 * @link		http://pecio-cms.com
 */

/**
 * The PecResourceGenerator generates a lot resources which can be used in a template. 
 * Always depending on the site_view and sub_site_view.
 */
class PecResourceGenerator {
    
	/**
	 * @var PecSetting		$settings, Pecio's settings.
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
	 * @var array			$active_menupoints, The menupoint objects which are targeting to the current target.
	 * @var array			$plugins, All available plugins (meta data of them).
	 */
    private $settings, $current_target_type, $current_target_data,
            $site_view, $sub_site_view, $current_view_data, $articles_on_start, $current_article,
            $current_blogpost, $current_blogcategory, $current_blogtag, $active_menupoints, $plugins;
    
	/**
	 * @static
	 * @var array All the objects that can be parsed with the plugin vars.
	 */
    static $parse_by_array = array(
        'article',
        'articles',
        'blogpost',
        'blogposts',
        'text',
        'texts'
    );
            
    function __construct($current_target_type, $current_target_data, 
                         $site_view, $sub_site_view, $current_view_data, $articles_on_start, 
                         $current_article, $current_blogpost, $current_blogcategory, $current_blogtag, $plugins) {
        global $pec_settings;
        $this->settings = $pec_settings;
                 
        $this->current_target_type = $current_target_type;
        $this->current_target_data = $current_target_data;
        $this->site_view = $site_view;
        $this->sub_site_view = $sub_site_view;
        $this->current_view_data = $current_view_data;
        
        $this->articles_on_start = $articles_on_start;
        $this->current_article = $current_article;
        $this->current_blogpost = $current_blogpost;
        $this->current_blogcategory = $current_blogcategory;
        $this->current_blogtag = $current_blogtag;
        
        $this->plugins = $plugins;
        
        $this->active_menupoints = PecMenuPoint::load(
            '',
            false,
            "WHERE point_target_type='" . $this->current_target_type . "'
             AND   point_target_data='" . $this->current_target_data . "'",
            true
        );
    }
    
    /**
	 * Generates an HTML menu with the available menupoints.
	 * 
	 * @param	integer		$root_id: ID of the root menupoint for which the HTML menu has to be created, e.g. 0 or 1 or ...
	 * @param	integer		$count: Current level of menupoint, e.g. 1 or 2 or ...
	 * @param	recursive	$count: Wether the menu should be created recursiveley or not, true|false
	 * @return	string		The HTML menu
	 */
    public function generate_menu($root_id=0, $count=0, $recursive=true) {
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
            
            foreach ($menupoints as $mp) {    
                // if the target type of the menupoint is the same as the current target
                // and if the target data of the menupoint is the same as the current active content 
                // (e.g. an article id)
                if (
                	($mp->get_target_type() == $this->current_target_type && 
                     $mp->get_target_data() == $this->current_target_data) || 
                     
                    ($mp->get_target_type() == MENUPOINT_TARGET_HOME &&
                     $this->site_view == SITE_VIEW_BLOGPOST &&
                     $this->settings->get_blog_onstart())
                   ) {
                    $li_class = 'active' . $count;
                }
                else {
                    // check if one of the active menupoints has the menupoint that's currently in the loop
                    // as root/superroot
                    $sub_menupoint_active = false;
                    foreach ($this->active_menupoints as $active_mp) {
                        if ($active_mp->get_superroot_id() == $mp->get_id() || $active_mp->get_root_id() == $mp->get_id()) {
                            $sub_menupoint_active = true; break;
                        }
                    }                    
                    $li_class = $sub_menupoint_active ? 'active' . $count : 'inactive' . $count;
                }
                
                $url = $this->generate_menupoint_url($mp);
                
                $item = str_replace('{%CLASS%}', $li_class, $item_template);
                $item = str_replace('{%URL%}', $url, $item);
                $item = str_replace('{%NAME%}', $mp->get_name(), $item);
                                
                if ($recursive) {
                    $submenu = $this->generate_menu($mp->get_id(), $count+1, $recursive);
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
            // which correspond to the currently active content
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
		 * @param	PecMenuPoint $menupoint: Menupoint object for that the URL has to be generated
		 * @return	string The proper URL
		 */
        private function generate_menupoint_url($menupoint) {
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
        
    /**
	 * Generates the sidebar texts which are allowed to be displayed on the current site view.
	 * 
	 * @return	string		HTML layout of the texts
	 */
    public function generate_texts() {
        // all texts that are allowed to be displayed on the currently active page/content/article/blog/...
        $all_visible_texts = array();
        
        // an array that contains arrays of PecSidebarText objects
        $text_arrays = array();
        
        // EVERYWHERE texts
        $text_arrays[] = PecSidebarText::load('visibility', TEXT_VISIBILITY_EVERYWHERE, '', true);
        
        // ARTICLE / SEARCH / 404 texts
        if ($this->site_view == SITE_VIEW_ARTICLE || $this->site_view == SITE_VIEW_SEARCH || 
            $this->site_view == SITE_VIEW_404) {
            $text_arrays[] = PecSidebarText::load('visibility', TEXT_VISIBILITY_ON_ALL_ARTICLES, '', true);
            // only load texts of specific article, if that is one
            if ($this->site_view == SITE_VIEW_ARTICLE) {
                $text_arrays[] = PecSidebarText::load('article', $this->current_article, '', true);
            }
        }
        // HOME texts
        elseif ($this->site_view == SITE_VIEW_HOME) {
            $text_arrays[] = PecSidebarText::load('visibility', TEXT_VISIBILITY_ON_ALL_ARTICLES, '', true);
            
            if ($this->settings->get_blog_onstart()) {
                $text_arrays[] = PecSidebarText::load('visibility', TEXT_VISIBILITY_ON_BLOG, '', true);
            }
            
            $texts_on_start_articles = array();
            $texts_already_in_array = array();
            
            foreach ($this->articles_on_start as $a) {
                $article_text_array = PecSidebarText::load('article', $a, '', true);
                
                // removing duplicates from this array (of this article)
                foreach ($article_text_array as $key => $t) {
                    if (in_array($t->get_id(), $texts_already_in_array)) {
                        unset($article_text_array[$key]);
                    }
                    else {
                        $texts_already_in_array[] = $t->get_id();
                    }
                }
                
                $texts_on_start_articles = array_merge($texts_on_start_articles, $article_text_array);
            }
            
            $text_arrays[] = $texts_on_start_articles;
        }
        // BLOG texts
        elseif ($this->site_view == SITE_VIEW_BLOG            || $this->site_view == SITE_VIEW_BLOGPOST || 
                $this->site_view == SITE_VIEW_BLOGCATEGORY || $this->site_view == SITE_VIEW_BLOGTAG  ||
                $this->site_view == SITE_VIEW_BLOGARCHIVE) {
            $text_arrays[] = PecSidebarText::load('visibility', TEXT_VISIBILITY_ON_BLOG, '', true);
        }
        
        // merging all text-arrays together
        foreach ($text_arrays as $texts) {
            $all_visible_texts = array_merge($all_visible_texts, $texts);
        }
        
        // sort the (now mixed) texts
        $biggest_sort_number = 1;
        $all_sorted_texts = array();
        foreach ($all_visible_texts as $t) {
            if ($t->get_sort() > $biggest_sort_number) {
                $biggest_sort_number = $t->get_sort();
            }
        }
        for ($i=1; $i<=$biggest_sort_number; $i++) {
            foreach ($all_visible_texts as $t) {
                if ($t->get_sort() == $i) {
                    $all_sorted_texts[] = $t;
                }
            }
        }
        $all_visible_texts = $all_sorted_texts;
        
        
        $text_template = get_intern_template(SIDEBARTEXT_TPL_FILE);
        
        // the html result of all texts 
        $texts_html = '';
        
        foreach ($all_visible_texts as $t) {
            $html = str_replace('{%ID%}', $t->get_id(), $text_template);
            $html = str_replace('{%TITLE%}', $t->get_title(), $html);
            $html = str_replace('{%CONTENT%}', $t->get_content(), $html);
            
            $texts_html .= $html;
        }
        
        return $texts_html;
    }
        
    /**
	 * Generates the sidebar links which are allowed to be displayed on the current site view.
	 * 
	 * @return	string		HTML layout of the links
	 */
    public function generate_links() {
        // all linkcategories that are allowed to be displayed on the currently active page/content/article/blog/...
        $all_visible_categories = array();
        
        // an array that contains arrays of PecSidebarLinkCat objects
        $linkcategory_arrays = array();
        
        // EVERYWHERE link-categories
        $linkcategory_arrays[] = PecSidebarLinkCat::load('visibility', TEXT_VISIBILITY_EVERYWHERE, '', true);
            
        // ARTICLE / SEARCH / 404 link-categories
        if ($this->site_view == SITE_VIEW_ARTICLE || $this->site_view == SITE_VIEW_SEARCH || 
            $this->site_view == SITE_VIEW_404) {
            $linkcategory_arrays[] = PecSidebarLinkCat::load('visibility', TEXT_VISIBILITY_ON_ALL_ARTICLES, '', true);
            // only load link-categories of specific article, if that is one
            if ($this->site_view == SITE_VIEW_ARTICLE) {
                $linkcategory_arrays[] = PecSidebarLinkCat::load('article', $this->current_article, '', true);
            }
        }
        // HOME link-categories
        elseif ($this->site_view == SITE_VIEW_HOME) {
            $linkcategory_arrays[] = PecSidebarLinkCat::load('visibility', TEXT_VISIBILITY_ON_ALL_ARTICLES, '', true);
            
            if ($this->settings->get_blog_onstart()) {
                $linkcategory_arrays[] = PecSidebarLinkCat::load('visibility', TEXT_VISIBILITY_ON_BLOG, '', true);
            }
            
            $linkcategories_on_start_articles = array();
            $linkcategories_already_in_array = array();
            
            foreach ($this->articles_on_start as $a) {                
                $article_linkcategory_array = PecSidebarLinkCat::load('article', $a, '', true);
                
                // removing duplicates from this array (of this article)
                foreach ($article_linkcategory_array as $key => $lc) {
                    if (in_array($lc->get_id(), $linkcategories_already_in_array)) {
                        unset($article_linkcategory_array[$key]);
                    }
                    else {
                        $linkcategories_already_in_array[] = $lc->get_id();
                    }
                }
                
                $linkcategories_on_start_articles = array_merge($linkcategories_on_start_articles, $article_linkcategory_array);
            }
            
            $linkcategory_arrays[] = $linkcategories_on_start_articles;
        }
        // BLOG link-categories
        elseif ($this->site_view == SITE_VIEW_BLOG            || $this->site_view == SITE_VIEW_BLOGPOST || 
                $this->site_view == SITE_VIEW_BLOGCATEGORY || $this->site_view == SITE_VIEW_BLOGTAG  ||
                $this->site_view == SITE_VIEW_BLOGARCHIVE) {
            $linkcategory_arrays[] = PecSidebarLinkCat::load('visibility', TEXT_VISIBILITY_ON_BLOG, '', true);
        }
        
        // merging all linkcategory-arrays together
        foreach ($linkcategory_arrays as $linkcategories) {
            $all_visible_categories = array_merge($all_visible_categories, $linkcategories);
        }
        
        // sort the (now mixed) link-categories
        $biggest_sort_number = 1;
        $all_sorted_categories = array();
        foreach ($all_visible_categories as $c) {
            if ($c->get_sort() > $biggest_sort_number) {
                $biggest_sort_number = $c->get_sort();
            }
        }
        for ($i=1; $i<=$biggest_sort_number; $i++) {
            foreach ($all_visible_categories as $c) {
                if ($c->get_sort() == $i) {
                    $all_sorted_categories[] = $c;
                }
            }
        }
        $all_visible_categories = $all_sorted_categories;
        
        $linkcategory_template = get_intern_template(SIDEBARLINKCAT_TPL_FILE);
        $link_template = get_intern_template(SIDEBARLINK_TPL_FILE);        
        
        // the html result of all links 
        $links_html = '';
        
        foreach ($all_visible_categories as $lc) {            
            $links = PecSidebarLink::load('cat', $lc);
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
    
    /**
	 * Parses the plugin vars in a given string.
	 * 
	 * @param	string	$content: The string (usually content) which has to be parsed
	 * @return	string	Parsed string
	 */
    public function parse_plugins($content) {
        foreach ($this->plugins as $p) {
        	if ($p->is_installed() || !$p->installation_required()) {
	            require_once(PLUGIN_PATH . $p->get_directory_name() . '/' . $p->get_property('main_file'));
	            
	            // INPUT ENABLED
	            if ($p->get_property('input_enabled')) {
	                $var_datas = grep_data_between('{%' . $p->get_property('variable') . '-(', ')%}', $content);
	                
	                foreach ($var_datas as $var_data) {
	                    $complete_var = '{%' . $p->get_property('variable') . '-(' . $var_data . ')%}';
	                    
	                    if (strpos($content, $complete_var) || is_integer(strpos($content, $complete_var))) {                            
	                        eval('$plugin_instance = new ' . $p->get_property('class_name') . '($p, $this->site_view, $this->sub_site_view);');
	                        $replace_data = $plugin_instance->run($var_data);
	                        $content = str_replace($complete_var, $replace_data, $content);
	                    }
	                }
	            }
	            
	            // NO INPUT
	            else {
	                $complete_var = '{%' . $p->get_property('variable') . '%}';
	                
	                if (strpos($content, $complete_var) || is_integer(strpos($content, $complete_var))) {
	                    eval('$plugin_instance = new ' . $p->get_property('class_name') . '($p, $this->site_view, $this->sub_site_view);');
	                    $replace_data = $plugin_instance->run();
	                    $content = str_replace($complete_var, $replace_data, $content);
	                }
	            }
        	}
        }
        
        return $content;
    }
    
    
	    /**
		 * Parses the plugin vars in a given object.
		 * 
		 * @param	string	$by: The type of object which's content has to be parsed, e.g. 'blogpost' or 'article'
		 * @param	mixed	$data: The object which's content has to be parsed, e.g. PecBlogPost or PecArticle
		 * @return	string	The object which's content is now parsed
		 */
        public function parse_plugins_of($by='article', $data=false) {
            if ($by && $data !== false && in_array($by, self::$parse_by_array)) {
                if ($by == 'article') {
                    $data->set_content($this->parse_plugins($data->get_content()));
                }
                elseif ($by == 'articles') {
                    $new_data = array();
                    foreach ($data as $a) {
                         $a->set_content($this->parse_plugins($a->get_content()));
                         $new_data[] = $a;
                    }
                    $data = $new_data;
                }
                elseif ($by == 'blogpost') {
                    $data->set_content_cut($this->parse_plugins($data->get_content_cut()));
                    $data->set_content($this->parse_plugins($data->get_content()));
                }
                elseif ($by == 'blogposts') {
                    $new_data = array();
                    foreach ($data as $bp) {
                        $bp->set_content_cut($this->parse_plugins($bp->get_content_cut()));
                        $bp->set_content($this->parse_plugins($bp->get_content()));
                        $new_data[] = $bp;
                    }
                    $data = $new_data;
                }
                elseif ($by == 'text') {
                    $data->set_content($this->parse_plugins($data->get_content()));
                }
                elseif ($by == 'texts') {
                    $new_data = array();
                    foreach ($data as $t) {
                         $t->set_content($this->parse_plugins($t->get_content()));
                         $new_data[] = $t;
                    }
                    $data = $new_data;
                }
                
                return $data;
            }
            else {
                return false;
            }
        }
    
    /**
	 * Loads the data for the HTML head that is given by the available plugins.
	 * 
	 * @return	string	All plugin HTML head data
	 */
    public function get_plugin_head_data() {        
        $head_data = '';
        
        foreach ($this->plugins as $p) {
            require_once(PLUGIN_PATH . $p->get_directory_name() . '/' . $p->get_property('main_file'));
            eval('$plugin_instance = new ' . $p->get_property('class_name') . '($p, $this->site_view, $this->sub_site_view);');
            $head_data .= $plugin_instance->head_data($this->site_view, $this->sub_site_view);
        }
        
        return $head_data;
    }
    
    /**
	 * Calculates the available pages of blogposts.
	 * 
	 * @param	array	$blogposts: Array of available blogposts
	 * @return	integer	Number of the pages of blogposts
	 */
    public function get_available_blog_pages($blogposts=false) {
    	if ($blogposts) {
    		$post_count = count($blogposts);
    		$available_pages =  $post_count / $this->settings->get_posts_per_page();
    		if ($post_count % $this->settings->get_posts_per_page() != 0) {
    			$available_pages = (int) $available_pages + 1;
    		}
    		else {
    			$available_pages = (int) $available_pages;
    		}
    		
    		return $available_pages;
    	}
    	else {
    		return false;
    	}
    }
    
    /**
	 * Calculates the page number of the older entries page, depending on the current blog page.
	 * 
	 * @param	integer	$current_blog_page: The current page of blogposts, e.g. 2
	 * @param	integer	$available_pages: All available pages of blogposts, e.g. 5
	 * @return	integer	Number of the older page of blogposts
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
    		return false;
    	}
    }
    
    /**
	 * Calculates the page number of the newer entries page, depending on the current blog page.
	 * 
	 * @param	integer	$current_blog_page: The current page of blogposts, e.g. 2
	 * @param	integer	$available_pages: All available pages of blogposts, e.g. 5
	 * @return	integer	Number of the newer page of blogposts
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
    		return false;
    	}
    }    
    
    /**
	 * Generates the blog URL for a page of blogposts.
	 * 
	 * @param	integer	$page: The page of blogposts for that the url has to be created, e.g. 2
	 * @return	string	Proper URL
	 */
    public function get_blog_page_url($page=false) {
    	if ($page !== false) {
    		if ($this->site_view == SITE_VIEW_BLOG) {
    			$home =  false;
    		}
    		elseif ($this->site_view == SITE_VIEW_HOME) {
    			$home =  true;
    		}
    		
    		switch ($this->sub_site_view) {
    			case SITE_VIEW_BLOG: $url = create_blog_url($page, $home); break;
    			case SITE_VIEW_BLOGCATEGORY: $url = create_blogcategory_url($this->current_blogcategory, $page, $home); break;
    			case SITE_VIEW_BLOGTAG: $url = create_blogtag_url($this->current_blogtag, $page, $home); break;
    			case SITE_VIEW_BLOGARCHIVE: 
    				$d = isset($_GET['day']) && !empty($_GET['day']) ? $_GET['day'] : false;
    				$m = isset($_GET['month']) && !empty($_GET['month']) ? $_GET['month'] : false;
    				$y = isset($_GET['year']) && !empty($_GET['year']) ? $_GET['year'] : false;
    				$url = create_blogarchive_url($d, $m, $y, $page, $home); 
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
	 * @param	PecBlogPost	$post: The blog post object for that the comment has to be created
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
                        $comment = new PecBlogComment(NULL_ID, $post->get_id(), $_POST['comment_title'], $_POST['comment_author'], 
                                                      $_POST['comment_email'], time(), $_POST['comment_content'], false);
                          $comment->save();
                        pec_redirect(create_blogpost_url($post, false, 'message=comment_created#messages'), 0, false, false);
                    }
                }
                
            }            
        }
    }
    
}

?>