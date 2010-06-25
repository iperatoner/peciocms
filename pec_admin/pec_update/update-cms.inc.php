<?php

/**
 * pec_admin/pec_update/update-cms.inc.php - Update functions
 * 
 * Defines the functions that updates the CMS to the uploaded version. 
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
 * @subpackage	pec_admin.pec_update
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */

function do_update($from_version) {
	try {
		$backup_arrays = backup_database_tables();
		update_database_tables();
		restore_backups($backup_arrays);
	}
	catch (Exception $e) {
		echo $e;
		return false;
	}
	
	return true;
}

function backup_database_tables() {
	global $pec_database;
	
	// Select all data from the tables that need to be backupped
	$query_articles = "SELECT * FROM " . DB_PREFIX . "articles";
	$query_blogposts = "SELECT * FROM " . DB_PREFIX . "blogposts";
	$query_settings = "SELECT * FROM " . DB_PREFIX . "settings";
	
	// Execute the queries
    $pec_database->db_connect();
    $result_articles = $pec_database->db_query($query_articles);
    $result_blogposts = $pec_database->db_query($query_blogposts);
    $result_settings = $pec_database->db_query($query_settings);
    $pec_database->db_close_handle();
    
    // Save results into arrays
    $articles = array();
    $blogposts = array();
    $settings = array($pec_database->db_fetch_array($result_settings));

    while ($a = $pec_database->db_fetch_array($result_articles)) {
    	$articles[] = $a;
    }
    
    while ($bp = $pec_database->db_fetch_array($result_blogposts)) {
    	$blogposts[] = $bp;
    }
    
    return array(
    	'articles' => $articles,
    	'blogposts' => $articles,
    	'settings' => $settings
    );
}

function update_database_tables() {
	global $pec_database;
	
	$queries = array();
	
	// Remove both tables completely
	$queries['drop_articles'] = "DROP TABLE " . DB_PREFIX . "articles";
	$queries['drop_blogposts'] = "DROP TABLE " . DB_PREFIX . "blogposts";
	$queries['drop_settings'] = "DROP TABLE " . DB_PREFIX . "settings";
	
	// Recreate the new tables
	$queries['create_articles_table'] =
	"CREATE TABLE " . DB_PREFIX . "articles (
	    article_id          INT AUTO_INCREMENT PRIMARY KEY,
	    article_title       VARCHAR(512),
	    article_slug        VARCHAR(768),
	    article_content     TEXT,
	    article_onstart     BOOLEAN,
	    article_template_id VARCHAR(512)
	)";
	
	$queries['create_blogposts_table'] =
	"CREATE TABLE " . DB_PREFIX . "blogposts (
	    post_id             INT AUTO_INCREMENT PRIMARY KEY,
	    post_timestamp      VARCHAR(32),
	    post_year           VARCHAR(4),
	    post_month          VARCHAR(2),
	    post_day            VARCHAR(2),
	    post_author_id      INT,
	    post_title          VARCHAR(256),
	    post_slug           VARCHAR(256),
	    post_content_cut    TEXT,
	    post_content        TEXT,
	    post_tags           TEXT,
	    post_categories     TEXT,
	    post_comments_allowed  BOOLEAN,
	    post_status         BOOLEAN
	)";
	
	$queries['create_settings_table'] =
	"CREATE TABLE " . DB_PREFIX . "settings (
	    setting_id              INT AUTO_INCREMENT PRIMARY KEY,        
	    setting_sitename_main   VARCHAR(256),
	    setting_sitename_sub    VARCHAR(256),
	    setting_description     TEXT,
	    setting_tags            VARCHAR(512),
	    setting_admin_email     VARCHAR(512),
	    setting_comment_notify  BOOLEAN,
	    setting_locale          VARCHAR(4),
	    setting_url_type        VARCHAR(32),
	    setting_posts_per_page  INT(11),
	    setting_blog_onstart    BOOLEAN,
	    setting_template_id     VARCHAR(256),
	    setting_nospam_key_1    VARCHAR(2048),
	    setting_nospam_key_2    VARCHAR(2048),
	    setting_nospam_key_3    VARCHAR(2048)
	)";
	
	// Execute the queries
    $pec_database->db_connect();
    foreach ($queries as $q) {
    	$pec_database->db_query($q);
    }
    $pec_database->db_close_handle();
}

function restore_backups($backup_arrays) {
	foreach ($backup_arrays['articles'] as $a) {
		$article = new PecArticle(
			$a['article_id'], $a['article_title'], $a['article_content'],
            $a['article_onstart'], GLOBAL_TEMPLATE_ID, $a['article_slug']
        );
        $article->save(true);
	}
	
	foreach ($backup_arrays['blogposts'] as $p) {
		$post = new PecBlogPost(
			$p['post_id'], $p['post_timestamp'], $p['post_year'], $p['post_month'], 
            $p['post_day'], $p['post_author_id'], $p['post_title'], $p['post_content_cut'], 
            $p['post_content'], $p['post_tags'], $p['post_categories'], true,
            $p['post_status'], $p['post_slug']
        );
        $post->save(false, true);
	}
	
	foreach ($backup_arrays['settings'] as $s) {
		$setting = new PecSetting(
			$s['setting_id'], $s['setting_sitename_main'], $s['setting_sitename_sub'],
            $s['setting_description'], $s['setting_tags'], $s['setting_admin_email'], 
            true, $s['setting_locale'], $s['setting_url_type'], 
            $s['setting_posts_per_page'], $s['setting_blog_onstart'], $s['setting_template_id'], 
            $s['setting_nospam_key_1'], $s['setting_nospam_key_2'], $s['setting_nospam_key_3']
        );
        $setting->save();
	}
}

?>
