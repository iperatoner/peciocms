<?php

/**
 * pec_classes/database.class.php - Database Class
 * 
 * Defines the main Database class which handles the chosen database and the queries.
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

class PecDatabase {

    private $db_host, $db_user, $db_pw, $db_name, $db_handle, $db_type, $cached_queries;

    function __construct($host, $user, $pw, $name, $type=TYPE_MYSQL, $connect=false) {
        $this->db_type = $type;
        
        if ($type === TYPE_MYSQL) {
            $this->db_host = $host;
            $this->db_user = $user;
            $this->db_pw   = $pw;
            $this->db_name = $name;
        }
        elseif ($type === TYPE_SQLITE) {
            $this->db_host = '';
            $this->db_user = '';
            $this->db_pw   = '';
            
            $this->db_name = pec_root_path() . $name;
        }

        if ($connect) {
            $this->db_connect();
        }
        else {
            $this->db_handle = false;
        }
        
        $this->cached_queries = array();
    }
    
    public function db_check_connection() {
        if ($this->db_type === TYPE_MYSQL) {
            try {
            	mysql_connect($this->db_host, $this->db_user, $this->db_pw);
            }
            catch (Exception $e) {
            	return false;
            }            
            
            if (mysql_select_db($this->db_name)) {
            	return true;
            }
            else {
            	return false;
            }
            
            return true;
        }
        elseif ($this->db_type === TYPE_SQLITE) {
            try {
            	sqlite_open($this->db_name);
            }
            catch (Exception $e) {
            	return false;
            }
            return true;       
        }
    }

    public function db_connect() {
        if ($this->db_type === TYPE_MYSQL) {
            $this->db_handle = mysql_connect($this->db_host, $this->db_user, $this->db_pw);
        }
        elseif ($this->db_type === TYPE_SQLITE) {
            $this->db_handle = sqlite_open($this->db_name);            
        }
        
        if (!is_resource($this->db_handle)) {
            die($this->db_log_error());
        }
        else {
            if ($this->db_type === TYPE_MYSQL) {
                mysql_select_db($this->db_name);
            }
        }
    }

    public function db_escape_string($str) {
        $this->db_connect();
        
        if ($this->db_type === TYPE_MYSQL) {
            $esc = mysql_real_escape_string(stripslashes($str), $this->db_handle);
        }
        elseif ($this->db_type === TYPE_SQLITE) {
            $esc = sqlite_escape_string(stripslashes($str));
        }
        
        $this->db_close_handle();
        return $esc;
    }

    public function db_string_protection($str=false, $strings=array()) {
        $return_data = '';
        
        $this->db_connect();
        
        
        if ($str !== false || $strings === false) {
            if ($this->db_type === TYPE_MYSQL) {
                $esc = htmlspecialchars(mysql_real_escape_string(stripslashes($str), $this->db_handle));
            }
            elseif ($this->db_type === TYPE_SQLITE) {
                $esc = htmlspecialchars(sqlite_escape_string(stripslashes($str)));            
            }
            $return_data =& $esc;
        }
        else {
            $escaped_strings = array();
            
            foreach ($strings as $key => $str) {
                if ($this->db_type === TYPE_MYSQL) {
                    $escaped_strings[$key] = htmlspecialchars(mysql_real_escape_string(stripslashes($str), $this->db_handle));
                }
                elseif ($this->db_type === TYPE_SQLITE) {
                    $escaped_strings[$key] = htmlspecialchars(sqlite_escape_string(stripslashes($str)));            
                }
            }
            $return_data =& $escaped_strings;
        }
        
        $this->db_close_handle();
        
        return $return_data;
    }

        public function db_string_protection_decode($str) {
            $dec = htmlspecialchars_decode(stripslashes($str));
            
            return $dec;
        }
    
    public function db_query($query) {
        if ($this->db_type === TYPE_MYSQL) {
        	/*if (isset($this->cached_queries[$query])) {
        		$resource = $this->cached_queries[$query];
        	}
        	else {*/
        		$resource = mysql_query($query, $this->db_handle);
        		/*$this->cached_queries[$query] = $resource;
        	}*/
        }
        elseif ($this->db_type === TYPE_SQLITE) {
            // replace INT with INTEGER if using sqlite
            if (strpos($query, 'INT')) {
                $query = str_replace(' INT ', ' INTEGER ', $query);
            }
            
            $resource = sqlite_query($query, $this->db_handle);
        }
        
        if (!$resource) {
            $e = $this->db_log_error($query);
            $this->db_close_handle();
            die($e);
        }
        else {
            return $resource;
        }
    }
    
    public function db_last_insert_id() {
        if ($this->db_type === TYPE_MYSQL) {
            return mysql_insert_id($this->db_handle);
        }
        elseif ($this->db_type === TYPE_SQLITE) {
            return sqlite_last_insert_rowid($this->db_handle);
        }
    }

    public function db_num_rows($resource) {
        if ($this->db_type === TYPE_MYSQL) {
            return mysql_num_rows($resource);
        }
        elseif ($this->db_type === TYPE_SQLITE) {
            return sqlite_num_rows($resource);
        }
    }

    public function db_fetch_array($resource) {
        if ($this->db_type === TYPE_MYSQL) {
            return mysql_fetch_assoc($resource);
        }
        elseif ($this->db_type === TYPE_SQLITE) {
            return sqlite_fetch_array($resource);
        }
    }

    public function db_error() {
        if ($this->db_type === TYPE_MYSQL) {
        	if ($this->db_handle) {
            	return mysql_error($this->db_handle);
        	}
        	else {
        		return mysql_error();
        	}
        }
        elseif ($this->db_type === TYPE_SQLITE) {
        	if ($this->db_handle) {
            	return sqlite_error_string(sqlite_last_error($this->db_handle));
        	}
        	else {
        		return '';
        	}
        }
    }

    public function db_error_number() {
        if ($this->db_type === TYPE_MYSQL) {
        	if ($this->db_handle) {
            	return mysql_errno($this->db_handle);
        	}
        	else {
        		return mysql_errno();
        	}
        }
        elseif ($this->db_type === TYPE_SQLITE) {
        	if ($this->db_handle) {
            	return sqlite_last_error($this->db_handle);
        	}
        	else {
        		return '';
        	}
        }
    }
    
    public function db_log_error($query=false) {
        $e = '<b>Database (' . $this->db_type . ') error</b>';
        $e .= '<ul>';
        $e .= '<li>Error message: ' . $this->db_error() . '</li>';
        $e .= '<li>Error number:  ' . $this->db_error_number() . '</li>';
        if ($query) {
            $e .= '<li>SQL query: ' . $query . '</li>';
        }
        $e .= '</ul>';
        return $e;
    }

    public function db_close_handle() {
        if ($this->db_type === TYPE_MYSQL) {
            return mysql_close($this->db_handle);
        }
        elseif ($this->db_type === TYPE_SQLITE) {
            return sqlite_close($this->db_handle);
        }
    }


    public function get_host() {
        return $this->db_host;
    }

    public function get_user() {
        return $this->db_user;
    }

    public function get_name() {
        return $this->db_name;
    }

    public function get_handle() {
        return $this->db_handle;
    }
}

?>
