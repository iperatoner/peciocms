<?php

/**
 * pec_includes/controller/template-resource.class.php - Template Resource Class
 * 
 * Defines the class which holds data that can be used for templates.
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
 * The PecTemplateResource holds the data that can be used in a template.
 */
class PecTemplateResource {
    
	/**
	 * @var array An array of all the data that can be used.
	 */
    private $data;
    
	/**
	 * @static
	 * @var array All array keys (properties) that can be set.
	 */
    static $resources = array(
        'site_view',
        'sub_site_view',
        'article',
        'articles',
        'blogpost',
        'blogposts',
        'blogcomments',
        'blogcategory',
        'blogtag',
        'blogarchive_day',
        'blogarchive_month',
        'blogarchive_year',
    	'blog_older_entries_page',
    	'blog_newer_entries_page',
    	'blog_older_entries_url',
    	'blog_newer_entries_url',
        'complete_menu',
        'root_menu',
        'sub_menu',
        'sidebar_texts',
        'sidebar_links',
        'search_form',
        'plugin_head_data',
        'settings',
        'template',
        'template_path',
        'homepage_main_title',
        'homepage_sub_title',
        'homepage_tags',
        'homepage_description',
        'root_path'
    );
    
    /**
     * Creates a PecTemplateResource instance.
     * 
     * @param	PecSettings	$settings: Pecio's settings
     * @param	PecTemplate	$template: Pecio's template
     * @param	string		$site_view: The current site view
     * @param	string		$sub_site_view: The current sub site view
     */
    function __construct($settings, $template, $site_view, $sub_site_view) {
        $this->data = array();
        
        $this->data['settings'] = $settings;
        $this->data['template'] = $template;
        $this->data['template_path'] = $template->get_directory_path(false);
        $this->data['site_view'] = $site_view;  
        $this->data['sub_site_view'] = $sub_site_view;    
        
        $this->data['homepage_main_title'] = $settings->get_sitename_main();
        $this->data['homepage_sub_title'] = $settings->get_sitename_sub();
        $this->data['homepage_tags'] = $settings->get_tags();
        $this->data['homepage_description'] = $settings->get_description();
        
        $this->data['article'] = false;
        $this->data['articles'] = false;
        $this->data['blogpost'] = false;
        $this->data['blogposts'] = false;
        $this->data['blogcomments'] = false;
        $this->data['blogcategory'] = false;
        $this->data['blogtag'] = false;
        $this->data['blogarchive_day'] = false;
        $this->data['blogarchive_month'] = false;
        $this->data['blogarchive_year'] = false;
        $this->data['blog_older_entries_page'] = false;
        $this->data['blog_newer_entries_page'] = false;
        $this->data['blog_older_entries_url'] = false;
        $this->data['blog_newer_entries_url'] = false;
        $this->data['complete_menu'] = false;
        $this->data['root_menu'] = false;
        $this->data['sub_menu'] = false;
        $this->data['sidebar_texts'] = false;
        $this->data['sidebar_links'] = false;
        $this->data['search_form'] = false;
        $this->data['plugin_head_data'] = false;
        $this->data['root_path'] = pec_root_path(false);
    }
    
    /**
     * Returns the data of an array key.
     * 
     * @param	string	$resource: The array key which's data should be returned, e.g. "sidebar_texts"
     * @return	string The proper data
     */
    public function get($resource='') {
        if (in_array($resource, self::$resources)) {
            return $this->data[$resource];
        }
        else {
            return '';
        }
    }
    
	    /**
	     * Prints the data of an array key.
	     * 
	     * @param	string	$resource: The array key which's data should be printed out, e.g. "sidebar_texts"
	     */
        public function out($resource='') {
            echo $this->get($resource);
        }
    
    /**
     * Sets the data of an array key.
     * 
     * @param	string	$resource: The array key which's data should be set, e.g. "sidebar_texts"
     * @param	mixed	$value: The data that should be set to the given array key
     */
    public function set($resource='', $value) {
        if (in_array($resource, self::$resources)) {
            $this->data[$resource] = $value;
        }
    }
    
