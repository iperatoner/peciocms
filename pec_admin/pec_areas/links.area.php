<?php 

/**
 * pec_admin/pec_areas/links.area.php - Managing sidebar links
 * 
 * Admin area to manage sidebar links.
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
 * @subpackage	pec_admin.pec_areas
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.2
 * @link		http://pecio-cms.com
 */

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=links');

/* main area data */
$area = array();
$area["title"] = $pec_localization->get('LABEL_GENERAL_LINKS');
$area["permission_name"] = 'permission_links';
$area["head_data"] = '';
$area["messages"] = '';
$area["content"] = 'No view was executed.';


/* a function that does actions depending on what data is in the query string */

function do_actions() {
	global $pec_localization;
	
    $messages = '';
    
    // costum message
    if (isset($_GET['message']) && !empty($_GET['message']) && 
        isset($_GET['message_data']) && !empty($_GET['message_data']) && 
        isset($_GET['message_content_type']) && !empty($_GET['message_content_type']) && 
        PecMessageHandler::exists($_GET['message'])) {  
                    
        $messages .= PecMessageHandler::get($_GET['message'], array(
            '{%CONTENT_TYPE%}' => $_GET['message_content_type'],
            '{%NAME%}' => $_GET['message_data'],
            '{%ID%}' => $_GET['message_data']
        ));
        
    }    
        
    if (isset($_GET['action'])) {
        // CREATE category
        if ($_GET['action'] == 'create_category' && isset($_POST['cat_title']) && 
            isset($_POST['cat_visibility'])) {
            
            // if the link category shall be visible on specific articles, 
            // we're converting the array of selected articles into the flat version for database
            if ($_POST['cat_visibility'] == TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES) {
                if (isset($_POST['cat_onarticles'])) {
                    $on_articles = array_to_flat($_POST['cat_onarticles']);
                }
                else {
                    $on_articles = array();
                } 
            }
                        
            $link_category = new PecSidebarLinkCat(NULL_ID, $_POST['cat_title'], $_POST['cat_visibility'], $on_articles);
            $link_category->save();
            
            $messages .= PecMessageHandler::get('content_created', array(
                '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINKCATEGORY'),
                '{%NAME%}' => $link_category->get_title()
            ));
        }
        
        // CREATE link
        if ($_GET['action'] == 'create_link' && isset($_POST['link_name']) && 
            isset($_POST['link_url']) && isset($_POST['link_cat'])) {
                
            $link = new PecSidebarLink(NULL_ID, $_POST['link_cat'], $_POST['link_name'], $_POST['link_url']);
            $link->save();
            
            $messages .= PecMessageHandler::get('content_created', array(
                '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINK'),
                '{%NAME%}' => $link->get_name()
            ));
        }
        
        // SAVE category
        elseif ($_GET['action'] == 'save_category' && isset($_POST['cat_title']) &&
                isset($_POST['cat_visibility'])) {
                    
            if (isset($_GET['id']) && PecSidebarLinkCat::exists('id', $_GET['id'])) {                
                $link_category = PecSidebarLinkCat::load('id', $_GET['id']);
                $link_category->set_title($_POST['cat_title']);
                $link_category->set_visibility($_POST['cat_visibility']);
                if (isset($_POST['cat_onarticles'])) {
                    $link_category->set_onarticles($_POST['cat_onarticles'], TYPE_ARRAY);
                }
                $link_category->save();
                
                $messages .= PecMessageHandler::get('content_edited', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINKCATEGORY'),
                    '{%NAME%}' => $link_category->get_title()
                ));
            }
            else {
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINKCATEGORY'),
                    '{%ID%}' => ''
                ));
            }
        }
        
        // SAVE link
        elseif ($_GET['action'] == 'save_link' && isset($_POST['link_name']) &&
                isset($_POST['link_url']) && isset($_POST['link_cat'])) {
                    
            if (isset($_GET['id']) && PecSidebarLink::exists('id', $_GET['id'])) {                
                $link = PecSidebarLink::load('id', $_GET['id']);
                $link->set_name($_POST['link_name']);
                $link->set_url($_POST['link_url']);
                $link->set_cat_id($_POST['link_cat']);
                $link->save();
                
                $messages .= PecMessageHandler::get('content_edited', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINK'),
                    '{%NAME%}' => $link->get_name()
                ));
            }
            else {
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINK'),
                    '{%ID%}' => ''
                ));
            }
        }
        
        // REMOVE category
        elseif ($_GET['action'] == 'remove_category' && isset($_GET['id'])) {
            if (PecSidebarLinkCat::exists('id', $_GET['id'])) {
                $link_category = PecSidebarLinkCat::load('id', $_GET['id']);
                $link_category_title = $link_category->get_title();
                
                if (isset($_GET['special']) && $_GET['special'] == 'recursive') {
                    $links = PecSidebarLink::load('cat', $link_category);
                    foreach ($links as $l) {
                        $l->remove();
                    }
                }
                
                $link_category->remove();
                
                $messages .= PecMessageHandler::get('content_removed', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINKCATEGORY'),
                    '{%NAME%}' => $link_category_title
                ));
            }
            else {                
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINKCATEGORY'),
                    '{%ID%}' => $_GET['id']
                ));
            }
        }
        
        // REMOVE link
        elseif ($_GET['action'] == 'remove_link' && isset($_GET['id'])) {
            if (PecSidebarLink::exists('id', $_GET['id'])) {
                $link = PecSidebarLink::load('id', $_GET['id']);
                $link_name = $link->get_name();               
                $link->remove();
                
                $messages .= PecMessageHandler::get('content_removed', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINK'),
                    '{%NAME%}' => $link_name
                ));
            }
            else {                
                $messages .= PecMessageHandler::get('content_not_found_id', array(
                    '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINK'),
                    '{%ID%}' => $_GET['id']
                ));
            }
        }
        
        // DEFAULT ACTIONS (REMOVE MULTIPLE, SORT)
        elseif ($_GET['action'] == 'default_view_actions') {
            // var to check if any link or category has been sorted
            $sorted_some_links = false;
            $sorted_some_categories = false;
        
            // REMOVE MULTIPLE
            if (isset($_POST['remove_links']) && !isset($_POST['sort'])) {
                if (!empty($_POST['remove_box'])) {
                    
                    foreach ($_POST['remove_box'] as $link_id) {
                        $link = PecSidebarLink::load('id', $link_id);
                        $link->remove();
                    }
                              
                    $messages .= PecMessageHandler::get('content_removed_multiple', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINKS'),
                        '{%NAME%}' => ''
                    ));
                }
                else {
                    $messages .= PecMessageHandler::get('content_not_selected', array(
                        '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINKS'),
                        '{%NAME%}' => ''
                    ));
                }
            }
            
            // SORT
            else {
                
                // sort categories
                if (!empty($_POST['sort_fields_category'])) {
                    foreach ($_POST['sort_fields_category'] as $key => $sort) {
                        // loading the extra data (id and origin sort value)
                        $extra_data = explode('-', $_POST['sort_extra_data_category'][$key]);                
                        $cat_id = $extra_data[0];
                        $cat_orig_sort= $extra_data[1];
                        
                        if ($sort != $cat_orig_sort) {
                            $sorted_some_categories = true;
                            
                            $cat = PecSidebarLinkCat::load('id', $cat_id);
                            $cat->set_sort($sort);
                            $cat->save();
                        }
                    }
                    
                    if ($sorted_some_categories) {            
                        $messages .= PecMessageHandler::get('content_sorted', array(
                            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINKCATEGORIES')
                        ));
                    }
                }
                
                // sort links
                if (!empty($_POST['sort_fields_link'])) {
                    foreach ($_POST['sort_fields_link'] as $key => $sort) {
                        // loading the extra data (id and origin sort value)
                        $extra_data = explode('-', $_POST['sort_extra_data_link'][$key]);                
                        $link_id = $extra_data[0];
                        $link_orig_sort= $extra_data[1];
                        
                        if ($sort != $link_orig_sort) {
                            $sorted_some_links = true;
                            
                            $link = PecSidebarLink::load('id', $link_id);
                            $link->set_sort($sort);
                            $link->save();
                        }
                    }
                    
                    if ($sorted_some_links) {            
                        $messages .= PecMessageHandler::get('content_sorted', array(
                            '{%CONTENT_TYPE%}' => $pec_localization->get('LABEL_GENERAL_LINKS')
                        ));
                    }
                }
                
            }
        }
        
    }
    
    return $messages;
}


