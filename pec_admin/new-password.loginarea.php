<?php

/**
 * pec_admin/new-password.loginarea.php - Area where new password link redirects to
 * 
 * This file is an area for the login screen on which a user can create a new password.
 * Before the user can do this, the file checks if the data in the query string is correct.
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
$new_password_form = false;

// URL STRUCTURE
if (isset($_GET['uid'])  && isset($_GET['user'])  && isset($_GET['dna'])  && isset($_GET['t']) &&
    !empty($_GET['uid']) && !empty($_GET['user']) && !empty($_GET['dna']) && !empty($_GET['t'])) {
    
    // USER ID COMPARISON
    if ($_GET['uid'] == base64_decode($_GET['user'])) {
        
        // EXISTS?
        if (PecUser::exists('id', $_GET['uid'])) {
            $user = PecUser::load('id', $_GET['uid']);
            
            // DNA CHECK
            if ($user->check_link_dna($_GET['dna'], $_GET['t'])) {
                
                // TIME CHECK
                if (time() - $_GET['t'] <= 172800) {
                    
                    // ACTION: CHANGE
                    if (isset($_GET['action']) && $_GET['action'] == 'create-pw') {
                        
                        // REPETITION CHECK
                        if ($_POST['user_pw'] == $_POST['user_pw_repeat'] && !empty($_POST['user_pw'])) {
                            $user->set_password($_POST['user_pw']);
                            $user->save();
                            $messages .= PecMessageHandler::get('user_new_pw_changed');
                        }
                        
                        // REPETITION WRONG
                        else {
                            $messages .= PecMessageHandler::get('user_new_pw_repeat_incorrect');
                            $new_password_form = true;
                        }
                    }
                    
                    // EVERYTHING ALRIGHT - DISPLAYING FORM
                    else {
                        $new_password_form = true;
                    }
                }
                
                // EXPIRED
                else {
                    $messages .= PecMessageHandler::get('user_pw_link_expired');
                }
                
            }
            
            // INCORRECT DNA
            else {
                $messages .= PecMessageHandler::get('user_pw_link_incorrect_data');
            }
            
        }
        
        // NOT EXISTS
        else {
            $messages .= PecMessageHandler::get('content_not_found_id', array(
                '{%CONTENT_TYPE%}' => 'user',
                '{%ID%}' => htmlspecialchars($_GET['uid'])
            ));
        }
        
    }
    
    // INCORRECT STRUCTURE
    else {
        $messages .= PecMessageHandler::get('user_pw_link_incorrect_data');
    } 

}

?>

<?php echo $messages; ?>

<?php if ($new_password_form === true) { ?>
    <?php $pec_localization->out('LABEL_LOSTPASSWORD_NEWPW_TEXT'); ?><br /><br />
    
    <form method="post" action="index.php?<?php echo $_SERVER['QUERY_STRING']; ?>&amp;action=create-pw">
        <h3><?php $pec_localization->out('LABEL_USERS_PASSWORD'); ?>:</h3>
        <input type="password" name="user_pw" value="" style="width: 200px;" />
        <br /><br />
        
        <h3><?php $pec_localization->out('LABEL_USERS_PASSWORD_REPEAT'); ?>:</h3>
        <input type="password" name="user_pw_repeat" value="" style="width: 200px;" />
        <br /><br />
        
        <input type="submit" value="<?php $pec_localization->out('BUTTON_CHANGE'); ?>" />
    </form>
<?php 
} 
else {
    PecMessageHandler::raise('redirect', array('{%TIME%}' => 4));
    pec_redirect('pec_admin/index.php', 4);    
}
?>
<br /><br />
<a href="index.php?area=lost-password" class="login_link_element"><?php $pec_localization->out('LABEL_LOSTPASSWORD_TITLE'); ?></a>
<a href="index.php" class="login_link_element"><?php $pec_localization->out('LABEL_LOGIN_BACKTOLOGIN'); ?></a>
<a href="../" class="login_link_element"><?php $pec_localization->out('LABEL_LOGIN_BACKTOSITE'); ?></a>