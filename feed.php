<?php

/**
 * feed.php - Feed redirect file
 * 
 * Redirects to a given blog/category/tag feed
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
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.2
 * @link		http://pecio-cms.com
 */

/* core includes */
require_once('pec_includes/functions.inc.php');
require_once('common.inc.php');
/* core include end */

if (isset($_GET['blog']) && empty($_GET['blog'])) {
	header('Content-Type: application/rss+xml');
	if (file_exists(MAIN_FEED_PATH . MAIN_FEED_FILE)) {
		readfile(MAIN_FEED_PATH . MAIN_FEED_FILE);
	}
}
elseif (isset($_GET['category']) && empty($_GET['category'])) {
	
	if (isset($_GET['id']) && !empty($_GET['id'])) {
		$filename = str_replace('/', '', str_replace('.', '', $_GET['id']));
		header('Content-Type: application/rss+xml');
		if (file_exists(CATEGORY_FEED_PATH . $filename . '.xml')) {
			readfile(CATEGORY_FEED_PATH . $filename . '.xml');
		}
	}
	else {
		header('Content-Type: application/rss+xml');
	}
	
}
elseif (isset($_GET['tag']) && empty($_GET['tag'])) {
	
	if (isset($_GET['id']) && !empty($_GET['id'])) {
		$filename = str_replace('/', '', str_replace('.', '', $_GET['id']));
		header('Content-Type: application/rss+xml');
		if (file_exists(TAG_FEED_PATH . $filename . '.xml')) {
			readfile(TAG_FEED_PATH . $filename . '.xml');
		}
	}
	else {
		header('Content-Type: application/rss+xml');
	}
	
}

exit;

?>