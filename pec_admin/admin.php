<?php

/**
 * pec_admin/admin.php - Main admin frontend file
 * 
 * The main administration frontend file which creates the main 
 * HTML layout for the different areas.
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
 * @subpackage	pec_admin
 * @author		Immanuel Peratoner <immanuel.peratoner@gmail.com>
 * @copyright	2009-2010 Immanuel Peratoner
 * @license		http://www.gnu.de/documents/gpl-3.0.en.html GNU GPLv3
 * @version		2.0.5
 * @link		http://pecio-cms.com
 */

/* core includes, creating core objects */
require_once('../pec_includes/functions.inc.php');
require_once('common.inc.php');
require_once('../pec_core.inc.php');
/* core include end */

require_once('pec_includes/admin_functions.inc.php');

if (!$pec_session->is_logged_in()) {
    pec_redirect('pec_admin/index.php');
}

require_once('pec_classes/area.class.php');

$plugins = PecPlugin::load();

// install plugins that have never been installed yet
auto_install_plugins($plugins);

if (isset($_GET['t']) && $_GET['t'] == 'plugin' && PecPlugin::exists('area_name', $_GET[ADMIN_AREA_VAR])) {
    // load the plugin and its area file and data
    $plugin = PecPlugin::load('area_name', $_GET[ADMIN_AREA_VAR]);
    $area = new PecArea($plugin, true);
}
else {
    // load the area file and its data
    $area = new PecArea($_GET[ADMIN_AREA_VAR]);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

    <head>
        <title>pecio cms</title>
        
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <link type="text/css" rel="stylesheet" href="pec_style/css/layout.css" />
        <link type="text/css" rel="stylesheet" href="pec_style/css/menu.css" />
        <link type="text/css" rel="stylesheet" href="pec_style/css/format.css" />
        <link type="text/css" rel="stylesheet" href="pec_style/css/misc.css" />
        <link type="text/css" rel="stylesheet" href="pec_style/css/tables.css" />
        
        <script type="text/javascript" src="pec_style/js/mootools/mootools-core.js"></script>
        <script type="text/javascript" src="pec_style/js/mootools/mootools-more.js"></script>
        <script type="text/javascript" src="pec_style/js/main-animations.js"></script>
        
        <script type="text/javascript" src="pec_style/js/misc.js"></script>
        <script type="text/javascript" src="pec_style/js/forms.js"></script>
        <script type="text/javascript" src="pec_style/js/ck_toolbars.js"></script>
        
        <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
        <script type="text/javascript" src="ckeditor/ckfinder/ckfinder.js"></script>
        
        <!--[if IE 7]>
		<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta2)/IE9.js"></script>
		<![endif]-->
        
        <?php $area->out('head_data'); ?>
    </head>

    <body>
        <div id="main_wrapper">
            <div id="main_inner_wrapper">
                <div id="top_wrapper">
                
                    <div style="float: left;">
                        <table cellpadding="0" cellspacing="0" style="height: 57px;">
                            <tr>
                                <td valign="middle" class="td_middle">
                                    <a href="."><img src="pec_style/images/logo.png" alt="pecio cms" style="margin-left: 10px; border: 0px;" /></a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div style="float: right;">
                        <table cellpadding="0" cellspacing="0" style="height: 53px;">
                            <tr>
                                <td>
                                    <span style="color: #ffffff; position: relative; top: -2px; text-shadow: 0pt 0pt 7px #ffffff;">
                                    	<?php 
                                    	$user_forename = $pec_session->get('pec_user')->get_forename();
                                    	$user_welcome_name = !empty($user_forename) ? $user_forename : $pec_session->get('pec_user')->get_name(); ?>
                                        <?php echo str_replace('{%USER%}', $user_welcome_name, $pec_localization->get('LABEL_GENERAL_WELCOME')); ?>&nbsp;&nbsp;
                                    </span>
                                </td>
                                <td valign="middle" class="td_middle">
                                    <a href="../" style="border: 0px;" target="_blank">
                                        <img src="pec_style/images/search.png" alt="View website" title="<?php $pec_localization->out('LABEL_GENERAL_VIEWSITE'); ?>" class="shortcut_button" style="border: 0; margin-right: -1px;" />
                                    </a>&nbsp;
                                </td>
                                <td valign="middle" class="td_middle">
                                    <a href="logout.php" style="border: 0px;">
                                        <img src="pec_style/images/bright-logout.png" alt="Logout" title="<?php $pec_localization->out('LABEL_GENERAL_LOGOUT'); ?>" class="shortcut_button" style="border: 0; margin-right: 13px;" />
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                </div>
                <div id="middle_wrapper">
                    <div id="middle_inner_wrapper">
                    
                        <div id="left_box">
                        
                            <?php if ($pec_session->get('pec_user')->get_permission('permission_articles') > PERMISSION_NONE ||
                                      $pec_session->get('pec_user')->get_permission('permission_texts') > PERMISSION_NONE ||
                                      $pec_session->get('pec_user')->get_permission('permission_links') > PERMISSION_NONE ||
                                      $pec_session->get('pec_user')->get_permission('permission_menupoints') > PERMISSION_NONE) { 
                            ?>
                            <div class="menu_category">
                                <div class="menu_category_head">
                                    <?php $pec_localization->out('LABEL_GENERAL_CONTENT'); ?>
                                </div>
                                
                                <div class="menu_category_main">
                                    <ul class="left_menu">
                                        <?php if ($pec_session->get('pec_user')->get_permission('permission_articles') > PERMISSION_NONE) { ?>
                                        <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=articles'"><a href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=articles"><?php $pec_localization->out('LABEL_GENERAL_ARTICLES'); ?></a></li>
                                        <?php } ?>
                                        
                                        <?php if ($pec_session->get('pec_user')->get_permission('permission_texts') > PERMISSION_NONE) { ?>
                                        <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=texts'"><a href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=texts"><?php $pec_localization->out('LABEL_GENERAL_TEXTS'); ?></a></li>
                                        <?php } ?>
                                        
                                        <?php if ($pec_session->get('pec_user')->get_permission('permission_links') > PERMISSION_NONE) { ?>
                                        <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=links'"><a href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=links"><?php $pec_localization->out('LABEL_GENERAL_LINKS'); ?></a></li>
                                        <?php } ?>
                                        
                                        <?php if ($pec_session->get('pec_user')->get_permission('permission_menupoints') > PERMISSION_NONE) { ?>
                                        <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=menupoints'"><a href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=menupoints"><?php $pec_localization->out('LABEL_GENERAL_MENUPOINTS'); ?></a></li>
                                        <?php } ?>
                                        
                                        <?php if ($pec_session->get('pec_user')->get_permission('permission_articles') > PERMISSION_READ || $pec_session->get('pec_user')->get_permission('permission_texts') > PERMISSION_READ) { ?>
                                        <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=filemanager'"><a href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=filemanager"><?php $pec_localization->out('LABEL_GENERAL_FILEMANAGER'); ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                
                            </div>
                            <?php } ?>
                        
                        
                            <?php if ($pec_session->get('pec_user')->get_permission('permission_blogposts') > PERMISSION_NONE ||
                                      $pec_session->get('pec_user')->get_permission('permission_blogcomments') > PERMISSION_NONE) { 
                            ?>
                            <div class="menu_category">
                                <div class="menu_category_head">
                                    <?php $pec_localization->out('LABEL_GENERAL_BLOG'); ?>
                                </div>
                                
                                <div class="menu_category_main">
                                    <ul class="left_menu">
                                        <?php if ($pec_session->get('pec_user')->get_permission('permission_blogposts') > PERMISSION_NONE) { ?>
                                        <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=blog-posts'"><a href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=blog-posts"><?php $pec_localization->out('LABEL_GENERAL_POSTS'); ?></a></li>
                                        <?php } ?>
                                        
                                        <?php if ($pec_session->get('pec_user')->get_permission('permission_blogcomments') > PERMISSION_NONE) { ?>
                                        <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=blog-comments'"><a href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=blog-comments"><?php $pec_localization->out('LABEL_GENERAL_COMMENTS'); ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                
                            </div>
                            <?php } ?>
                        
                            <?php if ($pec_session->get('pec_user')->get_permission('permission_users') > PERMISSION_NONE ||
                                      $pec_session->get('pec_user')->get_permission('permission_templates') > PERMISSION_NONE ||
                                      $pec_session->get('pec_user')->get_permission('permission_settings') > PERMISSION_NONE) { 
                            ?>
                            <div class="menu_category">
                                <div class="menu_category_head">
                                    <?php $pec_localization->out('LABEL_GENERAL_ADMINISTRATION'); ?>
                                </div>
                                
                                <div class="menu_category_main">
                                    <ul class="left_menu">
                                        <?php if ($pec_session->get('pec_user')->get_permission('permission_users') > PERMISSION_NONE) { ?>
                                        <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=users'"><a href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=users"><?php $pec_localization->out('LABEL_GENERAL_USERS'); ?></a></li>
                                        <?php } ?>
                                        
                                        <?php if ($pec_session->get('pec_user')->get_permission('permission_templates') > PERMISSION_NONE) { ?>
                                        <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=templates'"><a href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=templates"><?php $pec_localization->out('LABEL_GENERAL_TEMPLATES'); ?></a></li>
                                        <?php } ?>
                                        
                                        <?php if ($pec_session->get('pec_user')->get_permission('permission_plugins') > PERMISSION_NONE) { ?>
                                        <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=plugins'"><a href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=plugins"><?php $pec_localization->out('LABEL_GENERAL_PLUGINS'); ?></a></li>
                                        <?php } ?>
                                        
                                        <?php if ($pec_session->get('pec_user')->get_permission('permission_settings') > PERMISSION_NONE) { ?>
                                        <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=settings'"><a href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=settings"><?php $pec_localization->out('LABEL_GENERAL_SETTINGS'); ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                
                            </div>
                            <?php } ?>
                        
                            <?php if ($pec_session->get('pec_user')->get_permission('permission_plugins') > PERMISSION_NONE) { ?>
                            <br /><div style="border-bottom: 1px solid #7f7f7f;"></div><br />
                            <div class="menu_category">
                                <div class="menu_category_head">
                                    <a style="color: #000000;" href="<?php echo ADMIN_MAIN_FILE; ?>?<?php echo ADMIN_AREA_VAR; ?>=plugins"><?php $pec_localization->out('LABEL_GENERAL_PLUGINS'); ?></a>
                                </div>
                                
                                <div class="menu_category_main">
                                    <ul class="left_menu">
                                        <?php foreach ($plugins as $p) { ?>
                                            <li onclick="location.href='<?php echo ADMIN_MAIN_FILE; ?>?t=plugin&amp;<?php echo ADMIN_AREA_VAR; ?>=<?php echo $p->get_property('area_name'); ?>'">
                                            	<a href="<?php echo ADMIN_MAIN_FILE; ?>?t=plugin&amp;<?php echo ADMIN_AREA_VAR; ?>=<?php echo $p->get_property('area_name'); ?>">
                                            		<?php echo $p->get_property('title'); ?>
                                            	</a>
                                            	<span style="float: right; font-size: 7pt; color: #656565;">
                                            		<?php echo $p->is_installed() || !$p->installation_required() ? '&#x2713;' : '&#x2717;'; ?>
                                            	</span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                
                            </div>
                            <?php } ?>
                            
                        </div>
                        
                        <div id="right_box"><br />
                        
                        
                            <h2><?php $area->out('title'); ?></h2><br />
                            
                            <div id="messages">
                                <?php $area->out('messages'); ?>
                            </div>
                            
                            <div id="content">
                                <?php $area->out('content'); ?>
                            </div>
                            <br /><br />
                                                        
                        </div>
                        <div style="clear: left;"></div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>