    /**
     * Generates and returns/prints all the available HTML head data including plugin head data.
     * 
     * @param	boolean	$return: Wether the head data should be returned or printed
     * @return string The HTML head data
     */
    public function head_data($return=false) {
    	global $pec_localization;
    	
        // use data from settings etc.
        $head_data = '
            
            <meta name="description" content="' . $this->data['settings']->get_description() . '" />
            <meta name="keywords" content="' . $this->data['settings']->get_tags() . '" />
            <meta name="generator" content="pecio cms ' . PEC_VERSION . '" />
            
            <script type="text/javascript" src="' . pec_root_path(false) . 'pec_admin/pec_style/js/mootools/mootools-core.js"></script>
            <script type="text/javascript" src="' . pec_root_path(false) . 'pec_admin/pec_style/js/mootools/mootools-more.js"></script>
            <script type="text/javascript" src="' . pec_root_path(false) . 'pec_admin/pec_style/js/main-animations.js"></script>
            
            <link rel="stylesheet" type="text/css" href="' . pec_root_path(false) . 'pec_admin/pec_style/css/misc.css" />
            
        ';
        
        // feeds
        if ($this->data['sub_site_view'] == SITE_VIEW_BLOG || $this->data['sub_site_view'] == SITE_VIEW_ARCHIVE) {
        	$head_data .= '<link rel="alternate" type="application/rss+xml"  href="' . create_blog_feed_url() . '" title="Blog Feed" />';
        }
        elseif ($this->data['sub_site_view'] == SITE_VIEW_BLOGCATEGORY && $this->data['blogcategory']) {
        	$head_data .= '<link 
        		rel="alternate" 
        		type="application/rss+xml"
        		href="' . create_blogcategory_feed_url($this->data['blogcategory']) . '"
        		title="' . $pec_localization->get('LABEL_GENERAL_CATEGORY') . ' ' . $this->data['blogcategory']->get_name() . ' Feed" 
        	/>';
        }
        elseif ($this->data['sub_site_view'] == SITE_VIEW_BLOGTAG && $this->data['blogtag']) {
        	$head_data .= '<link 
        		rel="alternate" 
        		type="application/rss+xml"
        		href="' . create_blogtag_feed_url($this->data['blogtag']) . '"
        		title="' . $pec_localization->get('LABEL_GENERAL_TAG') . ' ' . $this->data['blogtag']->get_name() . ' Feed" 
        	/>';
        }
        
        // return or echo?
        if ($return) {
            return $head_data . $this->data['plugin_head_data'];
        }
        else {
            echo $head_data . $this->data['plugin_head_data'];
        }
    }
    
    /**
     * Loads all comments belonging to the given post.
     * 
     * @param	PecBlogPost	$post: The post to load the comments for
     * @param	string	$order: How the comments should be sorted, e.g. "asc"
     * @return array The proper PecBlogComment instances
     */
    public function get_comments($post=false, $order='ASC') {
    	if ($post) {
	    	$comments = PecBlogComment::load('post', $post, 'ORDER BY comment_timestamp ' . strtoupper($order));
    		return $comments;
    	}
    	else {
    		return array();
    	}
    }
    
    /**
     * Creates an instance of the given plugin class name.
     * 
     * @param	string	$class_name: The plugin's main class name
     * @return object Instance of the given plugin class name. Inherits PecAbstractPlugin
     */
    public function get_plugin_object($class_name='') {
    	$plugin = PecPlugin::load('class_name', $class_name);
    	if ($plugin) {                
    		eval('$plugin_instance = new ' . $plugin->get_property('class_name') . '($plugin, $this->data["site_view"], $this->data["sub_site_view"]);');
    	}
    	else {
    		$plugin_instance = false;
    	}
    	return $plugin_instance;
    }
    
    /**
     * Generates and returns/prints antispam inputs for the post comment form.
     * 
     * @param	boolean	$return: Wether the inputs should be returned or printed
     * @return string The HTML antispam inputs
     */
    public function comment_antispam_inputs($return=false) {
        $inputs = '
            <input type="hidden" name="' . $this->data['settings']->get_nospam_key(1) . '" value="" />
            <input type="hidden" name="' . md5($this->data['settings']->get_nospam_key(2)) . '" value="' . crypt($this->data['settings']->get_nospam_key(3)) . '" />
        ';
        
        if ($return) {
            return $inputs;
        }
        else {
            echo $inputs;
        }
    }
    
