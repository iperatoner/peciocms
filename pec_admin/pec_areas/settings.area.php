<?php 

/**
 * pec_admin/pec_areas/settings.area.php - Managing the settings
 * 
 * Admin area to manage the site settings.
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

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=settings');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_SETTINGS');
$area["permission_name"] = 'permission_settings';
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
            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_SETTINGS'),
            '{%NAME%}' => $_GET['message_data'],
            '{%ID%}' => $_GET['message_data']
        ));
        
    }
        
    if (isset($_GET['action'])) {
                
        global $pec_settings;
        
        // SAVE
        if ($_GET['action'] == 'save' && isset($_POST['setting_sitename_main']) && 
            isset($_POST['setting_sitename_sub']) && isset($_POST['setting_description']) &&
            isset($_POST['setting_tags']) && isset($_POST['setting_admin_email']) &&
            isset($_POST['setting_locale']) && isset($_POST['setting_url_type']) &&
            isset($_POST['setting_posts_per_page'])) {
            
            $pec_settings->set_sitename_main($_POST['setting_sitename_main']);
            $pec_settings->set_sitename_sub($_POST['setting_sitename_sub']);
            $pec_settings->set_description($_POST['setting_description']);
            $pec_settings->set_tags($_POST['setting_tags']);
            $pec_settings->set_admin_email($_POST['setting_admin_email']);
            
            $comment_notify = isset($_POST['setting_comment_notify']) ? true : false;
            $pec_settings->set_comment_notify($comment_notify);
            
            if (PecLocale::exists($_POST['setting_locale'])) {
            	$pec_settings->set_locale($_POST['setting_locale']);
            }
            
            if (in_array($_POST['setting_url_type'], array(URL_TYPE_DEFAULT, URL_TYPE_HUMAN, URL_TYPE_REWRITE))) {
            	$pec_settings->set_url_type($_POST['setting_url_type']);
            }
            
            $blog_onstart = isset($_POST['setting_blog_onstart']) ? true : false;
            $pec_settings->set_blog_onstart($blog_onstart);
            
            $posts_per_page = intval($_POST['setting_posts_per_page']);
            
            // check if an integer was passed for the posts per page setting
            if ($posts_per_page) {
                $pec_settings->set_posts_per_page($posts_per_page);
            }
            else {
                $messages .= PecMessageHandler::get('content_integer_required', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_SETTING'),
                    '{%NAME%}' => $pec_localization->get('LABEL_SETTINGS_POSTSPERPAGE')
                ));
            }
            
            $pec_settings->save();
                
            $messages .= PecMessageHandler::get('settings_saved', array());
        }
        elseif ($_GET['action'] == 'new_nospam_keys') {
        	$pec_settings->generate_new_nospam_keys();
        	$pec_settings->save();
        	
            $messages .= PecMessageHandler::get('settings_nospam_keys_generated', array());
        }        
    }
    
    return $messages;
}


/* creating functions for all the different views that will be available for this area */

function view_edit() {}

