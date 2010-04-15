<?php include('pec_templates/' . $pecio->get('template')->get_directory_name() . '/header.php'); ?>

<h2>
	<a href="<?php echo $pecio->blogpost_url($pecio->get('blogpost')); ?>"><?php echo $pecio->get('blogpost')->get_title(); ?></a>
</h2>

<div class="singlepostmeta">
	<?php echo $pecio->get('blogpost')->get_timestamp('l, d F Y - H:i'); ?> |
	Categories:
	<?php
		$start = true;
		foreach ($pecio->get('blogpost')->get_categories(TYPE_OBJ_ARRAY) as $c) {
			if (!$start) {
				echo ', ';	
			}
			$start = false;
			echo '<a href="' . $pecio->blogcategory_url($c) . '">' . $c->get_name() . '</a>';	
		}
	?> -
	Tags:
	<?php
		$start = true;
		foreach ($pecio->get('blogpost')->get_tags(TYPE_OBJ_ARRAY) as $t) {
			if (!$start) {
				echo ', ';	
			}
			$start = false;
			echo '<a href="' . $pecio->blogtag_url($t) . '">' . $t->get_name() . '</a>';	
		}
	?>
	<br /><br />
</div>

<div class="blogpost">

	<div class="postcontent">
		<?php echo $pecio->get('blogpost')->get_content(); ?>
	</div>
	
</div><br />


<?php
	if ($pecio->get('blogpost')->get_comments_allowed()) {
		$comments = $pecio->get('blogcomments');
		$comment_count = count($comments);
?>
<h3>
	<a name="comments" id="comments" href="#comments"><?php echo $comment_count; ?> comment(s)</a>
</h3>

<?php foreach ($comments as $comment) { ?>

<div class="blogcomment">
	<div class="commenttitle">
		<h3>
			<a name="comment-<?php echo $comment->get_id(); ?>" id="comment-<?php echo $comment->get_id(); ?>" 
			   href="#comment-<?php echo $comment->get_id(); ?>">
			   <?php echo $comment->get_title(); ?>
		   </a>
	   </h3>
	</div>

	<div class="commentmeta">
		by <?php echo $comment->get_author(); ?> - <?php echo $comment->get_timestamp('l, d F Y - H:i:s'); ?>
   		<br />
   	</div>
   	
   	<div class="commentcontent">
   		<p><?php echo $comment->get_content(); ?></p>
   	</div>
</div>
<?php } ?>
<br />

<h4>Leave a comment...</h4>

<?php $pecio->comment_form_messages(); ?>

<form method="post" action="<?php echo $pecio->comment_submit_url($pecio->get('blogpost')); ?>">
	<div id="leavecomment">
		<table width="75%">
			<tr>
				<td>Name: </td>
				<td><input size="35" type="text" name="comment_author" value="" /></td>
			</tr>
			<tr>
				<td>Email: </td>
				<td><input size="35" type="text" name="comment_email" value="" /></td>
			</tr>
			<tr>
				<td>Title: </td>
				<td><input size="35" type="text" name="comment_title" value="" /></td>
			</tr>
			<tr>
				<td valign="top">Comment: </td>
				<td><textarea cols="45" rows="10" name="comment_content"></textarea></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<?php $pecio->comment_antispam_inputs(); ?>
					<input type="submit" value="Submit Comment"/>
				</td>
			</tr>
		</table>
	</div>
</form>
<?php } ?>

<?php include('pec_templates/' . $pecio->get('template')->get_directory_name() . '/footer.php'); ?>