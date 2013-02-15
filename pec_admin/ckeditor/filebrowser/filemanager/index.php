<?php

include("confic.inc.php");

function str_ends_with($str, $sub) {
	return substr($str, strlen($str) - strlen($sub)) == $sub;
}

// upload file
if ($_GET['action'] == "upload") {
	$file_name = $_FILES["ffoto"]["name"];
	$file_size = $_FILES["ffoto"]["size"];
	$file_type = $_FILES["ffoto"]["type"];
	$path = $_POST['folder'];
	
	if (!str_ends_with($file_name, '.php')) {
		move_uploaded_file($_FILES['ffoto']['tmp_name'], $path . $file_name) ;
		$message='<p><b>Your file has been uploaded successfully</b></p>';
	}
}
else if ($_GET['action']=="delete") {
	unlink($_GET['file']);
}
// create folder
if ($_GET['action'] == "create_folder") {
	$path = $_POST['folder'];
	mkdir($path . $_POST['fname'], 0777);
}

// remove folder
if ($_GET['action'] == "delete_folder") {
	recursive_rmdir($_GET['id']);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>File Browser</title>
		
		<link href="css/jqueryFileTree.css" rel="stylesheet" type="text/css" media="screen" />
		<link href="css/file_manager.css" rel="stylesheet" type="text/css" />
		
		<script language="javascript" src="js/jquery-1.2.6.min.js"></script>
		<script language="javascript" src="js/jqueryFileTree.js"></script>
		<script language="javascript" src="js/jquery.form.js"></script>
		<script language="javascript" src="js/jquery.jframe.js"></script>
		
		<script language="javascript">
			function set_content(url, target){
				jQuery(target).loadJFrame(url);
				$(target).loadJFrame(url);
			}
		</script>
		
		<script language="javascript">
			$(document).ready(function(){
				$('#navBar').fileTree({
						root: '<?php echo C4d28b848; ?>', 
						script: 'jqueryFileTree.php', 
						loadMessage: 'Loading...',
						exts: 'jpeg,jpg,png,gif,tiff,pdf' 
					},
					function(file) { 	 
						set_content('file_details.php?file=' + file + '&<?php echo CKEDITOR_QUERY_VARS; ?>', '#fileDetails');
					}
				);
				
				jQuery.fn.waitingJFrame = function () {
					$(this).html("<b>loading...</b>");
				};
				
				$('.op_menu').hover(function () { 
				}); 
			});
		</script>
	</head>
	<body>
		<div id="fileWrapper" src="#">
		
			<div id="navBar" class="demo" src="#">
			</div>
			
			<div class="file_details_wrapper">
				 <a href="index.php" target="_self" class="refresh_link">Refresh</a><br /><br />
				 <div id="fileDetails">
					 <?php echo $message; ?>
					 Select a file from one of the folders in the left tree.
				 </div>
			 </div>
			 
		</div>
		
		<script language="javascript">
			function goto_folder_options(cual) {
				set_content('file_upload.php?folder=' + cual + '&<?php echo CKEDITOR_QUERY_VARS; ?>', '#fileDetails');	
			}
			
			<?php
			// if uploaded a file, switch to it
			if ($_GET['action'] == 'upload') {
				echo "set_content('file_details.php?file=" . $path . $file_name . "&" . CKEDITOR_QUERY_VARS . "', '#fileDetails');";
			}
			?>
		</script>
	</body>
</html>
