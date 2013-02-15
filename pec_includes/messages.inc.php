<?php

/**
 * pec_includes/messages.inc.php - Creates global message array
 * 
 * Defines the function which creates an array of all available messages.
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
 * @subpackage	pec_includes
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */


/**
 * Generates the available messages which can be displayed by the CMS
 * 
 * @return	array All the messages, e.g. Array("content_created" => Array("Created Something", "I created some content.", MESSAGE_INFO))
 */
function generate_messages() {
    global $pec_localization;
    
    $messages = array(
        
    
        // NOT FOUND WARNINGS
        
        'content_not_found' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_NOT_FOUND'),
            $pec_localization->get('MSG_TEXT_CONTENT_NOT_FOUND'),
            MESSAGE_WARNING
        ),
        'content_not_found_id' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_NOT_FOUND_ID'),
            $pec_localization->get('MSG_TEXT_CONTENT_NOT_FOUND_ID'),
            MESSAGE_WARNING
        ),
    
        
        // CONTENT - GENERAL
        
        'content_not_selected' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_NOT_SELECTED'), 
            $pec_localization->get('MSG_TEXT_CONTENT_NOT_SELECTED'),
            MESSAGE_WARNING
        ),    
        'content_created' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_CREATED'), 
            $pec_localization->get('MSG_TEXT_CONTENT_CREATED'),
            MESSAGE_INFO
        ),    
        'content_edited' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_EDITED'), 
            $pec_localization->get('MSG_TEXT_CONTENT_EDITED'),
            MESSAGE_INFO
        ),    
        'content_removed' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_REMOVED'), 
            $pec_localization->get('MSG_TEXT_CONTENT_REMOVED'),
            MESSAGE_INFO
        ),    
        'content_sorted' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_SORTED'), 
            $pec_localization->get('MSG_TEXT_CONTENT_SORTED'),
            MESSAGE_INFO
        ),    
        'content_published' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_PUBLISHED'), 
            $pec_localization->get('MSG_TEXT_CONTENT_PUBLISHED'),
            MESSAGE_INFO
        ),    
        'content_unpublished' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_UNPUBLISHED'), 
            $pec_localization->get('MSG_TEXT_CONTENT_UNPUBLISHED'),
            MESSAGE_INFO
        ),    
        'content_removed_multiple' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_REMOVED_MULTIPLE'), 
            $pec_localization->get('MSG_TEXT_CONTENT_REMOVED_MULTIPLE'),
            MESSAGE_INFO
        ),    
        'content_cached' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_CACHED'),
            $pec_localization->get('MSG_TEXT_CONTENT_CACHED'),
            MESSAGE_INFO
        ),    
        'content_activated' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_ACTIVATED'), 
            $pec_localization->get('MSG_TEXT_CONTENT_ACTIVATED'),
            MESSAGE_INFO
        ),    
        'content_exists' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_EXISTS'), 
            $pec_localization->get('MSG_TEXT_CONTENT_EXISTS'),
            MESSAGE_WARNING
        ),    
        'content_integer_required' => array(
            $pec_localization->get('MSG_TITLE_CONTENT_INTEGER_REQUIRED'),
            $pec_localization->get('MSG_TEXT_CONTENT_INTEGER_REQUIRED'),
            MESSAGE_WARNING
        ),
        
        
        // SETTINGS
    
        'settings_saved' => array(
            $pec_localization->get('MSG_TITLE_SETTINGS_SAVED'),
            $pec_localization->get('MSG_TEXT_SETTINGS_SAVED'),
            MESSAGE_INFO
        ),
    
        'settings_nospam_keys_generated' => array(
            $pec_localization->get('MSG_TITLE_SETTINGS_NOSPAM_KEYS_GENERATED'),
            $pec_localization->get('MSG_TEXT_SETTINGS_NOSPAM_KEYS_GENERATED'),
            MESSAGE_INFO
        ),
    
        
        // LOGIN
        
        'login_incorrect' => array(
            $pec_localization->get('MSG_TITLE_LOGIN_INCORRECT'),
            $pec_localization->get('MSG_TEXT_LOGIN_INCORRECT'),
            MESSAGE_WARNING
        ),    
        'logout_done' => array(
            $pec_localization->get('MSG_TITLE_LOGOUT_DONE'),
            $pec_localization->get('MSG_TEXT_LOGOUT_DONE'),
            MESSAGE_INFO
        ),
    
        
        // USER
        
        'user_remove_current_failed' => array(
            $pec_localization->get('MSG_TITLE_USER_REMOVE_CURRENT_FAILED'),
            $pec_localization->get('MSG_TEXT_USER_REMOVE_CURRENT_FAILED'),
            MESSAGE_WARNING
        ),    
        'password_empty' => array(
            $pec_localization->get('MSG_TITLE_PASSWORD_EMPTY'),
            $pec_localization->get('MSG_TEXT_PASSWORD_EMPTY'),
            MESSAGE_WARNING
        ),    
        'password_repeat_incorrect' => array(
            $pec_localization->get('MSG_TITLE_PASSWORD_REPEAT_INCORRECT'),
            $pec_localization->get('MSG_TEXT_PASSWORD_REPEAT_INCORRECT'),
            MESSAGE_WARNING
        ),    
        'password_repeat_incorrect_save' => array(
            $pec_localization->get('MSG_TITLE_PASSWORD_REPEAT_INCORRECT_SAVE'),
            $pec_localization->get('MSG_TEXT_PASSWORD_REPEAT_INCORRECT_SAVE'),
            MESSAGE_WARNING
        ),
        
        
        // LOST PASSWORD
        
        'user_email_not_found' => array(
            $pec_localization->get('MSG_TITLE_USER_EMAIL_NOT_FOUND'),
            $pec_localization->get('MSG_TEXT_USER_EMAIL_NOT_FOUND'),
            MESSAGE_WARNING
        ),        
        'user_pw_link_sent' => array(
            $pec_localization->get('MSG_TITLE_USER_PW_LINK_SENT'),
            $pec_localization->get('MSG_TEXT_USER_PW_LINK_SENT'),
            MESSAGE_INFO
        ),    
        'user_pw_link_incorrect_data' => array(
            $pec_localization->get('MSG_TITLE_USER_PW_LINK_INCORRECT_DATA'),
            $pec_localization->get('MSG_TEXT_USER_PW_LINK_INCORRECT_DATA'),
            MESSAGE_WARNING
        ),
        'user_pw_link_expired' => array(
            $pec_localization->get('MSG_TITLE_USER_PW_LINK_EXPIRED'),
            $pec_localization->get('MSG_TEXT_USER_PW_LINK_EXPIRED'),
            MESSAGE_WARNING
        ),
        'user_new_pw_repeat_incorrect' => array(
            $pec_localization->get('MSG_TITLE_USER_NEW_PW_REPEAT_INCORRECT'),
            $pec_localization->get('MSG_TEXT_USER_NEW_PW_REPEAT_INCORRECT'),
            MESSAGE_WARNING
        ),
        'user_new_pw_changed' => array(
            $pec_localization->get('MSG_TITLE_USER_NEW_PW_CHANGED'),
            $pec_localization->get('MSG_TEXT_USER_NEW_PW_CHANGED'),
            MESSAGE_INFO
        ),
        
        
        'redirect' => array(
            $pec_localization->get('MSG_TITLE_REDIRECT'),
            $pec_localization->get('MSG_TEXT_REDIRECT'),
            MESSAGE_INFO
        ),
        
        
        // COMMENTS
        
        'comment_created' => array(
            $pec_localization->get('MSG_TITLE_COMMENT_CREATED'), 
            $pec_localization->get('MSG_TEXT_COMMENT_CREATED'),
            MESSAGE_INFO
        ),    
        'comment_empty_fields' => array(
            $pec_localization->get('MSG_TITLE_COMMENT_EMPTY_FIELDS'), 
            $pec_localization->get('MSG_TEXT_COMMENT_EMPTY_FIELDS'),
            MESSAGE_WARNING
        ),    
        'comment_email_incorrect' => array(
            $pec_localization->get('MSG_TITLE_COMMENT_EMAIL_INCORRECT'), 
            $pec_localization->get('MSG_TEXT_COMMENT_EMAIL_INCORRECT'),
            MESSAGE_WARNING
        ),
        
        
        // DATABASE
        
        'db_connection_failed' => array(
            $pec_localization->get('MSG_TITLE_DB_CONNECTION_FAILED'), 
            $pec_localization->get('MSG_TEXT_DB_CONNECTION_FAILED'),
            MESSAGE_WARNING
        ),
        
        
        // INSTALLATION
        
        'installation_success' => array(
            $pec_localization->get('MSG_TITLE_INSTALLATION_SUCCESS'), 
            $pec_localization->get('MSG_TEXT_INSTALLATION_SUCCESS'),
            MESSAGE_INFO
        ),
        'installation_error' => array(
            $pec_localization->get('MSG_TITLE_INSTALLATION_ERROR'), 
            $pec_localization->get('MSG_TEXT_INSTALLATION_ERROR'),
            MESSAGE_WARNING
        ),
        'install_directory_remove_required' => array(
            $pec_localization->get('MSG_TITLE_INSTALL_DIRECTORY_REMOVE_REQUIRED'), 
            $pec_localization->get('MSG_TEXT_INSTALL_DIRECTORY_REMOVE_REQUIRED'),
            MESSAGE_WARNING
        ),
        
        
        // UPDATE
        
        'available_cms_files_too_old' => array(
            $pec_localization->get('MSG_TITLE_AVAILABLE_CMS_FILES_TOO_OLD'), 
            $pec_localization->get('MSG_TEXT_AVAILABLE_CMS_FILES_TOO_OLD'),
            MESSAGE_WARNING
        ),        
        'update_password_wrong' => array(
            $pec_localization->get('MSG_TITLE_UPDATE_PASSWORD_WRONG'), 
            $pec_localization->get('MSG_TEXT_UPDATE_PASSWORD_WRONG'),
            MESSAGE_WARNING
        ),        
        'cms_update_required' => array(
            $pec_localization->get('MSG_TITLE_CMS_UPDATE_REQUIRED'), 
            $pec_localization->get('MSG_TEXT_CMS_UPDATE_REQUIRED'),
            MESSAGE_INFO
        ),        
        'cms_update_successful' => array(
            $pec_localization->get('MSG_TITLE_CMS_UPDATE_SUCCESSFUL'), 
            $pec_localization->get('MSG_TEXT_CMS_UPDATE_SUCCESSFUL'),
            MESSAGE_INFO
        ),
        
        'cms_update_failed' => array(
            $pec_localization->get('MSG_TITLE_CMS_UPDATE_FAILED'), 
            $pec_localization->get('MSG_TEXT_CMS_UPDATE_FAILED'),
            MESSAGE_WARNING
        ),
        
        
        // PLUGINS
        
        'plugin_installed' => array(
            $pec_localization->get('MSG_TITLE_PLUGIN_INSTALLED'), 
            $pec_localization->get('MSG_TEXT_PLUGIN_INSTALLED'),
            MESSAGE_INFO
        ),
        
        'plugin_uninstalled' => array(
            $pec_localization->get('MSG_TITLE_PLUGIN_UNINSTALLED'), 
            $pec_localization->get('MSG_TEXT_PLUGIN_UNINSTALLED'),
            MESSAGE_INFO
        ),
        'plugin_install_rename_failed' => array(
            $pec_localization->get('MSG_TITLE_PLUGIN_INSTALL_RENAME_FAILED'), 
            $pec_localization->get('MSG_TEXT_PLUGIN_INSTALL_RENAME_FAILED'),
            MESSAGE_WARNING
        ),
        'plugin_uninstall_rename_failed' => array(
            $pec_localization->get('MSG_TITLE_PLUGIN_UNINSTALL_RENAME_FAILED'), 
            $pec_localization->get('MSG_TEXT_PLUGIN_UNINSTALL_RENAME_FAILED'),
            MESSAGE_WARNING
        ),
        'plugin_is_installed' => array(
            $pec_localization->get('MSG_TITLE_PLUGIN_IS_INSTALLED'), 
            $pec_localization->get('MSG_TEXT_PLUGIN_IS_INSTALLED'),
            MESSAGE_WARNING
        ),
        'plugin_is_not_installed' => array(
            $pec_localization->get('MSG_TITLE_PLUGIN_IS_NOT_INSTALLED'), 
            $pec_localization->get('MSG_TEXT_PLUGIN_IS_NOT_INSTALLED'),
            MESSAGE_WARNING
        )
    );
    
    return $messages;
}