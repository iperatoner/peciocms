<?php

/**
 * pec_includes/functions.inc.php - Basic functions
 * 
 * Defines basic helper functions used in the CMS.
 * 
 * LICENSE: This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU General Public License as published by the 
 * Free Software Foundation, either version 3 of the License, or (at your option) 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License 
 * for more details. You should have received a copy of the 
 * GNU General Public License along with this program. 
 * If not, see <http://www.gnu.org/licenses/>.
 * 
 * @package		peciocms
 * @subpackage	pec_includes
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.2
 * @link		http://pecio-cms.com
 */


/**
 * Defines basic constants that are used by the CMS
 * 
 * @param	integer $relative_directory_level: Levels of directories up from the root directory, e.g. 1
 * @param	string 	$directory: Current relative directory path up from the root directory, e.g. "pec_admin/pec_ajax"
 */
function define_constants($relative_directory_level=0, $directory='') {
    define('PEC_VERSION', '2.0.2');
    define('PEC_SESSION_NAME', 'pec_session');
    
    define('DEFAULT_TEMPLATE_NAME', 'Nova Blue');    
    define('DEFAULT_LOCALE', 'en');
    
    define('MESSAGE_INFO', 1);
    define('MESSAGE_WARNING', 2);
    
    define('TYPE_MYSQL', 'mysql');
    define('TYPE_SQLITE', 'sqlite');
    
    define('NULL_ID', 0);
    
    define('MENUPOINT_TARGET_HOME', 1);
    define('MENUPOINT_TARGET_ARTICLE', 2);
    define('MENUPOINT_TARGET_BLOG', 3);
    define('MENUPOINT_TARGET_URL', 4);
    
    define('QUERY_TARGET_HOME', 'home');
    define('QUERY_TARGET_ARTICLE', 'article');
    define('QUERY_TARGET_BLOG', 'blog');   
    define('QUERY_TARGET_SEARCH', 'search');
    
    define('SITE_VIEW_HOME', 'home');
    define('SITE_VIEW_ARTICLE', 'article');
    define('SITE_VIEW_SEARCH', 'search');
    define('SITE_VIEW_404', '404');
    define('SITE_VIEW_BLOG', 'blog');    
    define('SITE_VIEW_BLOGPOST', 'blogpost');
    define('SITE_VIEW_BLOGCATEGORY', 'blogcategory');
    define('SITE_VIEW_BLOGTAG', 'blogtag');
    define('SITE_VIEW_BLOGARCHIVE', 'blogarchive');
    
    define('TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES', 1);
    define('TEXT_VISIBILITY_ON_ALL_ARTICLES', 2);
    define('TEXT_VISIBILITY_ON_BLOG', 3);
    define('TEXT_VISIBILITY_EVERYWHERE', 4);
    
    define('MULTIPLE_ID_DILIMITER', '[%]');
    
    define('TYPE_FLAT', 'flat');
    define('TYPE_ARRAY', 'array');
    define('TYPE_OBJ_ARRAY', 'obj-array');
    
    define('PERMISSION_NONE', 0);
    define('PERMISSION_READ', 1);
    define('PERMISSION_FULL', 2);
    
    define('URL_TYPE_DEFAULT', 'default');
    define('URL_TYPE_HUMAN', 'human');
    define('URL_TYPE_REWRITE', 'rewrite');
    
    if ($relative_directory_level === 0 && $directory === '') {
        define('RELATIVE_BACK', '');
        define('CURRENT_DIR', '');
    }
    else {
        $relative_directories_back = '';
        for ($i=1; $i<=$relative_directory_level; $i++) {
            $relative_directories_back .= '../';
        }
        define('RELATIVE_BACK', $relative_directories_back);
        define('CURRENT_DIR', $directory);
    }
    
    define('TEMPLATE_PATH', pec_root_path() . 'pec_templates/');
    define('PLUGIN_PATH', pec_root_path() . 'pec_plugins/');
    define('LOCALE_PATH', pec_root_path() . 'pec_locales/');
    
    define('TEMPLATE_PATH_NC', pec_root_path(false) . 'pec_templates/');
    define('PLUGIN_PATH_NC', pec_root_path(false) . 'pec_plugins/');
    define('LOCALE_PATH_NC', pec_root_path(false) . 'pec_locales/');
    
    define('TEMPLATE_META_FILE', 'template_meta.inc.php');
    define('PLUGIN_META_FILE', 'plugin_meta.inc.php');
    
    define('PLUGIN_INSTALL_FILE', 'install_plugin.inc.php');
    define('PLUGIN_UNINSTALL_FILE', 'uninstall_plugin.inc.php');
    define('PLUGIN_INSTALLED_FILE', 'installed.txt');
    define('PLUGIN_UNINSTALLED_FILE', 'uninstalled.txt');
    
    define('INTERN_TPL_PATH', 'pec_includes/templates/');
    define('MESSAGE_INFO_TPL_FILE', 'message.tpl');
    define('MESSAGE_WARNING_TPL_FILE', 'error.tpl');
    define('SIDEBARTEXT_TPL_FILE', 'sidebar-text.tpl');
    define('SIDEBARLINKCAT_TPL_FILE', 'sidebar-linkcategory.tpl');
    define('SIDEBARLINK_TPL_FILE', 'sidebar-link.tpl');
    define('MENUWRAPPER_TPL_FILE', 'menu-wrapper.tpl');
    define('MENUITEM_TPL_FILE', 'menu-item.tpl');
    define('SEARCHWRAPPER_TPL_FILE', 'search-wrapper.tpl');
    define('SEARCHENTRY_TPL_FILE', 'search-entry.tpl');
    define('SEARCHHIGHLIGHT_TPL_FILE', 'search-highlight.tpl');
    define('SEARCHFORM_TPL_FILE', 'search-form.tpl');
          
    define('ADMIN_MAIN_FILE', 'admin.php');
    define('ADMIN_AREA_VAR', 'area');
    
    define('XML_STRINGS_REPLACE_VAR', '{%STRINGS%}');
    define('SEARCH_OUTLINE_LENGTH', 150);
    
    define('FEED_TYPE', 'RSS2.0');
    
    define('MAIN_FEED_PATH', pec_root_path() . 'pec_feeds/blog/');
    define('TAG_FEED_PATH', pec_root_path() . 'pec_feeds/tags/');
    define('CATEGORY_FEED_PATH', pec_root_path() . 'pec_feeds/categories/');
    
    define('MAIN_FEED_PATH_FRONT', pec_root_path(false) . 'pec_feeds/blog/');
    define('TAG_FEED_PATH_FRONT', pec_root_path(false) . 'pec_feeds/tags/');
    define('CATEGORY_FEED_PATH_FRONT', pec_root_path(false) . 'pec_feeds/categories/');
    
    define('MAIN_FEED_FILE', 'feed.xml');
    
    define('PEC_VERSION_FILE', pec_root_path() . 'pec_admin/version.txt');
    define('PEC_UPDATE_FILE', pec_root_path() . 'pec_admin/pec_update/update-cms.inc.php');
    
    define('COUNTER_FILE', pec_root_path() . 'counter.txt');
    define('COUNTER_IP_EXPIRE', 86400);
}

