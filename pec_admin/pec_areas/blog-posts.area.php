<?php 

/**
 * pec_admin/pec_areas/blog-posts.area.php - Managing blog posts
 * 
 * Admin area to manage blog posts.
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

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=blog-posts');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_BLOGPOSTS');
$area["permission_name"] = 'permission_blogposts';
$area["head_data"] = '';
$area["messages"] = '';
$area["content"] = 'No view was executed.';


/* a function that does actions depending on what data is in the query string */

function do_actions() {
    global $pec_session, $pec_localization, $pec_settings;
    
    $messages = '';
    
    // costum message
    if (isset($_GET['message']) && !empty($_GET['message']) && 
        isset($_GET['message_data']) && !empty($_GET['message_data']) && 
        PecMessageHandler::exists($_GET['message'])) {  
                    
        $messages .= PecMessageHandler::get($_GET['message'], array(
            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POST'),
            '{%NAME%}' => $_GET['message_data'],
            '{%ID%}' => $_GET['message_data']
        ));
        
    }    
        
    if (isset($_GET['action'])) {
        // CREATE
        if ($_GET['action'] == 'create' && isset($_POST['post_title']) && 
            isset($_POST['post_content_cut']) && isset($_POST['post_content']) && 
            isset($_POST['post_tags'])) {
            $status = isset($_POST['post_status']) ? true : false;
             
            // converting the array of selected categories into the flat version for database
            if (isset($_POST['post_categories'])) {
                $in_categories = array_to_flat($_POST['post_categories']);
            }
            else {
                $in_categories = array();
            }
            
            // the constructor only accepts the flat version of tag ids (i may change this in future)
            $tag_ids = array_to_flat(PecBlogTag::get_ids_of_tagnames($_POST['post_tags'], true));      
                  
            $timestamp = time();
            $y = date('Y', $timestamp);
            $m = date('m', $timestamp);
            $d = date('d', $timestamp);
            
            $author_id = $pec_session->get('pec_user')->get_id();
            
            $post = new PecBlogPost(NULL_ID, $timestamp, $y, $m, $d, $author_id, 
                                    $_POST['post_title'], $_POST['post_content_cut'], $_POST['post_content'], $tag_ids, $in_categories, $status);
            $post->save();
            
            $messages .= PecMessageHandler::get('content_created', array(
                '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POST'),
                '{%NAME%}' => $post->get_title()
            ));
        }
        
        // SAVE
        elseif ($_GET['action'] == 'save' && isset($_POST['post_title']) && 
                isset($_POST['post_content_cut']) && isset($_POST['post_content']) && 
                isset($_POST['post_tags'])) {
                    
            if (isset($_GET['id']) && PecBlogPost::exists('id', $_GET['id'])) {
            	$status = isset($_POST['post_status']) ? true : false; 
                
                $tag_ids = PecBlogTag::get_ids_of_tagnames($_POST['post_tags'], true);
                             
                $post = PecBlogPost::load('id', $_GET['id']);
                
                $post->set_title($_POST['post_title']);
                $post->set_content_cut($_POST['post_content_cut']);
                $post->set_content($_POST['post_content']);
                $post->set_tags($tag_ids, TYPE_ARRAY);
                $post->set_categories($_POST['post_categories'], TYPE_ARRAY);
                $post->set_status($status);
                
                $post->save();
                
                PecBlogTag::remove_deprecated_tags();
                
                $messages .= PecMessageHandler::get('content_edited', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POST'),
                    '{%NAME%}' => $post->get_title()
                ));
            }
            else {
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POST'),
                    '{%ID%}' => ''
                ));
            }
        }
        
        // REMOVE
        elseif ($_GET['action'] == 'remove' && isset($_GET['id'])) {
            if (PecBlogPost::exists('id', $_GET['id'])) {
                $post = PecBlogPost::load('id', $_GET['id']);
                $post_title = $post->get_title();
                $post->remove();
                
                PecBlogTag::remove_deprecated_tags();
                
                $messages .= PecMessageHandler::get('content_removed', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POST'),
                    '{%NAME%}' => $post_title
                ));
            }
            else {                
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POST'),
                    '{%ID%}' => $_GET['id']
                ));
            }
        }
        
        // PUBLISH
        elseif ($_GET['action'] == 'publish' || $_GET['action'] == 'unpublish' && isset($_GET['id'])) {
            if (PecBlogPost::exists('id', $_GET['id'])) {
                $post = PecBlogPost::load('id', $_GET['id']);
            
                if ($_GET['action'] == 'publish') {
                    $post->set_status(true);
                    $msg_type = 'content_published';
                }
                elseif ($_GET['action'] == 'unpublish') {
                    $post->set_status(false);
                    $msg_type = 'content_unpublished';
                }
                
                $post->save();
                
                $messages .= PecMessageHandler::get($msg_type, array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POST'),
                    '{%NAME%}' => $post->get_title()
                ));
            }
            else {                
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POST'),
                    '{%ID%}' => $_GET['id']
                ));
            }
        }
        
        // DEFAULT ACTIONS (REMOVE MULTIPLE)
        elseif ($_GET['action'] == 'default_view_actions') {
        
            // REMOVE MULTIPLE
            if (isset($_POST['remove_posts']) && !isset($_POST['submitted_posts_per_page'])) {
                if (!empty($_POST['remove_box'])) {
                    
                    foreach ($_POST['remove_box'] as $post_id) {
                        $post = PecBlogPost::load('id', $post_id);
                        $post->remove();
                    }
                              
                    $messages .= PecMessageHandler::get('content_removed_multiple', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POSTS'),
                        '{%NAME%}' => ''
                    ));
                }
                else {
                    $messages .= PecMessageHandler::get('content_not_selected', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POSTS'),
                        '{%NAME%}' => ''
                    ));
                }
            }
            
            // SET POSTS_PER_PAGE
            if (isset($_POST['set_posts_per_page']) || isset($_POST['submitted_posts_per_page'])) {
            	if (!empty($_POST['setting_posts_per_page'])) {
            		$posts_per_page = intval($_POST['setting_posts_per_page']);
		            	
		            // check if an integer was passed for the posts per page setting
		            if ($posts_per_page) {
		                $pec_settings->set_posts_per_page($posts_per_page);
            			$pec_settings->save();
		            	
            			$messages .= PecMessageHandler::get('settings_saved', array());
		            }
		            else {
		                $messages .= PecMessageHandler::get('content_integer_required', array(
		                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_SETTING'),
		                    '{%NAME%}' => 'Blogposts per Page'
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
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_BLOGPOSTS');
    $area_data["head_data"] = '
        <script type="text/javascript" src="pec_style/js/ajax/blog-categories.js"></script>
        <script type="text/javascript" src="pec_style/js/ajax/blog-posts.js"></script>
    ';    
    
    if (isset($_GET['id'])) {
        if (PecBlogPost::exists('id', $_GET['id'])) {
            $post = PecBlogPost::load('id', $_GET['id']);
            $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_POSTS_EDIT') . ' &raquo; ' . $post->get_title();
            
            $action = 'save';
            $id_query_var = '&amp;id=' . $_GET['id'];
        }
        else {
            pec_redirect('pec_admin/' . AREA . '&message=content_not_found_id&message_data=' . $_GET['id']);
        }
    }
    else {
        // create an empty post
        $post = new PecBlogPost(NULL_ID, 0, 0, 0, 0, 0, '', '', '', '', '', false);
        $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_POSTS_CREATE');
        
        $action = 'create';
        $id_query_var = '';
    }

    $published_checked = $post->get_status() == true ? 'checked="checked"' : '';
    
    
    // create a string with comma-separated tags
    $tag_string = '';
    $used_tags = $post->get_tags(TYPE_OBJ_ARRAY);
    $started = false;
    foreach ($used_tags as $t) {
        if (!$started) {
            $comma = '';
            $started = true;
        }
        else {
            $comma = ',';
        }
        
        $tag_string .= $comma . $t->get_name();
    }
    
    // create the category checkboxes
    $category_checkboxes = '';
    $available_categories = PecBlogCategory::load();
    foreach ($available_categories as $c) {
        if ($post->in_category($c)) {
            $checked = 'checked="checked"';
        }
        else {
            $checked = '';
        }
        
        $category_checkboxes .= '
            <div class="checkbox_data_row" id="category_row_' . $c->get_id() . '">
                <input type="checkbox" name="post_categories[]" id="cat_' . $c->get_id() . '" value="' . $c->get_id() . '" ' . $checked . ' /> 
                    <label for="cat_' . $c->get_id() . '">
                        ' . $c->get_name() . '
                    </label> &nbsp;
                    <span class="checkbox_data_row_actions">
                        <a href="javascript:edit_blog_category(' . $c->get_id() . ', \'' . $c->get_name() . '\');">' . $pec_localization->get('ACTION_EDIT') . '</a> 
                        | <a href="javascript:remove_blog_category(' . $c->get_id() . ');">&#x2716;</a>
                    </span>
                <br />
                
            </div>
        ';
    }
    
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&ampview=default&amp;action=' . $action . $id_query_var . '" id="posts_edit_form" />

            <h3 style="float: left; font-size: 8pt;">' . $pec_localization->get('LABEL_GENERAL_PERMALINK') . ':</h3> <a style="float: left; margin-left: 5px; font-size: 8pt;" href="' . create_blogpost_url($post) . '" target="_blank">' . create_blogpost_url($post) . '</a>

            <div style="clear: left; height: 10px;"></div>

            <h3>' . $pec_localization->get('LABEL_GENERAL_TITLE') . ':</h3>
            <input type="text" size="75" name="post_title" id="post_title" value="' . $post->get_title() . '" />
            <br /><br />
            
            <h3>' . $pec_localization->get('LABEL_POSTS_INTRODUCTION') . ':</h3>
            <textarea name="post_content_cut" id="post_content_cut" rows="10">' . $post->get_content_cut() . '</textarea>
            <br />
            
            <h3>' . $pec_localization->get('LABEL_POSTS_MAINTEXT') . ':</h3>
            <textarea name="post_content" id="post_content" style="height: 600px">' . $post->get_content() . '</textarea>
            <br /><br />
                        
            <div class="options_box_1 float_left" style="margin-right: 10px; height: 220px">
                <h3>' . $pec_localization->get('LABEL_POSTS_OPTIONS') . ':</h3>
                <input type="checkbox" name="post_status" id="post_status" value="1" ' . $published_checked . ' /> 
                <label for="post_status">' . $pec_localization->get('LABEL_POSTS_PUBLIC') . '</label><br /><br />
                
                <h3>' . $pec_localization->get('LABEL_POSTS_TAGS') . ':</h3>
                <input type="text" size="52" name="post_tags" id="post_tags" value="' . $tag_string . '" /> 
            </div>            
            
            <div class="options_box_1 float_left" style="height: 220px; width: 300px;">
                <h3>' . $pec_localization->get('LABEL_POSTS_CATEGORIES') . ':</h3>
                <div class="checkbox_data_selector" id="category_selector">
                    ' . $category_checkboxes . '
                </div>
                <br />
                
                <input type="text" name="category_name" id="category_name" value="" size="35" 
                       onkeydown="if (event.which == 13) { add_blog_category(); this.value = \'\'; return false; }" />
                <input type="button" id="category_add_button" value="' . $pec_localization->get('BUTTON_ADD') . '"/>
            </div>
            
            <div style="clear: left;"></div>
            
            <br /><br />
            
            <input type="hidden" name="post_id" id="post_id" value="' . $post->get_id() . '" />
            <input type="submit" value="' . $pec_localization->get('BUTTON_SAVE') . '" />
            <input type="button" value="' . $pec_localization->get('BUTTON_APPLY') . '" id="post_apply_button" />
            <a href="' . AREA . '"><input type="button" onclick="location.href=\'' . AREA . '\'" value="' . $pec_localization->get('BUTTON_CANCEL') . '" /></a>
        </form>            
    ';

    // replace textareas by ckeditor
    $area_data['content'] .= ckeditor_replace('post_content_cut', 'toolbar: ck_basic_toolbar(), uiColor: "#eaeaea"');
    $area_data['content'] .= ckeditor_replace('post_content');
    
    return $area_data;
}

function view_default() {
	global $pec_localization, $pec_settings;
	
    $area_data = array();
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_BLOGPOSTS');
    
    $posts = PecBlogPost::load(0, false, 'ORDER BY post_timestamp DESC');
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&amp;view=default&amp;action=default_view_actions" id="posts_main_form"/>
        
        	<div class="float_left">
	            <input type="button" value="' . $pec_localization->get('BUTTON_NEW_BLOGPOST') . '" onclick="location.href=\'' . AREA . '&amp;view=edit\'"/> 
	            
            	<input type="submit" name="submitted_posts_per_page" class="hidden_element" />
	            <input type="submit" name="remove_posts" value="' . $pec_localization->get('BUTTON_REMOVE') . '" onclick="return confirm(\'' . $pec_localization->get('LABEL_POSTS_REALLYREMOVE_SELECTED') . '\');" />
            </div>
            
            <div class="float_right">
           		<strong>' . $pec_localization->get('LABEL_SETTINGS_POSTSPERPAGE') . ':</strong>&nbsp;&nbsp;
            	<input type="text" size="5" name="setting_posts_per_page" value="' . $pec_settings->get_posts_per_page() . '" />
            	<input type="submit" name="set_posts_per_page" value="' . $pec_localization->get('BUTTON_APPLY') . '" />
            </div>
            
            <br style="clear: both;" /><br />
            
            <table class="data_table" cellspacing="0">
                <thead>
                    <tr class="head_row">
                        <th class="check_column"><input type="checkbox" onclick="checkbox_mark_all(\'remove_box\', \'posts_main_form\', this);" /></th>
                        <th class="long_column">' . $pec_localization->get('LABEL_GENERAL_TITLE') . '</th>
                        <th class="medium_column">' . $pec_localization->get('LABEL_GENERAL_SLUG') . '</th>
                        <th class="lower_medium_column">' . $pec_localization->get('LABEL_POSTS_DATE') . '</th>
                        <th class="medium_column">' . $pec_localization->get('LABEL_POSTS_AUTHOR') . '</th>
                        <th class="mid_thin_column">' . $pec_localization->get('LABEL_POSTS_PUBLISHED') . '</th>
                    <tr>
                </thead>
                <tbody>
    ';
    
    foreach ($posts as $p) {
        $publish_link_string = $p->get_status() == false ? $pec_localization->get('ACTION_PUBLISH') : $pec_localization->get('ACTION_UNPUBLISH');
        $publish_action = $p->get_status() == false ? 'publish' : 'unpublish';
        $author_name = PecUser::exists('id', $p->get_author_id()) ? $p->get_author()->get_name() : '<del>' . $p->get_author_id() . '</del>';
        
        
        $area_data['content'] .= '
                    <tr class="data_row" title="#' . $p->get_id() . '">
                        <td class="check_column"><input type="checkbox" class="remove_box" name="remove_box[]" value="' . $p->get_id() . '" /></td>
                        <td class="normal_column">
                            <a href="' . AREA . '&amp;view=edit&amp;id=' . $p->get_id() . '"><span class="main_text">' . $p->get_title() . '</span></a>
                            <div class="row_actions">
                                <a href="' . AREA . '&amp;view=edit&amp;id=' . $p->get_id() . '">' . $pec_localization->get('ACTION_EDIT') . '</a> - 
                                <a href="' . AREA . '&amp;view=default&amp;action=' . $publish_action . '&amp;id=' . $p->get_id() . '">
                                    ' . $publish_link_string . '
                                </a> - 
                                <a href="javascript:ask(\'' . $pec_localization->get('LABEL_POSTS_REALLYREMOVE') . '\', \'' . AREA . '&amp;view=default&amp;action=remove&amp;id=' . $p->get_id() . '\');">
                                    ' . $pec_localization->get('ACTION_REMOVE') . '
                                </a>
                            </div>
                        </td>
                        <td class="normal_column">' . $p->get_slug() . '</td>
                        <td class="normal_column">' . $p->get_timestamp('d.m.y - H:i') . '</td>
                        <td class="normal_column">' . $author_name . '</td>
                        <td class="normal_column">' . $p->get_status(true) . '</td>
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
