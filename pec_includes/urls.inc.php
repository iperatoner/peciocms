<?php

/**
 * pec_includes/urls.inc.php - URL generating functions
 * 
 * Defines functions to create the different available URLs of the CMS, 
 * always depending on which URL Type is chosen.
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
 * @subpackage	pec_includes
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */


/**
 * Creates the URL of the start page
 * 
 * @return	string The URL, e.g. "http://www.example.com/index.php?target=home"
 */
function create_home_url() {
    global $pec_settings;
    
    $url_type = $pec_settings->get_url_type();
    
    if ($url_type === URL_TYPE_DEFAULT) {
        $url = pec_root_url() . 'index.php?target=' . QUERY_TARGET_HOME;
    }
    elseif ($url_type === URL_TYPE_HUMAN) {
        $url = pec_root_url() . 'index.php?target=' . QUERY_TARGET_HOME;
    }
    elseif ($url_type === URL_TYPE_REWRITE) {
        $url = pec_root_url() . QUERY_TARGET_HOME;
    }
    
    return $url;
}

/**
 * Creates the URL of an article
 * 
 * @param	PecArticle 	$article The article object
 * @return	string The URL, e.g. "http://www.example.com/index.php?target=article&id=1"
 */
function create_article_url($article) {
    global $pec_settings;
    
    $url_type = $pec_settings->get_url_type();
    
    if ($url_type === URL_TYPE_DEFAULT) {
        $url = pec_root_url() . 'index.php?target=' . QUERY_TARGET_ARTICLE . '&id=' . $article->get_id();
    }
    elseif ($url_type === URL_TYPE_HUMAN) {
        $url = pec_root_url() . 'index.php?target=' . QUERY_TARGET_ARTICLE . '&id=' . $article->get_slug();
    }
    elseif ($url_type === URL_TYPE_REWRITE) {
        $url = pec_root_url() . QUERY_TARGET_ARTICLE . '/' . $article->get_slug();
    }
    
    return $url;
}

/**
 * Creates the URL of a search
 * 
 * @return	string The search URL, e.g. "http://www.example.com/index.php?target=search"
 */
function create_search_url() {
    global $pec_settings;
    
    $url_type = $pec_settings->get_url_type();
    
    if ($url_type === URL_TYPE_DEFAULT) {
        $url = pec_root_url() . 'index.php';
    }
    elseif ($url_type === URL_TYPE_HUMAN) {
        $url = pec_root_url() . 'index.php';
    }
    elseif ($url_type === URL_TYPE_REWRITE) {
        $url = pec_root_url() . QUERY_TARGET_SEARCH . '/';
    }
    
    return $url;
}

/**
 * Creates the URL of the blog
 * 
 * @param	integer|string $page: The page of blog entries, e.g. 2
 * @param	boolean 	   $home Wether the target should be the home-page or not
 * @return	string The URL of the blog, e.g. "http://www.example.com/index.php?target=blog"
 */
function create_blog_url($page=false, $home=false) {
    global $pec_settings;
    
    $url_type = $pec_settings->get_url_type();
    $query_target = $home ? QUERY_TARGET_HOME : QUERY_TARGET_BLOG;
    
    if ($url_type === URL_TYPE_DEFAULT) {
        $url = pec_root_url() . 'index.php?target=' . $query_target;
        $url .= $page !== false ? '&p=' . $page : ''; 
    }
    elseif ($url_type === URL_TYPE_HUMAN) {
        $url = pec_root_url() . 'index.php?target=' . $query_target;
        $url .= $page !== false ? '&p=' . $page : ''; 
    }
    elseif ($url_type === URL_TYPE_REWRITE) {
        $url = pec_root_url() . $query_target;
        $url .= $page !== false ? '/p/' . $page : ''; 
    }
    
    return $url;
}

/**
 * Creates the URL of a blogpost
 * 
 * @param	PecBlogPost $post A blog post object
 * @param	boolean 	$new_comment Wether the link shall include a new comment variable or not
 * @param	string 		$additional_vars Additional query vars, e.g. "foo=bar&something=2"
 * @return	string The URL of the blog post, e.g. "http://www.example.com/index.php?target=blog&post_id=1"
 */