/**
 * Returns the currently installed version (which is written into pec_admin/version.txt)
 * 
 * @return	string
 */
function pec_installed_version() {
	if (file_exists(PEC_VERSION_FILE)) {
		$version = file_get_contents(PEC_VERSION_FILE);
		if (empty($version)) {
			$version = PEC_VERSION;
		}
	}
	else {
		$version = PEC_VERSION;
	}
	
	return $version;
}

/**
 * Sets the currently installed version
 * 
 * @param	string 	$version: The version to set, e.g. "2.0"
 * @return	boolean Wether writing into the version file was successful or not
 */
function pec_set_installed_version($version='') {
	try {
		if (!empty($version)) {
			file_put_contents(PEC_VERSION_FILE, $version);
		}
		else {
			file_put_contents(PEC_VERSION_FILE, PEC_VERSION);
		}
	}
	catch (Exception $e) {
		return false;
	}
	
	return true;
}

/**
 * Returns the root filesystem path of the CMS installation
 * 
 * @param	boolean $canonicalized: Set to true if you want the canonicalized file system path or false if not
 * @return	string The filesystem path
 */
function pec_root_path($canonicalized=true) {
    if ($canonicalized) {
        $current_path = realpath('.');
    }
    else {  
        $current_path = dirname($_SERVER['PHP_SELF']);
    }  
    $current_path = str_replace(CURRENT_DIR, '', $current_path) . '/';
    $current_path = str_replace('//', '/', $current_path);
    return $current_path;
}

/**
 * Returns the root url of the CMS installation
 * 
 * @param	boolean $with_path: Set to true if you also want the path of the installation being appended
 * @return	string The installation url
 */
function pec_root_url($with_path=true) {
    return 'http://' . $_SERVER['HTTP_HOST'] . pec_root_path(false);
}

