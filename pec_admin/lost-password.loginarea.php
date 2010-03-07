<?php

/**
 * pec_admin/lost-password.loginarea.php - Area where new password link can be requested
 * 
 * This file is an area for the login screen on which a user can enter its email address 
 * to get a link for creating a new password.
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
 * @version		2.0.2
 * @link		http://pecio-cms.com
 */

$messages = '';

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'send_link' && isset($_POST['user_email']) && !empty($_POST['user_email'])) {
        if (PecUser::exists('email', $_POST['user_email'])) {
            $user = PecUser::load('email', $_POST['user_email']);
            $user->send_password_link();
            
            $messages .= PecMessageHandler::get('user_pw_link_sent', array(
                '{%EMAIL%}' => $_POST['user_email']
            ));
        }
        else {
            $messages .= PecMessageHandler::get('user_email_not_found', array(
                '{%EMAIL%}' => $_POST['user_email']
            ));
        }
    }
}


?>

<script type="text/javascript">
    document.getElementById('messages').innerHTML = document.getElementById('messages').innerHTML + '<?php echo $messages; ?>';
</script>

<?php $pec_localization->out('LABEL_LOSTPASSWORD_TEXT'); ?><br /><br />

<form method="post" action="index.php?area=lost-password&amp;action=send_link">
    <h3><?php $pec_localization->out('LABEL_LOSTPASSWORD_EMAIL'); ?>:</h3>
    <input type="text" name="user_email" value="" style="width: 200px;" />
    <br /><br />
    
    <input type="submit" value="<?php $pec_localization->out('BUTTON_SEND'); ?>" />
</form>

<br /><br />
<a href="index.php" class="login_link_element"><?php $pec_localization->out('LABEL_LOGIN_BACKTOLOGIN'); ?></a>
<a href="../" class="login_link_element"><?php $pec_localization->out('LABEL_LOGIN_BACKTOSITE'); ?></a>