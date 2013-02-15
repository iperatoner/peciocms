<?php
include("confic.inc.php");
$file=substr($_GET['file'], "3");

$file_path = get_correct_path($_GET['file']);

?>
<div id="optionsWrapper2">
	<p>
		<span style="padding-left: 4px;"><b>URL:</b>
			<a href="<?php echo C70d7bd0f . $file_path; ?>" target="_blank">
				<?php echo C70d7bd0f . $file_path; ?>
			</a>
		</span><br /><br />
		
		<a href="javascript:delete_file();">Delete</a>
		
		&nbsp;&nbsp;-&nbsp;&nbsp;
		
		<a href="javascript:ck_insert_url('<?php echo C70d7bd0f . $file_path; ?>');">Insert</a>
	</p>
</div>

 <?php
	$extension = substr($_GET['file'], -4, 4);
	if ($extension == ".jpg" || $extension == ".gif" || $extension == ".png") {
		echo '
			<div id="imgWrapper">
				<img src="' . $file_path . '">
			</div>
		';
	}
	else {
		echo '<a href="' . $file_path . '" target="_blank">See this file</a>';
	}
?>

<script language="javascript">
	function delete_file(codigo) {
		var fRet;
		fRet = confirm('Do you want delete this file?');
		if (fRet == false) {
			return;
		}
		else {
			window.location = 'index.php?action=delete&file=<?php echo $_GET['file'] . '&' . CKEDITOR_QUERY_VARS; ?>';
		}
	};
	
	// Helper function to get parameters from the query string.
	function getUrlParam(paramName) {
		var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
		var match = window.location.search.match(reParam) ;

		return (match && match.length > 1) ? match[1] : '' ;
	}
	
	function ck_insert_url(cual) {
	    var funcNum = getUrlParam('CKEditorFuncNum');
	    window.opener.CKEDITOR.tools.callFunction(funcNum, cual);
	    
		self.close();
	}
</script>
