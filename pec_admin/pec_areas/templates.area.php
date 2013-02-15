<?php 

/**
 * pec_admin/pec_areas/articles.area.php - Managing templates
 * 
 * Admin area to manage templates.
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
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=templates');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_TEMPLATES');
$area["permission_name"] = 'permission_templates';
$area["head_data"] = '';
$area["messages"] = '';
$area["content"] = 'No view was executed.';


/* a function that does actions depending on what data is in the query string */

function do_actions() {
    global $pec_localization;
    
    $messages = '';
    
    // costum message
    if (isset($_GET['message']) && !empty($_GET['message']) && 
        isset($_GET['message_data']) && !empty($_GET['message_data']) && 
        PecMessageHandler::exists($_GET['message'])) {  
                    
        $messages .= PecMessageHandler::get($_GET['message'], array(
            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEMPLATE'),
            '{%NAME%}' => $_GET['message_data'],
            '{%ID%}' => $_GET['message_data']
        ));
        
    }
        
    if (isset($_GET['action'])) {
        
        // ACTIVATE
        if ($_GET['action'] == 'activate' && isset($_POST['activate_box'])) {
            if (PecTemplate::exists('id', $_POST['activate_box'])) {
                global $pec_settings;
                
                $tpl = PecTemplate::load('id', $_POST['activate_box']);
                $pec_settings->set_template_id($_POST['activate_box']);
                $pec_settings->save();
                
                $messages .= PecMessageHandler::get('content_activated', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEMPLATE'),
                    '{%NAME%}' => $tpl->get_property('title')
                ));
            }
            else {
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEMPLATE'),
                    '{%ID%}' => $_POST['activate_box']
                ));
            }
        }
        
    }
    
    return $messages;
}


/* creating functions for all the different views that will be available for this area */

function view_edit() {}

function view_default() {
    global $pec_settings, $pec_localization;
    
    $area_data = array();
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_TEMPLATES');    
    
    $templates = PecTemplate::load();
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&amp;view=default&amp;action=activate" id="templates_main_form" />
            <input type="submit" name="remove_articles" value="' . $pec_localization->get('BUTTON_ACTIVATE') . '" /><br /><br />
            
            <table class="data_table">
                <tr class="head">
                    <td class="check"></th>
                    <td class="long">' . $pec_localization->get('LABEL_TEMPLATES_TEMPLATENAME') . '</td>
                    <td class="medium">' . $pec_localization->get('LABEL_TEMPLATES_AUTHOR') . '</td>
                    <td class="short">' . $pec_localization->get('LABEL_TEMPLATES_YEAR') . '</td>
                    <td class="short">' . $pec_localization->get('LABEL_TEMPLATES_LICENSE') . '</td>
                <tr>
    ';
    
    foreach ($templates as $t) {
        $checked = $t->get_property('id') == $pec_settings->get_template_id() ? 'checked="checked"' : '';
        $area_data['content'] .= '
                <tr class="data" 
                    title="ID: ' . $t->get_property('id') . '" 
                    onclick="document.getElementById(\'activate_box_' . $t->get_property('id') . '\').checked = \'checked\';">
                    
                    <td class="check">
                        <input type="radio" id="activate_box_' . $t->get_property('id') . '" 
                               name="activate_box" value="' . $t->get_property('id') . '" ' . $checked . ' />
                    </td>
                    <td class="long">
                        <span class="main_text">' . $t->get_property('title') . '</span><br />
                        ' . $t->get_property('description') . '
                    </td>
                    <td class="medium">
                        ' . $t->get_property('author') . '<br />
                        <em>' . $t->get_property('author_email') . '</em>
                    </td>
                    <td class="short">' . $t->get_property('year') . '</td>
                    <td class="short">' . $t->get_property('license') . '</td>
                    
                </tr>
        ';
    }
    
    $area_data['content'] .= '
            </table>
        </form>
    ';
    
    return $area_data;
}


/* doing all the actions and then display the view given in the query string */

if ($pec_session->get('pec_user')->get_permission($area['permission_name']) > PERMISSION_READ) {
    $area['messages'] = do_actions();
}

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

// append a "(Read-only)" if the user is only allowed to view the area
if ($pec_session->get('pec_user')->get_permission($area['permission_name']) < PERMISSION_FULL) {
    $area['title'] .= ' (' . $pec_localization->get('LABEL_GENERAL_READONLY') . ')';
}

?>
