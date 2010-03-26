<?php

if (isset($_GET['perform_actions']) && empty($_GET['perform_actions'])) {
	if (isset($_POST['locale_chooser']) && PecLocale::exists($_POST['locale_chooser'])) {
		pec_redirect('pec_install/install.php?step=2&locale=' . $_POST['locale_chooser']);
	}
	else {
		pec_redirect('pec_install/install.php?step=2&locale=en');
	}
}

$available_locales = PecLocale::scan();
$locale_select_options = '';
foreach ($available_locales as $lcl) {
    $locale_select_options .= '<option value="' . $lcl . '">' . $lcl . '</option>';
}

?>

 <?php echo str_replace('{%VERSION%}', PEC_VERSION, $pec_localization->get('LABEL_INSTALLATION_WELCOMETEXT')); ?><br /><br /><br />

<?php $current_core_permissions = pec_read_core_permissions(); ?>
<h3>Please check if all permissions are correct:</h3>
<table class="data_table">
    <tr class="head">
        <td class="short">File/Directory</td>
        <td class="short">Required</td>
        <td class="thin">Current</td>
    </tr>
    <?php 
    foreach ($pec_core_permissions as $core_filename => $perm) {
    	switch ($perm['type']) {
    		case 'r': $permission_type = 'recursive'; break;
    		case 'nr': $permission_type = 'not recursive'; break;
    		case 'f': $permission_type = 'file'; break;
    	}
    	
    	if ($perm['permission_before_install'] == $current_core_permissions[$core_filename]) {
    		$perm_color = 'green';
    	}
    	else {
    		$perm_color = 'red';
    	}
    ?>
	    <tr class="data" <?php if ($perm['display'] == 'sub') { echo 'style="background: #f6f6f6;"'; } ?>>
	        <td class="short" <?php if ($perm['display'] == 'sub') { echo 'style="padding-left: 17px;"'; } ?>><?php echo $core_filename; ?></td>
	        <td class="short"><?php echo $perm['permission_before_install']; ?> &nbsp;(<?php echo $permission_type; ?>)</td>
	        <td class="thin" style="color: <?php echo $perm_color; ?>;"><?php echo $current_core_permissions[$core_filename]; ?></td>
	    </tr>
    <?php 
    }
    ?>
</table>

<br /><br /><br />

<form method="post" action="install.php?step=1&perform_actions<?php echo LOCALE_QUERY_VAR; ?>">
	<h3><?php echo $pec_localization->get('LABEL_INSTALLATION_CHOOSELANGUAGE'); ?>:</h3>
	<select name="locale_chooser" style="width: 100px;">
		<?php echo $locale_select_options; ?>
	</select><br /><br />
	
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td align="left">
			</td>
			<td align="right">
				<input type="submit" value="<?php echo $pec_localization->get('BUTTON_NEXT'); ?>" />
			</td>
		</tr>	
	</table>
	
</form>


