<?php

/**
 * pec_admin/index.php - Main login frontend file
 * 
 * The main login frontend file which creates the main 
 * HTML layout for the different areas, but mainly for the login screen.
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
 * @subpackage	pec_admin
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */

/* core includes, creating core objects */
require_once('../pec_includes/functions.inc.php');
require_once('common.inc.php');
require_once('../pec_core.inc.php');
/* core include end */

// INSTALL DIRECTORY CHECK
if (file_exists('../pec_install')) {
	die(PecMessageHandler::get('install_directory_remove_required'));
}

$messages = '';
$update_required = false;

// VERSION CHECK
if ($_GET['area'] != 'update') {
	if (pec_installed_version() > PEC_VERSION) {
		$messages .= PecMessageHandler::get('available_cms_files_too_old', array(
			'{%VERSION_INSTALLED%}' => pec_installed_version(),
			'{%VERSION_FILES%}' => PEC_VERSION
		));
	}
	elseif (pec_installed_version() < PEC_VERSION) {
		$update_required = true;
		$messages .= PecMessageHandler::get('cms_update_required', array(
			'{%VERSION_INSTALLED%}' => pec_installed_version(),
			'{%VERSION_FILES%}' => PEC_VERSION
		));
	}
	elseif (!file_exists(PEC_VERSION_FILE)) {
		pec_set_installed_version(PEC_VERSION);
	}

	// LOGGED IN -> redirect
	if ($pec_session->is_logged_in() && !$update_required) {
	    pec_redirect('pec_admin/admin.php');
	}
}

// SPECIFIC AREA?
if (isset($_GET['area'])) {
    if ($_GET['area'] == 'lost-password') {
        $title = $pec_localization->get('LABEL_LOSTPASSWORD_TITLE');
    }
    elseif ($_GET['area'] == 'new-password') {
        $title = $pec_localization->get('LABEL_LOSTPASSWORD_NEWPW_TITLE');
    }
    elseif ($_GET['area'] == 'update') {
        $title = $pec_localization->get('LABEL_UPDATE_TITLE');
    }
}
else {
    $title = $pec_localization->get('LABEL_LOGIN_TITLE');;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

    <head>
        <title>pecio cms &raquo; <?php echo $title; ?></title>
        
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <link type="text/css" rel="stylesheet" href="pec_style/css/login/layout.css" />
        <link type="text/css" rel="stylesheet" href="pec_style/css/login/misc.css" />
        <link type="text/css" rel="stylesheet" href="pec_style/css/misc.css" />
        <link type="text/css" rel="stylesheet" href="pec_style/css/format.css" />
                
        <script type="text/javascript" src="pec_style/js/mootools/mootools-core.js"></script>
        <script type="text/javascript" src="pec_style/js/mootools/mootools-more.js"></script>
        <script type="text/javascript" src="pec_style/js/main-animations.js"></script>
        
        <script type="text/javascript" src="pec_style/js/forms.js"></script>
    </head>

    <body>
        <div id="main_wrapper">
            <div id="main_inner_wrapper">
                <div id="top_wrapper">
                
                    <div style="text-align: center;">
                        <table cellpadding="0" cellspacing="0" style="height: 57px;" width="100%">
                            <tr>
                                <td valign="middle" align="center" class="td_middle">
                                    <img src="pec_style/images/logo.png" alt="pecio cms" style="margin-left: -3px;" />
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                </div>
                <div id="middle_wrapper">
                    <div id="middle_inner_wrapper">                        
                            <h2><?php echo $title; ?></h2><br /><br />
                            
                            <div id="messages">
                                <?php
                                echo $messages;
                                
                                if (isset($_GET['message'])) {
                                    if ($_GET['message'] == 'login_incorrect') { 
                                        PecMessageHandler::raise('login_incorrect', array());
                                    }
                                    elseif ($_GET['message'] == 'logout') {
                                        PecMessageHandler::raise('logout_done', array());
                                    }
                                    elseif ($_GET['message'] == 'wrong_update_pw') {
                                        PecMessageHandler::raise('update_password_wrong', array());
                                    }
                                }
                                ?>
                            </div>
                            
                            <div id="content">
                                <?php 
                                
                                if (isset($_GET['area'])) {
                                    if ($_GET['area'] == 'lost-password') {
                                        require('lost-password.loginarea.php');
                                    }
                                    elseif ($_GET['area'] == 'new-password') {
                                        require('new-password.loginarea.php');
                                    }
                                    elseif ($_GET['area'] == 'update') {
                                        require('update.loginarea.php');
                                    }
                                }
                                else {                                
                                ?>
                                                                
	                                <?php if ($update_required) { ?>
	                                	<div class="options_box_2">
	                                    	<form method="post" action="index.php?area=update">
	                                			<h3><?php $pec_localization->out('LABEL_UPDATE_PASSWORD'); ?>:</h3> 
	                                			<input type="password" name="update_password" value="" style="width: 200px;" />
	                                			<input type="submit" name="update_button" value="<?php $pec_localization->out('BUTTON_UPDATE'); ?>" />
	                                		</form> 
	                                	</div><br />
	                                <?php } ?>
                                	
                                    <form method="post" action="login.php">
                                        <h3><?php $pec_localization->out('LABEL_LOGIN_USERNAME'); ?>:</h3>
                                        <input type="text" name="user_name" value="" style="width: 200px;" />
                                        <br /><br />
                                        
                                        <h3><?php $pec_localization->out('LABEL_LOGIN_PASSWORD'); ?>:</h3>
                                        <input type="password" name="user_password" value="" style="width: 200px;" />
                                        <br /><br />
                                        
                                        <input type="submit" name="login_button" value="<?php $pec_localization->out('BUTTON_LOGIN'); ?>" />
                                    </form>
                                    
                                    <br /><br /><br />
                                    
                                    <a href="index.php?area=lost-password" class="login_link_element"><?php $pec_localization->out('LABEL_LOSTPASSWORD_TITLE'); ?></a>
                                    <a href="../" class="login_link_element"><?php $pec_localization->out('LABEL_LOGIN_BACKTOSITE'); ?></a>
                                <?php } ?>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>