    /**
     * Generates and returns/prints messages for the comment form, if e.g. any errors occured.
     * 
     * @param	boolean	$return: Wether the messages should be returned or printed
     * @return string The HTML messages
     */
    public function comment_form_messages($return=false) {
        if (isset($_GET['message'])) {            
            $message = '<div id="messages">';
            switch ($_GET['message']) {
                case 'comment_created':    $message .= PecMessageHandler::get('comment_created'); break;
                case 'empty_fields': $message .= PecMessageHandler::get('comment_empty_fields'); break;
                case 'email_incorrect': $message .= PecMessageHandler::get('comment_email_incorrect'); break;
                default: $message .= ''; break;
            }
            $message .= '</div>';
            
            if ($return) {
                return $message;
            }
            else {
                echo $message;
            }
        }
    }
    
    
    // URLs
    
    /**
     * Returns URL of the start page.
     * 
     * @return string Home URL
     */
    public function home_url($return) {
        return create_home_url();
    }
    
    /**
     * Returns URL of an article.
     * 
     * @param PecArticle	$article: Article object of which the URL has to be created
     * @return string Article URL
     */
    public function article_url($article) {
        return create_article_url($article);
    }
    
    /**
     * Returns URL for the search form.
     * 
     * @return string Search form URL/action
     */
    public function search_url() {
        return create_search_url();
    }
    
    /**
     * Returns URL of the blog.
     * 
     * @return string Blog URL
     */
    public function blog_url() {
        return create_blog_url();
    }
    
    /**
     * Returns URL of a blog post.
     * 
     * @param PecBlogPost	$post: The blogpost object of which the URL has to be created
     * @return string Blogpost URL
     */
    public function blogpost_url($post) {
        return create_blogpost_url($post);
    }
    
    /**
     * Returns URL of a blog category.
     * 
     * @param	PecBlogCategory	$category: The blog category object of which the URL has to be created
     * @param	boolean			$home: Wether the URL should target to the start page or to the real blog. If "not_set", it automatically looks if the current site view is home or blog.
     * @return string Blog category URL
     */
    public function blogcategory_url($category, $home='not_set') {
    	// if the home-var is not set, we will automatically decide depending on the Site View
    	$home = $home == 'not_set' ? $this->data['site_view'] == SITE_VIEW_HOME : $home;
        return create_blogcategory_url($category, false, $home);
    }
    
    /**
     * Returns URL of a blog tag.
     * 
     * @param	PecBlogTag	$category: The blog tag object of which the URL has to be created
     * @param	boolean		$home: Wether the URL should target to the start page or to the real blog. If "not_set", it automatically looks if the current site view is home or blog.
     * @return string Blog tag URL
     */
    public function blogtag_url($tag, $home='not_set') {
    	$home = $home == 'not_set' ? $this->data['site_view'] == SITE_VIEW_HOME : $home;
        return create_blogtag_url($tag, false, $home);
    }
    
    /**
     * Returns URL of a blog archive.
     * 
     * @param	mixed	$day: The day of the archive
     * @param	mixed	$month: The month of the archive
     * @param	mixed	$year: The year of the archive
     * @param	boolean	$home: Wether the URL should target to the start page or to the real blog. If "not_set", it automatically looks if the current site view is home or blog.
     * @return string Blog archive URL
     */
    public function blogarchive_url($day, $month, $year, $home='not_set') {
    	$home = $home == 'not_set' ? $this->data['site_view'] == SITE_VIEW_HOME : $home;
        return create_blogarchive_url($day, $month, $year, false, $home);
    }
    
    
    /**
     * Generates form action URL for submitting a comment on a blog post.
     * 
     * @param	PecBlogPost	$post: The blog post object for which the URL has to be created
     * @return string Comment form submit URL
     */
    public function comment_submit_url($post) {
        return create_blogpost_url($post, true);
    }    
}

?>
