<?php 

/**
 * pec_admin/pec_areas/articles.area.php - Managing plugins
 * 
 * Admin area to manage plugins.
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

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=plugins');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_PLUGINS');
$area["permission_name"] = 'permission_plugins';
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
            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_PLUGIN'),
            '{%NAME%}' => $_GET['message_data'],
            '{%ID%}' => $_GET['message_data']
        ));
        
    }
        
    if (isset($_GET['action'])) {
        
        // INSTALL
        if ($_GET['action'] == 'install' && isset($_GET['plugin_area_name'])) {
        	
        	// exists?
            if (PecPlugin::exists('area_name', $_GET['plugin_area_name'])) {
                $plugin = PecPlugin::load('area_name', $_GET['plugin_area_name']);
                
                if ($plugin->installation_required() && !$plugin->is_installed()) {
                	
                	if ($plugin->set_installed()) {
	    				global $pec_database;
	                	require_once(PLUGIN_PATH . $plugin->get_directory_name() . '/' . PLUGIN_INSTALL_FILE);
	                	
		                $messages .= PecMessageHandler::get('plugin_installed', array(
		                    '{%NAME%}' => $plugin->get_property('title')
		                ));                		
                	}
                	else {            
		                $messages .= PecMessageHandler::get('plugin_install_rename_failed', array(
		                    '{%NAME%}' => $plugin->get_property('title')
		                )); 
                	}
                }
                else {                
	                $messages .= PecMessageHandler::get('plugin_is_installed', array(
	                    '{%NAME%}' => $plugin->get_property('title')
	                ));                	
                }
            }
            else {
                $messages .= PecMessageHandler::get('content_not_found', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_PLUGIN'),
                    '{%NAME%}' => $_POST['plugin_area_name']
                ));
            }
        }
        
        // UNINSTALL
        elseif ($_GET['action'] == 'uninstall' && isset($_GET['plugin_area_name'])) {
        	
        	// exists?
            if (PecPlugin::exists('area_name', $_GET['plugin_area_name'])) {
                $plugin = PecPlugin::load('area_name', $_GET['plugin_area_name']);
                
                if ($plugin->installation_required() && $plugin->is_installed()) {
                	              	
                	if ($plugin->set_uninstalled()) {                			
    					global $pec_database;
                		require_once(PLUGIN_PATH . $plugin->get_directory_name() . '/' . PLUGIN_UNINSTALL_FILE);
                		
		                $messages .= PecMessageHandler::get('plugin_uninstalled', array(
		                    '{%NAME%}' => $plugin->get_property('title')
		                ));                		
                	}
                	else {            
		                $messages .= PecMessageHandler::get('plugin_uninstall_rename_failed', array(
		                    '{%NAME%}' => $plugin->get_property('title')
		                )); 
                	}
                	
                }
                else {                
	                $messages .= PecMessageHandler::get('plugin_is_not_installed', array(
	                    '{%NAME%}' => $plugin->get_property('title')
	                ));                	
                }
            }
            else {
                $messages .= PecMessageHandler::get('content_not_found', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_PLUGIN'),
                    '{%NAME%}' => $_POST['plugin_area_name']
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
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_PLUGINS');    
    
    $plugins = PecPlugin::load();
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&amp;view=default" id="plugins_main_form" />            
            <table class="data_table" cellspacing="0">
                <thead>
                    <tr class="head_row">
                        <th class="long_column">' . $pec_localization->get('LABEL_GENERAL_TITLE') . '</th>
                        <th class="medium_column">' . $pec_localization->get('LABEL_PLUGINS_AUTHOR') . '</th>
                        <th class="thin_column">' . $pec_localization->get('LABEL_PLUGINS_YEAR') . '</th>
                        <th class="short_column">' . $pec_localization->get('LABEL_PLUGINS_LICENSE') . '</th>
                        <th class="short_column">' . $pec_localization->get('LABEL_PLUGINS_ACTIONS') . '</th>
                    <tr>
                </thead>
                <tbody>
    ';
    
    foreach ($plugins as $p) {
    	if ($p->installation_required() && $p->is_installed()) {
    		$actions = '<input type="button" value="' . $pec_localization->get('BUTTON_UNINSTALL') . '" onclick="location.href=\'' . AREA . '&amp;action=uninstall&amp;plugin_area_name=' . $p->get_property('area_name') . '\';" />';
    	}
    	elseif ($p->installation_required() && !$p->is_installed()) {
    		$actions = '<input type="button" value="' . $pec_localization->get('BUTTON_INSTALL') . '" onclick="location.href=\'' . AREA . '&amp;action=install&amp;plugin_area_name=' . $p->get_property('area_name') . '\';" />';
    	}
    	elseif (!$p->installation_required()) {
    		$actions = '<span style="font-style: italic; color: #7c7c7c;">' . $pec_localization->get('LABEL_PLUGINS_NO_INSTALL_REQUIRED') . '</span>';
    	}
    	else {
    		$actions = '-';
    	}
    	
        $area_data['content'] .= '
                    <tr class="data_row">
                        <td class="normal_column">
                            <span class="main_text">' . $p->get_property('title') . '</span><br />
                            ' . $p->get_property('description') . '
                        </td>
                        <td class="normal_column">
                            ' . $p->get_property('author') . '<br />
                            <em>' . $p->get_property('author_email') . '</em>
                        </td>
                        <td class="normal_column">' . $p->get_property('year') . '</td>
                        <td class="normal_column">' . $p->get_property('license') . '</td>
                        <td class="normal_column" style="vertical-align: middle;">
                        	' . $actions . '
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