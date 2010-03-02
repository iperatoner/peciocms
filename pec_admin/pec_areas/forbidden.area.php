<?php 

/**
 * pec_admin/pec_areas/forbidden.area.php - Forbidden area
 * 
 * Admin area which is displayed if a user does not have the permission to view the given area.
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
 * @version		2.0.1
 * @link		http://pecio-cms.com
 */

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=forbidden');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_AREADENIED_TITLE');
$area["permission_name"] = '';
$area["head_data"] = '';
$area["messages"] = '';
$area["content"] = $pec_localization->get('LABEL_GENERAL_AREADENIED_TEXT');

?>