/* creating functions for all the different views that will be available for this area */

function view_edit_category() {
	global $pec_localization;
	
    $area_data = array();    
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_LINKCATEGORIES');
    
    if (isset($_GET['id'])) {
        if (PecSidebarLinkCat::exists('id', $_GET['id'])) {
            $link_category = PecSidebarLinkCat::load('id', $_GET['id']);
            $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_LINKS_EDIT_CATEGORY') . ' &raquo; ' . $link_category->get_title();
            
            $action = 'save_category';
            $id_query_var = '&amp;id=' . $_GET['id'];
        }
        else {
            pec_redirect('pec_admin/' . AREA . '&message=content_not_found_id&message_data=' . $_GET['id'] . '&message_content_type=' . $pec_localization->get('LABEL_GENERAL_LINKCATEGORY'));
        }
    }
    else {
        // create an empty link category
        $link_category = new PecSidebarLinkCat(NULL_ID, '', 0, '');
        $area_data['title'] .= ' &raquo; ' . $pec_localization->get('LABEL_LINKS_CREATE_CATEGORY');
        
        $action = 'create_category';
        $id_query_var = '';
    }
    
    // set the checked state for the visibility radio buttons
    $visibility_selected = array();
    $visibility_selected[TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES] = $link_category->get_visibility() == TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES ? 'checked="checked"' : '';
    $visibility_selected[TEXT_VISIBILITY_ON_ALL_ARTICLES]      = $link_category->get_visibility() == TEXT_VISIBILITY_ON_ALL_ARTICLES      ? 'checked="checked"' : '';
    $visibility_selected[TEXT_VISIBILITY_ON_BLOG]              = $link_category->get_visibility() == TEXT_VISIBILITY_ON_BLOG              ? 'checked="checked"' : '';
    $visibility_selected[TEXT_VISIBILITY_EVERYWHERE]           = $link_category->get_visibility() == TEXT_VISIBILITY_EVERYWHERE           ? 'checked="checked"' : '';
    
    // create the article options for the select box
    $article_checkboxes = '';
    $available_articles = PecArticle::load();
    foreach ($available_articles as $a) {
        if ($link_category->is_on_article($a)) {
            $checked = 'checked="checked"';
        }
        else {
            $checked = '';
        }
        
        $article_checkboxes .= '
            <div class="checkbox_data_row" id="article_row_' . $a->get_id() . '">
                <input type="checkbox" name="cat_onarticles[]" id="article_' . $a->get_id() . '" value="' . $a->get_id() . '" ' . $checked . ' /> 
                    <label for="article_' . $a->get_id() . '">
                        ' . $a->get_title() . '
                    </label>
                <br />                
            </div>
        ';
    }
    
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&ampview=default&amp;action=' . $action . $id_query_var . '" id="categories_edit_form" />
            <h3>' . $pec_localization->get('LABEL_GENERAL_TITLE') . ':</h3>
            <input type="text" size="75" name="cat_title" value="' . $link_category->get_title() . '" />
            <br /><br />
            
            <div class="options_box_1 float_left">
                <h3>' . $pec_localization->get('LABEL_LINKS_VISIBILITY') . ':</h3>
                <table cellspacing="0" cellpadding="0" width="300px">
                    <tr>
                        <td>
                            <input type="radio" name="cat_visibility" id="visibility_everywhere" value="' . TEXT_VISIBILITY_EVERYWHERE . '" ' . $visibility_selected[TEXT_VISIBILITY_EVERYWHERE] . ' />
                            <label for="visibility_everywhere">' . $pec_localization->get('LABEL_LINKS_EVERYWHERE') . '</label><br />
                        </td>
                        <td></td>
                    </tr>
                    
                    <tr>
                        <td>
                            <input type="radio" name="cat_visibility" id="visibility_all_articles" value="' . TEXT_VISIBILITY_ON_ALL_ARTICLES . '" ' . $visibility_selected[TEXT_VISIBILITY_ON_ALL_ARTICLES] . ' />
                            <label for="visibility_all_articles">' . $pec_localization->get('LABEL_LINKS_ALLARTICLES') . '</label><br />
                        </td>
                        <td></td>
                    </tr>
                    
                    <tr>
                        <td>
                            <input type="radio" name="cat_visibility" id="visibility_blog" value="' . TEXT_VISIBILITY_ON_BLOG . '" ' . $visibility_selected[TEXT_VISIBILITY_ON_BLOG] . ' />
                            <label for="visibility_blog">' . $pec_localization->get('LABEL_LINKS_BLOG') . '</label><br />
                        </td>
                        <td></td>
                    </tr>
                    
                    <tr>
                        <td valign="top">
                            <input type="radio" name="cat_visibility" id="visibility_specific" value="' . TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES . '" ' . $visibility_selected[TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES] . ' />
                            <label for="visibility_specific">' . $pec_localization->get('LABEL_LINKS_SPECIFICARTICLES') . ':</label>
                        </td>
                        <td>
                            <label for="visibility_specific">
                                <div class="checkbox_data_selector" id="article_selector" style="padding: 5px !important;">
                                    ' . $article_checkboxes . '
                                </div>
                            </label>
                        </td>
                    </tr>
                        
                    
                </table>
            </div>
            <div style="clear: left;"></div>
            
            <br /><br />
            
            <input type="submit" value="' . $pec_localization->get('BUTTON_SAVE') . '" />
            <a href="' . AREA . '"><input type="button" onclick="location.href=\'' . AREA . '\'" value="' . $pec_localization->get('BUTTON_CANCEL') . '" /></a>
        </form>            
    ';
    
    return $area_data;
}

