<?php

/**
 * pec_admin/pec_includes/admin_functions.inc.php - Misc admin functions
 * 
 * Defines miscelleanous functions for the admin frontend.
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
 * @subpackage	pec_admin.pec_includes
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.1
 * @link		http://pecio-cms.com
 */

function ckeditor_replace($textarea, $additional_options='') {
    global $pec_settings;
    $additional_options = !empty($additional_options) ? ',' . $additional_options : '';
    
    return '
        <script type="text/javascript">
        //<![CDATA[
           CKEDITOR.replace(\'' . $textarea . '\', {
               language: \'' . $pec_settings->get_locale() . '\', 
               filebrowserBrowseUrl : \'ckeditor/filebrowser/filemanager/index.php\',
               filebrowserWindowWidth : \'750\',
               filebrowserWindowHeight : \'500\'
               ' . $additional_options . '
           });
        //]]>
        </script>
    ';
}

?>