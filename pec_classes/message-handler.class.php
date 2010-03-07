<?php

/**
 * pec_classes/message-handler.class.php - Message Handler Class
 * 
 * Defines the main Message Handler class which manages all the available messages.
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
 * @version		2.0.2
 * @link		http://pecio-cms.com
 */

class PecMessageHandler {
    
    public static function raise($identifier, $replace_strings=array()) {
        echo self::get($identifier, $replace_strings);
    }
    
    public static function get($identifier, $replace_strings=array()) {
        global $pec_messages;
        $message_array = $pec_messages[$identifier];
        
        $message_title = $message_array[0];
        $message_content = $message_array[1];
        $message_importance = $message_array[2];
        
        // replace all replace-strings with the given strings
        foreach ($replace_strings as $key => $data) {
            $message_title = str_replace($key, $data, $message_title);
            $message_content = str_replace($key, $data, $message_content);
        }
    
        // get the template for this message
        $template = get_intern_template(message_tpl_file($message_importance));
        
        $message = str_replace('{%TITLE%}', $message_title, $template);
        $message = str_replace('{%CONTENT%}', $message_content, $message);
        
        return $message;
    }
    
    public static function custom($title, $content, $importance) {    
        // get the template for this message
        $template = get_intern_template(message_tpl_file($importance));
        
        $message = str_replace('{%TITLE%}', $title, $template);
        $message = str_replace('{%CONTENT%}', $content, $message);
        
        return $message;
    }
    
    public static function exists($identifier) {
        global $pec_messages;
        return array_key_exists($identifier, $pec_messages);
    }
    
}

?>
