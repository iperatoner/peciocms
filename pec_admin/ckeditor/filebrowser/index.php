<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CKEditor Filemanager</title>
</head>

<body>
<script type="text/javascript" src="../ckeditor.js"></script>
<div style="width:700px; margin:auto;">
<textarea id="ftext" name="ftext"><p>
Donec lacus nisi, molestie nec consequat eget, auctor non dolor. Etiam luctus felis vitae massa cursus sit amet placerat ipsum tincidunt. Donec lorem ante, dignissim in rutrum ut; feugiat ut dolor. </p>
<p>Suspendisse in arcu at ligula bibendum euismod. Aenean vitae est dolor? Quisque congue elementum diam, sed auctor dui aliquet et. Etiam dictum eleifend augue vitae tincidunt. Vivamus vitae pharetra massa. Nunc elit tellus, pellentesque at eleifend facilisis, gravida in sem. </p></textarea>
</div>
<script type="text/javascript">
//<![CDATA[
CKEDITOR.replace( 'ftext',
{
	toolbar : [ [ 'Source', '-', 'Cut', 'Copy', 'Paste', '-', 'TextColor', 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'NumberedList', 'BulletedList', 'Outdent', 'Indent', '-', 'Link', 'Unlink', 'Anchor', '-' ],
	['Styles', 'Format', 'Font', 'FontSize', '-', 'Image', 'Flash', 'Table', 'HorizontalRule']
	 ],
	 height:500,
	  filebrowserBrowseUrl : 'filemanager/index.php'
});
//]]>
</script>
</body>
</html>