/**
 * Redirects to a given target/url
 * 
 * @param	string 	$target: Target being redirected to. If not a relative path, $append_url has to be set to false, e.g. "pec_admin/admin.php"
 * @param	integer $timeout: If you want to have a timeout until being redirected, e.g. 3
 * @param	boolean $return: If you only want the HTML-<meta>-Refresh string being returned, then this must be true
 * @param	boolean $append_url: Must be true if $target is a relative path (up from the root)
 * @return	string If param $return is true, the HTML-<meta>-Refresh string
 */
function pec_redirect($target, $timeout=0, $return=false, $append_url=true) {
    if ($append_url) {
        $refresh_string = '<meta http-equiv="refresh" content="' . $timeout . '; ' . pec_root_url() . $target . '" />';        
    }
    else {
        $refresh_string = '<meta http-equiv="refresh" content="' . $timeout . '; ' . $target . '" />';
    }
    
    if ($return) {
        return $refresh_string;
    }
    else {
        if ($timeout == 0 && !headers_sent()) {
            if ($append_url) {
                header('Location: ' . pec_root_url() . $target);
            }
            else {
                header('Location: ' . $target);
            }
        }
        echo $refresh_string;
        die();
    }
}


/**
 * Reads the permission of a given file or directory
 * 
 * @param	string $filename: Filename or directory name to check, e.g. "pec_uploads/"
 * @return	string The permission of the given file/directory, e.g. "644"
 */
function pec_file_permission($filename) {
    return substr(sprintf('%o', fileperms('index.lighttpd.html')), -3);
}

/**
 * Reads the permission of the core files and directories (defined in pec_core.inc.php)
 * 
 * @param	string $filename: Filename or directory name to check, e.g. "pec_uploads/"
 * @return	string The permission of the given file/directory, e.g. "644"
 */
function pec_read_core_permissions() {
    global $pec_permission_array;
    
    $file_permissions = array();

    foreach ($pec_permission_array as $file => $perm) {
        $file_permissions[$file] = pec_file_permission($file);
    }
}

/**
 * Slugifies a string (removing invalid characters and replacing whitespaces with dashes)
 * 
 * @param	string $str: String to be slugified, e.g. "First Post on this blog!"
 * @return	string Slugified string
 */
function slugify($str) {

    $str = strtolower($str);     
    
    $str = preg_replace("/[^a-z0-9\s-]/", "", $str);    
    $str = trim(preg_replace("/\s+/", " ", $str));    
    $str = trim(substr($str, 0, 50));    
    $str = preg_replace("/\s/", "-", $str);
    
    if (empty($str)) {
        return 'n-a';
    }
    
    return $str;
}

/**
 * Converts a flat string of IDs dlimited by the MULTIPLE_ID_DILIMITER to an array of these IDs
 * 
 * @param	string $multiple_id_flat: String with the dilimited IDs, e.g. "3[%]5[%]9[%]17[%]22[%]23"
 * @return	array The IDs, e.g. Array(3, 5, 9, 17, 22, 23)
 */
function flat_to_array($multiple_id_flat) {
    return explode(MULTIPLE_ID_DILIMITER, $multiple_id_flat);
}

/**
 * Converts an array of IDs to a flat string of these IDs dlimited by the MULTIPLE_ID_DILIMITER
 * 
 * @param	array $multiple_id_array: Array holding the IDs, e.g. Array(3, 5, 9, 17, 22, 23)
 * @return	string The IDs dilimited by the MULTIPLE_ID_DILIMITER, e.g. "3[%]5[%]9[%]17[%]22[%]23"
 */
function array_to_flat($multiple_id_array) {
    return implode(MULTIPLE_ID_DILIMITER, $multiple_id_array);
}

/**
 * Returns the available user permissions as an array
 * 
 * @return	array The available user permissions, usually Array(0, 1, 2)
 */
function pec_available_user_permissions() {
    return array(PERMISSION_NONE, PERMISSION_READ, PERMISSION_FULL);
}

/**
 * Gets the content of an intern HTML template (e.g. of the messages)
 * 
 * @param	string $filename: Filename of the template (without the path), e.g. "message-info.tpl"
 * @return	string Content of the intern template file
 */
function get_intern_template($filename) {
    return file_get_contents(RELATIVE_BACK . INTERN_TPL_PATH . $filename);
}

/**
 * Chooses the correct template file depending on the message importance
 * 
 * @param	integer $message_importance: Importance of the message (MESSAGE_INFO|MESSAGE_WARNING), e.g. 1
 * @return	string Filename of the correct template file, e.g. "message-warning.tpl"
 */
