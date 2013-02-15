<?php

/**
 * pec_admin/login.php - Logs a user in
 * 
 * This file logs a user in. It is calles by the login form's action.
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

if (isset($_POST['login_button'])) {
    

    /* core includes, creating core objects */
    require_once('../pec_includes/functions.inc.php');
    require_once('common.inc.php');
    require_once('../pec_core.inc.php');
    /* core include end */
    
    
    if (isset($_POST['user_name']) && isset($_POST['user_password'])) {
        if (PecUser::exists('name', $_POST['user_name'])) {
            $user = PecUser::load('name', $_POST['user_name']);
            
            if ($user->password_match($_POST['user_password'])) {
                $pec_session->set_logged_in($user);
                pec_redirect('pec_admin/admin.php');
            }
            else {
                pec_redirect('pec_admin/index.php?message=login_incorrect');
            }
        }
        else {
            pec_redirect('pec_admin/index.php?message=login_incorrect');
        }
    }
    else {
        pec_redirect('pec_admin/index.php');
    }
}
else {
    pec_redirect('pec_admin/index.php');
}

?>