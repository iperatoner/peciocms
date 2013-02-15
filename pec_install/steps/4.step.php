<?php

require('queries.inc.php');

try {
	$pec_database = new PecDatabase(DB_HOST, DB_USER, DB_PW, DB_NAME, DB_TYPE);
	
	
	// TABLES
	
	$pec_database->db_connect();
	foreach ($queries as $q) {
		$pec_database->db_query($q);
	}
	$pec_database->db_close_handle();
	
	$pec_settings = new PecSetting(
		NULL_ID, $_POST['setting_main_title'], $_POST['setting_sub_title'], '', '', 
		$_POST['setting_admin_email'], 1, $_GET['locale'], $_POST['setting_url_type'], 8, 0, 
		sha1('d26gR9rgrdNc921234jfi') . 'a19867770bRsf'
	);
	$pec_settings->save();
	
	
	// ENTRIES
	
	$first_article = new PecArticle(NULL_ID, $pec_localization->get('FIRST_ARTICLE_TITLE'), $pec_localization->get('FIRST_ARTICLE_CONTENT'), true);
	$first_article->save();
	
	$first_menupoint = new PecMenuPoint(NULL_ID, NULL_ID, NULL_ID, $pec_localization->get('FIRST_MENUPOINT_NAME'), MENUPOINT_TARGET_HOME, '-', 1);
	$first_menupoint->save();
	
	$first_menupoint_2 = new PecMenuPoint(NULL_ID, NULL_ID, NULL_ID, $pec_localization->get('SECOND_MENUPOINT_NAME'), MENUPOINT_TARGET_BLOG, '-', 2);
	$first_menupoint_2->save();
	
	$first_text = new PecSidebarText(
		NULL_ID, $pec_localization->get('FIRST_TEXT_TITLE'), $pec_localization->get('FIRST_TEXT_CONTENT'), 
		TEXT_VISIBILITY_EVERYWHERE, '', 1
	);
	$first_text->save();
	
	$first_link_category = new PecSidebarLinkCat(NULL_ID, $pec_localization->get('FIRST_LINKCAT_NAME'), TEXT_VISIBILITY_EVERYWHERE, '', 1);
	$first_link_category->save();
	
	$first_link_1 = new PecSidebarLink(NULL_ID, $first_link_category->get_id(), 'SwissVPS Virtual Server', 'http://swissvps.ch', 1);
	$first_link_1->save();
	
	$first_link_2 = new PecSidebarLink(NULL_ID, $first_link_category->get_id(), 'PHP Homepage', 'http://php.net', 2);
	$first_link_2->save();
	
	$first_link_3 = new PecSidebarLink(NULL_ID, $first_link_category->get_id(), 'pecio homepage', 'http://pecio-cms.com', 3);
	$first_link_3->save();
	
	$first_blog_category = new PecBlogCategory(NULL_ID, $pec_localization->get('FIRST_BLOGCAT_NAME'));
	$first_blog_category->save();
	
	$first_blog_tag = new PecBlogTag(NULL_ID, $pec_localization->get('FIRST_BLOGTAG_NAME'));
	$first_blog_tag->save();
	
	$password = random_string(10);
	$first_user = new PecUser(NULL_ID, 'admin', '', '', $_POST['setting_admin_email'], $password, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 'admin');
	$first_user->save();
	
	$time = time();
	$d = date('d', $time);
	$m = date('m', $time);
	$y = date('Y', $time);
	
	$first_blog_post = new PecBlogPost(
		NULL_ID, $time, $y, $m, $d, $first_user->get_id(), $pec_localization->get('FIRST_BLOGPOST_TITLE'), 
		$pec_localization->get('FIRST_BLOGPOST_CONTENT_CUT'), $pec_localization->get('FIRST_BLOGPOST_CONTENT'), 
		$first_blog_tag->get_id(), $first_blog_category->get_id(), 1, 1
	);
	$first_blog_post->save(false);
	
	$first_blog_comment = new PecBlogComment(
		NULL_ID, $first_blog_post->get_id(), $pec_localization->get('FIRST_BLOGCOMMENT_TITLE'), 
		$pec_localization->get('FIRST_BLOGCOMMENT_AUTHOR'), 'pecio@web', time(), 
		$pec_localization->get('FIRST_BLOGCOMMENT_CONTENT'), false
	);
	$first_blog_comment->save();
	
	PecMessageHandler::raise('installation_success');
}
catch (Exception $e) {
	PecMessageHandler::raise('installation_error');
}

?>

<br /><br />
<form method="post" action="install.php?step=5<?php echo LOCALE_QUERY_VAR; ?>">
	<input type="hidden" name="generated_password" value="<?php echo base64_encode($password); ?>" />
		
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