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

<h3>Please check if permissions are correct</h3>
<table class="data_table">
    <tr class="head">
        <td class="lower_medium">File/Directory</td>
        <td class="short">Required</td>
        <td class="thin center">Current</td>
    </tr>
    <tr class="data">
        <td class="lower_medium">pec_admin/</td>
        <td class="short">777 &nbsp;(not recursive)</td>
        <td class="thin center" style="color: red;">644</td>
    </tr>
    <tr class="data">
        <td class="lower_medium">pec_feeds/</td>
        <td class="short">777 &nbsp;(recursive)</td>
        <td class="thin center" style="color: green;">777</td>
    </tr>
</table>

<br />

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


