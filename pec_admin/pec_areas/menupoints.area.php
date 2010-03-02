<?php 

/**
 * pec_admin/pec_areas/menupoints.area.php - Managing menu points
 * 
 * Admin area to manage menu points.
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

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=menupoints');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_MENUPOINTS');
$area["permission_name"] = 'permission_menupoints';
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
            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_MENUPOINT'),
            '{%NAME%}' => $_GET['message_data'],
            '{%ID%}' => $_GET['message_data']
        ));
        
    }    
        
    if (isset($_GET['action'])) {
        // CREATE
        if ($_GET['action'] == 'create' && isset($_POST['menupoint_name']) && 
            isset($_POST['menupoint_root']) && isset($_POST['menupoint_target_type']) && 
            isset($_POST['menupoint_target_data_url']) && isset($_POST['menupoint_target_data_article'])) {
            
            // setting the target data depending on which target type has been chosen
            if ($_POST['menupoint_target_type'] == MENUPOINT_TARGET_ARTICLE) {
                $target_data = $_POST['menupoint_target_data_article'];
            }
            elseif ($_POST['menupoint_target_type'] == MENUPOINT_TARGET_URL) {
                $target_data = $_POST['menupoint_target_data_url'];
            }
            else {
                $target_data = '-';
            }
            
            // getting the superroot id
            if ($_POST['menupoint_root'] == 0) {
                $superroot_id = '0';
            }
            else {
                $root_menupoint = PecMenuPoint::load('id', $_POST['menupoint_root']);
                if ($root_menupoint->get_root_id() == 0) {
                    $superroot_id = $root_menupoint->get_id();
                }
                else {
                    $superroot_id = $root_menupoint->get_superroot_id();
                }
            }
                
            $menupoint = new PecMenuPoint(NULL_ID, $superroot_id, $_POST['menupoint_root'], $_POST['menupoint_name'], $_POST['menupoint_target_type'], $target_data);
            $menupoint->save();
            
            $messages .= PecMessageHandler::get('content_created', array(
                '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_MENUPOINT'),
                '{%NAME%}' => $menupoint->get_name()
            ));
        }
        
        // SAVE
        elseif ($_GET['action'] == 'save' && isset($_POST['menupoint_name']) && 
                isset($_POST['menupoint_root']) && isset($_POST['menupoint_target_type']) &&
                isset($_POST['menupoint_target_data_url']) && isset($_POST['menupoint_target_data_article'])) {
                    
            if (isset($_GET['id']) && PecMenuPoint::exists('id', $_GET['id'])) {   
                
                // setting the target data depending on which target type has been chosen
                if ($_POST['menupoint_target_type'] == MENUPOINT_TARGET_ARTICLE) {
                    $target_data = $_POST['menupoint_target_data_article'];
                }
                elseif ($_POST['menupoint_target_type'] == MENUPOINT_TARGET_URL) {
                    $target_data = $_POST['menupoint_target_data_url'];
                }
                else {
                    $target_data = '-';
                }
            
                // getting the superroot id
                if ($_POST['menupoint_root'] == 0) {
                    $superroot_id = '0';
                }
                else {
                    $root_menupoint = PecMenuPoint::load('id', $_POST['menupoint_root']);
                    if ($root_menupoint->get_root_id() == 0) {
                        $superroot_id = $root_menupoint->get_id();
                    }
                    else {
                        $superroot_id = $root_menupoint->get_superroot_id();
                    }
                }
                        
                $menupoint = PecMenuPoint::load('id', $_GET['id']);
                
                $menupoint->set_superroot_id($superroot_id);
                $menupoint->set_root_id($_POST['menupoint_root']);
                $menupoint->set_name($_POST['menupoint_name']);
                $menupoint->set_target($_POST['menupoint_target_type'], $target_data);
                
                $menupoint->save();
                
                $messages .= PecMessageHandler::get('content_edited', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_MENUPOINT'),
                    '{%NAME%}' => $menupoint->get_name()
                ));
            }
            else {
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_MENUPOINT'),
                    '{%ID%}' => ''
                ));
            }
        }
        
        // REMOVE
        elseif ($_GET['action'] == 'remove' && isset($_GET['id'])) {
            if (PecMenuPoint::exists('id', $_GET['id'])) {
                $menupoint = PecMenuPoint::load('id', $_GET['id']);
                $menupoint_name = $menupoint->get_name();
                $menupoint->remove();
                
                $messages .= PecMessageHandler::get('content_removed', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_MENUPOINT'),
                    '{%NAME%}' => $menupoint_name
                ));
            }
            else {                
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_MENUPOINT'),
                    '{%ID%}' => $_GET['id']
                ));
            }
        }
        
        // DEFAULT ACTIONS (REMOVE MULTIPLE, SORT)
        elseif ($_GET['action'] == 'default_view_actions') {
            // var to check if any menupoint has been sorted
            $sorted_something = false;
        
            // REMOVE MULTIPLE
            // if the remove button has been pressed, but not sent the form by clicking enter in a sort field
            if (isset($_POST['remove_menupoints']) && !isset($_POST['sort'])) {
                if (!empty($_POST['remove_box'])) {
                    
                    foreach ($_POST['remove_box'] as $menupoint_id) {
                        $menupoint = PecMenuPoint::load('id', $menupoint_id);
                        $menupoint->remove();
                    }
                              
                    $messages .= PecMessageHandler::get('content_removed_multiple', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_MENUPOINTS'),
                        '{%NAME%}' => ''
                    ));
                }
                else {
                    $messages .= PecMessageHandler::get('content_not_selected', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_MENUPOINTS'),
                        '{%NAME%}' => ''
                    ));
                }
            }
            
            // SORT
            else {
                if (!empty($_POST['sort_fields'])) {
                    foreach ($_POST['sort_fields'] as $key => $sort) {
                        // loading the extra data (id and origin sort value)
                        $extra_data = explode('-', $_POST['sort_extra_data'][$key]);                
                        $menupoint_id = $extra_data[0];
                        $menupoint_orig_sort= $extra_data[1];
                        
                        if ($sort != $menupoint_orig_sort) {
                            $sorted_something = true;
                            
                            $menupoint = PecMenuPoint::load('id', $menupoint_id);
                            $menupoint->set_sort($sort);
                            $menupoint->save();
                        }
                    }
                    
                    if ($sorted_something) {            
                        $messages .= PecMessageHandler::get('content_sorted', array(
                            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_MENUPOINTS')
                        ));
                    }
                }
            }            
        }
        
    }
    
    return $messages;
}


/* creating functions for all the different views that will be available for this area */

