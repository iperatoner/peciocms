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

$start_time = microtime(true);

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

require_once('pec_classes/abstract/abstract-resource.class.php');
require_once('pec_includes/controller/template-resource.class.php');

require_once('pec_classes/abstract/abstract-handler.class.php');
require_once('pec_includes/controller/handlers/article-handler.class.php');
require_once('pec_includes/controller/handlers/blog-handler.class.php');
require_once('pec_includes/controller/handlers/sidebar-handler.class.php');
require_once('pec_includes/controller/handlers/menu-handler.class.php');
require_once('pec_includes/controller/handlers/plugin-handler.class.php');

// increase the visitor counter
count_site_visit();

$query_target = isset($_GET['target']) && !empty($_GET['target']) 
	? $_GET['target']
	: false;

$controller = new PecSiteController(&$query_target);

$article_handler = new PecArticleHandler();
$blog_handler = new PecBlogHandler();
$sidebar_handler = new PecSidebarHandler();
$menu_handler = new PecMenuHandler();
$plugin_handler = new PecPluginHandler();

$controller->add_handler(&$article_handler);
$controller->add_handler(&$blog_handler);
$controller->add_handler(&$sidebar_handler);
$controller->add_handler(&$menu_handler);
$controller->add_handler(&$plugin_handler);

$controller->apply_handlers();

$controller->display();



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
 */

echo '<!-- generated in: ' . (microtime(true) - $start_time) . ' seconds -->';
?>