function view_default() {
    global $pec_settings, $pec_localization;
       
    $area_data = array();
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_SETTINGS');
    
    // load available locales and set the selected property for the select-box-options
    $available_locales = PecLocale::scan();
    $locale_select_options = '';
    foreach ($available_locales as $lcl) {
        if ($lcl == $pec_settings->get_locale()) {
            $selected = 'selected="selected"';
        }
        else {
            $selected = '';
        }
        $locale_select_options .= '<option value="' . $lcl . '" ' . $selected . '>' . $lcl . '</option>';
    }
    
    // set the selected property for the select-box-options of url type
    $url_type_select_options = '';
    foreach (array(URL_TYPE_DEFAULT, URL_TYPE_HUMAN, URL_TYPE_REWRITE) as $url_type) {
        if ($pec_settings->get_url_type() == $url_type) {
            $selected = 'selected="selected"';
        }
        else {
            $selected = '';
        }
        
        // Choosing the correct Label for the <select>-Entry
        switch ($url_type) {
        	case URL_TYPE_DEFAULT: $url_type_label = $pec_localization->get('LABEL_SETTINGS_URLTYPE_DEFAULT'); break;
        	case URL_TYPE_HUMAN: $url_type_label = $pec_localization->get('LABEL_SETTINGS_URLTYPE_HUMAN'); break;
        	case URL_TYPE_REWRITE: $url_type_label = $pec_localization->get('LABEL_SETTINGS_URLTYPE_REWRITE'); break;
        }
        $url_type_select_options .= '<option value="' . $url_type . '" ' . $selected . '>' . $url_type_label . '</option>';
    }
    
    $comment_notify_checked = $pec_settings->get_comment_notify() ? 'checked="checked"' : '';
    $blog_onstart_checked = $pec_settings->get_blog_onstart() ? 'checked="checked"' : '';
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&amp;view=default&amp;action=save" id="settings_main_form"/>            
            <div class="options_box_1" style="width: 500px;">
                <h3>' . $pec_localization->get('LABEL_SETTINGS_MAINTITLE') . ':</h3>
                <input type="text" style="width: 475px;" name="setting_sitename_main" value="' . $pec_settings->get_sitename_main() . '" />
                <br /><br />
                
                <h3>' . $pec_localization->get('LABEL_SETTINGS_SUBTITLE') . ':</h3>
                <input type="text" style="width: 475px;" name="setting_sitename_sub" value="' . $pec_settings->get_sitename_sub() . '" />
                <br /><br />
                
                <h3>' . $pec_localization->get('LABEL_SETTINGS_ADMINEMAIL') . ':</h3>
                <input type="text" style="width: 475px;" name="setting_admin_email" value="' . $pec_settings->get_admin_email() . '" />
                <br /><br />
                
                <h3>' . $pec_localization->get('LABEL_SETTINGS_COMMENTNOTIFY') . '</h3>
                <input type="checkbox" style="position: relative; left: 3px;" name="setting_comment_notify" value="1" ' . $comment_notify_checked . '/>
            </div>
            
            <div class="options_box_1" style="width: 500px;">
                <h3>' . $pec_localization->get('LABEL_SETTINGS_DESCRIPTION') . ':</h3>
                <textarea name="setting_description"  style="width: 475px;" rows="10">' . $pec_settings->get_description() . '</textarea>
                <br /><br />
                
                <h3>' . $pec_localization->get('LABEL_SETTINGS_TAGS') . ':</h3>
                <input type="text" style="width: 475px;" name="setting_tags" value="' . $pec_settings->get_tags() . '" />
            </div>
            
            <div class="options_box_1" style="width: 500px;">
                <h3>' . $pec_localization->get('LABEL_SETTINGS_LANGUAGE') . ':</h3>
                <select name="setting_locale" style="width: 120px;">' . $locale_select_options . '</select>
                <br /><br />
                
                <h3>' . $pec_localization->get('LABEL_SETTINGS_URLTYPE') . ':</h3>
                <select name="setting_url_type" style="width: 220px;">' . $url_type_select_options . '</select>
                <br /><br />
                
                <h3>' . $pec_localization->get('LABEL_SETTINGS_POSTSPERPAGE') . ':</h3>
                <input type="text" size="5" name="setting_posts_per_page" value="' . $pec_settings->get_posts_per_page() . '" />
                <br /><br />
                
                <h3>' . $pec_localization->get('LABEL_SETTINGS_BLOGONSTART') . ':</h3>
                <input type="checkbox" style="position: relative; left: 3px;" name="setting_blog_onstart" value="1" ' . $blog_onstart_checked . '/>
            </div>
            <br /><br />
            
            <input type="submit" value="' . $pec_localization->get('BUTTON_SAVE') . '" />
            <input type="button" value="' . $pec_localization->get('BUTTON_NEW_NOSPAM_KEYS') . '" onclick="location.href=\'' . AREA . '&action=new_nospam_keys\';" />
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