function view_edit_link() {
	global $pec_localization;
	
    $area_data = array();    
    
    if (isset($_GET['id'])) {
        if (PecSidebarLink::exists('id', $_GET['id'])) {
            $link = PecSidebarLink::load('id', $_GET['id']);
            $area_data['title'] = $pec_localization->get('LABEL_GENERAL_LINKS') . ' &raquo; ' . $pec_localization->get('LABEL_LINKS_EDIT') . ' &raquo; ' . $link->get_name();
            
            $action = 'save_link';
            $id_query_var = '&amp;id=' . $_GET['id'];
        }
        else {
            pec_redirect('pec_admin/' . AREA . '&message=content_not_found_id&message_data=' . $_GET['id'] . '&message_content_type=' . $pec_localization->get('LABEL_GENERAL_LINK'));
        }
    }
    else {
        // create an empty link
        $link = new PecSidebarLink(NULL_ID, 0, '', '');
        $area_data['title'] = $pec_localization->get('LABEL_GENERAL_LINKS') . ' &raquo; ' . $pec_localization->get('LABEL_LINKS_CREATE');
        
        $action = 'create_link';
        $id_query_var = '';
    }
    
    // create the link category options for the select box
    $category_select_options = '';
    $available_categories = PecSidebarLinkCat::load();
    foreach ($available_categories as $lc) {
        if ($link->belongs_to_category($lc)) {
            $selected = 'selected="selected"';
        }
        else {
            $selected = '';
        }
        
        $category_select_options .= '<option value="' . $lc->get_id() . '" ' . $selected . '>' . $lc->get_title() . '</option>';
    }
    
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&ampview=default&amp;action=' . $action . $id_query_var . '" id="links_edit_form" />
            <h3>' . $pec_localization->get('LABEL_GENERAL_NAME') . ':</h3>
            <input type="text" size="75" name="link_name" value="' . $link->get_name() . '" />
            <br /><br />
            
            <h3>' . $pec_localization->get('LABEL_LINKS_URL') . ':</h3>
            <input type="text" size="75" name="link_url" value="' . $link->get_url() . '" />
            <br /><br /><br />
            
            <div class="options_box_1 float_left">
                <h3>' . $pec_localization->get('LABEL_LINKS_CHOOSECAT') . ':</h3>
                <select name="link_cat" style="width: 100%;">' . $category_select_options . '</select>
            </div>
            <div style="clear: left;"></div>
            
            <br /><br />
            
            <input type="submit" value="' . $pec_localization->get('BUTTON_SAVE') . '" />
            <a href="' . AREA . '"><input type="button" onclick="location.href=\'' . AREA . '\'" value="' . $pec_localization->get('BUTTON_CANCEL') . '" /></a>
        </form>            
    ';
    
    return $area_data;
}

