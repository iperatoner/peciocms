<?php

/**
 * pec_classes/user.class.php - User Class
 * 
 * Defines the User class which manages the CMS Users.
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

class PecUser {
    private $user_id, $user_name, $user_slug, $user_forename, $user_surname, $user_email, $user_password,
            $user_permission_articles, $user_permission_menupoints, $user_permission_texts, 
            $user_permission_links, $user_permission_blogposts, $user_permission_blogcomments,
            $user_permission_users, $user_permission_plugins, $user_permission_templates,
            $user_permission_settings;
            
    static $by_array = array(
               'id' => 'user_id',
               'name' => 'user_name',
               'slug' => 'user_slug',
               'forename' => 'user_forename',
               'surname' => 'user_surname',
               'email' => 'user_email',
               'password' => 'user_password',
               'permission_articles' => 'user_permission_articles',
               'permission_menupoints' => 'user_permission_menupoints',
               'permission_texts' => 'user_permission_texts',
               'permission_links' => 'user_permission_links',
               'permission_blogposts' => 'user_permission_blogposts',
               'permission_blogcomments' => 'user_permission_blogcomments',
               'permission_users' => 'user_permission_users',
               'permission_plugins' => 'user_permission_plugins',
               'permission_templates' => 'user_permission_templates',
               'permission_settings' => 'user_permission_settings'
           );
            
    static $permissions = array(
               'permission_articles' => '',
               'permission_menupoints' => '',
               'permission_texts' => '',
               'permission_links' => '',
               'permission_blogposts' => '',
               'permission_blogcomments' => '',
               'permission_users' => '',
               'permission_plugins' => '',
               'permission_templates' => '',
               'permission_settings' => ''
           );
    
    function __construct($id=0, $name, $forename, $surname, $email, $password, $permission_articles,
                         $permission_menupoints, $permission_texts, $permission_links, $permission_blogposts,
                         $permission_blogcomments, $permission_users, $permission_plugins, 
                         $permission_templates, $permission_settings, $slug=false, $plain=true) {
        global $pec_database;
        $this->database =& $pec_database;
        
        /* escaping input data */
        $escaped_data = $this->database->db_string_protection(
            false, 
            array(
                'id' => $id, 'name' => $name, 'forename' => $forename, 'surname' => $surname,
                'email' => $email, 'permission_articles' => $permission_articles, 
                'permission_menupoints' => $permission_menupoints, 'permission_texts' => $permission_texts, 
                'permission_links' => $permission_links, 'permission_blogposts' => $permission_blogposts,
                'permission_blogcomments' => $permission_blogcomments, 'permission_users' => $permission_users,
                'permission_plugins' => $permission_plugins, 'permission_templates' => $permission_templates,
                'permission_settings' => $permission_settings
            )
        );
        $this->user_id = $escaped_data['id'];
        $this->user_name = $escaped_data['name'];
        
        /* if this article hasn't got a slug yet */
        if (!$slug) {
            $this->user_slug = self::slugify($name);
        }
        else {
            $this->user_slug = $slug;
        }
        
        $this->user_forename = $escaped_data['forename'];
        $this->user_surname = $escaped_data['surname'];
        $this->user_email = $escaped_data['email'];
        
        if ($plain) {
            $this->user_password = sha1($password);
        }
        else {
            $this->user_password = $password;
        }
        
        $this->user_permission_articles = intval($escaped_data['permission_articles']);
        $this->user_permission_menupoints = intval($escaped_data['permission_menupoints']);
        $this->user_permission_texts = intval($escaped_data['permission_texts']);
        $this->user_permission_links = intval($escaped_data['permission_links']);
        $this->user_permission_blogposts = intval($escaped_data['permission_blogposts']);
        $this->user_permission_blogcomments = intval($escaped_data['permission_blogcomments']);
        $this->user_permission_users = intval($escaped_data['permission_users']);
        $this->user_permission_plugins = intval($escaped_data['permission_plugins']);
        $this->user_permission_templates = intval($escaped_data['permission_templates']);
        $this->user_permission_settings = intval($escaped_data['permission_settings']);
        
    }
    
    public function get_id() {
        return $this->user_id;
    }
    
    public function get_name() {
        return $this->user_name;
    }
    
    public function get_slug() {
        return $this->user_slug;
    }
    
    public function get_forename() {
        return $this->user_forename;
    }
    
    public function get_surname() {
        return $this->user_surname;
    }
    
    public function get_email() {
        return $this->user_email;
    }
    
    public function get_password() {
        return $this->user_password;
    }
    
    public function get_permission($type=false) {
        if ($type && array_key_exists($type, self::$permissions)) {
            # TODO: Create constants for these permission strings
            switch ($type) {
                case 'permission_articles':
                    return $this->user_permission_articles;     break;
                case 'permission_menupoints':
                    return $this->user_permission_menupoints;   break;
                case 'permission_texts':
                    return $this->user_permission_texts;        break;
                case 'permission_links':
                    return $this->user_permission_links;        break;
                case 'permission_blogposts':
                    return $this->user_permission_blogposts;    break;
                case 'permission_blogcomments':
                    return $this->user_permission_blogcomments; break;
                case 'permission_users':
                    return $this->user_permission_users;        break;
                case 'permission_plugins':
                    return $this->user_permission_plugins;      break;
                case 'permission_templates':
                    return $this->user_permission_templates;    break;
                case 'permission_settings':
                    return $this->user_permission_settings;     break;
            }
        }
    }
    
    public function is_superadmin() {
        return $this->user_permission_articles && $this->user_permission_menupoints &&
               $this->user_permission_texts && $this->user_permission_links &&
               $this->user_permission_blogposts && $this->user_permission_blogcomments &&
               $this->user_permission_users && $this->user_permission_plugins &&
               $this->user_permission_templates && $this->user_permission_settings;
    }
    
    
    public function set_name($name) {
        if ($name != $this->user_name) {
            $this->user_name = $this->database->db_string_protection($name);
            $this->user_slug = $this->database->db_string_protection(self::slugify($name));
        }
    }
    
    public function set_forename($forename) {
        $this->user_forename = $this->database->db_string_protection($forename);
    }
    
    public function set_surname($surname) {
        $this->user_surname = $this->database->db_string_protection($surname);
    }
    
    public function set_email($email) {
        $this->user_email = $this->database->db_string_protection($email);
    }
    
    public function set_password($password, $plain=true) {
        if ($plain) {
            $this->user_password = sha1($password);
        }
        else {
            $this->user_password = $password;
        }
    }
    
    public function set_permission($type=false, $value=PERMISSION_READ) {
        if ($type && array_key_exists($type, self::$permissions)) {
            $value = intval($value);
            switch ($type) {
                case 'permission_articles':
                    $this->user_permission_articles = $value;     break;
                case 'permission_menupoints':
                    $this->user_permission_menupoints = $value;   break;
                case 'permission_texts':
                    $this->user_permission_texts = $value;        break;
                case 'permission_links':
                    $this->user_permission_links = $value;        break;
                case 'permission_blogposts':
                    $this->user_permission_blogposts = $value;    break;
                case 'permission_blogcomments':
                    $this->user_permission_blogcomments = $value; break;
                case 'permission_users':
                    $this->user_permission_users = $value;        break;
                case 'permission_plugins':
                    $this->user_permission_plugins = $value;      break;
                case 'permission_templates':
                    $this->user_permission_templates = $value;    break;
                case 'permission_settings':
                    $this->user_permission_settings = $value;     break;
            }
        }
    }
    
    public function password_match($plain_password_input, $hash=false) {
        if (!$hash) {
           return sha1($plain_password_input) == $this->user_password ? true : false;
        }
        else {
           return $plain_password_input == $this->user_password ? true : false;
        }
    }
    
    public function send_password_link() {
        global $pec_settings, $pec_localization;        
        
        $id = $this->user_id;
        $id_enc = base64_encode($id);
        $name = $this->user_name;
        $pw = $this->user_password;
        $unique = $pec_settings->get_nospam_key(3);
        $time = time();        
        
        $dna = md5($id . $name . $time . $pw . $unique);
        $params = 'uid=' . $id . '&user=' . $id_enc . '&dna=' . $dna . '&t=' . $time;
        $url = pec_root_url() . 'pec_admin/index.php?area=new-password&' . $params;
        
        $mail_subject = $pec_localization->get('LABEL_LOSTPASSWORD_EMAIL_TITLE');
        $mail_content = $pec_localization->get('LABEL_LOSTPASSWORD_EMAIL_TEXT');
        $mail_content = str_replace('{%USERNAME%}', $name, $mail_content);
        $mail_content = str_replace('{%ROOT_URL%}', pec_root_url(), $mail_content);
        $mail_content = str_replace('{%URL%}', $url, $mail_content);
            
        mail($this->user_email, $mail_subject, $mail_content);
    }
    
    public function check_link_dna($dna, $time) {
        global $pec_settings;
        
        $dna_wouldbe = md5($this->user_id . $this->user_name . $time . $this->user_password . $pec_settings->get_nospam_key(3)); 
               
        return $dna == $dna_wouldbe;
    }
    
    public function save() {
        $new = false;
        if (self::exists('id', $this->user_id)) {
            $query = "UPDATE " . DB_PREFIX . "users SET
                        user_name='"                    . $this->user_name . "',
                        user_slug='"                    . $this->user_slug . "',
                        user_forename='"                . $this->user_forename . "',
                        user_surname='"                 . $this->user_surname . "',
                        user_email='"                   . $this->user_email . "',
                        user_password='"                . $this->user_password . "',
                        user_permission_articles='"     . $this->user_permission_articles . "',
                        user_permission_menupoints='"   . $this->user_permission_menupoints . "',
                        user_permission_texts='"        . $this->user_permission_texts . "',
                        user_permission_links='"        . $this->user_permission_links . "',
                        user_permission_blogposts='"    . $this->user_permission_blogposts . "',
                        user_permission_blogcomments='" . $this->user_permission_blogcomments . "',
                        user_permission_users='"        . $this->user_permission_users . "',
                        user_permission_plugins='"      . $this->user_permission_plugins . "',
                        user_permission_templates='"    . $this->user_permission_templates . "',
                        user_permission_settings='"     . $this->user_permission_settings . "'
                      WHERE user_id='"    . $this->user_id . "'";
        }
        else {
            $new = true;
            $query = "INSERT INTO " . DB_PREFIX . "users (
                        user_name,
                        user_slug,
                        user_forename,
                        user_surname,
                        user_email,
                        user_password,
                        user_permission_articles,
                        user_permission_menupoints,
                        user_permission_texts,
                        user_permission_links,
                        user_permission_blogposts,
                        user_permission_blogcomments,
                        user_permission_users,
                        user_permission_plugins,
                        user_permission_templates,
                        user_permission_settings
                      ) VALUES
                      (
                        '" . $this->user_name . "',
                        '" . $this->user_slug . "',
                        '" . $this->user_forename . "',
                        '" . $this->user_surname . "',
                        '" . $this->user_email . "',
                        '" . $this->user_password . "',
                        '" . $this->user_permission_articles . "',
                        '" . $this->user_permission_menupoints . "',
                        '" . $this->user_permission_texts . "',
                        '" . $this->user_permission_links . "',
                        '" . $this->user_permission_blogposts . "',
                        '" . $this->user_permission_blogcomments . "',
                        '" . $this->user_permission_users . "',
                        '" . $this->user_permission_plugins . "',
                        '" . $this->user_permission_templates . "',
                        '" . $this->user_permission_settings . "'
                      )";
        }
        
        $this->database->db_connect();
        $this->database->db_query($query);
        if ($new) {
            $this->user_id = $this->database->db_last_insert_id();
        }
        $this->database->db_close_handle();
    }
    
    public function remove() {
        $query = "DELETE FROM " . DB_PREFIX . "users WHERE user_id='" . $this->user_id . "'";
        
        $this->database->db_connect();
        $this->database->db_query($query);
        $this->database->db_close_handle();
        
        unset($this);        
    }
    
        
    public static function load($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        /* loading a specific user, or a specific range of users */ 
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "users WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            
            $return_data = null;
            
            if ($pec_database->db_num_rows($resource) > 1) {
                $return_data = array();
                
                while ($u = $pec_database->db_fetch_array($resource)) {
                    $return_data[] = new PecUser($u['user_id'], $u['user_name'], $u['user_forename'], 
                                                 $u['user_surname'], $u['user_email'], $u['user_password'], 
                                                 $u['user_permission_articles'], $u['user_permission_menupoints'], 
                                                 $u['user_permission_texts'], $u['user_permission_links'], 
                                                 $u['user_permission_blogposts'], $u['user_permission_blogcomments'], 
                                                 $u['user_permission_users'], $u['user_permission_plugins'],
                                                 $u['user_permission_templates'], $u['user_permission_settings'],
                                                 $u['user_slug'], false);
                }
            }
            elseif ($pec_database->db_num_rows($resource) == 1) {
                $u = $pec_database->db_fetch_array($resource);
                $return_data = new PecUser($u['user_id'], $u['user_name'], $u['user_forename'], 
                                           $u['user_surname'], $u['user_email'], $u['user_password'], 
                                           $u['user_permission_articles'], $u['user_permission_menupoints'], 
                                           $u['user_permission_texts'], $u['user_permission_links'], 
                                           $u['user_permission_blogposts'], $u['user_permission_blogcomments'], 
                                           $u['user_permission_users'], $u['user_permission_plugins'],
                                           $u['user_permission_templates'], $u['user_permission_settings'],
                                           $u['user_slug'], false);
            }
            $pec_database->db_close_handle();
            
            return $return_data;            
        }
        
        /* loading all users */
        else {
            $query = "SELECT * FROM " . DB_PREFIX . "users " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
            
            $users = array();
            
            while ($u = $pec_database->db_fetch_array($resource)) {
                $users[] = new PecUser($u['user_id'], $u['user_name'], $u['user_forename'], 
                                       $u['user_surname'], $u['user_email'], $u['user_password'], 
                                       $u['user_permission_articles'], $u['user_permission_menupoints'], 
                                       $u['user_permission_texts'], $u['user_permission_links'], 
                                       $u['user_permission_blogposts'], $u['user_permission_blogcomments'], 
                                       $u['user_permission_users'], $u['user_permission_plugins'],
                                       $u['user_permission_templates'], $u['user_permission_settings'],
                                       $u['user_slug'], false);
            }
            $pec_database->db_close_handle();
            
            return $users;
        }
    }
        
    public static function exists($by='id', $data=false, $query_add='') {
        global $pec_database;
        
        if ($by && $data && array_key_exists($by, self::$by_array)) {
            $data = $pec_database->db_string_protection($data);
            $query = "SELECT * FROM " . DB_PREFIX . "users WHERE " . self::$by_array[$by] . "='" . $data . "' " . $query_add;
            
            $pec_database->db_connect();
            $resource = $pec_database->db_query($query);
                
            /* if there are more than 0 rows, the user exists, else not */
            $return_data = $pec_database->db_num_rows($resource) > 0 ? true : false;
            $pec_database->db_close_handle();
            
            return $return_data;            
        }        
        else {
            return false;
        }
    }
    
    public static function slugify($name) {        
        $slug = slugify($name);
        
        $counter = 1;
        while (self::exists('slug', $slug)) {
            $slug = slugify($name) . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}

?>