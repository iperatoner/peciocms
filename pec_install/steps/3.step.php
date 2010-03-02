<?php

// here are no actions to perform 
// because we are just sending the data to the next step: 
// the installation of database tables and entries

$url_type_select_options = '';
foreach (array(URL_TYPE_DEFAULT, URL_TYPE_HUMAN, URL_TYPE_REWRITE) as $url_type) {
	$url_type_select_options .= '<option value="' . $url_type . '">' . $url_type . '</option>';
}

?>

<form method="post" action="install.php?step=4<?php echo LOCALE_QUERY_VAR; ?>">
	
	<h3><?php echo $pec_localization->get('LABEL_SETTINGS_MAINTITLE'); ?>:</h3>
	<input type="text" name="setting_main_title" value="" style="width: 210px;" /><br /><br />
	
	<h3><?php echo $pec_localization->get('LABEL_SETTINGS_SUBTITLE'); ?>:</h3>
	<input type="text" name="setting_sub_title" value="" style="width: 210px;" /><br /><br />
	
	<h3><?php echo $pec_localization->get('LABEL_SETTINGS_ADMINEMAIL'); ?>:</h3>
	<input type="text" name="setting_admin_email" value="" style="width: 210px;" /><br /><br />
	
	<h3><?php echo $pec_localization->get('LABEL_SETTINGS_URLTYPE'); ?>:</h3>
	<select name="setting_url_type" style="width: 100px;">
		<?php echo $url_type_select_options; ?>
	</select>
	<br /><br /><br />
		
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td align="left">
				<input type="button" onclick="history.back();" value="<?php echo $pec_localization->get('BUTTON_PREV'); ?>" />
			</td>
			<td align="right">
				<input type="submit" value="<?php echo $pec_localization->get('BUTTON_NEXT'); ?>" />
			</td>
		</tr>	
	</table>
	
</form>