<?php

/**
 * pec_core.inc.php - Core include file
 * 
 * Makes the main includes required for the program.
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
 * @version		2.0.2
 * @link		http://pecio-cms.com
 */

require_once(RELATIVE_BACK . '_pec_config.inc.php');
require_once(RELATIVE_BACK . 'pec_classes/database.class.php');

require_once(RELATIVE_BACK . 'pec_classes/session.class.php');

require_once(RELATIVE_BACK . 'pec_classes/settings.class.php');

require_once(RELATIVE_BACK . 'pec_classes/locale.class.php');

require_once(RELATIVE_BACK . 'pec_classes/message-handler.class.php');
require_once(RELATIVE_BACK . 'pec_includes/messages.inc.php');

require_once(RELATIVE_BACK . 'pec_classes/article.class.php');

require_once(RELATIVE_BACK . 'pec_classes/blog-post.class.php');
require_once(RELATIVE_BACK . 'pec_classes/blog-comment.class.php');
require_once(RELATIVE_BACK . 'pec_classes/blog-category.class.php');
require_once(RELATIVE_BACK . 'pec_classes/blog-tag.class.php');

require_once(RELATIVE_BACK . 'pec_classes/menupoint.class.php');

require_once(RELATIVE_BACK . 'pec_classes/sidebartext.class.php');
require_once(RELATIVE_BACK . 'pec_classes/sidebarlink.class.php');
require_once(RELATIVE_BACK . 'pec_classes/sidebarlink-category.class.php');

require_once(RELATIVE_BACK . 'pec_classes/user.class.php');

require_once(RELATIVE_BACK . 'pec_classes/template.class.php');

require_once(RELATIVE_BACK . 'pec_classes/abstract-plugin.class.php');
require_once(RELATIVE_BACK . 'pec_classes/plugin.class.php');

require_once(RELATIVE_BACK . 'pec_includes/urls.inc.php');
require_once(RELATIVE_BACK . 'pec_includes/counter.inc.php');
require_once(RELATIVE_BACK . 'pec_includes/feed-creator/feedcreator.class.php');

if (!defined('INSTALLATION')) {
	$pec_database = new PecDatabase(DB_HOST, DB_USER, DB_PW, DB_NAME, DB_TYPE);
    $pec_settings = PecSetting::load();
    
    $pec_localization = new PecLocale($pec_settings->get_locale());
    $pec_session = new PecSession();
    
	$pec_messages = generate_messages();
}
else {
    // locale we can get from installation start screen
    $pec_localization = new PecLocale('en');

    // permissions the directories and files need to have (r = recursive, nr = not recursive, f = file)
    $pec_core_permissions = array(
        './' => array(
        	'permission_before_install' => '777',
        	'permission_after_install' => '777',
        	'type' => 'nr',
        	'display' => 'main'
    	),
    	
        'pec_admin/' => array(
        	'permission_before_install' => '777',
        	'permission_after_install' => '777',
        	'type' => 'nr',
        	'display' => 'main'
    	),
    	
        'pec_upload/' => array(
        	'permission_before_install' => '777',
        	'permission_after_install' => '777',
        	'type' => 'r',
        	'display' => 'main'
    	),
    	
        'pec_feeds/' => array(
        	'permission_before_install' => '777',
        	'permission_after_install' => '777',
        	'type' => 'r',
        	'display' => 'main'
    	),
    	
        'pec_feeds/blog/' => array(
        	'permission_before_install' => '777',
        	'permission_after_install' => '777',
        	'type' => 'r',
        	'display' => 'sub'
    	),
    	
        'pec_feeds/categories/' => array(
        	'permission_before_install' => '777',
        	'permission_after_install' => '777',
        	'type' => 'r',
        	'display' => 'sub'
    	),
    	
        'pec_feeds/tags/' => array(
        	'permission_before_install' => '777',
        	'permission_after_install' => '777',
        	'type' => 'r',
        	'display' => 'sub'
    	),
    	
        'counter.txt' => array(
        	'permission_before_install' => '777',
        	'permission_after_install' => '777',
        	'type' => 'f',
        	'display' => 'main'
    	),
    	
        'htaccess-sample' => array(
        	'permission_before_install' => '777',
        	'permission_after_install' => '644',
        	'type' => 'f',
        	'display' => 'main'
    	),
    	
        '_pec_config.inc.php' => array(
        	'permission_before_install' => '777',
        	'permission_after_install' => '644',
        	'type' => 'f',
        	'display' => 'main'
    	)
    );
}

?>
