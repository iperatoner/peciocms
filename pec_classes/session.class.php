<?php

/**
 * pec_classes/session.class.php - Session Class
 * 
 * Defines the Session class which handles the Server Sessions.
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
 * @subpackage	pec_classes
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */

class PecSession {
    
    private $session_id;
    
    function __construct($start=true) {
        session_name(PEC_SESSION_NAME);
        if ($start) {
            $this->session_id = session_start();
        }
        $this->set_default_vars();
    }
    
    private function set_default_vars() {
        $this->set('pec_visitor_ip', $_SERVER['REMOTE_ADDR']);
        $this->set('pec_visitor_referer', $_SERVER['HTTP_REFERER']);
        
        // check if the 'pec_logged_in' variable is already set. if not set it to false
        // we have to do this because it wouldn't be that good if we overwrite the var, if the user is logged in =)
        if ($this->is_set('pec_logged_in') == false) {
            $this->set('pec_logged_in', false);
        }
        
        // same as above with 'pec_user' var
        if ($this->is_set('pec_user') == false) {
            $this->set('pec_user', null);
        }
    }
    
    public function set($var, $data) {
        $_SESSION[$var] = $data;
    }
    
    public function get($var) {
        return $_SESSION[$var];
    }
    
    public function is_set($var) {
        return isset($_SESSION[$var]);
    }
    
    public function get_session_id() {
        return $this->session_id;
    } 
    
    public function set_logged_in($user=false) {
        if ($user) {
            $this->set('pec_logged_in', true);
            $this->set('pec_user', $user);
            
            return true;
        }
        else {
            return false;
        }
    }
    
    public function set_logged_out() {
        $this->set('pec_logged_in', false);
        $this->set('pec_user', null);
    }
    
    public function is_logged_in() {
        return $this->is_set('pec_logged_in')      && $this->is_set('pec_user') && 
               $this->get('pec_logged_in') == true && $this->get('pec_user') != null;
    }
    
    public function destroy() {
        $this->session_id = false;
        session_destroy();
    }
    
}

?>