<?php 

/**
 * pec_admin/pec_areas/users.area.php - Managing users
 * 
 * Admin area to manage the CMS users.
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

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=users');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_USERS');
$area["permission_name"] = 'permission_users';
$area["head_data"] = '';
$area["messages"] = '';
$area["content"] = 'No view was executed.';


/* a function that does actions depending on what data is in the query string */

function do_actions() {
    global $pec_session, $pec_localization;
    
    $messages = '';
    
    // costum message
    if (isset($_GET['message']) && !empty($_GET['message']) && 
        isset($_GET['message_data']) && !empty($_GET['message_data']) && 
        PecMessageHandler::exists($_GET['message'])) {  
                    
        $messages .= PecMessageHandler::get($_GET['message'], array(
            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USER'),
            '{%NAME%}' => $_GET['message_data'],
            '{%ID%}' => $_GET['message_data']
        ));
        
    }
        
    if (isset($_GET['action'])) {
        // CREATE
        if ($_GET['action'] == 'create' && isset($_POST['user_name']) && isset($_POST['user_forename']) && 
            isset($_POST['user_surname']) && isset($_POST['user_email']) && isset($_POST['user_password']) &&
            isset($_POST['user_password_repeat'])) {

            if (!PecUser::exists('name', $_POST['user_name'])) {
                // checking if the password field is empty or the repeated password is incorrect. 
                // then the cms will use an auto-generated password for default use.
                if (empty($_POST['user_password'])) {                
                    $password = random_string(8);
                    
                    $messages .= PecMessageHandler::get('password_empty', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USER'),
                        '{%NAME%}' => $_POST['user_name'],
                        '{%DEFAULT_PASSWORD%}' => $password
                    ));
                }
                elseif ($_POST['user_password'] != $_POST['user_password_repeat']) {             
                    $password = random_string(8);
                    
                    $messages .= PecMessageHandler::get('password_repeat_incorrect', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USER'),
                        '{%NAME%}' => $_POST['user_name'],
                        '{%DEFAULT_PASSWORD%}' => $password
                    ));
                }
                else {
                    $password = $_POST['user_password'];
                }
                
                $available_permissions = pec_available_user_permissions();
                   
                // getting the permissions            
                $permissions = array();
                $permissions['articles']     = in_array($_POST['user_perm_articles'], $available_permissions)     ? $_POST['user_perm_articles']        : PERMISSION_NONE;
                $permissions['menupoints']      = in_array($_POST['user_perm_menupoints'], $available_permissions)  ? $_POST['user_perm_menupoints']    : PERMISSION_NONE;
                $permissions['texts']          = in_array($_POST['user_perm_texts'], $available_permissions)         ? $_POST['user_perm_texts']        : PERMISSION_NONE;
                $permissions['links']          = in_array($_POST['user_perm_links'], $available_permissions)         ? $_POST['user_perm_links']        : PERMISSION_NONE;
                $permissions['blogposts']      = in_array($_POST['user_perm_posts'], $available_permissions)          ? $_POST['user_perm_posts']        : PERMISSION_NONE;
                $permissions['blogcomments'] = in_array($_POST['user_perm_comments'], $available_permissions)     ? $_POST['user_perm_comments']        : PERMISSION_NONE;
                $permissions['users']          = in_array($_POST['user_perm_users'], $available_permissions)          ? $_POST['user_perm_users']        : PERMISSION_NONE;
                $permissions['plugins']      = in_array($_POST['user_perm_plugins'], $available_permissions)     ? $_POST['user_perm_plugins']        : PERMISSION_NONE;
                $permissions['templates']      = in_array($_POST['user_perm_templates'], $available_permissions)     ? $_POST['user_perm_templates']    : PERMISSION_NONE;
                $permissions['settings']     = in_array($_POST['user_perm_settings'], $available_permissions)     ? $_POST['user_perm_settings']        : PERMISSION_NONE;
                
                // password will be hashed with sha1 by the __constructor
                $user = new PecUser(NULL_ID, $_POST['user_name'], $_POST['user_forename'], $_POST['user_surname'], $_POST['user_email'], $password, 
                                    $permissions['articles'], $permissions['menupoints'], $permissions['texts'], $permissions['links'], 
                                    $permissions['blogposts'], $permissions['blogcomments'], $permissions['users'], $permissions['plugins'], 
                                    $permissions['templates'], $permissions['settings']);
                $user->save();
                
                $messages .= PecMessageHandler::get('content_created', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USER'),
                    '{%NAME%}' => $user->get_name()
                ));
            }
            else {
                $user = PecUser::load('name', $_POST['user_name']);
                $messages .= PecMessageHandler::get('content_exists', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USER'),
                    '{%NAME%}' => $user->get_name()
                ));
            }
        }
        
        // SAVE
        elseif ($_GET['action'] == 'save' && isset($_POST['user_name']) && isset($_POST['user_forename']) && 
                isset($_POST['user_surname']) && isset($_POST['user_email']) && isset($_POST['user_password']) &&
                isset($_POST['user_password_repeat'])) {
                
            if (isset($_GET['id']) && PecUser::exists('id', $_GET['id'])) {     
                $change_password = false;
                // checking if the password field is empty or the repeated password is incorrect. 
                // then the cms will not change the current password. if the field is empty the password mustn't be changed.
                if (!empty($_POST['user_password'])) {
                    if ($_POST['user_password'] == $_POST['user_password_repeat']) {                
                        $password = $_POST['user_password'];
                        $change_password = true;
                    }
                    else {
                        $messages .= PecMessageHandler::get('password_repeat_incorrect_save', array(
                            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USER'),
                            '{%NAME%}' => $_POST['user_name']
                        ));
                    }
                }
                
            
                $available_permissions = pec_available_user_permissions();                
                
                // getting the permissions            
                $permissions = array();
                $permissions['articles']     = in_array($_POST['user_perm_articles'], $available_permissions)     ? $_POST['user_perm_articles']        : PERMISSION_NONE;
                $permissions['menupoints']      = in_array($_POST['user_perm_menupoints'], $available_permissions)  ? $_POST['user_perm_menupoints']    : PERMISSION_NONE;
                $permissions['texts']          = in_array($_POST['user_perm_texts'], $available_permissions)         ? $_POST['user_perm_texts']        : PERMISSION_NONE;
                $permissions['links']          = in_array($_POST['user_perm_links'], $available_permissions)         ? $_POST['user_perm_links']        : PERMISSION_NONE;
                $permissions['blogposts']      = in_array($_POST['user_perm_posts'], $available_permissions)          ? $_POST['user_perm_posts']        : PERMISSION_NONE;
                $permissions['blogcomments'] = in_array($_POST['user_perm_comments'], $available_permissions)     ? $_POST['user_perm_comments']        : PERMISSION_NONE;
                $permissions['users']          = in_array($_POST['user_perm_users'], $available_permissions)          ? $_POST['user_perm_users']        : PERMISSION_NONE;
                $permissions['plugins']      = in_array($_POST['user_perm_plugins'], $available_permissions)     ? $_POST['user_perm_plugins']        : PERMISSION_NONE;
                $permissions['templates']      = in_array($_POST['user_perm_templates'], $available_permissions)     ? $_POST['user_perm_templates']    : PERMISSION_NONE;
                $permissions['settings']     = in_array($_POST['user_perm_settings'], $available_permissions)     ? $_POST['user_perm_settings']        : PERMISSION_NONE;
                
                $user = PecUser::load('id', $_GET['id']);
                
                $user->set_name($_POST['user_name']);
                $user->set_forename($_POST['user_forename']);
                $user->set_surname($_POST['user_surname']);
                $user->set_email($_POST['user_email']);
                if ($change_password) {
                    $user->set_password($password);
                }
                $user->set_permission('permission_articles', $permissions['articles']);
                $user->set_permission('permission_menupoints', $permissions['menupoints']);
                $user->set_permission('permission_texts', $permissions['texts']);
                $user->set_permission('permission_links', $permissions['links']);
                $user->set_permission('permission_blogposts', $permissions['blogposts']);
                $user->set_permission('permission_blogcomments', $permissions['blogcomments']);
                $user->set_permission('permission_users', $permissions['users']);
                $user->set_permission('permission_plugins', $permissions['plugins']);
                $user->set_permission('permission_templates', $permissions['templates']);
                $user->set_permission('permission_settings', $permissions['settings']);
                
                $user->save();
                
                // set the new session user, if this one has changed
                if ($user->get_id() == $pec_session->get('pec_user')->get_id()) {
                	$pec_session->set('pec_user', $user);
                }
                
                $messages .= PecMessageHandler::get('content_edited', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USER'),
                    '{%NAME%}' => $user->get_name()
                ));
            }
            else {
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USER'),
                    '{%ID%}' => ''
                ));
            }
        }
        
        // REMOVE
        elseif ($_GET['action'] == 'remove' && isset($_GET['id'])) {
            if (PecUser::exists('id', $_GET['id'])) {
                if ($_GET['id'] != $pec_session->get('pec_user')->get_id()) {
                    $user = PecUser::load('id', $_GET['id']);
                    $user_name = $user->get_name();
                    $user->remove();
                    
                    $messages .= PecMessageHandler::get('content_removed', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USER'),
                        '{%NAME%}' => $user_name
                    ));
                }
                else {
                    $messages .= PecMessageHandler::get('user_remove_current_failed', array(
                        '{%NAME%}' => $pec_session->get('pec_user')->get_name()
                    ));
                }
            }
            else {                
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USER'),
                    '{%ID%}' => $_GET['id']
                ));
            }
        }
        
        // DEFAULT ACTIONS (REMOVE MULTIPLE)
        elseif ($_GET['action'] == 'default_view_actions') {
        
            // REMOVE MULTIPLE
            if (isset($_POST['remove_users'])) {
                if (!empty($_POST['remove_box'])) {
                	
                    $removed_some = false;
                    foreach ($_POST['remove_box'] as $user_id) {
                    	if ($user_id != $pec_session->get('pec_user')->get_id()) {
                    		$removed_some = true;
	                        $user = PecUser::load('id', $user_id);
	                        $user->remove();
                    	}
                    	else {
		                    $messages .= PecMessageHandler::get('user_remove_current_failed', array(
		                        '{%NAME%}' => $pec_session->get('pec_user')->get_name()
		                    ));
                    	}
                    }

                    if ($removed_some) {
	                    $messages .= PecMessageHandler::get('content_removed_multiple', array(
	                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USERS'),
	                        '{%NAME%}' => ''
	                    ));
                    }
                }
                else {
                    $messages .= PecMessageHandler::get('content_not_selected', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_USERS'),
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
    $area_data['title'] = 'Users';
    
    if (isset($_GET['id'])) {
        if (PecUser::exists('id', $_GET['id'])) {
            $user = PecUser::load('id', $_GET['id']);
            $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_USERS_EDIT') . ' &raquo; ' . $user->get_name();
            
            $action = 'save';
            $id_query_var = '&amp;id=' . $_GET['id'];
        }
        else {
            pec_redirect('pec_admin/' . AREA . '&message=content_not_found_id&message_data=' . $_GET['id']);
        }
    }
    else {
        // create an empty user
        $user = new PecUser(NULL_ID, '', '', '', '', '', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
        $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_USERS_CREATE');
        
        $action = 'create';
        $id_query_var = '';
    }

    $permission_checked = array();
    $permission_checked['articles_none']         = $user->get_permission('permission_articles')         == PERMISSION_NONE ? 'checked="checked"' : '';
    $permission_checked['articles_read']         = $user->get_permission('permission_articles')         == PERMISSION_READ ? 'checked="checked"' : '';
    $permission_checked['articles_full']         = $user->get_permission('permission_articles')         == PERMISSION_FULL ? 'checked="checked"' : '';
    
    $permission_checked['menupoints_none']        = $user->get_permission('permission_menupoints')     == PERMISSION_NONE    ? 'checked="checked"' : '';
    $permission_checked['menupoints_read']        = $user->get_permission('permission_menupoints')    == PERMISSION_READ    ? 'checked="checked"' : '';
    $permission_checked['menupoints_full']        = $user->get_permission('permission_menupoints')     == PERMISSION_FULL    ? 'checked="checked"' : '';
    
    $permission_checked['texts_none']            = $user->get_permission('permission_texts')         == PERMISSION_NONE ? 'checked="checked"' : '';
    $permission_checked['texts_read']            = $user->get_permission('permission_texts')         == PERMISSION_READ ? 'checked="checked"' : '';
    $permission_checked['texts_full']            = $user->get_permission('permission_texts')            == PERMISSION_FULL ? 'checked="checked"' : '';
    
    $permission_checked['links_none']            = $user->get_permission('permission_links')         == PERMISSION_NONE ? 'checked="checked"' : '';
    $permission_checked['links_read']            = $user->get_permission('permission_links')         == PERMISSION_READ ? 'checked="checked"' : '';
    $permission_checked['links_full']            = $user->get_permission('permission_links')         == PERMISSION_FULL ? 'checked="checked"' : '';
    
    $permission_checked['posts_none']        = $user->get_permission('permission_blogposts')     == PERMISSION_NONE ? 'checked="checked"' : '';
    $permission_checked['posts_read']        = $user->get_permission('permission_blogposts')     == PERMISSION_READ ? 'checked="checked"' : '';
    $permission_checked['posts_full']        = $user->get_permission('permission_blogposts')     == PERMISSION_FULL ? 'checked="checked"' : '';
    
    $permission_checked['comments_none']    = $user->get_permission('permission_blogcomments')     == PERMISSION_NONE ? 'checked="checked"' : '';
    $permission_checked['comments_read']    = $user->get_permission('permission_blogcomments')     == PERMISSION_READ ? 'checked="checked"' : '';
    $permission_checked['comments_full']    = $user->get_permission('permission_blogcomments')     == PERMISSION_FULL ? 'checked="checked"' : '';
    
    $permission_checked['users_none']            = $user->get_permission('permission_users')         == PERMISSION_NONE ? 'checked="checked"' : '';
    $permission_checked['users_read']            = $user->get_permission('permission_users')         == PERMISSION_READ ? 'checked="checked"' : '';
    $permission_checked['users_full']            = $user->get_permission('permission_users')         == PERMISSION_FULL ? 'checked="checked"' : '';
    
    $permission_checked['plugins_none']        = $user->get_permission('permission_plugins')         == PERMISSION_NONE ? 'checked="checked"' : '';
    $permission_checked['plugins_read']        = $user->get_permission('permission_plugins')         == PERMISSION_READ ? 'checked="checked"' : '';
    $permission_checked['plugins_full']        = $user->get_permission('permission_plugins')         == PERMISSION_FULL ? 'checked="checked"' : '';
    
    $permission_checked['templates_none']        = $user->get_permission('permission_templates')     == PERMISSION_NONE ? 'checked="checked"' : '';
    $permission_checked['templates_read']        = $user->get_permission('permission_templates')     == PERMISSION_READ ? 'checked="checked"' : '';
    $permission_checked['templates_full']        = $user->get_permission('permission_templates')     == PERMISSION_FULL ? 'checked="checked"' : '';
    
    $permission_checked['settings_none']        = $user->get_permission('permission_settings')         == PERMISSION_NONE ? 'checked="checked"' : '';
    $permission_checked['settings_read']        = $user->get_permission('permission_settings')         == PERMISSION_READ ? 'checked="checked"' : '';
    $permission_checked['settings_full']        = $user->get_permission('permission_settings')         == PERMISSION_FULL ? 'checked="checked"' : '';
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&ampview=default&amp;action=' . $action . $id_query_var . '" id="users_edit_form" />
            <h3>' . $pec_localization->get('LABEL_USERS_NAME') . ':</h3>
            <input type="text" size="75" name="user_name" value="' . $user->get_name() . '" />
            <br /><br />
            
            <h3>' . $pec_localization->get('LABEL_USERS_FORENAME') . ':</h3>
            <input type="text" size="75" name="user_forename" value="' . $user->get_forename() . '" />
            <br /><br />
            
            <h3>' . $pec_localization->get('LABEL_USERS_SURNAME') . ':</h3>
            <input type="text" size="75" name="user_surname" value="' . $user->get_surname() . '" />
            <br /><br />
            
            <h3>' . $pec_localization->get('LABEL_USERS_EMAIL') . ':</h3>
            <input type="text" size="75" name="user_email" value="' . $user->get_email() . '" />
            <br /><br />            
            
            <div class="options_box_1" style="width: 350px;">
                <h3>' . $pec_localization->get('LABEL_USERS_PASSWORD') . ':</h3>
                <input type="password" size="45" name="user_password" value="" />
                <br /><br />
                
                <h3>' . $pec_localization->get('LABEL_USERS_PASSWORD_REPEAT') . ':</h3>
                <input type="password" size="45" name="user_password_repeat" value="" />
                <br /><br />
                
                <em>' . $pec_localization->get('LABEL_USERS_PASSWORD_CHANGE_INFO') . '</em>
            </div>
                        
            <div class="options_box_1" style="width: 350px;">
                <h3>' . $pec_localization->get('LABEL_USERS_PERMISSIONS') . ':</h3>
                
                <div class="checkbox_data_selector" id="permission_selector" style="padding: 5px !important; height: 181px;">
                    <div class="checkbox_data_row">
                        ' . $pec_localization->get('LABEL_GENERAL_ARTICLES') . '
                        <div style="float: right;">
                            <input type="radio" name="user_perm_articles" id="perm_articles_none" value="' . PERMISSION_NONE . '" ' . $permission_checked['articles_none'] . '/> 
                            <label for="perm_articles_none">' . $pec_localization->get('LABEL_USERS_PERM_NONE') . '</label>&nbsp;&nbsp;
                                                
                            <input type="radio" name="user_perm_articles" id="perm_articles_read" value="' . PERMISSION_READ . '" ' . $permission_checked['articles_read'] . '/> 
                            <label for="perm_articles_read">' . $pec_localization->get('LABEL_USERS_PERM_READ') . '</label>&nbsp;&nbsp;
                                                    
                            <input type="radio" name="user_perm_articles" id="perm_articles_full" value="' . PERMISSION_FULL . '" ' . $permission_checked['articles_full'] . '/> 
                            <label for="perm_articles_full">' . $pec_localization->get('LABEL_USERS_PERM_FULL') . '</label>
                        </div>
                        <br />
                                    
                    </div>
                    <div class="checkbox_data_row">
                        ' . $pec_localization->get('LABEL_GENERAL_MENUPOINTS') . '
                        <div style="float: right;">
                            <input type="radio" name="user_perm_menupoints" id="perm_menupoints_none" value="' . PERMISSION_NONE . '" ' . $permission_checked['menupoints_none'] . '/> 
                            <label for="perm_menupoints_none">' . $pec_localization->get('LABEL_USERS_PERM_NONE') . '</label>&nbsp;&nbsp;
                                                
                            <input type="radio" name="user_perm_menupoints" id="perm_menupoints_read" value="' . PERMISSION_READ . '" ' . $permission_checked['menupoints_read'] . '/> 
                            <label for="perm_menupoints_read">' . $pec_localization->get('LABEL_USERS_PERM_READ') . '</label>&nbsp;&nbsp;
                                                    
                            <input type="radio" name="user_perm_menupoints" id="perm_menupoints_full" value="' . PERMISSION_FULL . '" ' . $permission_checked['menupoints_full'] . '/> 
                            <label for="perm_menupoints_full">' . $pec_localization->get('LABEL_USERS_PERM_FULL') . '</label>
                        </div>
                        <br />                
                    </div>
                    <div class="checkbox_data_row">
                        ' . $pec_localization->get('LABEL_GENERAL_TEXTS') . '
                        <div style="float: right;">
                            <input type="radio" name="user_perm_texts" id="perm_texts_none" value="' . PERMISSION_NONE . '" ' . $permission_checked['texts_none'] . '/> 
                            <label for="perm_texts_none">' . $pec_localization->get('LABEL_USERS_PERM_NONE') . '</label>&nbsp;&nbsp;
                                                
                            <input type="radio" name="user_perm_texts" id="perm_texts_read" value="' . PERMISSION_READ . '" ' . $permission_checked['texts_read'] . '/> 
                            <label for="perm_texts_read">' . $pec_localization->get('LABEL_USERS_PERM_READ') . '</label>&nbsp;&nbsp;
                                                    
                            <input type="radio" name="user_perm_texts" id="perm_texts_full" value="' . PERMISSION_FULL . '" ' . $permission_checked['texts_full'] . '/> 
                            <label for="perm_texts_full">' . $pec_localization->get('LABEL_USERS_PERM_FULL') . '</label>
                        </div>
                        <br />                
                    </div>
                    <div class="checkbox_data_row">
                        ' . $pec_localization->get('LABEL_GENERAL_LINKS') . '
                        <div style="float: right;">
                            <input type="radio" name="user_perm_links" id="perm_links_none" value="' . PERMISSION_NONE . '" ' . $permission_checked['links_none'] . '/> 
                            <label for="perm_links_none">' . $pec_localization->get('LABEL_USERS_PERM_NONE') . '</label>&nbsp;&nbsp;
                                                
                            <input type="radio" name="user_perm_links" id="perm_links_read" value="' . PERMISSION_READ . '" ' . $permission_checked['links_read'] . '/> 
                            <label for="perm_links_read">' . $pec_localization->get('LABEL_USERS_PERM_READ') . '</label>&nbsp;&nbsp;
                                                    
                            <input type="radio" name="user_perm_links" id="perm_links_full" value="' . PERMISSION_FULL . '" ' . $permission_checked['links_full'] . '/> 
                            <label for="perm_links_full">' . $pec_localization->get('LABEL_USERS_PERM_FULL') . '</label>
                        </div>
                        <br />                
                    </div>
                    <div class="checkbox_data_row">
                        ' . $pec_localization->get('LABEL_GENERAL_BLOGPOSTS') . '
                        <div style="float: right;">
                            <input type="radio" name="user_perm_posts" id="perm_posts_none" value="' . PERMISSION_NONE . '" ' . $permission_checked['posts_none'] . '/> 
                            <label for="perm_posts_none">' . $pec_localization->get('LABEL_USERS_PERM_NONE') . '</label>&nbsp;&nbsp;
                                                
                            <input type="radio" name="user_perm_posts" id="perm_posts_read" value="' . PERMISSION_READ . '" ' . $permission_checked['posts_read'] . '/> 
                            <label for="perm_posts_read">' . $pec_localization->get('LABEL_USERS_PERM_READ') . '</label>&nbsp;&nbsp;
                                                    
                            <input type="radio" name="user_perm_posts" id="perm_posts_full" value="' . PERMISSION_FULL . '" ' . $permission_checked['posts_full'] . '/> 
                            <label for="perm_posts_full">' . $pec_localization->get('LABEL_USERS_PERM_FULL') . '</label>
                        </div>
                        <br />                
                    </div>
                    <div class="checkbox_data_row">
                        ' . $pec_localization->get('LABEL_GENERAL_BLOGCOMMENTS') . '
                        <div style="float: right;">
                            <input type="radio" name="user_perm_comments" id="perm_comments_none" value="' . PERMISSION_NONE . '" ' . $permission_checked['comments_none'] . '/> 
                            <label for="perm_comments_none">' . $pec_localization->get('LABEL_USERS_PERM_NONE') . '</label>&nbsp;&nbsp;
                                                
                            <input type="radio" name="user_perm_comments" id="perm_comments_read" value="' . PERMISSION_READ . '" ' . $permission_checked['comments_read'] . '/> 
                            <label for="perm_comments_read">' . $pec_localization->get('LABEL_USERS_PERM_READ') . '</label>&nbsp;&nbsp;
                                                    
                            <input type="radio" name="user_perm_comments" id="perm_comments_full" value="' . PERMISSION_FULL . '" ' . $permission_checked['comments_full'] . '/> 
                            <label for="perm_comments_full">' . $pec_localization->get('LABEL_USERS_PERM_FULL') . '</label>
                        </div>
                        <br />                
                    </div>
                    <div class="checkbox_data_row">
                        ' . $pec_localization->get('LABEL_GENERAL_USERS') . '
                        <div style="float: right;">
                            <input type="radio" name="user_perm_users" id="perm_users_none" value="' . PERMISSION_NONE . '" ' . $permission_checked['users_none'] . '/> 
                            <label for="perm_users_none">' . $pec_localization->get('LABEL_USERS_PERM_NONE') . '</label>&nbsp;&nbsp;
                                                
                            <input type="radio" name="user_perm_users" id="perm_users_read" value="' . PERMISSION_READ . '" ' . $permission_checked['users_read'] . '/> 
                            <label for="perm_users_read">' . $pec_localization->get('LABEL_USERS_PERM_READ') . '</label>&nbsp;&nbsp;
                                                    
                            <input type="radio" name="user_perm_users" id="perm_users_full" value="' . PERMISSION_FULL . '" ' . $permission_checked['users_full'] . '/> 
                            <label for="perm_users_full">' . $pec_localization->get('LABEL_USERS_PERM_FULL') . '</label>
                        </div>
                        <br />                
                    </div>
                    <div class="checkbox_data_row">
                        ' . $pec_localization->get('LABEL_GENERAL_PLUGINS') . '
                        <div style="float: right;">
                            <input type="radio" name="user_perm_plugins" id="perm_plugins_none" value="' . PERMISSION_NONE . '" ' . $permission_checked['plugins_none'] . '/> 
                            <label for="perm_plugins_none">' . $pec_localization->get('LABEL_USERS_PERM_NONE') . '</label>&nbsp;&nbsp;
                                                
                            <input type="radio" name="user_perm_plugins" id="perm_plugins_read" value="' . PERMISSION_READ . '" ' . $permission_checked['plugins_read'] . '/> 
                            <label for="perm_plugins_read">' . $pec_localization->get('LABEL_USERS_PERM_READ') . '</label>&nbsp;&nbsp;
                                                    
                            <input type="radio" name="user_perm_plugins" id="perm_plugins_full" value="' . PERMISSION_FULL . '" ' . $permission_checked['plugins_full'] . '/> 
                            <label for="perm_plugins_full">' . $pec_localization->get('LABEL_USERS_PERM_FULL') . '</label>
                        </div>
                        <br />                
                    </div>
                    <div class="checkbox_data_row">
                        ' . $pec_localization->get('LABEL_GENERAL_TEMPLATES') . '
                        <div style="float: right;">
                            <input type="radio" name="user_perm_templates" id="perm_templates_none" value="' . PERMISSION_NONE . '" ' . $permission_checked['templates_none'] . '/> 
                            <label for="perm_templates_none">' . $pec_localization->get('LABEL_USERS_PERM_NONE') . '</label>&nbsp;&nbsp;
                                                
                            <input type="radio" name="user_perm_templates" id="perm_templates_read" value="' . PERMISSION_READ . '" ' . $permission_checked['templates_read'] . '/> 
                            <label for="perm_templates_read">' . $pec_localization->get('LABEL_USERS_PERM_READ') . '</label>&nbsp;&nbsp;
                                                    
                            <input type="radio" name="user_perm_templates" id="perm_templates_full" value="' . PERMISSION_FULL . '" ' . $permission_checked['templates_full'] . '/> 
                            <label for="perm_templates_full">' . $pec_localization->get('LABEL_USERS_PERM_FULL') . '</label>
                        </div>
                        <br />                
                    </div>
                    <div class="checkbox_data_row">
                        ' . $pec_localization->get('LABEL_GENERAL_SETTINGS') . '
                        <div style="float: right;">
                            <input type="radio" name="user_perm_settings" id="perm_settings_none" value="' . PERMISSION_NONE . '" ' . $permission_checked['settings_none'] . '/> 
                            <label for="perm_settings_none">' . $pec_localization->get('LABEL_USERS_PERM_NONE') . '</label>&nbsp;&nbsp;
                                                
                            <input type="radio" name="user_perm_settings" id="perm_settings_read" value="' . PERMISSION_READ . '" ' . $permission_checked['settings_read'] . '/> 
                            <label for="perm_settings_read">' . $pec_localization->get('LABEL_USERS_PERM_READ') . '</label>&nbsp;&nbsp;
                                                    
                            <input type="radio" name="user_perm_settings" id="perm_settings_full" value="' . PERMISSION_FULL . '" ' . $permission_checked['settings_full'] . '/> 
                            <label for="perm_settings_full">' . $pec_localization->get('LABEL_USERS_PERM_FULL') . '</label>
                        </div>
                        <br />
                    </div>
                </div>
                
            </div>
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
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_USERS');
    
    $users = PecUser::load();
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&amp;view=default&amp;action=default_view_actions" id="users_main_form" onsubmit="return confirm(\'' . $pec_localization->get('LABEL_USERS_REALLYREMOVE_SELECTED') . '\');" />
            <input type="button" value="' . $pec_localization->get('BUTTON_NEW_USER') . '" onclick="location.href=\'' . AREA . '&amp;view=edit\'"/>
            <input type="submit" name="remove_users" value="' . $pec_localization->get('BUTTON_REMOVE') . '" /><br /><br />
            
            <table class="data_table">
                <tr class="head">
                    <td class="check"><input type="checkbox" onclick="checkbox_mark_all(\'remove_box\', \'users_main_form\', this);" /></td>
                    <td class="long">' . $pec_localization->get('LABEL_USERS_NAME') . '</td>
                    <td class="medium">' . $pec_localization->get('LABEL_GENERAL_SLUG') . '</td>
                    <td class="long">' . $pec_localization->get('LABEL_USERS_ADDITIONAL_INFO') . '</td>
                    <td class="short">' . $pec_localization->get('LABEL_USERS_ADMIN') . '</td>
                <tr>
    ';
    
    foreach ($users as $u) {
        $superadmin_string = $u->is_superadmin() ? '&#x2713;' : '&#x2717;';
        $area_data['content'] .= '
                    <tr class="data" title="#' . $u->get_id() . '">
                        <td class="check"><input type="checkbox" class="remove_box" name="remove_box[]" value="' . $u->get_id() . '" /></td>
                        <td class="long">
                            <a href="' . AREA . '&amp;view=edit&amp;id=' . $u->get_id() . '"><span class="main_text">' . $u->get_name() . '</span></a>
                            <div class="row_actions">
                                <a href="' . AREA . '&amp;view=edit&amp;id=' . $u->get_id() . '">' . $pec_localization->get('ACTION_EDIT') . '</a> - 
                                <a href="javascript:ask(\'' . $pec_localization->get('LABEL_USERS_REALLYREMOVE') . '\', \'' . AREA . '&amp;view=default&amp;action=remove&amp;id=' . $u->get_id() . '\');">
                                	' . $pec_localization->get('ACTION_REMOVE') . '
                                </a>
                            </div>
                        </td>
                        <td class="medium">' . $u->get_slug() . '</td>
                        <td class="long">
                            ' . $u->get_forename() . ' ' . $u->get_surname() . '<br />
                            <em>' . $u->get_email() . '</em>
                        </td>
                        <td class="short">' . $superadmin_string . '</td>
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
