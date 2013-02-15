<?php 

/**
 * pec_admin/pec_areas/texts.area.php - Managing sidebar texts
 * 
 * Admin area to manage sidebar texts.
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

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=texts');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_TEXTS');
$area["permission_name"] = 'permission_texts';
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
            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXT'),
            '{%NAME%}' => $_GET['message_data'],
            '{%ID%}' => $_GET['message_data']
        ));
        
    }    
        
    if (isset($_GET['action'])) {
        // CREATE
        if ($_GET['action'] == 'create' && isset($_POST['text_title']) && 
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
            
            $messages .= PecMessageHandler::get('content_created', array(
                '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXT'),
                '{%NAME%}' => $text->get_title()
            ));
        }
        
        // SAVE
        elseif ($_GET['action'] == 'save' && isset($_POST['text_title']) && isset($_POST['text_content']) &&
                isset($_POST['text_visibility'])) {
                    
            if (isset($_GET['id']) && PecSidebarText::exists('id', $_GET['id'])) {                
                $text = PecSidebarText::load('id', $_GET['id']);
                $text->set_title($_POST['text_title']);
                $text->set_content($_POST['text_content']);
                $text->set_visibility($_POST['text_visibility']);
                if (isset($_POST['text_onarticles'])) {
                    $text->set_onarticles($_POST['text_onarticles'], TYPE_ARRAY);
                }
                $text->save();
                
                $messages .= PecMessageHandler::get('content_edited', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXT'),
                    '{%NAME%}' => $text->get_title()
                ));
            }
            else {
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXT'),
                    '{%ID%}' => ''
                ));
            }
        }
        
        // REMOVE
        elseif ($_GET['action'] == 'remove' && isset($_GET['id'])) {
            if (PecSidebarText::exists('id', $_GET['id'])) {
                $text = PecSidebarText::load('id', $_GET['id']);
                $text_title = $text->get_title();
                $text->remove();
                
                $messages .= PecMessageHandler::get('content_removed', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXT'),
                    '{%NAME%}' => $text_title
                ));
            }
            else {                
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXT'),
                    '{%ID%}' => $_GET['id']
                ));
            }
        }
        
        // DEFAULT ACTIONS (REMOVE MULTIPLE, SORT)
        elseif ($_GET['action'] == 'default_view_actions') {
            // var to check if any text has been sorted
            $sorted_something = false;
        
            // REMOVE MULTIPLE
            if (isset($_POST['remove_texts']) && !isset($_POST['sort'])) {
                if (!empty($_POST['remove_box'])) {
                    
                    foreach ($_POST['remove_box'] as $text_id) {
                        $text = PecSidebarText::load('id', $text_id);
                        $text->remove();
                    }
                              
                    $messages .= PecMessageHandler::get('content_removed_multiple', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXTS'),
                        '{%NAME%}' => ''
                    ));
                }
                else {
                    $messages .= PecMessageHandler::get('content_not_selected', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXTS'),
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
                        $text_id = $extra_data[0];
                        $text_orig_sort= $extra_data[1];
                        
                        if ($sort != $text_orig_sort) {
                            $sorted_something = true;
                            
                            $text = PecSidebarText::load('id', $text_id);
                            $text->set_sort($sort);
                            $text->save();
                        }
                    }
                    
                    if ($sorted_something) {            
                        $messages .= PecMessageHandler::get('content_sorted', array(
                            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_TEXTS')
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
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_TEXTS');
    $area_data["head_data"] = '<script type="text/javascript" src="pec_style/js/ajax/texts.js"></script>';
    
    if (isset($_GET['id'])) {
        if (PecSidebarText::exists('id', $_GET['id'])) {
            $text = PecSidebarText::load('id', $_GET['id']);
            $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_TEXTS_EDIT') . ' &raquo; ' . $text->get_title();
            
            $action = 'save';
            $id_query_var = '&amp;id=' . $_GET['id'];
        }
        else {
            pec_redirect('pec_admin/' . AREA . '&message=content_not_found_id&message_data=' . $_GET['id']);
        }
    }
    else {
        // create an empty text
        $text = new PecSidebarText(NULL_ID, '', '', 0, '');
        $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_TEXTS_CREATE');
        
        $action = 'create';
        $id_query_var = '';
    }
    
    // set the checked state for the visibility radio buttons
    $visibility_selected = array();
    $visibility_selected[TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES] = $text->get_visibility() == TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES ? 'checked="checked"' : '';
    $visibility_selected[TEXT_VISIBILITY_ON_ALL_ARTICLES]      = $text->get_visibility() == TEXT_VISIBILITY_ON_ALL_ARTICLES      ? 'checked="checked"' : '';
    $visibility_selected[TEXT_VISIBILITY_ON_BLOG]              = $text->get_visibility() == TEXT_VISIBILITY_ON_BLOG              ? 'checked="checked"' : '';
    $visibility_selected[TEXT_VISIBILITY_EVERYWHERE]           = $text->get_visibility() == TEXT_VISIBILITY_EVERYWHERE           ? 'checked="checked"' : '';
    
    // create the article options for the select box
    $article_checkboxes = '';
    $available_articles = PecArticle::load();
    foreach ($available_articles as $a) {
        if ($text->is_on_article($a)) {
            $checked = 'checked="checked"';
        }
        else {
            $checked = '';
        }
        
        $article_checkboxes .= '
            <div class="checkbox_data_row" id="article_row_' . $a->get_id() . '">
                <input type="checkbox" name="text_onarticles[]" id="article_' . $a->get_id() . '" value="' . $a->get_id() . '" ' . $checked . ' /> 
                    <label for="article_' . $a->get_id() . '">
                        ' . $a->get_title() . '
                    </label>
                <br />                
            </div>
        ';
    }
    
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&ampview=default&amp;action=' . $action . $id_query_var . '" id="texts_edit_form" name="texts_edit_form" />
            <h3>' . $pec_localization->get('LABEL_GENERAL_TITLE') . ':</h3>
            <input type="text" size="75" name="text_title" id="text_title" value="' . $text->get_title() . '" />
            <br /><br />
            
            <textarea name="text_content" id="text_content">' . htmlentities($text->get_content()) . '</textarea>
            <br /><br />
            
            <div class="options_box_1 float_left">
                <h3>' . $pec_localization->get('LABEL_TEXTS_VISIBILITY') . ':</h3>
                <table cellspacing="0" cellpadding="0" width="330px">
                    <tr>
                        <td>
                            <input type="radio" name="text_visibility" id="visibility_everywhere" value="' . TEXT_VISIBILITY_EVERYWHERE . '" ' . $visibility_selected[TEXT_VISIBILITY_EVERYWHERE] . ' />
                            <label for="visibility_everywhere">' . $pec_localization->get('LABEL_TEXTS_EVERYWHERE') . '</label><br />
                        </td>
                        <td></td>
                    </tr>
                    
                    <tr>
                        <td>
                            <input type="radio" name="text_visibility" id="visibility_all_articles" value="' . TEXT_VISIBILITY_ON_ALL_ARTICLES . '" ' . $visibility_selected[TEXT_VISIBILITY_ON_ALL_ARTICLES] . ' />
                            <label for="visibility_all_articles">' . $pec_localization->get('LABEL_TEXTS_ALLARTICLES') . '</label><br />
                        </td>
                        <td></td>
                    </tr>
                    
                    <tr>
                        <td>
                            <input type="radio" name="text_visibility" id="visibility_blog" value="' . TEXT_VISIBILITY_ON_BLOG . '" ' . $visibility_selected[TEXT_VISIBILITY_ON_BLOG] . ' />
                            <label for="visibility_blog">' . $pec_localization->get('LABEL_TEXTS_BLOG') . '</label><br />
                        </td>
                        <td></td>
                    </tr>
                    
                    <tr>
                        <td valign="top">
                            <input type="radio" name="text_visibility" id="visibility_specific" value="' . TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES . '" ' . $visibility_selected[TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES] . ' />
                            <label for="visibility_specific">' . $pec_localization->get('LABEL_TEXTS_SPECIFICARTICLES') . ':</label>
                        </td>
                        <td>
                            <label for="visibility_specific">
                                <div class="checkbox_data_selector" id="article_selector" style="padding: 5px !important;">
                                    ' . $article_checkboxes . '
                                </div>
                            </label>
                        </td>
                    </tr>
                        
                    
                </table>
            </div>
            <div style="clear: left;"></div>
            
            <br /><br />
            
            <input type="hidden" name="text_id" id="text_id" value="' . $text->get_id() . '" />
            <input type="submit" value="' . $pec_localization->get('BUTTON_SAVE') . '" />
            <input type="button" value="' . $pec_localization->get('BUTTON_APPLY') . '" id="text_apply_button" /> 
            <a href="' . AREA . '"><input type="button" onclick="location.href=\'' . AREA . '\'" value="' . $pec_localization->get('BUTTON_CANCEL') . '" /></a>
        </form>            
    ';

    // replace textarea by ckeditor
    $area_data['content'] .= ckeditor_replace('text_content');
    
    return $area_data;
}

function view_default() {
	global $pec_localization;
	
    $area_data = array();
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_TEXTS');
    
    $texts = PecSidebarText::load(0, false, 'ORDER BY text_sort');
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&amp;view=default&amp;action=default_view_actions" id="texts_main_form"/>
            <input type="button" value="' . $pec_localization->get('BUTTON_NEW_TEXT') . '" onclick="location.href=\'' . AREA . '&amp;view=edit\'"/> 
            
            <input type="submit" name="sort" value="" style="display: none;"/>
            <input type="submit" name="remove_texts" value="' . $pec_localization->get('BUTTON_REMOVE') . '" onclick="return confirm(\'' . $pec_localization->get('LABEL_TEXTS_REALLYREMOVE_SELECTED') . '\');" />
            <input type="submit" name="sort_texts" value="' . $pec_localization->get('BUTTON_SORT') . '" /><br /><br />
            
            <table class="data_table">
                <tr class="head">
                    <td class="check"><input type="checkbox" onclick="checkbox_mark_all(\'remove_box\', \'texts_main_form\', this);" /></td>
                    <td class="long">' . $pec_localization->get('LABEL_GENERAL_TITLE') . '</td>
                    <td class="medium">' . $pec_localization->get('LABEL_TEXTS_VISIBILITY') . '</td>
                    <td class="thin center">' . $pec_localization->get('LABEL_GENERAL_SORT') . '</td>
                <tr>
    ';
    
    foreach ($texts as $t) {
        switch ($t->get_visibility()) {
            case TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES: $visibility_string = $pec_localization->get('LABEL_TEXTS_SPECIFICARTICLES'); break;
            case TEXT_VISIBILITY_ON_ALL_ARTICLES: $visibility_string = $pec_localization->get('LABEL_TEXTS_ALLARTICLES'); break;
            case TEXT_VISIBILITY_ON_BLOG: $visibility_string = $pec_localization->get('LABEL_TEXTS_BLOG'); break;
            case TEXT_VISIBILITY_EVERYWHERE: $visibility_string = $pec_localization->get('LABEL_TEXTS_EVERYWHERE'); break;
            default: $visibility_string = '-'; break;
        }
        $area_data['content'] .= '
                <tr class="data" title="#' . $t->get_id() . '">
                    <td class="check"><input type="checkbox" class="remove_box" name="remove_box[]" value="' . $t->get_id() . '" /></td>
                    <td class="long">
                        <a href="' . AREA . '&amp;view=edit&amp;id=' . $t->get_id() . '"><span class="main_text">' . $t->get_title() . '</span></a>
                        <div class="row_actions">
                            <a href="' . AREA . '&amp;view=edit&amp;id=' . $t->get_id() . '">' . $pec_localization->get('ACTION_EDIT') . '</a> - 
                            <a href="javascript:ask(\'' . $pec_localization->get('LABEL_TEXTS_REALLYREMOVE') . '\', \'' . AREA . '&amp;view=default&amp;action=remove&amp;id=' . $t->get_id() . '\');">
                                ' . $pec_localization->get('ACTION_REMOVE') . '
                            </a>
                        </div>
                    </td>
                    <td class="medium">' . $visibility_string . '</td>
                    <td class="thin middle center">
                        <input type="text" size="2" name="sort_fields[]" value="' . $t->get_sort() . '" class="sort_input" />                                   
                        <input type="hidden" name="sort_extra_data[]" value="' . $t->get_id() . '-' . $t->get_sort() . '" />
                    </td>
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
    case 'edit': 
        $area_data = view_edit(); 
        $area['title'] = $area_data['title'];
        $area['head_data'] = $area_data['head_data'];
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
