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

 <?php echo str_replace('{%VERSION%}', PEC_VERSION, $pec_localization->get('LABEL_INSTALLATION_WELCOMETEXT')); ?><br /><br />

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


