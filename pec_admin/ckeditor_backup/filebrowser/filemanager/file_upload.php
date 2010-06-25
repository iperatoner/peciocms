<?php include('confic.inc.php'); ?>

Upload in folder: <?php echo get_correct_path($_GET['folder']); ?>

<div id="optionsWrapper"> 
	<form id="uploadForm" method="post" action="index.php?action=upload&<?php echo CKEDITOR_QUERY_VARS; ?>" enctype="multipart/form-data">
		<input type="file" name="ffoto">
		<input type="hidden" name="folder" value="<?php echo $_GET['folder']; ?>" />
		<input type="submit" value="Upload" class="btn" target="_self" />
	</form>
	<div id="uploadOutput"></div>
</div>

<br />

Create folder

<div id="optionsWrapper"> 
	<form id="folderForm" method="post" action="index.php?action=create_folder&<?php echo CKEDITOR_QUERY_VARS; ?>">
		<?php echo get_correct_path($_GET['folder']); ?>&nbsp;<input type="text" name="fname" size="20" />
		
		<input type="hidden" name="folder" value="<?php echo $_GET['folder']; ?>" />
		<input type="submit" name="submit" value="Create" class="btn" target="_self" />
	</form>
</div>
<br />

Delete this folder

<div id="optionsWrapper"> 
	<a href="index.php?action=delete_folder&id=<?php echo $_GET['folder']; ?>&<?php echo CKEDITOR_QUERY_VARS; ?>" target="_self">Delete folder and all content</a>
</div>