function view_default() {
	global $pec_localization;
	
    $area_data = array();
    $area_data['title'] = $pec_localization->get('LABEL_GENERAL_LINKS');
    
    $link_categories = PecSidebarLinkCat::load(0, false, 'ORDER BY cat_sort');
    
    $area_data['content'] = '
        <form method="post" action="' . AREA . '&amp;view=default&amp;action=default_view_actions" id="links_main_form"/>
            <input type="button" value="' . $pec_localization->get('BUTTON_NEW_LINKCATEGORY') . '" onclick="location.href=\'' . AREA . '&amp;view=edit_category\'"/> 
            <input type="button" value="' . $pec_localization->get('BUTTON_NEW_LINK') . '" onclick="location.href=\'' . AREA . '&amp;view=edit_link\'"/> 
            
            <input type="submit" name="sort" value="" style="display: none;"/>
            <input type="submit" name="remove_links" value="' . $pec_localization->get('BUTTON_REMOVE') . '" onclick="return confirm(\'' . $pec_localization->get('LABEL_LINKS_REALLYREMOVE_SELECTED') . '\');" />
            <input type="submit" name="sort_all" value="' . $pec_localization->get('BUTTON_SORT') . '" /><br /><br />
    ';
    
    foreach ($link_categories as $lc) {
        switch ($lc->get_visibility()) {
            case TEXT_VISIBILITY_ON_SPECIFIC_ARTICLES: $visibility_string = $pec_localization->get('LABEL_LINKS_SPECIFICARTICLES'); break;
            case TEXT_VISIBILITY_ON_ALL_ARTICLES: $visibility_string = $pec_localization->get('LABEL_LINKS_ALLARTICLES'); break;
            case TEXT_VISIBILITY_ON_BLOG: $visibility_string = $pec_localization->get('LABEL_LINKS_BLOG'); break;
            case TEXT_VISIBILITY_EVERYWHERE: $visibility_string = $pec_localization->get('LABEL_LINKS_EVERYWHERE'); break;
            default: $visibility_string = '-'; break;
        }
        
        $links = PecSidebarLink::load('cat', $lc, 'ORDER BY link_sort');
        
        $area_data['content'] .= '
            <div class="link_category_wrapper">
                <div class="link_category_head">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td><h3>' . $lc->get_title() . '</h3> (' . $pec_localization->get('LABEL_LINKS_VISIBILITY') . ': ' . $visibility_string . ')</td>
                            <td align="right">
                               <input type="button" value="' . $pec_localization->get('BUTTON_EDIT') . '" onclick="location.href=\'' . AREA . '&amp;view=edit_category&amp;id=' . $lc->get_id() . '\'" />
                               <input type="button" value="' . $pec_localization->get('BUTTON_REMOVE') . '" onclick="ask(\'' . $pec_localization->get('LABEL_LINKS_REALLYREMOVE_CATEGORY') . '\', \'' . AREA . '&amp;view=default&amp;action=remove_category&amp;id=' . $lc->get_id() . '\');" />
                               
                               <input type="text" size="2" name="sort_fields_category[]" value="' . $lc->get_sort() . '" class="sort_input" />                                   
                               <input type="hidden" name="sort_extra_data_category[]" value="' . $lc->get_id() . '-' . $lc->get_sort() . '" />
                            </td>
                        </tr>
                    </table>
                </div>
                
                <table class="data_table">
                    <tr class="head">
                        <td class="check"><input type="checkbox" onclick="checkbox_mark_all(\'remove_box_' . $lc->get_id() . '\', \'links_main_form\', this);" /></td>
                        <td class="long">' . $pec_localization->get('LABEL_GENERAL_NAME') . '</td>
                        <td class="medium">' . $pec_localization->get('LABEL_LINKS_URL') . '</td>
                        <td class="thin center">' . $pec_localization->get('LABEL_GENERAL_SORT') . '</td>
                    </tr>
        ';
        
        
        
        foreach ($links as $l) {
            $area_data['content'] .= '
                <tr class="data" title="#' . $l->get_id() . '">
                    <td class="check"><input type="checkbox" class="remove_box_' . $lc->get_id() . '" name="remove_box[]" value="' . $l->get_id() . '" /></td>                            
                    <td class="long">
                        <a href="' . AREA . '&amp;view=edit_link&amp;id=' . $l->get_id() . '"><span class="main_text">' . $l->get_name() . '</span></a>
                        <div class="row_actions">
                            <a href="' . AREA . '&amp;view=edit_link&amp;id=' . $l->get_id() . '">' . $pec_localization->get('ACTION_EDIT') . '</a> - 
                            <a href="javascript:ask(\'' . $pec_localization->get('LABEL_LINKS_REALLYREMOVE') . '\', \'' . AREA . '&amp;view=default&amp;action=remove_link&amp;id=' . $l->get_id() . '\');">
                                ' . $pec_localization->get('ACTION_REMOVE') . '
                            </a>
                        </div>
                    </td>                            
                    <td class="medium">' . $l->get_url() . '</td>
                    <td class="thin middle center">
                        <input type="text" size="2" name="sort_fields_link[]" value="' . $l->get_sort() . '" class="sort_input" />                                   
                        <input type="hidden" name="sort_extra_data_link[]" value="' . $l->get_id() . '-' . $l->get_sort() . '" />
                    </td>
                </tr>
            ';
        }
        
        $area_data['content'] .='
                </table>
            </div>
        ';
        
    }
    
    $area_data['content'] .= '
        </form>
    ';
    
    return $area_data;
}


/* doing all the actions and then display the view given in the query string */

if ($pec_session->get('pec_user')->get_permission($area['permission_name']) > PERMISSION_READ) {
    $area['messages'] = do_actions();
}

switch ($_GET['view']) {
    case 'edit_link': 
        if ($pec_session->get('pec_user')->get_permission($area['permission_name']) > PERMISSION_READ) {
            $area_data = view_edit_link(); 
            $area['title'] = $area_data['title'];
            $area['content'] = $area_data['content'];
            break;
        }
        
    case 'edit_category': 
        $area_data = view_edit_category(); 
        $area['title'] = $area_data['title'];
        $area['content'] = $area_data['content'];
        break;
        
    case 'default':
        $area_data = view_default(); 
        $area['title'] = $area_data['title'];
        $area['content'] = $area_data['content'];
        break;
        
    default:
        $area_data = view_default(); 
        $area['title'] = $area_data['title'];
        $area['content'] = $area_data['content'];
        break;
}

// append a "(Read-only)" if the user is only allowed to view the area
if ($pec_session->get('pec_user')->get_permission($area['permission_name']) < PERMISSION_FULL) {
    $area['title'] .= ' (' . $pec_localization->get('LABEL_GENERAL_READONLY') . ')';
}

?>