function create_blogpost_url($post, $new_comment=false, $additional_vars=false) {
    global $pec_settings;
    
    $url_type = $pec_settings->get_url_type();
    
    if ($url_type === URL_TYPE_DEFAULT) {
        $url = pec_root_url() . 'index.php?target=' . QUERY_TARGET_BLOG . '&post_id=' . $post->get_id();
        $url .= $new_comment ? '&action=new-comment' : '';
        $url .= $additional_vars ? '&' . $additional_vars : '';
    }
    elseif ($url_type === URL_TYPE_HUMAN) {
        $url = pec_root_url() . 'index.php?target=' . QUERY_TARGET_BLOG . '&post_id=' . $post->get_slug();
        $url .= $new_comment ? '&action=new-comment' : '';
        $url .= $additional_vars ? '&' . $additional_vars : '';
    }
    elseif ($url_type === URL_TYPE_REWRITE) {
        $url = pec_root_url() . QUERY_TARGET_BLOG . '/post/' . $post->get_slug();
        $url .= $new_comment ? '/new-comment' : '';
        $url .= $additional_vars ? '/?' . $additional_vars : '';
    }
    
    return $url;
}

/**
 * Creates the URL of a blog category
 * 
 * @param	PecBlogCategory $category A blog category object
 * @param	integer|string  $page: The page of blog entries, e.g. 2
 * @param	boolean 		$home Wether the target should be the home-page or not
 * @return	string The URL of the blog category, e.g. "http://www.example.com/index.php?target=blog&category=4"
 */
function create_blogcategory_url($category, $page=false, $home=false) {
    global $pec_settings;
    
    $url_type = $pec_settings->get_url_type();
    $query_target = $home ? QUERY_TARGET_HOME : QUERY_TARGET_BLOG;
    
    if ($url_type === URL_TYPE_DEFAULT) {
        $url = pec_root_url() . 'index.php?target=' . $query_target . '&category=' . $category->get_id();
        $url .= $page !== false ? '&p=' . $page : ''; 
    }
    elseif ($url_type === URL_TYPE_HUMAN) {
        $url = pec_root_url() . 'index.php?target=' . $query_target . '&category=' . $category->get_slug();
        $url .= $page !== false ? '&p=' . $page : ''; 
    }
    elseif ($url_type === URL_TYPE_REWRITE) {
        $url = pec_root_url() . $query_target . '/category/' . $category->get_slug();
        $url .= $page !== false ? '/p/' . $page : ''; 
    }
    
    return $url;
}

/**
 * Creates the URL of a blog tag
 * 
 * @param	PecBlogTag	   $tag A blog tag object
 * @param	integer|string $page: The page of blog entries, e.g. 2
 * @param	boolean		   $home Wether the target should be the home-page or not
 * @return	string The URL of the blog tag, e.g. "http://www.example.com/index.php?target=blog&tag=6"
 */
function create_blogtag_url($tag, $page=false, $home=false) {
    global $pec_settings;
    
    $url_type = $pec_settings->get_url_type();
    $query_target = $home ? QUERY_TARGET_HOME : QUERY_TARGET_BLOG;
    
    if ($url_type === URL_TYPE_DEFAULT) {
        $url = pec_root_url() . 'index.php?target=' . $query_target . '&tag=' . $tag->get_id();
        $url .= $page !== false ? '&p=' . $page : ''; 
    }
    elseif ($url_type === URL_TYPE_HUMAN) {
        $url = pec_root_url() . 'index.php?target=' . $query_target . '&tag=' . $tag->get_slug();
        $url .= $page !== false ? '&p=' . $page : ''; 
    }
    elseif ($url_type === URL_TYPE_REWRITE) {
        $url = pec_root_url() . $query_target . '/tag/' . $tag->get_slug();
        $url .= $page !== false ? '/p/' . $page : ''; 
    }
    
    return $url;
}

