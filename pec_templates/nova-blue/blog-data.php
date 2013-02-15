<?php 
foreach ($pecio->get('blogposts') as $bp) {
	if ($bp->get_comments_allowed()) {
		$comments = $pecio->get_comments($bp);
		$comment_count = count($comments);
	}
?>
	<div class="blogpost">
	
		<div class="posttitle">
			<p class="ptitle"><a href="<?php echo $pecio->blogpost_url($bp); ?>"><?php echo $bp->get_title(); ?></a></p>
		</div>
	
		<div class="postmeta">
			<?php echo $bp->get_timestamp('l, d F Y - H:i'); ?> |
			Categories:
			<?php
				$start = true;
				foreach ($bp->get_categories(TYPE_OBJ_ARRAY) as $c) {
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
				foreach ($bp->get_tags(TYPE_OBJ_ARRAY) as $t) {
					if (!$start) {
						echo ', ';	
					}
					$start = false;
					echo '<a href="' . $pecio->blogtag_url($t) . '">' . $t->get_name() . '</a>';	
				}
			?>
	    </div>
	    
	    <div class="postcomments_count">
	    	<?php if ($bp->get_comments_allowed()) { ?>
	    	<a href="<?php echo $pecio->blogpost_url($bp) . '#comments'; ?>"><?php echo $comment_count; ?> Comment(s)</a>
	    	<?php } else { ?>
	    	No comments
	    	<?php } ?>
	    </div>
	    
	    <div class="cleardiv"></div><br />
	    
	    <div class="postcontent">
	    	<strong><?php echo $bp->get_content_cut(); ?></strong><br /><br />
	    	
	    	<?php echo $bp->get_content(); ?>
	    </div>
	
	</div>
	
	<br />
<?php } ?>

<table id="otherentries">
	<tr>
		<?php
			if ($pecio->get('blog_older_entries_url')) {
				echo '<td align="left"><a href="' . $pecio->get('blog_older_entries_url') . '">&laquo; older entries</a></td>';
			}
			if ($pecio->get('blog_newer_entries_url')) {
				echo '<td align="right"><a href="' . $pecio->get('blog_newer_entries_url') . '">newer entries &raquo;</a></td>';
			}
		?>
	</tr>
</table>