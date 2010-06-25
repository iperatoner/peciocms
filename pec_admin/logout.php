<?php

/**
 * pec_admin/index.php - Logs a user out
 * 
 * This file logs the currently logged in user out.
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

if ($pec_session->is_logged_in()) {
    
    $pec_session->set_logged_out();
    $pec_session->destroy();
    
    pec_redirect('pec_admin/index.php?message=logout');
}
else {
    pec_redirect('pec_admin/index.php');
}

?>