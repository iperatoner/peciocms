<?php

/**
 * pec_admin/new-password.loginarea.php - Area where CMS update is done
 * 
 * This file is an area for the login screen which executes the update file.
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
 * @version		2.0.1
 * @link		http://pecio-cms.com
 */

if (isset($_POST['update_password']) && isset($_POST['update_button'])) {
	
	// PASSWORD CHECK
	if ($_POST['update_password'] == CMS_UPDATE_PASSWORD) {
		
		// VERSION CHECK
		if (pec_installed_version() < PEC_VERSION) {
			require(PEC_UPDATE_FILE);
			
			// do the UPDATE
			if (do_update(pec_installed_version())) {
				PecMessageHandler::raise('cms_update_successful', array(
					'{%VERSION_BEFORE%}' => pec_installed_version(),
					'{%VERSION_AFTER%}' => PEC_VERSION
				));
				
				pec_set_installed_version(PEC_VERSION);
			}
			else {
				PecMessageHandler::raise('cms_update_failed', array(
					'{%VERSION_BEFORE%}' => pec_installed_version(),
					'{%VERSION_AFTER%}' => PEC_VERSION
				));
			}
		}
		else {
			pec_redirect('pec_admin/index.php');
		}
		
	}
	else {
		pec_redirect('pec_admin/index.php?message=wrong_update_pw');
	}
	
}
else {
	pec_redirect('pec_admin/index.php');
}

?>

<br /><br />
<a href="index.php" class="login_link_element"><?php $pec_localization->out('LABEL_LOGIN_BACKTOLOGIN'); ?></a>
<a href="../" class="login_link_element"><?php $pec_localization->out('LABEL_LOGIN_BACKTOSITE'); ?></a>