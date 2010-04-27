<?php

include("confic.inc.php");

function str_ends_with($str, $sub) {
	return substr($str, strlen($str) - strlen($sub)) == $sub;
}

// upload 
if ($_GET['action']=="upload") {
	$file_name=$_FILES["ffoto"]["name"];
	$file_size=$_FILES["ffoto"]["size"];
	$file_type=$_FILES["ffoto"]["type"];
	$path=$_POST['folder'];
	if (!str_ends_with($file_name, '.php')) {
		move_uploaded_file($_FILES['ffoto']['tmp_name'], $path.$file_name) ;
		$message='<p><b>Your file has been uploaded successfully</b></p>';
	}
} else if ($_GET['action']=="delete") {
	unlink($_GET['file']);
}
// Crear carpeta 
if ($_GET['action']=="create_folder") {
	$path=$_POST['folder'];
mkdir($path.$_POST['fname'], 0777);
}
if ($_GET['action']=="delete_folder") {
	//echo 'folder deleted:'.$_GET['id']; 
	recursive_rmdir($_GET['id']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Editor</title>
<script language="javascript" src="js/jquery-1.2.6.min.js"></script>
<script language="javascript" src="js/jqueryFileTree.js"></script>
<script language="javascript" src="js/jquery.form.js"></script>
<script language="javascript" src="js/jquery.jframe.js"></script>
<link href="css/jqueryFileTree.css" rel="stylesheet" type="text/css" media="screen" />
<link href="css/file_manager.css" rel="stylesheet" type="text/css" />
<script language="javascript">
function F8669f9aa(url,target){
	jQuery(target).loadJFrame(url);
$(target).loadJFrame(url);
}
</script>
<script language="javascript">
$(document).ready(function(){
 $('#navBar').fileTree({ 
		//root: '../../userfiles/',  
 root: '<?php echo C4d28b848; ?>', 
 script: 'jqueryFileTree.php', 
 loadMessage: 'Loading...',
 exts: 'jpeg,jpg,png,gif,tiff,pdf' }
, function(file) { 
			//alert(file); 
 F8669f9aa('file_details.php?file='+file, '#fileDetails');
});
jQuery.fn.waitingJFrame = function () {
 $(this).html("<b>loading...</b>");
};	
	$('.op_menu').hover(function () { 
 alert("funka");
}); 
});
</script>
</head>
<body>
<div id="fileWrapper" src="#">
<div id="navBar" class="demo" src="#"></div>
	<div class="file_details_wrapper">
		 <a href="index.php" target="_self" class="refresh_link">Refresh</a><br /><br />
		 <div id="fileDetails">
			 <?php echo $message; ?>
			 Select a file from one of the folders in the left tree.
		 </div>
	 </div>
</div>	
<ul id="myMenu" class="contextMenu" style="-moz-user-select: none; top: 191px; left: 319px; display: none;">
	<li class="edit">
 <a href="#edit">Edit</a>
	</li>
	<li class="cut separator">
 <a href="#cut">Cut</a>
	</li>
	<li class="copy">
 <a href="#copy">Copy</a>
	</li>
	<li class="paste">
 <a href="#paste">Paste</a>
	</li>
	<li class="delete">
 <a href="#delete">Delete</a>
	</li>
	<li class="quit separator">
 <a href="#quit">Quit</a>
	</li>
</ul>
<script language="javascript">
function Fc310bc56(cual) {
	//alert(cual); 
	F8669f9aa('file_upload.php?folder='+cual, '#fileDetails');	
}
</script> 
</body>
</html>
