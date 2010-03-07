<?php 

/**
 * pec_admin/pec_areas/default.area.php - Default area
 * 
 * Admin area which is displayed by default, e.g. if the given area does not exist.
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

define('AREA', ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=default');

/* main area data */
$area = array();
$area["title"] = str_replace('{%VERSION%}', PEC_VERSION, $pec_localization->get('LABEL_GENERAL_WELCOMETITLE'));
$area["permission_name"] = '';
$area["head_data"] = '';
$area["messages"] = '';
$area["content"] = 'No view was executed.';


/* a function that does actions depending on what data is in the query string */

function do_actions() {
    $messages = '';
    return $messages;
}


/* creating functions for all the different views that will be available for this area */

function view_default() {
	global $pec_localization, $pec_session;
	
    $area_data = array();
    $area_data['title'] = str_replace('{%VERSION%}', PEC_VERSION, $pec_localization->get('LABEL_GENERAL_WELCOMETITLE'));
    
    $area_data['content'] = '
    	<div class="float_left">
    ';
    
    // Overview Start
    $area_data['content'] .= '
	    	<div class="options_box_2" style="width: 350px;">
	    		<h3>' . $pec_localization->get('LABEL_OVERVIEW') . '</h3>
	    		<table class="overview_table" cellspacing="0">
	    			<tbody>';
    
    if ($pec_session->get('pec_user')->get_permission('permission_articles') > PERMISSION_NONE) {
		$article_count = count(PecArticle::load());
	    $area_data['content'] .= '
	    				<tr class="view_row">
	    					<td class="normal_column thin_column">
	    						<a href="' . ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=articles">' . $article_count . '</a>
	    					</td>
	    					<td class="normal_column">
	    						<a href="' . ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=articles">' . $pec_localization->get('LABEL_GENERAL_ARTICLES') . '</a>
	    					</td>
	    				</tr>';
    }
    
    if ($pec_session->get('pec_user')->get_permission('permission_texts') > PERMISSION_NONE) {
		$text_count = count(PecSidebarText::load());
	    $area_data['content'] .= '
	    				<tr class="view_row">
	    					<td class="normal_column thin_column">
	    						<a href="' . ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=texts">' . $text_count . '</a>
	    					</td>
	    					<td class="normal_column">
	    						<a href="' . ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=texts">' . $pec_localization->get('LABEL_GENERAL_TEXTS') . '</a>
	    					</td>
	    				</tr>';
    }
    
    if ($pec_session->get('pec_user')->get_permission('permission_blogposts') > PERMISSION_NONE) {
		$blogpost_count = count(PecBlogPost::load());
    	$area_data['content'] .= '
	    				<tr class="view_row">
	    					<td class="normal_column thin_column">
	    						<a href="' . ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=blog-posts">' . $blogpost_count . '</a>
	    					</td>
	    					<td class="normal_column">
	    						<a href="' . ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=blog-posts">' . $pec_localization->get('LABEL_GENERAL_BLOGPOSTS') . '</a>
	    					</td>
	    				</tr>';
    }
    
    if ($pec_session->get('pec_user')->get_permission('permission_blogcomments') > PERMISSION_NONE) {
    	$comments = PecBlogComment::load();
		$blogcomment_count = count($comments);
    	$area_data['content'] .= '
	    				<tr class="view_row">
	    					<td class="normal_column thin_column">
	    						<a href="' . ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=blog-comments">' . $blogcomment_count . '</a>
	    					</td>
	    					<td class="normal_column">
	    						<a href="' . ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=blog-comments">' . $pec_localization->get('LABEL_GENERAL_BLOGCOMMENTS') . '</a>
	    					</td>
	    				</tr>';
    }
    
    $area_data['content'] .= '
	    			</tbody>
	    		</table>
	    	</div>
    ';
    // Overview End
    
    // Latest Comments Start
    $area_data['content'] .= '
	    	<div class="options_box_2" style="width: 350px;">
	    		<h3>' . $pec_localization->get('LABEL_OVERVIEW_LATESTCOMMENTS') . '</h3>
	    		<table class="overview_table" cellspacing="0">
	    			<tbody>';
    
    if ($pec_session->get('pec_user')->get_permission('permission_blogcomments') > PERMISSION_NONE) {
    	$comments = array_reverse($comments);
    	$max_count = 3;
    	$count = 0;
    	foreach ($comments as $c) {
    		if ($count < $max_count) {
	        	if (PecBlogPost::exists('id', $c->get_post_id())) {
		            $post = $c->get_post();
		            $post_title = $post->get_title();
		        }
		        else {
		            $post_title = '-';
		        }
		        
		        $content_cut = strlen($c->get_content()) > 50 ? substr($c->get_content(), 0, 50) . '...' : $c->get_content();
		        
		    	$area_data['content'] .= '
		    				<tr class="view_row">
			    				<td class="normal_column">
			    					<h2 class="overview_comment_heading">
			    						<a href="' . ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=blog-comments&amp;view=edit&amp;id=' . $c->get_id() . '">' . $c->get_title() . '</a>
		    						</h2>
			    					<span class="overview_comment_meta">
			    						' . $pec_localization->get('LABEL_COMMENTS_AUTHOR') . ': ' . $c->get_author() . ' - ' . $pec_localization->get('LABEL_GENERAL_POST') . ': 
			    						<a href="' . ADMIN_MAIN_FILE . '?' . ADMIN_AREA_VAR . '=blog-posts&amp;view=edit&amp;id=' . $c->get_post_id() . '">' . $post_title . '</a>
		    						</span><br />
			    					<p class="overview_comment_content">' . $content_cut . '</p>
			    				</td>
			    			</tr>
		    	';
		    	$count++;
    		}
    		else {
    			break;
    		}
    	}
    }    
    
    $area_data['content'] .= '
	    			</tbody>
	    		</table>
	    	</div>
    ';
    // Latest Comments End
    
    
    $area_data['content'] .= '
    	</div>
    	<div class="float_left" style="margin-left: 10px;">
    ';
    
    $counter_data = get_counter_data();
    
    $area_data['content'] .= '
    		<div class="options_box_2" style="width: 350px;">
	    		<h3>' . $pec_localization->get('LABEL_OVERVIEW_VISITSTATS') . '</h3>
    			<table class="overview_table" cellspacing="0">
	    			<tbody>
	    				<tr class="view_row">
	    					<td class="normal_column medium_column">
	    						' . $pec_localization->get('LABEL_OVERVIEW_STATS_TODAY') . ':
	    					</td>
	    					<td class="normal_column">
	    						' . $counter_data['today'] . '
	    					</td>
	    				</tr>
	    				<tr class="view_row">
	    					<td class="normal_column medium_column">
	    						' . $pec_localization->get('LABEL_OVERVIEW_STATS_WEEK') . ':
	    					</td>
	    					<td class="normal_column">
	    						' . $counter_data['week'] . '
	    					</td>
	    				</tr>
	    				<tr class="view_row">
	    					<td class="normal_column medium_column">
	    						' . $pec_localization->get('LABEL_OVERVIEW_STATS_MONTH') . ':
	    					</td>
	    					<td class="normal_column">
	    						' . $counter_data['month'] . '
	    					</td>
	    				</tr>
	    				<tr class="view_row">
	    					<td class="normal_column medium_column">
	    						' . $pec_localization->get('LABEL_OVERVIEW_STATS_YEAR') . ':
	    					</td>
	    					<td class="normal_column">
	    						' . $counter_data['year'] . '
	    					</td>
	    				</tr>
	    				<tr class="view_row">
	    					<td class="normal_column medium_column" style="border-top: 1px solid #c2c2c2;">
	    						' . $pec_localization->get('LABEL_OVERVIEW_STATS_TOTAL') . ':
	    					</td>
	    					<td class="normal_column" style="border-top: 1px solid #c2c2c2;">
	    						' . $counter_data['all'] . '
	    					</td>
	    				</tr>
	    				<tr class="view_row" style="background: #f2f2f2;">
	    					<td class="normal_column medium_column">
	    						' . $pec_localization->get('LABEL_OVERVIEW_STATS_RECORD') . ':
	    					</td>
	    					<td class="normal_column">
	    						' . $counter_data['record'] . ' (' . date('d.m.Y', $counter_data['record_date']) . ')
	    					</td>
	    				</tr>
	    			</tbody>
    			</table>
    		</div>
    ';
    
    $area_data['content'] .= '
    	</div>
    	<div style="clear: left;"></div>
    ';    
    
    return $area_data;
}


/* doing all the actions and then display the view given in the query string */
$area['messages'] = do_actions();

switch ($_GET['view']) {
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

?>