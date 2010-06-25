<?php 

/**
 * pec_admin/pec_areas/articles.area.php - Managing articles
 * 
 * Admin area to manage articles.
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

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=articles');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_ARTICLES');
$area["permission_name"] = 'permission_articles';
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
            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_ARTICLE'),
            '{%NAME%}' => $_GET['message_data'],
            '{%ID%}' => $_GET['message_data']
        ));
        
    }
        
    if (isset($_GET['action'])) {
        // CREATE
        if ($_GET['action'] == 'create' && isset($_POST['article_title']) && isset($_POST['article_content'])) {
                
            $onstart = isset($_POST['article_onstart']) ? true : false;
                
            $article = new PecArticle(NULL_ID, $_POST['article_title'], htmlentities($_POST['article_content']), $onstart, $_POST['article_template_id']);
            $article->save();
            
            $messages .= PecMessageHandler::get('content_created', array(
                '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_ARTICLE'),
                '{%NAME%}' => $article->get_title()
            ));
        }
        
        // SAVE
        elseif ($_GET['action'] == 'save' && isset($_POST['article_title']) && isset($_POST['article_content'])) {
                
            if (isset($_GET['id']) && PecArticle::exists('id', $_GET['id'])) {
                $onstart = isset($_POST['article_onstart']) ? true : false;
                
                $article = PecArticle::load('id', $_GET['id']);
                $article->set_title($_POST['article_title']);
                $article->set_content($_POST['article_content']);
                $article->set_onstart($onstart);
                $article->set_template_id($_POST['article_template_id']);
                $article->save();
                
                $messages .= PecMessageHandler::get('content_edited', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_ARTICLE'),
                    '{%NAME%}' => $article->get_title()
                ));
            }
            else {
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_ARTICLE'),
                    '{%ID%}' => ''
                ));
            }
        }
        
        // REMOVE
        elseif ($_GET['action'] == 'remove' && isset($_GET['id'])) {
            if (PecArticle::exists('id', $_GET['id'])) {
                $article = PecArticle::load('id', $_GET['id']);
                $article_title = $article->get_title();
                $article->remove();
                
                $messages .= PecMessageHandler::get('content_removed', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_ARTICLE'),
                    '{%NAME%}' => $article_title
                ));
            }
            else {                
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_ARTICLE'),
                    '{%ID%}' => $_GET['id']
                ));
            }
        }
        
        // DEFAULT ACTIONS (REMOVE MULTIPLE, SORT)
        elseif ($_GET['action'] == 'default_view_actions') {
        
            // REMOVE MULTIPLE
            if (isset($_POST['remove_articles'])) {
                if (!empty($_POST['remove_box'])) {
                    
                    foreach ($_POST['remove_box'] as $article_id) {
                        $article = PecArticle::load('id', $article_id);
                        $article->remove();
                    }
                              
                    $messages .= PecMessageHandler::get('content_removed_multiple', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_ARTICLES'),
                        '{%NAME%}' => ''
                    ));
                }
                else {
                    $messages .= PecMessageHandler::get('content_not_selected', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_ARTICLES'),
                        '{%NAME%}' => ''
                    ));
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
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_ARTICLES');
    $area_data["head_data"] = '<script type="text/javascript" src="pec_style/js/ajax/articles.js"></script>';
    
    if (isset($_GET['id'])) {
        if (PecArticle::exists('id', $_GET['id'])) {
            $article = PecArticle::load('id', $_GET['id']);
            $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_ARTICLES_EDIT') . ' &raquo; ' . $article->get_title();
            
            $action = 'save';
            $id_query_var = '&amp;id=' . $_GET['id'];
        }
        else {
            pec_redirect('pec_admin/' . AREA . '&message=content_not_found_id&message_data=' . $_GET['id']);
        }
    }
    else {
        // create an empty article
        $article = new PecArticle(NULL_ID, '', '', 0, '');
        $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_ARTICLES_CREATE');
        
        $action = 'create';
        $id_query_var = '';
    }
    
    $onstart_checked = $article->get_onstart() == true ? 'checked="checked"' : '';
    
    
    $template_id_options = '';
    $any_template_selected = false;
    $templates = PecTemplate::load();
    foreach ($templates as $tpl) {
    	if ($article->get_template_id() == $tpl->get_property('id')) {
    		$selected = 'selected="selected"';
    		$any_template_selected = true;
    	}
    	else {
    		$selected = '';
    	}
    	
    	$template_id_options .= '<option value="' . $tpl->get_property('id') . '" ' . $selected . '>' . $tpl->get_property('title') . '</option>';
    }
    // if no template is selected, select the global template
    if (!$any_template_selected) {
    	$selected = 'selected="selected"';
    }
    else {
    	$selected = '';
    }
    $template_id_options = '<option value="' . GLOBAL_TEMPLATE_ID . '" ' . $selected . '>-----</option>'
    					  . $template_id_options;
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&amp;view=default&amp;action=' . $action . $id_query_var . '" id="articles_edit_form" />

            <h3 style="float: left; font-size: 8pt;">' . $pec_localization->get('LABEL_GENERAL_PERMALINK') . ':</h3> <a style="float: left; margin-left: 5px; font-size: 8pt;" href="' . create_article_url($article) . '" target="_blank">' . create_article_url($article) . '</a>

            <div style="clear: left; height: 10px;"></div>

            <h3>' . $pec_localization->get('LABEL_GENERAL_TITLE') . ':</h3>
            <input type="text" size="75" name="article_title" id="article_title" value="' . $article->get_title() . '" />
            <br /><br />
            
            <textarea name="article_content" id="article_content">' . htmlentities($article->get_content()) . '</textarea>
            <br /><br />
            
            
            <div class="options_box_1 float_left">
                <h3>' . $pec_localization->get('LABEL_ARTICLES_OPTIONS') . ':</h3>
                <input type="checkbox" name="article_onstart" id="article_onstart" value="1" ' . $onstart_checked . ' /> <label for="article_onstart">' . $pec_localization->get('LABEL_ARTICLES_DISPLAY_ONSTART') . '</label>
                
                <br /><br />
                <h3>' . $pec_localization->get('LABEL_TEMPLATES_TEMPLATENAME') . ':</h3>
                <select name="article_template_id" id="article_template_id">
                	' . $template_id_options . '
                </select>
            </div>
            <div style="clear: left;"></div>
            <br /><br />
            
            <input type="hidden" name="article_id" id="article_id" value="' . $article->get_id() . '" />
            <input type="submit" value="' . $pec_localization->get('BUTTON_SAVE') . '"/> 
            <input type="button" value="' . $pec_localization->get('BUTTON_APPLY') . '" id="article_apply_button" /> 
            <a href="' . AREA . '"><input type="button" onclick="location.href=\'' . AREA . '\'" value="' . $pec_localization->get('BUTTON_CANCEL') . '" /></a>
        </form>            
    ';

    // replace textarea by ckeditor
    $area_data['content'] .= ckeditor_replace('article_content');
    
    return $area_data;
}

function view_default() {
    global $pec_localization;
    
    $area_data = array();
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_ARTICLES');
    
    $articles = PecArticle::load();
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&amp;view=default&amp;action=default_view_actions" id="articles_main_form" onsubmit="return confirm(\'' .  $pec_localization->get('LABEL_ARTICLES_REALLYREMOVE_SELECTED') . '\');" />
            <input type="button" value="' . $pec_localization->get('BUTTON_NEW_ARTICLE') . '" onclick="location.href=\'' . AREA . '&amp;view=edit\'"/>
            <input type="submit" name="remove_articles" value="' . $pec_localization->get('BUTTON_REMOVE') . '" /><br /><br />
            
            <table class="data_table">
                <tr class="head">
                    <td class="check"><input type="checkbox" onclick="checkbox_mark_all(\'remove_box\', \'articles_main_form\', this);" /></td>
                    <td class="long">' . $pec_localization->get('LABEL_GENERAL_TITLE') . '</td>
                    <td class="medium">' . $pec_localization->get('LABEL_GENERAL_SLUG') . '</td>
                    <td class="short">' . $pec_localization->get('LABEL_ARTICLES_ONSTART') . '</td>
                <tr>
    ';
    
    foreach ($articles as $a) {
        $area_data['content'] .= '
                <tr class="data" title="#' . $a->get_id() . '">
                    <td class="check"><input type="checkbox" class="remove_box" name="remove_box[]" value="' . $a->get_id() . '" /></td>
                    <td class="long">
                        <a href="' . AREA . '&amp;view=edit&amp;id=' . $a->get_id() . '"><span class="main_text">' . $a->get_title() . '</span></a>
                        <div class="row_actions">
                            <a href="' . AREA . '&amp;view=edit&amp;id=' . $a->get_id() . '">' . $pec_localization->get('ACTION_EDIT') . '</a> - 
                            <a href="javascript:ask(\'' . $pec_localization->get('LABEL_ARTICLES_REALLYREMOVE') . '\', \'' . AREA . '&amp;view=default&amp;action=remove&amp;id=' . $a->get_id() . '\');">' . $pec_localization->get('ACTION_REMOVE') . '</a>
                        </div>
                    </td>
                    <td class="medium">' . $a->get_slug() . '</td>
                    <td class="short">' . $a->get_onstart(true) . '</td>
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
