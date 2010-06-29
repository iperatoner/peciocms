<?php include $pecio->get('template_path_c') . 'header.php'; ?>

<h2><?php echo $pecio->get('article')->get_title(); ?></h2>
<?php echo $pecio->get('article')->get_content(); ?>

<?php include $pecio->get('template_path_c') . 'footer.php'; ?>