/**
 * Creates the URL of a blog archive
 * 
 * @param	string		   $day The day, e.g. "05"
 * @param	string		   $month The month, e.g. "10"
 * @param	string		   $year The year, e.g. "2010"
 * @param	integer|string $page: The page of blog entries, e.g. 2
 * @param	boolean		   $home Wether the target should be the home-page or not
 * @return	string The URL of the blog archive, e.g. "http://www.example.com/index.php?target=blog&day=05&month=10&year=2010"
 */
function create_blogarchive_url($day=false, $month=false, $year=false, $page=false, $home=false) {
    global $pec_settings;
    
    $url_type = $pec_settings->get_url_type();
    $query_target = $home ? QUERY_TARGET_HOME : QUERY_TARGET_BLOG;
    
    if ($url_type === URL_TYPE_DEFAULT) {
        $day     = $day      ? '&day='      . $day   : '';
        $month     = $month ? '&month=' . $month : '';
        $year     = $year  ? '&year='  . $year  : '';
        
        $url = pec_root_url() . 'index.php?target=' . $query_target . $day . $month . $year;
        $url .= $page !== false ? '&p=' . $page : ''; 
    }
    elseif ($url_type === URL_TYPE_HUMAN) {
        $day     = $day      ? '&day='   .   $day   : '';
        $month     = $month ? '&month=' . $month : '';
        $year     = $year  ? '&year='  .  $year  : '';
        
        $url = pec_root_url() . 'index.php?target=' . $query_target . $day . $month . $year;
        $url .= $page !== false ? '&p=' . $page : ''; 
    }
    elseif ($url_type === URL_TYPE_REWRITE) {
        $day     = $day      ? 'd-' . $day   . '/' : '';
        $month     = $month ? 'm-' . $month . '/' : '';
        $year     = $year  ? 'y-' . $year  . '/' : '';
        
        $url = pec_root_url() . $query_target . '/archive/' . $day . $month . $year;
        $url .= $page !== false ? 'p/' . $page : ''; 
    }
    
    return $url;
}

/**
 * Creates the URL of a 404 page
 * 
 * @param	string $salt Some kind of random string that is used as an article id, so that pecio will indicate it as non-existent, e.g. "0"
 * @return	string The URL of the 404 page, e.g. "http://www.example.com/index.php?target=article&id=0"
 */
function create_404_url($salt='0') {
    global $pec_settings;
    
    $url_type = $pec_settings->get_url_type();
    
    if ($url_type === URL_TYPE_DEFAULT) {
        $url = pec_root_url() . 'index.php?target=' . QUERY_TARGET_ARTICLE . '&id=' . $salt;
    }
    elseif ($url_type === URL_TYPE_HUMAN) {
        $url = pec_root_url() . 'index.php?target=' . QUERY_TARGET_ARTICLE . '&id=' . random_string(9);
    }
    elseif ($url_type === URL_TYPE_REWRITE) {
        $url = pec_root_url() . QUERY_TARGET_ARTICLE . '/' . random_string(9);
    }
    
    return $url;
}

/**
 * Creates the URL of the main blog feed
 * 
 * @return	string The URL of the feed, e.g. "http://www.example.com/feed.php?blog"
 */
function create_blog_feed_url() {    
    return pec_root_url() . 'feed.php?blog';
}

/**
 * Creates the URL of a blog tag feed
 * 
 * @param	PecBlogTag $tag A blog tag object
 * @return	string The URL of the feed, e.g. "http://www.example.com/feed.php?tag&id=3"
 */
function create_blogtag_feed_url($tag=false) {
	if ($tag) {
    	return pec_root_url() . 'feed.php?tag&id=' . $tag->get_id();
	}
	else {
		return '';
	}
}

/**
 * Creates the URL of a blog category feed
 * 
 * @param	PecBlogCategory $category A blog category object
 * @return	string The URL of the feed, e.g. "http://www.example.com/feed.php?category&id=3"
 */
function create_blogcategory_feed_url($category=false) {
	if ($category) {
    	return pec_root_url() . 'feed.php?category&id=' . $category->get_id();
	}
	else {
		return '';
	}
}

?>