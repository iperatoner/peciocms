<?php

/**
 * pec_admin/pec_ajax/blog-posts.ajax.php - Saving blogposts via ajax
 * 
 * This file creates/saves a blogpost. It is called via ajax.
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
 * @version		2.0.2
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
    
    // CREATE
    if ($_POST['action'] == 'create') {        
        if (isset($_POST['post_id']) && isset($_POST['post_title']) && 
            isset($_POST['post_content']) && isset($_POST['post_content_cut']) && 
            isset($_POST['post_status']) && isset($_POST['post_tags'])) {
                 
            $status = $_POST['post_status'] == 'true' ? true : false; 
             
            // converting the array of selected categories into the flat version for database
            if (isset($_POST['post_categories'])) {
                $in_categories = array_to_flat($_POST['post_categories']);
            }
            else {
                $in_categories = array();
            }
            
            // the constructor only accepts the flat version of tag ids (i may change this in future)
            $tag_ids = array_to_flat(PecBlogTag::get_ids_of_tagnames($_POST['post_tags'], true));      
                  
            $timestamp = time();
            $y = date('Y', $timestamp);
            $m = date('m', $timestamp);
            $d = date('d', $timestamp);
            
            $author_id = $pec_session->get('pec_user')->get_id();
            
            $post = new PecBlogPost(NULL_ID, $timestamp, $y, $m, $d, $author_id, 
                                    $_POST['post_title'], $_POST['post_content_cut'], $_POST['post_content'], $tag_ids, $in_categories, $status);
            $post->save();
            
            $output = PecMessageHandler::get('content_created', array(
                '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POST'),
                '{%NAME%}' => $post->get_title()
            ));
            
            $output .= '
                <script type="text/javascript">                
                    document.getElementById("post_id").value = "' . $post->get_id() . '";
                    document.getElementById("posts_edit_form").action = 
                        document.getElementById("posts_edit_form").action.replace("create", "save") + 
                        "&id=' . $post->get_id() . '";
                </script>
            ';
            
            
        }
    }
    
    // SAVE
    elseif ($_POST['action'] == 'save') {        
        if (isset($_POST['post_id']) && isset($_POST['post_title']) && 
            isset($_POST['post_content']) && isset($_POST['post_content_cut']) && 
            isset($_POST['post_status']) && isset($_POST['post_tags'])) {
                
            if (PecBlogPost::exists('id', $_POST['post_id'])) {  
                $status = $_POST['post_status'] == 'true' ? true : false; 
                            
                   $tag_ids = PecBlogTag::get_ids_of_tagnames($_POST['post_tags'], true);
                             
                $post = PecBlogPost::load('id', $_POST['post_id']);
                
                $post->set_title($_POST['post_title']);
                $post->set_content_cut($_POST['post_content_cut']);
                $post->set_content($_POST['post_content']);
                $post->set_tags($tag_ids, TYPE_ARRAY);
                $post->set_categories($_POST['post_categories'], TYPE_ARRAY);
                $post->set_status($status);
                
                $post->save();
                
                $output = PecMessageHandler::get('content_cached', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POST'),
                    '{%NAME%}' => $post->get_title()
                ));
            }
            else {
                $output = PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_POST'),
                    '{%ID%}' => $_POST['post_id']
                ));
            }
            
        }
    }
}

echo $output;

?>