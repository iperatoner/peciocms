<?php

define('INSTALLATION', true);

/* core includes, creating core objects */
require_once('../pec_includes/functions.inc.php');
require_once('common.inc.php');
require_once('../pec_core.inc.php');
/* core include end */

if (isset($_GET['locale']) && !empty($_GET['locale']) && PecLocale::exists($_GET['locale'])) {
	$pec_localization = new PecLocale($_GET['locale']);
	define('LOCALE_QUERY_VAR', '&locale=' . $_GET['locale']);
}
else {
	define('LOCALE_QUERY_VAR', '');
} 

$start_menupoint_class = 'login_menu_element';
$database_menupoint_class = 'login_menu_element';
$sitedata_menupoint_class = 'login_menu_element';
$installing_menupoint_class = 'login_menu_element';
$finished_menupoint_class = 'login_menu_element';

switch ($_GET['step']) {
    case '2': $title = $pec_localization->get('LABEL_INSTALLATION_DATABASE'); 	 $database_menupoint_class 	 = 'login_menu_element_active'; break;
    case '3': $title = $pec_localization->get('LABEL_INSTALLATION_SITEDATA');  $sitedata_menupoint_class 	 = 'login_menu_element_active'; break;
    case '4': $title = $pec_localization->get('LABEL_INSTALLATION_INSTALLING'); $installing_menupoint_class = 'login_menu_element_active'; break;
    case '5': $title = $pec_localization->get('LABEL_INSTALLATION_FINISHED'); 	 $finished_menupoint_class 	 = 'login_menu_element_active'; break;
    default : $title = $pec_localization->get('LABEL_INSTALLATION_START'); 	 $start_menupoint_class 	 = 'login_menu_element_active'; break;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

    <head>
        <title>pecio cms &raquo; <?php echo $pec_localization->get('LABEL_INSTALLATION'); ?> &raquo; <?php echo $title; ?></title>
        
        <link type="text/css" rel="stylesheet" href="../pec_admin/pec_style/css/login/layout.css" />
        <link type="text/css" rel="stylesheet" href="../pec_admin/pec_style/css/login/misc.css" />
        <link type="text/css" rel="stylesheet" href="../pec_admin/pec_style/css/misc.css" />
        <link type="text/css" rel="stylesheet" href="../pec_admin/pec_style/css/format.css" />
                
        <script type="text/javascript" src="../pec_admin/pec_style/js/mootools/mootools-core.js"></script>
        <script type="text/javascript" src="../pec_admin/pec_style/js/mootools/mootools-more.js"></script>
        <script type="text/javascript" src="../pec_admin/pec_style/js/main-animations.js"></script>
        
        <script type="text/javascript" src="../pec_admin/pec_style/js/forms.js"></script>
    </head>

    <body>
        <div id="main_wrapper">
            <div id="main_inner_wrapper">
                <div id="top_wrapper">
                
                    <div style="text-align: center;">
                        <table cellpadding="0" cellspacing="0" style="height: 57px;" width="100%">
                            <tr>
                                <td valign="middle" align="center" class="td_middle">
                                    <img src="../pec_admin/pec_style/images/logo.png" alt="pecio cms" style="margin-left: -3px;" />
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                </div>
                <div id="middle_wrapper">
                    <div id="middle_inner_wrapper">                        
                            <span class="<?php echo $start_menupoint_class; ?>"><?php echo $pec_localization->get('LABEL_INSTALLATION_START'); ?></span>
                            <span class="<?php echo $database_menupoint_class; ?>"><?php echo $pec_localization->get('LABEL_INSTALLATION_DATABASE'); ?></span>
                            <span class="<?php echo $sitedata_menupoint_class; ?>"><?php echo $pec_localization->get('LABEL_INSTALLATION_SITEDATA'); ?></span>
                            <span class="<?php echo $installing_menupoint_class; ?>"><?php echo $pec_localization->get('LABEL_INSTALLATION_INSTALLING'); ?></span>
                            <span class="<?php echo $finished_menupoint_class; ?>"><?php echo $pec_localization->get('LABEL_INSTALLATION_FINISHED'); ?></span>
                            <br /><br /><br />
                            
                            <h2><?php echo $pec_localization->get('LABEL_INSTALLATION'); ?> - <?php echo $title; ?></h2><br /><br />
                                                       
                            
                            <div id="messages">
                                <?php 
                                if (isset($_GET['message'])) {}
                                ?>
                            </div>
                            
                            <div id="content">
                                <?php                                 
                                
								switch ($_GET['step']) {
								    case '2': require('steps/2.step.php'); break;
								    case '3': require('steps/3.step.php'); break;
								    case '4': require('steps/4.step.php'); break;
								    case '5': require('steps/5.step.php'); break;
								    default : require('steps/1.step.php'); break;
								}
								
								?>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>
