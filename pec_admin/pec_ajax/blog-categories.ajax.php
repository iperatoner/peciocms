<?php

/**
 * pec_admin/pec_ajax/blog-categories.ajax.php - Managing Blog Categories via ajax
 * 
 * This file creates/saves/removes blog categories. It is called via ajax.
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
 * @subpackage	pec_admin.pec_ajax
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */

/* core includes, creating core objects */
require_once('../../pec_includes/functions.inc.php');
require_once('common.inc.php');
require_once('../../pec_core.inc.php');
/* core include end */

if (!$pec_session->is_logged_in()) {
    die();
}

$area = array();
$area['permission_name'] = 'permission_blogposts';

$output = '';

if ($pec_session->get('pec_user')->get_permission($area['permission_name']) > PERMISSION_READ) {
    if ($_POST['action'] == 'create') {
        if (isset($_POST['category_name']) && !empty($_POST['category_name'])) {
            $cat = new PecBlogCategory(NULL_ID, $_POST['category_name']);
            $cat->save();
            
            $output = '
                <div class="checkbox_data_row" id="category_row_' . $cat->get_id() . '">
                    <input type="checkbox" name="post_categories[]" id="cat_' . $cat->get_id() . '" value="' . $cat->get_id() . '" /> 
                        <label for="cat_' . $cat->get_id() . '">
                            ' . $cat->get_name() . '
                        </label> &nbsp;
                        <span class="checkbox_data_row_actions">
                            <a href="javascript:edit_blog_category(' . $cat->get_id() . ', \'' . $cat->get_name() . '\');">Edit</a> 
                            | <a href="javascript:remove_blog_category(' . $cat->get_id() . ');">&#x2716;</a>
                        </span>
                    <br />
                    
                </div>
            '; 
        }
    }
    elseif ($_POST['action'] == 'edit') {
        if (isset($_POST['id']) && PecBlogCategory::exists('id', $_POST['id']) && 
            isset($_POST['category_name']) && !empty($_POST['category_name'])) {
            $cat = PecBlogCategory::load('id', $_POST['id']);
            $cat->set_name($_POST['category_name']);
            $cat->save();
            
            $output = '
                <input type="checkbox" name="post_categories[]" id="cat_' . $cat->get_id() . '" value="' . $cat->get_id() . '" /> 
                    <label for="cat_' . $cat->get_id() . '">
                        ' . $cat->get_name() . '
                    </label> &nbsp;
                    <span class="checkbox_data_row_actions">
                        <a href="javascript:edit_blog_category(' . $cat->get_id() . ', \'' . $cat->get_name() . '\');">Edit</a> 
                        | <a href="javascript:remove_blog_category(' . $cat->get_id() . ');">&#x2716;</a>
                    </span>
                <br />
            '; 
        }
    }
    elseif ($_POST['action'] == 'remove') {
        if (isset($_POST['id']) && PecBlogCategory::exists('id', $_POST['id'])) {
            $cat = PecBlogCategory::load('id', $_POST['id']);
            $cat->remove();
            
            $output = ''; 
        }
    }
}

echo $output;

?>