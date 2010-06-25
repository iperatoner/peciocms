<?php 

/**
 * pec_admin/pec_areas/blog-comments.area.php - Managing blog comments
 * 
 * Admin area to manage blog comments.
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

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=blog-comments');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_BLOGCOMMENTS');
$area["permission_name"] = 'permission_blogcomments';
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
            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_COMMENT'),
            '{%NAME%}' => $_GET['message_data'],
            '{%ID%}' => $_GET['message_data']
        ));
        
    }
        
    if (isset($_GET['action'])) {
        
        // SAVE
        if ($_GET['action'] == 'save' && isset($_POST['comment_title']) && 
            isset($_POST['comment_author']) && isset($_POST['comment_content']) && 
            isset($_POST['comment_email'])) {
            if (isset($_GET['id']) && PecBlogComment::exists('id', $_GET['id'])) {     
                           
                $comment = PecBlogComment::load('id', $_GET['id']);
                $comment->set_title($_POST['comment_title']);
                $comment->set_author($_POST['comment_author']);
                $comment->set_email($_POST['comment_email']);
                $comment->set_content($_POST['comment_content']);
                $comment->save();
                
                $messages .= PecMessageHandler::get('content_edited', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_COMMENT'),
                    '{%NAME%}' => $comment->get_title()
                ));
            }
            else {
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_COMMENT'),
                    '{%ID%}' => ''
                ));
            }
        }
        
        // REMOVE
        elseif ($_GET['action'] == 'remove' && isset($_GET['id'])) {
            if (PecBlogComment::exists('id', $_GET['id'])) {
                $comment = PecBlogComment::load('id', $_GET['id']);
                $comment_title = $comment->get_title();
                $comment->remove();
                
                $messages .= PecMessageHandler::get('content_removed', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_COMMENT'),
                    '{%NAME%}' => $comment_title
                ));
            }
            else {                
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_COMMENT'),
                    '{%ID%}' => $_GET['id']
                ));
            }
        }
        
        // DEFAULT ACTIONS (REMOVE MULTIPLE)
        elseif ($_GET['action'] == 'default_view_actions') {
        
            // REMOVE MULTIPLE
            if (isset($_POST['remove_comments'])) {
                if (!empty($_POST['remove_box'])) {
                    
                    foreach ($_POST['remove_box'] as $comment_id) {
                        $comment = PecBlogComment::load('id', $comment_id);
                        $comment->remove();
                    }
                              
                    $messages .= PecMessageHandler::get('content_removed_multiple', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_COMMENTS'),
                        '{%NAME%}' => ''
                    ));
                }
                else {
                    $messages .= PecMessageHandler::get('content_not_selected', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_COMMENTS'),
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
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_BLOGCOMMENTS');
    
    if (!isset($_GET['id'])) {
        pec_redirect('pec_admin/' . AREA . '&message=content_not_found_id&message_data=0');
    }
    elseif (!PecBlogComment::exists('id', $_GET['id'])) {
        pec_redirect('pec_admin/' . AREA . '&message=content_not_found_id&message_data=' . $_GET['id']);
    }
    
    $comment = PecBlogComment::load('id', $_GET['id']);
    $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_COMMENTS_EDIT') . ' &raquo; ' . $comment->get_title();
    
    if (PecBlogPost::exists('id', $comment->get_post_id())) {
        $post = $comment->get_post();
        $post_title = $post->get_title();
        $post_id_string = $pec_localization->get('LABEL_GENERAL_POST') . ' #' . $comment->get_post_id();
    }
    else {
        $post_title = '-';
        $post_id_string = '<del>' . $pec_localization->get('LABEL_GENERAL_POST') . ' #' . $comment->get_post_id() . '</del>';
    }
    
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&ampview=default&amp;action=save&amp;id=' . $comment->get_id() . '" id="comments_edit_form" />            
            <h3>' . $pec_localization->get('LABEL_GENERAL_TITLE') . ':</h3>
            <input type="text" size="75" name="comment_title" value="' . $comment->get_title() . '" />
            <br /><br />
            
            <h3>' . $pec_localization->get('LABEL_COMMENTS_AUTHOR') . ':</h3>
            <input type="text" size="75" name="comment_author" value="' . $comment->get_author() . '" />
            <br /><br />
            
            <h3>' . $pec_localization->get('LABEL_COMMENTS_EMAIL') . ':</h3>
            <input type="text" size="75" name="comment_email" value="' . $comment->get_email() . '" />
            <br /><br />
            
            <h3>' . $pec_localization->get('LABEL_COMMENTS_CONTENT') . ':</h3>
            <textarea name="comment_content" style="width: 600px; height: 300px;">' . br2nl($comment->get_content()) . '</textarea>
            <br /><br />
            
            <div class="options_box_1 float_left">
                <h3 style="padding-left: 0px !important;">' . $pec_localization->get('LABEL_COMMENTS_RELATES_TO_POST') . ':</h3>
                ' . $post_title . '<br />
                ' . $post_id_string . '
                <br /><br />
                
                <h3 style="padding-left: 0px !important;">' . $pec_localization->get('LABEL_COMMENTS_DATE') . ':</h3>
                ' . $comment->get_timestamp('d.m.Y - H:i:s') . '
                <br /><br />
            </div>
            <div style="clear: left;"></div><br /><br />
            
            <input type="submit" value="' . $pec_localization->get('BUTTON_SAVE') . '" /> <a href="' . AREA . '">
            <input type="button" onclick="location.href=\'' . AREA . '\'" value="' . $pec_localization->get('BUTTON_CANCEL') . '" /></a>
        </form>            
    ';
    
    return $area_data;
}

function view_default() {
	global $pec_localization;
	   
    $area_data = array();
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_BLOGCOMMENTS');
    
    $comments = PecBlogComment::load();
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&amp;view=default&amp;action=default_view_actions" id="comments_main_form" onsubmit="return confirm(\'' . $pec_localization->get('LABEL_COMMENTS_REALLYREMOVE_SELECTED') . '\');" />
            <input type="submit" name="remove_comments" value="' . $pec_localization->get('BUTTON_REMOVE') . '" /><br /><br />
            
            <table class="data_table">
                <tr class="head">
                    <td class="check"><input type="checkbox" onclick="checkbox_mark_all(\'remove_box\', \'comments_main_form\', this);" /></td>
                    <td class="long">' . $pec_localization->get('LABEL_GENERAL_TITLE') . '</td>
                    <td class="medium">' . $pec_localization->get('LABEL_COMMENTS_AUTHOR') . '</td>
                    <td class="medium">' . $pec_localization->get('LABEL_COMMENTS_DATE') . '</td>
                    <td class="medium">' . $pec_localization->get('LABEL_COMMENTS_RELATES_TO') . '</td>
                <tr>
    ';
    
    foreach ($comments as $c) {
        if (PecBlogPost::exists('id', $c->get_post_id())) {
            $post = $c->get_post();
            $post_title = $post->get_title();
            $post_id = $post->get_id();
            $post_id_string = $pec_localization->get('LABEL_GENERAL_POST') . ' #' . $post_id;
        }
        else {
            $post_title = '-';
            $post_id = $c->get_post_id();
            $post_id_string = '<del>' . $pec_localization->get('LABEL_GENERAL_POST') . ' #' . $post_id . '</del>';
        }
        
        $area_data['content'] .= '
                <tr class="data" title="#' . $c->get_id() . '">
                    <td class="check"><input type="checkbox" class="remove_box" name="remove_box[]" value="' . $c->get_id() . '" /></td>
                    <td class="long">
                        <a href="' . AREA . '&amp;view=edit&amp;id=' . $c->get_id() . '"><span class="main_text">' . $c->get_title() . '</span></a>
                        <div class="row_actions">
                            <a href="' . AREA . '&amp;view=edit&amp;id=' . $c->get_id() . '">' . $pec_localization->get('ACTION_EDIT') . '</a> - 
                            <a href="javascript:ask(\'' . $pec_localization->get('LABEL_COMMENTS_REALLYREMOVE') . '\', \'' . AREA . '&amp;view=default&amp;action=remove&amp;id=' . $c->get_id() . '\');">' . $pec_localization->get('ACTION_REMOVE') . '</a>
                        </div>
                    </td>
                    <td class="medium">' . $c->get_author() . '<br /><em>' . $c->get_email() . '</em></td>
                    <td class="medium">' . $c->get_timestamp('d.m.Y - H:i') . '</td>
                    <td class="medium"><a href="' . ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=blog-posts&amp;view=edit&amp;id=' . $post_id. '">' . $post_title. '</a><br /><i>' . $post_id_string . '</i></td>
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

if ($pec_session->get('pec_user')->get_permission($area['permission_name']) >PERMISSION_READ) {
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
