<?php
include("confic.inc.php");
$file=substr($_GET['file'], "3");

$file_path = get_correct_path($_GET['file']);

?>
<div id="optionsWrapper2">
	<p>
 <span style="padding-left: 4px;"><b>URL:</b> <a href="<?php echo C70d7bd0f.$file_path; ?>" target="_blank"><?php echo C70d7bd0f.$file_path; ?></a></span><br /><br />
 <a href="javascript:Fa6ccfa7b();">Delete</a>
 &nbsp;&nbsp;-&nbsp;&nbsp;
<a href="javascript:Fe9b0cef7('<?php echo C70d7bd0f.$file_path; ?>');">Insert</a>
 </p>
</div>
 <?php
	$extension=substr($_GET['file'], -4, 4);
if ($extension==".jpg" || $extension==".gif" || $extension==".png") {
 echo '
 <div id="imgWrapper">
 <img src="'.$file_path.'">
 </div>
 ';
} else {
 echo '<a href="'.$file_path.'" target="_blank">See this file</a>';
}
?>	
<script language="javascript">
function Fa6ccfa7b(codigo) {
	var fRet;
fRet = confirm('Do you want delete this file?');
if (fRet==false) {
 return;
} else { 
 window.location='index.php?action=delete&file=<?php echo $_GET['file']; ?>';
}
};
function Fe9b0cef7(cual) {
	//var o = opener.document.getElementById("42_textInput");
	var o = $("div.cke_dialog input:first", window.opener.document).val(cual);
	//o.value = cual;
	self.close();	
}
</script>