function message_tpl_file($message_importance) {
    switch ($message_importance) {
        
        case MESSAGE_INFO: 
            return MESSAGE_INFO_TPL_FILE; break;
            
        case MESSAGE_WARNING: 
            return MESSAGE_WARNING_TPL_FILE; break;
        
    }
}

/**
 * Generates a randome string
 * 
 * @param	integer $len: Number of chars, e.g. 6
 * @return	string Random string
 */
function random_string($len){
    $randstr = '';
    srand((double) microtime() * 1000000);
    for($i=0; $i<$len; $i++){
        $n = rand(48, 120);
        while (($n >= 58 && $n <= 64) || ($n >= 91 && $n <= 96)) {
            $n = rand(48, 120);
        }
        $randstr .= chr($n);
    }
    return $randstr;
}

/**
 * Finds all positions of a substring in the given string
 * 
 * @param	string 	$str: String to search in, e.g. "Apples are not like bananas and bananas are not like apples."
 * @param	string 	$substr: Substring to be searched, e.g. "bananas"
 * @param	boolean $i: Wether to search case sensitive or not, (true|false)
 * @return	array All found positions, e.g. Array(20, 32)
 */
function str_all_pos($str, $substr, $i=false) {
    $pos_array = array();
    if (!$i) {
        $pos = strpos($str, $substr);
        while ($pos !== false) {
            $pos_array[] = $pos;
            $pos = strpos($str, $substr, $pos + 1);
        }
    }
    else {
        $pos = stripos($str, $substr);
        while ($pos !== false) {
            $pos_array[] = $pos;
            $pos = stripos($str, $substr, $pos + 1);
        }
    }
    return $pos_array;
}

/**
 * Greps string between two substrings in the given string
 * 
 * @param	string $start_string: Substring that is before the substring to be grepped. e.g. "{%"
 * @param	string $end_string: Substring that is after the substring to be grepped, e.g. "%}"
 * @param	string $string: String to grep in, e.g. "This is a {%nice%} string with {%another%} substring to grep."
 * @return	array All found strings, e.g. Array("nice", "another")
 */
function grep_data_between($start_string, $end_string, $string) {
    $datas = array();
    $start_string_char_count = strlen($start_string);
    $positions = str_all_pos($string, $start_string);
    foreach ($positions as $pos) {
        $data_start_pos = $pos + $start_string_char_count;
        $data_start = substr($string, $data_start_pos);
        $data_end_pos = strpos($data_start, $end_string);
        $data = substr($data_start, 0, $data_end_pos);
        $datas[] = $data;
    }
    return $datas;
}

/**
 * Checks if a string ends with the given substring
 * 
 * @param	string $str: String to search in, e.g. "Here are some apples."
 * @param	string $str: Substring with which the string may end, e.g. "apples."
 */
function str_ends_with($str, $sub) {
	return substr($str, strlen($str) - strlen($sub)) == $sub;
}

/**
 * Converts HTML-Linebreaks(<br />) to new lines (\n)
 * 
 * @param	string $str: The string in that the line breaks have to be replaced, e.g. "This is<br />no banana."
 * @return	string The new string with replaced HTML-Linebreaks, e.h. "This is\n noe banana."
 */
function br2nl($str) { 
	$str = preg_replace('/<br\\\\s*?\\/?/i', "\\n", $str); 
	return str_replace("<br />", "", $str); 
}

/**
 * Converts ampersands (&) to the HTML-Entity of ampersands (&amp;)
 * 
 * @param	string $str: The string in that the ampersands have to be replaced, e.g. "We are here & there."
 * @return	string The new string with replaced ampersands, e.g. "We are here &amp; there."
 */
function convert_ampersands_to_entities($str) {
	return str_replace('&', '&amp;', $str);
} 

/**
 * Checks if the syntax of an email address is correct
 * 
 * @param	string $email: Email address to be checked, e.g. "fo@bar@bar.cc" OR "foo@bar.com"
 * @return	boolean
 */
function email_syntax_correct($email) {
    return eregi("^([a-z0-9_]|\-|\.)+@(([a-z0-9_]|\-)+\.)+[a-z]{2,4}\$", $email) == 1 ? true : false;
}

/**
 * Checks if the host of an email address exists
 * 
 * @param	string $email: Email address to be checked, e.g. "someone@some-host-that-does-not-exist.net"
 * @return	boolean
 */
function email_host_exists($email) {
    $email = explode('@', $email);    
    $host = $email[1] . '.';    
    return getmxrr($host, $mxhosts) == true;
}

?>
