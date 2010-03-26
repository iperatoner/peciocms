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

<h3 style="margin-left: -2px;">Security hints:</h3>

<?php $pec_localization->out('LABEL_INSTALLATION_RMDIR_HINT'); ?>

<br /><br />

You should also set some file permissions back to their original values:<br /><br />

<?php $current_core_permissions = pec_read_core_permissions(); ?>
<table class="data_table">
    <tr class="head">
        <td class="short">File/Directory</td>
        <td class="short">Required</td>
        <td class="thin">Current</td>
    </tr>
    <?php 
    foreach ($pec_core_permissions as $core_filename => $perm) {
    	if ($perm['permission_before_install'] != $perm['permission_after_install']) {
	    	switch ($perm['type']) {
	    		case 'r': $permission_type = 'recursive'; break;
	    		case 'nr': $permission_type = 'not recursive'; break;
	    		case 'f': $permission_type = 'file'; break;
	    	}
	    	
	    	if ($perm['permission_after_install'] == $current_core_permissions[$core_filename]) {
	    		$perm_color = 'green';
	    	}
	    	else {
	    		$perm_color = 'red';
	    	}
    ?>
		    <tr class="data" <?php if ($perm['display'] == 'sub') { echo 'style="background: #f6f6f6;"'; } ?>>
		        <td class="short" <?php if ($perm['display'] == 'sub') { echo 'style="padding-left: 17px;"'; } ?>><?php echo $core_filename; ?></td>
		        <td class="short"><?php echo $perm['permission_after_install']; ?> &nbsp;(<?php echo $permission_type; ?>)</td>
		        <td class="thin" style="color: <?php echo $perm_color; ?>;"><?php echo $current_core_permissions[$core_filename]; ?></td>
		    </tr>
    <?php 
    	}
    }
    ?>
</table>

<br /><br />
<h3 style="margin-left: -2px;">URL Rewriting:</h3>

<?php $pec_localization->out('LABEL_INSTALLATION_RENAME_HTACCESS_HINT'); ?>
<br /><br /><br />

<a href="../pec_admin/"><input type="button" value="<?php echo $pec_localization->get('BUTTON_GOTO_LOGIN'); ?>" /></a>