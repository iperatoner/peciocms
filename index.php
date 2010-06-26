<?php

/**
 * index.php - The main frontend file
 * 
 * Creates and handles the main controller objects which 
 * handle the current view and its content.
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
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */

$start_time = microtime();

/* core includes, creating core objects */
require_once('pec_includes/functions.inc.php');
require_once('common.inc.php');

if (!file_exists(PEC_VERSION_FILE)){
	pec_redirect('pec_install/install.php');
}

require_once('pec_core.inc.php');
/* core include end */

if (file_exists('pec_install')) {
	die(PecMessageHandler::get('install_directory_remove_required'));
}

require_once('pec_classes/search.class.php');
require_once('pec_includes/controller/site-controller.class.php');
require_once('pec_includes/controller/resource-generator.class.php');
require_once('pec_includes/controller/template-resource.class.php');

// increase the visitor counter
count_site_visit();

// needed to decide if a menupoint is active or not
$current_target_type = MENUPOINT_TARGET_HOME;
$current_target_data = '-';

// the current view. can be e.g. home, article, blog, category, tag, ...
$site_view = '';
$current_view_data = '';

// sub-site-view is needed, if the blog is on the start page and e.g. a category or tag is given
$sub_site_view = '';

// blog has got different views (category, tag, default, ...)
$site_view = SITE_VIEW_HOME;

if (isset($_GET['target']) && !empty($_GET['target'])) {
    
    
    // ARTICLE
    
    if ($_GET['target'] == QUERY_TARGET_ARTICLE) {
        $current_target_type = MENUPOINT_TARGET_ARTICLE;
        $current_target_data = $_GET['id'];
        $current_view_data = $_GET['id'];        
        $site_view = SITE_VIEW_ARTICLE;
        $sub_site_view = SITE_VIEW_ARTICLE;
    }
    
    
    // SEARCH
    
    elseif ($_GET['target'] == QUERY_TARGET_SEARCH) {
        $current_target_type = MENUPOINT_TARGET_ARTICLE;
        $current_view_data = $_GET['term'];        
        $site_view = SITE_VIEW_SEARCH;
        $sub_site_view = SITE_VIEW_SEARCH;
    }
    
    
    // BLOG
    
    elseif ($_GET['target'] == QUERY_TARGET_BLOG) {
        $current_target_type = MENUPOINT_TARGET_BLOG;
        
        if (isset($_GET['post_id'])) {
            $site_view = SITE_VIEW_BLOGPOST;
            $sub_site_view = SITE_VIEW_BLOGPOST;            
            $current_view_data = $_GET['post_id'];
        }
        elseif (isset($_GET['category'])) {
            $site_view = SITE_VIEW_BLOGCATEGORY;
            $sub_site_view = SITE_VIEW_BLOGCATEGORY;            
            $current_view_data = $_GET['category'];
        }
        elseif (isset($_GET['tag'])) {
            $site_view = SITE_VIEW_BLOGTAG;
            $sub_site_view = SITE_VIEW_BLOGTAG;            
            $current_view_data = $_GET['tag'];
        }
        elseif (isset($_GET['day']) || isset($_GET['month']) || isset($_GET['year'])) {
            $site_view = SITE_VIEW_BLOGARCHIVE;
            $sub_site_view = SITE_VIEW_BLOGARCHIVE;
        }
        else {
            $site_view = SITE_VIEW_BLOG;
            $sub_site_view = SITE_VIEW_BLOG;
        }
    }
    
    
    // HOME
    
    else {
        // menupoints are using numbers as target type 
        $current_target_type = MENUPOINT_TARGET_HOME;
        
        $site_view = SITE_VIEW_HOME;
        $sub_site_view = SITE_VIEW_HOME;
        
        if ($pec_settings->get_blog_onstart()) {
            if (isset($_GET['category'])) {
                $sub_site_view = SITE_VIEW_BLOGCATEGORY;
                $current_view_data = $_GET['category'];
            }
            elseif (isset($_GET['tag'])) {
                $sub_site_view = SITE_VIEW_BLOGTAG;
                $current_view_data = $_GET['tag'];
            }
            elseif (isset($_GET['day']) || isset($_GET['month']) || isset($_GET['year'])) {
                $sub_site_view = SITE_VIEW_BLOGARCHIVE;
            }
            else {
                $sub_site_view = SITE_VIEW_BLOG;
            }
        }
    }
}
    
    
// HOME