function view_edit() {
	global $pec_localization;
	
    $area_data = array();   
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_MENUPOINTS'); 
    
    if (isset($_GET['id'])) {
        if (PecMenuPoint::exists('id', $_GET['id'])) {
            $menupoint = PecMenuPoint::load('id', $_GET['id']);
            $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_MENUPOINTS_EDIT') . ' &raquo; ' . $menupoint->get_name();
            
            $action = 'save';
            $id_query_var = '&amp;id=' . $_GET['id'];
        }
        else {
            pec_redirect('pec_admin/' . AREA . '&message=content_not_found_id&message_data=' . $_GET['id']);
        }
    }
    else {
        // create an empty menupoint
        $menupoint = new PecMenuPoint(NULL_ID, NULL_ID, NULL_ID, '', 0, '');
        $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_MENUPOINTS_CREATE');
        
        $action = 'create';
        $id_query_var = '';
    }
    
    // set the checked state for the Target Type radio buttons
    $target_type_selected = array();
    $target_type_selected[MENUPOINT_TARGET_HOME] = $menupoint->get_target_type() == MENUPOINT_TARGET_HOME ? 'checked="checked"' : '';
    $target_type_selected[MENUPOINT_TARGET_ARTICLE] = $menupoint->get_target_type() == MENUPOINT_TARGET_ARTICLE ? 'checked="checked"' : '';
    $target_type_selected[MENUPOINT_TARGET_BLOG] = $menupoint->get_target_type() == MENUPOINT_TARGET_BLOG ? 'checked="checked"' : '';
    $target_type_selected[MENUPOINT_TARGET_URL]  = $menupoint->get_target_type() == MENUPOINT_TARGET_URL  ? 'checked="checked"' : '';
    
    // create the root MENUPOINT OPTIONS for the select box
    $root_mp_select_options = '<option value="0">----------</option>';
    $available_menupoints = PecMenuPoint::load();
    foreach ($available_menupoints as $mp) {
        if ($mp->get_id() != $menupoint->get_id()) {
            if ($menupoint->get_root_id() == $mp->get_id()) {
                $selected = 'selected="selected"';
            }
            else {
                $selected = '';
            }
            $root_mp_select_options .= '<option value="' . $mp->get_id() . '" ' . $selected . '>' . $mp->get_name() . '</option>';
        }
    }    
    
    // create the ARTICLE OPTIONS for the select box
    $null_value = $menupoint->get_target_type() == MENUPOINT_TARGET_ARTICLE ? $menupoint->get_target_data() : '-';
    $article_select_options = '<option value="' . $null_value . '">----------</option>';
    $available_articles = PecArticle::load();
    foreach ($available_articles as $a) {
        if ($menupoint->get_target_data() == $a->get_id()) {
            $selected = 'selected="selected"';
        }
        else {
            $selected = '';
        }
        
        $article_select_options .= '<option value="' . $a->get_id() . '" ' . $selected . '>' . $a->get_title() . '</option>';
    }
    
    // we need to define the string that is placed in the target_url input, 
    // because otherwise it would display the article id, if the target is an article
    $target_data_url      = $menupoint->get_target_type() == MENUPOINT_TARGET_URL  ? $menupoint->get_target_data() : ''; 
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&ampview=default&amp;action=' . $action . $id_query_var . '" id="menupoints_edit_form" />
            <h3>' . $pec_localization->get('LABEL_GENERAL_NAME') . ':</h3>
            <input type="text" size="75" name="menupoint_name" value="' . $menupoint->get_name() . '" />
            <br /><br />

            <div class="options_box_1 float_left" style="margin-right: 10px; height: 100px;">
                <h3>' . $pec_localization->get('LABEL_MENUPOINTS_ROOT') . ':</h3>
                <select name="menupoint_root">' . $root_mp_select_options . '</select>
                <br /><br />
            </div>
            
            <div class="options_box_1 float_left" style="height: 100px">
                <h3>' . $pec_localization->get('LABEL_MENUPOINTS_TARGET') . ':</h3>
                <table cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="75px">
                            <input type="radio" name="menupoint_target_type" id="target_home" value="' . MENUPOINT_TARGET_HOME . '" ' . $target_type_selected[MENUPOINT_TARGET_HOME] . ' />
                            <label for="target_home">' . $pec_localization->get('LABEL_MENUPOINTS_HOME') . '</label><br />
                        </td>
                        <td></td>
                    </tr>
                    
                    <tr>
                        <td valign="middle">
                            <input type="radio" name="menupoint_target_type" id="target_article" value="' . MENUPOINT_TARGET_ARTICLE . '" ' . $target_type_selected[MENUPOINT_TARGET_ARTICLE] . ' />
                            <label for="target_article">' . $pec_localization->get('LABEL_MENUPOINTS_ARTICLE') . ':</label><br />
                        </td>
                        <td>                        
                            <select name="menupoint_target_data_article" onclick="document.getElementById(\'target_article\').checked = \'checked\';" >' . $article_select_options . '</select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <input type="radio" name="menupoint_target_type" id="target_blog" value="' . MENUPOINT_TARGET_BLOG . '" ' . $target_type_selected[MENUPOINT_TARGET_BLOG] . ' />
                            <label for="target_blog">' . $pec_localization->get('LABEL_MENUPOINTS_BLOG') . '</label><br />
                        </td>
                        <td></td>
                    </tr>
                    
                    <tr><td></td><td></td></tr>
                    
                    <tr>
                        <td valign="middle">
                            <input type="radio" name="menupoint_target_type" id="target_url" value="' . MENUPOINT_TARGET_URL . '" ' . $target_type_selected[MENUPOINT_TARGET_URL] . ' />
                            <label for="target_url">' . $pec_localization->get('LABEL_MENUPOINTS_URL') . ':</label>
                        </td>
                        <td>
                            <input type="text" name="menupoint_target_data_url" value="' . $target_data_url . '" size="58" onclick="document.getElementById(\'target_url\').checked = \'checked\';" />
                        </td>
                    </tr>
                        
                    
                </table>
            </div>
            
            <div style="clear: left;"></div>
            
            <br /><br />
            
            <input type="submit" value="' . $pec_localization->get('BUTTON_SAVE') . '" />
            <a href="' . AREA . '"><input type="button" onclick="location.href=\'' . AREA . '\'" value="' . $pec_localization->get('BUTTON_CANCEL') . '" /></a>
        </form>            
    ';
    
    return $area_data;
}

