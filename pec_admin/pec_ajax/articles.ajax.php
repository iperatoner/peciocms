<?php

/**
 * pec_admin/pec_ajax/articles.ajax.php - Saving article via ajax
 * 
 * This file creates/saves an article. It is called via ajax.
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
$area['permission_name'] = 'permission_articles';

$output = '';

if ($pec_session->get('pec_user')->get_permission($area['permission_name']) > PERMISSION_READ) {
    
    // CREATE
    if ($_POST['action'] == 'create') {        
        if (isset($_POST['article_id']) && isset($_POST['article_title']) && 
            isset($_POST['article_content']) && isset($_POST['article_onstart'])) {
            
            $onstart = $_POST['article_onstart'] == 'true' ? true : false;
            
            $article = new PecArticle(NULL_ID, $_POST['article_title'], $_POST['article_content'], $onstart);
            $article->save();
            
            $output = PecMessageHandler::get('content_created', array(
                '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_ARTICLE'),
                '{%NAME%}' => $article->get_title()
            ));
            
            $output .= '
                <script type="text/javascript">                
                    document.getElementById("article_id").value = "' . $article->get_id() . '";
                    document.getElementById("articles_edit_form").action = 
                        document.getElementById("articles_edit_form").action.replace("create", "save") + 
                        "&id=' . $article->get_id() . '";
                </script>
            ';
            
            
        }
    }
    
    // SAVE
    elseif ($_POST['action'] == 'save') {        
        if (isset($_POST['article_id']) && isset($_POST['article_title']) && 
            isset($_POST['article_content']) && isset($_POST['article_onstart'])) {
                
            if (PecArticle::exists('id', $_POST['article_id'])) {
                $article = PecArticle::load('id', $_POST['article_id']);
                $article->set_title($_POST['article_title']);
                $article->set_content($_POST['article_content']);
                if ($_POST['article_onstart'] == 'true') {
                    $article->set_onstart(true);
                }
                else {
                    $article->set_onstart(false);
                }
                $article->save();
                
                $output = PecMessageHandler::get('content_cached', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_ARTICLE'),
                    '{%NAME%}' => $article->get_title()
                ));
            }
            else {
                $output = PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_ARTICLE'),
                    '{%ID%}' => $_POST['article_id']
                ));
            }
            
        }
    }
}

echo $output;

?>