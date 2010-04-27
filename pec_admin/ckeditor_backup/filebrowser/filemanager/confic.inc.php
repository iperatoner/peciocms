<?php
session_name('pec_session');
session_start();

if (!isset($_SESSION['pec_logged_in']) || !$_SESSION['pec_logged_in']) {
	die();
}

function get_correct_path($path) {
	$pec_path = dirname($_SERVER['PHP_SELF']);
	$pec_path = str_replace('pec_admin/ckeditor/filebrowser/filemanager', '', $pec_path);
	$pec_path = str_replace('//', '/', $pec_path);

	$correct_path = str_replace('//', '/', $pec_path . str_replace(realpath('../../../../'), '', $path));
	
	return $correct_path;
}

function recursive_rmdir($path) {
    if (!is_dir ($path)) {
        return -1;
    }
    
    $dir = @opendir ($path);
    
    if (!$dir) {
        return -2;
    }
    
    while (($entry = @readdir($dir)) !== false) {
        if ($entry == '.' || $entry == '..') continue;
        if (is_dir ($path.'/'.$entry)) {
            $res = recursive_rmdir ($path.'/'.$entry);
            if ($res == -1) {
                @closedir ($dir);
                return -2;
            } else if ($res == -2) {
                @closedir ($dir);
                return -2;
            } else if ($res == -3) {
                @closedir ($dir);
                return -3;
            } else if ($res != 0) {
                @closedir ($dir);
                return -2;
            }
        } 
        else if (is_file ($path.'/'.$entry) || is_link ($path.'/'.$entry)) {
            $res = @unlink ($path.'/'.$entry);
            if (!$res) {
                @closedir ($dir);
                return -2;
            }
        } else {
            @closedir ($dir);
            return -3;
        }
    }
    
    @closedir ($dir);
    $res = @rmdir ($path);
    
    if (!$res) {
        return -2;
    }
    
    return 0;
}

$pec_real_path = realpath('../../../../pec_upload') . '/';

define("C4d28b848", $pec_real_path); // For example ../../userfiles/ 
define("C70d7bd0f", 'http://' . $_SERVER['HTTP_HOST']); // Absolute path for example http://www.mysite.com
?>