function view_default() {
	global $pec_localization;
	  
    $area_data = array();
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_MENUPOINTS');
    
    $menupoints = PecMenuPoint::load(0, false, 'ORDER BY point_sort');
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&amp;view=default&amp;action=default_view_actions" id="menupoints_main_form"/>
            <input type="button" value="' . $pec_localization->get('BUTTON_NEW_MENUPOINT') . '" onclick="location.href=\'' . AREA . '&amp;view=edit\'"/> 
            
            <input type="submit" name="sort" value="" style="display: none;"/>
            <input type="submit" name="remove_menupoints" value="' . $pec_localization->get('BUTTON_REMOVE') . '" onclick="return confirm(\'' . $pec_localization->get('LABEL_MENUPOINTS_REALLYREMOVE_SELECTED') . '\');" />
            <input type="submit" name="sort_menupoints" value="' . $pec_localization->get('BUTTON_SORT') . '" /><br /><br />
            
            <table class="data_table" cellspacing="0">
                <thead>
                    <tr class="head_row">
                        <th class="check_column"><input type="checkbox" onclick="checkbox_mark_all(\'remove_box\', \'menupoints_main_form\', this);" /></th>
                        <th class="long_column">' . $pec_localization->get('LABEL_GENERAL_NAME') . '</th>
                        <th class="medium_column">' . $pec_localization->get('LABEL_MENUPOINTS_ROOT') . '</th>
                        <th class="medium_column">' . $pec_localization->get('LABEL_MENUPOINTS_TARGET') . '</th>
                        <th class="thin_column">' . $pec_localization->get('LABEL_GENERAL_SORT') . '</th>
                    <tr>
                </thead>
                <tbody>
    ';
    
    foreach ($menupoints as $m) {
        switch ($m->get_target_type()) {
            case MENUPOINT_TARGET_HOME: $target_type = $pec_localization->get('LABEL_MENUPOINTS_HOME');         $target_data = '-'; break;
            case MENUPOINT_TARGET_ARTICLE: $target_type = $pec_localization->get('LABEL_MENUPOINTS_ARTICLE') . ':';     $target_data = PecArticle::exists('id', $m->get_target_data()) ? PecArticle::load('id', $m->get_target_data())->get_title() : '<del>' . $m->get_target_data() . '</del>'; break;
            case MENUPOINT_TARGET_BLOG: $target_type = $pec_localization->get('LABEL_MENUPOINTS_BLOG');         $target_data = '-'; break;
            case MENUPOINT_TARGET_URL:  $target_type = $pec_localization->get('LABEL_MENUPOINTS_URL') . ':';         $target_data = $m->get_target_data(); break;
            default: $target_type = '-'; $target_data = '-'; break;
        }
        
        // loading the root menupoint if there is any
        $root_menupoint_obj = $m->get_root_menupoint();
        if ($root_menupoint_obj) {
            $root_menupoint = $root_menupoint_obj->get_name();
        }
        else {
            $root_menupoint = '-';
        }
        
        $area_data['content'] .= '
                    <tr class="data_row" title="#' . $m->get_id() . '">
                        <td class="check_column"><input type="checkbox" class="remove_box" name="remove_box[]" value="' . $m->get_id() . '" /></td>
                        <td class="normal_column">
                            <a href="' . AREA . '&amp;view=edit&amp;id=' . $m->get_id() . '"><span class="main_text">' . $m->get_name() . '</span></a>
                            <div class="row_actions">
                                <a href="' . AREA . '&amp;view=edit&amp;id=' . $m->get_id() . '">' . $pec_localization->get('ACTION_EDIT') . '</a> - 
                                <a href="javascript:ask(\'' . $pec_localization->get('LABEL_MENUPOINTS_REALLYREMOVE') . '\', \'' . AREA . '&amp;view=default&amp;action=remove&amp;id=' . $m->get_id() . '\');">
                                    ' . $pec_localization->get('ACTION_REMOVE') . '
                                </a>
                            </div>
                        </td>
                        <td class="normal_column">' . $root_menupoint . '</td>
                        <td class="normal_column">' . $target_type . '<br /><i>' . $target_data . '</i></td>
                        <td class="check_column">
                            <input type="text" size="2" name="sort_fields[]" value="' . $m->get_sort() . '" class="sort_input" />                                   
                            <input type="hidden" name="sort_extra_data[]" value="' . $m->get_id() . '-' . $m->get_sort() . '" />
                        </td>
                    </tr>
        ';
    }
    
    $area_data['content'] .= '
    			</tbody>
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
    case 'edit': 
        $area_data = view_edit(); 
        $area['title'] = $area_data['title'];
        $area['content'] = $area_data['content'];
        break;
        
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