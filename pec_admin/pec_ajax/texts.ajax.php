<?php

/**
 * pec_admin/pec_ajax/texts.ajax.php - Saving texts via ajax
 * 
 * This file creates/saves a sidebar text. It is called via ajax.
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
 * @subpackage	pec_admin.pec_ajax
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */

/* core includes, creating core objects */
require_once('../../pec_includes/functions.inc.php');
require_once('common.inc.php');
require_once('../../pec_core.inc.php');
/* core include end */

if (!$pec_session->is_logged_in()) {
    die();
}

$area = array();
$area['permission_name'] = 'permission_texts';

$output = '';

if ($pec_session->get('pec_user')->get_permission($area['permission_name']) > PERMISSION_READ) {
    
    // CREATE
    if ($_POST['action'] == 'create') {        
        if (isset($_POST['text_id']) && isset($_POST['text_title']) && 
            isset($_POST['text_content']) && isset($_POST['text_visibility'])) {
            
            // if the text shall be visible on specific articles, 
            // we're converting the array of selected articles into the flat version for database
            if ($_POST['text_visibility'] == TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES) {
                if (isset($_POST['text_onarticles'])) {
                    $on_articles = array_to_flat($_POST['text_onarticles']);
                }
                else {
                    $on_articles = array();
                } 
            }
            
            $text = new PecSidebarText(NULL_ID, $_POST['text_title'], htmlentities($_POST['text_content']), $_POST['text_visibility'], $on_articles);
            $text->save();
            
            $output = PecMessageHandler::get('content_created', array(
                '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXT'),
                '{%NAME%}' => $text->get_title()
            ));
            
            $output .= '
                <script type="text/javascript">                
                    document.getElementById("text_id").value = "' . $text->get_id() . '";
                    document.getElementById("texts_edit_form").action = 
                        document.getElementById("texts_edit_form").action.replace("create", "save") + 
                        "&id=' . $text->get_id() . '";
                </script>
            ';
            
            
        }
    }
    
    // SAVE
    elseif ($_POST['action'] == 'save') {        
        if (isset($_POST['text_id']) && isset($_POST['text_title']) && 
            isset($_POST['text_content']) && isset($_POST['text_visibility'])) {
                
            if (PecSidebarText::exists('id', $_POST['text_id'])) {
                $text = PecSidebarText::load('id', $_POST['text_id']);
                $text->set_title($_POST['text_title']);
                $text->set_content($_POST['text_content']);
                $text->set_visibility($_POST['text_visibility']);
                if (isset($_POST['text_onarticles'])) {
                    $text->set_onarticles($_POST['text_onarticles'], TYPE_ARRAY);
                }
                $text->save();
                
                $output = PecMessageHandler::get('content_cached', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXT'),
                    '{%NAME%}' => $text->get_title()
                ));
            }
            else {
                $output = PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXT'),
                    '{%ID%}' => $_POST['text_id']
                ));
            }
            
        }
    }
}

echo $output;

?>