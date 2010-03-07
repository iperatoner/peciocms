<?php

/**
 * pec_classes/locale.class.php - Locale Class
 * 
 * Defines the main Locale class which manages the Locales.
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

class PecLocale {
    
    private $xml=false, $language;

    function __construct($language) {
        $this->language = str_replace('.', '', $language);
        $this->language = str_replace('/', '', $this->language);     
        
        // load all xml localestring files and append their content to one another
        $inner_xml_data = '';        
        foreach (scandir(LOCALE_PATH . $language . '/translations/') as $filename) {
            // language file must not be a directory
        	if (!is_dir(LOCALE_PATH . $language . '/translations/' . $filename) && str_ends_with($filename, '.xml')) {
	        	$inner_xml_data .= file_get_contents(LOCALE_PATH . $language . '/translations/' . $filename);
        	}
        }

        // load plugin xml localestring files and append their content to everything else
        $plugins = PecPlugin::load('locale_enabled', true, true);
        foreach ($plugins as $p) {
        	$plugin_lang_dir = $p->get_directory_path() . $p->get_property('locale_directory') . '/' . $language;
        	
        	// check if the locale directory exists in the plugin lang directory
        	// if not, set to default locale directory
        	if (!file_exists($plugin_lang_dir)) {
        		$plugin_lang_dir = $p->get_directory_path() . $p->get_property('locale_directory') . '/' . DEFAULT_LOCALE;
        	}
        	
        	// check if the (perhaps changed in the previous "if") locale directory exists
        	if (file_exists($plugin_lang_dir)) {
	            foreach (scandir($plugin_lang_dir) as $filename) {
	                // language file must not be a directory
	                if (!is_dir($plugin_lang_dir . '/' . $filename) && 
	                    str_ends_with($filename, '.xml')) {
	                    $inner_xml_data .= file_get_contents($plugin_lang_dir . '/' . $filename);
	                }
	            }
        	}
        }
        
        // load layout of the xml locale file and replace the string var with the loaded locale strings
        $this->xml_data = file_get_contents(LOCALE_PATH . $language . '/layout.xml');
        $this->xml_data = str_replace(XML_STRINGS_REPLACE_VAR, $inner_xml_data, $this->xml_data);
        
        // create the SimpleXML Element using the loaded data
        $this->xml = new SimpleXMLElement($this->xml_data);

        $this->cached_strings = array();
    }
    
    public function out($string_id) {
        echo $this->get($string_id);
    }
    
    public function get($string_id) {
    	if (isset($this->cached_strings[$string_id])) {
    		return $this->cached_strings[$string_id];
    	}
        elseif ($this->xml != false) {
            $xml_path = '/language[@id="' . strtoupper($this->language) . '"]/localestring[@id="' . $string_id . '"]/t';
            $result = $this->xml->xpath($xml_path);
            $this->cached_strings[$string_id] = $result[0];
            return $result[0];
        } 
        else {
          return 'Locale file is not loaded.';
        }
    }
    
    public static function scan() {
        $directories = scandir(LOCALE_PATH);
        
        $available_locales = array();
        
        foreach ($directories as $dir) {
            if (is_dir(LOCALE_PATH . $dir) && $dir != '.' && $dir != '..') {
                $available_locales[] = $dir;
            }
        }
        
        return $available_locales;
    }
    
    public static function exists($locale) {
    	$available_locales = self::scan();
    	return in_array($locale, $available_locales);
    }
    
}

?>
