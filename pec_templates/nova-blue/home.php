<?php include('pec_templates/' . $pecio->get('template')->get_directory_name() . '/header.php'); ?>

<?php 
foreach ($pecio->get('articles') as $a) { 
    echo '<h2>' . $a->get_title() . '</h2>';
    echo $a->get_content() . '<br />';    
}

if ($pecio->get('blogposts')) {
    include('pec_templates/' . $pecio->get('template')->get_directory_name() . '/blog-data.php');
}
?>

<?php include('pec_templates/' .$pecio->get('template')->get_directory_name() . '/footer.php'); ?>