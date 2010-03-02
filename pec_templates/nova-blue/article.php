<?php include('pec_templates/' . $pecio->get('template')->get_directory_name() . '/header.php'); ?>

<h2><?php echo $pecio->get('article')->get_title(); ?></h2>
<?php echo $pecio->get('article')->get_content(); ?>

<?php include('pec_templates/' . $pecio->get('template')->get_directory_name() . '/footer.php'); ?>