else {
    // menupoints are using numbers as target type 
    $current_target_type = MENUPOINT_TARGET_HOME;
    
    $site_view = SITE_VIEW_HOME;
    $sub_site_view = SITE_VIEW_HOME;
    
    if ($pec_settings->get_blog_onstart()) {
        if (isset($_GET['category'])) {
            $sub_site_view = SITE_VIEW_BLOGCATEGORY;
            $current_view_data = $_GET['category'];
        }
        elseif (isset($_GET['tag'])) {
            $sub_site_view = SITE_VIEW_BLOGTAG;
            $current_view_data = $_GET['tag'];
        }
        elseif (isset($_GET['day']) || isset($_GET['month']) || isset($_GET['year'])) {
            $sub_site_view = SITE_VIEW_BLOGARCHIVE;
        }
        else {
            $sub_site_view = SITE_VIEW_BLOG;
        }
    }
}

// CREATE ALL THOSE NEW MANAGERS HERE AND PUT THEM INTO THE SITE CONTROLLER
/*
 * Some ideas for the new managers:
 * 
 * PecManager has a method `grab_objects`. PecManager also has all the current page data.
 * so `grab_objects` would update the `current_objects` array with array_replace if we're on the correct view
 * The PecArticleManager e.g. would update the `article`-key with a 404 Article if the current view data doesnt match any articles
 * 
 */


/*
 * Just to explain it _one_ time:
 * 
 * $current_target_type  <->  a target that can be used by menupoints: home|article|blog|url
 * $current_target_data  <->  data that belongs to the target. also specified by menupoints. 
 * 							  NOT mandatory, else "-" (e.g. if target type is "blog")
 * 
 * $site_view  <->  the type of page that is currently being viewed. 
 * 					that can be different from the menupoint's target type, 
 * 					because a menupoint can't e.g. target to a category. 
 * 					possible values: 
 * 					home|article|search|404|blog|blogpost|blogcategory|blogtag|blogarchive
 * 
 * $sub_site_view  <->  the type of the sub-page that is currently being viewed.
 * 						this one is needed because there can be a second view
 * 						if we're on the home page.
 * 						Then there can be e.g. an article and a blog category.
 * 						Same possible values as $site_view
 * 
 * $current_view_data  <->  data that belongs to the current view. 
 * 							That can be a blog tag id, a search term or an 
 * 							article id (in this case it would be the same as current_target_data, 
 * 							because articles can be targeted by a menupoint)
 * 
 * 
 * QUERY TARGETS are just target types that can be _in_ the actual URL. so possible values are: home|article|blog|search
 * 
 * TODO: put them together in a $current_page array from the beginning
 * TODO: put the processing ifs, elses, elseifs etc into functions or methods
 * TODO: perhaps create the site controller at the beginning and then something like `grab_view_data($query_target)`. this could replace all those ifs and elses
 * 
 */

$current_page = array(
	'target' => array(
		'type' => $current_target_type,
		'data' => $current_target_data
	),
	'view' => array(
		'main' => $site_view,
		'sub' => $sub_site_view,
		'data' => $current_view_data
	)
);

$controller = new PecSiteController($current_target_type, $current_target_data, $site_view, $sub_site_view, $current_view_data);
$controller->prepare_view();
$controller->display();

echo '<!-- generated in: ' . (microtime() - $start_time) . ' seconds -->';
?>
