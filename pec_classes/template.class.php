<?php

/**
 * pec_classes/template.class.php - Template Class
 * 
 * Defines the Template class which manages all available templates.
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

class PecTemplate {
    
    private $template_directory_name, $template_directory_path, $template_directory_path_c, $template_properties;
    
    static $by_properties = array(
        "id",
        "title", 
        "description", 
        "author", 
        "author_email", 
        "year", 
        "license"
    );
    
    function __construct($directory_name) {
        $this->template_directory_name = $directory_name;
        $this->template_directory_path = TEMPLATE_PATH_NC . $directory_name . '/';
        $this->template_directory_path_c = TEMPLATE_PATH . $directory_name . '/';
        
        $this->template_properties = $this->load_template_properties();        
    }
    
    private function load_template_properties() {
        require($this->template_directory_path_c . TEMPLATE_META_FILE);
        return $template_meta;
    }
    
    public function get_directory_path($canonicalized=true) {
        return $canonicalized ? $this->template_directory_path_c : $this->template_directory_path;
    }
    
    public function get_directory_name() {
        return $this->template_directory_name;
    }
    
    public function get_property($by='id') {
        if ($by && in_array($by, self::$by_properties)) {
            return $this->template_properties[$by];
        }
    }
    
    public static function load($by='id', $data='') {
        if ($by && $data && in_array($by, self::$by_properties)) {
            $templates = self::load();
            $tpl = null;
            
            // check which template shall be loaded
            foreach ($templates as $t) {
                if ($t->get_property($by) == $data) {
                    $tpl = $t;
                    break;
                }
            }
            
            return $tpl;
        }
        else {
            $filenames = scandir(TEMPLATE_PATH);
            
            // check which of the files in the template directory are directories and which not
            $template_directories = array();
            foreach ($filenames as $file) {
                if (is_dir(TEMPLATE_PATH . $file) && $file != '.' && $file != '..') {
                    $template_directories[] = $file;
                }
            }
            
            // create the template objects and add them to an array
            $templates = array();
            foreach ($template_directories as $dir) {
                $templates[] = new PecTemplate($dir);
            }
            
            return $templates;
        }
    }
    
    public static function exists($by='id', $data='') {        
        if ($by && $data && in_array($by, self::$by_properties)) {
            $templates = self::load();
            
            $exists = false;
            foreach ($templates as $t) {
                if ($t->get_property($by) == $data) {
                    $exists = true;
                    break;
                }
            }
            
            return $exists;
        }
        else {
            return false;
        }
    }
    
}

?>
