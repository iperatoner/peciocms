<?php

$htaccess = file_get_contents('../htaccess-sample');
$htaccess = str_replace('{%PECIO_PATH%}', pec_root_path(false), $htaccess);
file_put_contents('../htaccess-sample', $htaccess);

pec_set_installed_version(PEC_VERSION);

?>

<?php echo str_replace('{%VERSION%}', PEC_VERSION, $pec_localization->get('LABEL_INSTALLATION_FINISHTEXT')); ?>

<br /><br />
<div class="options_box_2">
	<strong><?php echo $pec_localization->get('LABEL_USERS_NAME'); ?>:</strong> admin<br />
	<strong><?php echo $pec_localization->get('LABEL_USERS_PASSWORD'); ?>:</strong> <?php echo base64_decode($_POST['generated_password']); ?>
</div>
<br />

<?php $pec_localization->out('LABEL_INSTALLATION_UPDATEPW_TEXT'); ?>

<br /><br />
<div class="options_box_2">
	<strong><?php echo $pec_localization->get('LABEL_UPDATE_PASSWORD'); ?>:</strong> <?php echo CMS_UPDATE_PASSWORD; ?>
</div>
<br />

<?php $pec_localization->out('LABEL_INSTALLATION_RMDIR_HINT'); ?>

<br /><br />

<?php $pec_localization->out('LABEL_INSTALLATION_RENAME_HTACCESS_HINT'); ?>
<br /><br /><br />

<a href="../pec_admin/"><input type="button" value="<?php echo $pec_localization->get('BUTTON_GOTO_LOGIN'); ?>" /></a>