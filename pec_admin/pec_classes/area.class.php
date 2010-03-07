<?php 

/**
 * pec_admin/pec_classes/area.class.php - Admin Area Class
 * 
 * This file defines the Admin Area Class which handles the data of 
 * an area (integrated and plugins) for the admin frontend
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
 * @subpackage	pec_admin.pec_classes
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.2
 * @link		http://pecio-cms.com
 */

class PecArea {
    
    private $area_name, $area_path, $area_data;
    
    /* $data can be the admin-area name or a PecPlugin object */
    function __construct($data, $is_plugin=false) {
        if ($is_plugin) {
        	if ($data->is_installed() || !$data->installation_required()) {
            	$this->area_name = $data->get_property('area_name');
            	$this->area_path = PLUGIN_PATH . $data->get_directory_name() . '/' . $data->get_property('area_file');
        	}
        	else {
            	$this->area_name = 'plugins';
            	$this->area_path = 'pec_areas/' . $this->area_name . '.area.php';
        	}
        }
        else {
            $this->area_name = self::protect_area_name($data);
            $this->area_path = 'pec_areas/' . $this->area_name . '.area.php';
        }
        
        $this->area_data = array();
        
        if (file_exists($this->area_path)) {
            $check_permission = $this->area_name == 'default' || $this->area_name == 'forbidden' ? false : true;
            $this->extract_area_data($check_permission);
        }
        else {
            $this->insert_default_data();
        }
    }
    
    private static function protect_area_name($area_name) {
        $area_name = str_replace('/', '', $area_name);
        $area_name = str_replace('.', '', $area_name);
        $area_name = str_replace('%', '', $area_name);
        
        return $area_name;
    }
    
    private function extract_area_data($check_permission=true) {        
        // make global vars available in this method
        foreach ($GLOBALS as $key => $value) {
            if (strpos($key, '-') == false) {
                eval('global $' . $key . ';');
            }
        }
        
        require_once($this->area_path);
        
        // only insert the data, if the current user has the permission to access
        if ($pec_session->get('pec_user')->get_permission($area['permission_name']) > PERMISSION_NONE || !$check_permission) {
            $this->area_data = $area;
        }
        else {
            $this->insert_forbidden_data();
        }
    }
    
    private function insert_default_data() {
        $area = array();
        
        $this->area_name = 'default';
        $this->area_path = 'pec_areas/default.area.php';
        
        $this->extract_area_data(false);
    }
    
    private function insert_forbidden_data() {
        $area = array();
                
        $this->area_name = 'forbidden';
        $this->area_path = 'pec_areas/forbidden.area.php';
        
        $this->extract_area_data(false);
    }
    
    public function get($key='title') {
        if (array_key_exists($key, $this->area_data)) {
            return $this->area_data[$key];
        }
        else {
            return false;
        }
    }
    
    public function out($key='title') {
        echo $this->get($key);
    }
    
    public function get_name() {
        return $this->area_name;
    }
    
    public function get_path() {
        return $this->area_path;
    }
    
}

?>