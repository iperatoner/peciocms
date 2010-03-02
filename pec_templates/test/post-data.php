<h2><?php echo $pecio->get('blogpost')->get_title(); ?></h2>
<p><?php echo $pecio->get('blogpost')->get_content(); ?></p>
<br /><br />


<?php 

$comments = PecBlogComment::load('post', $pecio->get('blogpost'));
$comment_count = count($comments);

?>
<h3><?php echo $comment_count; ?> Comments</h3>
<div style="background: #f0f0f0; border: 1px solid #dfdfdf; padding: 8px;">
<?php 

foreach ($comments as $c) {
    echo '<h4>' . $c->get_title() . '</h4>';
    echo 'From: ' . $c->get_author() . '<br />';
    echo $c->get_content();
    echo '<br /><hr><br />';
}

?>

</div>
<br />

<?php $pecio->comment_form_messages(); ?>
<form method="post" action="<?php echo $pecio->comment_submit_url($pecio->get('blogpost')); ?>">

<h4>Name:</h4>
<input type="text" name="comment_author" value="" />
<br />

<h4>Email:</h4>
<input type="text" name="comment_email" value="" />
<br />

<h4>Title:</h4>
<input type="text" name="comment_title" value="" />
<br />

<h4>Content:</h4>
<textarea name="comment_content"></textarea>
<br />

<?php $pecio->comment_antispam_inputs(); ?>

<input type="submit" value="Send" />

</form>