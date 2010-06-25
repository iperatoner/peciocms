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
 * @version		2.0.2
 * @link		http://pecio-cms.com
 */

function do_update($from_version) {
	$backup_arrays = backup_database_tables();
	update_database_tables();
	restore_backups($backup_arrays);
}

function backup_database_tables() {
	global $pec_database;
	
	// Select all data from the tables that need to be backupped
	$query_articles = "SELECT * FROM " . DB_PREFIX . "articles";
	$query_settings = "SELECT * FROM " . DB_PREFIX . "settings";
	
	// Execute the queries
    $pec_database->db_connect();
    $result_articles = $pec_database->db_query($query_articles);
    $result_settings = $pec_database->db_query($query_settings);
    $pec_database->db_close_handle();
    
    // Save results into arrays
    $articles = array();
    $settings = array($pec_database->db_fetch_array($result_settings));
    
    while ($a = $pec_database->db_fetch_array($result_articles)) {
    	$articles[] = $a;
    }
    
    return array(
    	'articles' => $articles,
    	'settings' => $settings
    );
}

function update_database_tables() {
	global $pec_database;
	
	$queries = array();
	
	// Remove both tables completely
	$queries['drop_articles'] = "DROP TABLE " . DB_PREFIX . "articles";
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
	
	$queries['create_settings_table'] =
	"CREATE TABLE " . DB_PREFIX . "articles (
	    article_id          INT AUTO_INCREMENT PRIMARY KEY,
	    article_title       VARCHAR(512),
	    article_slug        VARCHAR(768),
	    article_content     TEXT,
	    article_onstart     BOOLEAN,
	    article_template_id VARCHAR(512)
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
        $article->save();
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