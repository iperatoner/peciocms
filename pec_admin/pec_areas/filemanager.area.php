<?php 

/**
 * pec_admin/pec_areas/filemanager.area.php - Filemanager area
 * 
 * Admin area which includes the file manager also used in CKEditor
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
 * @subpackage	pec_admin.pec_areas
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.2
 * @link		http://pecio-cms.com
 */

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=filemanager');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_FILEMANAGER');
$area["permission_name"] = 'permission_articles|permission_texts';
$area["head_data"] = '';
$area["messages"] = '';
$area["content"] = 'No view was executed.';


/* a function that does actions depending on what data is in the query string */

function do_actions() {
    $messages = '';
    return $messages;
}


/* creating functions for all the different views that will be available for this area */

function view_default() {
	global $pec_localization;
	
    $area_data = array();
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_FILEMANAGER');

    // Overview Start
    $area_data['content'] = '
        <iframe width="100%" height="800" style="margin-left: -8px;" frameborder="0" src="ckeditor/filebrowser/filemanager/index.php"></iframe>
    ';

    return $area_data;
}


/* doing all the actions and then display the view given in the query string */
$area['messages'] = do_actions();

switch ($_GET['view']) {
    case 'default':
        $area_data = view_default();
        $area['title'] = $area_data['title'];
        $area['content'] = $area_data['content'];
        break;
        
    default:
        $area_data = view_default(); 
        $area['title'] = $area_data['title'];
        $area['content'] = $area_data['content'];
        break;
}

?>
