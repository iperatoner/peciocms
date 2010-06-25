<?php

/**
 * pec_classes/search.class.php - Search Class
 * 
 * Defines the Search class which handles a Search.
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

class PecSearch {
    
    private $search_term, $results_articles, $results_blogposts, $search_result, $article_object, 
            $wrapper_template, $entry_template, $highlight_template, $highlighted_search_term, $pec_localization;
    
    function __construct($search_term) {
    	global $pec_localization;
        $this->pec_localization = $pec_localization;
    	
        $this->search_term = $search_term;
        
        $this->results_articles = '';
        $this->results_blogposts = '';
        
        $this->search_result = '';
        $this->article_object = new PecArticle(NULL_ID, $this->pec_localization->get('LABEL_SEARCH_RESULT_TITLE'), '', 0, '-');
        
        $this->wrapper_template = get_intern_template(SEARCHWRAPPER_TPL_FILE);
        $this->entry_template = get_intern_template(SEARCHENTRY_TPL_FILE);
        $this->highlight_template = get_intern_template(SEARCHHIGHLIGHT_TPL_FILE);
        
        $this->highlighted_search_term = str_replace('{%STRING%}', $this->search_term, $this->highlight_template);        
        
        $this->prepare_wrapper();
    }
    
    private function prepare_wrapper() {
        $this->search_result = str_replace('{%SEARCH_INFO%}', str_replace('{%TERM%}', '<em>' . $this->search_term . '</em>', $this->pec_localization->get('LABEL_SEARCH_RESULT_TEXT')), $this->wrapper_template);
        $this->search_result = str_replace('{%TITLE_ARTICLES%}', $this->pec_localization->get('LABEL_SEARCH_IN_ARTICLES_TITLE'), $this->search_result);
        $this->search_result = str_replace('{%TITLE_BLOGPOSTS%}', $this->pec_localization->get('LABEL_SEARCH_IN_POSTS_TITLE'), $this->search_result);
    }
    
    private function find_in_articles() {
        $all_articles = PecArticle::load();
        
        $found_something = false;
        
        foreach ($all_articles as $a) {
            if (stripos($a->get_title(), $this->search_term) || is_integer(stripos($a->get_title(), $this->search_term)) ||
                stripos($a->get_content(), $this->search_term) || is_integer(stripos($a->get_content(), $this->search_term))) {
                $found_something = true;
                
                $entry_title = str_ireplace($this->search_term, $this->highlighted_search_term, strip_tags($a->get_title()));
                $entry_outline = str_ireplace($this->search_term, $this->highlighted_search_term, substr(strip_tags($a->get_content()), 0, SEARCH_OUTLINE_LENGTH));
                
                $entry = str_replace('{%URL%}', create_article_url($a), $this->entry_template);
                $entry = str_replace('{%TITLE%}', $entry_title, $entry);
                $entry = str_replace('{%OUTLINE%}', $entry_outline, $entry);
                
                $this->results_articles .= $entry;
            }
        }
        
        if ($found_something) {
            $this->search_result = str_replace('{%RESULT_ARTICLES%}', $this->results_articles, $this->search_result);
        }
        else { 
            $this->search_result = str_replace('{%RESULT_ARTICLES%}', $this->pec_localization->get('LABEL_SEARCH_IN_ARTICLES_NOTHINGFOUND'), $this->search_result);
        }
    }
    
    private function find_in_blogposts() {
        $all_blogposts = PecBlogPost::load('post_status', 1);
        
        $found_something = false;
        
        foreach ($all_blogposts as $bp) {
            if (stripos($bp->get_title(), $this->search_term) || is_integer(stripos($bp->get_title(), $this->search_term)) ||
                stripos($bp->get_content(), $this->search_term) || is_integer(stripos($bp->get_content(), $this->search_term)) ||
                stripos($bp->get_content_cut(), $this->search_term) || is_integer(stripos($bp->get_content_cut(), $this->search_term))) {
                $found_something = true;
                
                $entry_title = str_ireplace($this->search_term, $this->highlighted_search_term, strip_tags($bp->get_title()));
                $entry_outline = str_ireplace($this->search_term, $this->highlighted_search_term, substr(strip_tags($bp->get_content()), 0, SEARCH_OUTLINE_LENGTH));
                
                $entry = str_replace('{%URL%}', create_blogpost_url($bp), $this->entry_template);
                $entry = str_replace('{%TITLE%}', $entry_title, $entry);
                $entry = str_replace('{%OUTLINE%}', $entry_outline, $entry);
                
                $this->results_blogposts .= $entry;
            }
        }
        
        if ($found_something) {
            $this->search_result = str_replace('{%RESULT_BLOGPOSTS%}', $this->results_blogposts, $this->search_result);
        }
        else {
            $this->search_result = str_replace('{%RESULT_BLOGPOSTS%}', $this->pec_localization->get('LABEL_SEARCH_IN_POSTS_NOTHINGFOUND'), $this->search_result);
        }
    }
    
    public function do_search() {
        $this->find_in_articles();
        $this->find_in_blogposts();
        
        $this->article_object->set_content($this->search_result);
    }
    
    public function get() {
        return $this->article_object;
    }
    
    public static function get_search_form() {
        global $pec_settings, $pec_localization;
        
        $form_template = get_intern_template(SEARCHFORM_TPL_FILE);
        $form = str_replace('{%SEARCH_URL%}', create_search_url(), $form_template);
        
        if ($pec_settings->get_url_type() == URL_TYPE_REWRITE) {
            $form = str_replace('{%SEARCH_QUERY_VAR_INPUT%}', '', $form);
        }
        else {
            // we need to create an input for the "target" Query-Var 
            // because it doesn't work if we just append it to the action with "?target=search"
            $form = str_replace('{%SEARCH_QUERY_VAR_INPUT%}', '<input type="hidden" name="target" value="' . QUERY_TARGET_SEARCH . '" />', $form);
        }
        
        $form = str_replace('{%SUBMIT_TEXT%}', $pec_localization->get('BUTTON_SEARCH'), $form);
        
        return $form;
    }
}

?>