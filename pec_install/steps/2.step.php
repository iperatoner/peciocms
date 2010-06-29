<?php

if (isset($_GET['perform_actions']) && empty($_GET['perform_actions'])) {
	
	if (isset($_POST['db_prefix']) && isset($_POST['db_type']) && isset($_POST['db_host']) &&
		isset($_POST['db_user']) && isset($_POST['db_pw']) && isset($_POST['db_name'])) {
		
		$db_type = $_POST['db_type'];
		$db_prefix = $_POST['db_prefix'];
		
		$db_host = $db_type == TYPE_MYSQL ? $_POST['db_host'] : '';
		$db_user = $db_type == TYPE_MYSQL ? $_POST['db_user'] : '';
		$db_pw 	 = $db_type == TYPE_MYSQL ? $_POST['db_pw'] : '';
		$db_name = $db_type == TYPE_MYSQL ? $_POST['db_name'] : CONFIG_DIR . $_POST['db_file'];
		
		$update_password = random_string(5);
		
		$config_data = 
"<?php

define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PW', '$db_pw');
define('DB_NAME', '$db_name');

define('DB_TYPE', '$db_type');
define('DB_PREFIX', '$db_prefix');

define('CMS_UPDATE_PASSWORD', '$update_password');

?>";
		
		try {
			file_put_contents('../' . CONFIG_DIR . CONFIG_FILE, $config_data);
		}
		catch (Exception $e) {
			die('<strong>Error:</strong> ' . $e->getMessage()); 
		}
		
		$db = new PecDatabase($db_host, $db_user, $db_pw, $db_name, $db_type);
		
		if ($db->db_check_connection()) {
			pec_redirect('pec_install/install.php?step=3' . LOCALE_QUERY_VAR);
		}
		else {
			PecMessageHandler::raise('db_connection_failed', array(
				'{%DB_LOG%}' => ''
			));
		}
	}
	
}

?>

<form method="post" action="install.php?step=2&perform_actions<?php echo LOCALE_QUERY_VAR; ?>">
	
	<h3><?php echo $pec_localization->get('LABEL_INSTALLATION_TBLPREFIX'); ?>:</h3>
	<input type="text" name="db_prefix" value="pec_" style="width: 210px;" /><br /><br />
	
	<div class="options_box_1" style="padding: 8px;">	
		<input type="radio" name="db_type" id="db_type_mysql" value="<?php echo TYPE_MYSQL; ?>" /> 
		<label for="db_type_mysql"><span style="font-weight: bold;"><?php echo $pec_localization->get('LABEL_INSTALLATION_MYSQL'); ?></span></label><br />
	</div>
	<div class="options_box_2" style="padding: 8px;">	
		<h3><?php echo $pec_localization->get('LABEL_INSTALLATION_DBHOST'); ?>:</h3>
		<input type="text" name="db_host" value="localhost" style="width: 210px;" /><br /><br />
		
		<h3><?php echo $pec_localization->get('LABEL_INSTALLATION_DBUSER'); ?>:</h3>
		<input type="text" name="db_user" value="" style="width: 210px;" /><br /><br />
		
		<h3><?php echo $pec_localization->get('LABEL_INSTALLATION_DBPW'); ?>:</h3>
		<input type="password" name="db_pw" value="" style="width: 210px;" /><br /><br />
		
		<h3><?php echo $pec_localization->get('LABEL_INSTALLATION_DBNAME'); ?>:</h3>
		<input type="text" name="db_name" value="" style="width: 210px;" /><br /><br />
	</div>
		
	<br />
	<div class="options_box_1" style="padding: 8px;">	
		<input type="radio" name="db_type" id="db_type_sqlite" value="<?php echo TYPE_SQLITE; ?>" />
		<label for="db_type_sqlite"><span style="font-weight: bold;"><?php echo $pec_localization->get('LABEL_INSTALLATION_SQLITE'); ?></span></label><br />
	</div>
	<div class="options_box_2" style="padding: 8px;">
		<h3><?php echo $pec_localization->get('LABEL_INSTALLATION_DBFILE'); ?>:</h3>
		<input type="text" name="db_file" value="pecio_database.sqlite" style="width: 210px;" />
	</div><br /><br /><br />

	
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td align="left">
				<input type="button" value="<?php echo $pec_localization->get('BUTTON_PREV'); ?>" />
			</td>
			<td align="right">
				<input type="submit" value="<?php echo $pec_localization->get('BUTTON_NEXT'); ?>" />
			</td>
		</tr>	
	</table>
	
</form>